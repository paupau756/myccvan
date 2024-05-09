<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <!-- Online CDN for Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="cdn.css">
    <link rel="icon" type="image/jpg" href="admin/uploads/mycc.jpg">
    <title>Booking Success</title>
</head>
<body>

<?php include 'header.php'; ?>

<div class="success-container">
    <!-- PNG Sticker -->
    <img src="admin/uploads/hooli.jpg" alt="Sticker" class="sticker">

    <center><h2>Booking Success</h2></center>

    <p>Your booking has been successfully submitted. Please wait for the admin to confirm your booking.</p>

    <!-- Additional content or instructions can be added here -->

    <div class="buttons-container">
        <a href="index.php" class="button">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <a href="bookingcart.php" class="button">
            Go to Cart <i class="fas fa-shopping-cart"></i>
        </a>
    </div>
</div>

<br><br><br>
<?php include 'footer.php';?>

</body>
</html>
