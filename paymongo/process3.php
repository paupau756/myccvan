<?php
require_once('vendor/autoload.php');
require_once('../admin/connection.php'); // Include your database connection file

$client = new \GuzzleHttp\Client();

function clean($string){
    $string = str_replace(' ','', $string);
    $string = str_replace('.','', $string);
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
}

try {
    $value = $_POST['amount'];
    $amount = clean($value) * 100; // Convert amount to cents
    $response = $client->request('POST', 'https://api.paymongo.com/v1/sources', [
        'body' => '{"data":{"attributes":{"amount":'.$amount.',"redirect":{"success":"https://tntscheduling.cloud/paymongo/success.php","failed":"https://tntscheduling.cloud/paymongo/failed.php"},"type":"gcash","currency":"PHP"}}}',
        'headers' => [
            'accept' => 'application/json',
            'authorization' => 'Basic c2tfdGVzdF90aHF2dHpoUll4VXFKcWZld0N2S05pcGk6',
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