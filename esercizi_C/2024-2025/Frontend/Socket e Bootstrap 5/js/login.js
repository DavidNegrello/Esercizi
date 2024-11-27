// Funzione per gestire l'accesso normale
function handleLogin(event) {
    event.preventDefault();
    const username = document.getElementById("email").value.split("@")[0];
    localStorage.setItem("username", username);
    window.location.href = "../index.html"; // Reindirizza alla home
}

// Funzione per gestire l'accesso anonimo
function handleGuestAccess() {
    const guestName = prompt("Inserisci un nome per accedere come ospite:", "Ospite");
    if (guestName) {
        localStorage.setItem("username", guestName);
        window.location.href = "../index.html"; // Reindirizza alla home
    }
}