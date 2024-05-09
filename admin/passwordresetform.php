<?php
// Start the session
session_start();

// Include your database connection code here if not already included
include("connection.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $resetEmail = $_SESSION['reset_email'];

    // Check if passwords match
    if ($password == $confirmPassword) {
        // Update password in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security
        $updatePasswordQuery = "UPDATE admin SET password = '$hashedPassword' WHERE email = '$resetEmail'";
        // Execute the query to update the password
        // Assuming $conn is your database connection object
        if ($conn->query($updatePasswordQuery) === TRUE) {
            echo "Password updated successfully.";
        } else {
            echo "Error updating password: " . $conn->error;
        }
    } else {
        echo "Passwords do not match. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
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
        
        .password-reset-container {
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
            margin-bottom: 4px;
            color: #555;
            font-weight: bold;
        }
        
        button[type="submit"] {
            margin-top: 20px;
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

<div class="password-reset-container">
    <h2>Password Reset</h2>
    <form action="" method="post">
        <label for="password">New Password:</label>
        <input type="password" name="password" id="password" required>
        <input type="checkbox" onclick="togglePasswordVisibility('password')"> Show<br><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <input type="checkbox" onclick="togglePasswordVisibility('confirm_password')"> Show<br>
        <button type="submit">Reset Password</button>
    </form>
</div>

<script>
    function togglePasswordVisibility(inputId) {
        var x = document.getElementById(inputId);
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>

</body>
</html>


