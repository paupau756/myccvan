<?php
// Include the connection file
include 'admin/connection.php';

// Start the session
session_start();

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
    $row = $result->fetch_assoc();
    $unread_count = $row['unread_count'];

    // Prepare and execute a query to fetch the user's name
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

} else {
    $loggedin = false;
    $username = ""; // Set username to empty if user is not logged in
    $unread_count = 0; // Set unread count to 0 if user is not logged in
}
?>
<!-- header start here -->
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
<!-- header ends -->






<?php
// Include PHPMailer Autoload
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include your database connection code here if not already included
include("admin/connection.php");

// Start the session
// session_start();

// Function to calculate the number of days between two dates
function calculateDays($start, $end) {
    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $interval = $startDate->diff($endDate);
    return $interval->days + 1; // Add 1 to include both start and end dates
}

// Function to check if there is a duplicate booking for the same user ID and date range
function checkDuplicateBooking($userid, $datestart, $dateend, $conn) {
    $query = "SELECT * FROM inquiries WHERE userid = '$userid' AND ((datestart <= '$datestart' AND dateend >= '$datestart') OR (datestart <= '$dateend' AND dateend >= '$dateend'))";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0; // Returns true if there is a duplicate booking
}

// Function to check if the maximum number of bookings (5) has been reached by different user IDs on the same date
function checkMaxBookingsReached($date, $conn) {
    // SQL query to count the number of bookings for the given date
    $query = "SELECT COUNT(DISTINCT userid) AS num_bookings FROM inquiries WHERE datestart <= '$date' AND dateend >= '$date'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $numBookings = $row['num_bookings'];
        return $numBookings >= 5; // Return true if the maximum bookings limit is reached
    }
    return false;
}

