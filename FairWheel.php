<?php
// Enable error reporting (for debugging only; disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database connection parameters
$host = 'localhost';
$dbname = 'fairwheel_db';
$username = 'root';
$password = '';

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    // Handle getting tickets
    if (isset($data['action']) && $data['action'] === 'get_tickets') {
        // 1. Check if the user is logged in (using your session)
        if (!isset($_SESSION['email'])) {
            echo json_encode(['success' => false, 'error' => 'User not logged in']);
            exit;
        }

        // 2. Get the user's ID from the session
        $user_id = $_SESSION['id'];

        // 3. Prepare and execute the SQL query to fetch the user's tickets
        $stmt = $conn->prepare("SELECT * FROM tickets WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // 4. Fetch the tickets and format them into an array
        $tickets = [];
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }

        // 5. Return the tickets as a JSON response
        echo json_encode(['success' => true, 'tickets' => $tickets]);
        exit;
    }

    // Handle booking a ticket
    if (isset($data['action']) && $data['action'] === 'book_ticket') {
        // Check if the user is logged in
        if (!isset($_SESSION['email'])) {
            echo json_encode(['success' => false, 'error' => 'User not logged in']);
            exit;
        }

        // Get the user's ID from the session
        $user_id = $_SESSION['id'];

        // Validate booking form data (assuming data is sent as JSON)
        $trip_type = $data['trip_type'] ?? '';
        $from_location = $data['from_location'] ?? '';
        $to_location = $data['to_location'] ?? '';
        $departure_date = $data['departure_date'] ?? '';
        $return_date = $data['return_date'] ?? '';
        $passengers = $data['passengers'] ?? 1; // Default to 1 passenger if not specified

        // Validate required fields
        if (empty($trip_type) || empty($from_location) || empty($to_location) || empty($departure_date) || empty($passengers)) {
            echo json_encode(['success' => false, 'error' => 'Please provide all required fields']);
            exit;
        }

        // Insert the ticket booking into the database
        $stmt = $conn->prepare("INSERT INTO tickets (user_id, trip_type, from_location, to_location, departure_date, return_date, passengers) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssi", $user_id, $trip_type, $from_location, $to_location, $departure_date, $return_date, $passengers);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Booking successful']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error booking the ticket. Please try again.']);
        }

        $stmt->close();
        exit;
    }

    // Validate email and password fields (for login)
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Email and password are required.']); 
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email format.']); 
        exit;
    }

    // Prepare and execute SQL query
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        die("SQL Error: " . $stmt->error);
    }

    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Successful login: set session variables
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $id;

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Return JSON success response with redirect information
            echo json_encode(['success' => true, 'redirect' => 'FairWheel.php']); 
            exit(); 
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid email or password. Please try again.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid email or password. Please try again.']);
    }

    // Close statement
    $stmt->close();
}

// Close database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&family=Inria+Serif:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="FairWheel.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <title>FairWheel</title>
    <link rel="icon" href="window_logo.png" type="image/x-icon" />
    </head>
<body>
    <header>
        <img src="header logo.png" alt="header-pic" class="head-pic">
        <ul class="navlist">
            <li><a href="Routes.html">Routes</a></li>
            <li><a href="Tickets.html">My Ticket</a></li>
            <li><a href="Contact-us.php">Contact Us</a></li>
            <div class="Sign-in" id="authButtons">
                <?php if (!isset($_SESSION['email'])): // User not logged in ?>
                    <li><button class="button-sign" id="LoginButton" onclick="window.location.href='LoginFrom.html'">Sign in</button></li>
                    <li><button class="button-register" onclick="window.location.href='Registration.php';">Register</button></li>
                <?php else: // User is logged in ?>
                    <li class="account-item">
    <button class="account-circle" onclick="redirectToProfile()">
        <i class="ri-account-circle-fill"></i>
    </button>
</li>



<style>

/* Styling the button */
.account-circle {
    margin: 0;
    background-color: transparent;
    border: none;
    cursor: pointer; /* Change cursor to pointer */
    bottom: 9px;
    border-radius: 50%; 
    position: relative;
    transition: transform 0.2s ease, background-color 0.3s ease; /* Add smooth hover effects */
    width: 70px; /* Explicit size for consistent scaling */
    display: flex; /* Center icon */
    align-items: center; 
    justify-content: center;
}

/* Add hover effect to the button */
.account-circle:hover {
    transform: scale(1.2); 
}

/* Styling the icon */
.account-circle i {
    font-size: 35px;
    color: black; 
    transition: color 0.3s ease; 
}

/* Change icon color on hover */
.account-circle:hover i {
    color: #50C878; 
}
                
</style>
                <?php endif; ?>
            </div>
        </ul>
        
       

