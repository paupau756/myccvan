<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your head content goes here -->
</head>
<body>

    <!-- Page content -->

    <footer>
        <div id="footer-content">
            <center>
                <p id="copyright" onclick="toggleColor()">© <?php echo date("Y"); ?> MYCC Van Rental. All rights reserved. | 
                    <a onclick="openModal('privacyModal')">Privacy Policy</a> |
                    <a onclick="openModal('termsModal')">Terms of Service</a>
                </p>
                <!-- Social Media Icons with gap -->
                <a href="https://m.facebook.com/myccvanrental/" class="social-icon"><img src="admin/uploads/ffb.png" alt="Facebook" style="width: 20px; height: 20px; margin-right: 10px;"></a>
                <a href="#" class="social-icon"><img src="admin/uploads/twit.png" alt="Twitter" style="width: 20px; height: 20px; margin-right: 10px;"></a>
                <a href="#" class="social-icon"><img src="admin/uploads/iig.png" alt="Instagram" style="width: 20px; height: 20px;"></a>
            </center>
        </div>
    </footer>

    <!-- Privacy Policy Modal -->
    <div id="privacyModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 80%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0);">
        <div class="modal-content" style="background-color: #fefefe; margin: 20%; padding: 20px; border: 1px solid #888; width: 60%;">
            <span class="close" onclick="closeModal('privacyModal')" style="color: #aaa; float: right; font-size: 28px; font-weight: bold;">&times;</span>
            <h2 style="margin-top: 10px; background-color: white; color: black; text-align: center;">Privacy Policy</h2>
            <br> <br>
            <p>
                Your privacy is important to us. This Privacy Policy explains how we collect, use,
                disclose, and safeguard your information when you visit our website.
                <!-- Add more details about your privacy policy here -->
            </p>
        </div>
    </div>

    <!-- Terms of Service Modal -->
    <div id="termsModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 80%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0);">
        <div class="modal-content" style="background-color: #fefefe; margin: 20%; padding: 20px; border: 1px solid #888; width: 60%;">
            <span class="close" onclick="closeModal('termsModal')" style="color: #aaa; float: right; font-size: 28px; font-weight: bold;">&times;</span>
            <h2 style="margin-top: 10px; background-color: white; color: black; text-align: center;">Terms of Service</h2>
            <br>
            <p style="text-align: center;">
                By accessing or using our services, you agree to comply with and be bound by these
                Terms of Service. Please read these terms carefully before using our website.

                <br><br>"Strictly no self-drive
                We only accept Van rental with driver only 🙂"
                <!-- Add more details about your terms of service here -->
            </p>
        </div>
    </div>

    <!-- Your script to handle modal functionality and text color -->
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "flex";
            toggleColor();
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        function toggleColor() {
            var footerContent = document.getElementById("footer-content");
            var copyright = document.getElementById("copyright");

            var bgColor = getComputedStyle(footerContent).backgroundColor;
            var textColor = isLight(bgColor) ? "black" : "white";

            copyright.style.color = textColor;
        }
    </script>

</body>
</html>
