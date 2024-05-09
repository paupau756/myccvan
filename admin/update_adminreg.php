<?php
include 'head.php'; // Start session and include necessary files

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include('connection.php');

    // Get admin registration status from the form
    $adminreg_status = $_POST['adminreg_status'];

    // Update adminreg status in the database
    try {
        $sql = "UPDATE adminreg SET adminregstatus = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $adminreg_status);
        $stmt->execute();
        $stmt->close();

        // Alert message
        echo '<script>alert("Admin registration status updated successfully.");</script>';
        // Reload to settings.php
        echo '<script>window.location.href = "settings.php";</script>';
    } catch (mysqli_sql_exception $e) {
        // Alert message
        echo '<script>alert("An error occurred while updating admin registration status.");</script>';
        // Reload to settings.php
        echo '<script>window.location.href = "settings.php";</script>';
    }
}
?>
