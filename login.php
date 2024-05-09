<?php
session_start();

// Include the database connection file
include("admin/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Fetch user details from the database based on the provided username
    $query = "SELECT userid, username, password, is_verified FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userid, $dbUsername, $dbPassword, $isVerified);

    if ($stmt->fetch() && password_verify($password, $dbPassword)) {
        if ($isVerified == 1) {
            // Account is verified, set session variables
            $_SESSION["userid"] = $userid;
            $_SESSION["username"] = $dbUsername;

            // Redirect to the index.php after successful login
            header("Location: index.php");
            exit();
        } else {
            // Account is not verified
            $error = "Your account is not verified. Please check your email for verification instructions.";
        }
    } else {
        // Incorrect username or password
        $error = "Invalid username or password";
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700&display=swap">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
    <title>Login</title>
    <style>
        body {
            background-image: url('images/van8.jpg'); /* Replace with your background image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.3);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-form input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            display: inline-block;
        }

        .login-form input[type="submit"] {
            background-color: #2980b9; /* Egg yellow */
            color: white;
            cursor: pointer;
        }

        .login-form input[type="submit"]:hover {
            background-color: #2980b9; /* Darker shade */
        }

        .login-form input[type="radio"] {
            margin-right: 5px;
        }

        .login-form .show-password-label {
            margin-left: 5px;
            color: #555;
        }

        .social-icons {
            text-align: center;
            margin-top: 20px;
        }

        .social-icons a {
            text-decoration: none;
            color: #333;
            margin: 0 10px;
        }

        .social-icons a:hover {
            color: #1877f2; /* Facebook color */
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form class="login-form" method="post" action="">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" id="password" placeholder="Password" required>

    <!-- Show Password Button -->
    <input type="button" id="showPasswordBtn" value="Show Password" onclick="togglePasswordVisibility()">

    <input type="submit" value="Login">
    <br>
    <center><a href="createaccount.php" style="color: blue; text-decoration: none;">No account? Sign up</a></center>

    <!-- Add Forgot Password Link -->
    <p style="color: black; margin-top: 1px; text-align: center;">Forgot your password? <a href="forgotpassword.php" style="text-decoration: none;">Reset Password</a></p>
</form>

<script>
    function togglePasswordVisibility() {
        var passwordInput = document.getElementById('password');
        var showPasswordBtn = document.getElementById('showPasswordBtn');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            showPasswordBtn.value = 'Hide Password';
        } else {
            passwordInput.type = 'password';
            showPasswordBtn.value = 'Show Password';
        }
    }
</script>

<hr>
    <!-- <div class="social-icons">
        <a href="https://facebook.com" target="_blank">
            <img src="admin/uploads/Facebook-Logosu.png" alt="Facebook" style="width: 44px;">
        </a>
        <a href="https://mail.google.com" target="_blank">
            <img src="admin/uploads/Gmail_icon_(2020).svg.png" alt="Gmail" style="width: 25px;">
        </a>
    </div> -->
</div>

</body>
</html>
