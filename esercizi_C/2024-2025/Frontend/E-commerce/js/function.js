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
