<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$userId = getCurrentUserId();
$username = getCurrentUsername();

function sanitize($data)
{
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}


function getStatusClass($status)
{
    switch ($status) {
        case 'Pending':
            return 'warning';
        case 'In Progress':
            return 'info';
        case 'Completed':
            return 'success';
        default:
            return 'secondary';
    }
}


function isOverdue($deadline)
{
    $today = date('Y-m-d');
    return $deadline < $today;
}


function getTaskStats($userId)
{
    global $conn;

    $stats = [
        'total' => 0,
        'pending' => 0,
        'in_progress' => 0,
        'completed' => 0,
        'overdue' => 0,
        'live' => 0
    ];

    $sql = "SELECT status, deadline FROM tasks WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $stats['total']++;

        switch ($row['status']) {
            case 'Pending':
                $stats['pending']++;
                break;
            case 'In Progress':
                $stats['in_progress']++;
                break;
            case 'Live':
                $stats['live']++;
                break;
            case 'Completed':
                $stats['completed']++;
                break;
        }

        if (isOverdue($row['deadline']) && !in_array($row['status'], ['Completed', 'Live'])) {
            $stats['overdue']++;
        }
    }

    return $stats;
}


function getMonthlyTaskData($userId)
{
    global $conn;

    $months = [];
    $completed = [];
    $created = [];


    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthName = date('M Y', strtotime("-$i months"));
        $months[] = $monthName;

        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));


        $sql = "SELECT COUNT(*) as count FROM tasks 
                WHERE user_id = ? AND status = 'Completed' 
                AND DATE(created_at) BETWEEN ? AND ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $userId, $startDate, $endDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $completed[] = $row['count'];


        $sql = "SELECT COUNT(*) as count FROM tasks 
                WHERE user_id = ? AND DATE(created_at) BETWEEN ? AND ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $userId, $startDate, $endDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $created[] = $row['count'];
    }

    return [
        'months' => $months,
        'completed' => $completed,
        'created' => $created
    ];
}

function getDailyTaskData($userId, $fromDate, $toDate)
{
    global $conn;

    $days = [];
    $completedMap = [];
    $createdMap = [];

    $start = new DateTime($fromDate);
    $end = new DateTime($toDate);
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end->modify('+1 day'));

    foreach ($period as $date) {
        $day = $date->format('Y-m-d');
        $dayLabel = $date->format('d M');
        $days[] = $dayLabel;

        $completedMap[$day] = 0;
        $createdMap[$day] = 0;
    }

    $sql = "SELECT DATE(created_at) as task_date, status, COUNT(*) as count 
            FROM tasks 
            WHERE user_id = ? AND created_at BETWEEN ? AND ?
            GROUP BY task_date, status";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $userId, $fromDate, $toDate);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $date = $row['task_date'];
        $status = $row['status'];
        $count = $row['count'];

        if (isset($createdMap[$date])) {
            $createdMap[$date] += $count;
        }

        if ($status === 'Completed' && isset($completedMap[$date])) {
            $completedMap[$date] = $count;
        }
    }

    $completed = [];
    $created = [];

    foreach ($period as $date) {
        $day = $date->format('Y-m-d');
        $completed[] = $completedMap[$day];
        $created[] = $createdMap[$day];
    }

    return [
        'days' => $days,
        'completed' => $completed,
        'created' => $created
    ];
}
function getAllStatus($type)
{
    global $conn;

    $validTypes = [0, 1, 2, 3];
    if (!in_array($type, $validTypes, true)) {
        return [];
    }
    $status_type = $_SESSION['user_type'] == 1 ? [1] : [1, 0];
    $sql = "SELECT id, name FROM status where type in (" . implode(',', $status_type) . ") ORDER BY sort";
    $result = mysqli_query($conn, $sql);
    if (!$result) return [];

    $statusArr = [];

    switch ($type) {
        case 0:
            while ($status = mysqli_fetch_assoc($result)) {
                $statusArr[$status['name']] = $status['id'];
            }
            break;

        case 1:
            while ($status = mysqli_fetch_assoc($result)) {
                $statusArr[$status['id']] = $status['name'];
            }
            break;

        case 2:
            while ($status = mysqli_fetch_assoc($result)) {
                $statusArr[$status['name']] = [];
            }
            break;

        case 3:
            while ($status = mysqli_fetch_assoc($result)) {
                $statusArr[] = $status['name'];
            }
            break;
    }

    return $statusArr;
}

