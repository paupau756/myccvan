<?php
// Include the connection file
include 'admin/connection.php';

// Start session
session_start();

// Initialize unread count
$unread_count = 0;

// Check if the user is logged in
if(isset($_SESSION['userid'])) {
    $loggedin = true;

    // Get the logged-in user's ID
    $userid = $_SESSION['userid'];

    // Query to fetch the count of unread notifications for the logged-in user
    $query = "SELECT COUNT(*) AS unread_count FROM notify WHERE userid = ? AND status = 'unread'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the unread notification count
    if ($row = $result->fetch_assoc()) {
        $unread_count = $row['unread_count'];
    }

    // Close the statement
    $stmt->close();
} else {
    // User is not logged in
    $loggedin = false;
}

// Close the database connection
$conn->close();
?>


<?php
// Start session
// session_start();

// Include connection
include "admin/connection.php";

// Check if inquiry ID is provided in the URL
if (!isset($_GET['id'])) {
    // Redirect or handle missing ID
    header("Location: bookingcart.php");
    exit();
}

// Fetch booking details based on inquiry ID
$inquiryid = $_GET['id'];
$query = "SELECT * FROM inquiries WHERE inquiryid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $inquiryid);
$stmt->execute();
$result = $stmt->get_result();

// Fetch user details
$booking = $user = [];
if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
    $userid = $booking['userid'];
    $queryUser = "SELECT * FROM users WHERE userid = ?";
    $stmtUser = $conn->prepare($queryUser);
    $stmtUser->bind_param("i", $userid);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    if ($resultUser->num_rows > 0) {
        $user = $resultUser->fetch_assoc();
    }
    $stmtUser->close();
}

// Fetch vehicle details
$vehicle = [];
$vehicleid = $booking['vehicleid'];
$queryVehicle = "SELECT * FROM vehicles WHERE vehicleid = ?";
$stmtVehicle = $conn->prepare($queryVehicle);
$stmtVehicle->bind_param("i", $vehicleid);
$stmtVehicle->execute();
$resultVehicle = $stmtVehicle->get_result();
if ($resultVehicle->num_rows > 0) {
    $vehicle = $resultVehicle->fetch_assoc();
}
$stmtVehicle->close();

// Initialize driver variables
$driver = [];
$driverid = '';

// Check if booking status is confirmed and fetch driver details
if ($booking['status'] === 'Confirmed') {
    $driverid = $booking['driverid'];
    $queryDriver = "SELECT * FROM drivers WHERE driverid = ?";
    $stmtDriver = $conn->prepare($queryDriver);
    $stmtDriver->bind_param("i", $driverid);
    $stmtDriver->execute();
    $resultDriver = $stmtDriver->get_result();
    if ($resultDriver->num_rows > 0) {
        $driver = $resultDriver->fetch_assoc();
    }
    $stmtDriver->close();
}

// Check if the user is logged in
$loggedin = false;
$username = "";
if(isset($_SESSION['userid'])) {
    $loggedin = true;

    // Prepare and execute a query to fetch the user's name
    $userid = $_SESSION['userid'];
    $query = "SELECT name FROM users WHERE userid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the query was successful and if a row was returned
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['name'];
    } else {
        // Error handling if user not found
        $username = "Unknown";
    }

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
    <title>View Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <!-- Online CDN for Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="cdn2.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
