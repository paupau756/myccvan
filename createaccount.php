<?php
// Include the database connection file
include("admin/connection.php");

// Include PHPMailer Autoload
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $address = $_POST["address"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if the email is already in use
    $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
    $checkEmailStmt = $conn->prepare($checkEmailQuery);
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailResult = $checkEmailStmt->get_result();

    if ($checkEmailResult->num_rows > 0) {
        // Email is already in use, display an alert
        echo "<script>alert('This email is already in use. Please enter another email.');";
        echo "window.history.back();</script>";


    } else {
        // Hash the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Generate a unique token for email verification
        $verificationToken = md5(uniqid(rand(), true));

        // Insert user details and verification token into the database
        $query = "INSERT INTO users (name, address, email, contact, username, password, verification_token) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $name, $address, $email, $contact, $username, $hashedPassword, $verificationToken);

        if ($stmt->execute()) {
            // Registration successful

            // Notify admin about the new user
            $notificationMessage = "New user registered: $name";
            $insertNotificationQuery = "INSERT INTO notifyadmin (message, status, created) VALUES ('$notificationMessage', 'unread', NOW())";
            $conn->query($insertNotificationQuery);


            // Send email verification
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
                $mail->addAddress($email, $name);

                
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Email Verification';
                $mail->Body = "<div style='text-align: center;'>
                                <h1>MYCC Vanrentals</h1>
                                <p>Click the following link to verify your email:</p>
                                <p><a href='http://tntscheduling.cloud/verifyemail.php?token=$verificationToken' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none;'>Verify Email</a></p>
                              </div>";



                                $mail->send();
                                $successMessage = "Account created successfully! Verification email sent.";
                            } catch (Exception $e) {
                                $errorMessage = "Error sending verification email. Please try again.";
                            }
                        } else {
                            // Registration failed
                            $errorMessage = "Error creating account. Please try again.";
                        }

                        $stmt->close();
                    }
                }

// Close the database connection
$conn->close();
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
    <title>Create Account</title>
    <style>
        body{
            font-family: times new roman;
        }
        .addressSuggestion {
            cursor: pointer;
            padding: 5px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .addressSuggestion:hover {
            background-color: #e9e9e9;
        }

    </style>
</head>

<body>
    <div class="two-column-layout">

        <div class="get-started-section">
            <h1>Get Started</h1>
            <p>Ready for your next adventure? Get started by exploring our services.</p>
            <a href="index.php">Get Started <i class="fas fa-arrow-right"></i></a>
        </div>

        <!-- Form Container -->
        <div class="account-form-container">
            <?php
            if (isset($successMessage)) {
                echo "<p style='color: green;'>$successMessage</p>";
            } elseif (isset($errorMessage)) {
                echo "<p style='color: red;'>$errorMessage</p>";
            }
            ?>
            <h2>Create Account</h2>
            <form method="post" action="">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your fullname" required><br>

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" required>
                <div id="addressSuggestions"></div>


                <label for="email">Email:</label>
                <input type="email" id="email" name="email" pattern="[a-zA-Z0-9._%+-]+@gmail\.com" title="Please enter a valid Gmail address (e.g., name@gmail.com)" placeholder="your@gmail.com" required><br>

                <label for="contact">Contact:</label>
                <input type="text" id="contact" name="contact" pattern="09\d{9}" title="Please enter a valid contact number starting with 09 and followed by 9 digits" placeholder="09xxxxxxxxx" required><br>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <span id="username-error" style="color: red; display: none;">This username is already in use.</span><br>


                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <!-- Show Password Button -->
                <input type="button" id="showPasswordBtn" value="Show Password" onclick="togglePasswordVisibility()">
                
                <input type="submit" value="Create Account">
                <br><hr>
                <a href="login.php">Already have an account? Login</a>
            </form>
        </div>

    </div>
</body>

</html>


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
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("password");
            passwordField.type = (passwordField.type === "password") ? "text" : "password";
        }

        // Check if the email is already in use
        <?php
        if (isset($errorMessage) && strpos($errorMessage, 'Duplicate entry') !== false) {
            echo 'alert("This email is already in use. Please enter another email.");';
        }
        ?>
    </script>

    <script>
        // Assuming you have a function to validate the username
        function validateUsername() {
            // Your validation logic here
            var isUsernameValid = true; // Placeholder for your validation result
            
            if (!isUsernameValid) {
                document.getElementById("username-error").style.display = "block";
            } else {
                document.getElementById("username-error").style.display = "none";
            }
        }

    </script>
    <script>
        // Function to fetch address suggestions from OpenStreetMap Nominatim API
        function getAddressSuggestions(query) {
            fetch(`https://nominatim.openstreetmap.org/search?q=${query}&format=json`)
            .then(response => response.json())
            .then(data => {
                displayAddressSuggestions(data);
            })
            .catch(error => {
                console.error('Error fetching address suggestions:', error);
            });
        }

        // Function to display address suggestions
        function displayAddressSuggestions(suggestions) {
            const suggestionsContainer = document.getElementById('addressSuggestions');
            suggestionsContainer.innerHTML = ''; // Clear previous suggestions

            suggestions.forEach(suggestion => {
                const suggestionElement = document.createElement('div');
                suggestionElement.textContent = suggestion.display_name;
                suggestionElement.classList.add('addressSuggestion');
                suggestionElement.addEventListener('click', function() {
                    document.getElementById('address').value = suggestion.display_name;
                    suggestionsContainer.innerHTML = ''; // Clear suggestions after selecting
                });
                suggestionsContainer.appendChild(suggestionElement);
            });
        }

        // Event listener for input changes in the address field
        document.getElementById('address').addEventListener('input', function(event) {
            const query = event.target.value;
            if (query.length >= 3) { // Fetch suggestions only when at least 3 characters are typed
                getAddressSuggestions(query);
            } else {
                document.getElementById('addressSuggestions').innerHTML = ''; // Clear suggestions if query is too short
            }
        });

    </script>

</body>

</html>

