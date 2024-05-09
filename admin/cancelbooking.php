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

// If inquiry found, proceed with cancellation
if ($result->num_rows > 0) {
    // Fetch inquiry details
    $row = $result->fetch_assoc();
    $userid = $row['userid'];
    $email = getUserEmail($userid, $conn); // Function to get user's email from database
    $name = getUserName($userid, $conn); // Function to get user's name from database
    $destination = $row['destination'];
    $datestart = date('F j, Y', strtotime($row['datestart'])); // Format datestart
    $dateend = date('F j, Y', strtotime($row['dateend'])); // Format dateend

    // Update status to "Cancelled"
    $updateQuery = "UPDATE inquiries SET status = 'Cancelled' WHERE inquiryid = $inquiryid";
    if ($conn->query($updateQuery) === TRUE) {
        // Send cancellation email
        $subject = "Booking Cancellation";
        $message = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Booking Cancellation</title>
            <style>
                /* CSS styles */
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }
                .header {
                    background-color: #f0f0f0;
                    padding: 20px;
                    text-align: center;
                }
                .logo {
                    max-width: 200px;
                    height: auto;
                }
                .message {
                    padding: 20px;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <!-- Header -->
            <div class='header'>
                <h1>MYCC Van Rental | Marilao</h1>
            </div>

            <!-- Message -->
            <div class='message'>
                <p>We apologize, $name, that your trip to $destination from $datestart to $dateend has been cancelled.</p>
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
            $mail->setFrom('djcaaaaastillo@gmail.com', 'MYYCC Van Rentals');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            // Send email
            $mail->send();
            echo "<script>alert('Booking cancelled successfully. Cancellation email sent.');</script>";
            
            // Insert notification into the notify table
            $notificationMessage = "We apologize that your booking has been cancelled.";
            $status = "unread"; // Assuming the notification is initially unread
            $created_at = date('Y-m-d H:i:s'); // Current date and time
            insertNotification($userid, $inquiryid, $notificationMessage, $status, $created_at);
        } catch (Exception $e) {
            echo "<script>alert('Error sending cancellation email: {$mail->ErrorInfo}');</script>";
        }
    } else {
        // Handle error when updating status
        echo "<script>alert('Error canceling booking: " . $conn->error . "');</script>";
    }
} else {
    // Handle if inquiry not found
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

// Function to get user's name from the database
function getUserName($userid, $conn) {
    $query = "SELECT name FROM users WHERE userid = $userid";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
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
