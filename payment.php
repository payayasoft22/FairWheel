<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fairwheel_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Ensure the user is logged in by checking session
if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['id']; // Get the logged-in user's ID

// Handle saving the payment method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if 'type' and 'details' are set
    $type = isset($data['type']) ? $data['type'] : '';
    $details = isset($data['details']) ? $data['details'] : '';

    if (empty($type) || empty($details)) {
        echo json_encode(["status" => "error", "message" => "Missing payment type or details"]);
        exit();
    }

    // Sanitize the input data
    $type = $conn->real_escape_string($type);
    $details = $conn->real_escape_string($details);

    // Check if the payment method already exists
    $checkStmt = $conn->prepare("SELECT id FROM payment_methods WHERE user_id = ? AND type = ? AND details = ?");
    $checkStmt->bind_param("iss", $user_id, $type, $details);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Payment method already exists
        echo json_encode(["status" => "error", "message" => "Payment method already exists"]);
    } else {
        // Insert the new payment method if it does not exist
        $insertStmt = $conn->prepare("INSERT INTO payment_methods (user_id, type, details) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iss", $user_id, $type, $details);

        if ($insertStmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Payment method saved successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error saving payment method"]);
        }
        $insertStmt->close();
    }

    $checkStmt->close();
}

// Handle fetching saved payment methods
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch payment methods for the logged-in user
    $stmt = $conn->prepare("SELECT * FROM payment_methods WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $payment_methods = [];
    while ($row = $result->fetch_assoc()) {
        $payment_methods[] = $row;
    }

    echo json_encode($payment_methods);
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
