<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<?php
// Include the connection file
include 'admin/connection.php';

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
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <!-- <link rel="stylesheet" href="packages.css"> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="cdn2.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
</head>
<body>

<?php
// Start the session
// session_start();

// Check if the user is logged in
if(isset($_SESSION['userid'])) {
    $loggedin = true;

    // Include the database connection file
    include 'admin/connection.php';

    // Prepare and execute a query to fetch the user's name
    $userid = $_SESSION['userid'];
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
}
?>

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
        <?php if($loggedin) { ?>
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
        <?php } else { ?>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="login.php">LOGIN</a>
            </li>
        </ul>
        <?php } ?>
    </div>
</nav>






<!-- notification start here -->
<div class="notification-header">
    <h1>Notifications</h1>
</div>

<?php
// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    // Redirect to the login page or handle as needed
    header("Location: login.php");
    exit();
}

// Include your database connection code here if not already included
include("admin/connection.php");

// Get userid from the session
$userid = $_SESSION['userid'];

// Fetch notifications for the user
$query = "SELECT `notifid`, `inquiryid`, `message`, `status`, `created_at` FROM notify WHERE userid = '$userid' ORDER BY created_at DESC";
$result = $conn->query($query);

// Check if there are any notifications
if ($result->num_rows > 0) {
    // Get the total number of notifications
    $totalNotifications = $result->num_rows;

    // Display the total number of notifications
    // echo '<h2>Total Notifications: ' . $totalNotifications . '</h2>';

    // Display the notifications in a table
    echo '<table class="notification-table" border="1">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';

    // Initialize the notification count
    $count = 1;

    // Output each row of the notification table
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        // Display the notification count
        echo '<td>' . $count . '</td>';
        $count++; // Increment the notification count

        echo '<td>' . $row['message'] . '</td>';
        echo '<td>' . $row['status'] . '</td>';
        echo '<td>' . date('F j, Y', strtotime($row['created_at'])) . '</td>';

        // Check the status and display appropriate action
        echo '<td class="action-link">';
        if ($row['status'] === 'unread') {
            echo '<button onclick="openModal(\'Read: ' . ', Message: <ul><li>' . str_replace("\n", '</li><li>', $row['message']) . '</li></ul> ' . date('F j, Y', strtotime($row['created_at'])) . '\', \'' . $row['notifid'] . '\')">Read</button>';
        } else {
           echo '<button class="button4" onclick="openModal(\'View Message: <ul><li>\' + \'' . str_replace("\n", '</li><li>', $row['message']) . '\' + \'</li></ul> ' . date('F j, Y', strtotime($row['created_at'])) . '\')">View</button>';
        }
        echo '<br><br>';
        // Add the remove action button
        echo '<a href="remove.php?id=' . $row['notifid'] . '" class="remove-link" onclick="return confirm(\'Are you sure you want to remove this item?\')"> Remove</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p>No notifications available.</p>';
}

// Close your database connection if necessary
mysqli_close($conn);
?>

<!-- Add this modal structure and JavaScript function to your HTML file -->
<div id="notificationModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modalContent"></div>
    </div>
</div>

<script>
    // Get the modal and the modal content element
    var modal = document.getElementById("notificationModal");
    var modalContent = document.getElementById("modalContent");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
        // Reload the page
        location.reload();
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
            // Reload the page
            location.reload();
        }
    }

    // Function to open the modal with specified content
    function openModal(content, notifid) {
        modalContent.innerHTML = content;
        modal.style.display = "block";

        // Update status to "read"
        updateNotificationStatus(notifid);
    }

    // Function to update notification status to "read"
    function updateNotificationStatus(notifid) {
        // Send AJAX request to update status
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "update_status.php?notifid=" + notifid, true);
        xhr.send();
    }
</script>

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
