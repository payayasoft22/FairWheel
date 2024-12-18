<?php
require 'vendor/autoload.php'; // Include PHPMailer
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fairwheel_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];

// Check if email exists
$query = $conn->prepare("SELECT id FROM users WHERE email = ?");
$query->bind_param('s', $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Email not registered.']);
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['email'] = $email;

// Send OTP email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
    $mail->SMTPAuth = true;
    $mail->Username = 'fairwheel.ph@gmail.com'; // Replace with your email
    $mail->Password = 'dhyevabsbphfgwln'; // Replace with your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('fairwheel.ph@gmail.com', 'FairWheel Support Team');
    $mail->addAddress($email);
    
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request';
    $mail->Body = "
    <html>
    <head>
        <title>Password Reset Request</title>
    </head>
    <body>
        <p>Hi there,</p>
        <p>You have requested to reset your password for your FairWheel account.</p>
        <p>Your OTP code is: <b>$otp</b></p>
        <p>Please enter this OTP on the FairWheel website to reset your password.</p>
        <p>If you did not request a password reset, please ignore this email.</p>
        <br>
        <p>Best regards,</p>
        <p>FairWheel Support Team</p>
    </body>
    </html>
    ";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'OTP sent successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
}
?>
