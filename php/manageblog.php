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

// --- SEARCH BLOG ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $query = "SELECT id, title, created_at FROM blog_posts WHERE id = '$search' OR title LIKE '%$search%' LIMIT 1";

    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $blog = $result->fetch_assoc();
        echo json_encode(['success' => true, 'blog' => $blog]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Blog post not found']);
    }
    $conn->close();
    exit;
}

// --- DELETE BLOG ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $deleteId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Blog post deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete blog post']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// --- ADD BLOG POST ---
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "User not logged in.";
        exit;
    }

    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $created_by = (int) $_SESSION['user_id'];
    $banner_image = NULL;

    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $banner_image = file_get_contents($_FILES['banner_image']['tmp_name']);
    }

    $stmt = $conn->prepare("INSERT INTO blog_posts (title, content, banner_image, created_by, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssi", $title, $content, $banner_image, $created_by);
    $stmt->send_long_data(2, $banner_image);

    if ($stmt->execute()) {
        echo "Blog post added successfully!";
    } else {
        echo "Error adding blog post: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}

$conn->close();
?>
