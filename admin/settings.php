<?php
// Start session
include 'head.php';

// Include database connection
include('connection.php');

// Function to hash passwords
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to update admin details
function updateAdminDetails($adminid, $name, $email, $contact, $username) {
    global $conn;
    $sql = "UPDATE admin SET name=?, email=?, contact=?, username=? WHERE adminid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $contact, $username, $adminid);
    $stmt->execute();
    $stmt->close();
}

// Function to fetch admin details
function fetchAdminDetails($adminid) {
    global $conn;
    $sql = "SELECT name, email, contact, username FROM admin WHERE adminid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $adminid);
    $stmt->execute();
    $stmt->bind_result($name, $email, $contact, $username);
    $stmt->fetch();
    $stmt->close();
    return array("name" => $name, "email" => $email, "contact" => $contact, "username" => $username);
}

// Function to verify current password
function verifyCurrentPassword($adminid, $password) {
    global $conn;
    $sql = "SELECT password FROM admin WHERE adminid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $adminid);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();
    return password_verify($password, $hashedPassword);
}

// Check if form is submitted for updating admin details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['adminid'])) {
    $adminid = $_SESSION['adminid'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $username = $_POST['username'];
    $currentPassword = $_POST['current_password'];

    // Verify current password
    if (verifyCurrentPassword($adminid, $currentPassword)) {
        // Update admin details
        try {
            updateAdminDetails($adminid, $name, $email, $contact, $username);
            echo "Admin details updated successfully.";
        } catch (mysqli_sql_exception $e) {
            // Check for duplicate key error
            if ($e->getCode() === 1062) {
                $errorMessage = "Email or username already exists.";
                echo '<script>alert("' . $errorMessage . '");</script>';
            } else {
                echo "An error occurred while updating admin details.";
            }
        }
    } else {
        echo "Incorrect current password. Admin details not updated.";
    }
}

// Redirect to login if adminid not set in session
if (!isset($_SESSION['adminid'])) {
    header("Location: login.php");
    exit();
}

// Fetch admin details
$adminDetails = fetchAdminDetails($_SESSION['adminid']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
</head>
<body>
    <div class="headerss">
        <h1>Admin Settings</h1>
    </div>
    <!-- Form for changig details -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo $adminDetails['name']; ?>"><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo $adminDetails['email']; ?>"><br>

        <label for="contact">Contact:</label><br>
        <input type="text" id="contact" name="contact" value="<?php echo $adminDetails['contact']; ?>"><br>

        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?php echo $adminDetails['username']; ?>"><br>

        <label for="current_password">Current Password:</label><br>
        <input type="password" id="current_password" name="current_password"><br>

        <input type="submit" value="Save">
    </form>


    <!-- Form for changing password -->
    <form method="post" action="change_password.php" id="passwordForm">
        <h3>Change Password</h3>
        <label for="current_password">Current Password:</label><br>
        <input type="password" id="current_password" name="current_password"><br>

        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password">
        <input type="checkbox" id="show_new_password"> <!-- Toggle button for new password -->
        <label for="show_new_password">Show Password</label><br>

        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password">
        <input type="checkbox" id="show_confirm_password"> <!-- Toggle button for confirm password -->
        <label for="show_confirm_password">Show Password</label><br>

        <input type="submit" value="Change Password">
    </form>

    <script>
        // Function to toggle password visibility
        function togglePasswordVisibility(inputField, checkbox) {
            var passwordField = document.getElementById(inputField);
            if (checkbox.checked) {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }

        // Add event listeners for toggle buttons
        document.getElementById("show_new_password").addEventListener("change", function() {
            togglePasswordVisibility("new_password", this);
        });

        document.getElementById("show_confirm_password").addEventListener("change", function() {
            togglePasswordVisibility("confirm_password", this);
        });
    </script>

    <!-- Form for updating admin registration status -->
    <form method="post" action="update_adminreg.php">
        <h3>Update Admin Registration Status</h3>
        <label for="adminreg_status">Admin Registration Status:</label><br>
        <?php
        // Include database connection
        include('connection.php');

        // Fetch current admin registration status from the database
        $sql = "SELECT adminregstatus FROM adminreg LIMIT 1"; // Assuming there's only one row in adminreg table
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_status = $row['adminregstatus'];
            echo '<select id="adminreg_status" name="adminreg_status">';
            echo '<option value="enable" ' . ($current_status == 'enable' ? 'selected' : '') . '>Enable</option>';
            echo '<option value="disable" ' . ($current_status == 'disable' ? 'selected' : '') . '>Disable</option>';
            echo '</select><br>';
        } else {
            echo 'Error fetching admin registration status.';
        }
        ?>
        <input type="submit" value="Update Status">
    </form>



</body>
</html>