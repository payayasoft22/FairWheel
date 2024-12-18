<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$otp = $data['otp'];

if (!isset($_SESSION['otp']) || $otp != $_SESSION['otp']) {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
    exit;
}

// OTP is valid
echo json_encode(['success' => true, 'message' => 'OTP verified successfully.']);
?>
