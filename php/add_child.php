<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$parent_id = $_SESSION['user_id'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$dateOfBirth = $_POST['dateOfBirth'];
$gender = $_POST['gender'];
$timezone = $_POST['timezone'];
$photo = null;

// Check if the child already exists
$sql = "SELECT id FROM children WHERE parent_id = ? AND first_name = ? AND last_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $parent_id, $firstName, $lastName);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Child already exists']);
    $stmt->close();
    $conn->close();
    exit();
}

$stmt->close();

// Handle file upload
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/photos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $uploaded_file = $upload_dir . basename($_FILES['photo']['name']);
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploaded_file)) {
        $photo = basename($_FILES['photo']['name']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error uploading file']);
        exit();
    }
}

// Insert child data into the database
$sql = "INSERT INTO children (parent_id, first_name, last_name, date_of_birth, gender, timezone, photo, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssss", $parent_id, $firstName, $lastName, $dateOfBirth, $gender, $timezone, $photo);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error inserting child data: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
