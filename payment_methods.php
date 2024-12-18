<?php
session_start();
header("Content-Type: application/json");


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fairwheel_db";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['id'];

// Fetch saved payment methods
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT id, type, details FROM payment_methods WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $savedPayments = [];
    while ($row = $result->fetch_assoc()) {
        $savedPayments[] = $row;
    }

    echo json_encode($savedPayments);
    $stmt->close();
}
$conn->close();
?>
