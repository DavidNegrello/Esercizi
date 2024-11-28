// Funzione per gestire il login e mostrare il nome utente nella navbar
window.onload = function () {
    const username = localStorage.getItem("username");
    const userDropdown = document.getElementById("userDropdown");
    const dropdownMenu = document.getElementById("dropdownMenu");
    const userDropdownContainer = document.getElementById("userDropdownContainer");
    const body = document.body;

    // Se l'utente è loggato, mostra il nome utente e abilita i permessi
    if (username) {
        // Mostra il nome utente al posto del link "Login"
        userDropdown.innerText = username;
        // Aggiungi l'opzione di logout nel menu a tendina
        dropdownMenu.innerHTML = `<li><a class="dropdown-item" href="#" onclick="handleLogout()">Esci</a></li>`;

        // Rendi possibile la selezione del testo e il click destro
        body.classList.remove("no-select");  // Rimuove la classe che disabilita la selezione
        document.removeEventListener("contextmenu", disableRightClick);  // Abilita il click destro
        document.removeEventListener("keydown", disableCopy);  // Abilita la copia
    } else {
        // Se non c'è nome utente salvato, mantiene il link di login
        userDropdown.innerText = "Login";
        // Rimuovi l'opzione "Esci" se l'utente non è loggato
        dropdownMenu.innerHTML = `<li><a class="dropdown-item" href="/pagine/login.html">Login</a></li>`;

        // Disabilita la selezione del testo, click destro e copia
        body.classList.add("no-select"); // Disabilita la selezione del testo
        document.addEventListener("contextmenu", disableRightClick);  // Disabilita il click destro
        document.addEventListener("keydown", disableCopy);  // Disabilita la copia
    }
};

// Funzione per disabilitare il click destro
function disableRightClick(event) {
    event.preventDefault(); // Blocca il menu contestuale (click destro)
}

// Funzione per disabilitare copia, taglia e incolla
function disableCopy(event) {
    if ((event.ctrlKey || event.metaKey) && (event.key === "c" || event.key === "x" || event.key === "v")) {
        event.preventDefault(); // Impedisce le azioni di copia/taglia/incolla
    }
}

// Funzione per il logout
function handleLogout() {
    // Rimuovi l'utente dal localStorage
    localStorage.removeItem("username");
    // Reindirizza alla pagina di login
    window.location.href = "/pagine/login.html";
}