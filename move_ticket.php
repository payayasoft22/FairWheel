<?php

$conn = new mysqli('localhost', 'root', '', 'fairwheel_db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $ticketId = $input['ticket_id'];
    $paymentStatus = $input['payment_status'];

    if ($paymentStatus === 'success') {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Insert into history
            $insertQuery = "INSERT INTO history (ticket_id, departure_date, from_location, to_location, bus_operator, selected_seat, trip_type, total_price, payment_status)
                            SELECT id, departure_date, from_location, to_location, bus_operator, selected_seat, trip_type, total_price, 'Paid'
                            FROM tickets
                            WHERE id = ?";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("i", $ticketId);
            $stmt->execute();

            // Delete from tickets
            $deleteQuery = "DELETE FROM tickets WHERE id = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("i", $ticketId);
            $stmt->execute();

            // Commit the transaction
            $conn->commit();

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $conn->rollback(); // Rollback on error
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
?>
