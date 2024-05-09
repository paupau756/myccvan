<?php
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["token"])) {
    $token = $_GET["token"];

    // Check if the verification token exists in the database
    $checkTokenQuery = "SELECT * FROM admin WHERE verification_token = ?";
    $stmt = $conn->prepare($checkTokenQuery);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mark the admin's email as verified
        $updateQuery = "UPDATE admin SET is_verified = 1, verification_token = NULL WHERE verification_token = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        echo "Email verification successful. You can now <a href='https://tntscheduling.cloud/admin/adminlogin.php'>login as an admin</a>.";
    } else {
        echo "Invalid verification token.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
