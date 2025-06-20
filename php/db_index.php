<?php
$host = 'turntable.proxy.rlwy.net';
$port = 37104;
$user = 'root';
$password = 'UZJFKFtxRYkTNsytXeLGrzuDXjYsENlk'; 
$database = 'railway';

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die(json_encode(['trainers' => [], 'classes' => [], 'promotions' => []]));
}

// --- Load Trainers ---
$trainerQuery = "SELECT pt.name, pt.specialization, pt.profile_image 
                 FROM personal_trainers pt 
                 LIMIT 3";
$trainerResult = $conn->query($trainerQuery);
$trainers = [];

while ($row = $trainerResult->fetch_assoc()) {
    $row['profile_image'] = 'data:image/jpeg;base64,' . base64_encode($row['profile_image']);
    $trainers[] = $row;
}

// --- Load Classes ---
$classQuery = "SELECT name, description, class_image FROM classes LIMIT 3";
$classResult = $conn->query($classQuery);
$classes = [];

while ($row = $classResult->fetch_assoc()) {
    $row['class_image'] = 'data:image/jpeg;base64,' . base64_encode($row['class_image']);
    $classes[] = $row;
}

// --- Load Promotions ---
$promoQuery = "SELECT title, code, description, start_date, end_date, banner_image FROM promotions LIMIT 3";
$promoResult = $conn->query($promoQuery);
$promotions = [];

while ($row = $promoResult->fetch_assoc()) {
    // Format date range
    $start = date("j F Y", strtotime($row['start_date']));
    $end = date("j F Y", strtotime($row['end_date']));
    $row['date_range'] = "$start - $end";

    // Handle banner image
    if (!empty($row['banner_image'])) {
        $row['banner_image'] = 'data:image/jpeg;base64,' . base64_encode($row['banner_image']);
    } else {
        $row['banner_image'] = '';
    }

    $promotions[] = $row;
}

// --- Output all data ---
header('Content-Type: application/json');
echo json_encode([
    'trainers' => $trainers,
    'classes' => $classes,
    'promotions' => $promotions
]);

$conn->close();
?>
