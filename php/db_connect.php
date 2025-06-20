<?php
$host = 'turntable.proxy.rlwy.net';
$port = 37104;
$user = 'root';
$password = 'UZJFKFtxRYkTNsytXeLGrzuDXjYsENlk'; 
$database = 'railway';

// Create connection
$conn = new mysqli($host, $user, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
