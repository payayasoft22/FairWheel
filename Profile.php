<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fairwheel_db";


session_start();

try {
    // Database connection using PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle profile update form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['fullName'])) {
        // Retrieve submitted form data
        $fullName = $_POST['fullName'];
        $gender = $_POST['gender'];
        $phoneNumber = $_POST['phoneNumber'];
        $email = $_POST['email'];
        $userId = $_SESSION['id']; 

        // Update query to modify user details
        $sql = "UPDATE users SET full_name = ?, gender = ?, phone_number = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$fullName, $gender, $phoneNumber, $email, $userId]);
    }

    // Fetch the logged-in user's details
    $userId = $_SESSION['id']; 
    $sql = "SELECT full_name, email, gender, phone_number FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Set variables for user details if fetched successfully
    if ($user) {
        $fullName = $user['full_name'];
        $email = $user['email'];
        $gender = $user['gender'];
        $phoneNumber = $user['phone_number'];
    }

    // Fetch the travel history for the logged-in user
    $sql = "SELECT ticket_number, from_location, to_location, departure_date, status, payment_method FROM history WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Display error if database operations fail
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
   * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f8f8f8;
    color: black;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.container {
    width: 95%;
    height: 100%;
    padding: 20px;
    margin: 20px auto;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.user-icon {
    width: 50px;
    height: 50px;
    background-color: #a7d1a8;
    border-radius: 50%;
    margin-right: 15px;
}

.user-details {
    flex-grow: 1;
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 18px;
}

.edit-info {
    text-align: right;
    font-size: small;
    cursor: pointer;
    margin-left: 10px;
    color: #4CAF50;
    font-weight: bold;
    font-size: 18px;
    transition: color 0.3s ease, text-decoration 0.3s ease;
}

.edit-info:hover {
    color: #45a049;
    text-decoration: underline;
}

#paymentOptions {
    display: none;
    padding: 15px;
    background-color: #f9f9f9;
    max-width: 500px;
    margin: 0 auto;
    font-family: Arial, sans-serif;
    color: black;
    gap: 20px;
}

.form-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

label {
    display: block;
    margin: 10px 0 5px;
    font-size: 18px;
}

input,
select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 20px;
    font-size: 15px;
}

button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 20px;
    cursor: pointer;
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.add-payment {
    display: flex;
    justify-content: center;
}

button:hover {
    background-color: #45a049;
}

.saved-payments {
    flex: 1;
    background-color: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.saved-payments h3 {
    margin-top: 0;
    font-size: 18px;
}

.saved-payments ul {
    list-style: none;
    padding: 0;
}

.saved-payments ul li {
    margin: 10px 0;
    font-size: 14px;
}

.payment-section {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.add-payment {
    flex: 0 0 auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

table thead {
    background-color: #f1f1f1;
}

table th,
table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: center;
}

table th {
    font-weight: bold;
    text-transform: uppercase;
}

table tbody tr:hover {
    background-color: #f9f9f9;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    width: 100%;
    max-width: 500px;
    text-align: center;
}

.modal-content h3 {
    margin-bottom: 20px;
}

.modal-content ul {
    list-style: none;
    padding: 0;
}

.modal-content ul li {
    margin: 10px 0;
}

.tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.tab-button {
    padding: 10px 15px;
    font-size: 14px;
    border: none;
    background-color: #eee;
    cursor: pointer;
    border-radius: 5px;
}

.tab-button.active {
    background-color:#4CAF50;
    color: white;
}

footer {
    text-align: center;
    padding: 10px;
    background-color: #f1f1f1;
}

#addPaymentBtn {
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 20px;
}

#addPaymentBtn:hover {
    background-color: rgb(0, 255, 85);
}

.form-buttons {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

button[type="submit"],
button[type="button"] {
    padding: 8px 16px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
}

button[type="submit"] {
    background-color: #4CAF50;
    color: white;
}

#cancel {
    background-color: #f44336;
    color: white;
}

