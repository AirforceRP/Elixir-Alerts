<?php
include 'db_connect.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$photo = null;

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Validate phone number format (example: +1234567890)
if (!preg_match('/^\+\d{1,3}\d{4,14}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number format. Example: +1234567890']);
    exit();
}

// Check if email or phone has changed from session value and check for duplication
$emailChanged = $email !== $_SESSION['user_data']['email'];
$phoneChanged = $phone !== $_SESSION['user_data']['phone'];

if ($emailChanged || $phoneChanged) {
    $sql = "SELECT id FROM users WHERE (email = ? OR phone = ?) AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $email, $phone, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email or phone number already exists']);
        $stmt->close();
        exit();
    }
    $stmt->close();
}

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
// Update profile in the database
$sql = "UPDATE users SET firstName = ?, lastName = ?, email = ?, phone = ?";
if ($photo) {
    $sql .= ", photo = ?";
}
$sql .= " WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($photo) {
    $stmt->bind_param("sssssi", $firstName, $lastName, $email, $phone, $photo, $user_id);
} else {
    $stmt->bind_param("ssssi", $firstName, $lastName, $email, $phone, $user_id);
}

if ($stmt->execute()) {
    // Update session data
    $_SESSION['user_data']['firstName'] = $firstName;
    $_SESSION['user_data']['lastName'] = $lastName;
    $_SESSION['user_data']['email'] = $email;
    $_SESSION['user_data']['phone'] = $phone;
    if ($photo) {
        $_SESSION['user_data']['photo'] = $photo;
    }

    echo json_encode(['success' => true, 'photo' => $photo]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
