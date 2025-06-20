<?php
$conn = new mysqli("127.0.0.1:3307", "root", "2Noah!haoN2", "apexstrength");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'DB connection failed']));
}

$query = "SELECT name, specialization, bio, profile_image FROM personal_trainers";
$result = $conn->query($query);

$trainers = [];

while ($row = $result->fetch_assoc()) {
    $row['profile_image'] = 'data:image/jpeg;base64,' . base64_encode($row['profile_image']);
    $trainers[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'trainers' => $trainers]);

$conn->close();
?>
