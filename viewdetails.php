<?php
include("admin/connection.php");

// Check if the tourid is set in the query parameters
if (isset($_GET['tourid'])) {
    $tourid = $_GET['tourid'];

    // Retrieve tour details based on tourid
    $query = "SELECT * FROM tours WHERE tourid = $tourid";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $tour = $result->fetch_assoc();

        // Fetch vehicle details from the database based on the vehicle ID
        $vehicleid = $tour['vehicleid'];
        $vehicleQuery = "SELECT vehiclename, max_seats FROM vehicles WHERE vehicleid = $vehicleid";
        $vehicleResult = $conn->query($vehicleQuery);
        if ($vehicleResult && $vehicleResult->num_rows > 0) {
            $vehicle = $vehicleResult->fetch_assoc();
        } else {
            // Set default vehicle details if not found
            $vehicle = array('vehiclename' => 'Unknown', 'max_seats' => 'Unknown');
        }
    } else {
        // Redirect to tours.php if tour not found
        header("Location: tours.php");
        exit();
    }
} else {
    // Redirect to tours.php if tourid not set
    header("Location: tours.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="stylesheet" href="cdn2.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
</head>
<body>

<?php include 'header.php'; ?>

<!-- Destination Details Section -->
<!-- Search form -->
<div id="search-tours">
    <h2>Destination Details</h2>
    <p>Find the perfect tour for your next adventure</p>
</div>

<!-- Display tour details -->
<div class="tour-details-container">
    <?php
    $imagePaths = explode(",", $tour['tourimages']);
    if (!empty($imagePaths)) {
        echo "<div class='tour-images-carousel'>";
        foreach ($imagePaths as $imagePath) {
            echo "<a href='#' class='image-link'><img src='admin/{$imagePath}' alt='Tour Image'></a>";
        }
        echo "</div>";
    }
    ?>
    <h3><?php echo $tour['destination']; ?></h3>
    <p>Activities: <?php echo $tour['touractivities']; ?></p>
    <p>Duration (Days): <?php echo $tour['tourduration']; ?></p>
    <p>Details: <?php echo $tour['tourdetails']; ?></p>
    <p>Inclusions: <?php echo $tour['tourinclusions']; ?></p>
    <p>Vehicle Details: <?php echo $vehicle['vehiclename'] . " (Max Seats: " . $vehicle['max_seats'] . ")"; ?></p>
    <p>Price: ₱<?php echo $tour['tourprice']; ?></p>

    
    <p><a href='allinform.php?tourid=<?php echo $tour['tourid']; ?>' class="book-now-button">Book Now</a></p>
    <!-- You can link this to a booking page with the selected tourid -->
</div>

<!-- Modal -->
<div id="imageModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="modal-images-carousel"></div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
    $(document).ready(function(){
        $('.tour-images-carousel').slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            adaptiveHeight: true
        });

        // Click event for opening modal and displaying images in carousel
        $('.image-link').click(function(e) {
            e.preventDefault();
            var imgIndex = $(this).index();
            var modalImages = [];
            $('.image-link').each(function() {
                modalImages.push('<div><img src="' + $(this).find('img').attr('src') + '"></div>');
            });
            $('.modal-images-carousel').html(modalImages.join(''));
            $('.modal-images-carousel').slick({
                dots: false,
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: true,
                initialSlide: imgIndex
            });
            $('#imageModal').css('display', 'block');
        });

        // Click event for closing modal and reloading the page
        $('.close').click(function() {
            $('#imageModal').css('display', 'none');
            location.reload(); // Reload the page
        });
    });
</script>



</body>
</html>

