document.addEventListener("DOMContentLoaded", function() {
    // Controlla se l'utente è loggato
    fetch('../api/check_auth.php')
        .then(response => response.json())
        .then(data => {
            if (!data.logged_in) {
                // Se l'utente non è loggato, reindirizza alla pagina di login
                window.location.href = "account.html";
                return;
            }
            
            // Aggiorna il nome utente nella navbar
            document.getElementById("user-name").textContent = data.user.name;
            
            // Aggiorna il messaggio di benvenuto
            document.getElementById("welcome-message").textContent = `Benvenuto, ${data.user.name}! Questa è la tua dashboard personale.`;
            
            // Carica i dati dell'utente
            loadUserData(data.user.id);
        })
        .catch(error => {
            console.error("Errore nel controllo dell'autenticazione:", error);
            window.location.href = "account.html";
        });
    
    // Funzione per caricare i dati dell'utente
    function loadUserData(userId) {
        // Carica gli ordini recenti
        fetch(`../api/get_user_orders.php?limit=5`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna il contatore degli ordini
                    document.getElementById("total-orders").textContent = data.total_count;
                    
                    // Aggiorna la data dell'ultimo ordine
                    if (data.orders && data.orders.length > 0) {
                        const lastOrder = data.orders[0];
                        const orderDate = new Date(lastOrder.data_ordine);
                        document.getElementById("last-order-date").textContent = orderDate.toLocaleDateString('it-IT');
                    }
                    
                    // Aggiorna la tabella degli ordini recenti
                    const recentOrdersTable = document.getElementById("recent-orders");
                    
                    if (data.orders && data.orders.length > 0) {
                        let ordersHtml = '';
                        
                        data.orders.forEach(order => {
                            const orderDate = new Date(order.data_ordine);
                            
                            ordersHtml += `
                                <tr>
                                    <td>ORD-${order.id}</td>
                                    <td>${orderDate.toLocaleDateString('it-IT')}</td>
                                    <td>${parseFloat(order.totale).toFixed(2)}€</td>
                                    <td><span class="badge bg-${getStatusBadgeClass(order.stato)}">${order.stato}</span></td>
                                    <td>
                                        <a href="dettaglio_ordine.html?id=${order.id}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        recentOrdersTable.innerHTML = ordersHtml;
                    } else {
                        recentOrdersTable.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center">Nessun ordine effettuato</td>
                            </tr>
                        `;
                    }
                } else {
                    document.getElementById("recent-orders").innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center">Errore nel caricamento degli ordini</td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error("Errore nel caricamento degli ordini:", error);
                document.getElementById("recent-orders").innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">Errore nel caricamento degli ordini</td>
                    </tr>
                `;
            });
        
        // Carica lo stato del questionario
        fetch(`../api/get_questionario_status.php`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna il profilo utente
                    if (data.completed && data.profile) {
                        document.getElementById("user-profile").textContent = data.profile;
                    }
                    
                    // Aggiorna la barra di progresso del questionario
                    const progressBar = document.getElementById("questionario-progress");
                    const progressPercentage = data.progress_percentage;
                    
                    progressBar.style.width = `${progressPercentage}%`;
                    progressBar.textContent = `${progressPercentage}%`;
                    progressBar.setAttribute("aria-valuenow", progressPercentage);
                    
                    // Se il questionario è completo, aggiorna il contenuto
                    if (data.completed) {
                        document.getElementById("questionario-status").innerHTML = `
                            <p class="mb-3">Hai completato il questionario! Il tuo profilo è: <strong>${data.profile}</strong></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="progress flex-grow-1 me-3" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
                                </div>
                                <a href="questionario.html" class="btn btn-outline-primary">Modifica</a>
                            </div>
                        `;
                    }
                }
            })
            .catch(error => {
                console.error("Errore nel caricamento dello stato del questionario:", error);
            });
    }
    
    // Funzione per ottenere la classe del badge in base allo stato dell'ordine
    function getStatusBadgeClass(status) {
        switch (status.toLowerCase()) {
            case 'completato':
                return 'success';
            case 'in lavorazione':
                return 'primary';
            case 'spedito':
                return 'info';
            case 'in attesa':
                return 'warning';
            case 'annullato':
                return 'danger';
            default:
                return 'secondary';
        }
    }
    
    // Gestione del logout
    document.getElementById("logout-btn").addEventListener("click", function(e) {
        e.preventDefault();
        logout();
    });
    
    document.getElementById("sidebar-logout-btn").addEventListener("click", function(e) {
        e.preventDefault();
        logout();
    });
    
    function logout() {
        fetch('../api/logout.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = "../index.html";
                }
            })
            .catch(error => {
                console.error("Errore durante il logout:", error);
                alert("Si è verificato un errore durante il logout. Riprova più tardi.");
            });
    }
});