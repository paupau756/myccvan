<?php
// Include your database connection code here if not already included
include("connection.php");

// Function to format date nicely
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Function to display multiple images
function displayImages($images) {
    $imagePaths = explode(',', $images);

    foreach ($imagePaths as $imagePath) {
        echo '<img src="' . $imagePath . '" alt="Announcement Image" style="max-width: 100px; max-height: 100px; margin-right: 10px;">';
    }
}

// Retrieve announcements from the database
$query = "SELECT * FROM announcements";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/jpg" href="uploads/mycc.jpg">
</head>
<body>

        <?php include 'head.php';?>
        
    <div class="headersearch">
        <!-- Header -->
        <div class="header">    
                <h2>Manage Announcements</h2> <a href="createannouncement.php" class="create-link"><i class="fa fa-plus-circle" aria-hidden="true"></i>Create Announcements</a>
        </div>
        <div>
            <!-- Search form -->
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search for Announcements..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                </form>
        </div>
    </div>
       

        <table border="1">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date Range</th>
                    <th>Context</th>
                    <th>Images</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display announcements
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['title'] . '</td>';
                        echo '<td>' . formatDate($row['datestart']) . ' to ' . formatDate($row['dateend']) . '</td>';
                        echo '<td>' . $row['context'] . '</td>';
                        echo '<td>';
                        displayImages($row['image']);
                        echo '</td>';
                        echo '<td>';
                        echo "<a href='updateannouncement.php?announceid=" . $row['announceid'] . "' class='action-link update-link' style='background-color: #4285f4; color: white; padding: 5px 10px; text-decoration: none; margin-right: 10px;'>Update</a>";
                        echo "<a href='deleteannouncement.php?announceid=" . $row['announceid'] . "' class='action-link delete-link' style='background-color: #ff0000; color: white; padding: 5px 10px; text-decoration: none;' onclick='return confirmDelete();'>Delete</a>";
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr>';
                    echo '<td colspan="5" style="text-align: center;">No announcements found.</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>



            <script>
            function confirmDelete() {
                return confirm("Are you sure you want to delete this announcement?");
            }
            </script>



<?php include 'adfooter.php'; ?>
</body>
</html>
