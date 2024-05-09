<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    body {
        margin: 0;
        padding-bottom: 60px;
    }

    .footer {
        background-color: transparent;
        color: black;
        padding: 20px;
        text-align: center;
        width: auto;
    }

    .footer a {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Responsive styles for smaller screens */
    @media only screen and (max-width: 412px) {
        .footer {
            position: all;
            padding: 5px;
            margin-left: 40%;
        }
    }

    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }

    .close {
        color: #333;
        font-size: 24px;
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    }
</style>



</head>
<body>

    <!-- Your page content goes here -->

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> MYCC Van Rental's. All rights reserved. | 
            <a onclick="openModal('privacyModal')">Privacy Policy</a> | 
            <a onclick="openModal('termsModal')">Terms of Service</a>
        </p>
    </div>

    <!-- Privacy Policy Modal -->
<div id="privacyModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%;">
        <span class="close" onclick="closeModal('privacyModal')" style="color: #aaa; float: right; font-size: 28px; font-weight: bold;">&times;</span>
        <h2 style="margin-top: 0; background-color: transparent; color: black; text-align: justify;">Privacy Policy</h2>
        <p>
            Your privacy is important to us. This Privacy Policy explains how we collect, use,
            disclose, and safeguard your information when you visit our website.
            <!-- Add more details about your privacy policy here -->
        </p>
    </div>
</div>

<!-- Terms of Service Modal -->
<div id="termsModal" class="modal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);">
    <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%;">
        <span class="close" onclick="closeModal('termsModal')" style="color: #aaa; float: right; font-size: 28px; font-weight: bold;">&times;</span>
        <h2 style="margin-top: 0; background-color: transparent; color: black; text-align: justify;">Terms of Service</h2>
        <p>
            By accessing or using our services, you agree to comply with and be bound by these
            Terms of Service. Please read these terms carefully before using our website.
            <!-- Add more details about your terms of service here -->
        </p>
    </div>
</div>




    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "flex";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }
    </script>

</body>
</html>
