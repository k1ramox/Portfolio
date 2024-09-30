//JS for modal of message this user
const message_this_user = document.querySelector('#message-this-user');
const message_modal = document.querySelector('.message-modal-container-outer').style;
const message_close_button = document.querySelector('#message-modal-close-button');

message_this_user.addEventListener('click', function(event){
    event.preventDefault();
    if (message_modal.display == 'flex') {
        message_modal.display = 'none';
    }
    else {
        message_modal.display = 'flex';
    }
});

message_close_button.addEventListener('click', function(event){
    event.preventDefault();

    message_modal.display = 'none';
});

//JS for sending a message
const message_form = document.getElementById('message-form');
const message_send_button = document.querySelector('.message-send-button');


message_form.addEventListener('submit', function(event) {
    event.preventDefault();

    let text_message = document.querySelector('.message-text-area').value;
    let receiver_id = document.querySelector('#follow-button-id-info').value;
    
    if (!text_message.trim()) {
        console.log(text_message.trim())
        return
    }
    else {

    }

    fetch('/message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            'text_message': text_message,
            'receiver_id': receiver_id
        })
    })
    .then (response => {
        return response.json();
    })
    .then (data => {
        console.log(data.text_message);
    })
});

message_send_button.addEventListener('click', function(event){
    let text_area = document.querySelector('.message-text-area').value.trim();
    let message_feedback = document.querySelector('.message-feedback');
    if (!text_area) {
        message_feedback.style.display = 'flex';
        message_feedback.style.backgroundColor = '#f44336';
        message_feedback.style.right = '30%'
        message_feedback.innerHTML = 'Please input a message'
        setTimeout(function(){
            message_feedback.style.display = 'none';
        }, 1000);
    }
    else {
        message_feedback.style.display = 'flex';
        message_feedback.style.backgroundColor = '#4CAF50';
        message_feedback.style.right = '45%'
        message_feedback.innerHTML = 'Sent'
        setTimeout(function(){
            message_feedback.style.display = 'none';
        }, 1000);
    }
    text_area = '';
});

//JS for followbutton
const follow_button = document.getElementById('follow-button-id');
const follow_button_id = document.getElementById('follow-button-id-info').getAttribute('value');
follow_button.addEventListener('click', ()=> {

    fetch('/follow', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(follow_button_id)
    })
    .then( response => {
        return response.json();
    })
    .then( data => { 
        if (data['followed'] === 'True') {
            follow_button.textContent = 'Unfollow';
            followers_count = document.getElementById('followers_count').textContent;
            document.getElementById('followers_count').textContent = +followers_count + 1;
        }
        else {
            follow_button.textContent = 'Follow';
            followers_count = document.getElementById('followers_count').textContent;
            document.getElementById('followers_count').textContent = +followers_count - 1;
        }
    })
    
});


//JS for givelovebutton
let givelovebutton = document.getElementsByClassName('give-love-button');
for (let i = 0; i < givelovebutton.length; i++) {
    givelovebutton[i].addEventListener('click', ()=> {
        let pet_id = givelovebutton[i].getAttribute('data-extra');
        fetch('/give_love', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(pet_id)
        })
        .then( response => {
            
            return response.json();
        })
        .then( data => {
            let diy_flashmessage_container = document.getElementById('diy-flashmessage-container').style;
            let diy_flashmessage_text = document.getElementById('diy-flashmessage');
            
            if (data['insufficient_token']) {
                diy_flashmessage_container.display = 'flex';
                diy_flashmessage_container.backgroundColor = '#F44336';
                diy_flashmessage_text.innerHTML = 'Insufficient tokens! Please check your token count or <a href="/top-up">click here</a> to top up.'

            }
            else {
                givelovebutton[i].value = 'Petting...';
                givelovebutton[i].disabled = true;
                setTimeout(()=> {
                    givelovebutton[i].disabled = false;
                    givelovebutton[i].value = 'GIVE LOVE ♥';
                    select_which_pet = data['pet_id'];
                    document.getElementById(select_which_pet).textContent = data['react_new_count'];
                    document.getElementById('loves_earned_count').textContent = data['loves_earned_new_count'];
                    diy_flashmessage_container.display = 'flex';
                    diy_flashmessage_container.backgroundColor = '#4CAF50';
                    diy_flashmessage_text.innerHTML = 'Love sent ♥'
                }, 3000);


            }
            
        })
}
)};