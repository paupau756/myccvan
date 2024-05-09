<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['adminid'])) {
    // Redirect to login.php if not logged in
    header("Location: adminlogin.php");
    exit();
}

// Include the necessary files
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

// Check if vehicleid is set in the query parameters
if (!isset($_GET['vehicleid'])) {
    // Redirect or handle as needed when vehicleid is not provided
    header("Location: managevehicle.php");
    exit();
}

// Get the vehicleid from the query parameters
$vehicleid = $_GET['vehicleid'];

// Fetch details of the specific vehicle
$query = "SELECT vehiclename FROM vehicles WHERE vehicleid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $vehicleid);
$stmt->execute();
$result = $stmt->get_result();

// Check if there is a valid result
if ($result->num_rows > 0) {
    // Vehicle found, delete it
    $deleteQuery = "DELETE FROM vehicles WHERE vehicleid = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $vehicleid);
    $stmt->execute();

    // Check if the vehicle was deleted successfully
    if ($stmt->affected_rows > 0) {
        // Vehicle deleted successfully
        $vehiclename = $result->fetch_assoc()['vehiclename']; // Retrieve vehiclename
        echo "<script>alert('Vehicle deleted successfully.'); window.location.href = 'managevehicle.php';</script>";

        // Construct activity log message
        $activityMessage = "Deleted vehicle: $vehiclename";

        // Insert activity log
        insertActivityLog($activityMessage);
    } else {
        // Error deleting vehicle
        echo "<script>alert('Error deleting vehicle.'); window.location.href = 'managevehicle.php';</script>";
    }

    // Close the prepared statement
    $stmt->close();
} else {
    // Handle the case when the vehicle is not found
    echo "<script>alert('Vehicle not found.'); window.location.href = 'managevehicle.php';</script>";
}

// Close the database connection
$conn->close();
?>
