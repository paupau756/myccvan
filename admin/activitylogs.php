<?php
// Include the database connection file
include "connection.php";

// Fetch activity logs from the database
$query = "SELECT * FROM activitylogs ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


<style> #scrollTopButton {
  display: none;
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 99;
  font-size: 16px;
  border: none;
  outline: none;
  background-color: #333;
  color: white;
  cursor: pointer;
  padding: 15px;
  border-radius: 5px;
}

#scrollTopButton:hover {
  background-color: #555;
}
</style>
</head>
<body>

    <?php include'head.php'; ?>

    <div class="headerss">
        <h2><i class=""></i> Activity Logs</h2>
    </div>

    <table>
        <thead>
            <tr>
                <!-- <th>Activity ID</th> -->
                <th>Activities</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if there are activity logs available
            if ($result && $result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    // echo "<td>" . $row['activityid'] . "</td>";
                    echo "<td>" . $row['activities'] . "</td>";
                    echo "<td>" . date('M d, Y | h:i A', strtotime($row['created_at'])) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No activity logs found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
<button onclick="topFunction()" id="scrollTopButton" title="Go to top"><i class="fas fa-arrow-up"></i></button>

<script>
  // Get the button
  var mybutton = document.getElementById("scrollTopButton");

  // When the user scrolls down 20px from the top of the document, show the button
  window.onscroll = function() {scrollFunction()};

  function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      mybutton.style.display = "block";
    } else {
      mybutton.style.display = "none";
    }
  }

  // When the user clicks on the button, scroll to the top of the document
  function topFunction() {
    // Scroll to the top of the document with smooth scrolling
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }
</script>


</body>
</html>
