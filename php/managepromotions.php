<?php
$host = 'hopper.proxy.rlwy.net';
$port = 13403;
$user = 'root';
$password = 'xwOUJonIxgZWDjrTdzwWLYsJSazIcthn';
$database = 'railway';

$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

// SEARCH PROMOTION 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $query = "SELECT id, title, description, start_date, end_date, code FROM promotions 
              WHERE id = '$search' 
              OR title LIKE '%$search%' 
              OR code LIKE '%$search%' 
              LIMIT 1";

    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $promo = $result->fetch_assoc();
        echo json_encode(['success' => true, 'promo' => $promo]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Promotion not found']);
    }
    $conn->close();
    exit;
}

// DELETE PROMOTION 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM promotions WHERE id = ?");
    $stmt->bind_param("i", $deleteId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Promotion deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete promotion']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// ADD PROMOTION 
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['title'], $_POST['code'], $_POST['description'], $_POST['start_date'], $_POST['end_date'])
) {
    $title = $conn->real_escape_string($_POST['title']);
    $code = $conn->real_escape_string($_POST['code']);
    $description = $conn->real_escape_string($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Process image if uploaded
    $imageData = null;
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['banner_image']['tmp_name']);
    }

    $stmt = $conn->prepare("INSERT INTO promotions (title, code, description, start_date, end_date, banner_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssb", $title, $code, $description, $start_date, $end_date, $null);
    $null = null;
    $stmt->send_long_data(5, $imageData);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Promotion added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding promotion: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

$conn->close();
?>