.delete-button {
    float: right;
    background-color: #ff4d4d;
    color: white;
    border: none;
    border-radius: 20px;
    padding: 10px 15px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.delete-button:hover {
    background-color: #e60000;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    .container {
        width: 100%;
        margin: 20px 0;
        padding: 10px;
    }

    .user-info {
        flex-direction: column;
        align-items: flex-start;
    }

    .user-details {
        font-size: 16px;
    }

    .user-icon {
        margin-bottom: 10px;
    }

    .edit-info {
        margin-top: 10px;
        text-align: left;
    }

    label {
        font-size: 16px;
    }

    input,
    select {
        font-size: 16px;
        padding: 12px;
    }

    button {
        width: 100%;
        padding: 12px;
        font-size: 16px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    table th,
    table td {
        font-size: 14px;
        padding: 8px;
    }

    table {
        font-size: 14px;
        width: 100%;
    }

    .payment-section {
        flex-direction: column;
    }

    .modal-content {
        width: 90%;
        max-width: 350px;
    }

    footer {
        font-size: 14px;
        padding: 12px 0;
    }
}
.history-record {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    position: relative;
}

.history-record input[type="checkbox"] {
    position: absolute;
    top: 16px;
    left: 16px;
    transform: scale(1.2);
    margin-right: 10px;
}

.delete-btn-container {
    margin-top: 16px;
    text-align: center;
}

.btn-delete {
    padding: 10px 20px;
    background-color: #d9534f;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-delete:hover {
    background-color: #c9302c;
}

  /* Add Payment Button */
  .add-payment {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }

        #addPaymentBtn {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #addPaymentBtn:hover {
            background-color: #45a049;
        }

        /* Payment Options Buttons */
        #paymentOptions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin: 1rem auto;
            max-width: 600px;
        }

        #paymentOptions button {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #paymentOptions button:hover {
            background-color: #45a049;
        }

        /* Payment Forms Container */
        .payment-forms {
            display: flex;
            justify-content: center;
            margin: 1rem auto;
        }

        .payment-details {
            display: none; 
            background: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
            animation: fadeIn 0.5s ease-in-out;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 14px;
            font-weight: bold;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .form-buttons button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-buttons button[type="submit"] {
            background-color: #4CAF50;
        }

        .form-buttons button[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-buttons button#cancel {
            background-color: #FF5252;
        }

        .form-buttons button#cancel:hover {
            background-color: #E53935;
        }

     
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            #paymentOptions {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            #paymentOptions button {
                width: 80%;
                text-align: center;
            }

            .payment-details {
                padding: 1rem;
                width: 90%;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            #addPaymentBtn {
                width: 80%;
                font-size: 14px;
            }

            h2 {
                font-size: 1.2rem;
            }

            .form-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .form-buttons button {
                width: 100%;
            }
        }

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}


.modal-content {
    background-color: #fff;
    margin: 15% auto; 
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 90%; 
    max-width: 400px; 
    text-align: center;
    animation: modal-fade-in 0.5s; 
}


@keyframes modal-fade-in {
    from {
        transform: translateY(-30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}


.close-btn:hover,
.close-btn:focus {
    color: #f44336;
}


h2 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: #333;
}


.modal-buttons {
    display: flex;
    justify-content: center;
    gap: 10px; 
    margin-top: 20px;
}


.modal-buttons button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}


.confirm-logout-btn {
    background-color: #f44336;
    color: white;
}

.confirm-logout-btn:hover {
    background-color: #d32f2f; 
}


.cancel-btn {
    background-color: #9e9e9e; 
    color: white;
}

.cancel-btn:hover {
    background-color: #757575; 
}


@media screen and (max-width: 600px) {
    .modal-content {
        margin: 20% auto; 
        padding: 15px;
        width: 90%; 
    }

    h2 {
        font-size: 1.2rem;
    }

    .modal-buttons button {
        padding: 8px 10px;
        font-size: 0.9rem;
    }
}

