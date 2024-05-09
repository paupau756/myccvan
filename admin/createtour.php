<?php

include("connection.php");

// Function to handle image uploads
function uploadImages($imageFiles)
{
    $uploadedImages = array();

    foreach ($imageFiles["tmp_name"] as $key => $tmp_name) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($imageFiles["name"][$key]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($tmp_name);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowedFormats = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowedFormats)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($tmp_name, $target_file)) {
                $uploadedImages[] = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    return $uploadedImages;
}

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

// Handle form submission for creating a new tour
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $destination = $_POST["destination"];
    $touractivities = $_POST["touractivities"];
    $tourdetails = $_POST["tourdetails"];
    $tourinclusions = $_POST["tourinclusions"];
    $tourprice = $_POST["tourprice"];
    $tourduration = $_POST["tourduration"];
    $vehicleid = $_POST["vehicleid"];

    // Validate inputs (you can add more specific validations)
    if (empty($destination) || empty($touractivities) || empty($tourdetails) || empty($tourinclusions) || empty($tourprice) || empty($tourduration) || empty($vehicleid)) {
        echo "All fields are required.";
    } else {
        // Handle image uploads
        $uploadedImages = uploadImages($_FILES["tourimages"]);

        // Prepare and execute SQL query to insert tour details into the database
        $insertQuery = "INSERT INTO tours (destination, touractivities, tourimages, tourdetails, tourinclusions, tourprice, tourduration, vehicleid)
                        VALUES ('$destination', '$touractivities', '" . implode(",", $uploadedImages) . "', '$tourdetails', '$tourinclusions', '$tourprice', '$tourduration', ' $vehicleid')";

        if ($conn->query($insertQuery) === TRUE) {
            // Insert activity log
            $activityMessage = "Created a new packages: $destination";
            insertActivityLog($activityMessage);

            echo "<script>alert('Tour created successfully.'); window.location.href = 'managetour.php';</script>";
        } else {
            echo "<script>alert('Error creating tour: " . $conn->error . "'); window.location.href = 'managetour.php';</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Packages</title>
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
            <h2>Create Destination</h2>
    </div>

    <!-- Tour creation form -->
    <form method="post" action="" enctype="multipart/form-data">
        <label for="destination">Tour Name:</label>
        <input type="text" id="destination" name="destination" required><br>

        <label for="touractivities">Tour Activities:</label>
        <textarea id="touractivities" name="touractivities" required></textarea><br>

        <div class="form-row">
            <div class="form-column">
                <label for="tourduration">Tour Duration (Days):</label>
                <input type="number" id="tourduration" name="tourduration" required>
            </div>
            <div class="form-column">
                <label for="vehicleid">Select Vehicle:</label>
                <select name="vehicleid" required>
                    <!-- Populate options dynamically from the database -->
                    <?php
                    // Query to fetch vehicles from the database
                    $vehicleQuery = "SELECT vehicleid, vehiclename, vehicletype, max_seats FROM vehicles";
                    $vehicleResult = mysqli_query($conn, $vehicleQuery);
                    if ($vehicleResult && mysqli_num_rows($vehicleResult) > 0) {
                        while ($row = mysqli_fetch_assoc($vehicleResult)) {
                            // Display vehicle name, type, and max seats in the option text
                            echo "<option value='{$row['vehicleid']}'> Type:{$row['vehicletype']} (Brand: {$row['vehiclename']}, Seater Capacity: {$row['max_seats']})</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <label for="tourdetails">Tour Details:</label>
        <textarea id="tourdetails" name="tourdetails" required></textarea><br>

        <label for="tourinclusions">Tour Inclusions:</label>
        <textarea id="tourinclusions" name="tourinclusions" required></textarea><br>

        <div class="form-row">
            <div class="form-column">
                <label for="tourimages">Tour Images:</label>
                <input type="file" id="tourimages" name="tourimages[]" multiple accept="image/*">
            </div>
            <div class="form-column">
                <label for="tourprice">Tour Price:</label>
                <input type="number" id="tourprice" name="tourprice" min="3000" value="3000" required>
            </div>
        </div>

        <input type="submit" value="Create Tour">
    </form>



</body>
</html>
