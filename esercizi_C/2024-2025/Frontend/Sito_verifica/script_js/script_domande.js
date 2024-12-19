// Funzione per recuperare il parametro 'id' dalla query string
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search); // Crea un oggetto URLSearchParams per analizzare la query string
    return urlParams.get(param); // Ottieni il valore del parametro dalla query string (ad esempio 'id')
}

// Funzione per caricare le domande dal file JSON
async function loadQuestions() {
    try {
        // Carica il file domande.json usando fetch
        const response = await fetch("domande.json");

        // Verifica che la risposta sia ok (status 200)
        if (!response.ok) {
            throw new Error(`Errore nel caricamento del file JSON: ${response.statusText}`);
        }

        // Ottieni i dati JSON dalla risposta
        const questions = await response.json();

        // Recupera l'ID dalla query string dell'URL
        const questionId = getQueryParam('id');

        // Se l'ID non è presente nella query string, mostra un errore
        if (questionId === null) {
            throw new Error("Nessun ID trovato nella query string.");
        }

        // Carica la domanda corrispondente all'ID
        loadQuestion(questionId, questions);

    } catch (error) {
        // Gestisci eventuali errori e mostra un messaggio di errore
        console.error("Errore:", error);
        document.getElementById('content').innerText = `Errore nel caricamento delle domande: ${error.message}`;
    }
}

// Funzione per caricare la domanda basata sull'ID
function loadQuestion(questionId, questions) {
    // Trova la domanda corrispondente all'ID nel file JSON
    const question = questions.find(q => q.id == questionId);

    // Se la domanda è trovata, visualizzala
    if (question) {
        document.getElementById('content').innerText = question.domanda; // Mostra la domanda nella pagina

        // Recupera la risposta precedentemente salvata (se presente)
        const savedResponse = localStorage.getItem(`response_${questionId}`);
        if (savedResponse) {
            document.getElementById("response-input").value = savedResponse; // Precompila il campo di risposta se esiste una risposta salvata
        }

    } else {
        // Se la domanda non è trovata, mostra un messaggio di errore
        document.getElementById('content').innerText = "Domanda non trovata.";
    }
}

// Funzione per inviare la risposta
function submitAnswer() {
    const questionId = getQueryParam('id'); // Recupera l'ID della domanda dalla query string
    const response = document.getElementById("response-input").value; // Ottieni la risposta dall'input dell'utente

    // Se la risposta è vuota, mostra un messaggio di avviso
    if (response.trim() === "") {
        alert("Per favore, scrivi una risposta prima di inviare.");
    } else {
        // Salva la risposta nel localStorage con la chiave 'response_' + ID domanda
        localStorage.setItem(`response_${questionId}`, response);

        // Puoi aggiungere qui la logica per inviare la risposta a un server, se necessario
        console.log("Risposta inviata:", response); // Mostra la risposta nel console log

        // Mostra un messaggio di conferma all'utente
        alert("Risposta salvata!");
    }
}

// Carica le domande quando la pagina è pronta (evento DOMContentLoaded)
document.addEventListener('DOMContentLoaded', loadQuestions);
