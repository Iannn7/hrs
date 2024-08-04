<?php
session_start();

if(isset($_SESSION["email"])){
    header("location: index.php");
    exit;
}

include "db.php";

$first_name = "";
$last_name = "";
$email = "";
$password = "";

$first_name_error = "";
$last_name_error = "";
$email_error = "";
$password_error = "";

$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($first_name)){
        $first_name_error = "First name is required!";
        $error = true;
    }
    if (empty($last_name)){
        $last_name_error = "Last name is required!";
        $error = true;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $email_error = "Email format is not valid!";
        $error = true;
    }
    if (empty($password)){
        $password_error = "Password is required";
        $error = true;
    }

    $dbConnection = getDataBaseConnection();

    $statement = $dbConnection->prepare("SELECT id FROM users WHERE email = ?");
    $statement->bind_param("s", $email);
    $statement->execute();
    $statement->store_result();
    if ($statement->num_rows > 0){
        $email_error = "Email is already used";
        $error = true;
    }
    $statement->close();

    if (!$error){
        $statement = $dbConnection->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        $statement->bind_param('ssss', $first_name, $last_name, $email, $password);
        $statement->execute();

        $insert_id = $statement->insert_id;
        $statement->close();

        $_SESSION["id"] = $insert_id;
        $_SESSION["first_name"] = $first_name;
        $_SESSION["last_name"] = $last_name;
        $_SESSION["email"] = $email;

        header("Location: login_auth.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title> 
    <link rel="stylesheet" href="style.css">
   </head>
<body>
  <div class="wrapper">
    <h2>Registration</h2>
    <form method="post">
    <div class="input-box">
        <input type="text" placeholder="Enter your first name" value="<?= $first_name ?>" name="first_name">
        <span class="error"><?= $first_name_error ?></span>
      </div>
      <div class="input-box">
        <input type="text" placeholder="Enter your last name" value="<?= $last_name ?>" name="last_name">
        <span class="error"><?= $last_name_error ?></span>
      </div>
      <div class="input-box">
        <input type="text" placeholder="Enter your email" value="<?= $email ?>" name="email">
        <span class="error"><?= $email_error ?></span>
      </div>
      <div class="input-box">
        <input type="password" placeholder="Create password" name="password">
        <span class="error"><?= $password_error ?></span>
      </div>


      <div class="input-box button">
        <input type="submit" value="Register Now">
      </div>
      <div class="text">
        <h3>Already have an account? <a href="login_auth.php">Login now</a></h3>
      </div>
    </form>
  </div>
</body>
</html>