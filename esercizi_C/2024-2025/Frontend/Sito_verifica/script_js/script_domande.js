// Funzione per caricare i dati dal file JSON
async function loadData(file) {
    try {
        const response = await fetch(file); // Carica il file JSON
        if (!response.ok) throw new Error('Errore nel caricamento del file JSON');
        return await response.json(); // Restituisce il contenuto del file come oggetto JSON
    } catch (error) {
        console.error('Errore:', error); // In caso di errore, stampa un messaggio di errore
        return null; // Restituisce null in caso di errore
    }
}

// Al caricamento della pagina
document.addEventListener('DOMContentLoaded', async function () {
    // Recupera il parametro 'id' dalla URL
    const urlParams = new URLSearchParams(window.location.search);
    const id = parseInt(urlParams.get('id'), 10);

    // Se non c'è un id valido, mostra un messaggio di errore
    if (!id) {
        document.getElementById('content').innerHTML = '<p>Domanda non trovata.</p>';
        return;
    }

    // Carica il file JSON e mostra la domanda
    const data = await loadData('domande.json'); // Percorso relativo al file JSON
    if (!data) {
        document.getElementById('content').innerHTML = '<p>Errore nel caricamento delle domande.</p>';
        return;
    }

    // Trova la domanda in base all'id
    const item = data.find(q => q.id === id);
    if (item) {
        // Se la domanda è trovata, la mostra nella pagina
        document.getElementById('content').innerHTML = `
            <h2>${item.domanda}</h2>
            <p>${item.descrizione}</p>
        `;
    } else {
        // Se non è trovata, mostra un messaggio di errore
        document.getElementById('content').innerHTML = '<p>Domanda non trovata.</p>';
    }
});

// Funzione per inviare la risposta dell'utente
function submitAnswer() {
    const userAnswer = document.getElementById('response-input').value.trim(); // Ottieni la risposta dell'utente

    // Verifica se la risposta è vuota
    if (!userAnswer) {
        alert('Scrivi una risposta prima di inviarla!');
        return;
    }

    // Mostra un messaggio con la risposta dell'utente
    alert(`Risposta inviata: ${userAnswer}`);
    // Qui potresti inviare la risposta a un server o salvarla in un database
}
