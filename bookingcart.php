<?php
// Include the connection file
include 'admin/connection.php';

// Start session
session_start();

// Initialize unread count
$unread_count = 0;

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
    if ($row = $result->fetch_assoc()) {
        $unread_count = $row['unread_count'];
    }

    // Close the statement
    $stmt->close();
} else {
    // User is not logged in
    $loggedin = false;
}

// Close the database connection
$conn->close();
?>

<?php
// Start session and include connection
// session_start();
include "admin/connection.php";

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    // Redirect to login page or handle unauthorized access
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];
$query = "SELECT * FROM inquiries 
          WHERE userid = $userid 
          AND status NOT IN ('Cancelled', 'Completed')
          ORDER BY datestart ASC, timestart ASC"; 
$result = $conn->query($query);



// Check if the user is logged in
if(isset($_SESSION['userid'])) {
    $loggedin = true;

    // Prepare and execute a query to fetch the user's name
    $query = "SELECT name FROM users WHERE userid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $userResult = $stmt->get_result();

    // Check if the query was successful and if a row was returned
    if($userResult && $userResult->num_rows > 0) {
        $row = $userResult->fetch_assoc();
        $username = $row['name'];
    } else {
        // Error handling if user not found
        $username = "Unknown";
    }

    // Close the statement
    $stmt->close();
} else {
    $loggedin = false;
    $username = ""; // Set username to empty if user is not logged in
}

// Close the database connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="cdn2.css">
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
                <a class="nav-link" href="notification.php">NOTIFICATIONS <?php echo ($unread_count > 0) ? "<span class='badge badge-danger'>$unread_count</span>" : ""; ?></a>
            </li>
        </ul>
        <ul class="navbar-nav">
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
        </ul>
    </div>
</nav>

<div class="booking-cart">
    <h1>Booking Cart</h1>
</div>

<table class="booking-table" border="1">
    <tr>
        <th>Destination</th>
        <th>Pickup</th>
        <th>Date Start</th>
        <th>Date End</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr";
            if ($row['status'] === 'Confirmed') {
                echo " class='confirmed'";
            } elseif ($row['status'] === 'Cancelled') {
                echo " class='cancelled'";
            }
            echo ">";
            echo "<td>" . $row['destination'] . "</td>";
            echo "<td>" . $row['pickup'] . "</td>";
            echo "<td>" . date('M d, Y | h:i A', strtotime($row['datestart'] . ' ' . $row['timestart'])) . "</td>";
            echo "<td>" . date('M d, Y | h:i A', strtotime($row['dateend'] . ' ' . $row['timeend'])) . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>";
            
            // Display View button for both Pending and Confirmed statuses
            echo "<a href='viewbooking.php?id=" . $row['inquiryid'] . "' class='view-booking-link'>View</a>";
            
            // Display Cancel button for Pending status only
            if ($row['status'] === 'Pending') {
                echo " | <a href='cancelbooking.php?id=" . $row['inquiryid'] . "' class='cancel-booking-link'>Cancel</a>";
            }
            
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No inquiries found.</td></tr>";
    }
    ?>
</table>


<script>
    // JavaScript to show the dropdown menu when clicking on the user's name
    document.addEventListener("DOMContentLoaded", function() {
        var dropdownToggle = document.querySelector('.dropdown-toggle');

        dropdownToggle.addEventListener('click', function() {
            var dropdownMenu = this.nextElementSibling;
            dropdownMenu.classList.toggle('show');
        });

        // JavaScript to toggle the collapsed navbar when the toggle button is clicked
        var navbarToggler = document.querySelector('.navbar-toggler');
        var navbarCollapse = document.querySelector('.navbar-collapse');

        navbarToggler.addEventListener('click', function() {
            navbarCollapse.classList.toggle('show');
        });
    });
</script>

</body>
</html>
