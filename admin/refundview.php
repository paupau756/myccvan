<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booking</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
    <!-- Include jsPDF library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
</head>
<body>


    <?php include 'head.php';?>

    <!-- Header with logout button -->
    <div class="header">
        <h1>Booking Details</h1>
    </div>

    <?php
include "connection.php";

// Check if inquiry ID is provided in the URL
if (!isset($_GET['inquiryid'])) {
    // Redirect or handle missing ID
    header("Location: manageinquiries.php");
    exit();
}

// Fetch inquiry details based on inquiry ID
$inquiryid = $_GET['inquiryid'];
$query = "SELECT * FROM inquiries WHERE inquiryid = $inquiryid";
$result = $conn->query($query);

// If inquiry found, display booking details
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userid = $row['userid'];

    // Fetch user details based on userid
    $userQuery = "SELECT * FROM users WHERE userid = $userid";
    $userResult = $conn->query($userQuery);

    // If user found, display user details
    if ($userResult->num_rows > 0) {
        $userRow = $userResult->fetch_assoc();
        ?>

        <div class="receipt-container">
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th colspan="2">User Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>User ID:</td>
                        <td><?php echo $userRow['userid']; ?></td>
                    </tr>
                    <tr>
                        <td>Name:</td>
                        <td><?php echo $userRow['name']; ?></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><?php echo $userRow['email']; ?></td>
                    </tr>
                    <tr>
                        <td>Contact:</td>
                        <td><?php echo $userRow['contact']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="receipt-container">
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th colspan="2">Inquiry Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Inquiry ID:</td>
                        <td><?php echo $row['inquiryid']; ?></td>
                    </tr>
                    <tr>
                        <td>Pickup:</td>
                        <td><?php echo $row['pickup']; ?></td>
                    </tr>
                    <tr>
                        <td>Date Start:</td>
                        <td><?php echo date('M d, Y', strtotime($row['datestart'])); ?> | <?php echo date('h:i A', strtotime($row['timestart'])); ?></td>
                    </tr>
                    <tr>
                        <td>Date End:</td>
                        <td><?php echo date('M d, Y', strtotime($row['dateend'])); ?> | <?php echo date('h:i A', strtotime($row['timeend'])); ?></td>
                    </tr>
                    <tr>
                        <td>Notes:</td>
                        <td><?php echo $row['note']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="receipt-container">
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th colspan="2">Driver Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $driverQuery = "SELECT * FROM drivers WHERE driverid = {$row['driverid']}";
                    $driverResult = $conn->query($driverQuery);
                    if ($driverResult->num_rows > 0) {
                        $driverRow = $driverResult->fetch_assoc();
                        ?>
                        <tr>
                            <td>Driver ID:</td>
                            <td><?php echo $driverRow['driverid']; ?></td>
                        </tr>
                        <tr>
                            <td>Name:</td>
                            <td><?php echo $driverRow['name']; ?></td>
                        </tr>
                        <tr>
                            <td>Contact:</td>
                            <td><?php echo $driverRow['contact']; ?></td>
                        </tr>
                        <tr>
                            <td>Driver License:</td>
                            <td><?php echo $driverRow['driverlicense']; ?></td>
                        </tr>
                        <?php
                    } else {
                        echo "<tr><td colspan='2'>No driver assigned.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>  


        <div class="receipt-container">
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th colspan="2">Vehicle Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $vehicleQuery = "SELECT * FROM vehicles WHERE vehicleid = {$row['vehicleid']}";
                    $vehicleResult = $conn->query($vehicleQuery);
                    if ($vehicleResult->num_rows > 0) {
                        $vehicleRow = $vehicleResult->fetch_assoc();
                        ?>
                        <tr>
                            <td>Number Of Vans:</td>
                            <td><?php echo $vehicleRow['vehicletype']; ?></td>
                        </tr>
                        <tr>
                            <td>Vehicle Brand/Names:</td>
                            <td><?php echo $vehicleRow['vehiclename']; ?></td>
                        </tr>
                        <tr>
                            <td>Seating Capacity:</td>
                            <td><?php echo $vehicleRow['max_seats']; ?></td>
                        </tr>
                        <?php
                    } else {
                        echo "<tr><td colspan='2'>No vehicle details found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="receipt-container">
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th colspan="2">Payment Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to fetch payment details based on inquiryid
                    $paymentQuery = "SELECT totalamount, downpayment FROM inquiries WHERE inquiryid = {$row['inquiryid']}";
                    $paymentResult = $conn->query($paymentQuery);

                    if ($paymentResult->num_rows > 0) {
                        $paymentRow = $paymentResult->fetch_assoc();
                        ?>
                        <tr>
                            <td>Total Amount:</td>
                            <td>₱<?php echo $paymentRow['totalamount']; ?></td>
                        </tr>
                        <tr>
                            <td>Downpayment:</td>
                            <td>₱<?php echo $paymentRow['downpayment']; ?></td>
                        </tr>
                        <?php
                        // Calculate and display Pending Amount
                        $pendingAmount = $paymentRow['totalamount'] - $paymentRow['downpayment'];
                        echo "<tr><td>Pending Amount:</td><td>₱$pendingAmount</td></tr>";
                    } else {
                        echo "<tr><td colspan='2'>Payment details not found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php
    } else {
        // Handle if user not found
        echo "<p>User details not found.</p>";
    }
} else {
    // Handle if inquiry not found
    echo "<p>Booking not found.</p>";
}
?>
<?php include 'adfooter.php'; ?>


</body>
</html>
