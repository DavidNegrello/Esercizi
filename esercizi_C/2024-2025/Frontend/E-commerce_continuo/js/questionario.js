document.addEventListener("DOMContentLoaded", function() {
    // Riferimenti agli elementi DOM
    const form = document.getElementById("questionario-form");
    const domandeContainer = document.getElementById("domande-container");
    const prevBtn = document.getElementById("prev-btn");
    const nextBtn = document.getElementById("next-btn");
    const submitBtn = document.getElementById("submit-btn");
    const progressBar = document.getElementById("progress-bar");
    
    // Stato del questionario
    let domande = [];
    let domandaCorrente = 0;
    let risposte = {};
    
    // Carica le domande dal server
    caricaDomande();
    
    // Gestione pulsanti di navigazione
    prevBtn.addEventListener("click", mostraDomandaPrecedente);
    nextBtn.addEventListener("click", mostraDomandaSuccessiva);
    form.addEventListener("submit", inviaRisposte);
    
    // Funzione per caricare le domande dal server
    function caricaDomande() {
        // In un'implementazione reale, questo dovrebbe essere un fetch a un endpoint PHP
        fetch("../php/load_data.php?type=questionario")
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error("Errore nel caricamento delle domande:", data.error);
                    domandeContainer.innerHTML = `<div class="alert alert-danger">Errore nel caricamento delle domande. Riprova più tardi.</div>`;
                    } else {
                    domande = data;
                    if (domande.length > 0) {
                        mostraDomanda(0);
                        aggiornaProgressBar();
                    } else {
                        domandeContainer.innerHTML = `<div class="alert alert-warning">Nessuna domanda disponibile al momento.</div>`;
                    }
                }
            })
            .catch(error => {
                console.error("Errore nella richiesta:", error);
                domandeContainer.innerHTML = `<div class="alert alert-danger">Errore di connessione. Riprova più tardi.</div>`;
                
                // Per test, carica domande di esempio
                caricaDomandeEsempio();
            });
    }
    
    // Funzione per caricare domande di esempio (solo per test)
    function caricaDomandeEsempio() {
        domande = [
            {
                id: 1,
                domanda: "Per quale scopo principale utilizzerai il tuo PC?",
                tipo: "singola",
                opzioni: ["Gaming", "Lavoro d'ufficio", "Editing video/foto", "Programmazione", "Navigazione web"]
            },
            {
                id: 2,
                domanda: "Qual è il tuo budget approssimativo?",
                tipo: "singola",
                opzioni: ["Meno di 500€", "500€ - 1000€", "1000€ - 1500€", "1500€ - 2000€", "Oltre 2000€"]
            },
            {
                id: 3,
                domanda: "Quali giochi o software utilizzerai principalmente?",
                tipo: "testo"
            },
            {
                id: 4,
                domanda: "Quali caratteristiche sono più importanti per te?",
                tipo: "multipla",
                opzioni: ["Prestazioni elevate", "Silenziosità", "Design accattivante", "Facilità di aggiornamento", "Risparmio energetico"]
            },
            {
                id: 5,
                domanda: "Quanto spazio di archiviazione ti serve?",
                tipo: "singola",
                opzioni: ["Meno di 500GB", "500GB - 1TB", "1TB - 2TB", "Più di 2TB"]
            }
        ];
        
        if (domande.length > 0) {
            mostraDomanda(0);
            aggiornaProgressBar();
        }
    }
    
    // Funzione per mostrare una domanda specifica
    function mostraDomanda(index) {
        if (index < 0 || index >= domande.length) return;
        
        const domanda = domande[index];
        let html = `
            <div class="domanda mb-4">
                <h4 class="mb-3">${index + 1}. ${domanda.domanda}</h4>
        `;
        
        switch (domanda.tipo) {
            case "singola":
                html += `<div class="opzioni-container">`;
                domanda.opzioni.forEach((opzione, i) => {
                    const checked = risposte[domanda.id] === opzione ? "checked" : "";
                    html += `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="domanda_${domanda.id}" 
                                id="opzione_${domanda.id}_${i}" value="${opzione}" ${checked} required>
                            <label class="form-check-label" for="opzione_${domanda.id}_${i}">
                                ${opzione}
                            </label>
                        </div>
                    `;
                });
                html += `</div>`;
                break;
                
            case "multipla":
                html += `<div class="opzioni-container">`;
                domanda.opzioni.forEach((opzione, i) => {
                    const checked = risposte[domanda.id] && risposte[domanda.id].includes(opzione) ? "checked" : "";
                    html += `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="domanda_${domanda.id}" 
                                id="opzione_${domanda.id}_${i}" value="${opzione}" ${checked}>
                            <label class="form-check-label" for="opzione_${domanda.id}_${i}">
                                ${opzione}
                            </label>
                        </div>
                    `;
                });
                html += `</div>`;
                break;
                
            case "testo":
                const value = risposte[domanda.id] || "";
                html += `
                    <div class="form-group">
                        <textarea class="form-control" name="domanda_${domanda.id}" 
                            rows="3" required>${value}</textarea>
                    </div>
                `;
                break;
                
            case "numero":
                const numValue = risposte[domanda.id] || "";
                html += `
                    <div class="form-group">
                        <input type="number" class="form-control" name="domanda_${domanda.id}" 
                            value="${numValue}" required>
                    </div>
                `;
                break;
        }
        
        html += `</div>`;
        domandeContainer.innerHTML = html;
        
        // Aggiorna lo stato dei pulsanti
        prevBtn.disabled = index === 0;
        nextBtn.style.display = index === domande.length - 1 ? "none" : "block";
        submitBtn.style.display = index === domande.length - 1 ? "block" : "none";
        
        // Aggiorna l'indice della domanda corrente
        domandaCorrente = index;
    }
    
    // Funzione per mostrare la domanda precedente
    function mostraDomandaPrecedente() {
        salvaDomandaCorrente();
        mostraDomanda(domandaCorrente - 1);
        aggiornaProgressBar();
    }
    
    // Funzione per mostrare la domanda successiva
    function mostraDomandaSuccessiva() {
        if (validaDomandaCorrente()) {
            salvaDomandaCorrente();
            mostraDomanda(domandaCorrente + 1);
            aggiornaProgressBar();
        }
    }
    
    // Funzione per salvare la risposta alla domanda corrente
    function salvaDomandaCorrente() {
        const domanda = domande[domandaCorrente];
        
        switch (domanda.tipo) {
            case "singola":
                const radioSelezionato = document.querySelector(`input[name="domanda_${domanda.id}"]:checked`);
                if (radioSelezionato) {
                    risposte[domanda.id] = radioSelezionato.value;
                }
                break;
                
            case "multipla":
                const checkboxSelezionati = document.querySelectorAll(`input[name="domanda_${domanda.id}"]:checked`);
                risposte[domanda.id] = Array.from(checkboxSelezionati).map(cb => cb.value);
                break;
                
            case "testo":
            case "numero":
                const input = document.querySelector(`[name="domanda_${domanda.id}"]`);
                if (input) {
                    risposte[domanda.id] = input.value;
                }
                break;
        }
    }
    
    // Funzione per validare la domanda corrente
    function validaDomandaCorrente() {
        const domanda = domande[domandaCorrente];
        
        switch (domanda.tipo) {
            case "singola":
                const radioSelezionato = document.querySelector(`input[name="domanda_${domanda.id}"]:checked`);
                if (!radioSelezionato) {
                    alert("Seleziona un'opzione per continuare.");
                    return false;
                }
                break;
                
            case "testo":
            case "numero":
                const input = document.querySelector(`[name="domanda_${domanda.id}"]`);
                if (!input || !input.value.trim()) {
                    alert("Questo campo è obbligatorio.");
                    return false;
                }
                break;
        }
        
        return true;
    }
    
    // Funzione per aggiornare la barra di progresso
    function aggiornaProgressBar() {
        const percentuale = Math.round(((domandaCorrente + 1) / domande.length) * 100);
        progressBar.style.width = `${percentuale}%`;
        progressBar.textContent = `${percentuale}%`;
        progressBar.setAttribute("aria-valuenow", percentuale);
    }
    
    // Funzione per inviare le risposte
    function inviaRisposte(event) {
        event.preventDefault();
        
        // Salva l'ultima domanda
        salvaDomandaCorrente();
        
        // Verifica che tutte le domande obbligatorie siano state risposte
        if (!validaTutteLeRisposte()) {
            return;
        }
        
        // Prepara i dati da inviare
        const datiDaInviare = {
            sessionId: window.getCurrentId(),
            risposte: risposte
        };
        
        // Invia i dati al server
        fetch("../php/save_questionario.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(datiDaInviare)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reindirizza alla pagina dei risultati
                window.location.href = `risultati_questionario.html?id=${data.profileId}`;
            } else {
                alert("Si è verificato un errore durante il salvataggio delle risposte. Riprova.");
            }
        })
        .catch(error => {
            console.error("Errore nell'invio delle risposte:", error);
            alert("Si è verificato un errore di connessione. Riprova più tardi.");
            
            // Per test, reindirizza comunque
            window.location.href = "risultati_questionario.html?test=1";
        });
    }
    
    // Funzione per validare tutte le risposte
    function validaTutteLeRisposte() {
        for (let i = 0; i < domande.length; i++) {
            const domanda = domande[i];
            
            // Verifica se la domanda è stata risposta
            if (domanda.tipo === "singola" || domanda.tipo === "testo" || domanda.tipo === "numero") {
                if (!risposte[domanda.id]) {
                    alert(`La domanda ${i + 1} è obbligatoria.`);
                    mostraDomanda(i);
                    return false;
                }
            }
        }
        
        return true;
    }
});