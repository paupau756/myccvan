<?php
// Start session and include connection
session_start();
include "admin/connection.php";

// Check if inquiry ID is provided in the URL
if (!isset($_GET['id'])) {
    // Redirect or handle missing ID
    header("Location: bookingcart.php");
    exit();
}

// Fetch inquiry details based on inquiry ID
$inquiryid = $_GET['id'];
$query = "SELECT * FROM inquiries WHERE inquiryid = $inquiryid";
$result = $conn->query($query);

// If inquiry found, proceed with cancellation
if ($result->num_rows > 0) {
    // Perform cancellation process here (e.g., update status in the database)
    $updateQuery = "UPDATE inquiries SET status = 'Cancelled' WHERE inquiryid = $inquiryid";
    $conn->query($updateQuery);

    // Display alert and redirect to booking cart page
    echo "<script>alert('Booking canceled successfully.'); window.location.href = 'bookingcart.php';</script>";
    exit();
} else {
    // Handle if inquiry not found
    echo "<script>alert('Booking not found.'); window.location.href = 'bookingcart.php';</script>";
    exit();
}
?>
