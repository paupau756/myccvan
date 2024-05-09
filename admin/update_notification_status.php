<?php
// Include your database connection code here if not already included
include("connection.php");

// Check if notifyid is provided
if(isset($_GET['notifyid'])) {
    $notifyid = $_GET['notifyid'];

    // Update notification status to "read" in the database
    $updateQuery = "UPDATE notifyadmin SET status = 'read' WHERE notifyid = $notifyid";
    if ($conn->query($updateQuery) === TRUE) {
        // Status updated successfully
        echo "Notification status updated to 'read'";
    } else {
        // Failed to update status
        echo "Error updating notification status: " . $conn->error;
    }
} else {
    // Notifyid not provided
    echo "Notify ID not provided";
}

// Close your database connection if necessary
mysqli_close($conn);
?>
