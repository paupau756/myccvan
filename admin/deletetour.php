<?php
session_start();
if (!isset($_SESSION["adminid"])) {
    header("Location: adminlogin.php");
    exit();
}

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

// Check if the tourid is set in the query parameters
if (isset($_GET['tourid'])) {
    $tourid = $_GET['tourid'];

    // Fetch tour details before deletion for notification message
    $tourQuery = "SELECT destination FROM tours WHERE tourid = $tourid";
    $tourResult = $conn->query($tourQuery);

    if ($tourResult->num_rows > 0) {
        $tour = $tourResult->fetch_assoc();

        // Delete the tour based on tourid
        $deleteQuery = "DELETE FROM tours WHERE tourid = $tourid";

        if ($conn->query($deleteQuery) === TRUE) {
            // Notify admin about the deleted tour
            $activityMessage = "Deleted tour: {$tour['destination']}";
            insertActivityLog($activityMessage);

            header("Location: managetour.php");
            exit();
        } else {
            echo "Error deleting tour: " . $conn->error;
        }
    } else {
        // Redirect to managetour.php if tour not found
        header("Location: managetour.php");
        exit();
    }
} else {
    // Redirect to managetour.php if tourid not set
    header("Location: managetour.php");
    exit();
}
?>
