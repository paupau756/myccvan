    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage Drivers</title>
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
                    <h2>Manage Drivers</h2> <a href="createdriver.php" class="create-link"><i class="fa fa-plus-circle" aria-hidden="true"></i>Create Drivers</a>
            </div>
            <div>
                <!-- Search form -->
                    <form method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search for Drivers..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit">Search</button>
                    </form>
            </div>
        </div>

        <table border="1">
            <tr>
                <th>Driver ID</th>
                <th>Name</th>
                <th>Driver License</th>
                <th>Contact</th>
                <th>Information</th> <!-- Added table header for Information -->
                <th>Action</th>
            </tr>
            <?php
            // Include the database connection file
            include("connection.php");

            // Define the search term variable
            $searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

            // Fetch drivers from the database based on search term
            if (!empty($searchTerm)) {
                $query = "SELECT * FROM drivers WHERE name LIKE '%$searchTerm%' OR driverlicense LIKE '%$searchTerm%' OR contact LIKE '%$searchTerm%'";
            } else {
                $query = "SELECT * FROM drivers";
            }

            $result = $conn->query($query);

            // Check if there are any drivers
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['driverid']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['driverlicense']; ?></td>
                        <td><?php echo $row['contact']; ?></td>
                        <td><?php echo $row['information']; ?></td> <!-- Displaying the Information -->
                        <td>
                            <a href="updatedriver.php?driverid=<?php echo $row['driverid']; ?>" style="background-color: #4285f4; color: white; padding: 5px 10px; text-decoration: none; margin-right: 10px;">Update</a>
                            <br><br>
                            <a href="deletedriver.php?driverid=<?php echo $row['driverid']; ?>" style="background-color: #ff0000; color: white; padding: 5px 10px; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this driver?')">Delete</a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                // If no drivers found
                echo "<tr><td colspan='6'>No drivers found.</td></tr>";
            }

            // Close the database connection
            $conn->close();
            ?>
        </table>


        <?php include 'adfooter.php'; ?>
        
    </body>
    </html>

