<?php
// Include the connection file
include 'admin/connection.php';

// Start session
session_start();

// Initialize unread count
$unread_count = 0;

// Check if the user is logged in
if(isset($_SESSION['userid'])) {
    $loggedin = true;

    // Get the logged-in user's ID
    $userid = $_SESSION['userid'];

    // Query to fetch the count of unread notifications for the logged-in user
    $query = "SELECT COUNT(*) AS unread_count FROM notify WHERE userid = ? AND status = 'unread'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the unread notification count
    if ($row = $result->fetch_assoc()) {
        $unread_count = $row['unread_count'];
    }

    // Close the statement
    $stmt->close();
} else {
    // User is not logged in
    $loggedin = false;
}

// Close the database connection
$conn->close();
?>


<?php
// Start session
// session_start();

// Check if the user is not logged in
if (!isset($_SESSION["userid"])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include("admin/connection.php");

// Fetch user details
$userid = $_SESSION["userid"];
$query = "SELECT * FROM users WHERE userid = $userid";
$result = $conn->query($query);

// Handle error or redirect if user not found
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Handle error or redirect as needed
}

// Function to update user details
function updateUserField($field, $value, $userid, $conn) {
    $updateQuery = "UPDATE users SET $field='$value' WHERE userid=$userid";
    return $conn->query($updateQuery);
}

// Function to update user password
function updatePassword($newPassword, $userid, $conn) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateQuery = "UPDATE users SET password='$hashedPassword' WHERE userid=$userid";
    return $conn->query($updateQuery);
}

// Function to update the username
function updateUsername($newUsername, $userid, $conn) {
    // Check if the new username already exists in the database
    $checkUsernameQuery = "SELECT userid FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkUsernameQuery);
    $stmt->bind_param("s", $newUsername);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // If a row is returned, it means the username already exists
    if ($result->num_rows > 0) {
        return "Username already in use. Please choose another username.";
    }

    // Update the username
    $updateQuery = "UPDATE users SET username=? WHERE userid=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $newUsername, $userid);
    if ($stmt->execute()) {
        return true;
    } else {
        return "Error updating username. Please try again.";
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["password_change"])) {
        $currentPassword = $_POST["current_password"];
        $newPassword = $_POST["new_password"];

        // Verify current password
        if (isset($user["password"]) && password_verify($currentPassword, $user["password"])) {
            if (updatePassword($newPassword, $userid, $conn)) {
                $successMessage = "Password updated successfully!";
            } else {
                $errorMessage = "Error updating password. Please try again.";
            }
        } else {
            $errorMessage = "Current password is incorrect.";
        }
    } elseif (isset($_POST["field"])) {
        $fieldToUpdate = $_POST["field"];
        $newValue = $_POST["new_value"];

        // Check if the field is 'username'
        if ($fieldToUpdate === 'username') {
            $updateResult = updateUsername($newValue, $userid, $conn);
            if ($updateResult === true) {
                $successMessage = "Username updated successfully!";
                $user['username'] = $newValue; // Update the local user variable
            } else {
                $errorMessage = $updateResult; // Display the error message returned from the function
            }
        } else {
            // Handle other fields
        }
    }
}
?>

