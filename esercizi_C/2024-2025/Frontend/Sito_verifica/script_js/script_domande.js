// Funzione per caricare i dati dal file JSON
async function loadData(file) {
    try {
        const response = await fetch(file); // Carica il file JSON
        if (!response.ok) {
            throw new Error('Errore nel caricare il file JSON');
        }
        const data = await response.json(); // Parso il JSON
        return data;
    } catch (error) {
        console.error("Errore nel caricare il JSON:", error);
    }
}

// Funzione che carica la domanda e la descrizione basata sull'ID
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search); // Ottieni i parametri dalla URL
    const id = parseInt(urlParams.get('id'), 10); // Prendi l'ID della domanda

    if (id) {
        // Carica il file JSON delle domande
        loadData('domande.json').then(data => {
            const item = data.find(item => item.id === id); // Trova la domanda con l'ID corrispondente
            if (item) {
                // Mostra la domanda e il campo di input per la risposta
                document.getElementById('content').innerHTML = `
                    <h2>${item.domanda}</h2>
                    <p>${item.descrizione}</p>
                    <input type="text" id="response-input" placeholder="Scrivi la tua risposta qui">
                `;
            } else {
                // Se non viene trovata la domanda
                document.getElementById('content').innerHTML = '<p>Domanda non trovata.</p>';
            }
        });
    }
});

// Funzione per inviare la risposta
function submitAnswer() {
    const userAnswer = document.getElementById('response-input').value; // Ottieni la risposta dell'utente

    if (userAnswer.trim() === "") {
        alert("Per favore, scrivi una risposta.");
    } else {
        alert(`Risposta inviata: ${userAnswer}`);
        // A questo punto, puoi fare ulteriori operazioni come inviare la risposta a un server o passare alla domanda successiva
    }
}
