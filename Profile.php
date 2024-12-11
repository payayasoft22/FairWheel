<?php
// Include your database connection file
include 'db_connection.php'; // Adjust the path if necessary

// Start the session to use session variables
session_start();

// Retrieve user data from session
$user_id = $_SESSION['id'];  // Get user ID from session

// Fetch user data from the database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


// Handle form submission to update user data
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the new values from the form
    $fullName = $_POST['fullName'];
    $gender = $_POST['gender'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];

    // Prepare SQL query to update user profile in the database
    $updateStmt = $conn->prepare("UPDATE users SET full_name = ?, gender = ?, phone_number = ?, email = ? WHERE id = ?");
    $updateStmt->bind_param("ssssi", $fullName, $gender, $phoneNumber, $email, $user_id);

    // Execute the query and check if the update was successful
    if ($updateStmt->execute()) {
        echo "<script>alert('Profile updated successfully!');</script>";
        // Reload the page to reflect the changes
        header("Location: Profile.php"); 
        exit; // Ensure the script stops here
    } else {
        echo "<script>alert('Failed to update profile.');</script>";
    }

    // Close the update statement
    $updateStmt->close();
}

// Close the connection when done
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
        font-family: sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f4f4f4;
        box-sizing: border-box;
      }
      
      header {
        background-color: #333;
        color: #fff;
        padding: 10px 20px;
        text-align: center;
      }
      
      main {
        max-width: 800px;
        margin: 20px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      }
      
      .profile-container {
        display: flex;
        gap: 20px;
      }
      
      .sidebar {
        width: 250px; /* Adjust as needed */
      }
      
      .sidebar .tabs {
        flex-direction: column; 
      }
      
      .sidebar .tab-button {
        margin-bottom: 5px;
        width: 100%; 
        text-align: left; 
      }
      
      .content-area {
        flex-grow: 1; 
      }
      
      .tabs {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
      }
      
      .tab-button {
        background-color: #eee;
        border: none;
        padding: 10px 20px;
        margin: 0 5px;
        cursor: pointer;
        border-radius: 4px;
      }
      
      .tab-button.active {
        background-color: #ddd;
      }
      
      .tab-content {
        display: none; 
      }
      
      .tab-content.active {
        display: block;
      }
      
      h3 {
        margin-top: 0;
      }
      
      .container {
        max-width: 500px;
        margin: 20px auto;
      }
      
      label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
      }
      
      input[type="text"],
      input[type="email"],
      select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
      }
      
      .payment-buttons button {
        background-color: #007bff; 
        color: white;
        border: none;
        padding: 10px 20px;
        margin: 5px;
        border-radius: 4px;
        cursor: pointer;
      }
      
      .payment-details {
        margin-top: 20px;
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 4px;
        display: none; 
      }
      
      .payment-details.active {
        display: block;
      }
      
      .saved-payments {
        margin-top: 20px;
      }
      
      button {
        background-color: #28a745; 
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
      }
      
      button:hover {
        background-color: #218838; 
      }
      
      .logout-button {
        display: block;
        margin: 20px auto;
      }
      
      footer {
        text-align: center;
        margin-top: 20px;
        color: #777;
      }
      
     /* The Modal */
.logout-modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    background-color: rgba(0, 0, 0, 0.5); /* Black with opacity */
    padding-top: 100px;
}

/* Modal Content */
.logout-modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 300px;
    text-align: center;
}

/* The Close Button */
.logout-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

/* Hover effect for close button */
.logout-modal-close:hover,
.logout-modal-close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Logout and Cancel Buttons */
.confirm-logout-button, .cancel-logout-button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    margin: 10px 5px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
}

.cancel-logout-button {
    background-color: #f44336;
}

/* Hover effects for buttons */
.confirm-logout-button:hover {
    background-color: #45a049;
}

.cancel-logout-button:hover {
    background-color: #e53935;
}
.delete-btn {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    margin-left: 10px;
    border-radius: 5px;
}

