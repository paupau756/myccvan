<?php
include("admin/connection.php");

if (isset($_GET['token'])) {
    $verificationToken = $_GET['token'];

    // Check if the token exists in the database
    $checkTokenQuery = "SELECT * FROM users WHERE verification_token = ?";
    $stmt = $conn->prepare($checkTokenQuery);
    $stmt->bind_param("s", $verificationToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, mark the user as verified
        $updateQuery = "UPDATE users SET is_verified = 1 WHERE verification_token = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("s", $verificationToken);
        $stmt->execute();

        $verificationMessage = "Email verified successfully! You can now log in.";
    } else {
        $verificationMessage = "Invalid verification token. Please try again or contact support.";
    }
} else {
    $verificationMessage = "Verification token not provided. Please check your email for the verification link.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <!-- Online CDN for Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="cdn.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
</head>

<body class="verification-body">

<div class="verify-email1" >
    <h2 class="verification-heading">Email Verification</h2>
    <p class="verification-message"><?php echo $verificationMessage; ?></p>

<!-- Link back to your website or login page -->
<a href="http://tntscheduling.cloud" class="verification-link">Back to Website</a>
</div>


</body>
</html>