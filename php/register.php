<?php
$host = 'hopper.proxy.rlwy.net';
$port = 13403;
$db = 'railway';
$user = 'root';
$pass = 'xwOUJonIxgZWDjrTdzwWLYsJSazIcthn';

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$role = 'user';

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  echo "Email already registered.";
} else {
  $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $password, $role);
  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "error: " . $stmt->error;
  }
}

$conn->close();
?>
