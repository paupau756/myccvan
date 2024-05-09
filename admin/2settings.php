<?php include 'head.php';?>
<?php
// Include the database connection file
include("connection.php");

// Check if the form is submitted for changing individual details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_details"])) {
    // Handle changing individual details
    $adminid = $_SESSION['adminid'];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];

    // Update the admin details in the database
    $query = "UPDATE admin SET name = ?, email = ?, contact = ? WHERE adminid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $name, $email, $contact, $adminid);

    if ($stmt->execute()) {
        $successMessage = "Admin details updated successfully!";
        echo '<script>alert("' . $successMessage . '");</script>';
    } else {
        $errorMessage = "Error updating admin details.";
        echo '<script>alert("' . $errorMessage . '");</script>';
    }

    $stmt->close();
}

// Check if the form is submitted for changing username and password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_credentials"])) {
    // Handle changing username and password
    $adminid = $_SESSION['adminid'];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update the admin credentials in the database
    $query = "UPDATE admin SET username = ?, password = ? WHERE adminid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $username, $hashedPassword, $adminid);

    if ($stmt->execute()) {
        $successMessage = "Admin credentials updated successfully!";
        echo '<script>alert("' . $successMessage . '");</script>';
    } else {
        $errorMessage = "Error updating admin credentials.";
        echo '<script>alert("' . $errorMessage . '");</script>';
    }

    $stmt->close();
}

// Fetch the current admin details from the database
$adminid = $_SESSION['adminid'];
$queryAdminDetails = "SELECT name, email, contact, username FROM admin WHERE adminid = ?";
$stmtAdminDetails = $conn->prepare($queryAdminDetails);
$stmtAdminDetails->bind_param("i", $adminid);
$stmtAdminDetails->execute();
$stmtAdminDetails->store_result();
$stmtAdminDetails->bind_result($currentName, $currentEmail, $currentContact, $currentUsername);
$stmtAdminDetails->fetch();
$stmtAdminDetails->close();

// Check if the form is submitted for enabling or disabling admin registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which action is requested (enable or disable)
    if (isset($_POST["enable"])) {
        $status = 'enable';
        updateAdminRegStatus($status);
        logActivity("Admin registration status has been enabled.");
    } elseif (isset($_POST["disable"])) {
        $status = 'disable';
        updateAdminRegStatus($status);
        logActivity("Admin registration status has been disabled.");
    }
}

// Function to update adminregstatus in the database
function updateAdminRegStatus($status)
{
    global $conn;

    // Update the adminregstatus in the adminreg table
    $query = "UPDATE adminreg SET adminregstatus = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $status);

    if ($stmt->execute()) {
        $successMessage = "Admin registration status updated successfully!";
        echo '<script>alert("' . $successMessage . '");</script>';
    } else {
        $errorMessage = "Error updating admin registration status.";
        echo '<script>alert("' . $errorMessage . '");</script>';
    }

    $stmt->close();
}

// Function to log activities
function logActivity($activity)
{
    global $conn;

    // Prepare the query to insert activity log
    $query = "INSERT INTO activitylogs (activities, created_at) VALUES (?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $activity);
    $stmt->execute();
    $stmt->close();
}

// Fetch the current adminregstatus from the database
$query = "SELECT adminregstatus FROM adminreg";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentStatus = $row['adminregstatus'];
} else {
    $errorMessage = "Error fetching admin registration status.";
    echo '<script>alert("' . $errorMessage . '");</script>';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="style.css">
    <!-- Add any additional styles or scripts as needed -->
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>



        <!-- Header -->
        <div class="headerss">
                <h2> Admin Settings</h2>
        </div>

        <!-- Form for changing individual details -->
        <form action="settings.php" method="post">
            <h3>Change Individual Details</h3>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $currentName; ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $currentEmail; ?>" required><br>

            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact" value="<?php echo $currentContact; ?>" required><br>

            <button type="submit" name="change_details">Change Details</button>
        </form>

        <!-- Form for changing username and password -->
        <form action="settings.php" method="post">
            <h3>Change Username and Password</h3>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $currentUsername; ?>" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="checkbox" id="showPassword" onclick="togglePassword()"> Show Password<br>

            <button type="submit" name="change_credentials">Change Credentials</button>
        </form>

        <!-- Form for enabling or disabling admin registration -->
        <form action="settings.php" method="post">
            <h3>Admin Registration Enable & Disable</h3>
            <p>Current Admin Registration Status: <?php echo isset($currentStatus) ? $currentStatus : 'Not available'; ?></p>

            <?php
            $enableButtonText = ($currentStatus === 'enable') ? 'Disable' : 'Enable';
            $disableButtonText = ($currentStatus === 'enable') ? 'Enable' : 'Disable';
            ?>

            <button type="submit" name="<?php echo strtolower($enableButtonText); ?>"><?php echo $enableButtonText; ?> Admin Registration</button>
            <button type="submit" name="<?php echo strtolower($disableButtonText); ?>"><?php echo $disableButtonText; ?> Admin Registration</button>
        </form>


    
         <!-- Script to toggle password visibility -->
        <script>
            function togglePassword() {
                var passwordInput = document.getElementById("password");
                var showPasswordCheckbox = document.getElementById("showPassword");

                if (showPasswordCheckbox.checked) {
                    passwordInput.type = "text";
                } else {
                    passwordInput.type = "password";
                }
            }
        </script>

<?php include 'adfooter.php'; ?>
</body>
</html>
