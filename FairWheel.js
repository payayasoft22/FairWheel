document.addEventListener('DOMContentLoaded', function () {
  // Get references to DOM elements
  const menu = document.querySelector('#menu-icon');
  const navlist = document.querySelector('.navlist');
  const signin = document.querySelector('.Sign-in');
  const showLoginFormButton = document.getElementById('LoginButton');
  const loginForm = document.getElementById('loginForm');
  const closeButton = document.querySelector('.close');
  const profileSection = document.getElementById('profileSection');
  const logoutButton = document.getElementById('logoutButton');
  const profilePic = document.getElementById('profilePic');
  const usernameInput = document.getElementById('username');
  const passwordInput = document.getElementById('password');
  const errorMessage = document.getElementById('loginError');
  const oneWayRadio = document.querySelector('input[value="One-way"]');
  const roundTripRadio = document.querySelector('input[value="round-trip"]');
  const returnDatePicker = document.querySelector('.Date-picker');


  // Manage login state
  let isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';

  function checkWindowSize() {
    if (window.innerWidth > 803) {
      navlist.style.display = 'flex';
      signin.style.display = 'flex';
      menu.style.display = 'none';
      menu.classList.remove('bx-x');
      authButtons.style.display = 'flex';
    } else {
      navlist.style.display = 'none';
      signin.style.display = 'none';
      menu.style.display = 'flex';

    }
  }

  // Initialize UI based on login state
  function updateUI() {
    if (isLoggedIn) {
        signin.style.display = 'none'; // Hide Sign In/Register buttons
        profileSection.style.display = 'flex'; // Show Profile Icon
        console.log('Logged in: Profile section visible');
    } else {
        signin.style.display = 'flex'; // Show Sign In/Register buttons
        profileSection.style.display = 'none'; // Hide Profile Icon
        console.log('Logged out: Sign-in/register visible');
    }
}

  // Handle menu toggle
  menu.addEventListener('click', () => {
    navlist.classList.toggle('show');
    menu.classList.toggle('bx-x');

    if (window.innerWidth <= 803) {
      const isNavVisible = navlist.style.display === 'flex';
      navlist.style.display = isNavVisible ? 'none' : 'flex';
      signin.style.display = isNavVisible ? 'none' : 'flex';
      authButtons.style.display = isNavVisible ? 'none' : 'block';
    }
  });

  // Show login form
  showLoginFormButton.addEventListener('click', () => {
    loginForm.style.display = 'block';
    errorMessage.textContent = ''; // Clear error message
  });

  // Close login form
  closeButton.addEventListener('click', () => {
    loginForm.style.display = 'none';
  });

  // Prevent closing login modal when clicking outside
  loginForm.addEventListener('click', (event) => {
    if (event.target === loginForm) {
      event.stopPropagation();
    }
  });

  // Handle login validation
  const loginSubmitButton = document.getElementById('loginSubmit');
  loginSubmitButton.addEventListener('click', (e) => {
    e.preventDefault();

    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    if (username === validCredentials.username && password === validCredentials.password) {
      // Successful login
      localStorage.setItem('isLoggedIn', 'true');
      isLoggedIn = true;
      updateUI();
      loginForm.style.display = 'none';
      console.log('Login successful');
    } else {
      // Show error message
      errorMessage.textContent = 'Invalid username or password.';
    }
  });

  // Handle logout
  logoutButton.addEventListener('click', function () {
    localStorage.removeItem('isLoggedIn'); // Clear login state
    isLoggedIn = false;
    updateUI();
    console.log('Logged out');
  });

  // Profile icon behavior
  profilePic.addEventListener('click', function () {
    if (isLoggedIn) {
      alert('Welcome to your profile!');
    } else {
      alert('Please log in first!');
    }
  });

  // Initial setup
  checkWindowSize();
  updateUI();

  // Handle window resize to update layout
  window.addEventListener('resize', checkWindowSize);
});

// Select the element with the id 'authButtons'
const authButtons = document.getElementById('authButtons');

// Apply flex display to the div
authButtons.style.display = 'flex';
authButtons.style.justifyContent = 'space-between';
authButtons.style.alignItems = 'center'; 
authButtons.style.marginBottom = '30px';

  const today = new Date();
  const formattedDate = today.toISOString().split('T')[0]; // Convert to YYYY-MM-DD format

  // Set the min attribute for Departure Date and Return Date inputs
  document.getElementById('dates').setAttribute('min', formattedDate); // Departure Date
  document.getElementById('date').setAttribute('min', formattedDate);  // Return Date



document.addEventListener('DOMContentLoaded', function () {
  const oneWayRadio = document.querySelector('input[value="One-way"]');
  const roundTripRadio = document.querySelector('input[value="round-trip"]');
  const returnDatePicker = document.querySelector('.Date-picker');

  // Function to toggle return date visibility
  function toggleReturnDate() {
    if (oneWayRadio.checked) {
      returnDatePicker.style.display = 'none'; // Hide return date
    } else if (roundTripRadio.checked) {
      returnDatePicker.style.display = 'block'; // Show return date
    }
  }

  // Add event listeners to the radio buttons
  oneWayRadio.addEventListener('change', toggleReturnDate);
  roundTripRadio.addEventListener('change', toggleReturnDate);

  // Initial setup based on the default selected radio button
  toggleReturnDate();
});


function togglePasswordVisibility() {
  const passwordField = document.getElementById('password');
  const toggleIcon = document.getElementById('togglePassword');

  // Toggle password field type between 'password' and 'text'
  if (passwordField.type === 'password') {
      passwordField.type = 'text'; // Show password
      toggleIcon.classList.remove('fa-eye-slash'); // Change icon to eye
      toggleIcon.classList.add('fa-eye'); // Add eye icon
  } else {
      passwordField.type = 'password'; // Hide password
      toggleIcon.classList.remove('fa-eye'); // Change icon to eye-slash
      toggleIcon.classList.add('fa-eye-slash'); // Add eye-slash icon
  }
}


