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

// Check if tourid is provided in the URL
if (!isset($_GET['tourid'])) {
    header("Location: managetours.php");
    exit();
}

$tourid = $_GET['tourid'];

// Fetch tour details based on tourid
$queryTour = "SELECT * FROM tours WHERE tourid = $tourid";
$resultTour = $conn->query($queryTour);

if ($resultTour->num_rows > 0) {
    $tour = $resultTour->fetch_assoc();
} else {
    // Redirect to managetours.php if tour not found
    header("Location: managetours.php");
    exit();
}

// Handle form submission for updating a tour
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variable to store update messages
    $updateMessages = array();

    if(isset($_POST["destination"])) {
        $destination = $_POST["destination"];
        $updateQuery = "UPDATE tours SET destination = '$destination' WHERE tourid = $tourid";
        if ($conn->query($updateQuery) === TRUE) {
            $updateMessages[] = "Destination updated successfully.";
        } else {
            echo "Error updating destination: " . $conn->error . "<br>";
        }
    }

    if(isset($_POST["touractivities"])) {
        $touractivities = $_POST["touractivities"];
        $updateQuery = "UPDATE tours SET touractivities = '$touractivities' WHERE tourid = $tourid";
        if ($conn->query($updateQuery) === TRUE) {
            $updateMessages[] = "Tour activities updated successfully.";
        } else {
            echo "Error updating tour activities: " . $conn->error . "<br>";
        }
    }

    if(isset($_POST["tourdetails"])) {
        $tourdetails = $_POST["tourdetails"];
        $updateQuery = "UPDATE tours SET tourdetails = '$tourdetails' WHERE tourid = $tourid";
        if ($conn->query($updateQuery) === TRUE) {
            $updateMessages[] = "Tour details updated successfully.";
        } else {
            echo "Error updating tour details: " . $conn->error . "<br>";
        }
    }

    if(isset($_POST["tourinclusions"])) {
        $tourinclusions = $_POST["tourinclusions"];
        $updateQuery = "UPDATE tours SET tourinclusions = '$tourinclusions' WHERE tourid = $tourid";
        if ($conn->query($updateQuery) === TRUE) {
            $updateMessages[] = "Tour inclusions updated successfully.";
        } else {
            echo "Error updating tour inclusions: " . $conn->error . "<br>";
        }
    }

    if(isset($_POST["tourprice"])) {
        $tourprice = $_POST["tourprice"];
        $updateQuery = "UPDATE tours SET tourprice = '$tourprice' WHERE tourid = $tourid";
        if ($conn->query($updateQuery) === TRUE) {
            $updateMessages[] = "Tour price updated successfully.";
        } else {
            echo "Error updating tour price: " . $conn->error . "<br>";
        }
    }

    if(isset($_POST["tourduration"])) {
        $tourduration = $_POST["tourduration"];
        $updateQuery = "UPDATE tours SET tourduration = '$tourduration' WHERE tourid = $tourid";
        if ($conn->query($updateQuery) === TRUE) {
            $updateMessages[] = "Tour duration updated successfully.";
        } else {
            echo "Error updating tour duration: " . $conn->error . "<br>";
        }
    }

    // Handle image uploads
    if (!empty($_FILES["tourimages"]["tmp_name"][0])) {
        $uploadedImages = uploadImages($_FILES["tourimages"]);
        // Update tour images in the database
        $tourimages = implode(",", $uploadedImages);
        $updateQuery = "UPDATE tours SET tourimages = '$tourimages' WHERE tourid = $tourid";
        if ($conn->query($updateQuery) === TRUE) {
            $updateMessages[] = "Tour images updated successfully.";
        } else {
            echo "Error updating tour images: " . $conn->error . "<br>";
        }
    }

    if(isset($_POST["vehicleid"])) {
        $vehicleid = $_POST["vehicleid"];
        $updateQuery = "UPDATE tours SET vehicleid = '$vehicleid' WHERE tourid = $tourid";
        if ($conn->query($updateQuery) === TRUE) {
            $updateMessages[] = "Vehicle updated successfully.";
        } else {
            echo "Error updating vehicle: " . $conn->error . "<br>";
        }
    }

    // Construct activity log message
    $activityMessage = "Updated tour details for destination: $destination";

    // Insert activity log
    insertActivityLog($activityMessage);

    // Output update messages
    foreach ($updateMessages as $message) {
        echo $message . "<br>";
    }

    // Add script alert and redirect
    echo "<script>alert('Tour details updated successfully.'); window.location.href = 'managetour.php';</script>";
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Packages</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'head.php';?>

    <!-- Header with logout button -->
    <div class="headerss">
        <h2><i class="fas fa-user-shield"></i> Update Packages</h2>
    </div>

    <!-- Tour update form -->
    <form method="post" action="" enctype="multipart/form-data">
        <label for="destination">Tour Name:</label>
        <input type="text" id="destination" name="destination" value="<?php echo $tour['destination']; ?>"
               required><br>

        <label for="touractivities">Tour Activities:</label>
        <textarea id="touractivities" name="touractivities"
                  required><?php echo $tour['touractivities']; ?></textarea><br>

        <div class="form-row">
            <div class="form-column">
                <label for="tourduration">Tour Duration (Days):</label>
                <input type="number" id="tourduration" name="tourduration" value="<?php echo $tour['tourduration']; ?>" required>
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
        <textarea id="tourdetails" name="tourdetails"
                  required><?php echo $tour['tourdetails']; ?></textarea><br>

        <label for="tourinclusions">Tour Inclusions:</label>
        <textarea id="tourinclusions" name="tourinclusions"
                  required><?php echo $tour['tourinclusions']; ?></textarea><br>

        <div class="form-row">
            <div class="form-column">
                <label for="tourimages">Tour Images:</label>
                <input type="file" id="tourimages" name="tourimages[]" multiple accept="image/*">
            </div>
            <div class="form-column">
                <label for="tourprice">Tour Price:</label>
                <input type="number" id="tourprice" name="tourprice" min="3000" value="<?php echo $tour['tourprice']; ?>" required>
            </div>
        </div>

        <input type="submit" value="Update Tour">
    </form>

<!-- Link back to Manage Tours -->
<!-- <a href="managetours.php">Back to Manage Tours</a> -->

<?php include'adfooter.php';?>

</body>
</html>
