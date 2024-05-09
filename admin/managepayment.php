<?php
include("connection.php");

// Fetch specific columns from payments
$query = "SELECT `paymentid`, `inquiryid`, `userid`, `paymentmethod`, `referenceno`, `amount`, `pay_at`, `imgreceipt` FROM `payments`";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'navigation.php';?>

        <!-- Header with logout button -->
        <div class="header">
            <div class="title-container">
                <h2>Manage Payments</h2>
            </div>
        </div>

        <!-- Display all payments -->
        <table border="1">
            <tr>
                <th>Payment ID</th>
                <th>Inquiry ID</th>
                <th>User ID</th>
                <th>Payment Method</th>
                <th>Reference No</th>
                <th>Amount</th>
                <th>Payment Date</th>
                <th>Receipt Image</th>
            </tr>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['paymentid']}</td>";
                echo "<td>{$row['inquiryid']}</td>";
                echo "<td>{$row['userid']}</td>";
                echo "<td>{$row['paymentmethod']}</td>";
                echo "<td>{$row['referenceno']}</td>";
                echo "<td>{$row['amount']}</td>";
                echo "<td>" . date("F j, Y", strtotime($row['pay_at'])) . "</td>";
                echo '<td><img src="../receipts/' . $row['imgreceipt'] . '" alt="Receipt Image" style="max-width: 100px; max-height: 100px;"></td>';

                echo "</tr>";
            }
            ?>
        </table>

</body>

</html>
