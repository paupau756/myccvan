<?php
// Include the connection file
include 'admin/connection.php';

// Start the session
session_start();

// Initialize unread count
$unread_count = 0;

// Check if the user is logged in
if(isset($_SESSION['userid'])) {
    $loggedin = true;

    // Get the logged-in user's ID
    $userid = $_SESSION['userid'];

    // Query to fetch the count of unread notifications for the logged-in user
    $query = "SELECT COUNT(*) AS unread_count FROM notify WHERE userid = ? AND status = 'unread'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the unread notification count
    if ($row = $result->fetch_assoc()) {
        $unread_count = $row['unread_count'];
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<?php
// Include your database connection code here if not already included
include("admin/connection.php");

// Include PHPMailer Autoload
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Start the session
// session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    // Redirect to the login page or handle as needed
    header("Location: login.php");
    exit();
}

// Set $userid based on the session
$userid = $_SESSION['userid'];

// Fetch tour details based on tourid from the URL parameter
if (!isset($_GET['tourid'])) {
    // Handle if tourid is not provided in the URL
    echo "Tour ID is missing.";
    exit();
}

$tourid = $_GET['tourid'];

// Fetch tour details based on tourid
$queryTour = "SELECT t.*, v.vehicletype, v.vehiclename, v.max_seats FROM tours t JOIN vehicles v ON t.vehicleid = v.vehicleid WHERE t.tourid = $tourid";
$resultTour = $conn->query($queryTour);

if ($resultTour->num_rows > 0) {
    $tour = $resultTour->fetch_assoc();
} else {
    // Handle if tour not found
    echo "<script>alert('Tour not found.'); window.location.href = 'tours.php';</script>";
    exit();
}

// Function to calculate the number of days between two dates
function calculateDays($start, $end) {
    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $interval = $startDate->diff($endDate);
    return $interval->days + 1; // Add 1 to include both start and end dates
}

// Function to insert notification into notifyadmin table
function insertNotification($message, $status, $created, $conn) {
    $query = "INSERT INTO notifyadmin (message, status, created) VALUES ('$message', '$status', '$created')";
    $result = $conn->query($query);
    if ($result) {
        return true; // Return true if insertion is successful
    } else {
        return false; // Return false if insertion fails
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $pickup = $_POST["pickup"];
    $datestart = $_POST["datestart"];
    $timestart = $_POST["timestart"];
    $dateend = $_POST["dateend"];
    $timeend = $_POST["timeend"];
    $note = $_POST["note"];
    $agreement = isset($_POST["agreement"]) ? 1 : 0; // Check if the agreement checkbox is checked

    // Calculate the number of days
    $numDays = calculateDays($datestart, $dateend);

    // Assign the total amount directly
    $totalamount = $tour['tourprice'];

    // Validate if there is already a booking for the user within the specified date range
    $queryValidation = "SELECT * FROM inquiries WHERE userid = '$userid' AND ((datestart <= '$datestart' AND dateend >= '$datestart') OR (datestart <= '$dateend' AND dateend >= '$dateend'))";
    $resultValidation = $conn->query($queryValidation);

    if ($resultValidation->num_rows > 0) {
        // Display an error message if there is already a booking within the specified date range
        echo '<script>alert("You already have a booking within the specified date range.");</script>';
        // Redirect to the previous page
        echo '<script>window.history.back();</script>';

        exit();
    }

    // Check if the maximum number of bookings for the specified date range has been reached
    $queryMaxBookings = "SELECT COUNT(DISTINCT userid) AS numBookings FROM inquiries WHERE datestart = '$datestart' AND dateend = '$dateend'";
    $resultMaxBookings = $conn->query($queryMaxBookings);

    if ($resultMaxBookings->num_rows > 0) {
        $rowMaxBookings = $resultMaxBookings->fetch_assoc();
        $numBookings = $rowMaxBookings['numBookings'];

        // Check if the maximum number of bookings (5) has been reached
        if ($numBookings >= 5) {
            // Display an error message if the maximum number of bookings has been reached
            echo '<script>alert("The maximum number of bookings for this date range has been reached.");</script>';
            // Redirect to the previous page
            echo '<script>window.history.back();</script>';

            exit();
        }
    }

    // Insert data into the inquiries table
    $query = "INSERT INTO inquiries (userid, pickup, tourid, destination, vehicleid, datestart, timestart, dateend, timeend, note, status, created_at, downpayment, totalamount, agreement) 
              VALUES ('$userid', '$pickup', '$tourid', '{$tour['destination']}', '{$tour['vehicleid']}', '$datestart', '$timestart', '$dateend', '$timeend', '$note', 'Pending', NOW(), '$downpayment', '$totalamount', '$agreement')";

    // Execute the query
    if ($conn->query($query) === TRUE) {
        // Send email notification
        sendEmailNotification($userid, $pickup, $tour['destination']);

        // Insert notification into notifyadmin table
        $message = "New booking inquiry received for tour: {$tour['destination']}";
        $status = "Unread";
        $created = date("Y-m-d H:i:s");
        if (insertNotification($message, $status, $created, $conn)) {
            // Notification insertion successful
            // Redirect to success.php upon successful submission
            header("Location: success.php");
            exit(); // Ensure that no other content is sent after the header
        } else {
            // Display an error message if notification insertion fails
            echo "Error: Notification insertion failed.";
        }
    } else {
        // Display an error message if the query fails
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

// Function to send email notification
function sendEmailNotification($userid, $pickup, $destination) {
    // Instantiate PHPMailer
    $mail = new PHPMailer();

    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'djcaaaaastillo@gmail.com'; // Your Gmail address
    $mail->Password = 'xpxi giba lyva fxwa'; // Your Gmail password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587; // Gmail SMTP port

    // Sender and recipient settings
    $mail->setFrom('djcaaaaastillo@gmail.com', 'New All-In Inquiry'); // Your Gmail address and your name
    $mail->addAddress('djcaaaaastillo@gmail.com'); // Recipient email

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'New Inquiry';
    $mail->Body = "New inquiry from user ID: $userid<br><br>
                   Pickup Location: $pickup<br>
                   Destination: $destination";

    // Send email
    if ($mail->send()) {
        echo "Email sent successfully";
    } else {
        echo "Email sending failed: " . $mail->ErrorInfo;
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tour</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="cdn2.css">
    <!-- Include Font Awesome CSS for icons -->
</head>
<body>


<!-- header lang ito -->
<?php
// Check if the user is logged in
if(isset($_SESSION['userid'])) {
    $loggedin = true;

    // Include the database connection file
    include 'admin/connection.php';

    // Prepare and execute a query to fetch the user's name
    $userid = $_SESSION['userid'];
    $query = "SELECT name FROM users WHERE userid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the query was successful and if a row was returned
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['name'];
    } else {
        // Error handling if user not found
        $username = "Unknown";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();

} else {
    $loggedin = false;
    $username = ""; // Set username to empty if user is not logged in
}
?>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">
        <img src="admin/uploads/mycc.jpg" alt="MYCC VAN RENTAL Logo" height="30" style="border-radius: 12px;" >
        MYCC VAN RENTAL
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">HOME</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tours.php">PACKAGES</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="form.php">INQUIRE</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="notification.php">NOTIFICATIONS <?php echo ($unread_count > 0) ? "<span class='badge badge-danger'>$unread_count</span>" : ""; ?></a>
            </li>
        </ul>
        <?php if($loggedin) { ?>
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <strong><?php echo $username; ?></strong>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="bookingcart.php">Booking Cart</a>
                    <a class="dropdown-item" href="bookinghistory.php">Booking History</a>
                    <a class="dropdown-item" href="settings.php">Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
        <?php } else { ?>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="login.php">LOGIN</a>
            </li>
        </ul>
        <?php } ?>
    </div>
</nav>
<!-- header end -->


<div class="aliform">
    <div class="tour-info-container">
        <h2 class="tour-title">Book this Destination: <?php echo $tour['destination']; ?></h2>
        <p class="tour-info"><strong>Duration (Days):</strong> <?php echo $tour['tourduration']; ?></p>
        <p class="tour-info"><strong>Activities:</strong> <?php echo $tour['touractivities']; ?></p>
        <br>
        <p class="tour-info"><strong>Details:</strong> <?php echo $tour['tourdetails']; ?></p>
        <br>
        <p class="tour-info"><strong>Inclusions:</strong> <?php echo $tour['tourinclusions']; ?></p>
        <br>
        <p class="tour-info"><strong>Number of Vehicle:</strong> <?php echo $tour['vehicletype']; ?></p>
        <p class="tour-info"><strong>Vehicle Brand:</strong> <?php echo $tour['vehiclename']; ?></p>
        <p class="tour-info"><strong>Seating Capacity:</strong> <?php echo $tour['max_seats']; ?></p>


        <!-- Additional note -->
        <p class="tour-note">Note: This is an All-In Packages. All other expenses is included except for Hotel accommodations are chosen and shouldered by the renters.</p>
    </div>

    <div class="form-container">
        <form method="post" action="">
            <label for="pickup">Pickup:</label>
            <input type="text" id="pickup" name="pickup" required autocomplete="off" placeholder="Enter Location Anywhere in Central Luzon">
            <div id="pickup-suggestions"></div>


            <div class="date-time-container">
                <div>
                    <label for="datestart">Start Date:</label><br>
                    <input type="date" id="datestart" name="datestart" min="<?php echo date('Y-m-d', strtotime('+3 days')); ?>" required><br><br>

                    <label for="timestart">Start Time:</label><br>
                    <input type="time" id="timestart" name="timestart" required><br><br>
                </div>
                <div>
                    <label for="dateend">End Date:</label><br>
                    <input type="date" id="dateend" name="dateend" readonly><br><br>

                    <label for="timeend">End Time:</label><br>
                    <input type="time" id="timeend" name="timeend" readonly><br><br>
                </div>
            </div>

            <label for="note">Note:</label><br>
            <textarea id="note" name="note" placeholder="Additional contact number, landmark of location"></textarea><br><br>


            <div class="payment-details">
                <div>
                    <label for="totalamount">Total Amount:</label>
                    <input type="text" id="totalamount" name="totalamount" value="₱<?php echo $tour['tourprice']; ?>" readonly>
                </div>
                <div>
                    <label for="downpayment">Minimum Downpayment:</label>
                    <input type="text" id="downpayment" name="downpayment" value="₱1000" readonly>
                </div>

            </div>


            <label for="agreement">
                <input type="checkbox" id="agreement" name="agreement" required>
                I agree to pay the downpayment.
            </label><br><br>

            <input type="submit" value="Submit Inquiry">
        </form>
    </div>
</div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var pickupInput = document.getElementById('pickup');
            var pickupSuggestionsContainer = document.getElementById('pickup-suggestions');
            var debounceTimer;

            pickupInput.addEventListener('input', function () {
                var query = pickupInput.value;

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    if (query.length > 2) {
                        var luzonBoundingBox = '117.174274,12.000007,126.537423,19.915233'; // Bounding box for Luzon, Philippines

                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&countrycodes=PH&bounded=1&viewbox=${luzonBoundingBox}`)
                            .then(response => response.json())
                            .then(data => {
                                pickupSuggestionsContainer.innerHTML = '';

                                data.forEach(place => {
                                    var suggestion = document.createElement('div');
                                    suggestion.textContent = place.display_name;
                                    suggestion.classList.add('suggestion');

                                    suggestion.addEventListener('click', function () {
                                        pickupInput.value = place.display_name;
                                        pickupSuggestionsContainer.innerHTML = '';
                                    });

                                    pickupSuggestionsContainer.appendChild(suggestion);
                                });
                            })
                            .catch(error => console.error('Error fetching suggestions:', error));
                    } else {
                        pickupSuggestionsContainer.innerHTML = '';
                    }
                }, 300); // Debounce time of 300 milliseconds
            });

            document.addEventListener('click', function (event) {
                if (!pickupInput.contains(event.target) && !pickupSuggestionsContainer.contains(event.target)) {
                    pickupSuggestionsContainer.innerHTML = '';
                }
            });
        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var datestartInput = document.getElementById('datestart');
            var timestartInput = document.getElementById('timestart');
            var dateendInput = document.getElementById('dateend');
            var timeendInput = document.getElementById('timeend');

            datestartInput.addEventListener('input', function () {
                // Get the selected start date
                var startDate = new Date(datestartInput.value);
                // Add the tour duration to the start date
                startDate.setDate(startDate.getDate() + <?php echo $tour['tourduration']; ?>);
                // Format the end date as YYYY-MM-DD
                var endDate = startDate.toISOString().split('T')[0];
                // Set the value of the readonly dateend input
                dateendInput.value = endDate;
            });

            timestartInput.addEventListener('input', function () {
                // Set the value of timeend same as timestart
                timeendInput.value = timestartInput.value;
            });
        });
    </script>

    <script>
        // JavaScript to show the dropdown menu when clicking on the user's name
        document.addEventListener("DOMContentLoaded", function() {
            var dropdownToggle = document.querySelector('.dropdown-toggle');

            dropdownToggle.addEventListener('click', function() {
                var dropdownMenu = this.nextElementSibling;
                dropdownMenu.classList.toggle('show');
            });

            // JavaScript to toggle the collapsed navbar when the toggle button is clicked
            var navbarToggler = document.querySelector('.navbar-toggler');
            var navbarCollapse = document.querySelector('.navbar-collapse');

            navbarToggler.addEventListener('click', function() {
                navbarCollapse.classList.toggle('show');
            });
        });
    </script>

</body>
</html>
