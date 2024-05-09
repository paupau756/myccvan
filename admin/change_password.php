<?php
// Start session
session_start();

// Include database connection
include('connection.php');

// Function to hash passwords
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
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

// Function to update admin password
function updateAdminPassword($adminid, $password) {
    global $conn;
    $hashedPassword = hashPassword($password);
    $sql = "UPDATE admin SET password=? WHERE adminid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashedPassword, $adminid);
    $stmt->execute();
    $stmt->close();
}

// Check if form is submitted for changing password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['adminid'])) {
    $adminid = $_SESSION['adminid'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Verify current password
    if (verifyCurrentPassword($adminid, $currentPassword)) {
        // Check if new password and confirm password match
        if ($newPassword === $confirmPassword) {
            // Update admin password
            updateAdminPassword($adminid, $newPassword);
            echo "Password changed successfully.";
        } else {
            echo "New password and confirm password do not match.";
        }
    } else {
        echo "Incorrect current password.";
    }
}

// Redirect to settings.php
header("Location: settings.php");
exit();

// Redirect to login if adminid not set in session
if (!isset($_SESSION['adminid'])) {
    header("Location: adminlogin.php");
    exit();
}
?>
