<?php
session_start();

$host = 'hopper.proxy.rlwy.net';
$port = 3306;
$user = 'root';
$password = 'xwOUJonIxgZWDjrTdzwWLYsJSazIcthn';
$database = 'railway';

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

// Sanitize email input
$email = filter_var($email, FILTER_SANITIZE_EMAIL);

// Fetch user data including role
$stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $name, $hashedPassword, $role);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['role'] = $role; // Set user role in session
        echo "success";
    } else {
        echo "Incorrect password";
    }
} else {
    echo "User not found";
}

$stmt->close();
$conn->close();
?>