<?php
// Check if the user is logged in
if(isset($_SESSION['userid'])) {
    $loggedin = true;

    // Include the database connection file
    include 'admin/connection.php';

    // Prepare and execute a query to fetch the user's name
    $userid = $_SESSION['userid'];
    $query = "SELECT name FROM users WHERE userid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the query was successful and if a row was returned
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['name'];
    } else {
        // Error handling if user not found
        $username = "Unknown";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();

} else {
    $loggedin = false;
    $username = ""; // Set username to empty if user is not logged in
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <!-- Online CDN for Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="cdn2.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
</head>
<body>

<!-- header navigation ito -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">
        <img src="admin/uploads/mycc.jpg" alt="MYCC VAN RENTAL Logo" height="30" style="border-radius: 12px;" >
        MYCC VAN RENTAL
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">HOME</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tours.php">PACKAGES</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="form.php">INQUIRE</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="notification.php">NOTIFICATIONS <?php echo ($unread_count > 0) ? "<span class='badge badge-danger'>$unread_count</span>" : ""; ?></a>
            </li>
        </ul>
        <?php if($loggedin) { ?>
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <strong><?php echo $username; ?></strong>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="bookingcart.php">Booking Cart</a>
                    <a class="dropdown-item" href="bookinghistory.php">Booking History</a>
                    <a class="dropdown-item" href="settings.php">Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
        <?php } else { ?>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="login.php">LOGIN</a>
            </li>
        </ul>
        <?php } ?>
    </div>
</nav>
<script>
    // JavaScript to show the dropdown menu when clicking on the user's name
    document.addEventListener("DOMContentLoaded", function() {
        var dropdownToggle = document.querySelector('.dropdown-toggle');

        dropdownToggle.addEventListener('click', function() {
            var dropdownMenu = this.nextElementSibling;
            dropdownMenu.classList.toggle('show');
        });

        // JavaScript to toggle the collapsed navbar when the toggle button is clicked
        var navbarToggler = document.querySelector('.navbar-toggler');
        var navbarCollapse = document.querySelector('.navbar-collapse');

        navbarToggler.addEventListener('click', function() {
            navbarCollapse.classList.toggle('show');
        });
    });
</script>
<!-- header navigation ito -->


<div class="account-section">
    <h2>Managing Account</h2>
</div>

<?php
if (isset($successMessage)) {
    echo "<p class='success-message'>$successMessage</p>";
} elseif (isset($errorMessage)) {
    echo "<p class='error-message'>$errorMessage</p>";
}
?>

<!-- User Details Form -->
<form method="post" action="" class="form-section">
    <label for="name"><i class="fas fa-user"></i> Name:</label>
    <input type="text" id="name" name="new_value" value="<?php echo $user['name']; ?>" class="form-input">
    <button type="submit" name="field" value="name" class="form-button"><i class="fas fa-save"></i></button>
</form>

<!-- Profile Picture Form -->
<!-- <form method="post" action="" enctype="multipart/form-data" class="form-section">
    <label for="profile_picture"><i class="fas fa-image"></i> Profile Picture:</label>
    <input type="file" id="profile_picture" name="new_value" accept="image/*" class="form-input">
    <button type="submit" name="field" value="profile_picture" class="form-button"><i class="fas fa-save"></i></button>
</form> -->

<!-- Address Form -->
<form method="post" action="" class="form-section">
    <label for="address"><i class="fas fa-map-marker-alt"></i> Address:</label>
    <input type="text" id="address" name="new_value" value="<?php echo $user['address']; ?>" class="form-input">
    <button type="submit" name="field" value="address" class="form-button"><i class="fas fa-save"></i></button>
</form>

<!-- Email Form -->
<form method="post" action="" class="form-section">
    <label for="email"><i class="fas fa-envelope"></i> Email:</label>
    <input type="email" id="email" name="new_value" value="<?php echo $user['email']; ?>" class="form-input">
    <button type="submit" name="field" value="email" class="form-button"><i class="fas fa-save"></i></button>
</form>

<!-- Contact Number Form -->
<form method="post" action="" class="form-section">
    <label for="contact"><i class="fas fa-phone"></i> Contact Number:</label>
    <input type="text" id="contact" name="new_value" value="<?php echo $user['contact']; ?>" class="form-input">
    <button type="submit" name="field" value="contact" class="form-button"><i class="fas fa-save"></i></button>
</form>

<!-- Username Form -->
<form method="post" action="" class="form-section">
    <label for="username"><i class="fas fa-user"></i> Username:</label>
    <input type="text" id="username" name="new_value" value="<?php echo $user['username']; ?>" class="form-input">
    <button type="submit" name="field" value="username" class="form-button"><i class="fas fa-save"></i></button>
</form>




<!-- Change Password Form -->
<form method="post" action="" class="form-section1">
    <input type="checkbox" id="password_change" name="password_change">
    <label for="password_change">Change Password</label><br>

    <div id="password_fields" style="display: none;">
        <label for="current_password"><i class="fas fa-lock"></i> Current Password:</label>
        <input type="password" id="current_password" name="current_password" class="form-input">
        <input type="checkbox" id="show_password" onclick="showPassword()"> Show <br>

        <label for="new_password"><i class="fas fa-lock"></i> New Password:</label>
        <input type="password" id="new_password" name="new_password" class="form-input"><br>

        <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-input"><br>

        <button type="submit" class="form-button"><i class="fas fa-save"></i> Update Password</button>
    </div>
</form>




<!-- JavaScript to toggle password fields visibility -->
<script>
document.getElementById("password_change").addEventListener("change", function () {
    var passwordFields = document.getElementById("password_fields");
    passwordFields.style.display = this.checked ? "block" : "none";
});

// Function to show/hide password
function showPassword() {
    var passwordField = document.getElementById("current_password");
    var showPasswordCheckbox = document.getElementById("show_password");

    if (showPasswordCheckbox.checked) {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}
</script>

<br><br><br><br><br>
<!-- Include footer -->
<?php include("footer.php"); ?>

</body>
</html>
