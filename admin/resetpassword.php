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
    $verificationCode = $_POST['verification_code'];
    $enteredCode = $_POST['entered_code'];
    $resetEmail = $_SESSION['reset_email'];

    if ($verificationCode == $enteredCode) {
        // Verification successful, proceed to reset password
        // Here you can redirect the user to a password reset form
        header("Location: passwordresetform.php");
        exit();
    } else {
        // Verification code does not match
        echo "Verification code does not match. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        /* Add your CSS styling here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        .reset-password-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        
        h2 {
            color: #333;
        }
        
        form {
            margin-top: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }
        
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="reset-password-container">
    <h2>Reset Password</h2>
    <form action="" method="post">
        <label for="verification_code">Enter the verification code sent to your email:</label>
        <input type="text" name="entered_code" required>
        <input type="hidden" name="verification_code" value="<?php echo $_SESSION['verification_code']; ?>">
        <button type="submit">Verify Code</button>
    </form>
</div>

</body>
</html>

