<?php
header('Content-Type: application/json');

// Database connection
$host = 'localhost'; 
$username = 'root'; 
$password = ''; 
$database = 'fairwheel_db'; 

$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Retrieve and sanitize input
$data = json_decode(file_get_contents('php://input'), true);
$ticketNumber = $conn->real_escape_string($data['ticketNumber']);

// Delete the ticket from the `tickets` table
$query = "DELETE FROM tickets WHERE ticket_number = '$ticketNumber'";
if ($conn->query($query) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Ticket deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting ticket: ' . $conn->error]);
}

$conn->close();
?>
