<?php
require_once('vendor/autoload.php');
require_once('../admin/connection.php'); // Include your database connection file

$client = new \GuzzleHttp\Client();

function clean($string){
    $string = str_replace(' ','', $string);
    $string = str_replace('.','', $string);
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
}

// Function to insert downpayment into the inquiry table
function insertDownpayment($inquiryId, $downpayment, $conn) {
    $updateQuery = "UPDATE inquiries SET downpayment = $downpayment WHERE inquiryid = $inquiryId";
    return $conn->query($updateQuery);
}

// Function to insert notification into notifyadmin table
function insertNotificationAdmin($message, $status, $created, $conn) {
    $insertQuery = "INSERT INTO notifyadmin (message, status, created) VALUES ('$message', '$status', '$created')";
    if ($conn->query($insertQuery)) {
        return true; // Return true if insertion is successful
    } else {
        return "Error inserting notification: " . $conn->error; // Return error message if insertion fails
    }
}

try {
    $value = $_POST['amount'];
    $amount = clean($value) * 100; // Convert amount to cents
    $response = $client->request('POST', 'https://api.paymongo.com/v1/sources', [
        'body' => '{"data":{"attributes":{"amount":'.$amount.',"redirect":{"success":"https://tntscheduling.cloud/paymongo/success.php","failed":"https://tntscheduling.cloud/paymongo/failed.php"},"type":"gcash","currency":"PHP"}}}',
        'headers' => [
            'accept' => 'application/json',
            'authorization' => 'use your authorization',
            'content-type' => 'application/json',
        ],
    ]);

    $data = json_decode($response->getBody() , true); // returns an array
  
    $redirect = $data['data']['attributes']['redirect']['checkout_url'];
    echo "Redirecting in 3 seconds..";
    header('Refresh: 3;URL='.$redirect);

    // Update status to "paid" in the database
    $inquiryId = $_POST['inquiryid']; // Assuming inquiry ID is passed via POST
    $updateQuery = "UPDATE inquiries SET status = 'Paid' WHERE inquiryid = $inquiryId";
    if ($conn->query($updateQuery) === TRUE) {
        // Status updated successfully

        // Insert downpayment into the database
        $downpayment = $_POST['amount']; // Assuming amount is the downpayment value
        if (insertDownpayment($inquiryId, $downpayment, $conn)) {
            // Downpayment inserted successfully

            // Insert notification into notifyadmin table with status "unread"
            $notificationMessage = "Downpayment received for inquiry ID: $inquiryId";
            $notificationStatus = "unread"; // Set status to "unread"
            $notificationCreated = date('Y-m-d H:i:s');
            $notificationResult = insertNotificationAdmin($notificationMessage, $notificationStatus, $notificationCreated, $conn);
            if ($notificationResult === true) {
                // Notification inserted successfully
            } else {
                // Handle error if notification insertion fails
                echo $notificationResult;
            }
        } else {
            // Handle error if downpayment insertion fails
        }
    } else {
        // Handle error if the status update fails
    }

} catch (GuzzleHttp\Exception\ClientException $e) {
    $response = $e->getResponse();
    $responseBodyAsString = $response->getBody()->getContents();

    $error = json_decode($responseBodyAsString, true);

    print_r($error['errors'][0]['detail']);
}
?>