.delete-btn:hover {
    background-color: #d32f2f;
}


      /* Media Queries for Responsiveness */
      @media (max-width: 768px) { 
        main {
          max-width: 95%; 
          margin: 10px auto; 
        }
      
        .profile-container {
          flex-direction: column; /* Stack vertically on smaller screens */
        }
      
        .sidebar {
          width: 100%; 
        }
      
        .tabs {
          flex-direction: column; 
          align-items: center; 
        }
      
        .tab-button {
          margin: 5px 0; 
          width: 80%; 
        }
      
        .container {
          max-width: 90%; 
        }
      
        .payment-details {
          margin-top: 10px; 
        }
      
        .modal-content {
          width: 80%; 
        }
      }
      
      @media (max-width: 480px) { 
        .modal-content {
          width: 95%; 
        }
      
        .payment-buttons button {
          padding: 8px 15px; 
          font-size: 14px; 
        }
      }

      input[readonly], select[disabled] {
    background-color: #f0f0f0;
    cursor: not-allowed;
}

.payment-buttons button {
            padding: 10px 20px;
            margin: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .payment-buttons button:hover {
            background-color: #45a049;
        }

        .payment-details {
            display: none;
            margin-top: 20px;
        }

        .payment-details input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .payment-details button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .payment-details button:hover {
            background-color: #45a049;
        }

        .saved-payments {
            margin-top: 30px;
        }

        .saved-payments h2 {
            margin-bottom: 10px;
        }

        #savedPaymentsList {
            margin-top: 10px;
        }

        .saved-payment {
            background-color: #f9f9f9;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
        }

        .saved-payments ul {
    list-style-type: none;
    padding: 0;
}

.saved-payments li {
    margin: 10px 0;
    font-size: 16px;
    color: #333;
}

.saved-payments h2 {
    font-size: 18px;
    margin-bottom: 10px;
}


    </style>
</head>
<body>

    <header>
        <h1>Profile Page</h1>
    </header>

    <main>
        <div class="tabs">
            <button class="tab-button" data-tab="personal-details">Personal Details</button>
            <button class="tab-button" data-tab="payment-method">Payment Method</button>
            <button class="tab-button" data-tab="history">History</button>
        </div>

        <div class="tab-content" id="personal-details">
    <h3>Personal Details</h3>

    <div class="container">
        <form id="profileForm" method="post">
            <div>
                <label for="fullName">Full Name:</label>
                <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
            </div>
            <div>
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" disabled>
                    <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
            <div>
                <label for="phoneNumber">Phone Number:</label>
                <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($user['phone_number']); ?>" readonly>
            </div>
            <div>
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            </div>

            <button type="button" id="editButton" onclick="toggleEdit()">EDIT</button>
            <button type="submit" id="saveButton" style="display: none;">SAVE</button>
        </form>
    </div>
</div>

<div class="tab-content" id="payment-method">
    <div class="container">
        <h1>Payment Methods</h1>

        <!-- Display saved payment methods -->
        <div class="saved-payments">
            <h2>Saved Payment Methods</h2>
            <!-- The saved payment methods will be dynamically displayed here -->
        </div>
    </div>

    <div class="payment-buttons">
        <button id="gcashBtn">GCash</button>
        <button id="cardBtn">Card</button>
        <button id="paypalBtn">PayPal</button>
        <button id="paymayaBtn">PayMaya</button> <!-- PayMaya button -->
    </div>

    <!-- GCash Details -->
    <div id="gcashDetails" class="payment-details">
        <h2>GCash Details</h2>
        <form id="gcashForm">
            <label for="gcashNumber">GCash Number</label>
            <input type="text" id="gcashNumber" name="gcashNumber" required placeholder="Enter GCash number">
            <button type="submit">Save GCash</button>
        </form>
    </div>

    <!-- Card Details -->
    <div id="cardDetails" class="payment-details">
        <h2>Card Details</h2>
        <form id="cardForm">
            <label for="cardNumber">Card Number</label>
            <input type="text" id="cardNumber" name="cardNumber" required placeholder="Enter card number">

            <label for="cardName">Cardholder Name</label>
            <input type="text" id="cardName" name="cardName" required placeholder="Enter cardholder name">

            <label for="cardExpiry">Expiry Date (MM/YY)</label>
            <input type="text" id="cardExpiry" name="cardExpiry" required placeholder="MM/YY">

            <label for="cardCVV">CVV</label>
            <input type="text" id="cardCVV" name="cardCVV" required placeholder="Enter CVV">

            <button type="submit">Save Card</button>
        </form>
    </div>

    <!-- PayPal Details -->
    <div id="paypalDetails" class="payment-details">
        <h2>PayPal Details</h2>
        <form id="paypalForm">
            <label for="paypalEmail">PayPal Email</label>
            <input type="email" id="paypalEmail" name="paypalEmail" required placeholder="Enter PayPal email">
            <button type="submit">Save PayPal</button>
        </form>
    </div>

    <!-- PayMaya Details -->
    <div id="paymayaDetails" class="payment-details">
        <h2>PayMaya Details</h2>
        <form id="paymayaForm">
            <label for="paymayaNumber">PayMaya Number</label>
            <input type="text" id="paymayaNumber" name="paymayaNumber" required placeholder="Enter PayMaya number">
            <button type="submit">Save PayMaya</button>
        </form>
    </div>
