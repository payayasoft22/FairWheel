document.addEventListener("DOMContentLoaded", () => {
    // Get the modal, buttons, and other elements
    const loginForm = document.getElementById("loginForm");
    const rememberMeCheckbox = document.getElementById("rememberMeCheckbox");
    const loginFormSubmit = document.getElementById("loginFormSubmit");

    // Check login status on page load
    const checkLoginStatus = () => {
        const isLoggedIn = localStorage.getItem("isLoggedIn");

        // If not logged in, show the login modal
        if (!isLoggedIn || isLoggedIn !== "true") {
            showModal();  // Show the login modal
        } else {
            // If logged in, ensure modal is hidden and continue normal behavior
            loginForm.style.display = "none"; // Hide the login modal
        }
    };

    // Show the modal
    const showModal = () => {
        loginForm.style.display = "block";  // Show the modal
    };

    // Close the modal
    const closeModal = () => {
        loginForm.style.display = "none";  // Hide the modal
    };

    // Handle login submission
    loginFormSubmit.addEventListener("submit", (e) => {
        e.preventDefault();

        // Get email and password (for simplicity, assume they are valid)
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        // Simulate successful login by setting a login flag
        if (email && password) {
            // Set login status in localStorage
            localStorage.setItem("isLoggedIn", "true");

            // If remember me is checked, save in localStorage
            if (rememberMeCheckbox.checked) {
                localStorage.setItem("rememberMe", "true");
                localStorage.setItem("userEmail", email); // Store user email as an example
            }

            // Close the modal and redirect to the main profile page
            closeModal();
            window.location.href = "profile.html";  // Redirect to the profile page or another page
        }
    });

    // Handle logout (You can call this function on the logout button or anywhere appropriate)
    const logoutUser = () => {
        // Clear login state
        localStorage.removeItem("isLoggedIn");
        localStorage.removeItem("rememberMe");
        localStorage.removeItem("userEmail");

        // Redirect to login page
        window.location.href = "FairWheel.php#loginForm"; // Adjust to your login page URL
    };

    // Check login status on page load
    checkLoginStatus();
});
