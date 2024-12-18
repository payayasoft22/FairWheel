<?php
session_start();

// Database Connection
$conn = new mysqli("localhost", "root", "", "fairwheel_db");

// Check Connection
if ($conn->connect_error) {
    die("Database connection failed.");
}

// Get Form Data
$fullName = $_POST['fullName'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$confirmPassword = $_POST['confirmPassword'];
$otp = $_POST['otp'];

// Verify Password Match
if ($_POST['password'] !== $_POST['confirmPassword']) {
    // Redirect with error message
    header("Location: Registration.html?message=" . urlencode("Passwords do not match.")); 
    exit();
}

// Validate OTP
if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_email']) || !isset($_SESSION['otp_expires'])) {
    header("Location: Registration.html?message=" . urlencode("OTP session data is missing.")); 
    exit();
}

if ($_SESSION['otp_email'] !== $email) {
    header("Location: Registration.html?message=" . urlencode("Email does not match the OTP email.")); 
    exit();
}

if ($_SESSION['otp'] !== (int)$otp) {
    header("Location: Registration.html?message=" . urlencode("Invalid OTP.")); 
    exit(); 
}

if (time() > $_SESSION['otp_expires']) {
    header("Location: Registration.html?message=" . urlencode("OTP has expired."));
    exit(); 
}

// Save User Data
$stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $fullName, $email, $password);

if ($stmt->execute()) {
    // Clear OTP Session Data
    unset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['otp_expires']);

    // Redirect with success message
    header("Location: FairWheel.php?message=" . urlencode("Registration successful!")); 
    exit();
} else {
    // Redirect with error message
    header("Location: FairWheel.php?message=" . urlencode("Failed to register user.")); 
    exit();
}
?>