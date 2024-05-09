<?php
// Include your database connection code here if not already included
include("admin/connection.php");

// Check if notifid is set in the query parameters
if (!isset($_GET['notifid'])) {
    // Handle as needed when notifid is not provided
    echo "Error: Notifid not provided.";
    exit();
}

// Get notifid from the query parameters
$notifid = $_GET['notifid'];

// Update the status to "read" in the database
$updateQuery = "UPDATE notify SET status = 'read' WHERE notifid = '$notifid'";
$updateResult = $conn->query($updateQuery);

// Check if the update was successful
if ($updateResult) {
    echo "Status updated to read successfully!";
} else {
    echo "Error updating status.";
}

// Close your database connection if necessary
mysqli_close($conn);
?>
