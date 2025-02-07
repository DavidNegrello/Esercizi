
//========================LOGIN=====================


//===================PRODOTTI=======================
document.addEventListener("DOMContentLoaded", function () {
    fetch("../data/prodotti.json")
        .then(response => response.json())
        .then(data => {
            let prodottiContainer = document.getElementById("prodotti-popolari");
            
            if (prodottiContainer) {
                let prodottiPopolari = data.prodotti.sort((a, b) => b.vendite - a.vendite).slice(0, 6); // Prende i 6 più venduti
                
                prodottiContainer.innerHTML = prodottiPopolari.map(prodotto => `
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img src="${prodotto.immagine}" class="card-img-top" alt="${prodotto.nome}">
                            <div class="card-body">
                                <h5 class="card-title">${prodotto.nome}</h5>
                                <p class="card-text">${prodotto.descrizione}</p>
                                <p class="fw-bold">€${prodotto.prezzo.toFixed(2)}</p>
                                <a href="dettagli.html?id=${prodotto.id}" class="btn btn-primary">Dettagli</a>
                            </div>
                        </div>
                    </div>
                `).join("");
            }
        })
        .catch(error => console.error("Errore nel caricamento dei dati:", error));
});

//====================FOOTER======================
document.addEventListener("DOMContentLoaded", function () {
    fetch("../data/footer.json")
        .then(response => response.json())
        .then(data => {
            // Social media
            let socialHtml = "";
            data.social.forEach(social => {
                socialHtml += `<a href="${social.link}" class="text-light me-3"><i class="${social.icon} fa-lg"></i></a>`;
            });
            document.getElementById("footer-social").innerHTML = socialHtml;

            // Email
            document.getElementById("footer-email").textContent = data.email;
            document.getElementById("footer-email").href = `mailto:${data.email}`;

            // Copyright
            document.getElementById("footer-copyright").innerHTML = data.copyright;
        })
        .catch(error => console.error("Errore nel caricamento del footer:", error));
});

//================INDEX==================
document.addEventListener("DOMContentLoaded", function() {
    fetch("../data/home.json")
        .then(response => response.json())
        .then(data => {
            // Carica l'hero section
            document.getElementById("hero-title").textContent = data.hero.titolo;
            document.getElementById("hero-desc").textContent = data.hero.descrizione;
            let heroBtn = document.getElementById("hero-btn");
            heroBtn.textContent = data.hero.bottone.testo;
            heroBtn.href = data.hero.bottone.link;

            // Carica i prodotti più acquistati
            let prodottiContainer = document.getElementById("prodotti-popolari");
            data.prodotti_piu_acquistati.forEach(prodotto => {
                let col = document.createElement("div");
                col.className = "col-md-4";

                col.innerHTML = `
                    <div class="card">
                        <img src="${prodotto.immagine}" class="card-img-top" alt="${prodotto.nome}">
                        <div class="card-body">
                            <h5 class="card-title">${prodotto.nome}</h5>
                            <p class="card-text"><strong>Categoria:</strong> ${prodotto.categoria}</p>
                            <p class="card-text"><strong>Prezzo:</strong> €${prodotto.prezzo.toFixed(2)}</p>
                            <a href="prodotti.html?categoria=${prodotto.categoria}" class="btn btn-primary">Vedi</a>
                        </div>
                    </div>
                `;

                prodottiContainer.appendChild(col);
            });
        })
        .catch(error => console.error("Errore nel caricamento del JSON:", error));
});


