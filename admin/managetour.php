<?php
include("connection.php");

// Define default search value
$search = "";

// Check if search query is provided
if (isset($_GET['search'])) {
    // Sanitize the search query to prevent SQL injection
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Fetch tours based on search query
$query = "SELECT * FROM tours WHERE destination LIKE '%$search%' OR touractivities LIKE '%$search%' OR tourinclusions LIKE '%$search%' OR tourprice LIKE '%$search%'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages</title>
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
        <div class="header">    
                <h2>Manage Packages</h2> <a href="createtour.php" class="create-link"><i class="fa fa-plus-circle" aria-hidden="true"></i>Create Packages</a>
        </div>
        <div>
            <!-- Search form -->
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search for packages..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                </form>
        </div>
    </div>
    

    <!-- Display all tours -->
    <table border="1">
        <thead>
            <tr>
                <th>Tour ID</th>
                <th>Destination</th>
                <th>Tour Activities</th>
                <th>Tour Inclusions</th>
                <th>Tour Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['tourid']; ?></td>
                    <td><?php echo $row['destination']; ?></td>
                    <td><?php echo $row['touractivities']; ?></td>
                    <td><?php echo $row['tourinclusions']; ?></td>
                    <td><?php echo $row['tourprice']; ?></td>
                    <td>
                        <a href='updatetour.php?tourid=<?php echo $row['tourid']; ?>' style='background-color: #4285f4; color: white; padding: 5px 10px; text-decoration: none; margin-right: 10px;'>Update</a><br><br> 
                        <a href="deletetour.php?tourid=<?php echo $row['tourid']; ?>" onclick="return confirm('Are you sure you want to delete this tour?');" style="background-color: #ff0000; color: white; padding: 5px 10px; text-decoration: none;">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php include'adfooter.php';?>

    <!-- Script for logout alert -->
    <script>
        function showLogoutAlert() {
            alert("You have been logged out!");
        }
    </script>

</body>
</html>
