<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$parent_id = $_SESSION['user_id'];
$child_id = isset($_GET['child_id']) && $_GET['child_id'] !== 'all' ? $_GET['child_id'] : null;

$sql = "SELECT m.id as medicine_id, m.name, m.start_date, m.end_date, mts.time, c.id as child_id, c.first_name, c.last_name
        FROM medicine_time_slots mts
        JOIN medicine m ON mts.medicine_id = m.id
        JOIN children c ON m.child_id = c.id
        WHERE c.parent_id = ?";
$params = [$parent_id];

if ($child_id) {
    $sql .= " AND c.id = ?";
    $params[] = $child_id;
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Error preparing statement: " . $conn->error);
    echo json_encode([]);
    exit();
}

$stmt->bind_param(str_repeat('i', count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

$medications = [];
$colors = ['#378006', '#1e90ff', '#ff6347', '#ffa500', '#8a2be2'];  // Different colors for different children
$child_colors = [];

while ($row = $result->fetch_assoc()) {
    if (!isset($child_colors[$row['child_id']])) {
        $child_colors[$row['child_id']] = array_shift($colors);
    }

    $start_date = new DateTime($row['start_date']);
    $end_date = new DateTime($row['end_date']);
    $end_date->modify('+1 day'); // To include the end date

    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start_date, $interval, $end_date);

    foreach ($date_range as $date) {
        $medications[] = [
            'title' => "{$row['first_name']} {$row['last_name']}: {$row['name']} at {$row['time']}",
            'start' => $date->format('Y-m-d') . 'T' . $row['time'],
            'color' => $child_colors[$row['child_id']],
            'extendedProps' => [
                'child_name' => "{$row['first_name']} {$row['last_name']}",
                'medication_name' => $row['name'],
                'time' => $row['time'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date']
            ]
        ];
    }
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($medications);
?>