function getAssignedUsers($type)
{
    global $conn, $userId;

    if (!isManager()) {
        return [];
    }


    $validTypes = [0, 1, 2, 3, 4, 5];
    if (!in_array($type, $validTypes, true)) {
        return [];
    }
    if ($type != 5) {
        $sql = "SELECT ua.user_id, u.username, u.id, u.role, u.created_at
                FROM user_assign ua
                JOIN users u ON ua.user_id = u.id WHERE ";


        if ($userId) {
            $sql .= " ua.manager_id = " . intval($userId) . " AND ";
        }

        $sql .= "ua.status = 1  ORDER BY u.id";
    } else {
        $sql = "SELECT username, id, role, created_at
                FROM users ORDER BY id";
    }
    $result = mysqli_query($conn, $sql);
    if (!$result) return [];
    $assignedUsers = [];

    switch ($type) {
        case 0:
            while ($row = mysqli_fetch_assoc($result)) {
                $assignedUsers[$row['username']] = $row['user_id'];
            }
            break;

        case 1:
            while ($row = mysqli_fetch_assoc($result)) {
                $assignedUsers[$row['user_id']] = $row['username'];
            }
            break;

        case 2:
            while ($row = mysqli_fetch_assoc($result)) {
                $assignedUsers[$row['username']] = [];
            }
            break;
        case 3:
            while ($row = mysqli_fetch_assoc($result)) {
                $assignedUsers[] = $row['username'];
            }
            break;
        case 4:
        case 5:
            while ($row = mysqli_fetch_assoc($result)) {
                $assignedUsers[] = $row;
            }
            break;
    }

    return $assignedUsers;
}

function pr($d)
{
    echo '<pre>';
    print_r($d);
    echo '</pre>';
}

function assignUser($user_id)
{
    global $conn;

    $manager_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    if (!$user_id || !$manager_id) {
        $_SESSION['error_msg'] = "User or manager information missing.";
        return;
    }


    $checkSql = "SELECT id FROM user_assign WHERE user_id = ? AND manager_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "ii", $user_id, $manager_id);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) == 0) {
        $insertSql = "INSERT INTO user_assign (user_id, manager_id, assign_date, status) VALUES (?, ?, NOW(), 1)";
        $insertStmt = mysqli_prepare($conn, $insertSql);
        $status = 1;
        mysqli_stmt_bind_param($insertStmt, "iii", $user_id, $manager_id, $status);

        if (mysqli_stmt_execute($insertStmt)) {
            $_SESSION['success_msg'] = "User created and assigned successfully.";
        } else {
            $_SESSION['error_msg'] = "User created but assignment failed.";
        }

        mysqli_stmt_close($insertStmt);
    } else {
        $_SESSION['error_msg'] = "User is already assigned.";
    }

    mysqli_stmt_close($checkStmt);
}

function getTasks($userId, $user_filter = null, $type = 0)
{
    global $conn;
    $tasks = [];
    $tasks = getAllStatus(2);
    if ($_SESSION['user_type'] == 1) {
        $typeArr = ['Pending', 'In Progress', 'Completed'];
    }
    if ($type == 0) {
        $ss = "!= 'Hold'";
    } else {
        $ss = "= 'Hold'";
    }

    if (isManager()) {
        if ($user_filter) {
            $sql = "SELECT tasks.id, title, description, deadline, status, tasks.created_at, users.username 
                    FROM tasks 
                    JOIN users ON users.id = tasks.user_id 
                    WHERE user_id = $user_filter and status $ss
                    ORDER BY deadline DESC";
        } else {
            $sql = "SELECT tasks.id, title, description, deadline, status, tasks.created_at, users.username 
                    FROM tasks 
                    JOIN users ON users.id = tasks.user_id where status $ss
                    ORDER BY deadline DESC";
        }

        $result = mysqli_query($conn, $sql);
        $users = getAssignedUsers(1);

        while ($row = mysqli_fetch_assoc($result)) {
            if (in_array($row['username'], $users) || isSuper() || $row['username'] == $_SESSION['username']) {
                $tasks[$row['status']][] = $row;
            }
        }
    } else {
        $sql = "SELECT tasks.id, title, description, deadline, status, tasks.created_at, users.username 
                FROM tasks 
                JOIN users ON users.id = tasks.user_id 
                WHERE user_id = ? and status $ss
                ORDER BY deadline DESC";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            if ($_SESSION['user_type'] == 1) {
                if (in_array($row['status'], $typeArr)) {
                    $tasks[$row['status']][] = $row;
                }
            } else {
                $tasks[$row['status']][] = $row;
            }
        }
    }

    return $tasks;
}