<div id="resetPasswordModal" class="modals" style="display: none;">
    <div class="modalcontent">
        <div class="close" id="closeResetPasswordModal">
            <i class="ri-close-fill"></i>
        </div>
        <div class="logo">
            <h2>Reset Your Password</h2>
        </div>
        <form>
            <p>Enter your registered email, and we'll send you a link to reset your password.</p>
            <input type="email" placeholder="Email" required>
            <button type="submit">Send Reset Link</button>
        </form>
    </div>
</div>

        <div class="menu-line" id="menu-icon">
            <div class="fas fa-bars" id="menu-icon"></div>
        </div>
    </header>
    <div class="inside-text">
        <p>Reserve, Relax, Repeat.</p>
    </div>
    <div class="items">
        <div class="Radios">
            <label class="custom-radio">
                <input type="radio" name="radio-choice" value="One-way"> One Way
            </label>
            <label class="custom-radio">
                <input type="radio" name="radio-choice" value="round-trip" checked> Round Trip
            </label>
        </div>
        <div class="Location-picker">
    <select id="fromLocation" onchange="updateToLocationOptions()">
        <option value="" disabled selected>From:</option>
        <option value="Manila">Manila</option>
        <option value="Batangas">Batangas</option>
        <option value="Baguio">Baguio</option>
        <option value="Mindoro">Mindoro</option>
        <option value="Bicol">Bicol</option>
    </select>
</div>
<i class="ri-arrow-left-right-line"></i>
<i class="ri-arrow-up-down-line"></i>
<div class="location-picker">
    <select id="toLocation" onchange="updateFromLocationOptions()">
        <option value="" disabled selected>To:</option>
        <option value="Manila">Manila</option>
        <option value="Batangas">Batangas</option>
        <option value="Baguio">Baguio</option>
        <option value="Mindoro">Mindoro</option>
        <option value="Bicol">Bicol</option>
    </select>
</div>
        <i class="ri-calendar-line"></i>
        <div class="date-picker">
            <p>Departure Date</p>
            <input type="date" id="dates" onchange="setMinReturnDate()" onclick="this.showPicker()">
