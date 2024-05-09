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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $driverlicense = $_POST["driverlicense"];
    $contact = $_POST["contact"];
    $information = $_POST["information"]; // New field for information

    // Insert the new driver into the database
    $query = "INSERT INTO drivers (name, driverlicense, contact, information) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $name, $driverlicense, $contact, $information); // Update bind_param

    // Execute the statement
    if ($stmt->execute()) {
        // Close the prepared statement
        $stmt->close();

        // Insert activity log
        $activityMessage = "Added new driver: $name";
        insertActivityLog($activityMessage);

        // Close the database connection
        $conn->close();
        // Redirect to the managedriver.php page with a success message
        echo "<script>alert('Driver added successfully.'); window.location.href = 'managedriver.php';</script>";
        exit();
    } else {
        // Display an error message if the insertion fails
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Driver</title>
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
        <h2>Add Drivers</h2>
    </div>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="driverlicense">Driver License:</label>
        <input type="text" id="driverlicense" name="driverlicense" required><br>

        <label for="contact">Contact:</label>
        <input type="text" id="contact" name="contact" required><br>

        <label for="information">Information:</label> <!-- New field for information -->
        <textarea id="information" name="information"></textarea><br> <!-- Textarea for information -->

        <input type="submit" value="Add Driver">
    </form>
</body>
</html>
