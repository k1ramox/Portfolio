const fullname_form = document.querySelector('#fullname-form');

fullname_form.addEventListener('submit', function(event){
    let form_firstname = document.querySelector('#form-firstname').value.trim();
    let form_middleinitial = document.querySelector('#form-middleinitial').value.trim();
    let form_lastname = document.querySelector('#form-lastname').value.trim();
    let update_fullname_feedback = document.querySelector('.update-fullname-feedback');

    if (form_firstname.length > 12) {
        update_fullname_feedback.innerHTML = 'First name must not exceed 12 characters';
        update_fullname_feedback.style.color = 'red';
        event.preventDefault();
    }
    else if (form_middleinitial.length > 1) {
        update_fullname_feedback.innerHTML = 'Middle initial must not exceed 2 characters';
        update_fullname_feedback.style.color = 'red';
        event.preventDefault();
    }
    else if (form_lastname.length > 12) {
        update_fullname_feedback.innerHTML = 'Last name must not exceed 12 characters';
        update_fullname_feedback.style.color = 'red';
        event.preventDefault();
    }
    

});
