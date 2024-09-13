<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Validate if all fields exist
if (!isset($_POST['child_id'], $_POST['firstName'], $_POST['lastName'], $_POST['dateOfBirth'], $_POST['gender'], $_POST['timezone'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

$parent_id = $_SESSION['user_id'];
$child_id = $_POST['child_id'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$dateOfBirth = $_POST['dateOfBirth'];
$gender = $_POST['gender'];
$timezone = $_POST['timezone'];
$photo = null;

// Check if the name already exists for this parent and is different from the current child's name
$sql = "SELECT id FROM children WHERE parent_id = ? AND first_name = ? AND last_name = ? AND id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issi", $parent_id, $firstName, $lastName, $child_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Child with the same name already exists']);
    $stmt->close();
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

// Update child data in the database
$sql = "UPDATE children SET first_name = ?, last_name = ?, date_of_birth = ?, gender = ?, timezone = ?";
if ($photo) {
    $sql .= ", photo = ?";
}
$sql .= " WHERE id = ? AND parent_id = ?";
$stmt = $conn->prepare($sql);

if ($photo) {
    $stmt->bind_param("ssssssii", $firstName, $lastName, $dateOfBirth, $gender, $timezone, $photo, $child_id, $parent_id);
} else {
    $stmt->bind_param("sssssii", $firstName, $lastName, $dateOfBirth, $gender, $timezone, $child_id, $parent_id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating child data: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
