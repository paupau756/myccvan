<?php
// Include the database connection
include "admin/connection.php";

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    // Redirect to the login page or handle as needed
    header("Location: login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $inquiryid = $_POST["inquiryid"];
    $satisfaction = $_POST["satisfaction"];
    $comment = $_POST["comment"];

    // Fetch user's name
    $userSql = "SELECT `name` FROM `users` WHERE `userid` = '{$_SESSION['userid']}'";
    $userResult = mysqli_query($conn, $userSql);
    $userRow = mysqli_fetch_assoc($userResult);
    $userName = $userRow['name'];

    // Fetch inquiry destination
    $inquirySql = "SELECT `destination` FROM `inquiries` WHERE `inquiryid` = '$inquiryid'";
    $inquiryResult = mysqli_query($conn, $inquirySql);
    $inquiryRow = mysqli_fetch_assoc($inquiryResult);
    $destination = $inquiryRow['destination'];

    // Insert feedback into the database
    $insertQuery = "INSERT INTO feedback (inquiryid, userid, name, destination, satisfaction, comment, created) 
                    VALUES ('$inquiryid', '{$_SESSION['userid']}', '$userName', '$destination', '$satisfaction', '$comment', NOW())";

    if ($conn->query($insertQuery) === TRUE) {
        // Feedback submitted successfully
        echo "<script>alert('Thank you for your feedback!'); window.location.href = 'viewbooking.php?inquiryid=$inquiryid';</script>";
        exit();
    } else {
        // Handle database insert error
        echo "Error: " . $insertQuery . "<br>" . $conn->error;
    }
}
?>
