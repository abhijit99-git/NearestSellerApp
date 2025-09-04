<?php
$host = "localhost";
$username = "root";
$password = "abhi879687#";
$database = "nearest_seller_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
