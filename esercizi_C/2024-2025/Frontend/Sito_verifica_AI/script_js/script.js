// Funzione per avviare il timer
function startTimer(duration, display) {
    let timer = duration, minutes, seconds;
    setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            timer = duration;
        }
    }, 1000);
}

// Timer iniziale
document.addEventListener('DOMContentLoaded', function () {
    let timeLimit = 10 * 60; // 10 minuti
    let display = document.querySelector('#countdown');
    startTimer(timeLimit, display);
});

//===================================Consegna================================
// Funzione per raccogliere tutte le risposte e creare il file .txt
function saveAnswers() {
    let fileContent = ""; // Variabile per contenere il contenuto del file .txt

    // Array degli ID delle domande per cui raccogliere le risposte
    const questionIds = [1, 2, 3]; // Esempio: Domande 1, 2, 3 (ID delle domande)
    const userResponsesQuestions = []; // Array per memorizzare le risposte delle domande

    // Raccogli le risposte delle domande dal localStorage
    questionIds.forEach(id => {
        // Recupera la risposta per ciascuna domanda, se non esiste metti "vuota"
        const response = localStorage.getItem(`response_${id}`) || "vuota";
        userResponsesQuestions.push(response); // Aggiungi la risposta all'array
    });

    // Aggiungi le risposte delle domande al contenuto del file
    fileContent += "Risposte alle Domande:\n"; // Titolo della sezione delle domande
    questionIds.forEach((questionId, index) => {
        // Aggiungi la domanda e la risposta al contenuto del file
        fileContent += `Domanda ${questionId}: ${userResponsesQuestions[index]}\n`;
    });

    fileContent += "\n"; // Linea vuota tra le domande e i testi

    // Array degli ID dei testi per cui raccogliere le risposte
    const textIds = [1, 2]; // Esempio: Testo A (ID 1) e Testo B (ID 2)

    // Carica i dati dei testi dal file JSON
    loadData('testi.json').then(data => {
        if (!data) return; // Se i dati non vengono caricati, esci dalla funzione

        // Cicla attraverso ogni testo
        textIds.forEach(id => {
            // Carica le risposte per ciascun testo dal localStorage (se esistono)
            const userResponses = JSON.parse(localStorage.getItem(`userResponses_${id}`)) || [];
            // Trova il testo nel file JSON utilizzando l'ID
            const item = data.find(t => t.id === id);
            if (item) {
                // Aggiungi il titolo del testo al contenuto del file
                fileContent += `Testo ${item.titolo}:\n`;

                // Aggiungi le risposte per ciascuna domanda del testo
                item.domande.forEach((question, index) => {
                    // Recupera la risposta per la domanda, se non esiste metti "vuota"
                    const response = userResponses[index] || "vuota";
                    // Aggiungi la domanda e la risposta al contenuto del file
                    fileContent += `Domanda ${index + 1}: ${response}\n`;
                });

                fileContent += "\n"; // Linea vuota tra i testi
            }
        });

        // Crea un Blob con il contenuto del file
        const blob = new Blob([fileContent], { type: "text/plain" });
        // Crea un link per scaricare il file
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'risposte_completa.txt';  // Nome del file .txt che sarà scaricato
        link.click(); // Simula il clic per avviare il download del file
    });
}

// Aggiungi l'evento per il tasto "Consegna" che richiama la funzione saveAnswers()
document.getElementById('submit-answers').addEventListener('click', function () {
    saveAnswers(); // Quando si clicca su "Consegna", salva le risposte e crea il file
});

// Funzione per caricare i dati dei testi dal file JSON
async function loadData(file) {
    try {
        // Recupera i dati dal file JSON
        const response = await fetch(file);
        if (!response.ok) throw new Error('Errore nel caricamento del file JSON');
        return await response.json(); // Restituisce i dati in formato JSON
    } catch (error) {
        console.error('Errore:', error); // Gestisce eventuali errori nel caricamento
    }
}
