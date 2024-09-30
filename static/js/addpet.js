const add_pet_form = document.querySelector('#add_pet_id_selector')

    add_pet_form.addEventListener('submit', function(event){
        let pet_name = document.querySelector('.pet_name').value.trim();
        let pet_species = document.querySelector('.pet_species').value.trim();
        let imageUpload = document.querySelector('.input-pet-profile');
        if (pet_name === '' || pet_species === '' ||  imageUpload.files.length === 0) {
            let addpet_feedback = document.querySelector('.addpet_feedback')
            addpet_feedback.style.color = 'red';
            addpet_feedback.innerHTML = 'All above fields are required';
            event.preventDefault();
        }
        else if (pet_name.length > 9 || pet_species.length > 9) {
            let addpet_feedback = document.querySelector('.addpet_feedback')
            addpet_feedback.style.color = 'red';
            addpet_feedback.innerHTML = 'Name/species limit to 9 characters only';
            event.preventDefault();
        }
        else if (imageUpload.files[0].type != 'image/jpeg') {
            let addpet_feedback = document.querySelector('.addpet_feedback')
            addpet_feedback.style.color = 'red';
            addpet_feedback.innerHTML = 'Invalid type of file (jpg or jpeg only)';
            event.preventDefault();
        }
        else {
            let addpet_feedback = document.querySelector('.addpet_feedback')
            addpet_feedback.style.color = 'green';
            addpet_feedback.innerHTML = 'Pet successfully added';
        }

        imageUpload.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                const fileType = file.type; // Get the file type
                const isJpg = fileType === 'image/jpeg'; // Check if it's a .jpg or .jpeg

                if (isJpg) {
                    console.log('The uploaded file is a .jpg image.');
                } 
                else {
                    document.querySelector('#add_pet_id_selector').addEventListener('submit', function(event){
                        event.preventDefault();
                        let addpet_feedback = document.querySelector('.addpet_feedback')
                        addpet_feedback.style.color = 'red';
                        addpet_feedback.innerHTML = 'Invalid type of file (jpg or jpeg only)';
                    });
                }
            }
        });
    });