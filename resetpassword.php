<?php
include("admin/connection.php");

// Check if the token is provided in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $checkTokenQuery = "SELECT * FROM users WHERE reset_token = ?";
    $stmt = $conn->prepare($checkTokenQuery);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        // Token is valid, allow the user to reset the password
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPassword = $_POST["new_password"];

            // Hash the new password before storing it in the database
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the user's password and remove the reset token
            $updatePasswordQuery = "UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?";
            $stmt = $conn->prepare($updatePasswordQuery);
            $stmt->bind_param("ss", $hashedPassword, $token);

            if ($stmt->execute()) {
                $successMessage = "Password reset successfully. You can now login with your new password.";
                // Redirect to the login page
                header("Location: login.php");
                exit; // Ensure that script execution stops after redirection
            } else {
                $errorMessage = "Error resetting password. Please try again.";
            }


            $stmt->close();
        }
    } else {
        $errorMessage = "Invalid or expired token. Please request a new password reset link.";
    }
} else {
    $errorMessage = "Token not provided. Please request a password reset link.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
    <title>Reset Password</title>
    <!-- Your existing head content -->
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("new_password");
            var showPasswordButton = document.getElementById("show_password");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                showPasswordButton.textContent = "Hide";
            } else {
                passwordInput.type = "password";
                showPasswordButton.textContent = "Show";
            }
        }
    </script>
</head>
<body>

<div class="two-column-layout2">
    <!-- Form Container -->
    <div class="password-reset-form">
        <!-- Your existing form content -->
        <form method="post" action="">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <button type="button" id="show_password" onclick="togglePassword()">Show</button><br><br>
            <input type="submit" value="Reset Password">
            <!-- Your existing form content -->
        </form>
    </div>
</div>

</body>
</html>
