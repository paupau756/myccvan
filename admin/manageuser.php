<?php
include("connection.php");

// Define default search value
$search = "";

// Check if search query is provided
if (isset($_GET['search'])) {
    // Sanitize the search query to prevent SQL injection
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Fetch users based on search query
$query = "SELECT * FROM users WHERE name LIKE '%$search%' OR address LIKE '%$search%' OR email LIKE '%$search%' OR contact LIKE '%$search%' OR username LIKE '%$search%'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'head.php';?>

    <div class="headersearch">
        <!-- Header -->
        <div class="headers">    
                <h2>Manage Users</h2>
        </div>
        <div>
            <!-- Search form -->
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search for Users..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                </form>
        </div>
    </div>

    
                
    <!-- Display all users -->
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Username</th>
            <!-- <th>Profile Picture</th> -->
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['userid']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['address']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['contact']}</td>";
            echo "<td>{$row['username']}</td>";
            // echo "<td><img src='{$row['profile_picture']}' alt='Profile Picture' style='width: 50px; height: 50px;'></td>";
            echo "</tr>";
        }
        ?>
    </table>


<?php include 'adfooter.php';?>

</body>
</html>
