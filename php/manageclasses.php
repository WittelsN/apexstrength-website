<?php
$host = 'hopper.proxy.rlwy.net';
$port = 3306;
$user = 'root';
$password = 'xwOUJonIxgZWDjrTdzwWLYsJSazIcthn';
$database = 'railway';

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- SEARCH CLASS ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $query = "SELECT id, name, description FROM classes 
              WHERE id = '$search' OR name LIKE '%$search%' LIMIT 1";

    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $class = $result->fetch_assoc();
        echo json_encode(['success' => true, 'class' => $class]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Class not found']);
    }
    $conn->close();
    exit;
}

// --- DELETE CLASS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];
    $delete = $conn->prepare("DELETE FROM classes WHERE id = ?");
    $delete->bind_param("i", $deleteId);

    if ($delete->execute()) {
        echo json_encode(['success' => true, 'message' => 'Class deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting class']);
    }

    $delete->close();
    $conn->close();
    exit;
}

// --- ADD NEW CLASS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['description'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);

        // Proper handling of LONGBLOB
        $stmt = $conn->prepare("INSERT INTO classes (name, description, class_image) VALUES (?, ?, ?)");
        $null = NULL;
        $stmt->bind_param("ssb", $name, $description, $null);
        $stmt->send_long_data(2, $imageData);
    } else {
        $stmt = $conn->prepare("INSERT INTO classes (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Class added successfully!'); window.location.href='../manageclasses.html';</script>";
    } else {
        echo "Error saving class: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}

$conn->close();
?>
