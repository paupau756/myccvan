<?php
include "connection.php";


// Fetch administrators from the database
$query = "SELECT * FROM admin";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Administrators</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

        <?php include 'head.php';?>

    <div class="headerss">
        <h2>Manage Admins</h2>
    </div>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['adminid'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No administrators found.</td></tr>";
        }
        ?>
    </table>

    <?php include 'adfooter.php'; ?>
</body>
</html>
