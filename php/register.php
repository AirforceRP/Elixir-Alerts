<?php
include 'db_connect.php';
session_start();

// Get the posted data
$data = json_decode(file_get_contents("php://input"), true);

$firstName = $data['firstName'];
$lastName = $data['lastName'];
$email = $data['email'];
$phone = $data['phone'];
$password = $data['password'];
$confirmpassword = $data['confirmpassword'];

// Validate phone number format (example: +1234567890)
if (!preg_match('/^\+\d{1,3}\d{4,14}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number format. Example: +1234567890']);
    exit();
}

// Validate password (minimum 8 characters, at least one special character)
if (!preg_match('/^(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/', $password)) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long and contain at least one special character.']);
    exit();
}

// Validate passwords match
if ($password !== $confirmpassword) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit();
}

// Check for duplicate email and phone number
$sql = "SELECT id FROM users WHERE email = ? OR phone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $phone);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email or phone number already exists']);
    $stmt->close();
    exit();
}
$stmt->close();

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert data into database
$sql = "INSERT INTO users (firstName, lastName, email, phone, password) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $conn->error]);
    exit();
}

$stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $hashedPassword);

if ($stmt->execute()) {
    // Get the user ID of the newly created user
    $userId = $stmt->insert_id;

    // Create session
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_data'] = [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $email,
        'phone' => $phone
    ];

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error executing statement: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
