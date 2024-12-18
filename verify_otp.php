<?php
// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "fairwheel_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $otp = trim($_POST['otp']);

    // Validate input
    if (empty($email) || empty($otp)) {
        echo json_encode(["status" => "error", "message" => "Email or OTP is missing."]);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit;
    }

    // Retrieve the OTP and expiry time from the database for the given email
    $stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE email = ?");
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Error preparing the query."]);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "Email not found."]);
        exit;
    }

    $stmt->bind_result($storedOtp, $otpExpiry);
    $stmt->fetch();

    // Check if OTP matches and is still valid (i.e., not expired)
    $currentDateTime = new DateTime();
    $expiryDateTime = new DateTime($otpExpiry);

    if ($otp !== $storedOtp) {
        echo json_encode(["status" => "error", "message" => "Invalid OTP."]);
    } elseif ($currentDateTime > $expiryDateTime) {
        echo json_encode(["status" => "error", "message" => "OTP has expired."]);
    } else {
        echo json_encode(["status" => "success", "message" => "OTP is valid."]);
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