</style>
</head>
<div class="container">
    <div class="user-info">
      <div class="user-details">
      <div id="displayName">Hello!  <?php echo isset($fullName) ? $fullName : '[display name]'; ?></div>
      </div>
      <div class="edit-info" onclick="toggleEditForm()">Edit Information</div>
    </div>

    <div class="details-form" id="editForm" style="display: none;">
      <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
          <label for="fullName">Name:</label>
          <input type="text" id="fullName" name="fullName" 
                 value="<?php echo isset($fullName) ? $fullName : '[display name]'; ?>">
        </div>
        <div>
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" 
                 value="<?php echo isset($email) ? $email : '[email]'; ?>">
        </div>
        <div>
          <label for="gender">Gender:</label>
          <select id="gender" name="gender">
            <option value="">Select</option>
            <option value="male" <?php if (isset($gender) && $gender === 'male') echo 'selected'; ?>>Male</option>
            <option value="female" <?php if (isset($gender) && $gender === 'female') echo 'selected'; ?>>Female</option>
            <option value="other" <?php if (isset($gender) && $gender === 'other') echo 'selected'; ?>>Other</option>
          </select>
        </div>
        <div>
          <label for="phone">Phone:</label>
          <input type="tel" id="phoneNumber" name="phoneNumber" 
                 value="<?php echo isset($phoneNumber) ? $phoneNumber : ''; ?>">
        </div>
        </form>

        <div class="payment-section">
    <div class="saved-payments">
        <h3>Saved Payment Methods:</h3>
        <ul id="savedPaymentsList">
            <!-- Saved payment methods will be appended here -->
        </ul>
    </div>
</div>

<div class="add-payment">
    <button type="button" id="addPaymentBtn" onclick="openPaymentModal()">Add Payment</button>
</div>

<!-- Hidden Payment Option Buttons -->
<div id="paymentOptions" style="display: none;">
    <button type="button" onclick="showPaymentForm('maya')">Add Maya Payment</button>
    <button type="button" onclick="showPaymentForm('gcash')">Add GCash Payment</button>
    <button type="button" onclick="showPaymentForm('card')">Add Card Payment</button>
    <button type="button" onclick="showPaymentForm('paypal')">Add PayPal Payment</button>
</div>

<!-- Payment Forms Container -->
<div class="payment-forms" id="paymentForms" style="display: none;">
    <!-- Maya Payment Form -->
    <div id="mayaDetails" class="payment-details" style="display: none;">
        <h2>Maya Details</h2>
        <form id="mayaForm">
            <label for="mayaNumber">Maya Number</label>
            <input type="text" id="mayaNumber" name="mayaNumber" placeholder="Enter Maya number" required>
            <div class="form-buttons">
                <button type="submit">Save Maya</button>
                <button type="button" id="cancel" onclick="cancelPaymentForm()">Cancel</button>
            </div>
        </form>
    </div>

    <!-- GCash Payment Form -->
    <div id="gcashDetails" class="payment-details" style="display: none;">
        <h2>GCash Details</h2>
        <form id="gcashForm">
            <label for="gcashNumber">GCash Number</label>
            <input type="text" id="gcashNumber" name="gcashNumber" placeholder="Enter GCash number" required>
            <div class="form-buttons">
                <button type="submit">Save GCash</button>
                <button type="button" id="cancel" onclick="cancelPaymentForm()">Cancel</button>
            </div>
        </form>
    </div>

    <!-- Card Payment Form -->
    <div id="cardDetails" class="payment-details" style="display: none;">
        <h2>Card Details</h2>
        <form id="cardForm">
            <label for="cardNumber">Card Number</label>
            <input type="text" id="cardNumber" name="cardNumber" placeholder="Enter card number" required>
            <label for="cardName">Cardholder Name</label>
            <input type="text" id="cardName" name="cardName" placeholder="Enter cardholder name" required>
            <label for="cardExpiry">Expiry Date (MM/YY)</label>
            <input type="text" id="cardExpiry" name="cardExpiry" placeholder="MM/YY" required>
            <label for="cardCVV">CVV</label>
            <input type="text" id="cardCVV" name="cardCVV" placeholder="Enter CVV" required>
            <div class="form-buttons">
                <button type="submit">Save Card</button>
                <button type="button" id="cancel" onclick="cancelPaymentForm()">Cancel</button>
            </div>
        </form>
    </div>

    <!-- PayPal Payment Form -->
    <div id="paypalDetails" class="payment-details" style="display: none;">
        <h2>PayPal Details</h2>
        <form id="paypalForm">
            <label for="paypalEmail">PayPal Email</label>
            <input type="email" id="paypalEmail" name="paypalEmail" placeholder="Enter PayPal email" required>
            <div class="form-buttons">
                <button type="submit">Save PayPal</button>
                <button type="button" id="cancel" onclick="cancelPaymentForm()">Cancel</button>
            </div>
        </form>
    </div>
