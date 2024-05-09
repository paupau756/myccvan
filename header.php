<?php
// Include the connection file
include 'admin/connection.php';

// Start the session
session_start();

// Check if the user is logged in
if(isset($_SESSION['userid'])) {
    $loggedin = true;

    // Get the logged-in user's ID
    $userid = $_SESSION['userid'];

    // Query to fetch the count of unread notifications for the logged-in user
    $query = "SELECT COUNT(*) AS unread_count FROM notify WHERE userid = ? AND status = 'unread'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the unread notification count
    $row = $result->fetch_assoc();
    $unread_count = $row['unread_count'];

    // Include the database connection file
    include 'admin/connection.php';

    // Prepare and execute a query to fetch the user's name
    $query = "SELECT name FROM users WHERE userid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the query was successful and if a row was returned
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['name'];
    } else {
        // Error handling if user not found
        $username = "Unknown";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();

} else {
    $loggedin = false;
    $username = ""; // Set username to empty if user is not logged in
    $unread_count = 0; // Set unread count to 0 if user is not logged in
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MYCC Van Rental | Marilao</title>
    <!-- Online CDN for Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="cdn.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">
        <img src="admin/uploads/mycc.jpg" alt="MYCC VAN RENTAL Logo" height="30" style="border-radius: 12px;" >
        MYCC VAN RENTAL
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">HOME</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tours.php">PACKAGES</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="form.php">INQUIRE</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="notification.php">NOTIFICATIONS <?php echo ($loggedin && $unread_count > 0) ? "<span class='badge badge-danger'>$unread_count</span>" : ""; ?></a>
            </li>
        </ul>
        <ul class="navbar-nav">
            <?php if($loggedin) { ?>
            <!-- If the user is logged in, display dropdown menu -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <strong><?php echo $username; ?></strong>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="bookingcart.php">Booking Cart</a>
                    <a class="dropdown-item" href="bookinghistory.php">Booking History</a>
                    <a class="dropdown-item" href="settings.php">Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
            <?php } else { ?>
            <!-- If the user is not logged in, display login link -->
            <li class="nav-item">
                <a class="nav-link" href="login.php">LOGIN</a>
            </li>
            <?php } ?>
        </ul>
    </div>
</nav>

<script>
    // JavaScript to toggle the collapsed navbar when the toggle button is clicked
    document.addEventListener("DOMContentLoaded", function() {
        var navbarToggler = document.querySelector('.navbar-toggler');
        var navbarCollapse = document.querySelector('.navbar-collapse');

        navbarToggler.addEventListener('click', function() {
            navbarCollapse.classList.toggle('show');
        });

        // Open the dropdown menu when clicking on the user's name
        var dropdownToggle = document.querySelector('.dropdown-toggle');
        dropdownToggle.addEventListener('click', function() {
            var dropdownMenu = this.nextElementSibling;
            dropdownMenu.classList.toggle('show');
        });

        // Close the collapsed navbar when a nav link is clicked on larger screens
        var navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth > 992) { // Only close navbar on larger screens
                    navbarCollapse.classList.remove('show');
                }
            });
        });
    });
</script>



</body>
</html>
