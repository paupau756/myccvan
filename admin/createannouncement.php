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

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input values (you may need to add more validation)
    $title = $_POST['title'];
    $context = $_POST['context'];
    $datestart = $_POST['datestart'];
    $dateend = $_POST['dateend'];

    // Handle image upload
    $targetDir = "../announcement/";
    $uploadedImages = [];

    // Check if files were uploaded
    if (!empty($_FILES['images']['name'][0])) {
        // Loop through each uploaded file
        foreach ($_FILES['images']['name'] as $key => $fileName) {
            $targetFilePath = $targetDir . basename($fileName);

            // Move the file to the specified directory
            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFilePath)) {
                $uploadedImages[] = $targetFilePath;
            } else {
                echo "Error uploading file.";
            }
        }
    }

    // Insert data into the announcements table
    $insertQuery = "INSERT INTO announcements (title, image, context, datestart, dateend)
                    VALUES ('$title', '" . implode(',', $uploadedImages) . "', '$context', '$datestart', '$dateend')";

    if ($conn->query($insertQuery) === TRUE) {
        // Insert activity log
        $activityMessage = "Created a new announcement: $title";
        insertActivityLog($activityMessage);

        // Redirect to a success page or any other appropriate action
        header("Location: announcement.php");
        exit();
    } else {
        echo "Error: " . $insertQuery . "<br>" . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'head.php';?>



<div class="main">
        <!-- Header -->
        <div class="headerss">
            <h2> Create Announcements</h2>
        </div>
        
    </div>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="context">Context:</label>
        <textarea name="context" id="context" rows="4" cols="50" required></textarea>

        <label for="datestart">Start Date:</label>
        <input type="date" name="datestart" min="<?php echo date('Y-m-d'); ?>" required>

        <label for="dateend">End Date:</label>
        <input type="date" name="dateend" min="<?php echo date('Y-m-d'); ?>" required>

        <label for="images">Images:</label>
        <input type="file" name="images[]" id="images" accept="image/*" multiple>

        <input type="submit" value="Create Announcement">
    </form>
</div>

<?php include 'adfooter.php'; ?>

</body>
</html>
