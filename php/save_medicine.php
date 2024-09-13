<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}
$data = json_decode(file_get_contents("php://input"), true);
 // Validate if all fields exist
if (!isset($data['child_id'], $data['name'], $data['start_date'], $data['end_date'], $data['time_slots']) || count($data['time_slots']) < 1) {

    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

$child_id = $data['child_id'];
$name = $data['name'];
$start_date = $data['start_date'];
$end_date = $data['end_date'];
$time_slots = $data['time_slots'];

// Start transaction
$conn->begin_transaction();

try {
    // Check if the medicine already exists
    $sql = "SELECT id FROM medicine WHERE child_id = ? AND name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $child_id, $name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Medicine exists, update the existing one
        $stmt->bind_result($medicine_id);
        $stmt->fetch();
        $stmt->close();

        // Delete existing time slots
        $sql = "DELETE FROM medicine_time_slots WHERE medicine_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $medicine_id);
        $stmt->execute();
        $stmt->close();

        // Insert new time slots
        $sql = "INSERT INTO medicine_time_slots (medicine_id, time) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($time_slots as $time) {
            $stmt->bind_param("is", $medicine_id, $time);
            $stmt->execute();
        }
        $stmt->close();
    } else {
        // Medicine does not exist, insert new medicine
        $stmt->close();
        $sql = "INSERT INTO medicine (child_id, name, start_date, end_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $child_id, $name, $start_date, $end_date);
        $stmt->execute();
        $medicine_id = $stmt->insert_id;
        $stmt->close();

        // Insert time slots
        $sql = "INSERT INTO medicine_time_slots (medicine_id, time) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($time_slots as $time) {
            $stmt->bind_param("is", $medicine_id, $time);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction if there is an error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error saving medicine details: ' . $e->getMessage()]);
}

$conn->close();
?>
