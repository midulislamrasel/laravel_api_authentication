


<script>
    document.querySelector("#logoutBtn").addEventListener('click',function(){
        const token = localStorage.getItem('api_token');

        fetch('/api/logout',{
            method:'POST',
            headers:{
                'Authorization': `Bearer ${token}`,
            }
        })
            .then(response=>response.json())
            .then(data=>{
                console.log(data);

                window.location.href="http://localhost:8000/";
            })
    })
</script>
