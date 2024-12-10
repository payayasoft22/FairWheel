<?php
session_start(); // Start the session

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['id']); // Or check any other session variable

// Return the login status as JSON
echo json_encode(['isLoggedIn' => $isLoggedIn]); 
?>