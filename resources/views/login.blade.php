

<script>
    $(document).ready(function (){
        $("#loginButton").on('click',function (){
            const email = $("#email").val();
            const password = $("#password").val();

            $.ajax({
                url:'/api/login',
                type:'POST',
                contentType:'application/json',
                data:Json.stringify({
                    email:email,
                    password:password
                }),
                success:function (response){
                    console.log(response)

                    localStorage.setItem('api_token',response.token);
                    window.location.href = "http:://localhost:8000/allposts";
                },
                error:function(xhr,status,error){
                    alert('Error:' + xhr.responseText);
                }
            })
        });

    })

</script>
