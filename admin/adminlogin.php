<?php
session_start();

// Include the database connection file
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Fetch admin details from the database based on the provided username
    $query = "SELECT adminid, username, password, is_verified FROM admin WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($adminid, $dbUsername, $dbPassword, $isVerified);

    if ($stmt->fetch() && password_verify($password, $dbPassword)) {
        if ($isVerified == 1) {
            // Password verification successful and email is verified, set session variables
            $_SESSION["adminid"] = $adminid;
            $_SESSION["username"] = $dbUsername;

            // Redirect to the index.php after successful login
            header("Location: index.php");
            exit();
        } else {
            // Email not verified
            $error = "Email not verified. Please check your email for verification.";
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
    <title>Admin Login</title>
    <!-- <link rel="stylesheet" href="adminstyle.css"> -->
    <style>
    /* Common styles for all screen sizes */
    body {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        background: linear-gradient(to right, #D6DAC8, #DDDDDD);
        font-family: 'Barlow Sans', sans-serif;
        color: black;
    }

    header {
        background-color: transparent;
        color: #fff;
        padding: 20px;
        text-align: center;
    }

    /* Responsive styles for phones */
    @media only screen and (max-width: 600px) {
        .login-container {
            margin: 10%;
            padding: 20px;
        }

        .visit-website-container {
            margin-top: 20%;
        }

        .visit-website-container p {
            font-size: 16px;
        }

        .visit-website-logo {
            max-width: 150px;
        }
    }

    /* Common styles for larger screens */
    .login-container {
        max-width: 400px;
        margin-right: 15%;
        margin-top: 10%;
        padding: 50px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        float: right; /* Align to the right */
    }

    .error-message {
        color: black;
        margin-bottom: 10px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        color: black;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        box-sizing: border-box;
    }

    input[type="checkbox"] {
        margin-right: 5px;
    }

    input[type="submit"] {
        background-color: #65B741;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: green;
    }

    /* Visit the Website Section */
    .visit-website-container {
        margin-top: 15%;
        text-align: center;
    }

    .visit-website-container p {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .visit-website-logo {
        max-width: 300px; /* Adjust the width as needed */
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.9);
        margin-bottom: 5%;
    }

    .visit-website-button {
        display: inline-block;
        background-color: #65B741;
        color: #fff;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .visit-website-button:hover {
        background-color: green;
    }
</style>

</head>
<body>

<header>
    <h1><!-- Admin Login --></h1>
</header>

<!-- Your body content here -->
<div class="login-container">
    <h1>Administration Login</h1>
    <?php
    if (isset($error)) {
        echo "<p class='error-message'>$error</p>";
    }
    ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <input type="checkbox" id="showPassword" onclick="showHidePassword()">
            <label for="showPassword">Show Password</label>

            <input type="submit" value="Login">
        </div>
        <hr><br>

        <a href="adminreg.php" style="margin-right: 20px;">Create Account</a>
         <a href="forgotpassword.php">Forgot Password?</a>
    </form>
</div>

<!-- Visit the Website Section -->
<div class="visit-website-container">
    <img src="uploads/mycc.jpg" alt="Website Logo" class="visit-website-logo">
    <!-- <p>Visit the website:</p>
    <a href="https://tntscheduling.cloud/" target="_blank" class="visit-website-button">Continue</a> -->
</div>

<script>
    function showHidePassword() {
        var passwordInput = document.getElementById("password");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    }
</script>


</body>
</html>

