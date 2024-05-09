<?php
// Include the database connection file
include "connection.php";

// Include PHPMailer autoload
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Function to insert notification
function insertNotification($userid, $inquiryid, $message, $status) {
    global $conn;
    $insertNotificationQuery = "INSERT INTO notify (userid, inquiryid, message, status, created_at) VALUES ('$userid', '$inquiryid', '$message', '$status', NOW())";
    $insertNotificationResult = $conn->query($insertNotificationQuery);
    return $insertNotificationResult;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inquiryid']) && isset($_POST['newDriver'])) {
    // Retrieve inquiry ID and new driver ID from the form data
    $inquiryId = $_POST['inquiryid'];
    $newDriverId = $_POST['newDriver'];

    // Fetch destination and current driver name from the database
    $destinationQuery = "SELECT destination FROM inquiries WHERE inquiryid = $inquiryId";
    $destinationResult = $conn->query($destinationQuery);
    $destinationRow = $destinationResult->fetch_assoc();
    $destination = $destinationRow['destination'];

    $currentDriverQuery = "SELECT drivers.name FROM drivers INNER JOIN inquiries ON drivers.driverid = inquiries.driverid WHERE inquiries.inquiryid = $inquiryId";
    $currentDriverResult = $conn->query($currentDriverQuery);
    $currentDriverRow = $currentDriverResult->fetch_assoc();
    $currentDriverName = $currentDriverRow['name'];

    // Fetch the name of the new driver
    $newDriverQuery = "SELECT name FROM drivers WHERE driverid = $newDriverId";
    $newDriverResult = $conn->query($newDriverQuery);
    $newDriverRow = $newDriverResult->fetch_assoc();
    $newDriverName = $newDriverRow['name'];

    // Update the driver ID for the specified inquiry in the database
    $updateQuery = "UPDATE inquiries SET driverid = $newDriverId WHERE inquiryid = $inquiryId";
    $updateResult = $conn->query($updateQuery);

    if ($updateResult) {
        // Driver changed successfully

        // Fetch the user's ID and email address associated with the inquiry ID
        $userQuery = "SELECT users.userid, users.email FROM users INNER JOIN inquiries ON users.userid = inquiries.userid WHERE inquiries.inquiryid = $inquiryId";
        $userResult = $conn->query($userQuery);
        $userRow = $userResult->fetch_assoc();
        $userId = $userRow['userid'];
        $userIdEmail = $userRow['email'];

        // Insert activity log
        $activity = "Changed driver for inquiry ID: $inquiryId. Destination: $destination. Previous Driver: $currentDriverName. New Driver: $newDriverName";
        $insertActivityQuery = "INSERT INTO activitylogs (activities, created_at) VALUES ('$activity', NOW())";
        $insertActivityResult = $conn->query($insertActivityQuery);

        // Insert notification
        $notificationMessage = "The assigned driver for your booking has been replaced by $newDriverName";
        $insertNotificationResult = insertNotification($userId, $inquiryId, $notificationMessage, 'unread');

        if ($insertActivityResult && $insertNotificationResult) {
            // Send email to user
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'djcaaaaastillo@gmail.com'; // Your Gmail email address
                $mail->Password = 'xpxi giba lyva fxwa'; // Your Gmail password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                //Recipients
                $mail->setFrom('djcaaaaastillo@gmail.com', 'MYCC Van Rental | Marilao'); // Your name and email address
                $mail->addAddress($userIdEmail); // User's email address

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Driver Change Notification';
                $mail->Body    = $notificationMessage;

                $mail->send();
                echo "<script>alert('Driver changed successfully. Email notification sent.'); window.location.href = 'viewbooking.php?inquiryid=$inquiryId';</script>";
            } catch (Exception $e) {
                echo "<script>alert('Error occurred while sending email notification.');</script>";
            }
        } else {
            echo "<script>alert('Error occurred while logging activity or adding notification.'); window.location.href = 'viewbooking.php?inquiryid=$inquiryId';</script>";
        }
    } else {
        // Error occurred while changing driver
        echo "<script>alert('Error occurred while changing driver: " . $conn->error . "'); window.location.href = 'viewbooking.php?inquiryid=$inquiryId';</script>";
    }
} else {
    // Redirect or handle invalid form submission
    echo "<script>alert('Invalid form submission.'); window.location.href = 'viewbooking.php?inquiryid=$inquiryId';</script>";
}
?>
