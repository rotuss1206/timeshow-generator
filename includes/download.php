<?php
// download.php
$file = $_GET['file'] ?? '';
$allowed_dir = TIME_DIR . '/uploads/ai_docs/';

// безпечний шлях
$path = realpath($allowed_dir . '/' . basename($file));
if (!$path || strpos($path, $allowed_dir) !== 0 || !file_exists($path)) {
    die('File not found');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($path) . '"');
header('Content-Length: ' . filesize($path));

readfile($path);
exit;
