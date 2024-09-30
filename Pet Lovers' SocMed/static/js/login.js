 const form_login = document.querySelector('#form_login');
    form_login.addEventListener('submit', function(event){
        let username_login = document.querySelector('#username_login').value.trim();
        let password_login = document.querySelector('#password_login').value.trim();

        if (username_login.length === 0) {
            let login_apology = document.querySelector('.login-apology');
            login_apology.innerHTML = 'Must input username!'
            event.preventDefault();
        }
        else if (password_login.length === 0) {
            let login_apology = document.querySelector('.login-apology');
            login_apology.innerHTML = 'Must input password!'
            event.preventDefault();
        }
    });
