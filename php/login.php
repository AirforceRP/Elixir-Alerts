<?php
include 'db_connect.php';
session_start();

// Get the posted data
$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = $data['password'];

// Prepare and execute the query to fetch the user by email
$sql = "SELECT id, firstName, lastName, email, phone, password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $conn->error]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $firstName, $lastName, $email, $phone, $hashedPassword);
    $stmt->fetch();

    // Verify the password
    if (password_verify($password, $hashedPassword)) {
        // Create session
        $_SESSION['user_id'] = $id;
        $_SESSION['user_data'] = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phone' => $phone
        ];

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Email not found']);
}

$stmt->close();
$conn->close();
?>
