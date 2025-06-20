<?php
session_start();
require 'db_connect.php';

$response = ['loggedIn' => false];

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT name, membership FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($name, $membership);
    if ($stmt->fetch()) {
        $response['loggedIn'] = true;
        $response['membership'] = $membership;
        $response['name'] = $name;
    }
    $stmt->close();
}
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
