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

// Check if announceid is provided in the URL
if (!isset($_GET['announceid'])) {
    // Redirect or handle missing ID
    header("Location: manageannouncements.php");
    exit();
}

// Get the announceid from the URL
$announceid = $_GET['announceid'];

// Retrieve announcement details from the database based on announceid
$query = "SELECT * FROM announcements WHERE announceid = $announceid";
$result = $conn->query($query);

// Check if the announcement exists
if ($result->num_rows === 0) {
    // Handle non-existent announcement
    echo "Announcement not found.";
    exit();
}

// Fetch announcement details
$row = $result->fetch_assoc();
$title = $row['title'];
$context = $row['context'];
$datestart = $row['datestart'];
$dateend = $row['dateend'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input values (you may need to add more validation)
    $newTitle = $_POST['title'];
    $newContext = $_POST['context'];
    $newDatestart = $_POST['datestart'];
    $newDateend = $_POST['dateend'];

    // Update announcement in the database
    $updateQuery = "UPDATE announcements SET title = '$newTitle', context = '$newContext', datestart = '$newDatestart', dateend = '$newDateend' WHERE announceid = $announceid";
    if ($conn->query($updateQuery) === TRUE) {
        // Insert activity log
        $activityMessage = "Updated announcement: $newTitle";
        insertActivityLog($activityMessage);

        // Redirect to manage announcements page or any other appropriate action
        echo "<script>alert('Announcement updated successfully.');</script>";
        header("Location: announcement.php");
        exit();
    } else {
        echo "<script>alert('Error updating announcement: " . $conn->error . "');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Announcement</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'head.php';?>

    <!-- Header -->
        <div class="headerss">
            <h2> Update Announcements</h2>
        </div>

    <!-- Your HTML form for updating the announcement -->
    <!-- Make sure to set the form action to the current page -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?announceid=' . $announceid; ?>" method="post">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo $title; ?>" required>

        <label for="context">Context:</label>
        <textarea name="context" id="context" rows="4" cols="50" required><?php echo $context; ?></textarea>

        <label for="datestart">Start Date:</label>
        <input type="date" name="datestart" value="<?php echo $datestart; ?>" min="<?php echo date('Y-m-d'); ?>" required>

        <label for="dateend">End Date:</label>
        <input type="date" name="dateend" value="<?php echo $dateend; ?>" min="<?php echo date('Y-m-d'); ?>" required>

        <input type="submit" value="Update Announcement">
    </form>

    <?php include 'adfooter.php'; ?>
</body>
</html>
