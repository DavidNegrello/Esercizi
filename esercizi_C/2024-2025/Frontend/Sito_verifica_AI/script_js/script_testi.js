// Funzione per caricare un file JSON in modo asincrono
async function loadData(file) {
    try {
        // Carica il file JSON tramite una richiesta fetch
        const response = await fetch(file);

        // Verifica che la risposta sia corretta
        if (!response.ok) throw new Error('Errore nel caricamento del file JSON');

        // Restituisce i dati del file JSON
        return await response.json();
    } catch (error) {
        console.error('Errore:', error); // Gestisce eventuali errori nel caricamento
    }
}

// Evento che si attiva quando la pagina è completamente caricata
document.addEventListener('DOMContentLoaded', async function () {
    // Estrae l'ID dal parametro 'id' della query string dell'URL
    const urlParams = new URLSearchParams(window.location.search);
    const id = parseInt(urlParams.get('id'), 10); // Converte il parametro in un intero

    // Se l'ID non è valido, mostra un errore
    if (!id) {
        document.getElementById('content').innerHTML = '<p>Testo non trovato.</p>';
        return;
    }

    // Carica il contenuto del file 'testi.json'
    const data = await loadData('testi.json');
    if (!data) return; // Se non ci sono dati, esci dalla funzione

    // Trova l'elemento del testo che corrisponde all'ID
    const item = data.find(t => t.id === id);
    if (item) {
        // Mostra il titolo e il contenuto del testo
        document.getElementById('content').innerHTML = `
            <h2>${item.titolo}</h2>
            <p>${item.contenuto}</p>
        `;

        // Recupera le domande relative a questo testo
        const questions = item.domande;
        let currentQuestionIndex = 0; // Indice della domanda corrente

        // Carica le risposte dell'utente, se esistono, dal localStorage
        let userResponses = JSON.parse(localStorage.getItem(`userResponses_${id}`)) || []; 

        // Funzione per aggiornare i pulsanti "Domanda precedente" e "Domanda successiva"
        function updateButtons() {
            // Disabilita i pulsanti se siamo all'inizio o alla fine della lista di domande
            document.getElementById('prev-question').disabled = currentQuestionIndex === 0;
            document.getElementById('next-question').disabled = currentQuestionIndex >= questions.length - 1;
        }

        // Funzione per caricare una domanda e le sue opzioni
        function loadQuestion(index) {
            const question = questions[index]; // Ottiene la domanda corrente
            document.getElementById('question').textContent = question.domanda; // Mostra la domanda

            const optionsContainer = document.getElementById('options');
            optionsContainer.innerHTML = ''; // Pulisce le opzioni precedenti

            // Aggiunge ogni opzione di risposta come un elemento della lista
            question.opzioni.forEach(option => {
                const li = document.createElement('li');
                li.innerHTML = `<label><input type="radio" name="question${index}" value="${option}"> ${option}</label>`;
                optionsContainer.appendChild(li); // Aggiunge l'opzione al contenitore
            });

            // Pre-seleziona la risposta salvata se esiste
            if (userResponses[index] !== undefined) {
                const savedResponse = userResponses[index];
                const inputs = document.querySelectorAll(`input[name="question${index}"]`);
                inputs.forEach(input => {
                    if (input.value === savedResponse) {
                        input.checked = true; // Se la risposta corrisponde a quella salvata, la seleziona
                    }
                });
            }

            updateButtons(); // Aggiorna lo stato dei pulsanti
        }

        // Funzione per inviare la risposta selezionata
        function submitAnswer() {
            // Trova l'opzione selezionata
            const selectedOption = document.querySelector('input[name="question' + currentQuestionIndex + '"]:checked');
            if (selectedOption) {
                // Salva la risposta nel localStorage
                userResponses[currentQuestionIndex] = selectedOption.value;
                localStorage.setItem(`userResponses_${id}`, JSON.stringify(userResponses)); // Salva le risposte per il testo corrente

                // Disabilita il pulsante "Invia Risposta" per evitare invii ripetuti
                document.getElementById('submit-answer').disabled = true;
                alert('Risposta salvata!');
            } else {
                alert('Per favore, seleziona una risposta prima di inviare.'); // Messaggio di avviso se nessuna risposta è selezionata
            }
        }

        // Gestisce il click sul pulsante "Domanda successiva"
        document.getElementById('next-question').addEventListener('click', function () {
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++; // Passa alla domanda successiva
                loadQuestion(currentQuestionIndex); // Carica la nuova domanda
                // Riabilita il pulsante "Invia Risposta" quando si cambia domanda
                document.getElementById('submit-answer').disabled = false;
            } else {
                // Se siamo all'ultima domanda, mostra un messaggio di completamento
                document.getElementById('question').textContent = 'Hai completato tutte le domande!';
                document.getElementById('options').innerHTML = ''; // Pulisce le opzioni
                updateButtons(); // Aggiorna i pulsanti
            }
        });

        // Gestisce il click sul pulsante "Domanda precedente"
        document.getElementById('prev-question').addEventListener('click', function () {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--; // Torna alla domanda precedente
                loadQuestion(currentQuestionIndex); // Carica la domanda precedente
                // Riabilita il pulsante "Invia Risposta" quando si cambia domanda
                document.getElementById('submit-answer').disabled = false;
            }
        });

        // Ascolta le modifiche nelle risposte (quando l'utente seleziona una nuova opzione)
        document.getElementById('options').addEventListener('change', function () {
            const selectedOption = document.querySelector('input[name="question' + currentQuestionIndex + '"]:checked');
            if (selectedOption) {
                userResponses[currentQuestionIndex] = selectedOption.value; // Salva la risposta selezionata
                localStorage.setItem(`userResponses_${id}`, JSON.stringify(userResponses)); // Salva nel localStorage
            }
        });

        // Crea e aggiungi il pulsante "Invia Risposta"
        const submitButton = document.createElement('button');
        submitButton.id = 'submit-answer';
        submitButton.textContent = 'Invia Risposta';
        submitButton.addEventListener('click', submitAnswer);

        // Aggiunge il pulsante alla sezione delle domande
        document.querySelector('.question-section').appendChild(submitButton);

        // Carica la prima domanda
        loadQuestion(currentQuestionIndex);

    } else {
        // Se il testo con l'ID specificato non esiste, mostra un errore
        document.getElementById('content').innerHTML = '<p>Testo non trovato.</p>';
    }
});
