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
 * dettaglio_preassemblato.php
 *
 * Pagina che mostra i dettagli di un PC o laptop preassemblato, incluse specifiche,
 * varianti di colore e opzioni di personalizzazione.
 */

// Inclusione del file di configurazione del database
require_once '../conf/db_config.php';

// Controllo se è stato fornito un ID valido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Reindirizzamento alla pagina principale o mostro un errore
    header("Location: ../index.html");
    exit;
}

$id = (int)$_GET['id'];

// Recupero i dati del preassemblato
$preassemblato = fetchOne(
    "SELECT * FROM preassemblati WHERE id = ?",
    'i',
    [$id]
);

// Se non esiste il preassemblato, reindirizzo
if (!$preassemblato) {
    header("Location: index.php");
    exit;
}

// Recupero le specifiche di base
$specificheBase = fetchAll(
    "SELECT nome_specifica, valore FROM preassemblati_specifiche_base WHERE preassemblato_id = ? ORDER BY nome_specifica",
    'i',
    [$id]
);

// Recupero le specifiche dettagliate
$specificheDettagliate = fetchAll(
    "SELECT nome_specifica, valore FROM preassemblati_specifiche_dettagliate WHERE preassemblato_id = ? ORDER BY nome_specifica",
    'i',
    [$id]
);

// Recupero le immagini
$immagini = fetchAll(
    "SELECT url, is_principale FROM preassemblati_immagini WHERE preassemblato_id = ? ORDER BY is_principale DESC",
    'i',
    [$id]
);

// Recupero i colori disponibili
$colori = fetchAll(
    "SELECT colore FROM preassemblati_colori WHERE preassemblato_id = ?",
    'i',
    [$id]
);

// Recupero le varianti colore e le immagini associate
$variantiColore = fetchAll(
    "SELECT colore, url FROM preassemblati_varianti_colore WHERE preassemblato_id = ?",
    'i',
    [$id]
);

// Raggruppo le varianti colore per colore
$immaginiPerColore = [];
foreach ($variantiColore as $variante) {
    $immaginiPerColore[$variante['colore']][] = $variante['url'];
}

// Recupero le personalizzazioni
$personalizzazioni = fetchAll(
    "SELECT id, nome, prezzo FROM preassemblati_personalizzazioni WHERE preassemblato_id = ? ORDER BY prezzo",
    'i',
    [$id]
);

// Gestione del colore selezionato
$coloreSelezionato = isset($_GET['colore']) ? sanitizeInput($_GET['colore']) :
    (count($colori) > 0 ? $colori[0]['colore'] : '');

// Immagine principale
$immaginePrincipale = $preassemblato['immagine'];
if (!empty($immagini)) {
    foreach ($immagini as $img) {
        if ($img['is_principale']) {
            $immaginePrincipale = $img['url'];
            break;
        }
    }
}

// Se c'è un colore selezionato e abbiamo immagini per quel colore, usiamo quelle
if ($coloreSelezionato && isset($immaginiPerColore[$coloreSelezionato]) && !empty($immaginiPerColore[$coloreSelezionato])) {
    $immaginePrincipale = $immaginiPerColore[$coloreSelezionato][0];
    $immaginiColoreAttuale = $immaginiPerColore[$coloreSelezionato];
} else {
    // Altrimenti usiamo le immagini standard
    $immaginiColoreAttuale = array_column($immagini, 'url');
}

// Calcoliamo il prezzo totale con le personalizzazioni selezionate
$prezzoTotale = $preassemblato['prezzo'];
if (isset($_POST['aggiungi_al_carrello'])) {
    if (isset($_POST['personalizzazioni']) && is_array($_POST['personalizzazioni'])) {
        foreach ($_POST['personalizzazioni'] as $persId) {
            foreach ($personalizzazioni as $p) {
                if ($p['id'] == $persId) {
                    $prezzoTotale += $p['prezzo'];
                    break;
                }
            }
        }
    }
    // Qui si potrebbe implementare la logica per aggiungere al carrello
    // Per ora mostriamo solo un messaggio
    $messaggioCarrello = "Prodotto aggiunto al carrello con un prezzo totale di €" . number_format($prezzoTotale, 2, ',', '.');
}

