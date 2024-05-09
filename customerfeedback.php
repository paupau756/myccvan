<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedback</title>
    <!-- Online CDN for Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Your custom CSS -->
    <link rel="stylesheet" href="cdn.css">
</head>
<body>
    <!-- Feedback Section -->
    <section class="feedback-section">
        <h1 class="feedbackh1">Customer's Feedbacks</h1>
        <div class="feedback-container" id="feedbackContainer">
            <?php
            // Include the database connection
            include "admin/connection.php";

            // Fetch feedback data from the database
            $sql = "SELECT * FROM feedback";
            $result = mysqli_query($conn, $sql);

            // Check if there are any feedbacks
            if (mysqli_num_rows($result) > 0) {
                // Loop through each feedback and display details
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='feedback-item'>";
                    echo "<p class='name'><strong>Name:</strong> {$row['name']}</p>";
                    echo "<p class='destination'><strong>Destination:</strong> {$row['destination']}</p>";
                    echo "<p class='comment'><strong>Comment:</strong> {$row['comment']}</p>";
                    
                    // Display star rating based on satisfaction level
                    echo "<p class='satisfaction'><strong></strong> ";
                    if ($row['satisfaction'] === 'Verysatisfied') {
                        echo "<span class='stars'>★★★★★</span>";
                    } elseif ($row['satisfaction'] === 'Satisfied') {
                        echo "<span class='stars'>★★★★☆</span>";
                    } elseif ($row['satisfaction'] === 'Neutral') {
                        echo "<span class='stars'>★★★☆☆</span>";
                    } elseif ($row['satisfaction'] === 'Dissatisfied') {
                        echo "<span class='stars'>★★☆☆☆</span>";
                    } elseif ($row['satisfaction'] === 'Verydissatisfied') {
                        echo "<span class='stars'>★☆☆☆☆</span>";
                    }
                    echo "</p>";
                    echo "<p class='created'><strong></strong> " . date("F j, Y, | g:i A", strtotime($row['created'])) . "</p>";

                    echo "</div>";
                }
            } else {
                // Display a message if no feedbacks are found
                echo "<div class='no-feedbacks'>No feedbacks available.</div>";
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        </div>
    </section>
    <!-- Feedback Section Ends -->

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('feedbackContainer');
            let isDragging = false;
            let startX, scrollLeft;

            container.addEventListener('mousedown', (e) => {
                isDragging = true;
                container.classList.add('dragging');
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('touchstart', (e) => {
                isDragging = true;
                container.classList.add('dragging');
                startX = e.touches[0].clientX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('mouseleave', () => {
                if (isDragging) {
                    container.classList.remove('dragging');
                    isDragging = false;
                }
            });

            container.addEventListener('mouseup', () => {
                if (isDragging) {
                    container.classList.remove('dragging');
                    isDragging = false;
                }
            });

            container.addEventListener('touchend', () => {
                if (isDragging) {
                    container.classList.remove('dragging');
                    isDragging = false;
                }
            });

            container.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2; // You can adjust the scroll speed by changing the multiplier

                requestAnimationFrame(() => {
                    container.scrollLeft = scrollLeft - walk;
                });
            });

            container.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.touches[0].clientX - container.offsetLeft;
                const walk = (x - startX) * 2; // You can adjust the scroll speed by changing the multiplier

                requestAnimationFrame(() => {
                    container.scrollLeft = scrollLeft - walk;
                });
            });
        });
    </script>

</body>
</html>
