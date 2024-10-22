
  
    //open the modal
    let moreButtons = document.querySelectorAll('.actions-url-td');
    let i;

    moreButtons.forEach( function(button){
        button.addEventListener( 'click', function(event){
            i = 3;
            let row = this.closest('tr');
            document.getElementById('shortlink-container').value = row.querySelector('.shortened-url').value;
            document.getElementById('modal-background').style.display = 'flex';

            document.getElementById('visit-count-container').textContent = row.querySelector('.visit-count').value;
        } );
    } );

    //to close the modal
    let modalBackground = document.getElementById('modal-background');

    function closeModal () {
        document.getElementById('modal-background').style.display = 'none';
    }
        
        modalBackground.addEventListener( 'click', function(event){
            closeModal ();
        });
    let modal = document.getElementById('modal');
    modal.addEventListener('click', function(event){
        event.stopPropagation();
    });
    

    document.getElementById('modal-background').addEventListener('click', function(event){
        closeModal();
    })

    let feedbackStatus = '';

    //Copy button implementation
    let copyButton = document.getElementById("copy-button");

    copyButton.addEventListener('click', function(event){
        event.preventDefault();
        let text = document.getElementById('shortlink-container').value;

        navigator.clipboard.writeText(text)
        feedbackStatus = 'copy';
        feedbackModal(feedbackStatus);
    });

    // Update row numbers
    function updateRowNumbers() {
    let rows = document.querySelectorAll('.url-table tbody tr');
        rows.forEach( function (row, index) {
            row.cells[0].textContent = index + 1; 
        });
    }


  //remove row function
  let removeButton = document.getElementById("remove-button");
        removeButton.addEventListener('click', function(event) {
        event.preventDefault();
        removeButton.disabled = true;
        // Find the closest row (tr)
        //let row = this.closest('tr'); 
        let shortlinkValue = document.getElementById('shortlink-container').value;
        let rows = document.querySelectorAll('tr');

        let modalFeedback = document.getElementById('modal-feedback');
        modalFeedback.style.display = 'flex';
        
        let i = 3;

        modalFeedback.textContent = `Removing in ${i}...`;

        let timer = setInterval(() => {
                modalFeedback.textContent = `Removing in ${--i}...`;;
            }, 1000);

        let removing = setTimeout(() => {

        rows.forEach(function(row){
            let hiddenInput = row.querySelector('input[type="hidden"]');
            if (hiddenInput && hiddenInput.value === shortlinkValue) {
                // Remove the row if the value matches
                row.remove();
            }
        });

        
        if (shortlinkValue) {
            
        
            //fetch REMOVE
            fetch('functions/removeUrl.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ code: shortlinkValue}),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) { //if success: true
                    console.log(data.message);
                    updateRowNumbers();
                } else { //if success: false
                    console.log(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('urlResult').value = 'Something went wrong.';
            });
            closeModal();
            clearInterval(timer);
            modalFeedback.style.display = 'none';
            removeButton.disabled = false;
        }
    }, 3000);

    modalBackground.addEventListener('click', function(event){
        closeModal();
        clearTimeout(removing);
        clearInterval(timer);
        modalFeedback.style.display = 'none';
        removeButton.disabled = false;
    });

    });



    //modal feedback
    function feedbackModal(status) {
        let modalFeedback = document.getElementById('modal-feedback');
        
        switch(status) {
            case 'copy':
                modalFeedback.style.display = 'block';
                modalFeedback.style.color = 'yellowgreen';
                modalFeedback.textContent = 'Link copied!';
                setTimeout(() => {
                modalFeedback.style.display= 'none';
                }, 2000); // Fade out after 3 seconds
        }
    }
