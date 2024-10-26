//feedback for empty fields
document.getElementById('login-form').addEventListener('submit', function(event){
    let username = document.getElementById('username-field');
    let password = document.getElementById('password-field');

    if (!username.value) {
        event.preventDefault();
        username.classList.add('error');
    }
    if (!password.value) {
        event.preventDefault();
        password.classList.add('error');
    }

    username.addEventListener('input', function(event){
        username.classList.remove('error');
    })
    password.addEventListener('input', function(event){
        password.classList.remove('error');
    })
})