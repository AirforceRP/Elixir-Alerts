<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$parent_id = $_SESSION['user_id'];
$medication_id = $data['medication_id'];
$name = $data['name'];
$start_date = $data['start_date'];
$end_date = $data['end_date'];
$time_slots = $data['time_slots'];

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
    // Update medication details
    $sql = "UPDATE medicine SET name = ?, start_date = ?, end_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $start_date, $end_date, $medication_id);
    $stmt->execute();
    $stmt->close();

    // Delete old time slots
    $sql = "DELETE FROM medicine_time_slots WHERE medicine_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $medication_id);
    $stmt->execute();
    $stmt->close();

    // Insert new time slots
    $sql = "INSERT INTO medicine_time_slots (medicine_id, time) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    foreach ($time_slots as $time) {
        $stmt->bind_param("is", $medication_id, $time);
        $stmt->execute();
    }
    $stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction if there is an error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error updating medication: ' . $e->getMessage()]);
}

$conn->close();
?>
