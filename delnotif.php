<?php
// Include your database connection code here if not already included
include("admin/connection.php");

// Check if notifid is set in the POST request
if (isset($_POST['notifid'])) {
    $notifid = $_POST['notifid'];

    // Delete the notification from the notifications table
    $deleteQuery = "DELETE FROM notifications WHERE notifid = '$notifid'";
    if ($conn->query($deleteQuery) === TRUE) {
        // Display an alert and redirect to notifications.php
        echo '<script>';
        echo 'alert("Notification deleted successfully!");';
        echo 'window.location.href = "notification.php";';
        echo '</script>';
        exit();
    } else {
        echo "Error deleting notification: " . $conn->error;
    }
}

// Close your database connection if necessary
mysqli_close($conn);
?>
