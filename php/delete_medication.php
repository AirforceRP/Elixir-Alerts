<?php
include 'db_connect.php';
session_start();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Medication ID is required']);
    exit();
}

$medication_id = $data['id'];
$parent_id = $_SESSION['user_id'];

// Check if the logged-in parent is the owner of the medication
$sql = "SELECT m.id FROM medicine m 
        JOIN children c ON m.child_id = c.id 
        WHERE m.id = ? AND c.parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $medication_id, $parent_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized action or medication not found']);
    $stmt->close();
    exit();
}

$stmt->close();

// Start transaction
$conn->begin_transaction();

try {
    // Delete time slots
    $sql = "DELETE FROM medicine_time_slots WHERE medicine_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $medication_id);
    $stmt->execute();
    $stmt->close();

    // Delete medication
    $sql = "DELETE FROM medicine WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $medication_id);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction if there is an error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error deleting medication: ' . $e->getMessage()]);
}

$conn->close();
?>
