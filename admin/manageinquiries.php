<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <?php include 'head.php';?>

<div class="headersearch">
    <div class="header">
        <h2>Manage Inquiries</h2><a href="history.php" class="create-link"><i class="fa fa-history" aria-hidden="true"></i>History</a>
    </div>
    <div>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search for inquiries..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Search</button>
        </form>
    </div>
</div>
    

    <table class="table table-bordered" style="width: 96%;">
        <thead>
            <tr>
                <th>Inquiry ID</th>
                <!-- <th>User ID</th> -->
                <th>Name</th>
                <th>Pickup</th>
                <th>Destination</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Include the database connection
            include "connection.php";

            // Define the default search value
            $search = "";

            // Check if search query is provided
            if (isset($_GET['search'])) {
                // Sanitize the search query to prevent SQL injection
                $search = mysqli_real_escape_string($conn, $_GET['search']);
            }

            // Fetch inquiries from the database based on search query
            $query = "SELECT inquiries.inquiryid, inquiries.userid, users.name, inquiries.pickup, inquiries.destination, inquiries.status, inquiries.dateend, inquiries.timeend
                      FROM inquiries 
                      INNER JOIN users ON inquiries.userid = users.userid
                      WHERE users.name LIKE '%$search%' OR inquiries.pickup LIKE '%$search%' OR inquiries.destination LIKE '%$search%' OR inquiries.status LIKE '%$search%'";

            $result = mysqli_query($conn, $query);

            // Check if there are inquiries
            if ($result && mysqli_num_rows($result) > 0) {
                // Loop through each inquiry and display details
                while ($row = mysqli_fetch_assoc($result)) {
                    // Exclude inquiries with status "Completed" or "Cancelled"
                    if ($row['status'] !== 'Completed' && $row['status'] !== 'Cancelled') {
                        echo "<tr>";
                        echo "<td>{$row['inquiryid']}</td>";
                        // echo "<td>{$row['userid']}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['pickup']}</td>";
                        echo "<td>{$row['destination']}</td>";
                        echo "<td>{$row['status']}</td>";
                        echo "<td>";

                        // Check the status and display action buttons accordingly
                        if ($row['status'] === 'Confirmed') {
                            // Check if the current date and time have passed the dateend and timeend of the booking
                            $currentDateTime = date("Y-m-d H:i:s");
                            if ($currentDateTime > $row['dateend'] . ' ' . $row['timeend']) {
                                echo "<a href='autocompletedbooking.php?inquiryid={$row['inquiryid']}' class='btn btn-warning' onclick='alert(\"Booking completed successfully.\")'>Completed</a>";
                            }
                            echo "&nbsp;"; // Add a space as a gap
                            echo "<a href='viewbooking.php?inquiryid={$row['inquiryid']}' class='btn btn-primary'>View</a>";
                            } elseif ($row['status'] === 'Pending') {
                            // Display "View", "Confirm", and "Cancel" buttons
                            echo "<a href='viewbooking.php?inquiryid={$row['inquiryid']}' class='btn btn-primary'>View</a>";
                            echo "&nbsp;"; // Add a space as a gap
                            echo "<a href='confirmbooking.php?inquiryid={$row['inquiryid']}' class='btn btn-success'>Confirm</a>";
                            echo "&nbsp;"; // Add a space as a gap
                            echo "<a href='cancelbooking.php?inquiryid={$row['inquiryid']}' class='btn btn-danger'>Cancel</a>";
                        } elseif ($row['status'] === 'Confirmed') {
                            // Display "View" and "Cancel" buttons
                            echo "<a href='viewbooking.php?inquiryid={$row['inquiryid']}' class='btn btn-primary'>View</a>";
                            echo "&nbsp;"; // Add a space as a gap
                            echo "<a href='cancelbooking.php?inquiryid={$row['inquiryid']}' class='btn btn-danger'>Cancel</a>";
                        } elseif ($row['status'] === 'Paid') {
                            // Display "View" and "Cancel" buttons
                            echo "<a href='viewbooking.php?inquiryid={$row['inquiryid']}' class='btn btn-primary'>View</a>";
                            // echo "<a href='cancelbooking.php?inquiryid={$row['inquiryid']}' class='btn btn-danger'>Cancel</a>";
                        }


                        echo "</td>";
                        echo "</tr>";
                    }
                }
            } else {
                // Display "No inquiries found." message inside the table
                echo "<tr><td colspan='7'>No inquiries found.</td></tr>";
            }
            ?>

        </tbody>
    </table>

   
    <?php include 'adfooter.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
