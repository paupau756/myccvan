<?php
// Include the database connection file
include 'connection.php';

// Function to insert notification
function insertNotification($userid, $inquiryid, $message, $status) {
    global $conn;
    $insertNotificationQuery = "INSERT INTO notify (userid, inquiryid, message, status, created_at) VALUES ('$userid', '$inquiryid', '$message', '$status', NOW())";
    $insertNotificationResult = $conn->query($insertNotificationQuery);
    return $insertNotificationResult;
}

// Check if inquiryid is provided in the URL
if (isset($_GET['inquiryid'])) {
    // Get the inquiryid from the URL parameter
    $inquiryid = $_GET['inquiryid'];

    // Fetch the userid associated with the inquiry
    $getUserQuery = "SELECT userid FROM inquiries WHERE inquiryid = $inquiryid";
    $getUserResult = $conn->query($getUserQuery);

    if ($getUserResult->num_rows > 0) {
        $userData = $getUserResult->fetch_assoc();
        $userid = $userData['userid'];

        // Update the status of the inquiry to 'Cancelled'
        $updateQuery = "UPDATE inquiries SET status = 'Cancelled' WHERE inquiryid = $inquiryid";
        $updateResult = $conn->query($updateQuery);

        // Check if the update was successful
        if ($updateResult) {
            // Insert activity log
            $activity = "Inquiry with ID $inquiryid was confirmed and status updated to Cancelled.";
            $insertActivityQuery = "INSERT INTO activitylogs (activities, created_at) VALUES ('$activity', NOW())";
            $insertActivityResult = $conn->query($insertActivityQuery);

            // Insert notification
            $notificationMessage = "Your booking with Inquiry ID $inquiryid has been cancelled.";
            $insertNotificationResult = insertNotification($userid, $inquiryid, $notificationMessage, 'unread');

            // Check if the notification insertion was successful
            if ($insertNotificationResult) {
                // Display an alert confirming the update
                echo "<script>alert('Inquiry status updated to Cancelled.'); window.location.href = 'managerefund.php';</script>";
                exit; // Stop further execution
            } else {
                // Display an error alert if the notification insertion failed
                echo "<script>alert('Error inserting notification.'); window.location.href = 'managerefund.php';</script>";
                exit; // Stop further execution
            }
        } else {
            // Display an error alert if the update failed
            echo "<script>alert('Error updating inquiry status.'); window.location.href = 'managerefund.php';</script>";
            exit; // Stop further execution
        }
    } else {
        // Display an error alert if no user is found for the inquiry
        echo "<script>alert('No user found for the inquiry.'); window.location.href = 'managerefund.php';</script>";
        exit; // Stop further execution
    }
} else {
    // Redirect the user if inquiryid is not provided in the URL
    header("Location: managerefund.php");
    exit; // Stop further execution
}
?>
