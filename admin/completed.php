<?php
// Include the database connection
include "connection.php";

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer autoload file
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

// Define the getUserEmail function
function getUserEmail($userid, $conn) {
    $email = ""; // Initialize email variable

    // Query to fetch user's email based on user ID
    $query = "SELECT email FROM users WHERE userid = '$userid'";
    $result = $conn->query($query);

    // Check if the query was successful and if it returned any rows
    if ($result && $result->num_rows > 0) {
        // Fetch the email from the result
        $row = $result->fetch_assoc();
        $email = $row['email'];
    }

    return $email;
}

// Get the current date and time
$currentDateTime = date('Y-m-d H:i:s');

// Update Confirmed status to Completed where dateend and timeend are in the past
$query = "UPDATE inquiries SET status = 'Completed' WHERE status = 'Confirmed' AND CONCAT(dateend, ' ', timeend) <= '$currentDateTime'";
$result = $conn->query($query);

if ($result) {
    echo "Confirmed bookings auto-completed successfully.";

    // Fetch completed bookings
    $completedBookingsQuery = "SELECT userid FROM inquiries WHERE status = 'Completed'";
    $completedBookingsResult = $conn->query($completedBookingsQuery);

    if ($completedBookingsResult) {
        // Send completion notification emails to users
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'djcaaaaastillo@gmail.com'; // Your Gmail email address
            $mail->Password   = 'xpxi giba lyva fxwa'; // Your Gmail password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Loop through completed bookings and send emails
            while ($row = $completedBookingsResult->fetch_assoc()) {
                $userid = $row['userid'];
                $email = getUserEmail($userid, $conn); // Function to get user's email from database

                // Content
                $mail->addAddress($email); // User's email
                $mail->isHTML(true);
                $mail->Subject = 'Booking Completion Notification';
                $mail->Body = '<h1>MYCC Van Rental | Marilao</h1><br><br>Your booking has been completed.';

                // Send email
                $mail->send();

                // Clear addresses for next iteration
                $mail->clearAddresses();
            }

            echo 'Completion notification emails sent successfully.';
        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error fetching completed bookings: " . $conn->error;
    }

    // JavaScript code to reload the window after 3 seconds
    echo "<script>setTimeout(function(){window.location.reload();}, 3000);</script>";
} else {
    echo "Error auto-completing confirmed bookings: " . $conn->error;
}
?>
