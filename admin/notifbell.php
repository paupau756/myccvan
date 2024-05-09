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

// Check if userid is provided in the URL
if(isset($_GET['userid'])) {
    // Retrieve userid from the URL
    $userid = $_GET['userid'];

    // Define inquiryid, message, status, and created_at
    $inquiryid = ""; // You need to define this value according to your application logic
    $message = "Hello! Just a reminder. Dont forget to prepare and get excited for your UPCOMING RESERVATION!"; // Notification message indicating the booking day is approaching
    $status = "unread"; // Set initial status as unread
    $created_at = date('Y-m-d H:i:s'); // Current date and time

    // Insert data into the notify table
    $insertQuery = "INSERT INTO notify (userid, inquiryid, message, status, created_at) VALUES ('$userid', '$inquiryid', '$message', '$status', '$created_at')";
    
    if ($conn->query($insertQuery) === TRUE) {
        // Retrieve email address associated with userid from the database
        $emailQuery = "SELECT email FROM users WHERE userid = '$userid'";
        $emailResult = $conn->query($emailQuery);
        
        if ($emailResult->num_rows > 0) {
            $row = $emailResult->fetch_assoc();
            $recipientEmail = $row['email'];
            
            // Email notification
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'djcaaaaastillo@gmail.com'; // Your Gmail username
                $mail->Password = 'xpxi giba lyva fxwa'; // Your Gmail password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                //Recipients
                $mail->setFrom('djcaaaaastillo@gmail.com', 'MYCC Van Rental | Marilao');
                $mail->addAddress($recipientEmail); // Email address of the recipient

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Booking Reminder';
                $mail->Body    = $message;

                $mail->send();
                
                // Display alert and redirect
                echo "<script>alert('Notification added successfully and email sent.'); window.location.href = 'index.php';</script>";
                exit;
            } catch (Exception $e) {
                echo "<script>alert('Notification added successfully, but email could not be sent. Mailer Error: {$mail->ErrorInfo}'); window.location.href = 'index.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('User ID not found or email not associated with the provided User ID.'); window.location.href = 'index.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Error: {$conn->error}'); window.location.href = 'index.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('User ID not provided.'); window.location.href = 'index.php';</script>";
    exit;
}
?>
