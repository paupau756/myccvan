<?php
include("admin/connection.php");

// Include PHPMailer Autoload
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Check if the email exists in the database
    $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        // Generate a unique token for password reset
        $resetToken = md5(uniqid(rand(), true));

        // Store the reset token in the database
        $storeTokenQuery = "UPDATE users SET reset_token = ? WHERE email = ?";
        $stmt = $conn->prepare($storeTokenQuery);
        $stmt->bind_param("ss", $resetToken, $email);
        $stmt->execute();
        $stmt->close();

        // Send email with reset link
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
            $mail->setFrom('djcaaaaastillo@gmail.com', 'MYCC Van Rentals');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset';
            $mail->Body    = "<h1>MYCC Van Rental | Marilao</h1><br><br>
                              Click the following link to reset your password: 
                              <a href='http://tntscheduling.cloud/resetpassword.php?token=$resetToken'>Reset Password</a>";

            $mail->send();
            $successMessage = "Password reset link sent to your email.";
        } catch (Exception $e) {
            $errorMessage = "Error sending reset link. Please try again.";
        }
    } else {
        $errorMessage = "Email not found. Please enter a valid email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <!-- Online CDN for Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="cdn.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
    <title>Forgot Password</title>
</head>
<body>

<div class="two-column-layout2">
    <!-- Form Container -->
    <div class="form-container2">
        <h2>Forgot Password</h2>
        <?php
        if (isset($successMessage)) {
            echo "<p style='color: green;'>$successMessage</p>";
        } elseif (isset($errorMessage)) {
            echo "<p style='color: red;'>$errorMessage</p>";
        }
        ?>

        <form method="post" action="">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Input your email@gmail.com" required><br>

            <input type="submit" value="Send Reset Link">
            <br>
            <a href="login.php">Remember your password? Login</a>
        </form>
    </div>

</div>

</body>
</html>
