<?php
session_start();
require 'db_connect.php'; // Use your actual DB connection file name

if (!isset($_SESSION['user_id']) || !isset($_POST['membership'])) {
  echo "Unauthorized or missing membership";
  exit;
}

$userId = $_SESSION['user_id'];
$membership = $_POST['membership'];

$stmt = $conn->prepare("UPDATE users SET membership = ? WHERE id = ?");
$stmt->bind_param("si", $membership, $userId);

if ($stmt->execute()) {
  echo "Membership updated";
} else {
  echo "Error updating: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
