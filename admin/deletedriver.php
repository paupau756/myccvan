<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['adminid'])) {
    // Redirect to admin login page if not logged in
    header("Location: adminlogin.php");
    exit();
}

// Include the database connection file
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

// Check if driverid is set in the query parameters
if (!isset($_GET['driverid'])) {
    // Redirect to managedriver.php if driverid is not provided
    header("Location: managedriver.php");
    exit();
}

// Get the driverid from the query parameters
$driverid = $_GET['driverid'];

// Fetch the name of the driver
$query = "SELECT name FROM drivers WHERE driverid=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $driverid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    // Fetch the driver name
    $row = $result->fetch_assoc();
    $driverName = $row['name'];
} else {
    // Redirect to managedriver.php if driverid is not found
    header("Location: managedriver.php");
    exit();
}

// Close the prepared statement
$stmt->close();

// Delete the driver from the database
$query = "DELETE FROM drivers WHERE driverid=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $driverid);

// Execute the statement
if ($stmt->execute()) {
    // Close the prepared statement
    $stmt->close();

    // Insert activity log
    $activityMessage = "Deleted driver: $driverName";
    insertActivityLog($activityMessage);

    // Close the database connection
    $conn->close();
    // Redirect to the managedriver.php page with a success message
    echo "<script>alert('Driver deleted successfully.'); window.location.href = 'managedriver.php';</script>";
    exit();
} else {
    // Display an error message if the deletion fails
    echo "Error: " . $conn->error;
}
?>
