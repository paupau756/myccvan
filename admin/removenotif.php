<?php
// Include your database connection code here if not already included
include("connection.php");

// Get the notifyid from the query parameters
$notifyid = $_GET['notifyid'];

// Prepare and execute the SQL query to remove the notification
$query = "DELETE FROM notifyadmin WHERE notifyid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $notifyid);

if ($stmt->execute()) {
    // Success alert
    echo "<script>alert('Notification removed successfully.');</script>";
} else {
    // Error alert
    echo "<script>alert('Failed to remove notification.');</script>";
}

// Close your database connection if necessary
mysqli_close($conn);

// Reload the page
echo "<script>window.location.reload();</script>";
?>
