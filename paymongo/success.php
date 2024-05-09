<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="paymongo.css">
</head>
<body>
    <div class="container">
        <div class="success-box">
            <h1 class="success-heading">Payment Successful!</h1>
            <p class="success-message">Thank you for your payment.</p>

            <div class="button-container">
                <button class="btn btn-primary dashboard-button" onclick="window.location.href = '../index.php';">Go to Dashboard</button>
                <button class="btn btn-secondary cart-button" onclick="window.location.href = '../bookingcart.php';">View Book Cart</button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
