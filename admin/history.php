<?php
// Include the database connection
include "connection.php";

// Fetch completed inquiries with user details from the database, sorted by dateend in descending order
$query = "SELECT inquiries.*, users.userid, users.name, users.address, users.email, users.contact 
          FROM inquiries 
          JOIN users ON inquiries.userid = users.userid 
          WHERE inquiries.status = 'Completed'
          ORDER BY inquiries.dateend DESC";
$result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries History</title>
    <!-- Include any CSS files or stylesheets here -->
    <link rel="stylesheet" href="style.css">
    
</head>
<body>

    <?php include 'head.php';?>
    
    <div class="headerss">
        <h2>Completed Inquiries </h2>
    </div>



    <div class="inquiries">
        <!-- <h3>Completed Inquiries</h3> -->
        <table border="1">
            <thead>
                <tr>
                    <th>Inquiry ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Pickup</th>
                    <th>Destination</th>
                    <th>Status</th>
                    <th>Action</th> <!-- Add this column for the review modal button -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are completed inquiries
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["inquiryid"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["contact"] . "</td>";
                        echo "<td>" . $row["pickup"] . "</td>";
                        echo "<td>" . $row["destination"] . "</td>";
                        echo "<td>" . $row["status"] . "</td>";
                        echo "<td><a class='review-link' href='review.php?inquiryid={$row['inquiryid']}'>Review</a></td>";
                        // Link to review.php with inquiryid parameter
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No completed inquiries found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
<?php include 'adfooter.php'; ?>

</body>
</html>
