<?php


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fairwheel_db";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from POST request
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);

// Check if JSON decoding was successful and data exists
if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
    $ticketId = $data['ticket_id'];
    $paymentMethod = $data['payment_method'];
    $totalPrice = $data['total_price'];

    try {
        // Start a transaction
        $conn->begin_transaction();

        // Prepare the SELECT statement
        $selectStmt = $conn->prepare("SELECT * FROM tickets WHERE ticket_number = ?");
        $selectStmt->bind_param("s", $ticketId);
        $selectStmt->execute();
        $result = $selectStmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Prepare the INSERT statement for the history table
            $insertStmt = $conn->prepare("INSERT INTO history (
                ticket_number, user_id, from_location, to_location, departure_date, 
                return_date, bus_operator, selected_seat, trip_type, payment_method, total_price
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $insertStmt->bind_param("sissssssssd", 
                $row['ticket_number'], $row['user_id'], $row['from_location'], $row['to_location'], 
                $row['departure_date'], $row['return_date'], $row['bus_operator'], $row['selected_seat'], 
                $row['trip_type'], $paymentMethod, $totalPrice
            );

            if ($insertStmt->execute()) {
                // Prepare the DELETE statement
                $deleteStmt = $conn->prepare("DELETE FROM tickets WHERE ticket_number = ?");
                $deleteStmt->bind_param("s", $ticketId);

                if ($deleteStmt->execute()) {
                    // Commit transaction
                    $conn->commit();
                    echo json_encode(['status' => 'success']);
                } else {
                    // Rollback transaction on delete error
                    $conn->rollback();
                    echo json_encode(['status' => 'error', 'message' => 'Error deleting ticket: ' . $conn->error]);
                }

            } else {
                // Rollback transaction on insert error
                $conn->rollback();
                echo json_encode(['status' => 'error', 'message' => 'Error inserting into history: ' . $conn->error]);
            }

        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ticket not found']);
        }

    } catch (Exception $e) {
        // Rollback transaction on any exception
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Error processing payment: ' . $e->getMessage()]);
    } finally {
        // Close statements
        if (isset($selectStmt)) $selectStmt->close();
        if (isset($insertStmt)) $insertStmt->close();
        if (isset($deleteStmt)) $deleteStmt->close();
    }

} else {
    // Handle JSON decoding error or missing data
    echo json_encode(['status' => 'error', 'message' => 'Invalid request data']);
}

$conn->close(); 

?>