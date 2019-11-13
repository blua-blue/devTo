const devToApi = {
    endpoint: '{{base}}api.v1/dev-to?',
    config: {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token'),
            'Content-Type': 'application/json'
        }
    },
    call:async (paramString) => {
        return await fetch(devToApi.endpoint + paramString, devToApi.config).then(res => res.json());
    }
};



document.querySelector('#dev-api-key-form').addEventListener('submit', event=>{
    let apiKey = document.querySelector('#api-key');
    devToApi.call('apiKey='+apiKey.value).then(body =>{
        if(typeof body.test.error !== 'undefined'){
            alert('This API key does not seem to work! Please reenter.');
            apiKey.value = '';
        } else {
            document.querySelector('#webhook-token').textContent = body.token;
        }
    });

   event.preventDefault();
});
