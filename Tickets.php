<?php
// Database Configuration
$host = 'localhost';
$dbname = 'fairwheel_db'; 
$username = 'root'; 
$password = ''; 

// Set response to JSON
header('Content-Type: application/json');

// Start session to manage user authentication
session_start();

// Establish Database Connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Handle GET Request: Fetch tickets for the logged-in user
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        try {
            $ticketId = $_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = :id");
            $stmt->execute(['id' => $ticketId]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ticket) {
                $ticket['occupied_seats'] = $ticket['occupied_seats'] ? explode(',', $ticket['occupied_seats']) : [];
                $ticket['selected_seat'] = $ticket['selected_seat'] ? explode(',', $ticket['selected_seat']) : [];
                echo json_encode($ticket);
            } else {
                echo json_encode(['success' => false, 'error' => 'Ticket not found.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to fetch ticket: ' . $e->getMessage()]);
        }
        exit;
    } else {
        if (!isset($_SESSION['id'])) { 
            echo json_encode(['success' => false, 'error' => 'User not logged in']);
            exit;
        }

        try {
            $user_id = $_SESSION['id']; 
            $stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tickets as &$ticket) {
                $ticket['occupied_seats'] = $ticket['occupied_seats'] ? explode(',', $ticket['occupied_seats']) : [];
                $ticket['selected_seat'] = $ticket['selected_seat'] ? explode(',', $ticket['selected_seat']) : [];
            }

            echo json_encode($tickets);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to fetch tickets: ' . $e->getMessage()]);
        }
        exit; 
    }
}

// Handle POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    error_log(print_r($data, true)); // Debugging

    $action = $data['action'] ?? null;

    if ($action === 'book') {
        $tripType = $data['trip_type'] ?? '';
        $fromLocation = $data['from_location'] ?? '';
        $toLocation = $data['to_location'] ?? '';
        $departureDate = $data['departure_date'] ?? '';
        $returnDate = $data['return_date'] ?? null; 
        $passengers = $data['passengers'] ?? 1;

        if (!$tripType || !$fromLocation || !$toLocation || !$departureDate || $passengers < 1) {
            echo json_encode(['success' => false, 'error' => 'Invalid input.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO tickets (ticket_number, trip_type, from_location, to_location, departure_date, passengers, return_date, user_id) 
                VALUES (:ticket_number, :trip_type, :from_location, :to_location, :departure_date, :passengers, :return_date, :user_id)
            ");
            $randomNumber = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $ticketNumber = 'TICKET NO.' . strtoupper(uniqid()) . '-' . $randomNumber;
            $stmt->execute([
                'ticket_number' => $ticketNumber,
                'trip_type' => $tripType,
                'from_location' => $fromLocation,
                'to_location' => $toLocation,
                'departure_date' => $departureDate,
                'passengers' => $passengers,
                'return_date' => $returnDate,
                'user_id' => $_SESSION['id'], 
            ]);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to book ticket: ' . $e->getMessage()]);
        }
        exit; 
    }

    if ($action === 'delete') {
        $ticketId = $data['id'] ?? null;

        if (!$ticketId) {
            echo json_encode(['success' => false, 'error' => 'Ticket ID is required to delete.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = :id");
            $stmt->execute(['id' => $ticketId]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to delete ticket: ' . $e->getMessage()]);
        }
        exit; 
    }

    if ($action === 'update') {
        $ticketId = $data['id'] ?? null;
        $updates = $data['updates'] ?? null;

        if (!$ticketId || !$updates) {
            echo json_encode(['success' => false, 'error' => 'Ticket ID and updates are required.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = :id");
            $stmt->execute(['id' => $ticketId]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ticket) {
                echo json_encode(['success' => false, 'error' => 'Ticket not found.']);
                exit;
            }

            // Update occupied_seats
            $occupiedSeats = $ticket['occupied_seats'] ? explode(',', $ticket['occupied_seats']) : [];
            foreach ($updates['seat'] as $seat) {
                if (in_array($seat, $occupiedSeats)) {
                    echo json_encode(['success' => false, 'error' => "Seat $seat is already occupied."]);
                    exit;
                }
                $occupiedSeats[] = $seat;
            }

            $occupiedSeatsString = implode(',', $occupiedSeats);
            $selectedSeat = implode(',', $updates['seat']);
            $busOperator = $updates['operator'];

            $stmt = $pdo->prepare("
                UPDATE tickets 
                SET occupied_seats = :occupied_seats, selected_seat = :selected_seat, bus_operator = :bus_operator 
                WHERE id = :id
            ");
            $stmt->execute([
                'occupied_seats' => $occupiedSeatsString,
                'selected_seat' => $selectedSeat,
                'bus_operator' => $busOperator,
                'id' => $ticketId,
            ]);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to update ticket: ' . $e->getMessage()]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Invalid action.']);
    exit;
}
?>
