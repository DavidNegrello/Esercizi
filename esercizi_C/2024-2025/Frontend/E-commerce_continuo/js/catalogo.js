//====================CATALOGO=====================
document.addEventListener("DOMContentLoaded", function () {
    // Carica i dati del catalogo
    caricaCatalogo();
});

function caricaCatalogo() {
    // Mostra un loader mentre si caricano i dati
    document.getElementById("catalogo-prodotti").innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Caricamento...</span></div></div>';
    
    // Carica le categorie e marche per i filtri
    Promise.all([
        fetch('../api/prodotti.php?action=categories'),
        fetch('../api/prodotti.php?action=brands')
    ])
    .then(responses => Promise.all(responses.map(res => res.json())))
    .then(([categoriesData, brandsData]) => {
        if (!categoriesData.success || !brandsData.success) {
            throw new Error('Errore nel caricamento dei filtri');
        }
        
        // Carica la sidebar con i filtri
        caricaSidebar(categoriesData.categorie, brandsData.marche);
        
        // Carica tutti i prodotti inizialmente
        return fetch('../api/prodotti.php?action=all');
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.error || 'Errore nel caricamento dei prodotti');
        }
        
        // Carica i prodotti nella pagina
        caricaProdotti(data.prodotti);
        
        // Associa gli eventi ai filtri
        document.getElementById("search-bar").addEventListener("input", applicaFiltri);
        document.getElementById("price-filter").addEventListener("input", function () {
            document.getElementById("price-label").textContent = `Fino a ${this.value}€`;
            applicaFiltri();
        });

        document.querySelectorAll(".category-filter, .brand-filter").forEach(el => {
            el.addEventListener("change", applicaFiltri);
        });
    })
    .catch(error => {
        console.error("Errore nel caricamento del catalogo:", error);
        document.getElementById("catalogo-prodotti").innerHTML = `
            <div class="alert alert-danger" role="alert">
                Si è verificato un errore nel caricamento del catalogo. Riprova più tardi.
            </div>
        `;
    });
}

function caricaSidebar(categorie, marche) {
    // Costruisci la sidebar con i filtri
    let sidebarHtml = `<h5>Filtri</h5>`;

    // Barra di ricerca
    sidebarHtml += `<input type="text" id="search-bar" class="form-control mb-3" placeholder="Cerca prodotti...">`;

    // Categorie
    sidebarHtml += `<h6>Categorie</h6><ul id="category-list" class="list-unstyled">`;
    categorie.forEach(cat => {
        sidebarHtml += `<li><input type="checkbox" class="category-filter" value="${cat}"> ${cat}</li>`;
    });
    sidebarHtml += `</ul>`;

    // Filtro Prezzo
    sidebarHtml += `
        <h6>Prezzo</h6>
        <input type="range" id="price-filter" class="form-range" min="0" max="2000" step="50" value="2000">
        <span id="price-label">Fino a 2000€</span>
    `;

    // Marche
    sidebarHtml += `<h6>Marca</h6><ul id="brand-list" class="list-unstyled">`;
    marche.forEach(marca => {
        sidebarHtml += `<li><input type="checkbox" class="brand-filter" value="${marca}"> ${marca}</li>`;
    });
    sidebarHtml += `</ul>`;

    document.getElementById("sidebar").innerHTML = sidebarHtml;
}

