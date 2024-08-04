<?php
session_start();

$email = "";
$password = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and Password are required!";
    } else {
        include "db.php";
        $dbConnection = getDataBaseConnection();
        
        if ($dbConnection->connect_error) {
            die("Connection failed: " . $dbConnection->connect_error);
        }

        $statement = $dbConnection->prepare(
            "SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ?"
        );

        if ($statement === false) {
            die("Error preparing statement: " . $dbConnection->error);
        }

        $statement->bind_param('s', $email);
        if (!$statement->execute()) {
            die("Error executing statement: " . $statement->error);
        }

        $statement->bind_result($id, $first_name, $last_name, $db_email, $db_password, $db_role);
        if ($statement->fetch()) {
            if ($password === $db_password) { // Ensure this matches how passwords are stored
                $_SESSION["id"] = $id;
                $_SESSION["first_name"] = $first_name;
                $_SESSION["last_name"] = $last_name;
                $_SESSION["email"] = $db_email;
                $_SESSION["role"] = $db_role;

                header("Location: index.php");
                exit;
            } else {
                $error = "Email or password incorrect";
            }
        } else {
            $error = "Email or password incorrect";
        }

        $statement->close();
        $dbConnection->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Account</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Login</h2>
    <form method="post">
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <div class="input-box">
            <input type="text" placeholder="Enter your email" value="<?= htmlspecialchars($email) ?>" name="email">
        </div>
        <div class="input-box">
            <input type="password" placeholder="Enter password" name="password">
        </div>
        <div class="input-box button">
            <input type="submit" value="Login">
        </div>
        <div class="text">
            <h3>Don't have an account? <a href="register_auth.php">Register here</a></h3>
        </div>
    </form>
</div>
</body>
</html>
