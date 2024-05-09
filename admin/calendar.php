<?php
// Database connection
include 'connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MYCC Calendar</title>
</head>
<body>



<?php include 'head.php';?>

<div class="headerss">     
    <h2>MYCC Calendar</h2>
</div>

<?php
function display_inquiries($start_date, $end_date) {
    global $conn;
    $sql = "SELECT inquiries.inquiryid, inquiries.userid, users.name, inquiries.datestart, inquiries.timestart, inquiries.dateend, inquiries.timeend
            FROM inquiries
            JOIN users ON inquiries.userid = users.userid
            WHERE inquiries.datestart >= '$start_date' AND inquiries.dateend <= '$end_date' AND inquiries.status = 'Confirmed'
            LIMIT 10"; // Limit the result to 10 rows
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h3 style='text-align: center;'>Scheduled Travel:</h3>";
        echo "<table>";
        echo "<tr><th>Inquiry ID</th><th>User ID</th><th>Name</th><th>Start Date & Time</th><th>End Date & Time</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["inquiryid"] . "</td>";
            echo "<td>" . $row["userid"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . date('F j, Y | h:i A', strtotime($row["datestart"] . ' ' . $row["timestart"])) . "</td>";
            echo "<td>" . date('F j, Y | h:i A', strtotime($row["dateend"] . ' ' . $row["timeend"])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No confirmed inquiries found within the specified date range.</p>";
    }
}



function generate_calendar($month, $year) {
    global $conn;
    
    $first_day = mktime(0, 0, 0, $month, 1, $year);
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $first_day_of_week = date('N', $first_day);
    
    echo "<h1 style='text-align: center;'>" . date('F Y', $first_day) . "</h1>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Mon</th>";
    echo "<th>Tue</th>";
    echo "<th>Wed</th>";
    echo "<th>Thu</th>";
    echo "<th>Fri</th>";
    echo "<th>Sat</th>";
    echo "<th>Sun</th>";
    echo "</tr>";
    
    echo "<tr>";
    $day_count = 1;
    for ($i = 1; $i < $first_day_of_week; $i++) {
        echo "<td></td>";
        $day_count++;
    }
    
    // Check each day of the month
    for ($day = 1; $day <= $days_in_month; $day++) {
        $date = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
        $sql = "SELECT COUNT(*) AS count FROM inquiries WHERE datestart <= '$date' AND dateend >= '$date' AND status = 'Confirmed'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $inquiry_count = $row['count'];
        
        // Apply CSS class if there are inquiries for this date
        $class = ($inquiry_count > 0) ? 'highlighted-date' : '';
        
        echo "<td class='$class'>$day</td>";
        $day_count++;
        
        if ($day_count > 7) {
            echo "</tr><tr>";
            $day_count = 1;
        }
    }
    
    while ($day_count > 1 && $day_count <= 7) {
        echo "<td></td>";
        $day_count++;
    }
    
    echo "</tr>";
    echo "</table>";
    
    // Previous Month and Next Month links
    echo "<a href=\"?month=" . ($month == 1 ? 12 : $month - 1) . "&year=" . ($month == 1 ? $year - 1 : $year) . "\" class=\"previous-month\">Previous Month</a>";
    echo "<a href=\"?month=" . ($month == 12 ? 1 : $month + 1) . "&year=" . ($month == 12 ? $year + 1 : $year) . "\" class=\"next-month\">Next Month</a>";
}

if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = intval($_GET['month']);
    $year = intval($_GET['year']);
} else {
    $month = date('n');
    $year = date('Y');
}

generate_calendar($month, $year);

// Display inquiries for the current month
$start_date = date('Y-m-01', strtotime("$year-$month-01"));
$end_date = date('Y-m-t', strtotime("$year-$month-01"));
display_inquiries($start_date, $end_date);

?>

<?php include 'adfooter.php';?>
</body>
</html>
