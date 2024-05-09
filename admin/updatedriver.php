<?php
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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $driverlicense = $_POST["driverlicense"];
    $contact = $_POST["contact"];
    $information = $_POST["information"]; // New field for information

    // Update the driver information in the database
    $query = "UPDATE drivers SET name=?, driverlicense=?, contact=?, information=? WHERE driverid=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $driverlicense, $contact, $information, $driverid);

    // Execute the statement
    if ($stmt->execute()) {
        // Close the prepared statement
        $stmt->close();

        // Insert activity log
        $activityMessage = "Updated driver information: $name";
        insertActivityLog($activityMessage);

        // Close the database connection
        $conn->close();
        // Redirect to the managedriver.php page with a success message
        echo "<script>alert('Driver information updated successfully.'); window.location.href = 'managedriver.php';</script>";
        exit();
    } else {
        // Display an error message if the update fails
        echo "Error: " . $conn->error;
    }
}

// Fetch the current driver information
$query = "SELECT * FROM drivers WHERE driverid=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $driverid);
$stmt->execute();
$result = $stmt->get_result();

// Check if there is a valid result
if ($result->num_rows == 1) {
    // Fetch the driver details
    $driver = $result->fetch_assoc();
} else {
    // Redirect to managedriver.php if driverid is not found
    header("Location: managedriver.php");
    exit();
}

// Close the prepared statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Driver</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'head.php';?>

    <div class="headerss">
        <h2><i class=""></i> Update Driver</h2>
    </div>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?driverid=" . $driverid); ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $driver['name']; ?>" required><br>

        <label for="driverlicense">Driver License:</label>
        <input type="text" id="driverlicense" name="driverlicense" value="<?php echo $driver['driverlicense']; ?>" required><br>

        <label for="contact">Contact:</label>
        <input type="text" id="contact" name="contact" value="<?php echo $driver['contact']; ?>" required><br>

        <label for="information">Information:</label> <!-- New field for information -->
        <textarea id="information" name="information"><?php echo $driver['information']; ?></textarea><br> <!-- Textarea for information -->

        <input type="submit" value="Update Driver">
    </form>
</body>
</html>
