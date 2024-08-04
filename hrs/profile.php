<?php
session_start();
$authenticated = false;
$user_role = '';

if (isset($_SESSION["email"])) {
    $authenticated = true;
    $user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
}

// Database connection
require 'db.php';
$conn = getDataBaseConnection();

// Fetch user details from the database
$user_id = $_SESSION['id'];
$sql = "SELECT first_name, last_name, email, profile_image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission to update user details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $profile_image = $user['profile_image'];

    // Check if a file was uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_file_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_file_types)) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image = $target_file;
                echo "File uploaded successfully: " . $target_file . "<br>";
            } else {
                echo "Sorry, there was an error uploading your file.<br>";
            }
        } else {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
        }
    }

    // Prepare SQL statement to update user details
    $sql_update = "UPDATE users SET first_name = ?, last_name = ?, email = ?, profile_image = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $first_name, $last_name, $email, $profile_image, $user_id);

    if ($stmt_update->execute()) {
        // Update session variables
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email'] = $email;
        $_SESSION['profile_image'] = $profile_image;
        
        // Set success message in session
        $_SESSION['update_success'] = true;

        // Redirect to the profile page or any other page
        header('Location: profile.php');
        exit();
    } else {
        echo "Error: " . $stmt_update->error;
    }

    $stmt_update->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="font.css">        
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body style="background-color: #08618D;">
<?php
if (isset($_SESSION['update_success'])) {
    echo '<div id="success-alert" class="alert alert-success text-center">Updated successfully</div>';
    unset($_SESSION['update_success']); 
}
?>
<div class="container py-5">
    <div class="d-flex justify-content-center mt-5">
        <div class="border shadow p-4" style="width: 100%; max-width: 600px; background-color: #ffffff;">
            <h2 class="text-center mb-4">Profile</h2>
            <hr>
            <?php if ($user['profile_image']): ?>
                <div class="text-center mt-4">
                    <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="rounded-circle mb-5" style="width: 100px; height: 100px;">
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-4 fw-bold mb-2">First Name :</div>
                    <div class="col-sm-8 mb-2">
                        <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>">
                    </div>
                    <div class="col-sm-4 fw-bold mb-2">Last Name :</div>
                    <div class="col-sm-8 mb-2">
                        <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>">
                    </div>
                    <div class="col-sm-4 fw-bold mb-2">Email :</div>
                    <div class="col-sm-8 mb-2">
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    <div class="col-sm-4 fw-bold mb-2">Upload profile :</div>
                    <div class="col-sm-8 mb-2">
                        <input type="file" class="form-control" name="profile_image">
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-outline-primary me-2">Back</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.display = 'none';
        }, 3000);
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
