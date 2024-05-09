<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    // Perform data validation if needed

    // Store form data in the database
    include("admin/connection.php"); // Include your database connection file

    $insertQuery = "INSERT INTO contact_submissions (name, email, message, submission_date)
                    VALUES ('$name', '$email', '$message', NOW())";

    if ($conn->query($insertQuery) === TRUE) {
        // Form submitted successfully
        echo "<script>alert('Form submitted successfully!'); window.location.href = 'index.php';</script>";

        // Add a notification to the notifications table
        $notificationMessage = "New contact form submission from $name ($email)";
        $insertNotificationQuery = "INSERT INTO notifications (message) VALUES ('$notificationMessage')";
        $conn->query($insertNotificationQuery);
    } else {
        // Error submitting form
        echo "<script>alert('Error submitting form: " . $conn->error . "'); window.location.href = 'index.php';</script>";
    }

    $conn->close();
} else {
    // Redirect or handle accordingly if someone tries to access this script directly
    header("Location: index.php");
    exit();
}
?>
