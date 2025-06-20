<?php
$host = 'hopper.proxy.rlwy.net';
$port = 3306;
$user = 'root';
$password = 'xwOUJonIxgZWDjrTdzwWLYsJSazIcthn';
$dbname = 'railway';

// Connect
$conn = new mysqli($host, $user, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Load and run SQL file
$sql = file_get_contents("apexstrength.sql");
if ($conn->multi_query($sql)) {
    echo "✅ Import successful.";
} else {
    echo "❌ Error: " . $conn->error;
}

$conn->close();
?>
