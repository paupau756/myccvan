<?php
// Include your database connection file
include 'admin/connection.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $refundAmount = $_POST['refund_amount'];
    $refundReason = $_POST['refund_reason'];
    $inquiryId = $_POST['inquiryid'];
    $downpayment = $_POST['downpayment'];
    $userid = $_POST['userid'];
    $paymentcode = $_POST['paymentcode']; // Retrieve payment code

    // Calculate the deducted amount (15%)
    $deductedAmount = $refundAmount * 0.15;
    // Calculate the final refund amount
    $finalRefundAmount = $refundAmount - $deductedAmount;

    // Get current date and time
    $createdAt = date('Y-m-d H:i:s');

    // Insert refund details into the database
    $insertQuery = "INSERT INTO refund (inquiryid, downpayment, userid, name, amount, reason, created_at, paymentcode)
                    VALUES ('$inquiryId', '$downpayment', '$userid', '$name', '$finalRefundAmount', '$refundReason', '$createdAt', '$paymentcode')";

    // Execute the insert query
    if ($conn->query($insertQuery) === TRUE) {
        // Redirect to a success page or display a success message
        echo '<script>alert("Refund request submitted successfully!"); window.location.href = "bookingcart.php";</script>';
    } else {
        // Redirect to an error page or display an error message
        echo '<script>alert("Error: ' . $conn->error . '"); window.location.href = "error.php";</script>';
    }
}
?>
