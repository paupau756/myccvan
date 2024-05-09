<?php
// Include your database connection file here
include("connection.php");

// Get total number of users
$queryUsers = "SELECT COUNT(userid) AS totalUsers FROM users";
$resultUsers = $conn->query($queryUsers);
$totalUsers = ($resultUsers->fetch_assoc())['totalUsers'];

// Get total number of admin
$queryAdmin = "SELECT COUNT(adminid) AS totalAdmin FROM admin";
$resultAdmin = $conn->query($queryAdmin);
$totalAdmin = ($resultAdmin->fetch_assoc())['totalAdmin'];

// Get total number of Inquiries
$queryInquiries = "SELECT COUNT(inquiryid) AS totalInquiries FROM inquiries";
$resultInquiries = $conn->query($queryInquiries);
$totalInquiries = ($resultInquiries->fetch_assoc())['totalInquiries'];

// Get total number of tours
$queryTours = "SELECT COUNT(tourid) AS totalTours FROM tours";
$resultTours = $conn->query($queryTours);
$totalTours = ($resultTours->fetch_assoc())['totalTours'];

// Get total number of Contact
$queryMessage = "SELECT COUNT(messageid) AS totalMessage FROM contact_submissions";
$resultMessage = $conn->query($queryMessage);
$totalMessage = ($resultMessage->fetch_assoc())['totalMessage'];

// Get total number of tours
$queryFeedback = "SELECT COUNT(feedbackid) AS totalFeedback FROM feedback";
$resultFeedback = $conn->query($queryFeedback);
$totalFeedback = ($resultFeedback->fetch_assoc())['totalFeedback'];

// Get total number of admins
$queryAdmins = "SELECT COUNT(adminid) AS totalAdmins FROM admin";
$resultAdmins = $conn->query($queryAdmins);
$totalAdmins = ($resultAdmins->fetch_assoc())['totalAdmins'];

// Get total number of vehicles
$queryVehicles = "SELECT COUNT(vehicleid) AS totalVehicles FROM vehicles";
$resultVehicles = $conn->query($queryVehicles);
$totalVehicles = ($resultVehicles->fetch_assoc())['totalVehicles'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpg" href="uploads/mycc.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-iqoXFDVO/mP1MXH0wyDExzvPR28+zOITIdOaJAJ/KDUSzT8J6xC0q5iIqSF4DV3dOy8gktTnX00qrBcL/zke5A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>

    <?php include 'head.php';?>
       
    <!-- Header with logout button -->
    <div class="headerss" >
        <h2><i class="fas fa-user-shield"></i> Dashboard</h2>
        
    </div>

    <!-- Dashboard insights -->
    <div class="insights">
        <div class="insight">
            <i class="fas fa-question-circle"></i>
            <h3>Inquiries</h3>
            <p><?php echo $totalInquiries; ?></p>
        </div>
        <div class="insight">
            <i class="fa fa-comments"></i>
            <h3>Contact Message</h3>
            <p><?php echo $totalMessage; ?></p>
        </div>
        <div class="insight">
            <i class="fas fa-globe"></i>
            <h3>Packages</h3>
            <p><?php echo $totalTours; ?></p>
        </div>
        <div class="insight">
            <i class="fas fa-users"></i>
            <h3>Users</h3>
            <p><?php echo $totalUsers; ?></p>
        </div>
        <div class="insight">
            <i class="fas fa-user-shield"></i>
            <h3>Admins</h3>
            <p><?php echo $totalAdmin; ?></p>
        </div>
        <div class="insight">
            <i class="fas fa-comments"></i>
            <h3>Feedbacks</h3>
            <p><?php echo $totalFeedback; ?></p>
        </div>
    </div>


        <div class="upcoming-tasks">
            <h2>UPCOMING TASKS</h2>
        </div>

        <table class="activity-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Inquiry Date</th>
                    <th>Pickup</th>
                    <th>Destination</th>
                    <th>Action</th> <!-- Added header for the action column -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch confirmed inquiries for the activity task with a limit of 5, sorted by datestart
                $activityQuery = "SELECT inquiries.datestart, inquiries.dateend, inquiries.pickup, inquiries.destination, users.userid, users.name FROM inquiries INNER JOIN users ON inquiries.userid = users.userid WHERE inquiries.status = 'Confirmed' ORDER BY inquiries.datestart LIMIT 5";
                $activityResult = $conn->query($activityQuery);

                // Display each confirmed inquiry as a row in the table
                if ($activityResult->num_rows > 0) {
                    while ($row = $activityResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['userid']}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>" . date('M d, Y', strtotime($row['datestart'])) . " - " . date('M d, Y', strtotime($row['dateend'])) . "</td>";
                        echo "<td>{$row['pickup']}</td>";
                        echo "<td>{$row['destination']}</td>";
                        // Action button with bell icon
                        echo "<td><a href='notifbell.php?userid={$row['userid']}' class='action-button'><i class='fas fa-bell'></i></a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No confirmed inquiries found.</td></tr>";
                }
                ?>
            </tbody>
        </table>



<?php include 'adfooter.php';?>
<!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
