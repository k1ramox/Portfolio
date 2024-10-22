const registerInputFields = document.querySelectorAll('.register-input-field');
const submitButton = document.querySelector('#register-submit');

submitButton.addEventListener('click', function(event){
    registerInputFields.forEach(function(inp){
        if (!inp.value) {
            event.preventDefault();
            inp.classList.add('error');
        }
        inp.addEventListener('input', function(event) {
            inp.classList.remove('error');
        });
        });
    document.querySelector('#register-feedback').remove();
});