// Titolo della pagina
$titoloPagina = $preassemblato['nome'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titoloPagina; ?> - Dettaglio Prodotto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../stili/navbar_footer.css">
    <link rel="stylesheet" href="../stili/dettaglio_preassemblato.css">

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
                            <li><a class="dropdown-item text-danger" href="../actions/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
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

<div class="container my-5">
    <!-- Barra di navigazione e breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.html">Home</a></li>
            <li class="breadcrumb-item"><a href="../pagine/preassemblati.php?cat=<?php echo urlencode($preassemblato['categoria']); ?>"><?php echo $preassemblato['categoria']; ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $preassemblato['nome']; ?></li>
        </ol>
    </nav>




    <?php if (isset($messaggioCarrello)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $messaggioCarrello; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Immagini del prodotto -->
        <div class="col-md-6 mb-4">
            <div class="text-center">
                <img id="main-image" src="<?php echo $immaginePrincipale; ?>" alt="<?php echo $preassemblato['nome']; ?>" class="img-fluid">
            </div>

            <!-- Miniature -->
            <div class="thumbnail-container">
                <?php foreach ($immaginiColoreAttuale as $index => $img): ?>
                    <img src="<?php echo $img; ?>"
                         alt="Thumbnail"
                         class="thumbnail <?php echo ($index === 0) ? 'active' : ''; ?>"
                         onclick="cambiaImmagine('<?php echo $img; ?>', this)">
                <?php endforeach; ?>
            </div>

            <!-- Selezione colore se disponibile -->
            <?php if (!empty($colori)): ?>
                <div class="mt-4">
                    <h5>Colori disponibili:</h5>
                    <?php foreach ($colori as $colore): ?>
                        <a href="?id=<?php echo $id; ?>&colore=<?php echo urlencode($colore['colore']); ?>"
                           class="btn btn-outline-secondary <?php echo ($coloreSelezionato === $colore['colore']) ? 'active' : ''; ?>">
                            <?php echo $colore['colore']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Informazioni prodotto -->
        <div class="col-md-6">
            <h1><?php echo $preassemblato['nome']; ?></h1>
            <h4 class="text-primary mb-4">€<?php echo number_format($preassemblato['prezzo'], 2, ',', '.'); ?></h4>

            <div class="mb-4">
                <p><?php echo $preassemblato['descrizione']; ?></p>
            </div>

            <!-- Specifiche principali -->
            <div class="mb-4">
                <h4>Specifiche Principali</h4>
                <table class="table spec-table">
                    <tbody>
                    <?php foreach ($specificheBase as $spec): ?>
                        <tr>
                            <td><?php echo $spec['nome_specifica']; ?></td>
                            <td><?php echo $spec['valore']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Form per aggiungere al carrello con personalizzazioni -->
            <form method="post" action="?id=<?php echo $id; ?><?php echo $coloreSelezionato ? '&colore=' . urlencode($coloreSelezionato) : ''; ?>">
                <?php if (!empty($personalizzazioni)): ?>
                    <div class="mb-4">
                        <h4>Personalizzazioni disponibili</h4>
                        <?php foreach ($personalizzazioni as $pers): ?>
                            <div class="personalizzazione-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="personalizzazioni[]"
                                           value="<?php echo $pers['id']; ?>" id="pers<?php echo $pers['id']; ?>"
                                           onchange="aggiornaPrezzo()">
                                    <label class="form-check-label" for="pers<?php echo $pers['id']; ?>">
                                        <?php echo $pers['nome']; ?> - €<?php echo number_format($pers['prezzo'], 2, ',', '.'); ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <h4>Prezzo Totale: <span id="prezzo-totale">€<?php echo number_format($preassemblato['prezzo'], 2, ',', '.'); ?></span></h4>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="aggiungi_al_carrello" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>Aggiungi al carrello
                    </button>
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="fas fa-heart me-2"></i>Aggiungi ai preferiti
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Specifiche dettagliate -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Specifiche dettagliate</h3>
            <div class="table-responsive">
                <table class="table table-striped spec-table">
                    <tbody>
                    <?php foreach ($specificheDettagliate as $spec): ?>
                        <tr>
                            <td><?php echo $spec['nome_specifica']; ?></td>
                            <td><?php echo $spec['valore']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Prodotti correlati -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Prodotti correlati</h3>
            <div class="row">
                <?php
                $prodottiCorrelati = fetchAll(
                    "SELECT id, nome, prezzo, immagine FROM preassemblati 
                         WHERE categoria = ? AND id != ? 
                         ORDER BY RAND() LIMIT 4",
                    'si',
                    [$preassemblato['categoria'], $id]
                );

                foreach ($prodottiCorrelati as $prodotto):
                    ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo $prodotto['immagine']; ?>" class="card-img-top" alt="<?php echo $prodotto['nome']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $prodotto['nome']; ?></h5>
                                <p class="card-text text-primary">€<?php echo number_format($prodotto['prezzo'], 2, ',', '.'); ?></p>
                                <a href="dettaglio_preassemblato.php?id=<?php echo $prodotto['id']; ?>" class="btn btn-sm btn-outline-primary">Visualizza</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Funzione per cambiare l'immagine principale
    function cambiaImmagine(url, elemento) {
        document.getElementById('main-image').src = url;

        // Rimuovi la classe 'active' da tutte le miniature
        let thumbnails = document.querySelectorAll('.thumbnail');
        thumbnails.forEach(thumb => {
            thumb.classList.remove('active');
        });

        // Aggiungi la classe 'active' all'elemento cliccato
        elemento.classList.add('active');
    }

    // Funzione per aggiornare il prezzo totale in base alle personalizzazioni selezionate
    function aggiornaPrezzo() {
        let prezzoBase = <?php echo $preassemblato['prezzo']; ?>;
        let prezzoTotale = prezzoBase;

        // Recupera tutti i checkbox delle personalizzazioni
        let checkboxes = document.querySelectorAll('input[name="personalizzazioni[]"]:checked');

        // Array di prezzi delle personalizzazioni
        let prezziPersonalizzazioni = <?php echo json_encode(array_column($personalizzazioni, 'prezzo', 'id')); ?>;

        // Somma i prezzi delle personalizzazioni selezionate
        checkboxes.forEach(function(checkbox) {
            prezzoTotale += parseFloat(prezziPersonalizzazioni[checkbox.value] || 0);
        });

        // Aggiorna il prezzo totale mostrato
        document.getElementById('prezzo-totale').textContent = '€' + prezzoTotale.toFixed(2).replace('.', ',');
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>