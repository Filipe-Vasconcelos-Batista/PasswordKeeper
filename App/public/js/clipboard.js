function copyToClipboard(password) {
    navigator.clipboard.writeText(password).then(function(){
        displayMessage('Password copied to clipboard!', 'success');
    }, function(error){
        displayMessage('Failed copying Password to clipboard!' + error, 'error');
    })
    function displayMessage(message,type){
        let messageDiv= document.createElement("div");
        messageDiv.className='alert mt-3';
        messageDiv.role='alert';
        messageDiv.innerText= message;
        if(type=== 'success'){
            messageDiv.classList.add('alert-success');
        }else if(type=== 'error'){
            messageDiv.classList.add('alert-danger');
        }
        const container= document.getElementById('clipboard');
        container.innerHTML=' ';
        container.appendChild(messageDiv);
    }
}