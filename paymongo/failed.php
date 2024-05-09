<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="paymongo.css">
</head>
<body>
    <div class="container">
        <div class="failure-box">
            <h1 class="failure-heading">Payment Failed!</h1>
            <p class="failure-message">We're sorry, but there was a problem processing your payment.</p>

            <div class="button-container">
                <button class="btn btn-primary try-again-button" onclick="window.history.back();">Try Again</button>
                <button class="btn btn-secondary contact-support-button" onclick="window.location.href = '../index.php#contact-us';">Contact Support</button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
