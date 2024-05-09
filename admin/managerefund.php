<?php
include 'head.php';
// Start session and check admin login
// session_start();
if (!isset($_SESSION['adminid'])) {
    header("Location: adminlogin.php");
    exit();
}

// Include necessary files
include 'connection.php';

// Define the default search value
$search = "";

// Check if search query is provided
if (isset($_GET['search'])) {
    // Sanitize the search query to prevent SQL injection
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Refunds</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="headersearch">
        <!-- Header -->
        <div class="headers">    
                <h2>Manage Refunds</h2>
        </div>
        <div>
            <!-- Search form -->
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search for Refunds..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                </form>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Refund ID</th>
                <th>Inquiry ID</th>
                <th>User ID</th>
                <th>Name</th> <!-- Replaced user_name with name -->
                <th>Downpayment</th>
                <th>Refund Amount</th>
                <th>Reason</th>
                <th>Payment Code</th> <!-- New column for Payment Code -->
                <th>Created At</th>
                <th>Action</th> <!-- New column for the action button -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Retrieve refund requests based on search query
            $query = "SELECT refund.*, users.name FROM refund INNER JOIN users ON refund.userid = users.userid INNER JOIN inquiries ON refund.inquiryid = inquiries.inquiryid WHERE (refund.refundid LIKE '%$search%' OR refund.inquiryid LIKE '%$search%' OR refund.userid LIKE '%$search%' OR refund.downpayment LIKE '%$search%' OR refund.amount LIKE '%$search%' OR refund.reason LIKE '%$search%' OR refund.paymentcode LIKE '%$search%') AND inquiries.status <> 'cancelled' ORDER BY refund.refundid DESC";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['refundid'] . "</td>";
                    echo "<td>" . $row['inquiryid'] . "</td>";
                    echo "<td>" . $row['userid'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>"; // Display User Name
                    echo "<td>" . $row['downpayment'] . "</td>";
                    echo "<td>" . $row['amount'] . "</td>";
                    echo "<td>" . $row['reason'] . "</td>";
                    echo "<td>" . $row['paymentcode'] . "</td>"; // Display Payment Code
                    echo "<td>" . date('F j, Y | g:i A', strtotime($row['created_at'])) . "</td>";

                    // Action buttons
                    echo "<td>";
                    echo "<a href='refundview.php?inquiryid=" . $row['inquiryid'] . "' class='action-button3'>View</a>";
                    echo "&nbsp;"; // Non-breaking space for spacing between buttons
                    echo "<a href='refundconfirm.php?inquiryid=" . $row['inquiryid'] . "' onclick='return confirm(\"Are you sure you want to confirm this refund?\");' class='action-button3'>Confirm Refund</a>";
                    // Action button for refund confirmation
                    echo "</td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No refund requests found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php include 'adfooter.php';?>

</body>
</html>
