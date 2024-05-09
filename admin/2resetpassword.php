<?php
// Start the session
session_start();

// Include your database connection code here if not already included
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure that the verification code matches the one stored in the session
    if ($_POST['verification_code'] == $_SESSION['verification_code']) {
        // Get the user's email from the session
        $userEmail = $_SESSION['reset_email'];

        // Generate a hashed password
        $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        // Update the password in the database
        $updateQuery = "UPDATE `admin` SET `password` = '$newPassword' WHERE `email` = '$userEmail'";

        if (mysqli_query($conn, $updateQuery)) {
            // Password update successful, display success message and redirect to login
            echo "<script>alert('Password reset successful. You can now login with your new password.');</script>";
            echo "<script>window.location.href = 'adminlogin.php';</script>";
            exit();
        } else {
            // Handle database error
            echo "Error updating password: " . mysqli_error($conn);
        }
    } else {
        // Verification code does not match, handle the error
        echo "<script>alert('Verification code is incorrect. Please try again.');</script>";
    }
}

// Close your database connection if necessary
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
    /* Form Container */
    form {
        max-width: 400px;
        margin-top: 20%;
        margin-left: 33%;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 1); /* Box shadow */
        text-align: center;
    }

    /* Form Heading */
    h2 {
        margin-bottom: 20px;
        color: #333;
    }

    /* Input Container */
    .password-container {
        position: relative;
        margin: 2%;
    }

    /* Show/Hide Password Icon */
    .password-container i {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #999;
    }

    /* Checkbox and Label */
    #showPassword,
    .show-password-label {
        margin-top: 10px;
        color: #333;
    }

    /* Submit Button */
    button[type="submit"] {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
        background-color: #0056b3;
    }

    /* Responsive adjustments */
    @media only screen and (max-width: 600px) {
        form {
            max-width: 300px;
            margin: 30% auto; /* Center the form */
            padding: 15px;
        }
    }
</style>

    
</head>
<body>

<form action="" method="post">
    <h2>Reset Password</h2>
    <label for="verification_code" class="verification-label">Verification Code:</label>
    <input type="text" name="verification_code" class="verification-input" required>

    <div class="password-container">
        <label for="password">New Password:</label>
        <input type="password" name="password" id="password" required>
        <i class="fas fa-eye-slash" id="passwordToggle"></i>
    </div>

    <div>
        <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
        <label for="showPassword">Show Password</label>
    </div>

    <button type="submit">Reset Password</button>
</form>

<script>
    function togglePasswordVisibility() {
        const passwordField = document.getElementById("password");
        const passwordToggle = document.getElementById("passwordToggle");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            passwordToggle.classList.remove("fa-eye-slash");
            passwordToggle.classList.add("fa-eye");
        } else {
            passwordField.type = "password";
            passwordToggle.classList.remove("fa-eye");
            passwordToggle.classList.add("fa-eye-slash");
        }
    }
</script>

</body>
</html>
