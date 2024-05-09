<?php
// Include your database connection code here if not already included
include("connection.php");

// Function to insert log message into activitylogs table
function insertActivityLog($activity) {
    global $conn;
    $activity = mysqli_real_escape_string($conn, $activity); // Sanitize input
    $insertQuery = "INSERT INTO activitylogs (activities) VALUES ('$activity')";

    if ($conn->query($insertQuery) === TRUE) {
        return true;
    } else {
        echo "Error inserting activity log: " . $conn->error;
        return false;
    }
}

// Check if announceid is provided
if (!isset($_GET['announceid'])) {
    // Redirect or handle missing announceid
    echo "<script>alert('Announcement ID not provided.');</script>";
    header("Location: announcement.php");
    exit();
}

// Get the announceid from the URL
$announceid = $_GET['announceid'];

// Fetch the title of the announcement
$query = "SELECT title FROM announcements WHERE announceid = $announceid";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    // Handle non-existent announcement
    echo "<script>alert('Announcement not found.');</script>";
    header("Location: announcement.php");
    exit();
}

$row = $result->fetch_assoc();
$title = $row['title'];

// Delete announcement from the database
$deleteQuery = "DELETE FROM announcements WHERE announceid = $announceid";
if ($conn->query($deleteQuery) === TRUE) {
    // Insert activity log
    $activityMessage = "Deleted announcement: $title";
    insertActivityLog($activityMessage);

    // Display success alert and redirect
    echo "<script>alert('Announcement deleted successfully.');</script>";
    header("Location: announcement.php");
    exit();
} else {
    // Display error alert and redirect
    echo "<script>alert('Error deleting announcement: " . $conn->error . "');</script>";
    header("Location: announcement.php");
    exit();
}
?>
