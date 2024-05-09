<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popular Tours</title>
    <!-- Online CDN for Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Your custom CSS -->
    <link rel="stylesheet" href="cdn.css">
</head>
<body>

<!-- Popular Tours Section -->
<section id="popular-tours">
    <div id="popular-tours-header">
        <h2>Popular Tours</h2>
        <p>Discover our most loved travel experiences.</p>
    </div>

    <div class="popular-tours-container" id="popular-tours-container">
        <?php
            // Include database connection
            include 'admin/connection.php';

            // Fetch tour images and names from the tours table
            $sql = "SELECT tourimages, destination FROM tours";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    // Explode the path to get the filename
                    $imagePath = $row["tourimages"];
                    $pathParts = explode('/', $imagePath);
                    $filename = end($pathParts);

                    echo '<div class="tour-item">';
                    echo '<img src="admin/uploads/' . $filename . '" alt="' . $row["destination"] . '">';
                    echo '<p>' . $row["destination"] . '</p>';
                    echo '</div>';
                }
            } else {
                echo "No popular tours available.";
            }

            // Close database connection
            $conn->close();
        ?>
    </div>
</section>
<!-- Popular Tours Section ENds -->


    <!-- popular tours scoller -->
        <script>
            const container = document.getElementById('popular-tours-container');
            let isDown = false;
            let startX;
            let scrollLeft;

            container.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });

            container.addEventListener('mouseleave', () => {
                isDown = false;
            });

            container.addEventListener('mouseup', () => {
                isDown = false;
            });

            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2; // Adjust scrolling speed
                container.scrollLeft = scrollLeft - walk;
            });
        </script>

</body>
</html>
