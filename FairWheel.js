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
