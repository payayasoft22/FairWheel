<?php

// Enable error reporting (for debugging only; disable in production)
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
    echo "<script>alert('Connection failed: " . $conn->connect_error . "');</script>";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate user input
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Check for empty fields
    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "<script>alert('All fields are required.');
        setTimeout(function() {
                    window.location.href = 'Registration.php';
                }, 2);</script>";
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');
        setTimeout(function() {
                    window.location.href = 'Registration.php';
                }, 2);</script>";
        exit;
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');
        setTimeout(function() {
                    window.location.href = 'Registration.php';
                }, 2);</script>";
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($stmt === false) {
        echo "<script>alert('Error preparing query: " . $conn->error . "');</script>";
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>
                alert('Email already exists. Please use a different email.');
                setTimeout(function() {
                    window.location.href = 'Registration.php';
                }, 2);
              </script>";
        $stmt->close();
        exit;
    }
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
    if ($stmt === false) {
        echo "<script>alert('Error preparing query: " . $conn->error . "');</script>";
        exit;
    }
    $stmt->bind_param("sss", $fullName, $email, $hashedPassword);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the desired page after successful registration
        echo "<script>alert('Registration successful! Redirecting to login page...'); window.location.href = 'FairWheel.php';</script>";
        exit; 
    } else {
        echo "<script>alert('Error executing query: " . $stmt->error . "');</script>";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="window_logo.png" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="Registration.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>FairWheel Registration</title>
</head>

<body>
    <div class="Left-Photo">
    </div>
    <div class="registration-form">
        <h2>Sign Up</h2>
        <form action="Registration.php" method="POST" id="registrationForm">


            <label for="fullName">Full Name:</label>
            <input type="text" id="fullName" name="fullName" placeholder="Enter Name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter Email" required>

            <label for="password">Password:</label>
            <div class="password-wrapper">
                <input type="password" id="Password" name="password" placeholder="Enter Password" required>
                <i class="fa fa-eye-slash" id="togglePassword" onclick="togglePasswordVisibility('Password', 'togglePassword')"></i>
            </div>

            <label for="confirmPassword">Confirm Password:</label>
            <div class="password-wrapper">
                <input type="password" id="ConfirmPassword" name="confirmPassword" placeholder="Re-Enter Password" required>
                <i class="fa fa-eye-slash" id="toggleConfirmPassword" onclick="togglePasswordVisibility('ConfirmPassword', 'toggleConfirmPassword')"></i>
            </div>
            <div id="passwordMessage" class="password-message"></div>

            <button type="submit">Register</button>
            <div class="Back_Login">
                <p>Already have an Account? <a href="FairWheel.php">Sign In</a></p>
            </div>
        </form>
    </div>
</body>

<script>
    function togglePasswordVisibility(passwordId, iconId) {
        const passwordInput = document.getElementById(passwordId);
        const passwordIcon = document.getElementById(iconId);

        if (passwordInput.type === "password") {
            passwordInput.type = "text"; 
            passwordIcon.classList.remove("fa-eye-slash"); 
            passwordIcon.classList.add("fa-eye"); 
        } else {
            passwordInput.type = "password"; 
            passwordIcon.classList.remove("fa-eye"); 
            passwordIcon.classList.add("fa-eye-slash"); 
        }
    }

    const passwordInput = document.getElementById('Password');
    const confirmPasswordInput = document.getElementById('ConfirmPassword');
    const passwordMessage = document.getElementById('passwordMessage');

    function checkPasswords() {
        if (passwordInput.value === confirmPasswordInput.value && passwordInput.value !== '') {
            passwordMessage.textContent = 'Passwords match.';
            passwordMessage.classList.remove('password-mismatch');
            passwordMessage.classList.add('password-match');
        } else {
            passwordMessage.textContent = 'Passwords do not match.';
            passwordMessage.classList.remove('password-match');
            passwordMessage.classList.add('password-mismatch');
        }
    }

    passwordInput.addEventListener('input', checkPasswords);
    confirmPasswordInput.addEventListener('input', checkPasswords);
</script>

</html>
