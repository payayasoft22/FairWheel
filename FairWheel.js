document.addEventListener('DOMContentLoaded', function() {
    let menu = document.querySelector('#menu-icon');
    let navlist = document.querySelector('.navlist');
    let signin = document.querySelector('.Sign-in');


    function checkWindowSize() {
        if (window.innerWidth > 848) {
            navlist.style.display = 'flex';
            signin.style.display = 'flex'; 
            menu.style.display = 'none'; 
            menu.classList.remove('bx-x'); 
        } else {
            navlist.style.display = 'none'; 
            signin.style.display = 'none'; 
            menu.style.display = 'block'; 
        }
    }

    checkWindowSize();


    menu.onclick = () => {
        menu.classList.toggle('bx-x'); 

        if (window.innerWidth <= 848) {
            if (navlist.style.display === 'flex') {
                navlist.style.display = 'none'; 
                signin.style.display = 'none'; 
            } else {
                navlist.style.display = 'flex'; 
                signin.style.display = 'flex'; 
            }
        }
    };


    window.addEventListener('resize', checkWindowSize);
});


document.addEventListener('DOMContentLoaded', function () {
    const radioButtons = document.querySelectorAll('input[name="radio-choice"]');
    const returnDatePicker = document.querySelector('.Date-picker'); 

    function updateSpacing() {
        if (this.value === 'One-way') {
            returnDatePicker.style.display = 'none';
        } else if (this.value === 'round-trip') {
            returnDatePicker.style.display = 'block';   
            returnDateText.style.display = 'block';   
        }
    }


    radioButtons.forEach(radio => {
        radio.addEventListener('change', updateSpacing);
    });


    const selectedOption = document.querySelector('input[name="radio-choice"]:checked');
    if (selectedOption) {
        updateSpacing.call(selectedOption);
    }
});



const showLoginFormButton = document.getElementById('LoginButton');
const loginForm = document.getElementById('loginForm');
const closeButton = document.querySelector('.close');

showLoginFormButton.addEventListener('click', () => {
  loginForm.style.display = 'block';
});

closeButton.addEventListener('click', () => {
  loginForm.style.display = 'none';
});


var modal = document.getElementById("myModal");


var closeBtn = document.querySelector(".close");


closeBtn.onclick = function() {
    modal.style.display = "none";
};



const menuIcon = document.getElementById('menu-icon');
const menu = document.querySelector('.navlist'); 

menuIcon.addEventListener('click', () => {
  menu.classList.toggle('active');
});


//for login and if logged in already
function isLoggedIn() {


    return false; 
  }
  

  function openLoginModal() {
    const modal = document.getElementById('loginForm');
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
  }
  

  function closeLoginModal() {
    const modal = document.getElementById('loginForm');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
  }
  
  document.getElementById('bookButton').addEventListener('click', () => {
    if (isLoggedIn()) {
      // Redirect to the booking page or process the booking directly
      window.location.href = 'booking.html'; // Or trigger booking process
    } else {
      openLoginModal();
    }
  });
  

  document.getElementById('loginButton').addEventListener('click', () => {
    openLoginModal();
  });
  

  document.getElementById('closeModal').addEventListener('click', () => {
    closeLoginModal();
  });

  
  function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePassword');

    const isPassword = passwordInput.getAttribute('type') === 'password';


    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

   
    if (isPassword) {
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    } else {
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    }
}
