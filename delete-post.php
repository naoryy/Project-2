<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid post ID.']);
    exit;
}

include __DIR__ . '/service/database.php';

if (!isset($db) || !$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$stmt = $db->prepare("SELECT username FROM content WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Post not found.']);
    exit;
}

$row = $result->fetch_assoc();
$owner = $row['username'];
$current = $_SESSION['username'];

if ($owner !== $current) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'You can only delete your own posts.']);
    exit;
}

$del = $db->prepare("DELETE FROM content WHERE id = ?");
$del->bind_param('i', $id);

if ($del->execute()) {
    echo json_encode(['success' => true, 'message' => 'Post deleted successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete from database.']);
}

exit;
?>