// Function to send email notification
function sendEmailNotification($userid, $pickup, $destination, $conn) {
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
    $mail->setFrom('djcaaaaastillo@gmail.com', 'New All-Out Inquiry'); // Your Gmail address and your name
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

// Function to insert notification into notifyadmin table
function insertNotification($message, $status, $created_at, $conn) {
    $query = "INSERT INTO notifyadmin (message, status, created) VALUES ('$message', '$status', '$created_at')";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        // Handle error if the query fails
        echo "Error inserting notification: " . mysqli_error($conn);
    }
}

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    // Redirect to the login page or handle as needed
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data including the userid from the session
    $userid = $_SESSION['userid'];

    // Retrieve form data
    $pickup = $_POST["pickup"];
    $destination = $_POST["destination"];
    $vehicleid = $_POST["vehicleid"];
    $datestart = $_POST["datestart"];
    $timestart = $_POST["timestart"];
    $dateend = $_POST["dateend"];
    $timeend = $_POST["timeend"];
    $note = $_POST["note"];
    $status = "Pending"; // Default status
    $created_at = date("Y-m-d H:i:s"); // Current timestamp
    $agreement = isset($_POST["agreement"]) ? 1 : 0; // Check if the agreement checkbox is checked

    // Check if there is a duplicate booking
    $duplicateBooking = checkDuplicateBooking($userid, $datestart, $dateend, $conn);

    if ($duplicateBooking) {
        // Display a JavaScript alert if there's a duplicate booking
        echo '<script>alert("You already have a booking within the selected date range."); history.back();</script>';
        exit(); // Stop further execution
    }

    // Check if the maximum bookings limit is reached
    if (checkMaxBookingsReached($datestart, $conn)) {
        // Display a JavaScript alert if the maximum bookings limit is reached
        echo '<script>alert("Maximum number of bookings for this date range has been reached."); history.back();</script>';
        exit(); // Stop further execution
    }

    // Fetch the price of the selected vehicle from the database
    $priceQuery = "SELECT price FROM vehicles WHERE vehicleid = $vehicleid";
    $priceResult = mysqli_query($conn, $priceQuery);
    $vehiclePrice = mysqli_fetch_assoc($priceResult)['price'];

    // Calculate the number of days
    $numDays = calculateDays($datestart, $dateend);

    // Calculate the total amount and downpayment
    $totalamount = $vehiclePrice * $numDays;
    // $downpayment = $totalamount * 0.25;

    // Insert data into the inquiries table (Assuming you have a database connection)
    $query = "INSERT INTO inquiries (userid, pickup, destination, vehicleid, datestart, timestart, dateend, timeend, note, status, created_at, downpayment, totalamount, agreement) VALUES ('$userid', '$pickup', '$destination', '$vehicleid', '$datestart', '$timestart', '$dateend', '$timeend', '$note', '$status', '$created_at', '$downpayment', '$totalamount', '$agreement')";

    // Execute the query (Assuming you have a database connection)
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if ($result) {
        // Send email notification
        sendEmailNotification($userid, $pickup, $destination, $conn);

        // Insert notification into notifyadmin table
        $message = "New booking inquiry from user ID: $userid";
        $status = "Unread";
        $created_at = date("Y-m-d H:i:s");
        insertNotification($message, $status, $created_at, $conn);

        // Redirect to success.php
        header("Location: success.php");
        exit(); // Ensure that no other content is sent after the header
    } else {
        // Display a JavaScript alert if there's an error
        echo '<script>alert("Error submitting inquiry: ' . mysqli_error($conn) . '"); window.location.href = "form.php";</script>';
    }

    // Close your database connection if necessary
    mysqli_close($conn);
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <!-- Online CDN for Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="cdn2.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
</head>
<body>

    

    <h2 class="form-title">Inquiry Form</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="inquiry-form">
        <label for="pickup">Pickup:</label>
        <input type="text" id="pickup" name="pickup" required autocomplete="off" placeholder="Enter your Location Around Central Luzon Only" class="form-input">
        <div id="pickup-suggestions" class="suggestions"></div>

        <label for="destination">Destination:</label>
        <input type="text" id="destination" name="destination" required autocomplete="off" placeholder="Enter your Location Around Central Luzon Only" class="form-input">
        <div id="destination-suggestions" class="suggestions"></div>

        <label for="vehicleid">Select Vehicle:</label>
        <select name="vehicleid" required class="form-select">
            <!-- Populate options dynamically from the database -->
            <?php
            // Query to fetch vehicles from the database
            $vehicleQuery = "SELECT vehicleid, vehiclename, vehicletype, max_seats FROM vehicles";
            $vehicleResult = mysqli_query($conn, $vehicleQuery);
            if ($vehicleResult && mysqli_num_rows($vehicleResult) > 0) {
                while ($row = mysqli_fetch_assoc($vehicleResult)) {
                    // Display vehicle name, type, and max seats in the option text
                    echo "<option value='{$row['vehicleid']}'>{$row['vehicletype']} (Model/ Brand: {$row['vehiclename']}, Max Seats: {$row['max_seats']})</option>";
                }
            }
            ?>
        </select><br><br>


        <div class="grid-container">
            <div class="grid-item">
                <label for="datestart">Start Date:</label>
                <input type="date" name="datestart" min="<?php echo date('Y-m-d', strtotime('+3 days')); ?>" required class="form-input">
            </div>
            <div class="grid-item">
                <label for="timestart">Start Time:</label>
                <input type="time" name="timestart" id="timestart" required class="form-input">
            </div>
            <div class="grid-item">
                <label for="dateend">End Date:</label>
                <input type="date" name="dateend" min="<?php echo date('Y-m-d', strtotime('+4 days')); ?>" required class="form-input">
            </div>
            <div class="grid-item">
                <label for="timeend">End Time:</label>
                <input type="time" name="timeend" id="timeend" required class="form-input">
            </div>
        </div>

        <label for="note">Additional Notes:</label><br>
        <textarea name="note" placeholder="Landmarks, Other Contacts, etc." class="form-textarea"></textarea><br><br>


        <label for="agreement">
            <input type="checkbox" name="agreement" id="agreement" required="" class="form-checkbox">
            I agree to pay the downpayment.
        </label><br><br>

        <label><i style="color: green;"> Note: This is an All-Out Travel you should shoulder all the fees, we only charge our base rate then you can go all the places you want. </i></label><br><br>

        <input type="submit" value="Submit Inquiry" class="form-submit">
    </form>


     <?php include 'footer.php';?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Autocomplete for Pickup
            var pickupInput = document.getElementById('pickup');
            var pickupSuggestionsContainer = document.getElementById('pickup-suggestions');
            var pickupTimer;

            pickupInput.addEventListener('input', function () {
                clearTimeout(pickupTimer); // Clear previous timer

                var query = pickupInput.value;

                if (query.length > 2) {
                    pickupTimer = setTimeout(function () {
                        // Specify the bounding box coordinates for Luzon, Philippines
                        var luzonBoundingBox = '117.174274,12.000007,126.537423,19.915233';

                        // Make a request to Nominatim API with country code for the Philippines and Luzon bounding box
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
                    }, 300); // Delay of 300 milliseconds
                } else {
                    pickupSuggestionsContainer.innerHTML = '';
                }
            });

            document.addEventListener('click', function (event) {
                if (!pickupInput.contains(event.target) && !pickupSuggestionsContainer.contains(event.target)) {
                    pickupSuggestionsContainer.innerHTML = '';
                }
            });

            // Autocomplete for Destination
            var destinationInput = document.getElementById('destination');
            var destinationSuggestionsContainer = document.getElementById('destination-suggestions');
            var destinationTimer;

            destinationInput.addEventListener('input', function () {
                clearTimeout(destinationTimer); // Clear previous timer

                var query = destinationInput.value;

                if (query.length > 2) {
                    destinationTimer = setTimeout(function () {
                        // Specify the bounding box coordinates for Luzon, Philippines
                        var luzonBoundingBox = '117.174274,12.000007,126.537423,19.915233';

                        // Make a request to Nominatim API with country code for the Philippines and Luzon bounding box
                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&countrycodes=PH&bounded=1&viewbox=${luzonBoundingBox}`)
                            .then(response => response.json())
                            .then(data => {
                                destinationSuggestionsContainer.innerHTML = '';

                                data.forEach(place => {
                                    var suggestion = document.createElement('div');
                                    suggestion.textContent = place.display_name;
                                    suggestion.classList.add('suggestion');

                                    suggestion.addEventListener('click', function () {
                                        destinationInput.value = place.display_name;
                                        destinationSuggestionsContainer.innerHTML = '';
                                    });

                                    destinationSuggestionsContainer.appendChild(suggestion);
                                });
                            })
                            .catch(error => console.error('Error fetching suggestions:', error));
                    }, 300); // Delay of 300 milliseconds
                } else {
                    destinationSuggestionsContainer.innerHTML = '';
                }
            });

            document.addEventListener('click', function (event) {
                if (!destinationInput.contains(event.target) && !destinationSuggestionsContainer.contains(event.target)) {
                    destinationSuggestionsContainer.innerHTML = '';
                }
            });
        });
    </script>


    <script>
        // Add event listener to timestart input
        document.getElementById('timestart').addEventListener('change', function() {
            // Get the value of timestart
            var startTime = this.value;

            // Set the value of timeend to be equal to timestart
            document.getElementById('timeend').value = startTime;
        });
    </script>

    <script>
        // Add event listener to date inputs
        document.addEventListener('DOMContentLoaded', function () {
            var startDateInput = document.querySelector('input[name="datestart"]');
            var endDateInput = document.querySelector('input[name="dateend"]');

            startDateInput.addEventListener('change', function () {
                // Get the selected start date
                var startDate = new Date(startDateInput.value);
                // Get the selected end date
                var endDate = new Date(endDateInput.value);

                // Check if the end date is before or equal to the start date
                if (endDate <= startDate) {
                    // Set the end date to the next day after the start date
                    var nextDay = new Date(startDate);
                    nextDay.setDate(startDate.getDate() + 1);
                    // Update the end date input value
                    endDateInput.value = nextDay.toISOString().split('T')[0];
                }
            });

            endDateInput.addEventListener('change', function () {
                // Get the selected start date
                var startDate = new Date(startDateInput.value);
                // Get the selected end date
                var endDate = new Date(endDateInput.value);

                // Check if the end date is before or equal to the start date
                if (endDate <= startDate) {
                    // Set the end date to the next day after the start date
                    var nextDay = new Date(startDate);
                    nextDay.setDate(startDate.getDate() + 1);
                    // Update the end date input value
                    endDateInput.value = nextDay.toISOString().split('T')[0];
                }
            });
        });
    </script>
</body>
</html>