function getEmailContent($branch, $task, $description, $toname, $comment, $fromname, $t = 0)
{
    $formattedComment = nl2br(html_entity_decode($comment ?? "", ENT_QUOTES | ENT_HTML5));
    $withNewlines = str_replace(['\\r\\n', '\\n', '\\r'], "\n", $formattedComment);

    // Then convert newlines to <br>
    $finalOutput = nl2br($withNewlines);

    if ($t == 0) {
        $details = "
        <strong>Assigned To:</strong> $fromname<br>
        <strong>Branch:</strong> $branch<br>
        <strong>Task:</strong> $task<br>
        <strong>Description:</strong><br>
        $description<br>
        <strong>DB Comment:</strong><br>
        $finalOutput
        ";
    } else {
        $details = "
        <strong>Live Done:</strong> Please Test on Production<br>
        <strong>Branch:</strong> $branch<br>
        <strong>Task:</strong> $task<br>
        <strong>Description:</strong><br>
        $description<br>
        <strong>DB Comment:</strong><br>
        $finalOutput
        ";
    }

    return trim($details);
}

function send_email_to_manager($row)
{
    global $conn;

    if (!$row) {
        return ['success' => 1, 'mes' => ''];
    }

    if (empty($row['branch'])) {
        return
            ['success' => 0, 'mes' => 'Branch not found. Please add Branch'];
    }


    $emailContentHtml = getEmailContent(
        $row['branch'],
        $row['title'],
        $row['description'],
        $row['managername'],
        $row['comment'],
        $row['username']
    );

    $subject = "Merge Branch " . html_entity_decode($row['branch']);
    $emailconfiguredata = getEmailConfiguration();
    if ($emailconfiguredata) {
        $sendResult = sendSmtpMail(
            $emailconfiguredata,
            $row['manageremail'],
            $row['useremail'],
            $subject,
            $emailContentHtml,
            "AWS"
        );
    } else {
        $sendResult = sendPersonalEmail(
            $row['manageremail'],
            $subject,
            $emailContentHtml,
            $row['useremail']
        );
    }

    if (strpos($sendResult, 'Email sent successfully.') !== false) {
        return ['success' => 1, 'mes' => 'Email sent'];
    } else {
        return ['success' => 0, 'mes' => 'Email failed: ' . $sendResult];
    }
}