</div>


                <button type="button" onclick="saveChanges()">Save Changes</button>
            </div>

           
    <div class="history-container">
    <h2>History in FairWheel</h2>
        <?php
        if (!empty($history)) {
            foreach ($history as $row) {
                echo "<div class='history-record'>";
                echo "<div><strong>Ticket #:</strong> " . htmlspecialchars($row['ticket_number']) . "</div>";
                echo "<div><strong>Departure:</strong> " . htmlspecialchars($row['from_location']) . "</div>";
                echo "<div><strong>Destination:</strong> " . htmlspecialchars($row['to_location']) . "</div>";
                echo "<div><strong>Date:</strong> " . date("m-d-y", strtotime($row['departure_date'])) . "</div>";
                echo "<div><strong>Status:</strong> " . htmlspecialchars($row['status']) . "</div>";
                echo "</div>";
            }
        }
        ?>
    </div>





<div id="logoutModal" class="modal">
    <div class="modal-content">
        <h2>Are you sure you want to leave without fairwheel?</h2>
        <div class="modal-buttons">
            <button onclick="logout()" class="confirm-logout-btn">Yes, log out</button>
            <button onclick="closeModal()" class="cancel-btn">Cancel</button>
        </div>
    </div>
</div>

<!-- Back and Logout Buttons -->
<div style="text-align: center; margin-top: 20px;">
    <a href="FairWheel.php" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Back</a>
    <button onclick="openLogoutModal()" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; margin-left: 10px;">Logout</button>
</div>
</div>
<script>
function saveChanges() {
    // Hide the form container
    const formContainer = document.getElementById('editForm');
    if (formContainer) {
        formContainer.style.display = 'none';
    }

    console.log("Saved Completely!");


    const messageContainer = document.getElementById("messageContainer");
    messageContainer.innerHTML = "Saving changes...";

 
    fetch("Profile.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
            fullName: document.getElementById("fullName").value,
            email: document.getElementById("email").value,
            gender: document.getElementById("gender").value,
            phoneNumber: document.getElementById("phoneNumber").value
        }),
    })
}

   // Toggle form visibility
function toggleEditForm() {
    const form = document.getElementById("editForm");
    form.style.display = form.style.display === "none" ? "block" : "none";
}

// Handle form submission
function handleFormSubmission() {
    // Show alert for feedback
    alert("Information successfully updated!");

    // Hide the form
    document.getElementById("editForm").style.display = "none";

   

    return true; 
}

    // Function to show payment options when the "Add Payment" button is clicked
    function openPaymentModal() {
        var paymentOptions = document.getElementById('paymentOptions');
        var paymentForms = document.getElementById('paymentForms');
        
        // Toggle visibility for payment options
        if (paymentOptions.style.display === 'none') {
            paymentOptions.style.display = 'flex';
            paymentForms.style.display = 'none'; // Hide payment forms if options are visible
        } else {
            paymentOptions.style.display = 'none'; // Hide payment options
        }
    }

    // Function to show the form for the selected payment method
    function showPaymentForm(paymentMethod) {
        // Hide all payment forms initially
        document.getElementById('mayaDetails').style.display = 'none';
        document.getElementById('gcashDetails').style.display = 'none';
        document.getElementById('cardDetails').style.display = 'none';
        document.getElementById('paypalDetails').style.display = 'none';
        
        // Show the payment form for the selected payment method
        if (paymentMethod === 'maya') {
            document.getElementById('mayaDetails').style.display = 'block';
        } else if (paymentMethod === 'gcash') {
            document.getElementById('gcashDetails').style.display = 'block';
        } else if (paymentMethod === 'card') {
            document.getElementById('cardDetails').style.display = 'block';
        } else if (paymentMethod === 'paypal') {
            document.getElementById('paypalDetails').style.display = 'block';
        }

        // Hide payment options and show the form container
        document.getElementById('paymentForms').style.display = 'block';
        document.getElementById('paymentOptions').style.display = 'none';
    }

    document.addEventListener("DOMContentLoaded", function () {
        const paymentOptions = document.getElementById('paymentOptions');
        const paymentForms = document.getElementById('paymentForms');
        const savedPaymentsList = document.getElementById('savedPaymentsList');

        // Function to hide all payment form details
        function resetPaymentDetails() {
            const allDetails = document.querySelectorAll(".payment-details");
            allDetails.forEach(detail => detail.style.display = "none");
        }

        // Function to show specific payment form
        function showPaymentForm(paymentMethod) {
            resetPaymentDetails();

            // Show the selected form based on the payment method
            const form = document.getElementById(`${paymentMethod}Details`);
            form.style.display = "block";
            paymentForms.style.display = "block";
            paymentOptions.style.display = "none";
        }
    });
    // Function to cancel and hide the currently open payment form
