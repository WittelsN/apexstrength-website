<?php
$host = 'hopper.proxy.rlwy.net';
$port = 13403;
$user = 'root';
$password = 'xwOUJonIxgZWDjrTdzwWLYsJSazIcthn';
$database = 'railway';

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- SEARCH TRAINER ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $query = "SELECT pt.id, pt.name, pt.specialization, pt.bio 
              FROM personal_trainers pt
              WHERE pt.id = '$search' OR pt.name LIKE '%$search%' LIMIT 1";

    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $trainer = $result->fetch_assoc();
        echo json_encode(['success' => true, 'trainer' => $trainer]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Trainer not found']);
    }
    $conn->close();
    exit;
}

// --- DELETE TRAINER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];

    // Get user_id linked to trainer
    $res = $conn->query("SELECT user_id FROM personal_trainers WHERE id = $deleteId");
    if ($res && $res->num_rows > 0) {
        $userRow = $res->fetch_assoc();
        $userId = $userRow['user_id'];

        $conn->query("DELETE FROM personal_trainers WHERE id = $deleteId");
        $conn->query("DELETE FROM users WHERE id = $userId");

        echo json_encode(['success' => true, 'message' => 'Trainer deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Trainer not found']);
    }
    $conn->close();
    exit;
}

// --- ADD NEW TRAINER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['bio'], $_POST['specialization'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $bio = $conn->real_escape_string($_POST['bio']);
    $specialization = $conn->real_escape_string($_POST['specialization']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Optional: file type check (basic)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg'])) {
            echo "Unsupported image type. Please upload a JPG or PNG file.";
            exit;
        }

        $imgData = file_get_contents($_FILES['image']['tmp_name']);

        // Generate a unique placeholder email
        $uniqueEmail = strtolower(str_replace(' ', '', $name)) . uniqid('@trainer.apex');

        // Add user to users table
        $stmtUser = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, '', 'trainer')");
        $stmtUser->bind_param("ss", $name, $uniqueEmail);
        if (!$stmtUser->execute()) {
            echo "Error adding user: " . $stmtUser->error;
            exit;
        }
        $user_id = $stmtUser->insert_id;
        $stmtUser->close();

        // Add trainer to personal_trainers table (includes name)
        $stmt = $conn->prepare("INSERT INTO personal_trainers (user_id, name, bio, specialization, profile_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $name, $bio, $specialization, $imgData);

        if ($stmt->execute()) {
            echo "<script>alert('Trainer added successfully!'); window.location.href='managetrainers.html';</script>";
        } else {
            echo "Error saving trainer: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $errorCode = $_FILES['image']['error'];
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => "File exceeds upload_max_filesize.",
            UPLOAD_ERR_FORM_SIZE  => "File exceeds form MAX_FILE_SIZE.",
            UPLOAD_ERR_PARTIAL    => "File was only partially uploaded.",
            UPLOAD_ERR_NO_FILE    => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
            UPLOAD_ERR_EXTENSION  => "File upload stopped by extension.",
        ];
        $message = $errorMessages[$errorCode] ?? "Unknown upload error.";
        echo "Image upload failed: $message";
    }
}

$conn->close();
?>
