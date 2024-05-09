<?php
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
$query = "SELECT * FROM vehicles WHERE vehicleid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $vehicleid);
$stmt->execute();
$result = $stmt->get_result();

// Check if there is a valid result
if ($result->num_rows > 0) {
    // Vehicle found, fetch details
    $vehicle = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $vehicletype = $_POST['vehicletype'];
        $vehiclename = $_POST['vehiclename'];
        $description = $_POST['description'];
        $max_seats = $_POST['max_seats'];
        $price = $_POST['price']; // New field for price

        // Update the vehicle in the database
        $updateQuery = "UPDATE vehicles SET vehicletype = ?, vehiclename = ?, description = ?, max_seats = ?, price = ? WHERE vehicleid = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssidi", $vehicletype, $vehiclename, $description, $max_seats, $price, $vehicleid);
        $stmt->execute();

        // Check if the vehicle was updated successfully
        if ($stmt->affected_rows > 0) {
            // Vehicle updated successfully
            echo "<script>alert('Vehicle updated successfully.'); window.location.href = 'managevehicle.php';</script>";

            // Construct activity log message
            $activityMessage = "Updated vehicle details for: $vehiclename";

            // Insert activity log
            insertActivityLog($activityMessage);
        } else {
            // Error updating vehicle
            echo "Error updating vehicle.";
        }

        // Close the prepared statement
        $stmt->close();
    }
} else {
    // Handle the case when the vehicle is not found
    echo "Vehicle not found.";
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Vehicle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'head.php';?>
    <!-- Add navigation or header if necessary -->

    <div class="headerss">
            <h2>Update Vehicle</h2>
    </div>

    <!-- Form to update the vehicle -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?vehicleid=' . $vehicleid); ?>">
        <label for="vehicletype">Vehicle Type:</label>
        <select name="vehicletype" required>
            <option value="single" <?php if ($vehicle['vehicletype'] == 'single') echo 'selected'; ?>>1 Vans</option>
            <option value="double" <?php if ($vehicle['vehicletype'] == 'double') echo 'selected'; ?>>2 Vans</option>
            <option value="3" <?php if ($vehicle['vehicletype'] == '3') echo 'selected'; ?>>3 Vans</option>
            <option value="4" <?php if ($vehicle['vehicletype'] == '4') echo 'selected'; ?>>4 Vans</option>
            <option value="5" <?php if ($vehicle['vehicletype'] == '5') echo 'selected'; ?>>5 Vans</option>
        </select><br><br>

        <label for="vehiclename">Vehicle Name:</label>
        <input type="text" name="vehiclename" value="<?php echo $vehicle['vehiclename']; ?>" required><br><br>

        <label for="description">Description:</label><br>
        <textarea name="description"><?php echo $vehicle['description']; ?></textarea><br><br>

        <label for="max_seats">Max Seats:</label>
        <input type="number" name="max_seats" min="1" value="<?php echo $vehicle['max_seats']; ?>" required><br><br>

        <!-- New field for price -->
        <label for="price">Price:</label>
        <input type="number" name="price" min="0" step="0.01" value="<?php echo $vehicle['price']; ?>" required><br><br>

        <input type="submit" value="Update Vehicle">
    </form>

</body>
</html>
