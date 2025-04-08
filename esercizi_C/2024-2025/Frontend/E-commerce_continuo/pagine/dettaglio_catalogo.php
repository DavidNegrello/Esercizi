<?php
// Gestione sessione utente
session_start();
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : '';

// Conteggio articoli nel carrello
$cartCount = 0;
if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $items) {
        $cartCount += $items['quantity'];
    }
}

// Determina pagina attiva
$currentPage = basename($_SERVER['PHP_SELF']);

/**
 * Pagina di dettaglio prodotto
 *
 * Questa pagina mostra i dettagli completi di un prodotto dal catalogo,
 * incluse descrizioni, specifiche, varianti (colori, taglie, capacità, wattaggio)
 * e immagini multiple.
 */

// Inclusione della configurazione del database
require_once '../conf/db_config.php';

// Inizializzazione variabili
$prodotto = null;
$descrizione = null;
$specifiche = [];
$immagini = [];
$colori = [];
$varianti_colore = [];
$capacita = [];
$taglie = [];
$varianti_wattaggio = [];
$prodotti_correlati = [];
$error = '';
$id = 0;

// Controllo se è stato passato un ID valido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Query per ottenere i dettagli del prodotto con nome categoria e marca
    $sql = "SELECT p.*, c.nome AS categoria_nome, m.nome AS marca_nome 
            FROM prodotti p
            JOIN categorie c ON p.categoria_id = c.id
            JOIN marche m ON p.marca_id = m.id
            WHERE p.id = ?";

    // Utilizzo della funzione fetchOne con prepared statement per sicurezza
    $prodotto = fetchOne($sql, 'i', [$id]);

    if ($prodotto) {
        // Ottieni descrizione
        $sql_descrizione = "SELECT descrizione FROM prodotti_descrizioni WHERE prodotto_id = ?";
        $descrizione_result = fetchOne($sql_descrizione, 'i', [$id]);
        $descrizione = $descrizione_result ? $descrizione_result['descrizione'] : null;

        // Ottieni specifiche
        $sql_specifiche = "SELECT s.nome, ps.valore 
                           FROM prodotti_specifiche ps
                           JOIN specifiche s ON ps.specifica_id = s.id
                           WHERE ps.prodotto_id = ?";
        $specifiche = fetchAll($sql_specifiche, 'i', [$id]);

        // Ottieni immagini
        $sql_immagini = "SELECT * FROM prodotti_immagini WHERE prodotto_id = ? ORDER BY is_principale DESC";
        $immagini = fetchAll($sql_immagini, 'i', [$id]);

        // Ottieni colori disponibili
        $sql_colori = "SELECT c.* 
                       FROM prodotti_colori pc
                       JOIN colori c ON pc.colore_id = c.id
                       WHERE pc.prodotto_id = ?";
        $colori = fetchAll($sql_colori, 'i', [$id]);

        // Ottieni varianti di colore con immagini
        $sql_varianti_colore = "SELECT vc.id, c.nome AS colore_nome, c.id AS colore_id
                               FROM varianti_colore vc
                               JOIN colori c ON vc.colore_id = c.id
                               WHERE vc.prodotto_id = ?";
        $varianti_colore_temp = fetchAll($sql_varianti_colore, 'i', [$id]);

        // Per ogni variante di colore, ottieni le relative immagini
        foreach ($varianti_colore_temp as $variante) {
            $sql_variante_immagini = "SELECT url FROM varianti_colore_immagini WHERE variante_id = ?";
            $immagini_variante = fetchAll($sql_variante_immagini, 'i', [$variante['id']]);

            $varianti_colore[] = [
                'colore_id' => $variante['colore_id'],
                'colore_nome' => $variante['colore_nome'],
                'immagini' => $immagini_variante
            ];
        }

        // Ottieni taglie disponibili (per RAM, ecc.)
        $sql_taglie = "SELECT pt.prezzo, t.nome AS taglia_nome, t.descrizione, t.id as taglia_id
               FROM prodotti_taglie pt
               JOIN taglie t ON pt.taglia_id = t.id
               WHERE pt.prodotto_id = ?";
        $taglie = fetchAll($sql_taglie, 'i', [$id]);

// Ottieni capacità disponibili (per SSD, HDD, ecc.)
        $sql_capacita = "SELECT pc.prezzo, c.nome AS capacita_nome, c.id as capacita_id
                 FROM prodotti_capacita pc
                 JOIN capacita c ON pc.capacita_id = c.id
                 WHERE pc.prodotto_id = ?";
        $capacita = fetchAll($sql_capacita, 'i', [$id]);

        // Ottieni varianti di wattaggio (per PSU)
        $sql_wattaggio = "SELECT vw.id, vw.wattaggio
                          FROM varianti_wattaggio vw
                          WHERE vw.prodotto_id = ?";
        $varianti_wattaggio_temp = fetchAll($sql_wattaggio, 'i', [$id]);

        // Per ogni variante di wattaggio, ottieni le relative immagini
        foreach ($varianti_wattaggio_temp as $variante) {
            $sql_variante_immagini = "SELECT url FROM varianti_wattaggio_immagini WHERE variante_id = ?";
            $immagini_variante = fetchAll($sql_variante_immagini, 'i', [$variante['id']]);

            $varianti_wattaggio[] = [
                'wattaggio' => $variante['wattaggio'],
                'immagini' => $immagini_variante
            ];
        }

        // Ottieni specifiche dettagliate
        $sql_spec_dettagliate = "SELECT sd.nome, psd.valore 
                                 FROM prodotti_specifiche_dettagliate psd
                                 JOIN specifiche_dettagliate sd ON psd.specifica_dettagliata_id = sd.id
                                 WHERE psd.prodotto_id = ?";
        $specifiche_dettagliate = fetchAll($sql_spec_dettagliate, 'i', [$id]);

        // Aggiungi le specifiche dettagliate all'array delle specifiche se ci sono
        if (!empty($specifiche_dettagliate)) {
            $specifiche = array_merge($specifiche, $specifiche_dettagliate);
        }

        // Ottenere prodotti correlati (stessa categoria)
        $sql_correlati = "SELECT p.*, m.nome AS marca_nome 
                        FROM prodotti p
                        JOIN marche m ON p.marca_id = m.id
                        WHERE p.categoria_id = ? AND p.id != ?
                        ORDER BY RAND()
                        LIMIT 4";

        $prodotti_correlati = fetchAll($sql_correlati, 'ii', [$prodotto['categoria_id'], $id]);
    } else {
        $error = 'Prodotto non trovato';
    }
} else {
    $error = 'ID prodotto non valido';
}

