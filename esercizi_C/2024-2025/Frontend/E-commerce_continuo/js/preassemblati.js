//========================PREASSEMBLATI==================
document.addEventListener("DOMContentLoaded", function () {
    fetch("../data/preassemblati.json")
        .then(response => response.json())
        .then(data => {
            const catalogo = document.getElementById("catalogo");
            const categorie = {};

            data.forEach(prodotto => {
                if (!categorie[prodotto.categoria]) {
                    categorie[prodotto.categoria] = [];
                }
                categorie[prodotto.categoria].push(prodotto);
            });

            Object.keys(categorie).forEach(categoria => {
                const section = document.createElement("div");
                section.classList.add("mb-5");
                section.innerHTML = `
                    <h2 class="text-center">${categoria}</h2>
                    <div class="row" id="sezione-${categoria.replace(/\s+/g, '-')}"></div>
                `;
                catalogo.appendChild(section);

                const sezioneProdotti = document.getElementById(`sezione-${categoria.replace(/\s+/g, '-')}`);

                categorie[categoria].forEach(prodotto => {
                    const card = document.createElement("div");
                    card.classList.add("col-md-4", "mb-4");

                    card.innerHTML = `
                        <div class="card shadow-sm">
                            <img src="${prodotto.immagine}" class="card-img-top" alt="${prodotto.nome}">
                            <div class="card-body">
                                <h5 class="card-title">${prodotto.nome}</h5>
                                <p class="card-text">${prodotto.descrizione}</p>
                                <h6 class="text-primary">${prodotto.prezzo} €</h6>
                                <a href="dettaglio.html?id=${prodotto.id}" class="btn btn-dark w-100">Visualizza</a>
                            </div>
                        </div>
                    `;

                    sezioneProdotti.appendChild(card);
                });
            });
        })
        .catch(error => console.error("Errore nel caricamento dei dati:", error));
});



//=========================Dettaglio.html=======================
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const prodottoId = params.get("id");

    if (!prodottoId) {
        document.getElementById("dettaglio-prodotto").innerHTML = "<p>Prodotto non trovato.</p>";
        return;
    }

    fetch("../data/contenuti_pc.json")
        .then(response => response.json())
        .then(data => {
            const prodotto = data.prodotti.find(p => p.id === prodottoId);

            if (!prodotto) {
                document.getElementById("dettaglio-prodotto").innerHTML = "<p>Prodotto non trovato.</p>";
                return;
            }

            // Carica le specifiche base
            let specificheHTML = "";
            for (const [chiave, valore] of Object.entries(prodotto.specifiche)) {
                specificheHTML += `<li><strong>${chiave}:</strong> ${valore}</li>`;
            }

            // Carica le specifiche dettagliate
            let specificheDettagliateHTML = "";
            if (prodotto.specifiche_dettagliate) {
                specificheDettagliateHTML = Object.entries(prodotto.specifiche_dettagliate).map(([chiave, valore]) => `
                    <div class="specifica-dettagliata">
                        <div class="specifica-titolo">${chiave}</div>
                        <div class="specifica-valore">${valore}</div>
                    </div>
                `).join('');
            }

            // Carica le miniature delle immagini
            let miniatureHTML = prodotto.immagini.map((img, index) => `
                <img src="${img}" class="miniatura ${index === 0 ? 'active' : ''}" data-index="${index}" alt="Miniatura ${index + 1}" style="width: 50px; cursor: pointer; margin-right: 5px;">
            `).join('');

            // Carica i colori disponibili
            let coloriDisponibili = prodotto.colori ? prodotto.colori.map(colore => `
                <button class="btn btn-outline-dark colore-btn" data-colore="${colore}">${colore}</button>
            `).join('') : "<p>Nessuna opzione colore disponibile.</p>";

            // Carica le informazioni aggiuntive
            let informazioniAggiuntiveHTML = prodotto.informazioniAggiuntive ? prodotto.informazioniAggiuntive.map(img => `
                <img src="${img}" class="img-fluid mb-2" alt="Informazione Aggiuntiva">
            `).join('') : "<p>Nessuna informazione aggiuntiva disponibile.</p>";

            // Carica le personalizzazioni
            let personalizzazioniHTML = prodotto.personalizzazioni ? prodotto.personalizzazioni.map(opzione => `
                <li>
                    ${opzione.nome} - +${opzione.prezzo}€
                    <button class="btn btn-outline-success btn-sm aggiungi-personalizzazione" data-prezzo="${opzione.prezzo}" data-nome="${opzione.nome}">+</button>
                </li>
            `).join('') : "<p>Nessuna opzione di personalizzazione disponibile.</p>";

            // Composizione finale dell'HTML
            document.getElementById("dettaglio-prodotto").innerHTML = `
                <div class="col-md-6">
                    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            ${prodotto.immagini.map((img, index) => `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                    <img src="${img}" class="d-block w-100" alt="${prodotto.nome}">
                                </div>
                            `).join('')}
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span>
                            <span class="visually-hidden">Precedente</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1);"></span>
                            <span class="visually-hidden">Successivo</span>
                        </button>
                    </div>
                    <div class="mt-2 d-flex">${miniatureHTML}</div>
                </div>
                <div class="col-md-6">
                    <h2>${prodotto.nome}</h2>
                    <p>${prodotto.descrizione}</p>
                    <h4 class="text-primary" id="prezzo">${prodotto.prezzo}</h4>
                    <button class="btn btn-success w-100" id="aggiungi-carrello">Aggiungi al Carrello</button>
                    <div class="mt-3">
                        <h5>Colori disponibili:</h5>
                        <div>${coloriDisponibili}</div>
                    </div>
                    <div class="mt-3">
                        <h5>Personalizzazione:</h5>
                        <ul>${personalizzazioniHTML}</ul>
                    </div>
                </div>
                <div class="col-md-12 mt-4">
                    <h3>Informazioni Aggiuntive</h3>
                    ${informazioniAggiuntiveHTML}
                </div>
                <div class="col-md-12 mt-4 specifiche-dettagliate">
                    <h3>Specifiche Dettagliate</h3>
                    ${specificheDettagliateHTML}
                </div>
            `;

            // Gestione delle miniature
            document.querySelectorAll(".miniatura").forEach(img => {
                img.addEventListener("click", function () {
                    let index = this.getAttribute("data-index");
                    document.querySelector(".carousel-inner .active").classList.remove("active");
                    document.querySelectorAll(".carousel-item")[index].classList.add("active");
                    document.querySelectorAll(".miniatura").forEach(mini => mini.classList.remove("active"));
                    this.classList.add("active");
                });
            });

            // Gestione del cambio di colore
            let coloreSelezionato = null;
            document.querySelectorAll(".colore-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    coloreSelezionato = this.getAttribute("data-colore");
                    const immaginiColore = prodotto.varianti[coloreSelezionato] || prodotto.immagini;
                    document.querySelector(".carousel-inner").innerHTML = immaginiColore.map((img, index) => `
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <img src="${img}" class="d-block w-100" alt="${prodotto.nome}">
                        </div>
                    `).join('');
                    document.querySelector(".mt-2").innerHTML = immaginiColore.map((img, index) => `
                        <img src="${img}" class="miniatura ${index === 0 ? 'active' : ''}" data-index="${index}" alt="Miniatura ${index + 1}" style="width: 50px; cursor: pointer; margin-right: 5px;">
                    `).join('');
                });
            });

            // Gestione del tasto + per aggiungere una personalizzazione
