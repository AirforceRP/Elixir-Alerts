<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Medication ID is required']);
    exit();
}

$medication_id = $_GET['id'];

// Fetch medication details and time slots
$sql = "SELECT m.id, m.name, m.start_date, m.end_date, mts.time 
        FROM medicine m
        LEFT JOIN medicine_time_slots mts ON m.id = mts.medicine_id
        WHERE m.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $medication_id);
$stmt->execute();
$result = $stmt->get_result();

$medication = null;
$time_slots = [];
while ($row = $result->fetch_assoc()) {
    if (!$medication) {
        $medication = [
            'id' => $row['id'],
            'name' => $row['name'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'time_slots' => []
        ];
    }
    $time_slots[] = $row['time'];
}
if ($medication) {
    $medication['time_slots'] = $time_slots;
}

$stmt->close();
$conn->close();

if ($medication) {
    echo json_encode(['success' => true, 'data' => $medication]);
} else {
    echo json_encode(['success' => false, 'message' => 'Medication not found']);
}
?>
