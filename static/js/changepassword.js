const changepassword_form = document.querySelector('#changepassword-form');

changepassword_form.addEventListener('submit', function(event){
    let old_password = document.querySelector('#oldpassword').value.trim();
    let new_password = document.querySelector('#newpassword').value.trim();
    let confirm = document.querySelector('#confirmnewpassword').value.trim();
    let feedback = document.querySelector('.changepass-feedback');

    if (old_password.length === 0 || new_password.length === 0 || confirm.length === 0) {
        feedback.style.color = 'red';
        feedback.innerHTML = 'All fields are required!'
        event.preventDefault();
    }
});
