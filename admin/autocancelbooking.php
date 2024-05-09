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

// Fetch pending inquiries where datestart is in the past
$currentDate = date('Y-m-d H:i:s');
$query = "UPDATE inquiries SET status = 'Cancelled' WHERE status = 'Pending' AND datestart <= '$currentDate'";
if ($conn->query($query) === TRUE) {
    echo "Pending inquiries successfully cancelled.";

    // Send cancellation notification to users via email
    sendCancellationNotification($conn);
} else {
    echo "Error cancelling pending inquiries: " . $conn->error;
}

// Function to send cancellation notification to users via email
function sendCancellationNotification($conn) {
    // Fetch cancelled inquiries
    $cancelledInquiriesQuery = "SELECT * FROM inquiries WHERE status = 'Cancelled'";
    $cancelledInquiriesResult = $conn->query($cancelledInquiriesQuery);

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

        // Loop through cancelled inquiries and send emails
        while ($row = $cancelledInquiriesResult->fetch_assoc()) {
            $userid = $row['userid'];
            $email = getUserEmail($userid, $conn); // Function to get user's email from database

            // Content
            $mail->addAddress($email); // User's email
            $mail->isHTML(true);
            $mail->Subject = 'Apology for Booking Cancellation';
            $mail->Body = 'We apologize, but your booking has been cancelled.';

            // Send email
            $mail->send();

            // Clear addresses for next iteration
            $mail->clearAddresses();
        }

        echo 'Cancellation notifications sent successfully.';
    } catch (Exception $e) {
        echo "Error sending cancellation notifications: {$mail->ErrorInfo}";
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
?>
