<?php
session_start();

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'loggedIn' => true,
        'userId' => $_SESSION['user_id'],
        'userName' => $_SESSION['user_name'],
        'role' => isset($_SESSION['role']) ? $_SESSION['role'] : 'user'
    ]);
} else {
    echo json_encode([
        'loggedIn' => false
    ]);
}
?>
