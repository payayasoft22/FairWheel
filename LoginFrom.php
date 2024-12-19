<?php
// Start session to handle user data
session_start();

// Include Google API Client Library
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('75930271197-es6qhm3f3c0dvr4q0dnprvb414e6pei6.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/FairWheel/auth.php');
$client->addScope('email');

// Generate the login URL
$login_url = $client->createAuthUrl();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login to FairWheel</title>
  <link rel="icon" href="window_logo.png" type="image/x-icon" />
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  background-color: #ffffff;
}

.Bus {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 70vw;
  height: 100vh; 
  position: fixed;
  right: -5vw;
  top: 0;
  overflow: hidden; 
}

.Bus img {
  width: 100%; 
  height: auto;
}

.login-container {
  width: 40%;
  max-width: 500px;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 30px;
  background-color: #ffffff;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  border-radius: 10px;
  margin-right: auto;
  margin-left: 150px;
}

h2 {
  font-size: 2.5rem;
  font-weight: 900;
  margin-bottom: 20px;
}

form {
  width: 100%;
}

input[type="text"],
input[type="password"] {
  width: 100%;
  padding: 15px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1rem;
}

input[type="text"] {
  width: 94%;
}

.password-container {
  position: relative;
  display: flex;
  align-items: center;
}

.password-container i {
  position: absolute;
  right: 10px;
  cursor: pointer;
  font-size: 1.2rem;
}

button {
  width: 100%;
  padding: 15px;
  background-color: #8ef28e;
  border: none;
  border-radius: 25px;
  color: #000;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s;
}


button:hover {
  background-color:#5ea35e;
  color: #ffffff;
}

.remember-forgot {
  display: flex;
  justify-content: space-between;
  margin: 10px 0;
  font-size: 0.9rem;
}

.remember-forgot a {
  text-decoration: none;
  color: #000000;
  transition: color 0.3s; 
}


.remember-forgot a:hover {
  text-decoration: underline;
  color: #5ea35e;
}

.social-login {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin: 20px 0;
}

.social-login a {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: #f5f5f5;
  font-size: 1.5rem;
  color: #555;
  transition: background-color 0.3s, color 0.3s;
}


.social-login a:hover {
  background-color: #5ea35e; 
  color: #fff;
}


p {
  margin: 15px 0;
  font-size: 0.9rem;
  text-align: center;
}

p a {
  text-decoration: none;
  color: #000000;
  font-weight: bold;
}

p a:hover {
  text-decoration: underline;
  color: #5ea35e;
}
@media (max-width: 1530px){
  .login-container{
    margin-left: 0px;

  }
}
@media (max-width: 768px) {
  body {
    flex-direction: column;
    justify-content: flex-start;
  }

  .Bus {
    display: none;
  }

  .login-container {
    width: 70%;
    margin: 100px auto;
  }

  h2 {
    font-size: 2rem;
  }

  input[type="text"],
  input[type="password"] {
    padding: 12px;
  }

  button {
    
    padding: 12px;
    font-size: 0.9rem;
  }
}

@media (max-width: 480px) {
  h2 {
    font-size: 1.8rem;
  }

  .social-login a {
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
  }
}


.modal-dialog {
  max-width: 400px; 
  margin: 30px auto;
}


.modal-content {
  padding: 20px;
  border-radius: 8px; 
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}


.modal-header {
  border-bottom: none; 
  padding-bottom: 10px; 
  text-align: center;
}

.modal-title {
  font-size: 1.5rem; 
  font-weight: bold; 
  color: #007bff; 
}

.close {
  color: #007bff;
  font-size: 1.5rem;
}

.modal-body {
  padding: 20px; 
  text-align: center; 
}


#forgotPasswordForm {
  margin: 0 auto; 
  max-width: 300px;
}

.form-group {
  margin-bottom: 20px; 
}

.form-control {
  padding: 10px;
  font-size: 1rem; 
  border-radius: 5px; 
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
  transition: border 0.3s ease; 
}

.form-control:focus {
  border-color: #007bff; 
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

.btn-primary {
  width: 100%;
  padding: 10px;
  background-color: #007bff; 
  border: none;
  border-radius: 5px; 
  color: white;
  font-size: 1rem;
}

.btn-primary:hover {
  background-color: #0056b3;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .modal-dialog {
    max-width: 100%; 
    margin: 20px; 
  }

  .form-control {
    font-size: 0.9rem; 
  }
}

  </style>
</head>
<body>
  <div class="Bus">
    <img src="LoginPic.png">
</div>
<div class="login-container">
    <div class="logo">
        <h2>Welcome Back!</h2>
    </div>
    <form id="loginForm" method="POST" action="FairWheel.php">
        <input type="text" id="email" name="email" placeholder="Email" required>
        <div class="password-container">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <i class="fa fa-eye-slash" id="togglePassword" onclick="togglePasswordVisibility()"></i>
        </div>
        <div class="remember-forgot">
            <label>
                <input type="checkbox" id="rememberMeCheckbox"> Remember me?
            </label>
            <a href="forgot_pass.html">Forgot Password?</a>

        </div>
        <button type="submit">Login</button>
        <p>Or Continue With:</p>
        <div class="social-login">
          <a href="<?= $login_url ?>"><i class="fab fa-google"></i></a> 
        </div>
        <p>Don't Have Account? <a href="Registration.php">Sign Up</a></p>
    </form>
    <button onclick="window.location.href='FairWheel.php'">Back</button>
</div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>

      
      // Toggle password visibility
      function togglePasswordVisibility() {
        const password = document.getElementById("password");
        const toggleIcon = document.getElementById("togglePassword");
        if (password.type === "password") {
          password.type = "text";
          toggleIcon.classList.remove("fa-eye-slash");
          toggleIcon.classList.add("fa-eye");
        } else {
          password.type = "password";
          toggleIcon.classList.remove("fa-eye");
          toggleIcon.classList.add("fa-eye-slash");
        }
      }
      
      // Form submission handling
      const form = document.getElementById("loginForm");
      
      form.addEventListener("submit", async (e) => {
        e.preventDefault();
        
        const email = document.getElementById("email").value; 
        const password = document.getElementById("password").value;
        
        try {
          const response = await fetch("FairWheel.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
              email,
              password,
            }),
          });
          
          const result = await response.json();
          console.log(result); // Log the result to the console
          
          if (result.success) {
            // Redirect to FairWheel.php on success
            window.location.href = "FairWheel.php";
          } else {
            // Show error message
            alert(result.error || "Invalid login credentials.");
          }
        } catch (error) {
          console.error("Error:", error);
          alert("Invalid Pass or Email.");
        }
      });

      $(document).ready(function() {
      // Handle form submission
      $('#forgotPasswordForm').on('submit', function(event) {
        event.preventDefault(); // Prevent form from submitting traditionally

        // Get the email entered by the user
        var email = $('#email').val();

        // Perform validation (basic email check)
        if (email === '') {
          alert('Please enter your email address.');
        } else if (!validateEmail(email)) {
          alert('Please enter a valid email address.');
        } else {
          // Simulate sending email
          alert('A password reset link has been sent to ' + email);
          $('#forgotPasswordModal').modal('hide'); // Close the modal after submission
        }
      });

      // Simple email validation function
      function validateEmail(email) {
        var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        return regex.test(email);
      }
    });
    </script>
  </body>
  </html>