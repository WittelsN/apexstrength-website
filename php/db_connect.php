<?php
$host = 'hopper.proxy.rlwy.net';
$port = 3306;
$user = 'root';
$password = 'xwOUJonIxgZWDjrTdzwWLYsJSazIcthn'; 
$database = 'railway';

// Create connection
$conn = new mysqli($host, $user, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
