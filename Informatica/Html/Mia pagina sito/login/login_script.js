const container = document.querySelector(".container"),
mostraNascondi = document.querySelectorAll(".mostraNascondi"),
passwordPosto = document.querySelectorAll(".password");

 
  //Secondo form 
  function gestisciLogin() {
    // Nasconde il form di accesso
    document.getElementById('form-login').classList.add('hidden');
    
    // Mostra il form 
    document.getElementById('secondo-form').classList.remove('hidden');
};   
 //   Per mostrare o nascondere la password
 mostraNascondi.forEach(eyeIcon =>{
        eyeIcon.addEventListener("click", ()=>{
            passwordPosto.forEach(passwordPosto =>{
                if(passwordPosto.type ==="password"){
                    passwordPosto.type = "text";
                    mostraNascondi.forEach(icon =>{
                        icon.classList.replace("uil-eye-slash", "uil-eye");
                    })
                }else{
                    passwordPosto.type = "password";
                    mostraNascondi.forEach(icon =>{
                        icon.classList.replace("uil-eye", "uil-eye-slash");
                    })
                }
            }) 
        })
    })
