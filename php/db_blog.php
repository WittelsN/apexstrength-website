<?php
UjV4OenXwGrU1a

$host = 'turntable.proxy.rlwy.net';
$port = 37104;
$user = 'root';
$password = 'UZJFKFtxRYkTNsytXeLGrzuDXjYsENlk'; 
$database = 'railway';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$query = "SELECT id, title, content, banner_image FROM blog_posts ORDER BY created_at DESC";
$result = $conn->query($query);
$posts = [];

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $posts[] = [
      'id' => $row['id'],
      'title' => $row['title'],
      'content' => $row['content'],
      'banner_image' => 'data:image/jpeg;base64,' . base64_encode($row['banner_image']),
    ];
  }
}

$conn->close();
header('Content-Type: application/json');
echo json_encode(['success' => true, 'posts' => $posts]);
?>
