<!-- for removing the notifications -->
<?php
// Start session and include connection
session_start();
include "admin/connection.php";

// Check if notification ID is provided in the URL
if (!isset($_GET['id'])) {
    // Redirect or handle missing ID
    header("Location: notifications.php");
    exit();
}

// Get the notification ID from the URL
$notifId = $_GET['id'];

// Delete the notification from the database
$deleteQuery = "DELETE FROM notify WHERE notifid = $notifId";
if ($conn->query($deleteQuery) === TRUE) {
    // Alert for successful deletion
    echo "<script>alert('Notification deleted successfully.');</script>";
} else {
    // Alert for deletion failure
    echo "<script>alert('Error deleting notification: " . $conn->error . "');</script>";
}

// Redirect back to the notifications page
echo "<script>window.location.href = 'notification.php';</script>";
?>