function caricaProdotti(prodotti) {
    let prodottiHtml = "";
    
    if (prodotti.length === 0) {
        prodottiHtml = `
            <div class="col-12 text-center py-4">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h3>Nessun prodotto trovato</h3>
                <p>Prova a modificare i filtri di ricerca.</p>
            </div>
        `;
    } else {
        prodotti.forEach(prodotto => {
            prodottiHtml += `
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="${prodotto.immagine}" class="card-img-top" alt="${prodotto.nome}">
                        <div class="card-body">
                            <h5 class="card-title">${prodotto.nome}</h5>
                            <p class="card-text">${prodotto.categoria} - ${prodotto.marca}</p>
                            <p class="card-text fw-bold">${prodotto.prezzo}€</p>
                            <a href="dettaglio_catalogo.html?id=${prodotto.id}" class="btn btn-primary">Visualizza</a>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    document.getElementById("catalogo-prodotti").innerHTML = prodottiHtml;
}

function applicaFiltri() {
    // Ottieni i valori dei filtri
    const filtroTesto = document.getElementById("search-bar").value.toLowerCase();
    const filtroPrezzo = parseFloat(document.getElementById("price-filter").value);
    const categorieSelezionate = [...document.querySelectorAll(".category-filter:checked")].map(el => el.value);
    const marcheSelezionate = [...document.querySelectorAll(".brand-filter:checked")].map(el => el.value);
    
    // Costruisci l'URL per la richiesta API con i filtri
    let url = '../api/prodotti.php?action=filter';
    
    if (filtroTesto) {
        url += `&search=${encodeURIComponent(filtroTesto)}`;
    }
    
    if (!isNaN(filtroPrezzo)) {
        url += `&maxPrice=${filtroPrezzo}`;
    }
    
    if (categorieSelezionate.length > 0) {
        url += `&categories=${encodeURIComponent(categorieSelezionate.join(','))}`;
    }
    
    if (marcheSelezionate.length > 0) {
        url += `&brands=${encodeURIComponent(marcheSelezionate.join(','))}`;
    }
    
    // Mostra un loader mentre si caricano i dati filtrati
    document.getElementById("catalogo-prodotti").innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Caricamento...</span></div></div>';
    
    // Richiedi i prodotti filtrati
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.error || 'Errore nel caricamento dei prodotti filtrati');
            }
            
            // Carica i prodotti filtrati
            caricaProdotti(data.prodotti);
        })
        .catch(error => {
            console.error("Errore nell'applicazione dei filtri:", error);
            document.getElementById("catalogo-prodotti").innerHTML = `
                <div class="alert alert-danger" role="alert">
                    Si è verificato un errore nell'applicazione dei filtri. Riprova più tardi.
                </div>
            `;
        });
}

//=========================Dettaglio_catalogo.html=======================
document.addEventListener("DOMContentLoaded", function () {
    // Verifica se siamo nella pagina di dettaglio
    if (window.location.pathname.includes('dettaglio_catalogo.html')) {
        const params = new URLSearchParams(window.location.search);
        const prodottoId = params.get("id");

        if (!prodottoId) {
            document.getElementById("dettaglio-prodotto").innerHTML = "<p>Prodotto non trovato.</p>";
            return;
        }

        // Carica i dettagli del prodotto
        caricaDettaglioProdotto(prodottoId);
    }
});

function caricaDettaglioProdotto(prodottoId) {
    // Mostra un loader mentre si caricano i dati
    document.getElementById("dettaglio-prodotto").innerHTML = '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Caricamento...</span></div></div>';
    
    // Richiedi i dettagli del prodotto
    fetch(`../api/prodotti.php?action=detail&id=${prodottoId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.error || 'Errore nel caricamento del prodotto');
            }
            
            const prodotto = data.prodotto;
            
            // Carica le specifiche base
            let specificheHTML = "";
            for (const [chiave, valore] of Object.entries(prodotto.specifiche || {})) {
                specificheHTML += ` 
                    <li class="specifica-item">
                        <span class="specifica-titolo">${chiave}:</span>
                        <span class="specifica-valore">${valore}</span>
                    </li>
                `;
            }

            // Carica tutte le specifiche dettagliate
            let specificheDettagliateHTML = "";
            for (const [chiave, valore] of Object.entries(prodotto.specifiche_dettagliate || {})) {
                specificheDettagliateHTML += ` 
                    <li class="specifica-item">
                        <span class="specifica-titolo">${chiave}:</span> 
                        <span class="specifica-valore">${valore || "Non disponibile"}</span>
                    </li>
                `;
            }

            // Carica le miniature delle immagini
            let miniatureHTML = prodotto.immagini.map((img, index) => ` 
                <img src="${img}" class="miniatura ${index === 0 ? 'active' : ''}" data-index="${index}" alt="Miniatura ${index + 1}" style="width: 50px; cursor: pointer; margin-right: 5px;">
            `).join('');

            // Gestione delle varianti per PSU (750W, 850W, 1000W)
            let variantiDisponibili = "";
            let prezzoBase = prodotto.prezzo_base;
            if (prodotto.categoria === "PSU" && prodotto.varianti && prodotto.varianti.potenza) {
                variantiDisponibili = ` 
                    <div class="mt-3">
                        <h5>Seleziona la potenza:</h5>
                        ${Object.keys(prodotto.varianti.potenza).map(potenza => ` 
                            <button class="btn btn-outline-primary potenza-btn" data-potenza="${potenza}" data-prezzo="${prodotto.varianti.potenza[potenza].prezzo}">${potenza}</button>
                        `).join('')}
                    </div>
                `;
            }

            // Gestione delle varianti per SSD (capacità)
            let variantiSSD = "";
            if (prodotto.categoria === "Storage" && prodotto.varianti && prodotto.varianti.capacita) {
                variantiSSD = ` 
                    <div class="mt-3">
                        <h5>Seleziona la capacità:</h5>
                        ${Object.keys(prodotto.varianti.capacita).map(capacita => ` 
                            <button class="btn btn-outline-primary capacita-btn" data-capacita="${capacita}" data-prezzo="${prodotto.varianti.capacita[capacita].prezzo}">${capacita}</button>
                        `).join('')}
                    </div>
                `;
            }

            // Gestione delle varianti per RAM (colore e taglia)
            let variantiRAM = "";
            if (prodotto.categoria === "RAM") {
                if (prodotto.colori) {
                    variantiRAM += ` 
                        <div class="mt-3">
                            <h5>Seleziona il colore:</h5>
                            ${prodotto.colori.map(colore => ` 
                                <button class="btn btn-outline-primary colore-btn" data-colore="${colore}">${colore}</button>
                            `).join('')}
                        </div>
                    `;
                }
                
                if (prodotto.varianti && prodotto.varianti.taglia) {
                    variantiRAM += ` 
                        <div class="mt-3">
                            <h5>Seleziona la taglia:</h5>
                            ${Object.keys(prodotto.varianti.taglia).map(taglia => ` 
                                <button class="btn btn-outline-primary taglia-btn" data-taglia="${taglia}" data-prezzo="${prodotto.varianti.taglia[taglia].prezzo}">${taglia}</button>
                            `).join('')}
                        </div>
                    `;
                }
            }

            // Composizione finale dell'HTML con tutte le varianti
            document.getElementById("dettaglio-prodotto").innerHTML = ` 
                <div class="col-md-6">
                    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            ${prodotto.immagini.map((img, index) => ` 
                                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                    <img src="${img}" class="d-block w-100" alt="${prodotto.nome}" style="object-fit: contain;">
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
                    <h4 class="text-primary" id="prezzo">${prodotto.prezzo_base}€</h4>
                    <button class="btn btn-success w-100" id="aggiungi-carrello">Aggiungi al Carrello</button>
                    ${variantiDisponibili}
                    ${variantiRAM}
                    ${variantiSSD}
                </div>
                <div class="col-md-12 mt-4 specifiche-dettagliate">
                    <h3>Specifiche Dettagliate</h3>
                    <ul class="specifiche-lista">
                        ${specificheDettagliateHTML}
                    </ul>
                    <ul class="specifiche-lista">
                        ${specificheHTML}
                    </ul>
                </div>
            `;

            // Variabile per tracciare il prezzo corrente
            let prezzoCorrente = prodotto.prezzo_base;
            let variantiSelezionate = {};

            // Gestione del cambio di potenza (PSU)
            document.querySelectorAll(".potenza-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    document.querySelectorAll(".potenza-btn").forEach(button => button.classList.remove("active"));
                    this.classList.add("active");
                    const potenzaSelezionata = this.getAttribute("data-potenza");
                    prezzoCorrente = parseFloat(this.getAttribute("data-prezzo"));
                    variantiSelezionate.potenza = potenzaSelezionata;
                    document.getElementById("prezzo").textContent = prezzoCorrente.toFixed(2) + "€";
                });
            });

            // Gestione del cambio di colore (RAM)
            document.querySelectorAll(".colore-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    document.querySelectorAll(".colore-btn").forEach(button => button.classList.remove("active"));
                    this.classList.add("active");
                    const coloreSelezionato = this.getAttribute("data-colore");
                    variantiSelezionate.colore = coloreSelezionato;
                });
            });

            // Gestione del cambio di taglia (RAM)
            document.querySelectorAll(".taglia-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    document.querySelectorAll(".taglia-btn").forEach(button => button.classList.remove("active"));
                    this.classList.add("active");
                    const tagliaSelezionata = this.getAttribute("data-taglia");
                    prezzoCorrente = parseFloat(this.getAttribute("data-prezzo"));
                    variantiSelezionate.taglia = tagliaSelezionata;
                    document.getElementById("prezzo").textContent = prezzoCorrente.toFixed(2) + "€";
                });
            });

            // Gestione del cambio di capacità (SSD)
            document.querySelectorAll(".capacita-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    document.querySelectorAll(".capacita-btn").forEach(button => button.classList.remove("active"));
                    this.classList.add("active");
                    const capacitaSelezionata = this.getAttribute("data-capacita");
                    prezzoCorrente = parseFloat(this.getAttribute("data-prezzo"));
                    variantiSelezionate.capacita = capacitaSelezionata;
                    document.getElementById("prezzo").textContent = prezzoCorrente.toFixed(2) + "€";
                });
            });

            // Gestione dell'aggiunta al carrello
            document.getElementById("aggiungi-carrello").addEventListener("click", function () {
                // Prepara i dati del prodotto da aggiungere al carrello
                const prodottoCarrello = {
                    prodotto_id: prodotto.id,
                    prezzo: prezzoCorrente,
                    varianti: Object.keys(variantiSelezionate).length > 0 ? variantiSelezionate : null,
                    tipo: "catalogo"
                };
                
                // Aggiungi il prodotto al carrello tramite API
                fetch('../api/carrello.php?action=add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(prodottoCarrello)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostra un messaggio di conferma
                        const btnAggiungi = document.getElementById("aggiungi-carrello");
                        const testoOriginale = btnAggiungi.innerHTML;
                        
                        btnAggiungi.innerHTML = '<i class="fas fa-check"></i> Aggiunto al carrello!';
                        btnAggiungi.classList.remove("btn-success");
                        btnAggiungi.classList.add("btn-success", "disabled");
                        
                        setTimeout(() => {
                            btnAggiungi.innerHTML = testoOriginale;
                            btnAggiungi.classList.remove("disabled");
                        }, 2000);
                    } else {
                        alert("Errore nell'aggiunta del prodotto al carrello.");
                    }
                })
                .catch(error => {
                    console.error("Errore nell'aggiunta al carrello:", error);
                    alert("Si è verificato un errore. Riprova più tardi.");
                });
            });
            
            // Gestione delle miniature
            document.querySelectorAll(".miniatura").forEach(miniatura => {
                miniatura.addEventListener("click", function() {
                    const index = this.getAttribute("data-index");
                    document.querySelectorAll(".carousel-item").forEach((item, i) => {
                        item.classList.remove("active");
                        if (i == index) item.classList.add("active");
                    });
                    
                    document.querySelectorAll(".miniatura").forEach(m => m.classList.remove("active"));
                    this.classList.add("active");
                });
            });
        })
        .catch(error => {
            console.error("Errore nel caricamento del prodotto:", error);
            document.getElementById("dettaglio-prodotto").innerHTML = `
                <div class="alert alert-danger" role="alert">
                    Si è verificato un errore nel caricamento del prodotto. Riprova più tardi.
                </div>
            `;
        });
}