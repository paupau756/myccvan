<?php
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the database connection
include "connection.php";

// Include PHPMailer autoload file
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

// Get the current date and time
$currentDateTime = date('Y-m-d H:i:s');

// Fetch confirmed bookings where dateend and timeend are in the past
$query = "UPDATE inquiries SET status = 'Completed' WHERE status = 'Confirmed' AND CONCAT(dateend, ' ', timeend) <= '$currentDateTime'";
$result = $conn->query($query);

if ($result) {
    echo "Confirmed bookings auto-completed successfully.";
    echo "<script>window.history.back();</script>";

    // Send completion notification to users via email
    sendCompletionNotification($conn);

    // Insert notification into notify table
    insertNotification($conn);
} else {
    echo "Error auto-completing confirmed bookings: " . $conn->error;
}

// Function to send completion notification to users via email
function sendCompletionNotification($conn) {
    // Fetch completed bookings
    $completedBookingsQuery = "SELECT * FROM inquiries WHERE status = 'Completed'";
    $completedBookingsResult = $conn->query($completedBookingsQuery);

    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'djcaaaaastillo@gmail.com'; // Your Gmail address
        $mail->Password = 'xpxi giba lyva fxwa'; // Your Gmail password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('djcaaaaastillo@gmail.com', 'MYCC Van Rentals'); // Your name and Gmail address
        $mail->addReplyTo('djcaaaaastillo@gmail.com', 'MYCC Van Rentals'); // Your name and Gmail address

        // Loop through completed bookings and send emails
        while ($row = $completedBookingsResult->fetch_assoc()) {
            $userid = $row['userid'];
            $email = getUserEmail($userid, $conn); // Function to get user's email from database

            // Content
            $mail->addAddress($email); // User's email
            $mail->isHTML(true);
            $mail->Subject = 'Booking Completion Notification';
            $mail->Body = 'Your booking has been completed.';

            // Send email
            $mail->send();

            // Clear addresses for next iteration
            $mail->clearAddresses();
        }

        echo 'Completion notifications sent successfully.';
    } catch (Exception $e) {
        echo "Error sending completion notifications: {$mail->ErrorInfo}";
    }
}

// Function to fetch user's email
function getUserEmail($userid, $conn) {
    $query = "SELECT email FROM users WHERE userid = $userid";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['email'];
    }
    return null;
}

// Function to insert notification into notify table
function insertNotification($conn) {
    $query = "SELECT userid, inquiryid FROM inquiries WHERE status = 'Completed'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $userid = $row['userid'];
            $inquiryid = $row['inquiryid'];
            $message = "Booking completed successfully Inquiry ID: $inquiryid.";
            $status = "Unread";
            $created_at = date("Y-m-d H:i:s");
            $insertQuery = "INSERT INTO notify (userid, inquiryid, message, status, created_at) VALUES ('$userid', '$inquiryid', '$message', '$status', '$created_at')";
            $insertResult = $conn->query($insertQuery);
            if (!$insertResult) {
                echo "Error inserting notification: " . $conn->error;
            }
        }
        echo "Notifications inserted successfully.";
    } else {
        echo "No completed bookings found.";
    }
}

?>
