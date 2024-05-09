<?php
// Include the necessary files
include("connection.php");

// Define the search term variable
$searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Query to fetch vehicles from the database based on the search term
$query = "SELECT * FROM vehicles";
if (!empty($searchTerm)) {
    $query .= " WHERE vehicletype LIKE '%$searchTerm%' OR vehiclename LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%'";
}

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    // Include the necessary files
    include("connection.php");

    // Query to fetch vehicles from the database
    $query = "SELECT * FROM vehicles";
    $result = $conn->query($query);
    ?>

    <?php include 'head.php';?>

    <div class="headersearch">
        <!-- Header -->
        <div class="header">    
                <h2>Manage Vehicles</h2> <a href="createvehicle.php" class="create-link"><i class="fa fa-plus-circle" aria-hidden="true"></i>Create Vehicles</a>
        </div>
        <div>
            <!-- Search form -->
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search for Vehicles..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                </form>
        </div>
    </div>

    <!-- Display vehicles in a table -->
    <table>
        <thead>
            <tr>
                <th>Vehicle ID</th>
                <th>Vehicle Type</th>
                <th>Vehicle Name</th>
                <th>Description</th>
                <th>Max Seats</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['vehicleid']; ?></td>
                        <td><?php echo ucfirst($row['vehicletype']); ?></td>
                        <td><?php echo $row['vehiclename']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['max_seats']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td>
                            <a href="updatevehicle.php?vehicleid=<?php echo $row['vehicleid']; ?>" style="background-color: green; /* Green */
                              border: none;
                              color: white;
                              padding: 5px 10px;
                              text-align: center;
                              text-decoration: none;
                              display: inline-block;
                              font-size: 16px;
                              margin-right: 5px;">Update</a>
                            <a href="deletevehicle.php?vehicleid=<?php echo $row['vehicleid']; ?>" onclick="return confirmDelete();" style="background-color: red; /* Red */
                              border: none;
                              color: white;
                              padding: 5px 10px;
                              text-align: center;
                              text-decoration: none;
                              display: inline-block;
                              font-size: 16px;">Delete</a>
                            </a>
                        </td>
                    </tr>
                <?php } 
            } else {
                // Display message inside the table if no vehicles found
                echo "<tr><td colspan='7'>No vehicles found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- JavaScript function for confirming delete action -->
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this vehicle?");
        }
    </script>
</body>
</html>
