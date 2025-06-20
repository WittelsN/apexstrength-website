<?php
$conn = new mysqli("127.0.0.1:3307", "root", "2Noah!haoN2", "apexstrength");

if ($conn->connect_error) {
    die(json_encode(["error" => "DB connection failed"]));
}

$classQuery = "SELECT name, description, class_image FROM classes";
$result = $conn->query($classQuery);
$classes = [];

while ($row = $result->fetch_assoc()) {
    $row['class_image'] = 'data:image/jpeg;base64,' . base64_encode($row['class_image']);
    $classes[] = $row;
}

echo json_encode(["classes" => $classes]);
$conn->close();
?>
