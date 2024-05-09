<?php
// Include your database connection file
include("connection.php");

// Query to get the count of unread notifications
$query = "SELECT COUNT(*) AS unreadCount FROM notifyadmin WHERE status = 'unread'";
$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $unreadCount = $row['unreadCount'];

    // Return the count as JSON
    echo json_encode(["unreadCount" => $unreadCount]);
} else {
    // Handle the error (modify as needed)
    echo json_encode(["error" => "Error fetching unread notification count"]);
}

// Close the database connection
$conn->close();
?>
