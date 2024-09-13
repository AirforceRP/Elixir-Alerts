<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$parent_id = $_SESSION['user_id'];
$child_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($child_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid child ID']);
    exit();
}

// Check if the child belongs to the logged-in user
$sql = "SELECT id FROM children WHERE id = ? AND parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $child_id, $parent_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Child not found or access denied']);
    exit();
}
$stmt->close();

// Delete time slots associated with the child's medicines
$sql = "DELETE mts
        FROM medicine_time_slots mts
        JOIN medicine m ON mts.medicine_id = m.id
        WHERE m.child_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $child_id);
if (!$stmt->execute()) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Failed to delete medicine time slots: ' . $stmt->error]);
    exit();
}
$stmt->close();

// Delete medicines associated with the child
$sql = "DELETE FROM medicine WHERE child_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $child_id);
if (!$stmt->execute()) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Failed to delete medicines: ' . $stmt->error]);
    exit();
}
$stmt->close();

// Delete the child profile
$sql = "DELETE FROM children WHERE id = ? AND parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $child_id, $parent_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete child profile: ' . $stmt->error]);
}
$stmt->close();

$conn->close();
?>
