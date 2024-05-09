<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notifications</title>
    <!-- Include your CSS or other styles if needed -->
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Barlow Sans Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Sans:wght@400;500&display=swap">
    <!-- Link to your custom CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'head.php';?>

<div class="main">
    <!-- Header -->
    <div class="headerss">
            <h2> Notification</h2>
    </div>

<?php

// Include your database connection code here if not already included
include("connection.php");

// Fetch notifications for the admin
$query = "SELECT * FROM notifyadmin ORDER BY created DESC";
$result = $conn->query($query);

// Check if there are any notifications
if ($result->num_rows > 0) {
    // Display the notifications in a table
    echo '<table border="1">
            <thead>
                <tr>
                    <th>Notify ID</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['notifyid'] . '</td>';
        echo '<td>' . $row['message'] . '</td>';
        echo '<td>' . $row['status'] . '</td>';
        echo '<td>' . date("F j, Y", strtotime($row['created'])) . '</td>';

        
        echo '<td>';
        if ($row['status'] == 'read') {
            echo '<button class="action-button1" onclick="openViewModal(\'' . $row['message'] . '\')">View Message</button>';
        } else {
            echo '<button class="action-button2" onclick="openReadModal(' . $row['notifyid'] . ', \'' . $row['message'] . '\', \'' . date('F j, Y', strtotime($row['created'])) . '\')">Read</button>';
        }
        // Add remove button
        echo '<button class="action-button5" onclick="removeNotification(' . $row['notifyid'] . ')">Remove</button>';
        echo '</td>';

        echo '</tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No notifications available.</p>';
}

// Close your database connection if necessary
mysqli_close($conn);
?>

<?php include'adfooter.php';?>

<!-- View Message Modal -->
<div id="viewMessageModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('viewMessageModal')">&times;</span>
    <p id="messageContent"></p>
  </div>
</div>

<!-- Read Notification Modal -->
<div id="readNotificationModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('readNotificationModal')">&times;</span>
    <h2>Notification Details</h2>
    <p><strong>Message:</strong> <span id="readMessage"></span></p>
    <p><strong>Created At:</strong> <span id="readCreatedAt"></span></p>
  </div>
</div>

<script>
// Function to open view message modal
function openViewModal(message) {
  document.getElementById('messageContent').innerHTML = message;
  document.getElementById('viewMessageModal').style.display = "block";
}

// Function to open read notification modal
function openReadModal(notifyid, message, created) {
  // Update the modal content
  document.getElementById('readMessage').innerText = message;
  document.getElementById('readCreatedAt').innerText = created;

  // Update the notification status to "read"
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      // Do nothing on successful update
    }
  };
  xhttp.open("GET", "update_notification_status.php?notifyid=" + notifyid, true);
  xhttp.send();

  // Display the modal
  document.getElementById('readNotificationModal').style.display = "block";

  // Add event listener to detect modal close and refresh page
  document.getElementById('readNotificationModal').addEventListener('click', function(event) {
    if (event.target == document.getElementById('readNotificationModal')) {
      location.reload();
    }
  });
}

// Function to remove notification
function removeNotification(notifyid) {
  if (confirm("Are you sure you want to remove this notification?")) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        location.reload(); // Reload the page after successful removal
      }
    };
    xhttp.open("GET", "removenotif.php?notifyid=" + notifyid, true);
    xhttp.send();
  }
}

// Function to close modal and refresh page
function closeModal(modalId) {
  document.getElementById(modalId).style.display = "none";
  location.reload();
}

// Close modal when clicking outside of it
window.onclick = function(event) {
  var modals = document.getElementsByClassName('modal');
  for (var i = 0; i < modals.length; i++) {
    if (event.target == modals[i]) {
      modals[i].style.display = "none";
    }
  }
}
</script>

</body>
</html>
