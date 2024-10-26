
document.getElementById('form-generate-random').addEventListener('submit', function(event) {
    event.preventDefault();

    const url = document.getElementById('urlInput');

    if (!url.value) {
        console.log("Cannot be empty");

        //add a border nga error if walay sulod
        url.classList.add('required', 'shake');
        //walaon ang red nga border if nagstart na ug type sa input
        url.addEventListener('input', function(event) {
            url.classList.remove('required', 'shake');
        });
        return;
    }

    document.getElementById('form-generate-random-display').style.display = 'none';
    document.getElementById('random-generate-status').style.display = 'flex';
    

    fetch('functions/shortenUrl.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ url: url.value }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('urlResult').value = data.shortenedUrl;
            document.getElementById('random-generate-status').style.display = 'none';
            document.getElementById('success-generate-random').style.display = 'flex';
        } else {
            document.getElementById('random-generate-status').innerHTML = data.message;
            document.getElementById('random-generate-status').style.color = 'red';
            document.getElementById('try-again-button').style.display = 'flex';
            
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('urlResult').value = 'Something went wrong.';
    });
});

//for "Try Again" button redirection.
document.getElementById('try-again-button').addEventListener('click', function() {
    window.location.href = 'shorten-random.php';
});

//for "SHORTEN ANOTHER" button redirection
document.getElementById('shorten-another-button').addEventListener('click', function(event) {
    event.preventDefault();
    window.location.href = 'shorten-random.php';
});




// Copy button implementation
document.getElementById("copy-button").addEventListener('click', function(event) {
    event.preventDefault();
    let textField = document.getElementById('urlResult');

    // Check if the Clipboard API is available
    if (navigator.clipboard) {
        navigator.clipboard.writeText(textField.value)
            .then(() => {
                console.log("Text copied to clipboard!");
            })
            .catch(err => {
                console.error("Failed to copy: ", err);
            });
    } else {
        // Fallback for browsers that do not support the Clipboard API
        textField.select();
        document.execCommand("copy");
        console.log("Fallback: Text copied to clipboard!");
    }
});