</head>
<body>
    <!-- header ito -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="header.php">MYCC VAN RENTAL</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">HOME</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="tours.php">PACKAGES</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="form.php">INQUIRE</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notification.php">NOTIFICATIONS <?php echo ($unread_count > 0) ? "<span class='badge badge-danger'>$unread_count</span>" : ""; ?></a>
                </li>
            </ul>
            <?php if($loggedin) { ?>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <strong><?php echo $username; ?></strong>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="bookingcart.php">Booking Cart</a>
                        <a class="dropdown-item" href="bookinghistory.php">Booking History</a>
                        <a class="dropdown-item" href="settings.php">Settings</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
            <?php } else { ?>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">LOGIN</a>
                </li>
            </ul>
            <?php } ?>
        </div>
    </nav>
    <script>
        // JavaScript to show the dropdown menu when clicking on the user's name
        document.addEventListener("DOMContentLoaded", function() {
            var dropdownToggle = document.querySelector('.dropdown-toggle');

            dropdownToggle.addEventListener('click', function() {
                var dropdownMenu = this.nextElementSibling;
                dropdownMenu.classList.toggle('show');
            });

            // JavaScript to toggle the collapsed navbar when the toggle button is clicked
            var navbarToggler = document.querySelector('.navbar-toggler');
            var navbarCollapse = document.querySelector('.navbar-collapse');

            navbarToggler.addEventListener('click', function() {
                navbarCollapse.classList.toggle('show');
            });
        });
    </script>
    <!-- header end dito -->



    <div class="booking-section">
        <h1 class="booking-title">View Booking</h1>
    </div>


    <div class="booking-receipt-container">
        <div class="booking-receipt">
            <h2 class="section-title">User Details</h2>
            <div class="user-details">
                <p class="booking-info">User ID: <?php echo $userid; ?></p>
                <p class="booking-info">Name: <?php echo $user['name']; ?></p>
                <p class="booking-info">Contact: <?php echo $user['contact']; ?></p>
                <p class="booking-info">Email: <?php echo $user['email']; ?></p>
            </div>


            <h2 class="section-title">Booking Details</h2>
            <div class="booking-details">
                <p class="booking-info">Inquiry ID: <?php echo $booking['inquiryid']; ?></p>
                <p class="booking-info">Destination: <?php echo $booking['destination']; ?></p>
                <p class="booking-info">Pickup: <?php echo $booking['pickup']; ?></p>
                <p class="booking-info">Date Start: <?php echo date('M d, Y h:i A', strtotime($booking['datestart'] . ' ' . $booking['timestart'])); ?></p>
                <p class="booking-info">Date End: <?php echo date('M d, Y h:i A', strtotime($booking['dateend'] . ' ' . $booking['timeend'])); ?></p>
                <!-- <p class="booking-info">Vehicle ID: <?php echo $vehicleid; ?></p> -->
                <p class="booking-info">Vehicle Type: <?php echo $vehicle['vehicletype']; ?></p>
                <p class="booking-info">Vehicle Name: <?php echo $vehicle['vehiclename']; ?></p>
                <p class="booking-info">Max Seats: <?php echo $vehicle['max_seats']; ?></p>
                <!-- <p class="booking-info">Total Amount: <?php echo $booking['totalamount']; ?></p>
                <p class="booking-info">Downpayment: <?php echo $booking['downpayment']; ?></p> -->
                <p class="booking-info">Status: <?php echo $booking['status']; ?></p>
            </div>

            <?php if ($booking['status'] === 'Confirmed'): ?>
                <h2 class="section-title">Driver Details</h2>
                <div class="driver-details">
                    <p class="booking-info">Driver ID: <?php echo $driverid; ?></p>
                    <p class="booking-info">Name: <?php echo $driver['name']; ?></p>
                    <p class="booking-info">Contact: <?php echo $driver['contact']; ?></p>
                    <p class="booking-info">Driver License: <?php echo $driver['driverlicense']; ?></p>
                    <p class="booking-info">Information: <?php echo $driver['information']; ?></p> <!-- Add Information section -->
                </div>
            <?php endif; ?>

            <div class="button-container">
                <?php if ($booking['status'] === 'Pending' || $booking['status'] === 'Confirmed'): ?>
                    <button class="cancel-button" onclick="confirmCancellation(<?php echo $inquiryid; ?>)">Cancel Booking</button>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($booking['status'] === 'Confirmed'): ?>
        <!-- Updated code with added class -->
        <div class="payment-form">
            <h2>Payment Form</h2>
            <form action="paymongo/process.php" method="POST" onsubmit="return validateForm()">
                <h3>Accepting GCash Payment Only</h3>
                <input type="hidden" name="inquiryid" value="<?php echo $booking['inquiryid']; ?>">
                <!-- <label for="amount">Amount to Pay:</label><br> -->
                <input type="number" id="amount" name="amount" placeholder="Enter Your Downpayment" min="1000" max="<?php echo $booking['totalamount']; ?>">
                <br><br>
                <button type="submit" class="submit-button1">Submit</button>
                <span id="amount-error" class="error-message"></span> <!-- Error message span -->
            </form>
        </div>

        <script>
            function validateForm() {
            var amountInput = document.getElementById("amount");
            var amountError = document.getElementById("amount-error");

            // Check if amount is not empty
            if (amountInput.value.trim() === "") {
                amountError.textContent = "Please enter the amount to pay.";
                return false; // Prevent form submission
            }

            // Check if amount is within the valid range
            var minAmount = parseFloat(amountInput.min);
            var maxAmount = parseFloat(amountInput.max);
            var enteredAmount = parseFloat(amountInput.value);
            if (enteredAmount < minAmount || enteredAmount > maxAmount) {
                amountError.textContent = "Please enter an amount within the valid range.";
                return false; // Prevent form submission
            }

            // Clear error message if everything is valid
            amountError.textContent = "";
            return true; // Allow form submission
        }
        </script>



        <?php else: ?>
        <div class="payment-info">
            <h2 class="section-title">Payment Details</h2>
            <div class="payment-details">
                <br>
                <!-- Payment Details -->
                <!-- <p class="booking-info">Payment Method: <?php echo $booking['paymentmethod']; ?></p> -->
                <p class="booking-info">Total Amount: ₱<?php echo $booking['totalamount']; ?></p>
                <p class="booking-info">Downpayment: ₱<?php echo $booking['downpayment']; ?></p>
                <p class="booking-info">Current Total Amount: ₱<?php echo $booking['totalamount'] - $booking['downpayment']; ?></p>

            </div>

            <br><br><BR>

                <div class="refund_form" style="display: none;">
                    <!-- Refund Request Form -->
                    <h3>Request Refund</h3>
                    <form action="refund_process.php" method="post">
                        <input type="hidden" name="inquiryid" value="<?php echo $booking['inquiryid']; ?>">
                        <input type="hidden" name="downpayment" value="<?php echo $booking['downpayment']; ?>">
                        <input type="hidden" name="userid" value="<?php echo $booking['userid']; ?>">
                        <!-- <input type="hidden" name="name" value="<?php echo $booking['name']; ?>"> -->

                        <label for="refund_amount">Refund Amount:</label>
                        <input type="number" id="refund_amount" name="refund_amount" required value="<?php echo $booking['downpayment'] * 0.85; ?>" readonly>
                        <br>

                        <!-- Add paymentcode field -->
                        <label for="paymentcode">Payment Code:</label>
                        <input type="text" id="paymentcode" name="paymentcode" required>
                        <br>
                        <p>Note: Refunding the downpayment will be deducted by 15%.</p>
                        <!-- <span id="refund_message">Refund amount should be equal to or greater than 15% of the downpayment.</span> --> <br><br>

                        <label for="refund_reason">Reason for Refund:</label>
                        <textarea id="refund_reason" name="refund_reason" rows="4" required></textarea>
                        <br>
                        
                        <button type="submit">Submit Request</button>
                    </form>
                </div>

                <?php if ($booking['status'] === 'Paid'): ?>
                        <!-- Refund Request Button -->
                        <div class="button-container">
                            <button id="show_refund_form_button">Request Refund</button>
                        </div>
                <?php endif; ?>

            </div>

        <?php endif; ?>
    </div>


    <script>
        function confirmCancellation(inquiryId) {
            if (confirm("Are you sure you want to cancel this booking?")) {
                window.location.href = 'cancelbooking.php?id=' + inquiryId;
            }
        }
    </script>
     
    <!-- <script>
        // Get the downpayment value from PHP
        var downpayment = <?php echo $booking['downpayment']; ?>;
        
        // Calculate the minimum and maximum refund amount
        var minRefund = downpayment * 0.9; // 90% of downpayment
        var maxRefund = downpayment; // Same as downpayment
        
        // Set the min and max attributes of the refund amount input field
        document.getElementById("refund_amount").setAttribute("min", minRefund);
        document.getElementById("refund_amount").setAttribute("max", maxRefund);
    </script> -->

    <script>
        // Toggle visibility of refund form and button when button is clicked
        document.getElementById("show_refund_form_button").addEventListener("click", function() {
            var refundForm = document.querySelector(".refund_form");
            var refundButton = document.getElementById("show_refund_form_button");
                        
            // Toggle display of refund form
            refundForm.style.display = refundForm.style.display === "none" ? "block" : "none";
            
            // Toggle display of refund button
            refundButton.style.display = refundButton.style.display === "none" ? "block" : "none";
        });
    </script>

    <br><br><br><br><br>
<?php include 'footer.php'; ?>
</body>
</html>
