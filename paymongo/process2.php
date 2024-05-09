<?php
require_once('vendor/autoload.php');

$client = new \GuzzleHttp\Client();

$response = $client->request('POST', 'https://api.paymongo.com/v1/sources', [
  'body' => '{"data":{"attributes":{"amount":100100,"redirect":{"success":"http://localhost/5/paymongo/success.php","failed":"http://localhost/5/paymongo/failed.php"},"type":"gcash","currency":"PHP"}}}',
  'headers' => [
    'accept' => 'application/json',
    'authorization' => 'Basic cGtfdGVzdF92amdtS24yaTZvRGhEdEo4Q0hLYW5qd1o6',
    'content-type' => 'application/json',
  ],
]);

echo $response->getBody();