let prezzoBase = parseFloat(prodotto.prezzo.replace('€', '').replace(',', '.'));
document.querySelectorAll(".aggiungi-personalizzazione").forEach(btn => {
    btn.addEventListener("click", function () {
        const prezzoAggiuntivo = parseFloat(this.getAttribute("data-prezzo"));
        const nomePersonalizzazione = this.getAttribute("data-nome");
        prezzoBase += prezzoAggiuntivo;
        document.getElementById("prezzo").innerText = `${prezzoBase.toFixed(2)}€`;
        this.disabled = true; // Disabilita il tasto dopo che è stato aggiunto
        this.innerText = `Aggiunto - ${nomePersonalizzazione}`;
    });
});

// Gestione del click per aggiungere il prodotto preassemblato al carrello
document.getElementById("aggiungi-carrello").addEventListener("click", function () {
    const prodottoCarrello = {
        id: prodottoId,
        nome: prodotto.nome,
        prezzo: prodotto.prezzo,
        colore: coloreSelezionato || "Base",
        personalizzazioni: [],
        immagine: prodotto.immagini[0], // Assumiamo che la prima immagine sia quella principale
        tipo: "preassemblato" // Aggiungi il tipo di prodotto
    };

    // Aggiungi le personalizzazioni selezionate
    document.querySelectorAll(".aggiungi-personalizzazione:disabled").forEach(btn => {
        const nomePersonalizzazione = btn.getAttribute("data-nome");
        const prezzoPersonalizzazione = parseFloat(btn.getAttribute("data-prezzo"));
        prodottoCarrello.personalizzazioni.push({
            nome: nomePersonalizzazione,
            prezzo: prezzoPersonalizzazione
        });
    });

    // Recupera il carrello preassemblato dal localStorage
    const carrelloPreassemblato = JSON.parse(localStorage.getItem("carrelloPreassemblato")) || { prodotti: [] };
    carrelloPreassemblato.prodotti.push(prodottoCarrello);

    // Salva il carrello aggiornato nel localStorage
    localStorage.setItem("carrelloPreassemblato", JSON.stringify(carrelloPreassemblato));

    // Debugging
    console.log("Carrello Preassemblato aggiornato:", carrelloPreassemblato);

    alert("Prodotto preassemblato aggiunto al carrello!");
});




        })
        .catch(error => console.error("Errore nel caricamento del prodotto:", error));
});




