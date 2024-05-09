<?php
include 'header.php';
include("admin/connection.php");

// Fetch all tours or filtered tours based on search
if(isset($_GET['search'])){
    $searchTerm = $_GET['search'];
    $query = "SELECT * FROM tours WHERE destination LIKE '%$searchTerm%'";
} else {
    $query = "SELECT * FROM tours";
}

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Tours</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
    <link rel="stylesheet" type="text/css" href="cdn2.css">
<body>


<!-- Search form -->
<div id="search-tours">
    <h2 style="color: #007bff;">Search Places</h2>
    <p>Find the perfect tour for your next adventure</p>
    <form method="GET" action="">
        <label for="search"></label>
        <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" >
        <button type="submit"><i class="fas fa-search">Search</i></button>
    </form>
</div>


<!-- Display all tours or filtered tours based on search -->
<div>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<div class='tour-carousel-container'>";
        

        // Display images with correct path
        $imagePaths = explode(",", $row['tourimages']);

        // Check if there are images for the tour
        if (!empty($imagePaths)) {
            echo "<div class='tour-images-carousel'>";
            foreach ($imagePaths as $imagePath) {
                echo "<div><img src='admin/{$imagePath}' alt='Tour Image'></div>";
            }
            echo "</div>";
        }
        echo "<h3>{$row['destination']}</h3>";
        echo "<p>{$row['touractivities']}</p>";
        echo "<p>{$row['tourdetails']}</p>";
        echo "<p><a class='details-button' href='viewdetails.php?tourid={$row['tourid']}'>View Full Details</a></p>";

        echo "</div>";
    }
    ?>
</div>
<!-- Back to Top button -->
<button id="back-to-top-btn" title="Go to top"><i class="fas fa-arrow-up"></i></button>


<?php include 'footer.php'; ?>

<!-- Slick Carousel Script -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
    $(document).ready(function(){
        $('.tour-images-carousel').slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            adaptiveHeight: true,
            autoplay: true, // Autoplay the carousel
            autoplaySpeed: 2000, // Set autoplay speed to 2 seconds
            pauseOnHover: true, // Pause autoplay on hover
            draggable: true // Enable drag functionality
        });
    });
</script>
</body>
</html>
