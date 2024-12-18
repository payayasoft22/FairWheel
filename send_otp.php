<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

// Get Email from JSON Request
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? null;

if (!$email) {
    echo json_encode(["success" => false, "error" => "Email is required to send OTP."]);
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['otp_email'] = $email;
$_SESSION['otp_expires'] = time() + 300; // OTP expires in 5 minutes

// Send OTP Email
$mail = new PHPMailer(true);

try {
    // Server Settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'fairwheel.ph@gmail.com'; 
    $mail->Password = 'dhyevabsbphfgwln';  
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Email Content
    $mail->setFrom('fairwheel.ph@gmail.com', 'FairWheel Verification Code,');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body = "<p>From FairWheel Team,</p><p>Your OTP code is <strong>$otp</strong>.</p><p>This code is valid for 5 minutes.</p>";

    $mail->send();
    echo json_encode(["success" => true, "message" => "OTP sent to $email"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Failed to send OTP. Error: " . $mail->ErrorInfo]);
}
