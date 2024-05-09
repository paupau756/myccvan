<?php
session_start();
if (!isset($_SESSION["adminid"])) {
    header("Location: adminlogin.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>Head</title> -->
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
</head>
<body>

<header>
    <nav class="main-nav">
        <nav class="main-nav1">
            <!-- Dashboard Button -->
            <a href="index.php" class="dashboard-btn">DASHBOARD</a>
            <!-- Dropdown for Manages -->
            <div class="dropdown">
                <button class="dropbtn"><strong>MANAGES</strong> <i class="fas fa-caret-down"></i></button>
                <div class="dropdown-content">
                    <a href="manageinquiries.php">Manage Inquiries</a>
                    <a href="managerefund.php">Manage Refunds</a>
                    <a href="managecontact.php">Manage Contacts</a>
                    <a href="managetour.php">Manage Tours</a>
                    <a href="manageuser.php">Manage Users</a>
                    <a href="manageadmin.php">Manage Admins</a>
                    <a href="managedriver.php">Manage Drivers</a>
                    <a href="announcement.php">Announcements</a>
                    <a href="managevehicle.php">Manage Vehicles</a>
                </div>
            </div>
        </nav>

        <ul class="nav-list">
            <li class="nav-item">
                <a href="calendar.php"><i class="fas fa-calendar"></i> </a>
            </li>

            <li class="nav-item">
                <a href="notification.php" style="position: relative;"><i class="fas fa-bell"></i> 
                     <span id="unreadCount" class="notification-count">0</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="activitylogs.php"><i class="fa fa-book"></i></a>
            </li>

            <li class="nav-item">
                <a href="settings.php"><i class="fas fa-cogs"></i></a>
            </li>

            <li class="nav-item">
                <a href="#" class="logout-button" onclick="showLogoutAlert()"><i class="fas fa-sign-out-alt"></i> </a>
            </li>
            <!-- Add additional navigation items as needed -->
        </ul>
    </nav>
</header>


<script>
    function showLogoutAlert() {
        var result = confirm("Are you sure you want to log out?");
        if (result) {
            alert("You have been logged out!");
            // Redirect to logout script
            window.location.href = "adminlogout.php"; 
        } else {
            // Do nothing if "No" is clicked
        }
    }
</script>

<script>
    // Fetch unread notification count from the backend
    async function fetchUnreadNotificationCount() {
        try {
            const response = await fetch('getUnreadNotificationCount.php');
            const data = await response.json();

            // Update the count
            unreadNotifications = data.unreadCount;
            updateNotificationCount();
        } catch (error) {
            console.error('Error fetching unread notification count:', error);
        }
    }

    // Update the count
    function updateNotificationCount() {
        var countElement = document.getElementById('unreadCount');
        countElement.textContent = unreadNotifications;

        // Show/hide the count based on the number of unread notifications
        if (unreadNotifications > 0) {
            countElement.style.display = 'inline-block';
        } else {
            countElement.style.display = 'none';
        }
    }

    // Call the function to fetch and initialize the count
    fetchUnreadNotificationCount();
</script>
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
