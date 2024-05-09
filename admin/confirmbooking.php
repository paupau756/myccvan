<?php
// Start the session
session_start();

// Include the database connection
include "connection.php";

// Include PHPMailer autoload
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check if the admin is logged in
if (!isset($_SESSION['adminid'])) {
    // Redirect to the login page or handle as needed
    header("Location: adminlogin.php");
    exit();
}

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

// If inquiry found, proceed with confirmation
if ($result->num_rows > 0) {
    // Fetch inquiry details
    $inquiryDetails = $result->fetch_assoc();
    $userid = $inquiryDetails['userid'];
    $email = getUserEmail($userid, $conn); // Function to get user's email from database

    // Fetch driver details
    $driverid = $inquiryDetails['driverid'];
    $driverQuery = "SELECT * FROM drivers WHERE driverid = $driverid";
    $driverResult = $conn->query($driverQuery);
    $driverDetails = $driverResult->fetch_assoc();

    // Update status to "Confirmed" and select an available driver
    $startDate = $inquiryDetails['datestart'];
    $endDate = $inquiryDetails['dateend'];
    $availableDriversQuery = "SELECT * FROM drivers WHERE driverid NOT IN (
        SELECT driverid FROM inquiries 
        WHERE status = 'Confirmed' 
        AND ((datestart BETWEEN '$startDate' AND '$endDate') OR (dateend BETWEEN '$startDate' AND '$endDate'))
    )";
    $availableDriversResult = $conn->query($availableDriversQuery);

    if ($availableDriversResult->num_rows > 0) {
        // Fetch the first available driver (you can implement a more sophisticated selection logic here)
        $availableDriver = $availableDriversResult->fetch_assoc();
        $selectedDriverId = $availableDriver['driverid'];
        
        // Update the booking with the selected driver's ID and set status to "Confirmed"
        $updateBookingQuery = "UPDATE inquiries SET driverid = $selectedDriverId, status = 'Confirmed' WHERE inquiryid = $inquiryid";
        if ($conn->query($updateBookingQuery) === TRUE) {
            // Fetch user details
            $userQuery = "SELECT * FROM users WHERE userid = $userid";
            $userResult = $conn->query($userQuery);
            $userDetails = $userResult->fetch_assoc();

            // Fetch vehicle details
            $vehicleQuery = "SELECT * FROM vehicles WHERE vehicleid = {$inquiryDetails['vehicleid']}";
            $vehicleResult = $conn->query($vehicleQuery);
            $vehicleDetails = $vehicleResult->fetch_assoc();

            // Prepare confirmation email message
            $subject = "Booking Confirmation";
            $message = "
                <html>
                <head>
                    <style>
                        /* CSS Styles */
                        .header {
                            text-align: center;
                            background-color: #f0f0f0;
                            padding: 20px;
                        }
                        .header h1 {
                            margin: 0;
                            font-size: 24px;
                        }
                        .logo {
                            max-width: 150px; /* Adjust size as needed */
                            margin-bottom: 10px;
                        }
                        /* Other CSS styles as needed */
                    </style>
                </head>
                <body>
                    <div class='header'>
                        <h1>MYCC Van Rental | Marilao</h1>
                    </div>
                    <div class='content'>
                        <p><strong>User Details:</strong></p>
                        <p>User ID: {$userDetails['userid']}</p>
                        <p>Name: {$userDetails['name']}</p>
                        <p>Contact: {$userDetails['contact']}</p>
                        <p>Email: {$userDetails['email']}</p>
                        <br>
                        <p><strong>Inquiry Details:</strong></p>
                        <p>Inquiry ID: {$inquiryDetails['inquiryid']}</p>
                        <p>Pickup: {$inquiryDetails['pickup']}</p>
                        <p>Destination: {$inquiryDetails['destination']}</p>
                        <p>Date Start: " . date('M d, Y', strtotime($inquiryDetails['datestart'])) . "</p>
                        <p>Time Start: " . date('h:i A', strtotime($inquiryDetails['timestart'])) . "</p>
                        <p>Date End: " . date('M d, Y', strtotime($inquiryDetails['dateend'])) . "</p>
                        <p>Time End: " . date('h:i A', strtotime($inquiryDetails['timeend'])) . "</p>
                        <p>Total Amount: {$inquiryDetails['totalamount']}</p>
                        <p>Downpayment: {$inquiryDetails['downpayment']}</p>
                        <br>
                        <p><strong>Driver Details:</strong></p>
                        <p>Driver ID: {$availableDriver['driverid']}</p>
                        <p>Name: {$availableDriver['name']}</p>
                        <p>Contact: {$availableDriver['contact']}</p>
                        <br>
                        <p><strong>Vehicle Details:</strong></p>
                        <p>Vehicle ID: {$vehicleDetails['vehicleid']}</p>
                        <p>Vehicle Type: {$vehicleDetails['vehicletype']}</p>
                        <p>Vehicle Name: {$vehicleDetails['vehiclename']}</p>
                        <p>Maximum Seats: {$vehicleDetails['max_seats']}</p>
                        <br>
                        <p>Your booking has been confirmed. See you soon!</p>
                    </div>
                </body>
                </html>
            ";

            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'djcaaaaastillo@gmail.com'; // Your Gmail address
                $mail->Password = 'xpxi giba lyva fxwa'; // Your Gmail password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipient
                $mail->setFrom('djcaaaaastillo@gmail.com', 'MYCC Van Rental | Marilao');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;

                // Send email
                $mail->send();

                // Insert notification into the notify table
                $notificationMessage = "Hello {$userDetails['name']}, your booking (ID: {$inquiryDetails['inquiryid']}) has been confirmed.";
                insertNotification($userid, $inquiryid, $notificationMessage, 'unread', date('Y-m-d H:i:s'));

                echo "<script>alert('Booking confirmed successfully. Confirmation email sent.');</script>";
                echo "<script>window.location.href = 'manageinquiries.php';</script>";
                exit();
            } catch (Exception $e) {
                echo "<script>alert('Error sending confirmation email: {$mail->ErrorInfo}');</script>";
            }
        } else {
            // Handle error updating the booking
            echo "<script>alert('Error confirming booking: " . $conn->error . "');</script>";
        }
    } else {
        // Handle no available drivers for the specified date range
        echo "<script>alert('No available drivers for the specified date range.');</script>";
    }
} else {
    // Handle missing inquiry
    echo "<script>alert('Booking not found.');</script>";
}

// Redirect back to manage inquiries page
echo "<script>window.location.href = 'manageinquiries.php';</script>";

// Function to get user's email from the database
function getUserEmail($userid, $conn) {
    $query = "SELECT email FROM users WHERE userid = $userid";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['email'];
    } else {
        return null;
    }
}

// Function to insert notification into the notify table
function insertNotification($userid, $inquiryid, $message, $status, $created_at) {
    global $conn;
    $insertQuery = "INSERT INTO notify (userid, inquiryid, message, status, created_at)
    VALUES ('$userid', '$inquiryid', '$message', '$status', '$created_at')";
    if ($conn->query($insertQuery) === TRUE) {
        // Notification inserted successfully
        return true;
    } else {
        // Failed to insert notification
        echo "Error: " . $insertQuery . "<br>" . $conn->error;
        return false;
    }
}
?>
