<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if it's not already started
}

// Check if the user is logged in
if(isset($_SESSION['userid'])) {
    // Check if the announcement has already been displayed for this user
    if(!isset($_SESSION['announcement_displayed'])) {
        // Set the session variable to indicate that the announcement has been displayed
        $_SESSION['announcement_displayed'] = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floating Announcement</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="cdn.css">
    <style>
        /* Additional CSS styles */
        #floatingAnnouncement {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999; /* Ensure it's above other elements */
            display: none;
            max-width: 300px; /* Set max width */
            height: auto; /* Auto height */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); /* Box shadow */
            background-color: #ffffff; /* Background color */
        }
        #floatingAnnouncement .close {
            position: absolute;
            top: 5px;
            right: 10px;
        }
        #floatingAnnouncement img {
            width: 100px; /* Image width */
            height: auto; /* Auto height */
            border-radius: 10px 0 0 10px; /* Rounded corners on the left */
        }
        #floatingAnnouncement .media-body {
            padding: 10px;
        }
    </style>
</head>
<body>

<?php
// Include your PHP code here
include("admin/connection.php");

// Fetch up to 3 announcements from the database
$fetchAnnouncementsQuery = "SELECT * FROM announcements WHERE datestart <= CURDATE() AND dateend >= CURDATE() ORDER BY created_at DESC LIMIT 1";
$announcementsResult = mysqli_query($conn, $fetchAnnouncementsQuery);

// Check if there are any announcements
if (mysqli_num_rows($announcementsResult) > 0) {
    // Display the floating announcement container
?>
<div id="floatingAnnouncement" class="alert alert-primary alert-dismissible fade show">
    <?php
    while ($announcement = mysqli_fetch_assoc($announcementsResult)) {
    ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="closeAnnouncement()">
        <span aria-hidden="true">&times;</span>
    </button>
    <img src="announcement/<?php echo $announcement['image']; ?>" alt="<?php echo $announcement['title']; ?>" class="mr-3">
    <div class="media-body">
        <h5 class="mt-0"><?php echo $announcement['title']; ?></h5>
        <?php echo $announcement['context']; ?>
    </div>
    <?php
    }
?>
</div>
<?php
}

// Close your database connection
mysqli_close($conn);
?>
<?php
    } // End of if(!isset($_SESSION['announcement_displayed']))
} // End of if(isset($_SESSION['userid']))
?>

<script>
    // JavaScript to show after 5 seconds and auto-hide after 15 seconds
    setTimeout(function() {
        document.getElementById("floatingAnnouncement").style.display = "block";
    }, 5000);

    setTimeout(function() {
        closeAnnouncement();
    }, 15000);

    function closeAnnouncement() {
        document.getElementById("floatingAnnouncement").style.display = "none";
    }
</script>
</body>
</html>
