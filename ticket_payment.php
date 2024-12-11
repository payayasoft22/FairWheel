<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "fairwheel_db";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get ticket ID from the form
$ticket_id = $_POST['ticket_id'];

// Fetch the ticket data
$sql_fetch = "SELECT * FROM tickets WHERE id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("i", $ticket_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();

if ($result->num_rows > 0) {
    // Insert data into completed_tickets
    $ticket = $result->fetch_assoc();
    $sql_insert = "INSERT INTO completed_tickets (column1, column2, column3) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param(
        "sss",
        $ticket['column1'],
        $ticket['column2'],
        $ticket['column3']
    );

    if ($stmt_insert->execute()) {
        // Delete from tickets table
        $sql_delete = "DELETE FROM tickets WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $ticket_id);

        if ($stmt_delete->execute()) {
            echo "Payment processed successfully.";
        } else {
            echo "Error deleting ticket: " . $conn->error;
        }
    } else {
        echo "Error inserting into completed_tickets: " . $conn->error;
    }
} else {
    echo "Ticket not found.";
}

// Close connection
$conn->close();
?>
