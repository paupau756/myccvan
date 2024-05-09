<?php
// Include the database connection file
include("connection.php");

// Fetch the current adminregstatus from the database
$query = "SELECT adminregstatus FROM adminreg";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $adminregstatus = $row['adminregstatus'];

    if ($adminregstatus === 'disable') {
        // Admin registration is disabled, redirect to an error page
        header("Location: error.php");
        exit();
    }
} else {
    // Handle the case when the adminregstatus is not available
    // You may want to redirect to an error page or handle it in a different way
    $errorMessage = "Error fetching admin registration status.";
}

// Continue with the registration process if adminregstatus is enable

// Include PHPMailer Autoload
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];

    // Check if the email is already in use
    $checkEmailQuery = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMessage = "Email is already in use.";
    } else {
        // Check if the username is already taken
        $checkUsernameQuery = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($checkUsernameQuery);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errorMessage = "Username is already taken.";
        } else {
            // Hash the password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Generate a unique verification token
            $verificationToken = md5(uniqid(rand(), true));

            // Handle profile picture upload (assuming 'profile_picture' is the name attribute of the file input)
            // $targetDir = "profile_pictures/";
            // $profilePicture = basename($_FILES["profile_picture"]["name"]);
            // $targetFile = $targetDir . $profilePicture;
            // move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile);

            // Insert admin details and verification token into the database
            $insertQuery = "INSERT INTO admin (name, username, password, email, contact, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssssss", $name, $username, $hashedPassword, $email, $contact, $verificationToken);

            if ($stmt->execute()) {
                // Registration successful

                // Send email verification
                $verificationLink = "http://tntscheduling.com/adminverify.php?token=$verificationToken";
                $subject = "Email Verification for Admin Registration";
                $message = "Thank you for registering as an admin! Please click the following link to verify your email: $verificationLink";
                sendEmailNotification($email, $subject, $message);

                $successMessage = "Admin registered successfully! Please check your email for verification.";
            } else {
                // Registration failed
                $errorMessage = "Error registering admin. Please try again.";
            }

            $stmt->close();
        }
    }
}

// Close the database connection
$conn->close();

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
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        // Handle error, if any
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <style>
        body {
             font-family: 'Barlow Condensed', sans-serif;
            background: linear-gradient(to right,  #D6DAC8, #DDDDDD);
            text-align: center;
            padding: 20px;
        }

        h2 {
            color: black;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: transparent;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: black;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="file"] {
            margin-bottom: 10px;
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

        p {
            margin: 10px 0;
            font-size: 16px;
        }

        p[style^='color: green'] {
            color: green;
        }

        p[style^='color: red'] {
            color: red;
        }
        .password-container {
        position: relative;
    }

    #password {
        padding-right: 30px; /* Adjust the padding to make room for the button */
    }

    #togglePassword {
        position: absolute;
        right: 10px; /* Adjust the right position as needed */
        top: 40%;
        transform: translateY(-50%);
        cursor: pointer;
        border: none;
        background: none;
    }
    </style>
</head>
<body>

<h2>Admin Registration</h2>

<?php
if (isset($successMessage)) {
    echo "<p style='color: green;'>$successMessage</p>";
} elseif (isset($errorMessage)) {
    echo "<p style='color: red;'>$errorMessage</p>";
}
?>

<form method="post" action="" enctype="multipart/form-data">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" placeholder="Enter your Name" required><br>

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" placeholder="Enter Username" required><br>

    <label for="password">Password:</label>
    <div class="password-container">
        <input type="password" id="password" name="password" placeholder="Enter Password" required>
        <button type="button" id="togglePassword"><i class="fas fa-eye"></i></button>
    </div>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" pattern="[a-zA-Z0-9._%+-]+@gmail\.com" title="Please enter a valid Gmail address" placeholder="Enter your Gmail" required><br>

    <label for="contact">Contact:</label>
    <input type="text" id="contact" name="contact" placeholder="Enter your Contact Number" pattern="09\d{9}" title="Please enter a valid contact number starting with 09 and followed by 9 digits" required><br>


    <!-- <label for="profile_picture">Profile Picture:</label>
    <input type="file" id="profile_picture" name="profile_picture" accept=".jpg, .jpeg, .png"><br> -->

    <input type="submit" value="Register">
    <hr>
    <a href="adminlogin.php" style="text-decoration: none;">Already have an account? Login</a>
</form>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var passwordField = document.getElementById('password');
        var icon = this.querySelector('i');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
</body>
</html>
