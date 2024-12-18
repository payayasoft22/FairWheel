<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fairwheel_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get and validate JSON input
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data received.', 'rawInput' => $rawInput]);
    exit;
}

$password = password_hash($data['password'], PASSWORD_BCRYPT);

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please try again.']);
    exit;
}

$email = $_SESSION['email'];

// Prepare the SQL statement
$query = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
if (!$query) {
    echo json_encode(['success' => false, 'message' => 'Database query failed: ' . $conn->error]);
    exit;
}

$query->bind_param('ss', $password, $email);

if ($query->execute()) {
    unset($_SESSION['otp'], $_SESSION['email']);
    echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update password.']);
}

$query->close();
$conn->close();
?>