</div>
        <div class="Date-picker">
            <p>Return Date</p>
            <input type="date" id="date" onclick="this.showPicker()">
        </div>
        <div class="passenger-number">
            <i class="ri-user-line"></i>
            <p>No. of Passengers</p>
            <input type="number" id="passenger-number" min="1" max="20" value="1">
          
        </div>
        <div class="Book-button">
            <button type="submit" value="Book" onclick="handelBooking()">Book</button>
        </div>


    </div>
    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <p>&copy; 2024 FairWheel. All rights reserved.</p>
            </div>
            <div class="footer-middle">
                <p>Contact Us:</p>
                <p>Email: info@fairwheel.com | Phone: +123 456 7890</p>
            </div>
            <div class="footer-right">
                <p>Follow Us:</p>
                <a href="https://facebook.com" target="_blank">Facebook</a> |
                <a href="https://twitter.com" target="_blank">Twitter</a> |
                <a href="https://instagram.com" target="_blank">Instagram</a>
            </div>
        </div>
        <script src="FairWheel.js"></script>
        <script>
     document.addEventListener("DOMContentLoaded", () => {
  // Function to check if the user is logged in
function isLoggedIn() {
  // Check if a session variable (e.g., 'email') is set
  return <?php echo isset($_SESSION['email']) ? 'true' : 'false'; ?>; 
}

// Function to handle booking (updated)
async function handelBooking() {
  if (!isLoggedIn()) {
    alert("Please log in first to book tickets.");
    window.location.href = "LoginFrom.html"; 
    return;
  }

  // ... (rest of your booking logic) ...
}

// Add event listeners to "My Ticket" and "Book" buttons
const ticketsButton = document.querySelector('a[href="Tickets.html"]'); // Select the "My Ticket" link
const bookButton = document.querySelector(".Book-button button"); 

if (ticketsButton) {
  ticketsButton.addEventListener("click", (event) => {
    if (!isLoggedIn()) {
      event.preventDefault(); // Prevent default link behavior
      alert("Please log in first to view your tickets.");
      window.location.href = "LoginFrom.html"; 
    } 
  });
}

if (bookButton) {
  bookButton.addEventListener("click", handelBooking);
}


    function closeProfileModal() {
        const modal = document.getElementById("profileModal");
        if (modal) {
            modal.style.display = "none";
        }
    }

  
    function initializeModalHandlers() {
        const closeModalButton = document.getElementById("closeModal");
        const modal = document.getElementById("profileModal");

        if (closeModalButton) {
            // Attach event listener to the close button
            closeModalButton.onclick = closeProfileModal;
        }

        if (modal) {
            // Close modal when clicking outside of it
            window.onclick = function (event) {
                if (event.target === modal) {
                    closeProfileModal();
                }
            };
        }
    }

    /**
     * Redirect to Profile.html when the profile button is clicked.
     */
    function redirectToProfile() {
        window.location.href = "Profile.html";
    }

    /**
     * Logout and redirect to FairWheel.php.
     */
    function logout() {
        // Redirect to the logout script
        window.location.href = "logout.php";
    }

    // Initialize modal handlers
    initializeModalHandlers();

    // Attach click event to profile button
    const profileButton = document.querySelector(".account-circle");
    if (profileButton) {
        profileButton.addEventListener("click", redirectToProfile);
    }

    // Attach logout functionality
    const logoutButton = document.getElementById("logoutButton");
    if (logoutButton) {
        logoutButton.addEventListener("click", logout);
    }
});

    
    // Function to handle booking
    async function handelBooking(event) { // Add event parameter
   
  // 2. THEN, if logged in, get the form data
  const tripType = document.querySelector('input[name="radio-choice"]:checked')?.value;
  const fromLocation = document.querySelector('.Location-picker select')?.value;
  const toLocation = document.querySelector('.location-picker select')?.value;
  const departureDate = document.getElementById('dates')?.value;
  const returnDate = document.getElementById('date')?.value || '';
  const passengers = document.getElementById('passenger-number')?.value;

  // 3. Validate the form data
  if (!tripType || !fromLocation || !toLocation || !departureDate || passengers < 1) {
    alert('Please fill out all required fields.');
    return;
  } 

  // 4. Prepare the data for the fetch request
  const formData = {
    action: 'book',
    trip_type: tripType,
    from_location: fromLocation,
    to_location: toLocation,
    departure_date: departureDate,
    return_date: tripType === 'One-way' ? '' : returnDate,
    passengers: passengers,
  };

  // 5. Send the fetch request
  try {
    const response = await fetch('Tickets.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json', 
      },
      body: JSON.stringify(formData), 
    });

    if (!response.ok) throw new Error('Server error');

    const result = await response.json();

    if (result.success) {
      alert('Booking successful!');
      window.location.href = 'tickets.html'; 
    } else {
      alert('Booking failed: ' + (result.error || 'Unknown error.'));
    }
  } catch (error) {
    alert('An error occurred: ' + error.message);
  }
}


    // Function to close the modal
    function closeModal() {
        document.getElementById('loginModal').style.display = 'none';
    }

    // Function to simulate user login (you should implement your actual login logic)
    function loginUser() {
    // Simulate a successful login
    localStorage.setItem('user', 'exampleUser'); // Store user in local storage

    // Close the login form modal
    closeModal();

    // Notify the user
    alert('You are now logged in!');

    // Optionally: You can redirect to the page the user was trying to access after logging in
    window.location.href = 'FairWheel.php';  // Uncomment and adjust this line if needed
}

function updateToLocationOptions() {
        const fromLocation = document.getElementById('fromLocation');
        const toLocation = document.getElementById('toLocation');
        
        const selectedFromValue = fromLocation.value;

        Array.from(toLocation.options).forEach(option => {
            // Disable "From:" placeholder and selected location in "To"
            if (option.value === selectedFromValue || option.value === "") {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        });
    }

    function updateFromLocationOptions() {
        const fromLocation = document.getElementById('fromLocation');
        const toLocation = document.getElementById('toLocation');

        const selectedToValue = toLocation.value;

        Array.from(fromLocation.options).forEach(option => {
            // Disable "To:" placeholder and selected location in "From"
            if (option.value === selectedToValue || option.value === "") {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('#fromLocation option[value=""]').disabled = true;
        document.querySelector('#toLocation option[value=""]').disabled = true;
    });


    // Function to validate the passenger number
    const passengerInput = document.getElementById('passenger-number');
    const errorMessage = document.getElementById('error-message');

    // Function to validate the passenger number
    function validatePassengerNumber() {
        const value = parseInt(passengerInput.value, 10);


// Function to validate the passenger number

    if (value < 1 || value > 20) {
        alert("Please enter a number between 1 and 20."); // Alert message
        passengerInput.value = 1; // Reset to 1
    }
}

    // Add event listener to the input field
    passengerInput.addEventListener('input', validatePassengerNumber);

    function setMinReturnDate() {
            const departureDateInput = document.getElementById('dates');
            const returnDateInput = document.getElementById('date');
            const departureDate = new Date(departureDateInput.value);

            // Set the minimum return date to the day after the departure date
            if (departureDateInput.value) {
                departureDate.setDate(departureDate.getDate() + 1); // Add one day
                returnDateInput.min = departureDate.toISOString().split('T')[0]; // Set min attribute
            } else {
                returnDateInput.min = ''; // Reset min if no departure date is selected
            }
        }
    </script>
    </footer>
</body>
</html>