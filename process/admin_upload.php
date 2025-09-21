<?php
require_once '../src/includes/config.php';
require_once '../src/includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['image'];
$fileSize = $file['size'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];

// Validate file size
if ($fileSize > UPLOAD_CONFIG['max_file_size']) {
    http_response_code(400);
    echo json_encode(['error' => 'File size too large']);
    exit;
}

// Validate file extension
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
if (!in_array($fileExtension, UPLOAD_CONFIG['allowed_extensions'])) {
    http_response_code(400);
    echo json_encode(['error' => 'File type not allowed']);
    exit;
}

// Generate unique filename
$uniqueFileName = uniqid() . '_' . time() . '.' . $fileExtension;

// [FIX] Absolute path for saving
$uploadDir = rtrim(UPLOAD_CONFIG['upload_path'], '/\\') . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$uploadPath = $uploadDir . $uniqueFileName;

// Move uploaded file
if (move_uploaded_file($fileTmpName, $uploadPath)) {
    // [FIX] Web path (what browser can use in <img src>)
    $fileUrl = '/assets/uploads/' . $uniqueFileName;

    echo json_encode([
        'success' => true,
        'fileUrl' => $fileUrl,
        'fileName' => $uniqueFileName
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to move uploaded file']);
}
