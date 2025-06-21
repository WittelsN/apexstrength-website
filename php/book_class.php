<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  echo "not_logged_in";
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $userId = $_SESSION['user_id'];
  $className = $_POST['class_name'] ?? '';
  $location = $_POST['location'] ?? '';
  $date = $_POST['date'] ?? '';
  $time = $_POST['time'] ?? '';

  if (empty($className) || empty($location) || empty($date) || empty($time)) {
    echo "missing_fields";
    exit;
  }

  // ✅ Updated connection for Railway environment
  $conn = new mysqli(
    getenv('MYSQLHOST'),
    getenv('MYSQLUSER'),
    getenv('MYSQLPASSWORD'),
    getenv('MYSQLDATABASE')
  );

  if ($conn->connect_error) {
    echo "db_error";
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO user_bookings (user_id, class_name, location, date, time) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issss", $userId, $className, $location, $date, $time);

  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "insert_error";
  }

  $stmt->close();
  $conn->close();
}
?>
