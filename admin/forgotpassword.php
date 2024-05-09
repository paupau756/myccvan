<?php
// Start the session
session_start();

// Include your database connection code here if not already included
include("connection.php");


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Include PHPMailer Autoload
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the database (you should have a users table)
    $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmailQuery);

    if ($result->num_rows > 0) {
        // Generate a verification code (you can customize this based on your requirements)
        $verificationCode = mt_rand(100000, 999999);

        // Store the verification code in the session for later verification
        $_SESSION['verification_code'] = $verificationCode;
        $_SESSION['reset_email'] = $email;

        // Send verification code to the user's email
        sendVerificationCode($email, $verificationCode);

        // Redirect to resetpassword.php
        header("Location: resetpassword.php");
        exit();
    } else {
        // Email not found in the database
        echo "Email not found. Please enter a valid email address.";
    }
}

// Function to send verification code through PHPMailer
function sendVerificationCode($email, $verificationCode) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'djcaaaaastillo@gmail.com'; // Replace with your Gmail username
        $mail->Password   = 'xpxi giba lyva fxwa'; // Replace with your Gmail password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('djcaaaaastillo@gmail.com', 'MYCC Van Rental | Marilao');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Verification Code';
        $mail->Body    = "Your verification code is: $verificationCode";

        $mail->send();
    } catch (Exception $e) {
        // Handle error, if any
        echo "Verification code email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style >
        /* Reset some default styles */
    body, h2, form {
        margin: 0;
        padding: 0;
    }

    .forgot-password-container {
        max-width: 400px;
        margin: 200px auto;
        padding: 20px;
        background-color: transparent;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.8);
    }

    h2 {
        text-align: center;
        color: #333;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    label {
        margin-bottom: 10px;
        color: #333;
    }

    input {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    button {
        background-color: #333;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #555;
    }
    </style>
    <!-- Add your CSS styling here -->
</head>
<body>

<div class="forgot-password-container">
    <h2>Forgot Password</h2>
    <form action="" method="post">
        <label for="email">Enter your email:</label>
        <input type="email" name="email" required>
        <button type="submit">Send Verification Code</button>
    </form>
</div>

</body>
</html>
