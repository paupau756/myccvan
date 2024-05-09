<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <!-- Your custom CSS -->
    <link rel="stylesheet" href="cdn.css">
</head>
<body>

<!-- Contact Us Section -->
<section id="contact-us">
    <h3>Contact Us</h3>

    <div id="contact-form">
        <!-- Your contact form goes here -->
        <form action="process_contact_form.php" method="post">
            <!-- Your form fields go here -->
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="name@gmail.com" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="4" placeholder="You have a question? Leave us a message here." required></textarea>

            <button type="submit">Submit</button>
        </form>
    </div>

    <div id="contact-details">
        <!-- Contact details like Facebook and Gmail go here -->
        <p>Connect with us on:</p>
        <ul>
            <li><a href="https://m.facebook.com/myccvanrental/" target="_blank">Facebook</a></li>
            <li><strong>Contact No:</strong> 0976 040 2949/ 0961 758 8437</li>
            <li><strong>Email:</strong> myccvanrental@gmail.com</li>
            <li><strong>Address:</strong> Blk 35 Lot 7, St. Therese Phase, Deca Homes, Marilao, 3019 Bulacan</li>
        </ul>
        
        <iframe width="100%" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=119.72107%2C14.26593%2C124.11529%2C18.64525&amp;layer=mapnik&amp;marker=14.7575%2C120.9619&amp;zoom=19"></iframe><br/><small><a href="https://www.openstreetmap.org/?mlat=14.7575&amp;mlon=120.9619#map=12/14.7575/120.9619"></a></small>
    </div>

</section>
<!-- Contact Us Section Ends-->

</body>
</html>
