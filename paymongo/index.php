<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
</head>
<body>
    <div class="payment-form">
        <h2>Payment Form</h2>
        <form action="process.php" method="post">
            <label for="amount">Amount:</label>
            <input type="text" name="amount" id="currency" data-type="currency">
            <input type="submit" value="Pay Now">
        </form>
    </div>
</body>
</html>
