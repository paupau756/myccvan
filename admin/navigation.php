<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'head.php';?>

<!-- Navigation.php -->
<div class="sidenav custom-sidenav">
    <div class="logo-container">
        <img src="uploads/mycc.jpg" alt="Logo" class="logo">
    </div>
    <a href="index.php"><i class="fas fa-home"></i><span class="nav-text"> Dashboard</span></a>
    <a href="manageinquiries.php"><i class="fas fa-question-circle"></i><span class="nav-text"> Manage Inquiries</span></a>
    <a href="managecontact.php"><i class="fa fa-comments"></i><span class="nav-text"> Manage Contacts</span></a>
    <a href="managetour.php"><i class="fas fa-globe"></i><span class="nav-text"> Manage Packages</span></a>
    <a href="manageuser.php"><i class="fas fa-users"></i><span class="nav-text"> Manage Users</span></a>
    <a href="manageadmin.php"><i class="fas fa-user-shield"></i><span class="nav-text"> Manage Admins</span></a>
    <a href="managedriver.php"><i class="fa fa-id-card" aria-hidden="true"></i><span class="nav-text"> Manage Drivers</span></a>
    <a href="announcement.php"><i class="fas fa-bullhorn"></i><span class="nav-text"> Manage Announcements</span></a>
    <a href="managevehicle.php"><i class="fa fa-car"></i><span class="nav-text"> Manage Vehicles</span></a>
</div>


<script>
// JavaScript to handle active menu item
var currentLocation = window.location.href;
var menuItem = document.querySelectorAll('.custom-sidenav a');

menuItem.forEach(function(element) {
    if (element.href === currentLocation) {
        element.classList.add('active');
    }
});
</script>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>