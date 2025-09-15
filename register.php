<?php

require_once "config/database.php";


$username = $password = $email = $confirm_password = "";
$type = 0;
$username_err = $email_err =  $password_err = $confirm_password_err = $type_err =  "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter a email.";
    } else if (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {

        $sql = "SELECT id FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {

            $param_email = trim($_POST["email"]);


            mysqli_stmt_bind_param($stmt, "s", $param_email);


            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                $login_err =  "Oops! Something went wrong. Please try again later!";
            }


            mysqli_stmt_close($stmt);
        }
    }

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    // if (empty(trim($_POST["type"])) && trim($_POST["type"]) == 0) {
    //     $type_err = "Please Select type";
    // } else {
    //     if (trim($_POST["type"]) == 1) {
    //         $type = 1;
    //     } else if (trim($_POST["type"]) == 2) {
    //         $type = 0;
    //     }
    // }
    // if ($type == 0) {
    //     $role = 'manager';
    // }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }


    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($type_err)) {


        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        // echo $sql;
        // print_r($conn);
        // print_r($_POST);

        if ($stmt = mysqli_prepare($conn, $sql)) {
            print_r($stmt);

            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);


            mysqli_stmt_bind_param($stmt, "sss", $username, $param_email, $param_password);

            if (mysqli_stmt_execute($stmt)) {

                header("location: login.php?registered=1");
            } else {
                $login_err = "Oops! Something went wrong. Please try again later !!";
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
    <title>Register - Gurukul</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .register-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .register-header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <h2>Sign Up</h2>
                <p>Please fill this form to create an account.</p>
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
            $login_err = '';
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Email</label>
                    <input type="text" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="mb-3">
                    <!-- <label for="type" class="form-label">Type</label>
                    <select name="type" class="form-control <?php echo (!empty($type_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $type_err; ?>" id="">
                        <option value="0">Select Type</option>
                        <option value="1">Individual</option>
                        <option value="2">Team</option>
                    </select> -->
                    <span class="invalid-feedback"><?php echo $type_err; ?></span>

                </div>
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary w-100" value="Submit">
                </div>
                <p class="text-center">Already have an account? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>