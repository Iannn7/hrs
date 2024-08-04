<?php
session_start();
$authenticated = false;
$user_role = ''; 

if (isset($_SESSION["email"])) {
    $authenticated = true;
    $user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hotel Reservation</title>
    <link rel="stylesheet" href="custom.css">        
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="">
<nav class="navbar navbar-expand-lg border-bottom shadow-sm w-100 px-5">
        <div class="container-fluid">
            <a class="navbar-brand me-5 fw-bold" style="color: #08618d;" href="index.php">Hotel Reservation</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 hover-li">
                    <li class="nav-item">
                        <a class="nav-link text-dark hover" href="index.php">Home</a>
                    </li>
                    <li class="nav-item nav-hover">
                        <a class="nav-link text-dark hover" href="index.php">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark hover" href="index.php">Offers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark hover" href="index.php">Contact Us</a>
                    </li>
                    <?php if ($authenticated && $user_role != 'admin') : ?>
                    <li class="nav-item">
                        <a class="nav-link text-dark hover" href="index.php">My Reservations</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($authenticated && $user_role != 'client') : ?>
                <li class="nav-item">
                    <a class="nav-link text-dark hover" href="index.php">Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark hover" href="admin.php">Create Admin</a>
                </li>
            <?php endif; ?>
                </ul>
                <form class="d-flex me-5" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>

                <?php if ($authenticated): ?>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                        <?php

require 'db.php'; // Include your database connection file

// Get database connection
$connection = getDataBaseConnection();

// Assuming the user's ID is stored in the session
$user_id = $_SESSION['id'];

// Prepare and execute the query
$stmt = $connection->prepare('SELECT profile_image FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the user data
$user = $result->fetch_assoc();
$profile_image = $user['profile_image'];

// Close the connection
$stmt->close();
$connection->close();
?>

<a class="nav-link dropdown-toggle text-dark d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    <?php if (!empty($profile_image)): ?>
        <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image" class="rounded-circle me-2" style="width: 30px; height: 30px;">
    <?php endif; ?>
    <?php
    if ($user_role === 'admin') {
        echo 'Admin';
    } elseif ($user_role === 'client') {
        echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);
    } else {
        echo 'User';
    }
    ?>
</a>

                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="login_auth.php" class="btn btn-primary me-2">Login</a>
                            <a href="register_auth.php" class="btn btn-primary me-2">Register</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
<div style="background-color: #08618d;">
    <div class="container text-white py-5">
      <div class="row align-items-center g-5">
        <div class="col-md-6">
          <h1 class="mb-5 display-2"><strong>See life from a different perspective</strong></h1>
          <p>Your to go reservation for your accommodation and hotel rooms.</p>
        </div>
        <div class="col-md-6 text-center">
          <img src="hotel.png" alt="Hotel">
        </div>
      </div>
    </div>
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