// Funzione per verificare se un'immagine esiste
function imageExists($image_path) {
    if (empty($image_path)) return false;

    // Se il percorso inizia con http:// o https://, non controllare l'esistenza fisica
    if (strpos($image_path, 'http://') === 0 || strpos($image_path, 'https://') === 0) {
        return true;
    }

    // Rimuovi '../' dall'inizio del percorso se presente
    if (strpos($image_path, '../') === 0) {
        $image_path = substr($image_path, 3);
    }

    return file_exists($image_path);
}

// Funzione per generare breadcrumbs
function generateBreadcrumbs($categoria, $nome_prodotto) {
    return '
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="catalogo.php">Catalogo</a></li>
            <li class="breadcrumb-item"><a href="catalogo.php?categoria=' . urlencode($categoria) . '">' . htmlspecialchars($categoria) . '</a></li>
            <li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($nome_prodotto) . '</li>
        </ol>
    </nav>';
}



?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $prodotto ? htmlspecialchars($prodotto['nome']) : 'Dettaglio Prodotto'; ?> - Catalogo PC Parts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../stili/dettagli_catalogo.css">
    <link rel="stylesheet" href="../stili/navbar_footer.css">

    <style>
        .toast-notification {
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            animation: fadeIn 0.3s, fadeOut 0.3s 2.7s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .toast-notification.success {
            background-color: #28a745;
        }

        .toast-notification.error {
            background-color: #dc3545;
        }

        .toast-notification i {
            margin-right: 8px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-20px); }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <!-- Logo e Brand -->
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
            <img src="../immagini/favicon_io/favicon.ico" alt="Logo" width="30" height="30" class="me-2">
            <span class="fw-bold">PC Componenti</span>
        </a>

        <!-- Hamburger per mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu navigazione -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == '../index.php' ? 'active' : ''; ?>"
                       href="../index.php" <?php echo $currentPage == '../index.php' ? 'aria-current="page"' : ''; ?>>Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="catalogo.php" aria-expanded="false">
                        Componenti
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'preassemblati.php' ? 'active' : ''; ?>"
                       href="../pagine/preassemblati.php">PC Preassemblati</a>
                </li>
            </ul>

            <!-- Carrello e Login/Utente a destra -->
            <div class="d-flex align-items-center">
                <!-- Carrello con counter dinamico -->
                <div class="position-relative me-3">
                    <a class="btn btn-outline-light btn-sm position-relative" href="../pagine/carrello.php">
                        <i class="fas fa-shopping-cart me-1"></i> Carrello
                        <?php if($cartCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $cartCount; ?>
                            <span class="visually-hidden">Articoli nel carrello</span>
                        </span>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Login o Menu Utente in base allo stato -->
                <?php if($loggedIn): ?>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($username); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="../pagine/profilo.php"><i class="fas fa-id-card me-2"></i>Il mio profilo</a></li>
                            <li><a class="dropdown-item" href="../pagine/ordini.php"><i class="fas fa-box me-2"></i>I miei ordini</a></li>
                            <li><a class="dropdown-item" href="../pagine/wishlist.php"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../pagine/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="../pagine/login.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-sign-in-alt me-1"></i> Accedi
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>


<div class="container mt-4 mb-5">
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
            <p class="mt-2">
                <a href="catalogo.php" class="btn btn-primary">Torna al catalogo</a>
            </p>
        </div>
    <?php elseif ($prodotto): ?>
        <?php echo generateBreadcrumbs($prodotto['categoria_nome'], $prodotto['nome']); ?>

        <div class="card mb-4">
            <div class="row g-0">
                <div class="col-md-5">
                    <div class="position-relative">
                        <?php if (!empty($immagini)): ?>
                            <img id="main-product-image" src="<?php echo htmlspecialchars($immagini[0]['url']); ?>" class="img-fluid product-image p-3" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>">
                        <?php elseif ($prodotto['immagine'] && imageExists($prodotto['immagine'])): ?>
                            <img id="main-product-image" src="<?php echo htmlspecialchars($prodotto['immagine']); ?>" class="img-fluid product-image p-3" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>">
                        <?php else: ?>
                            <div class="text-center p-5">
                                <i class="bi bi-image text-secondary" style="font-size: 8rem;"></i>
                                <p class="text-muted">Immagine non disponibile</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($immagini) && count($immagini) > 1): ?>
                        <div class="d-flex flex-wrap justify-content-start p-3 gap-2">
                            <?php foreach ($immagini as $index => $img): ?>
                                <img src="<?php echo htmlspecialchars($img['url']); ?>"
                                     class="thumbnail-image <?php echo $index === 0 ? 'active' : ''; ?>"
                                     alt="Thumbnail <?php echo $index+1; ?>"
                                     onclick="updateMainImage('<?php echo htmlspecialchars($img['url']); ?>', this)">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-7">
                    <div class="card-body">
                        <h1 class="card-title"><?php echo htmlspecialchars($prodotto['nome']); ?></h1>
                        <p class="card-text">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($prodotto['categoria_nome']); ?></span>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($prodotto['marca_nome']); ?></span>
                        </p>

                        <div class="mb-4">
                            <h2 class="text-danger" id="product-price">€<?php echo number_format($prodotto['prezzo'], 2, ',', '.'); ?></h2>
                        </div>

                        <?php if (!empty($descrizione)): ?>
                            <div class="mb-4">
                                <p><?php echo nl2br(htmlspecialchars($descrizione)); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($colori)): ?>
                            <div class="mb-4">
                                <h5>Colore:</h5>
                                <div class="d-flex align-items-center mb-3" id="color-options">
                                    <?php foreach($colori as $index => $colore): ?>
                                        <div class="color-option <?php echo $index === 0 ? 'active' : ''; ?>"
                                             style="background-color: <?php echo htmlspecialchars($colore['nome']); ?>"
                                             title="<?php echo htmlspecialchars($colore['nome']); ?>"
                                             data-color-id="<?php echo $colore['id']; ?>"
                                             onclick="selectColor(this)"></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($taglie)): ?>
                            <div class="mb-4">
                                <h5>Dimensione:</h5>
                                <div class="d-flex flex-wrap align-items-center mb-3" id="size-options">
                                    <?php foreach($taglie as $index => $taglia): ?>
                                        <div class="variant-option <?php echo $index === 0 ? 'active' : ''; ?>"
                                             data-price="<?php echo $taglia['prezzo']; ?>"
                                             data-size="<?php echo htmlspecialchars($taglia['taglia_nome']); ?>"
                                             data-size-id="<?php echo $taglia['taglia_id']; ?>"
                                             onclick="selectVariant(this, 'size')">
                                            <?php echo htmlspecialchars($taglia['taglia_nome']); ?>
                                            <?php if ($taglia['prezzo'] != $prodotto['prezzo']): ?>
                                                (€<?php echo number_format($taglia['prezzo'], 2, ',', '.'); ?>)
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($capacita)): ?>
                            <div class="mb-4">
                                <h5>Capacità:</h5>
                                <div class="d-flex flex-wrap align-items-center mb-3" id="capacity-options">
                                    <?php foreach($capacita as $index => $cap): ?>
                                        <div class="variant-option <?php echo $index === 0 ? 'active' : ''; ?>"
                                             data-price="<?php echo $cap['prezzo']; ?>"
                                             data-capacity="<?php echo htmlspecialchars($cap['capacita_nome']); ?>"
                                             data-capacity-id="<?php echo $cap['capacita_id']; ?>"
                                             onclick="selectVariant(this, 'capacity')">
                                            <?php echo htmlspecialchars($cap['capacita_nome']); ?>
                                            <?php if ($cap['prezzo'] != $prodotto['prezzo']): ?>
                                                (€<?php echo number_format($cap['prezzo'], 2, ',', '.'); ?>)
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($varianti_wattaggio)): ?>
                            <div class="mb-4">
                                <h5>Wattaggio:</h5>
                                <div class="d-flex flex-wrap align-items-center mb-3" id="wattage-options">
                                    <?php foreach($varianti_wattaggio as $index => $watt): ?>
                                        <div class="variant-option <?php echo $index === 0 ? 'active' : ''; ?>"
                                             data-wattage="<?php echo htmlspecialchars($watt['wattaggio']); ?>"
                                             onclick="selectVariant(this, 'wattage')">
                                            <?php echo htmlspecialchars($watt['wattaggio']); ?>W
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex mb-4">
                            <div class="input-group me-3" style="width: 130px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(-1)">-</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1">
                                <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(1)">+</button>
                            </div>

                            <button class="btn btn-success btn-lg" onclick="addToCart()">
                                <i class="bi bi-cart-plus"></i> Aggiungi al carrello
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Specifiche tecniche -->
        <?php if (!empty($specifiche)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="mb-0">Specifiche Tecniche</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped specs-table">
                        <tbody>
                        <?php foreach ($specifiche as $spec): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($spec['nome']); ?></td>
                                <td><?php echo htmlspecialchars($spec['valore']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Prodotti correlati -->
        <?php if (!empty($prodotti_correlati)): ?>
            <section class="mt-5">
                <h3 class="mb-4">Prodotti correlati</h3>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    <?php foreach ($prodotti_correlati as $correlato): ?>
                        <div class="col">
                            <div class="card related-product-card h-100">
                                <?php if ($correlato['immagine'] && imageExists($correlato['immagine'])): ?>
                                    <img src="<?php echo htmlspecialchars($correlato['immagine']); ?>" class="card-img-top related-product-img p-2" alt="<?php echo htmlspecialchars($correlato['nome']); ?>">
                                <?php else: ?>
                                    <div class="text-center p-3">
                                        <i class="bi bi-image text-secondary" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($correlato['nome']); ?></h5>
                                    <p class="card-text">
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($correlato['marca_nome']); ?></span>
                                    </p>
                                    <p class="card-text fw-bold">€<?php echo number_format($correlato['prezzo'], 2, ',', '.'); ?></p>
                                </div>
                                <div class="card-footer">
                                    <a href="dettaglio_catalogo.php?id=<?php echo $correlato['id']; ?>" class="btn btn-primary btn-sm">Visualizza dettagli</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

    <?php endif; ?>
</div>
<?php require_once '../pagine/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Funzione per aggiornare l'immagine principale quando si clicca su una thumbnail
    function updateMainImage(src, element) {
        document.getElementById('main-product-image').src = src;

        // Rimuovi la classe active da tutte le thumbnails
        const thumbnails = document.querySelectorAll('.thumbnail-image');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));

        // Aggiungi la classe active solo all'elemento cliccato
        element.classList.add('active');
    }

    // Funzione per selezionare un colore
    function selectColor(element) {
        // Rimuovi la classe active da tutti i colori
        const colorOptions = document.querySelectorAll('.color-option');
        colorOptions.forEach(option => option.classList.remove('active'));

        // Aggiungi la classe active solo all'elemento cliccato
        element.classList.add('active');

        // Qui potresti aggiungere codice per aggiornare l'immagine in base al colore
        // se hai immagini specifiche per ciascun colore
        const colorId = element.getAttribute('data-color-id');
        console.log('Colore selezionato:', colorId);

        // Esempio: se hai un array di immagini per colore, potresti aggiornare l'immagine principale
        <?php if (!empty($varianti_colore)): ?>
        const colorVariants = <?php echo json_encode($varianti_colore); ?>;
        for (const variant of colorVariants) {
            if (variant.colore_id === colorId && variant.immagini.length > 0) {
                document.getElementById('main-product-image').src = variant.immagini[0].url;
                break;
            }
        }
        <?php endif; ?>
    }

    // Funzione per selezionare una variante (taglia, capacità, wattaggio)
    function selectVariant(element, type) {
        // Identifica il container delle opzioni in base al tipo
        let containerId;
        switch(type) {
            case 'size': containerId = 'size-options'; break;
            case 'capacity': containerId = 'capacity-options'; break;
            case 'wattage': containerId = 'wattage-options'; break;
            default: return;
        }

        // Rimuovi la classe active da tutte le opzioni dello stesso tipo
        const options = document.querySelectorAll(`#${containerId} .variant-option`);
        options.forEach(option => option.classList.remove('active'));

        // Aggiungi la classe active solo all'elemento cliccato
        element.classList.add('active');

        // Se la variante ha un prezzo diverso, aggiorna il prezzo visualizzato
        if (element.hasAttribute('data-price')) {
            const price = parseFloat(element.getAttribute('data-price'));
            document.getElementById('product-price').textContent =
                '€' + price.toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2}).replace('.', ',');
        }

        console.log(`${type} selezionato:`, element.textContent.trim());
    }

    // Funzioni per gestire la quantità
    function updateQuantity(change) {
        const quantityInput = document.getElementById('quantity');
        let currentValue = parseInt(quantityInput.value);

        currentValue += change;

        // Non permettere valori inferiori a 1
        if (currentValue < 1) currentValue = 1;

        quantityInput.value = currentValue;
    }

    // Funzione per aggiungere al carrello
    function addToCart() {
        const quantity = document.getElementById('quantity').value;
        const productId = <?php echo $id; ?>;

        // Raccogli tutte le varianti selezionate
        let selectedVariants = {};

        // Colore
        const selectedColor = document.querySelector('.color-option.active');
        if (selectedColor) {
            selectedVariants.colorId = selectedColor.getAttribute('data-color-id');
        }

        // Taglia
        const selectedSize = document.querySelector('#size-options .variant-option.active');
        if (selectedSize) {
            selectedVariants.sizeId = selectedSize.getAttribute('data-size-id');
        }

        // Capacità
        const selectedCapacity = document.querySelector('#capacity-options .variant-option.active');
        if (selectedCapacity) {
            selectedVariants.capacityId = selectedCapacity.getAttribute('data-capacity-id');
        }

        // Ottieni il prezzo corrente (quello visualizzato)
        const displayedPrice = document.getElementById('product-price').textContent
            .replace('€', '')
            .replace('.', '')
            .replace(',', '.');

        // Invia i dati al backend tramite AJAX
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                productId: productId,
                quantity: quantity,
                colorId: selectedVariants.colorId || null,
                sizeId: selectedVariants.sizeId || null,
                capacityId: selectedVariants.capacityId || null,
                price: displayedPrice
            })
        })
            .then(response => response.json())
            .then(response => {
                console.log('Risposta dal server:', response);
                return response.json();  // Proseguire con la decodifica del JSON
            })
            .then(data => {
                console.log('Dati dal server:', data);  // Verifica cosa contiene il JSON
                if (data.success) {
                    // Procedi come previsto
                } else {
                    // Gestisci l'errore
                    console.log('Errore:', data.message || 'Errore sconosciuto');
                }
            })

            .then(data => {
                if (data.success) {
                    // Mostra notifica di successo
                    const toast = document.createElement('div');
                    toast.className = 'toast-notification success';
                    toast.innerHTML = '<i class="bi bi-check-circle"></i> Prodotto aggiunto al carrello!';
                    document.body.appendChild(toast);

                    // Aggiorna il contatore del carrello
                    updateCartCount(data.cartCount);

                    // Rimuovi la notifica dopo 3 secondi
                    setTimeout(() => {
                        toast.remove();
                    }, 3000);
                } else {
                    // Mostra notifica di errore
                    const toast = document.createElement('div');
                    toast.className = 'toast-notification error';
                    toast.innerHTML = '<i class="bi bi-exclamation-circle"></i> ' + (data.message || 'Errore durante l\'aggiunta al carrello');
                    document.body.appendChild(toast);

                    // Rimuovi la notifica dopo 3 secondi
                    setTimeout(() => {
                        toast.remove();
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Si è verificato un errore durante l\'aggiunta al carrello.');
            });
    }

    // Funzione per aggiornare il contatore del carrello
    function updateCartCount(count) {
        const cartBadge = document.querySelector('.badge.rounded-pill.bg-danger');
        if (cartBadge) {
            cartBadge.textContent = count;
        } else {
            // Se il badge non esiste, crealo
            const cartLink = document.querySelector('a[href="../pagine/carrello.php"]');
            if (cartLink) {
                const newBadge = document.createElement('span');
                newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                newBadge.innerHTML = count + '<span class="visually-hidden">Articoli nel carrello</span>';
                cartLink.appendChild(newBadge);
            }
        }
    }
</script>
<script src="../js/dettaglio_catalogo.js"></script>
</body>
</html>