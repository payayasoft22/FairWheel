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
    die("Connection failed: " . $conn->connect_error);
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
    $type = $data['type'];
    $details = $data['details'];

    // Insert the payment method with user_id into the database
    $stmt = $conn->prepare("INSERT INTO payment_methods (user_id, type, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $type, $details);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Payment method saved"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error saving payment method"]);
    }

    $stmt->close();
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

$conn->close();
?>
