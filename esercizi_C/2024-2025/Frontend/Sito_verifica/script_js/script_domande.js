// Funzione per recuperare il parametro 'id' dalla query string
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param); // Ottieni il parametro 'id' dalla query string
}

// Funzione per caricare le domande dal file JSON
async function loadQuestions() {
    try {
        // Carica il file domande.json
        const response = await fetch("domande.json"); 
        
        // Verifica che la risposta sia ok
        if (!response.ok) {
            throw new Error(`Errore nel caricamento del file JSON: ${response.statusText}`);
        }

        // Ottieni i dati JSON
        const questions = await response.json(); 
        
        // Recupera l'ID dalla query string dell'URL
        const questionId = getQueryParam('id'); 
        
        if (questionId === null) {
            throw new Error("Nessun ID trovato nella query string.");
        }

        // Carica la domanda corrispondente all'ID
        loadQuestion(questionId, questions);

    } catch (error) {
        console.error("Errore:", error);
        document.getElementById('content').innerText = `Errore nel caricamento delle domande: ${error.message}`;
    }
}

// Funzione per caricare la domanda basata sull'ID
function loadQuestion(questionId, questions) {
    // Trova la domanda corrispondente all'ID
    const question = questions.find(q => q.id == questionId); 

    // Se la domanda è trovata, visualizzala, altrimenti mostra un errore
    if (question) {
        document.getElementById('content').innerText = question.domanda;
    } else {
        document.getElementById('content').innerText = "Domanda non trovata.";
    }
}

// Funzione per inviare la risposta
function submitAnswer() {
    const response = document.getElementById("response-input").value;
    if (response.trim() === "") {
        alert("Per favore, scrivi una risposta prima di inviare.");
    } else {
        // Puoi aggiungere qui la logica per inviare la risposta, ad esempio inviarla al server
        console.log("Risposta inviata:", response);
    }
}

// Carica le domande quando la pagina è pronta
document.addEventListener('DOMContentLoaded', loadQuestions);