</div>

        <div class="tab-content" id="history">
            <h3>History</h3>
            <p>Here is your purchase history...</p>
        </div>

        <button id="logoutButton" class="logout-button">Logout</button>
        <div id="logoutModal" class="logout-modal">
    <div class="logout-modal-content">
        <span class="logout-modal-close" onclick="closeModal()">&times;</span>
        <h3>Are you sure you want to logout?</h3>
        <button id="confirmLogout" class="confirm-logout-button">Yes, Logout</button>
        <button onclick="closeModal()" class="cancel-logout-button">Cancel</button>
    </div>
    </main>

    <footer>
        <p>&copy; 2024 Your Website Name</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const tabs = document.querySelectorAll(".tab-button");
            const contents = document.querySelectorAll(".tab-content");

            tabs.forEach((tab) => {
                tab.addEventListener("click", () => {
                    // Remove active classes
                    tabs.forEach((t) => t.classList.remove("active"));
                    contents.forEach((c) => c.classList.remove("active"));

                    // Add active class to current tab and its content
                    tab.classList.add("active");
                    const tabId = tab.getAttribute("data-tab");
                    document.getElementById(tabId).classList.add("active");
                });
            });

            // Activate the first tab and content by default
            tabs[0].classList.add("active");
            contents[0].classList.add("active");
        });

       // Get the modal
var modal = document.getElementById("logoutModal");

// Get the logout button
var logoutButton = document.getElementById("logoutButton");

// Get the confirm logout button
var confirmLogoutButton = document.getElementById("confirmLogout");

// When the user clicks the logout button, open the modal
logoutButton.onclick = function() {
    modal.style.display = "block";
}

// Close the modal when the user clicks on <span> (x)
function closeModal() {
    modal.style.display = "none";
}

// When the user clicks "Yes, Logout", perform logout action
confirmLogoutButton.onclick = function() {
    // Your logout logic here (e.g., clearing session, redirecting to login page)
    window.location.href = 'logout.php'; // Redirect to a logout page or handle logout as per your needs
}




function toggleEdit() {
    // Toggle readonly and disabled states for the form fields
    const fullName = document.getElementById('fullName');
    const gender = document.getElementById('gender');
    const phoneNumber = document.getElementById('phoneNumber');
    const email = document.getElementById('email');
    const editButton = document.getElementById('editButton');
    const saveButton = document.getElementById('saveButton');

    if (fullName.readOnly) {
        // Enable editing
        fullName.readOnly = false;
        gender.disabled = false;
        phoneNumber.readOnly = false;
        email.readOnly = false;

        // Show SAVE button and hide EDIT button
        saveButton.style.display = 'inline-block';
        editButton.style.display = 'none';
    } else {
        // Disable editing (if needed)
        fullName.readOnly = true;
        gender.disabled = true;
        phoneNumber.readOnly = true;
        email.readOnly = true;

        // Hide SAVE button and show EDIT button
        saveButton.style.display = 'none';
        editButton.style.display = 'inline-block';
    }
}

