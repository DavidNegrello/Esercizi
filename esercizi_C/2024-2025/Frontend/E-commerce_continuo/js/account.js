document.addEventListener("DOMContentLoaded", function() {
    // Form di login
    const loginForm = document.getElementById("login-form");
    const loginMessage = document.getElementById("login-message");
    
    if (loginForm) {
        loginForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const email = document.getElementById("login-email").value;
            const password = document.getElementById("login-password").value;
            const rememberMe = document.getElementById("remember-me").checked;
            
            // Prepara i dati per l'invio
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            formData.append('remember_me', rememberMe);
            
            // Mostra il loader
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Accesso in corso...';
            submitBtn.disabled = true;
            
            // Invia la richiesta al server
            fetch('../api/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostra il messaggio di successo
                    loginMessage.classList.remove("d-none", "alert-danger");
                    loginMessage.classList.add("alert-success");
                    loginMessage.textContent = "Accesso effettuato con successo! Reindirizzamento...";
                    
                    // Reindirizza alla home page dopo un breve ritardo
                    setTimeout(() => {
                        window.location.href = "../index.html";
                    }, 1500);
                } else {
                    // Mostra il messaggio di errore
                    loginMessage.classList.remove("d-none", "alert-success");
                    loginMessage.classList.add("alert-danger");
                    loginMessage.textContent = data.message || "Errore durante l'accesso. Verifica le tue credenziali.";
                    
                    // Ripristina il pulsante
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error("Errore:", error);
                loginMessage.classList.remove("d-none", "alert-success");
                loginMessage.classList.add("alert-danger");
                loginMessage.textContent = "Si è verificato un errore durante l'accesso. Riprova più tardi.";
                
                // Ripristina il pulsante
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Form di registrazione
    const registerForm = document.getElementById("register-form");
    const registerMessage = document.getElementById("register-message");
    
    if (registerForm) {
        registerForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const name = document.getElementById("register-name").value;
            const email = document.getElementById("register-email").value;
            const password = document.getElementById("register-password").value;
            const confirmPassword = document.getElementById("register-confirm-password").value;
            
            // Verifica che le password corrispondano
            if (password !== confirmPassword) {
                registerMessage.classList.remove("d-none", "alert-success");
                registerMessage.classList.add("alert-danger");
                registerMessage.textContent = "Le password non corrispondono.";
                return;
            }
            
            // Prepara i dati per l'invio
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);
            
            // Mostra il loader
            const submitBtn = registerForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registrazione in corso...';
            submitBtn.disabled = true;
            
            // Invia la richiesta al server
            fetch('../api/register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostra il messaggio di successo
                    registerMessage.classList.remove("d-none", "alert-danger");
                    registerMessage.classList.add("alert-success");
                    registerMessage.textContent = "Registrazione completata con successo! Ora puoi accedere al tuo account.";
                    
                    // Pulisci il form
                    registerForm.reset();
                    
                    // Passa alla scheda di login dopo un breve ritardo
                    setTimeout(() => {
                        document.getElementById("login-tab").click();
                    }, 2000);
                } else {
                    // Mostra il messaggio di errore
                    registerMessage.classList.remove("d-none", "alert-success");
                    registerMessage.classList.add("alert-danger");
                    registerMessage.textContent = data.message || "Errore durante la registrazione.";
                }
                
                // Ripristina il pulsante
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            })
            .catch(error => {
                console.error("Errore:", error);
                registerMessage.classList.remove("d-none", "alert-success");
                registerMessage.classList.add("alert-danger");
                registerMessage.textContent = "Si è verificato un errore durante la registrazione. Riprova più tardi.";
                
                // Ripristina il pulsante
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Controlla se l'utente è già loggato
    fetch('../api/check_auth.php')
        .then(response => response.json())
        .then(data => {
            if (data.logged_in) {
                // Se l'utente è già loggato, mostra un messaggio e reindirizza alla dashboard
                window.location.href = "dashboard.html";
            }
        })
        .catch(error => {
            console.error("Errore nel controllo dell'autenticazione:", error);
        });
});