//====================CATALOGO=====================
document.addEventListener("DOMContentLoaded", function () {
    let prodottiOriginali = []; // Ora è globale

    fetch("../data/catalogo.json")
        .then(response => response.json())
        .then(data => {
            prodottiOriginali = data.prodotti; // ✅ Corretto: prodottiOriginali ora è globale
            console.log("Dati caricati:", prodottiOriginali);

            // Carica Sidebar
            let sidebarHtml = `<h5>${data.sidebar.titolo}</h5>`;

            // Barra di ricerca
            sidebarHtml += `<input type="text" id="search-bar" class="form-control mb-3" placeholder="${data.sidebar.filtri.ricerca}">`;

            // Categorie
            sidebarHtml += `<h6>Categorie</h6><ul id="category-list" class="list-unstyled">`;
            data.sidebar.filtri.categorie.forEach(cat => {
                sidebarHtml += `<li><input type="checkbox" class="category-filter" value="${cat}"> ${cat}</li>`;
            });
            sidebarHtml += `</ul>`;

            // Filtro Prezzo
            sidebarHtml += `
                <h6>Prezzo</h6>
                <input type="range" id="price-filter" class="form-range" min="${data.sidebar.filtri.prezzo.min}" max="${data.sidebar.filtri.prezzo.max}" step="${data.sidebar.filtri.prezzo.step}">
                <span id="price-label">${data.sidebar.filtri.prezzo.min} - ${data.sidebar.filtri.prezzo.max}€</span>
            `;

            // Marche
            sidebarHtml += `<h6>Marca</h6><ul id="brand-list" class="list-unstyled">`;
            data.sidebar.filtri.marche.forEach(marca => {
                sidebarHtml += `<li><input type="checkbox" class="brand-filter" value="${marca}"> ${marca}</li>`;
            });
            sidebarHtml += `</ul>`;

            document.getElementById("sidebar").innerHTML = sidebarHtml;

            // Carica prodotti iniziali
            caricaProdotti(prodottiOriginali);
            
            // Associa gli eventi SOLO dopo aver caricato i prodotti
            document.getElementById("search-bar").addEventListener("input", applicaFiltri);
            document.getElementById("price-filter").addEventListener("input", function () {
                document.getElementById("price-label").textContent = `Fino a ${this.value}€`;
                applicaFiltri();
            });

            document.querySelectorAll(".category-filter, .brand-filter").forEach(el => {
                el.addEventListener("change", applicaFiltri);
            });
        })
        .catch(error => console.error("Errore nel caricamento del catalogo:", error));

    function caricaProdotti(prodotti) {
        let prodottiHtml = "";
        prodotti.forEach(prodotto => {
            prodottiHtml += `
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="${prodotto.immagine}" class="card-img-top" alt="${prodotto.nome}">
                        <div class="card-body">
                            <h5 class="card-title">${prodotto.nome}</h5>
                            <p class="card-text">${prodotto.categoria} - ${prodotto.marca}</p>
                            <p class="card-text fw-bold">${prodotto.prezzo}€</p>
                            <a href="#" class="btn btn-primary">Aggiungi al carrello</a>
                        </div>
                    </div>
                </div>
            `;
        });
        document.getElementById("catalogo-prodotti").innerHTML = prodottiHtml;
    }

    function applicaFiltri() {
        if (prodottiOriginali.length === 0) {
            console.log("⚠️ prodottiOriginali è vuoto, impossibile applicare filtri!");
            return;
        }

        let filtroTesto = document.getElementById("search-bar").value.toLowerCase();
        let filtroPrezzo = parseFloat(document.getElementById("price-filter").value);
        let categorieSelezionate = [...document.querySelectorAll(".category-filter:checked")].map(el => el.value);
        let marcheSelezionate = [...document.querySelectorAll(".brand-filter:checked")].map(el => el.value);

        console.log("Categorie selezionate:", categorieSelezionate);
        console.log("Marche selezionate:", marcheSelezionate);
        console.log("Prezzo massimo selezionato:", filtroPrezzo);

        let prodottiFiltrati = prodottiOriginali.filter(prodotto => {
            let matchTesto = prodotto.nome.toLowerCase().includes(filtroTesto);
            let matchPrezzo = isNaN(filtroPrezzo) || prodotto.prezzo <= filtroPrezzo;
            let matchCategoria = categorieSelezionate.length === 0 || categorieSelezionate.includes(prodotto.categoria);
            let matchMarca = marcheSelezionate.length === 0 || marcheSelezionate.includes(prodotto.marca);

            return matchTesto && matchPrezzo && matchCategoria && matchMarca;
        });

        console.log("Prodotti filtrati dopo il filtro:", prodottiFiltrati);

        caricaProdotti(prodottiFiltrati);
    }
});