document.addEventListener("DOMContentLoaded", function () {
    // Get the buttons, form sections, and the saved payments container
    const gcashBtn = document.getElementById("gcashBtn");
    const cardBtn = document.getElementById("cardBtn");
    const paypalBtn = document.getElementById("paypalBtn");
    const paymayaBtn = document.getElementById("paymayaBtn");

    const gcashDetails = document.getElementById("gcashDetails");
    const cardDetails = document.getElementById("cardDetails");
    const paypalDetails = document.getElementById("paypalDetails");
    const paymayaDetails = document.getElementById("paymayaDetails");

    const savedPaymentsContainer = document.querySelector(".saved-payments");

    // Hide all forms initially
    const allDetails = document.querySelectorAll(".payment-details");
    allDetails.forEach(detail => detail.style.display = "none");

    // Function to reset all payment details
    function resetPaymentDetails() {
        allDetails.forEach(detail => {
            detail.style.display = "none";
        });
    }

    // Event listeners for payment method buttons
    gcashBtn.addEventListener("click", function () {
        resetPaymentDetails();
        gcashDetails.style.display = "block";
    });

    cardBtn.addEventListener("click", function () {
        resetPaymentDetails();
        cardDetails.style.display = "block";
    });

    paypalBtn.addEventListener("click", function () {
        resetPaymentDetails();
        paypalDetails.style.display = "block";
    });

    paymayaBtn.addEventListener("click", function () {
        resetPaymentDetails();
        paymayaDetails.style.display = "block";
    });

    // Function to mask sensitive information
    function maskSensitiveData(type, details) {
        if (type === "GCash" || type === "PayMaya") {
            return details.replace(/\d(?=\d{4})/g, "*");
        } else if (type === "Card") {
            return details.replace(/\d(?=\d{4})/g, "*");
        } else if (type === "PayPal") {
            const [username, domain] = details.split("@");
            const maskedUsername = username.charAt(0) + "*".repeat(username.length - 1);
            return maskedUsername + "@" + domain;
        }
        return details;
    }

    // Function to display saved payments
    function displaySavedPayments() {
        savedPaymentsContainer.innerHTML = "<h2>Saved Payment Methods</h2>";

        // Fetch saved payment methods from the backend
        fetch("payment.php", {
            method: "GET",
        })
            .then(response => response.json())
            .then(savedMethods => {
                if (savedMethods.length === 0) {
                    savedPaymentsContainer.innerHTML += "<p>No saved payment methods.</p>";
                } else {
                    const list = document.createElement("ul");
                    savedMethods.forEach(method => {
                        const listItem = document.createElement("li");
                        const maskedDetails = maskSensitiveData(method.type, method.details);

                        listItem.textContent = `${method.type}: ${maskedDetails}`;

                        list.appendChild(listItem);
                    });
                    savedPaymentsContainer.appendChild(list);
                }
            })
            .catch(error => console.error("Error fetching saved payments:", error));
    }

    // Event listeners for form submissions (store payment methods)
    document.getElementById("gcashForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const gcashNumber = document.getElementById("gcashNumber").value;

        // Send the data to the server
        fetch("payment.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ type: "GCash", details: gcashNumber }),
        })
            .then(response => response.json())
            .then(data => {
                displaySavedPayments();  // Re-render the saved payments list
                resetPaymentDetails();   // Hide the form after saving
            })
            .catch(error => console.error("Error saving GCash payment:", error));
    });

    document.getElementById("cardForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const cardNumber = document.getElementById("cardNumber").value;
        const cardName = document.getElementById("cardName").value;
        const cardExpiry = document.getElementById("cardExpiry").value;
        const cardCVV = document.getElementById("cardCVV").value;

        // Send the data to the server
        fetch("payment.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                type: "Card",
                details: `Card Number: ${cardNumber}, Expiry: ${cardExpiry}`,
            }),
        })
            .then(response => response.json())
            .then(data => {
                displaySavedPayments();  // Re-render the saved payments list
                resetPaymentDetails();   // Hide the form after saving
            })
            .catch(error => console.error("Error saving card payment:", error));
    });

    document.getElementById("paypalForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const paypalEmail = document.getElementById("paypalEmail").value;

        // Send the data to the server
        fetch("payment.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ type: "PayPal", details: paypalEmail }),
        })
            .then(response => response.json())
            .then(data => {
                displaySavedPayments();  // Re-render the saved payments list
                resetPaymentDetails();   // Hide the form after saving
            })
            .catch(error => console.error("Error saving PayPal payment:", error));
    });

    document.getElementById("paymayaForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const paymayaNumber = document.getElementById("paymayaNumber").value;

        // Send the data to the server
        fetch("payment.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ type: "PayMaya", details: paymayaNumber }),
        })
            .then(response => response.json())
            .then(data => {
                displaySavedPayments();  // Re-render the saved payments list
                resetPaymentDetails();   // Hide the form after saving
            })
            .catch(error => console.error("Error saving PayMaya payment:", error));
    });

    // Initial call to display saved payments on page load
    displaySavedPayments();
});

    </script>
</body>
</html>
