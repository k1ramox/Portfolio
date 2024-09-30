const register_form = document.querySelector('#register_form');

    register_form.addEventListener('submit', function(event){
        event.preventDefault();
        let username_register = document.querySelector('#username_register').value.trim();
        let password_register = document.querySelector('#password_register').value.trim();
        let confirm_register = document.querySelector('#confirm_register').value.trim();

        fetch('/check_username_if_exist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(username_register.toLowerCase())
        })
        .then( response => {
            return response.json();
        })
        .then( data => { 
            if (data['status'] === 'taken') {
                document.querySelector('.register-apology').innerHTML = 'This username is already taken!';
                return
            }
            else if (password_register != confirm_register) {
                document.querySelector('.register-apology').innerHTML = "Password doesn't match!";
                return
            }
            else {
                if (username_register.length === 0 || password_register.length === 0 || confirm_register.length === 0) {
                    document.querySelector('.register-apology').innerHTML = 'All fields are required!'
                    event.preventDefault();
                }
                else {
                    register_form.submit();
                }
            }
            
        })


    });
