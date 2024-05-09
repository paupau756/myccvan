<?php
include 'header.php';
include 'admin/connection.php';

if (!isset($_SESSION['userid'])) {
    // Redirect or handle unauthorized access
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="cdn2.css">
</head>
<body>

<div class="booking-history">
    <h1>Booking History</h1>
</div>

<div class="booking-list">
    <table class="booking-table">
        <thead>
            <tr>
                <th>Inquiry ID</th>
                <th>User ID</th>
                <th>Destination</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch inquiries with status Completed or Cancelled
            $query = "SELECT inquiries.*, users.name AS username FROM inquiries 
                      INNER JOIN users ON inquiries.userid = users.userid 
                      WHERE inquiries.userid = $userid AND inquiries.status IN ('Completed', 'Cancelled')
                      ORDER BY inquiries.created_at DESC"; // Ordering by the created column in descending order
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Display each booking
                    echo '<tr>';
                    echo '<td>' . $row['inquiryid'] . '</td>';
                    echo '<td>' . $row['userid'] . ' (' . $row['username'] . ')' . '</td>';
                    echo '<td>' . $row['destination'] . '</td>';
                    echo '<td>' . $row['status'] . '</td>';
                    // Modified code for displaying the button
                    echo '<td><button onclick="openFeedbackModal(' . $row['inquiryid'] . ')">Leave Feedback</button></td>';

                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5">No bookings found.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal form for leaving feedback -->
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFeedbackModal()">&times;</span>
        <h2 class="modal-title">Leave Feedback</h2>
        <form action="submitfeedback.php" method="POST" class="feedback-form">
            <input type="hidden" name="inquiryid" id="inquiryidField">
            <label for="satisfaction" class="form-label">Satisfaction:</label>
            <select name="satisfaction" id="satisfaction" class="form-select">
                <option value="Verysatisfied">Very Satisfied</option>
                <option value="Satisfied">Satisfied</option>
                <option value="Neutral">Neutral</option>
                <option value="Dissatisfied">Dissatisfied</option>
                <option value="Verydissatisfied">Very Dissatisfied</option>
            </select>
            <label for="comment" class="form-label">Comment:</label>
            <textarea name="comment" id="comment" rows="4" class="form-textarea"></textarea>
            <button type="submit" class="submit-button">Submit Feedback</button>
        </form>
    </div>
</div>


<script>
    // JavaScript function to open the feedback modal
    function openFeedbackModal(inquiryid) {
        // Set the inquiryid value in the hidden input field
        document.getElementById("inquiryidField").value = inquiryid;
        // Display the modal
        document.getElementById("feedbackModal").style.display = "block";
    }

    // JavaScript function to close the feedback modal
    function closeFeedbackModal() {
        // Hide the modal
        document.getElementById("feedbackModal").style.display = "none";
    }
</script>


</body>
</html>



<?php include 'footer.php'; ?>
