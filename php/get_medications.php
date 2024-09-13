<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if (!isset($_GET['child_id'])) {
    echo json_encode(['success' => false, 'message' => 'Child ID is required']);
    exit();
}

$child_id = $_GET['child_id'];

// Fetch medications and their time slots
$sql = "SELECT m.id, m.name, m.start_date, m.end_date, mts.time 
        FROM medicine m
        LEFT JOIN medicine_time_slots mts ON m.id = mts.medicine_id
        WHERE m.child_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $child_id);
$stmt->execute();
$result = $stmt->get_result();

$medications = [];
while ($row = $result->fetch_assoc()) {
    $medication_id = $row['id'];
    if (!isset($medications[$medication_id])) {
        $medications[$medication_id] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'time_slots' => []
        ];
    }
    $medications[$medication_id]['time_slots'][] = $row['time'];
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'data' => array_values($medications)]);
?>
