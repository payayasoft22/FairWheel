<?php

session_start();

// Include  Google API client library
require_once 'vendor/autoload.php'; 

// Configure your Google API credentials
$client = new Google_Client();
$client->setClientId('75930271197-es6qhm3f3c0dvr4q0dnprvb414e6pei6.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/FairWheel/auth.php');  
$client->addScope('email');
$client->addScope('profile');

// Generate the login URL
$login_url = $client->createAuthUrl();

// Handle the Google authentication callback
if (isset($_GET['code'])) {
    // Fetch access token using the code
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    // Set the access token
    $client->setAccessToken($token);
    
    // Get the user's profile information
    $google_oauth = new Google_Service_Oauth2($client);
    $user_info = $google_oauth->userinfo->get();
    
    $email = $user_info->email;
    $name = $user_info->name;
    
    // Store user information in session for use in other pages (optional)
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $name;
    
    // Redirect to the welcome page or dashboard
    header('Location: welcome.php');
    exit;
}

// Check if the user is already logged in
if (isset($_SESSION['email']) && isset($_SESSION['name'])) {
    echo "Welcome, " . $_SESSION['name'];
} else {
    // Display a login link if not logged in
    echo "<a href='$login_url'>Login with Google</a>";
}
?>
