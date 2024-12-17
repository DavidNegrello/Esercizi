// Funzione per gestire l'accesso normale
function handleLogin(event) {
    event.preventDefault(); 
    // Prende l'email inserita e estrae la parte prima del simbolo "@" (ad esempio 'nomeutente' da 'nomeutente@email.com')
    const username = document.getElementById("email").value.split("@")[0]; 
    // Salva il nome utente nel localStorage per mantenerlo disponibile anche dopo il riavvio della pagina
    localStorage.setItem("username", username);
    // Reindirizza l'utente alla pagina principale (home) dopo che il login è stato eseguito
    window.location.href = "../index.html"; 
}


// Funzione per gestire l'accesso anonimo
function handleGuestAccess() {
    // Mostra una finestra di prompt chiedendo all'utente di inserire un nome per accedere come ospite
    const guestName = prompt("Inserisci un nome per accedere come ospite:", "Ospite");

    // Verifica che l'utente abbia inserito un nome (cioè che guestName non sia vuoto o nullo)
    if (guestName) {
        // Salva il nome dell'ospite nel localStorage con la chiave "username"
        localStorage.setItem("username", guestName);

        // Reindirizza l'utente alla home page (index.html)
        window.location.href = "../index.html"; // Reindirizza alla home
    }
}