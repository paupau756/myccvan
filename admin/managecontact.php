<?php

include("connection.php");

// Include PHPMailer Autoload
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Handle form submission for answering messages
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $messageId = $_POST["messageid"];
    $answer = $_POST["answer"];

    // Update the message in the database with the answer
    $updateQuery = "UPDATE contact_submissions SET answer='$answer' WHERE messageid=$messageId";
    $conn->query($updateQuery);

    // Get the email address of the person who submitted the message
    $getEmailQuery = "SELECT email FROM contact_submissions WHERE messageid=$messageId";
    $emailResult = $conn->query($getEmailQuery);
    $row = $emailResult->fetch_assoc();
    $recipientEmail = $row['email'];

    // Send email notification to the person who submitted the message
    $subject = "Your Contact Message has been Answered";
    $message = "Dear User,\n\nYour contact message has been answered. Here is the response:\n\n$answer\n\nThank you.";
    sendEmailNotification($recipientEmail, $subject, $message);

    // JavaScript alert
    echo "<script>alert('Message answered successfully!'); window.location.href = window.location.href;</script>";
}

// Define default search value
$search = "";

// Check if search query is provided
if (isset($_GET['search'])) {
    // Sanitize the search query to prevent SQL injection
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Fetch contact messages based on search query
$query = "SELECT * FROM contact_submissions WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR message LIKE '%$search%' OR answer LIKE '%$search%' ORDER BY submission_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contact Messages</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

 <?php include 'head.php';?>

<div class="headersearch">
        <!-- Header -->
        <div class="headers">    
                <h2>Manage Contacts</h2>
        </div>
        <div>
            <!-- Search form -->
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search for Contacts..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                </form>
        </div>
    </div>
    
    
    <!-- Display all contact messages -->
    <table border="1">
        <thead>
            <tr>
                <th>Message ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Answer</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['messageid']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['message']; ?></td>
                    <td><?php echo $row['answer']; ?></td>
                    <td>
                        <?php if (empty($row['answer'])) : ?>
                            <form method='post' action=''>
                                <input type='hidden' name='messageid' value='<?php echo $row['messageid']; ?>' class="message-id">
                                <input type='text' name='answer' placeholder='Enter answer' class="answer-input">
                                <button type='submit' class="submit-button">Submit Answer</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

<?php
function sendEmailNotification($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'djcaaaaastillo@gmail.com'; // Replace with your Gmail username
        $mail->Password   = 'xpxi giba lyva fxwa'; // Replace with your Gmail password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('djcaaaaastillo@gmail.com', 'MYCC Van Rental | Marilao');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        // Handle error, if any
    }
}
?>

<?php include'adfooter.php';?>

</body>
</html>
