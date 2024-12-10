<?php
function function_alert($message) {

    echo "<script>alert('$message'); window.location.href = 'FairWheel.html';</script>";  // Redirect to new page after alert
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }


    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        die("Invalid phone number format. Use only digits, 10-15 characters long.");
    }


    $servername = "localhost"; 
    $username = "root";        
    $password = "";           
    $dbname = "fairwheel_db"; 


    $conn = new mysqli($servername, $username, $password, $dbname);


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $stmt = $conn->prepare("INSERT INTO concerns (name, email, phone, message) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing the SQL statement: " . $conn->error);
    }

    $stmt->bind_param("ssss", $name, $email, $phone, $message);


    if ($stmt->execute()) {
        function_alert("Thank you for your message! We will get back to you soon.");
    } else {
        echo "Error: " . $stmt->error;
    }

   
    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="icon" href="window_logo.png" type="image/x-icon" />
    <link rel="stylesheet" href="Contact-us.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <a href="FairWheel.php">
            <i class="ri-arrow-left-line"></i>
        </a>
        <div class="left-side">
            <h2 id="message">Need Help?</h2>
            <p>Contact us for any questions or concerns.</p>
        </div>
        <div class="contact-form">
            <h2>Contact Us</h2>
            <form method="POST" action="contact-us.php">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Email" required>
                
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
                
                <label for="messageInput">Message:</label>
                <textarea id="messageInput" name="message" placeholder="What do you have in mind?" required></textarea>
                
                <button type="submit">Submit</button>
            </form>
            
        </div>
    </div>

    <script>
        const dynamicTextElement = document.getElementById('message');
        const messages = ['Welcome!', 'How can we assist you?', 'We’re here 24/7.', 'Contact us anytime!', 'Need Help?'];
        let index = 0;

        function updateText() {
            dynamicTextElement.classList.add('slide-out');
            setTimeout(() => {
                dynamicTextElement.textContent = messages[index];
                index = (index + 1) % messages.length;
                dynamicTextElement.classList.remove('slide-out');
                dynamicTextElement.classList.add('slide-in');
                setTimeout(() => dynamicTextElement.classList.remove('slide-in'), 500);
            }, 500);
        }
        setInterval(updateText, 2500);
    </script>
</body>

</html>