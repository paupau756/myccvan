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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $vehicletype = $_POST['vehicletype'];
    $vehiclename = $_POST['vehiclename'];
    $description = $_POST['description'];
    $max_seats = $_POST['max_seats'];
    $price = $_POST['price']; // Add this line to retrieve the price

    // Insert the vehicle into the database
    $query = "INSERT INTO vehicles (vehicletype, vehiclename, description, max_seats, price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssii", $vehicletype, $vehiclename, $description, $max_seats, $price); // Update bind_param with the new parameter
    $stmt->execute();

    // Check if the vehicle was inserted successfully
    if ($stmt->affected_rows > 0) {
        // Vehicle created successfully
        echo "<script>alert('Vehicle created successfully.'); window.location.href = 'managevehicle.php';</script>";

        // Construct activity log message
        $activityMessage = "Created a new vehicle: $vehiclename";

        // Insert activity log
        insertActivityLog($activityMessage);
    } else {
        // Error creating vehicle
        echo "Error creating vehicle.";
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Vehicle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'head.php';?>
    <!-- Add navigation or header if necessary -->

    <div class="headerss">
            <h2>Add Vehicle</h2>
    </div>

    <!-- Form to create a new vehicle -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="vehicletype">Number Of Vans:</label>
        <select name="vehicletype" required>
            <option value="single">1 Van</option>
            <option value="double">2 Vans</option>
            <option value="3">3 Vans</option>
            <option value="4">4 Vans</option>

            <option value="5">5 Vans</option>
        </select><br><br>

        <label for="vehiclename">Brands:</label>
        <input type="text" name="vehiclename" required><br><br>

        <label for="description">Description:</label><br>
        <textarea name="description"></textarea><br><br>

        <label for="max_seats">Seating Capacity:</label>
        <input type="number" name="max_seats" min="1" required><br><br>

        <label for="price">Rate:</label>
        <input type="number" name="price" min="0" step="0.01" required><br><br> <!-- Add the price input field -->

        <input type="submit" value="Create Vehicle">
    </form>

</body>
</html>
