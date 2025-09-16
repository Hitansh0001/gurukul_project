<?php
require_once "config/database.php";

session_start();

if (isset($_SESSION["user_id"])) {
    header("location: dashboard.php");
    exit;
}


$email = $password = "";
$email_err = $password_err = $login_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter email.";
    } else {
        $email = trim($_POST["email"]);
    }


    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {

        $sql = "SELECT id, name, password FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {

            $param_email = $email;
            mysqli_stmt_bind_param($stmt, "s", $param_email);


            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {

                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {

                            session_start();

                            $_SESSION["user_id"] = $id;
                            // $_SESSION["user_type"] = $type;
                            $_SESSION["username"] = $username;
                            // $_SESSION["role"] = $role;
                            $_SESSION["last_activity"] = time();
                            $_SESSION["just_logged_in"] = 1;

                            header("location: frontend/index.html");
                            exit;
                        } else {
                            $login_err = "Invalid password.";
                        }
                    }
                } else {

                    $login_err = "Invalid email";
                }
            } else {
                $login_err = 'Oops! Something went wrong. Please try again later.';
            }

            mysqli_stmt_close($stmt);
        }
    }


    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gurukul</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>


<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h3>Gurukul</h3>
                <p>Please fill in your credentials to login.</p>
            </div>

            <?php
            if (!empty($login_err)) {
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }
            if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
                echo '<div class="alert alert-warning">Your session has expired. Please login again.</div>';
            }
            if (isset($_GET['registered']) && $_GET['registered'] == 1) {
                echo '<div class="alert alert-success">Registration successful! You can now log in.</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary w-100" value="Login">
                </div>
                <p class="text-center">Don't have an account? <a href="register.php">Sign up now</a></p>
                <!-- <p class="text-center">Demo credentials: testuser / password123 (Manager)</p>
                <p class="text-center">Demo credentials: regularuser / password123 (User)</p> -->
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>