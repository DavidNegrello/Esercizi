document.addEventListener("DOMContentLoaded", function () {
    // Carica il carrello dal server
    fetch("../api/carrello.php")
        .then(response => response.json())
        .then(data => {
            const items = data.items;
            const total = data.total;
            
            const carrelloProdottiElement = document.getElementById("carrello-prodotti");
            const subtotaleElement = document.getElementById("subtotale");
            const totaleElement = document.getElementById("totale");
            const procediCheckoutBtn = document.getElementById("procedi-checkout");
            
            if (!items || items.length === 0) {
                carrelloProdottiElement.innerHTML = `
                    <div class="empty-cart text-center py-4">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h3>Il carrello è vuoto</h3>
                        <p class="text-muted">Non ci sono prodotti nel tuo carrello</p>
                        <a href="catalogo.html" class="btn btn-primary mt-3">Torna al catalogo</a>
                    </div>
                `;
                subtotaleElement.innerText = "€0.00";
                totaleElement.innerText = "€0.00";
                procediCheckoutBtn.classList.add("disabled");
                return;
            }
            
            // Aggiungi ogni prodotto al carrello
            let prodottiHtml = "";
            items.forEach(item => {
                const varianti = JSON.parse(item.varianti || '{}');
                let variantiHtml = '';
                
                if (Object.keys(varianti).length > 0) {
                    variantiHtml = `
                        <div class="product-variants mt-1">
                            <p class="mb-0 small text-muted">
                                ${Object.entries(varianti).map(([key, value]) => 
                                    `${key.charAt(0).toUpperCase() + key.slice(1)}: ${value}`
                                ).join(' | ')}
                            </p>
                        </div>
                    `;
                }
                
                // Contenuto specifico per i bundle
                let bundleContent = '';
                if (item.tipo === 'bundle' && item.prodotti_bundle) {
                    bundleContent = `
                        <div class="bundle-products mt-2">
                            <p class="mb-1 small fw-bold">Prodotti inclusi:</p>
                            <ul class="list-unstyled small ps-2">
                                ${item.prodotti_bundle.map(prod => 
                                    `<li>- ${prod.nome} (${prod.categoria})</li>`
                                ).join('')}
                            </ul>
                        </div>
                    `;
                }
                
                prodottiHtml += `
                    <div class="cart-item mb-3 pb-3 border-bottom" data-id="${item.id}">
                        <div class="row align-items-center">
                            <div class="col-md-2 col-4 mb-2 mb-md-0">
                                <img src="${item.immagine_principale}" alt="${item.nome}" class="img-fluid rounded">
                                ${item.tipo !== 'catalogo' ? `<span class="badge bg-primary position-absolute top-0 start-0 m-1 small">${item.tipo}</span>` : ''}
                            </div>
                            <div class="col-md-4 col-8 mb-2 mb-md-0">
                                <h5 class="mb-1">${item.nome}</h5>
                                ${variantiHtml}
                                ${bundleContent}
                            </div>
                            <div class="col-md-2 col-4 text-md-center">
                                <div class="quantity-control">
                                    <button class="btn btn-sm btn-outline-secondary decrease-quantity" data-id="${item.id}">-</button>
                                    <span class="mx-2 quantity">${item.quantita}</span>
                                    <button class="btn btn-sm btn-outline-secondary increase-quantity" data-id="${item.id}">+</button>
                                </div>
                            </div>
                            <div class="col-md-2 col-4 text-md-end">
                                <p class="mb-0 fw-bold">${(item.prezzo_unitario * item.quantita).toFixed(2)}€</p>
                                <p class="mb-0 small text-muted">${item.prezzo_unitario.toFixed(2)}€ cad.</p>
                            </div>
                            <div class="col-md-2 col-4 text-md-end">
                                <button class="btn btn-sm btn-danger remove-item" data-id="${item.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            carrelloProdottiElement.innerHTML = prodottiHtml;
            
            // Aggiungi il pulsante per svuotare il carrello
            carrelloProdottiElement.innerHTML += `
                <div class="text-end mt-3">
                    <button id="svuota-carrello" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-2"></i> Svuota carrello
                    </button>
                </div>
            `;
            
            // Mostra il totale
            subtotaleElement.innerText = `€${total.toFixed(2)}`;
            totaleElement.innerText = `€${total.toFixed(2)}`;
            
            // Aggiungi gli event listener per i pulsanti
            document.querySelectorAll(".increase-quantity").forEach(button => {
                button.addEventListener("click", function() {
                    const itemId = this.getAttribute("data-id");
                    const quantityElement = this.parentElement.querySelector(".quantity");
                    let quantity = parseInt(quantityElement.textContent) + 1;
                    updateQuantity(itemId, quantity);
                });
            });
            
            document.querySelectorAll(".decrease-quantity").forEach(button => {
                button.addEventListener("click", function() {
                    const itemId = this.getAttribute("data-id");
                    const quantityElement = this.parentElement.querySelector(".quantity");
                    let quantity = parseInt(quantityElement.textContent) - 1;
                    if (quantity >= 1) {
                        updateQuantity(itemId, quantity);
                    }
                });
            });
            
            document.querySelectorAll(".remove-item").forEach(button => {
                button.addEventListener("click", function() {
                    const itemId = this.getAttribute("data-id");
                    removeItem(itemId);
                });
            });
            
            document.getElementById("svuota-carrello").addEventListener("click", function() {
                if (confirm("Sei sicuro di voler svuotare il carrello?")) {
                    clearCart();
                }
            });
        })
        .catch(error => {
            console.error("Errore nel caricamento del carrello:", error);
            document.getElementById("carrello-prodotti").innerHTML = `
                <div class="alert alert-danger">
                    Si è verificato un errore nel caricamento del carrello. Riprova più tardi.
                </div>
            `;
        });
    
    // Funzione per aggiornare la quantità di un prodotto
    function updateQuantity(itemId, quantity) {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('item_id', itemId);
        formData.append('quantity', quantity);
        
        fetch('../api/carrello.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Aggiorna la UI
                location.reload();
            } else {
                alert("Errore nell'aggiornamento della quantità.");
            }
        })
        .catch(error => {
            console.error("Errore:", error);
            alert("Si è verificato un errore durante l'aggiornamento della quantità.");
        });
    }
    
    // Funzione per rimuovere un prodotto dal carrello
    function removeItem(itemId) {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('item_id', itemId);
        
        fetch('../api/carrello.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Aggiorna la UI
                location.reload();
            } else {
                alert("Errore nella rimozione del prodotto.");
            }
        })
        .catch(error => {
            console.error("Errore:", error);
            alert("Si è verificato un errore durante la rimozione del prodotto.");
        });
    }
    
    // Funzione per svuotare il carrello
    function clearCart() {
        const formData = new FormData();
        formData.append('action', 'clear');
        
        fetch('../api/carrello.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Aggiorna la UI
                location.reload();
            } else {
                alert("Errore nello svuotamento del carrello.");
            }
        })
        .catch(error => {
            console.error("Errore:", error);
            alert("Si è verificato un errore durante lo svuotamento del carrello.");
        });
    }
});