function cancelPaymentForm() {
    // Hide all payment forms
    const paymentForms = document.querySelectorAll(".payment-details");
    paymentForms.forEach(form => {
        form.style.display = "none";
    });

   
    document.getElementById("paymentForms").style.display = "none";
    document.getElementById("paymentOptions").style.display = "flex";
}

document.addEventListener("DOMContentLoaded", function() {
    // Display saved payments when the page is loaded
    displaySavedPayments();

    // Add event listeners for form submissions
    document.getElementById("mayaForm").addEventListener("submit", function(event) {
        event.preventDefault();
        const mayaNumber = document.getElementById("mayaNumber").value;
        savePaymentDetails("Maya", mayaNumber);
    });

    document.getElementById("gcashForm").addEventListener("submit", function(event) {
        event.preventDefault();
        const gcashNumber = document.getElementById("gcashNumber").value;
        savePaymentDetails("GCash", gcashNumber);
    });

    document.getElementById("cardForm").addEventListener("submit", function(event) {
        event.preventDefault();
        const cardNumber = document.getElementById("cardNumber").value;
        const cardName = document.getElementById("cardName").value;
        const cardExpiry = document.getElementById("cardExpiry").value;
        const cardCVV = document.getElementById("cardCVV").value;

       
        if (!cardNumber || !cardName || !cardExpiry || !cardCVV) {
            alert("Please fill out all card details.");
            return;
        }

        const details = `${cardNumber}, ${cardName}, Exp: ${cardExpiry}, CVV: ${cardCVV}`;
        savePaymentDetails("Card", details);
    });

    document.getElementById("paypalForm").addEventListener("submit", function(event) {
        event.preventDefault();
        const paypalEmail = document.getElementById("paypalEmail").value;
        if (!paypalEmail) {
            alert("Please provide a valid PayPal email.");
            return;
        }
        savePaymentDetails("PayPal", paypalEmail);
    });
});


function savePaymentDetails(type, details) {
    if (details.trim() === "") {
        alert("Please provide valid payment details.");
        return;
    }

    fetch("payment.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ type: type,details })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert(`${type} payment details saved successfully.`);
            displaySavedPayments(); // Refresh the saved payments list
            cancelPaymentForm();    // Hide the payment form after saving
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error saving payment method:", error);
        alert("There was an error saving the payment method. Please try again.");
    });
}

// Function to display saved payments from the server
function displaySavedPayments() {
    const savedPaymentsList = document.getElementById("savedPaymentsList");
    
    // Clear current payment methods
    savedPaymentsList.innerHTML = "";

    // Fetch payment methods from the server
    fetch("payment.php", { method: "GET" })
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Loop through each payment method and display it
                data.forEach(payment => {
                    const listItem = document.createElement("li");
                    listItem.textContent = `${payment.type}:${payment.details}`;
                    savedPaymentsList.appendChild(listItem);
                });
            } else {
                // Display a placeholder if no payments exist
                savedPaymentsList.innerHTML = "<li>No saved payment methods found.</li>";
            }
        })
        .catch(error => {
            console.error("Error fetching payment methods:", error);
            savedPaymentsList.innerHTML = "<li>Error fetching payment methods.</li>";
        });
}

// Function to open the logout confirmation modal
function openLogoutModal() {
    document.getElementById("logoutModal").style.display = "block";
}



function logout() {

    window.location.href = "logout.php"; 
}

function closeModal() {
    const logoutModal = document.getElementById('logoutModal');
    if (logoutModal) {
        logoutModal.style.display = 'none';
    }
}



    </script>
</body>
</html>