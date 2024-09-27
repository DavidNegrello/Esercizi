const loginBottone = document.getElementById('login-bottone');

loginBottone.addEventListener('click', () => {
        const nuovaFinestra = window.open('', '_blank', 'width=500,height=400');
        const newDoc = nuovaFinestra.document;
        
        newDoc.write(`
          <html>
            <head>
              <link rel="stylesheet" href="style.css">
            </head>
            <body>
              <div class="login-form">
                <h1 class="login-header">Login</h1>
                <input type="text" name="nome" class="login-form-field" placeholder="nome">
                <input type="password" name="password" class="login-form-field" placeholder="Password">
                <button class="login-form-invia">Login</button>
              </div>
            </body>
          </html>
        `);
        
        const loginForm = newDoc.querySelector('.login-form');
        const loginForminvia = loginForm.querySelector('.login-form-invia');
        
        loginForminvia.addEventListener('click', (e) => {
          e.preventDefault();
          const nome = loginForm.querySelector('input[name="nome"]').value;
          const password = loginForm.querySelector('input[name="password"]').value;
          
          // Verifica credenziali predefinite admin-passoword
          if (nome === 'admin' && password === 'password') {
            alert('Login successful!');
            loginBottone.textContent = `Ciao capo, ${nome}`;
            loginBottone.disabled = true;
          } else {
            alert('Credenziali non valide');
          }
        });
      });