function getEmailConfiguration($t = 0)
{
    global $conn;
    if ($t == 0) {
        $sql = "SELECT * FROM emails_configure WHERE status = 1 and type = 'AWS'";
    } else {
        $sql = "SELECT * FROM emails_configure WHERE status = 1 and type = 'Personal'";
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $config = mysqli_fetch_assoc($result);

    return $config;
}

function sendSmtpMail($emailconfiguredata, $toemail, $ccemail, $subjectname, $emailtextdata)
{
    if (empty($emailconfiguredata)) {
        return "Email configuration not found.";
    }

    $host = $emailconfiguredata['host'];
    $type = $emailconfiguredata['type'];
    $port = $emailconfiguredata['port'];
    $username = $emailconfiguredata['username'];
    $password = $emailconfiguredata['password'];
    $fromemail = $emailconfiguredata['email'];
    $fromname = "Beyoung";

    if (!filter_var($toemail, FILTER_VALIDATE_EMAIL)) {
        return "Invalid recipient email address.";
    }

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = $port;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;

        if (strtolower($type) === 'aws' || !empty($emailconfiguredata['tls'])) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }


        $mail->setFrom($fromemail, $fromname);
        $mail->addAddress($toemail);
        if (!empty($ccemail)) {
            $mail->addCC($ccemail);
        }


        $mail->isHTML(true);
        $mail->Subject = $subjectname;
        $mail->Body    = nl2br($emailtextdata);
        $mail->AltBody = strip_tags($emailtextdata);

        $mail->send();
        return "Email sent successfully.";
    } catch (Exception $e) {
        return "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}

function sendPersonalEmail($toemail, $subject, $message, $ccemail = '')
{

    $emailconfiguredata = getEmailConfiguration(1);
    if (empty($emailconfiguredata)) {
        return "Email configuration not found.";
    }
    $fromEmail = $emailconfiguredata['email'];
    $fromName = $emailconfiguredata['username'];
    $emailPassword = $emailconfiguredata['password'];

    if (!filter_var($toemail, FILTER_VALIDATE_EMAIL)) {
        return "Invalid recipient email address.";
    }

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $fromEmail;
        $mail->Password = $emailPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;


        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toemail);
        if (!empty($ccemail)) {
            $mail->addCC($ccemail);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($message);
        $mail->AltBody = strip_tags($message);

        $mail->send();
        return "Email sent successfully.";
    } catch (Exception $e) {
        return "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}

function verify_task_to_send_mail($taskId, $type = 0)
{
    global $conn;

    if (!$taskId) {
        return ['success' => 0, 'mes' => 'Invalid task ID'];
    }
    if ($type == 1) {
        $te = '(0,1)';
    } else {
        $te = '(0)';
    }
    $sql = "SELECT 
                t.title, 
                t.description,
                t.branch, 
                t.comment, 
                u.username AS username, 
                u.email AS useremail, 
                m.username AS managername, 
                m.email AS manageremail 
            FROM tasks t 
            JOIN users u ON u.id = t.user_id 
            JOIN user_assign ua ON u.id = ua.user_id 
            JOIN users m ON ua.manager_id = m.id 
            WHERE t.id = ? and mail_status in $te
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $taskId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        if ($row['branch']) {
            return ['status' => 1, 'data' => $row];
        } else {
            return ['status' => 0, 'mes' => 'Branch Not Found'];
        }
    } else {
        return ['status' => 1, 'data' => []];
    }
}

function send_done_on_live($row)
{
    if (!isManager()) return ['success' => 0, 'mes' => 'Only Manager can live task'];

    if (!$row) {
        return ['success' => 1, 'mes' => ''];
    }

    if (empty($row['branch'])) {
        return
            ['success' => 0, 'mes' => 'Branch not found. Please add Branch'];
    }


    $emailContentHtml = getEmailContent(
        $row['branch'],
        $row['title'],
        $row['description'],
        $row['managername'],
        $row['comment'],
        $row['username'],
        1
    );

    $subject = "Branch Merged Successfully " . html_entity_decode($row['branch']);
    $emailconfiguredata = getEmailConfiguration();
    if ($emailconfiguredata) {
        $sendResult = sendSmtpMail(
            $emailconfiguredata,
            $row['manageremail'],
            $row['useremail'],
            $subject,
            $emailContentHtml,
            "AWS"
        );
    } else {
        $sendResult = sendPersonalEmail(
            $row['useremail'],
            $subject,
            $emailContentHtml
        );
    }

    if (strpos($sendResult, 'Email sent successfully.') !== false) {
        return ['success' => 1, 'mes' => 'Email sent'];
    } else {
        return ['success' => 0, 'mes' => 'Email failed: ' . $sendResult];
    }
}


function getUsers($type)
{
    global $conn, $userId;

    $validTypes = [0, 1, 2, 3, 4, 5];
    if (!in_array($type, $validTypes, true)) {
        return [];
    }

    $sql = "SELECT username, u.id, role, u.created_at 
            FROM users u 
            JOIN user_assign ua on ua.user_id = u.id 
            WHERE ua.manager_id = (SELECT manager_id FROM user_assign WHERE user_id = $userId) 
            ORDER BY id";

    $result = mysqli_query($conn, $sql);
    if (!$result) return [];
    $assignedUsers = [];

    switch ($type) {
        case 0:
            while ($row = mysqli_fetch_assoc($result)) {
                $assignedUsers[$row['username']] = $row['id'];
            }
            break;

        case 1:
            while ($row = mysqli_fetch_assoc($result)) {
                $assignedUsers[$row['id']] = $row['username'];
            }
            break;

        case 2:
            while ($row = mysqli_fetch_assoc($result)) {
                $assignedUsers[$row['username']] = [];
            }
            break;
        case 3:
            while ($row = mysqli_fetch_assoc($result)) {
                $assignedUsers[] = $row['username'];
            }
            break;
        case 4:
        case 5:
            while ($row = mysqli_fetch_assoc($result)) {
                $assignedUsers[] = $row;
            }
            break;
    }

    return $assignedUsers;
}


function createCurrentFile()
{
    date_default_timezone_set("Asia/Kolkata");

    $date = date("Y-m-d");

    $month = date("Y-m");
    $filename = "message/$month.json";

    if (!is_dir("message")) {
        mkdir("message", 0777, true);
    }

    if (!file_exists($filename)) {
        $data = [[$date => new stdClass()]];
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
    }
}
