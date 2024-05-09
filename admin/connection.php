<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "aaa";

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// You can use $conn for database operations

?>
