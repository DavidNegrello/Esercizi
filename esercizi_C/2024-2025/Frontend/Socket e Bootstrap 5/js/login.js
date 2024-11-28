// Funzione per gestire l'accesso normale
function handleLogin(event) {
    event.preventDefault(); 
    // Prende l'email inserita e estrae la parte prima del simbolo "@" (ad esempio 'nomeutente' da 'nomeutente@email.com')
    const username = document.getElementById("email").value.split("@")[0]; 
    // Salva il nome utente nel localStorage per mantenerlo disponibile anche dopo il riavvio della pagina
    localStorage.setItem("username", username);
    // Reindirizza l'utente alla pagina principale (home) dopo che il login è stato eseguito
    window.location.href = "index.html"; 
}


// Funzione per gestire l'accesso anonimo
function handleGuestAccess() {
    const guestName = prompt("Inserisci un nome per accedere come ospite:", "Ospite");
    if (guestName) {
        localStorage.setItem("username", guestName);
        window.location.href = "index.html"; // Reindirizza alla home
    }
}