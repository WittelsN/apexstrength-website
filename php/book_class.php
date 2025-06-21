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

  // ✅ Railway connection settings
  $host = 'hopper.proxy.rlwy.net';
  $port = 13403;
  $user = 'root';
  $password = 'xwOUJonIxgZWDjrTdzwWLYsJSazIcthn';
  $database = 'railway';

  $conn = new mysqli($host, $user, $password, $database, $port);

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
