document.addEventListener("DOMContentLoaded", function() {
    // Ottieni l'ID del profilo dall'URL
    const urlParams = new URLSearchParams(window.location.search);
    const profileId = urlParams.get('id');
    const isTest = urlParams.get('test') === '1';
    
    // Se è un test o abbiamo un ID profilo, carica i risultati
    if (isTest || profileId) {
        caricaRisultati(profileId);
    } else {
        alert("Errore: Profilo non trovato.");
        window.location.href = "questionario.html";
    }
    
    // Event listeners per i pulsanti
    document.getElementById("aggiungi-configurazione").addEventListener("click", aggiungiConfigurazioneAlCarrello);
    document.getElementById("scopri-abbonamento").addEventListener("click", mostraDettagliAbbonamento);
    
    /**
     * Carica i risultati del questionario
     */
    function caricaRisultati(profileId) {
        if (isTest) {
            // Dati di esempio per test
            mostraRisultatiTest();
        } else {
            // Carica i dati dal server
            fetch(`../php/get_profile.php?id=${profileId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("Errore nel caricamento del profilo:", data.error);
                        mostraRisultatiTest(); // Mostra dati di esempio in caso di errore
                    } else {
                        mostraProfilo(data);
                    }
                })
                .catch(error => {
                    console.error("Errore nella richiesta:", error);
                    mostraRisultatiTest(); // Mostra dati di esempio in caso di errore
                });
        }
    }
    
    /**
     * Mostra i risultati di test
     */
    function mostraRisultatiTest() {
        // Profilo di esempio
        const profilo = {
            tipo: "gamer",
            descrizione: "Sei un appassionato di gaming che cerca prestazioni elevate per i giochi più recenti.",
            icona: "gamepad",
            configurazione: [
                { nome: "Processore Intel Core i7-12700K", prezzo: 399.99 },
                { nome: "Scheda Video NVIDIA RTX 3070", prezzo: 599.99 },
                { nome: "RAM 32GB DDR4 3600MHz", prezzo: 159.99 },
                { nome: "SSD NVMe 1TB", prezzo: 129.99 },
                { nome: "Alimentatore 750W Gold", prezzo: 99.99 }
            ],
            abbonamento: {
                nome: "Gaming Pro",
                descrizione: "Ricevi ogni mese nuovi componenti e accessori gaming selezionati per te.",
                prezzo: 49.99,
                durata: "mensile"
            },
            prodotti: [
                { id: 1, nome: "Mouse Gaming RGB", descrizione: "Mouse ad alta precisione con illuminazione RGB", prezzo: 59.99, immagine: "https://via.placeholder.com/150" },
                { id: 2, nome: "Tastiera Meccanica", descrizione: "Tastiera meccanica con switch Cherry MX", prezzo: 89.99, immagine: "https://via.placeholder.com/150" },
                { id: 3, nome: "Cuffie Gaming 7.1", descrizione: "Cuffie con audio surround 7.1", prezzo: 79.99, immagine: "https://via.placeholder.com/150" },
                { id: 4, nome: "Mousepad XXL", descrizione: "Tappetino per mouse di grandi dimensioni", prezzo: 29.99, immagine: "https://via.placeholder.com/150" }
            ]
        };
        
        mostraProfilo(profilo);
    }
    
    /**
     * Mostra il profilo dell'utente
     */
    function mostraProfilo(profilo) {
        // Aggiorna l'icona del profilo
        const iconaMap = {
            "gamer": "gamepad",
            "creativo": "paint-brush",
            "sviluppatore": "code",
            "business": "briefcase",
            "casual": "user"
        };
        
        const icona = iconaMap[profilo.tipo] || profilo.icona || "user";
        document.getElementById("profile-icon").className = `fas fa-${icona} fa-5x text-primary`;
        
        // Aggiorna il titolo del profilo
        const titoloMap = {
            "gamer": "Profilo Gaming",
            "creativo": "Profilo Creativo",
            "sviluppatore": "Profilo Sviluppatore",
            "business": "Profilo Business",
            "casual": "Profilo Casual"
        };
        
        document.getElementById("profile-title").textContent = titoloMap[profilo.tipo] || "Il tuo profilo";
        
        // Aggiorna la descrizione del profilo
        document.getElementById("profile-description").textContent = profilo.descrizione || "Abbiamo analizzato le tue risposte e creato un profilo personalizzato.";
        
        // Mostra la configurazione consigliata
        mostraConfigurazione(profilo.configurazione);
        
        // Mostra l'abbonamento consigliato
        mostraAbbonamento(profilo.abbonamento);
        
        // Mostra i prodotti consigliati
        mostraProdottiConsigliati(profilo.prodotti);
    }
    
    /**
     * Mostra la configurazione consigliata
     */
    function mostraConfigurazione(configurazione) {
        const container = document.getElementById("configurazione-consigliata");
        let html = "";
        let totale = 0;
        
        if (configurazione && configurazione.length > 0) {
            configurazione.forEach(item => {
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        ${item.nome}
                        <span class="badge bg-primary rounded-pill">${item.prezzo.toFixed(2)}€</span>
                    </li>
                `;
                totale += parseFloat(item.prezzo);
            });
            
            html += `
                <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                    Totale
                    <span class="badge bg-success rounded-pill">${totale.toFixed(2)}€</span>
                </li>
            `;
        } else {
            html = `<li class="list-group-item">Nessuna configurazione disponibile</li>`;
        }
        
        container.innerHTML = html;
    }
    
    /**
     * Mostra l'abbonamento consigliato
     */
    function mostraAbbonamento(abbonamento) {
        const container = document.getElementById("abbonamento-consigliato");
        
        if (abbonamento) {
            container.innerHTML = `
                <div class="card border-info">
                    <div class="card-body">
                        <h5 class="card-title">${abbonamento.nome}</h5>
                        <p class="card-text">${abbonamento.descrizione}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-info">${abbonamento.durata}</span>
                            <span class="fw-bold">${abbonamento.prezzo.toFixed(2)}€</span>
                        </div>
                    </div>
                </div>
            `;
        } else {
            container.innerHTML = `<p>Nessun abbonamento disponibile</p>`;
        }
    }
    
    /**
     * Mostra i prodotti consigliati
     */
    function mostraProdottiConsigliati(prodotti) {
        const container = document.getElementById("prodotti-consigliati");
        
        if (prodotti && prodotti.length > 0) {
            let html = "";
            
            prodotti.forEach(prodotto => {
                html += `
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <img src="${prodotto.immagine}" class="card-img-top" alt="${prodotto.nome}">
                            <div class="card-body">
                                <h5 class="card-title">${prodotto.nome}</h5>
                                <p class="card-text small">${prodotto.descrizione}</p>
                                <p class="card-text fw-bold">${prodotto.prezzo.toFixed(2)}€</p>
                                <button class="btn btn-sm btn-primary aggiungi-prodotto" data-id="${prodotto.id}">
                                    <i class="fas fa-cart-plus"></i> Aggiungi
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            
            // Aggiungi event listener ai pulsanti "Aggiungi"
            document.querySelectorAll(".aggiungi-prodotto").forEach(button => {
                button.addEventListener("click", function() {
                    const prodottoId = this.getAttribute("data-id");
                    aggiungiProdottoAlCarrello(prodottoId);
                });
            });
        } else {
            container.innerHTML = `<div class="col-12"><p>Nessun prodotto consigliato disponibile</p></div>`;
        }
    }
    
    /**
     * Aggiunge la configurazione consigliata al carrello
     */
    function aggiungiConfigurazioneAlCarrello() {
        const items = document.querySelectorAll("#configurazione-consigliata .list-group-item:not(:last-child)");
        
        if (items.length === 0) {
            alert("Nessuna configurazione disponibile da aggiungere al carrello.");
            return;
        }
        
        // Crea un array di prodotti dalla configurazione
        const prodotti = [];
        items.forEach(item => {
            const nome = item.textContent.trim().split('\\n')[0].trim();
            const prezzo = parseFloat(item.querySelector(".badge").textContent.replace('€', ''));
            
            prodotti.push({
                nome: nome,
                prezzo: prezzo,
                immagine: "https://via.placeholder.com/100", // Immagine placeholder
                attivo: true
            });
        });
        
        // Aggiungi i prodotti al carrello
        let carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };
        carrelloCatalogo.prodotti = [...carrelloCatalogo.prodotti, ...prodotti];
        localStorage.setItem("carrelloCatalogo", JSON.stringify(carrelloCatalogo));
        
        alert("Configurazione aggiunta al carrello!");
        
        // Aggiorna il contatore del carrello
        if (typeof aggiornaContatoreCarrello === 'function') {
            aggiornaContatoreCarrello();
        }
    }
    
    /**
     * Mostra i dettagli dell'abbonamento
     */
    function mostraDettagliAbbonamento() {
        // Reindirizza alla pagina degli abbonamenti
        window.location.href = "abbonamenti.html";
    }
    
    /**
     * Aggiunge un prodotto consigliato al carrello
     */
    function aggiungiProdottoAlCarrello(prodottoId) {
        // Trova il prodotto nella lista dei prodotti consigliati
        const prodottoElement = document.querySelector(`.aggiungi-prodotto[data-id="${prodottoId}"]`).closest(".card");
        const nome = prodottoElement.querySelector(".card-title").textContent;
        const prezzo = parseFloat(prodottoElement.querySelector(".fw-bold").textContent.replace('€', ''));
        const immagine = prodottoElement.querySelector(".card-img-top").src;
        
        // Crea l'oggetto prodotto
        const prodotto = {
            id: prodottoId,
            nome: nome,
            prezzo: prezzo,
            immagine: immagine,
            attivo: true
        };
        
        // Aggiungi il prodotto al carrello
        let carrelloCatalogo = JSON.parse(localStorage.getItem("carrelloCatalogo")) || { prodotti: [] };
        carrelloCatalogo.prodotti.push(prodotto);
        localStorage.setItem("carrelloCatalogo", JSON.stringify(carrelloCatalogo));
        
        // Mostra un messaggio di conferma
        alert(`"${nome}" aggiunto al carrello!`);
        
        // Aggiorna il contatore del carrello
        if (typeof aggiornaContatoreCarrello === 'function') {
            aggiornaContatoreCarrello();
        }
    }
});