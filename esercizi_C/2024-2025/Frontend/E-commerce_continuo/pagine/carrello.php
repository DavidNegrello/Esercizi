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
 * Carrello.php
 *
 * Pagina che mostra gli articoli nel carrello dell'utente,
 * con opzioni per modificare quantità, rimuovere articoli e procedere all'acquisto.
 */

// Inclusione del file di configurazione del database
require_once '../conf/db_config.php';

// Inizializzazione variabili
$totaleCarrello = 0;
$messaggioCarrello = '';

// Gestione azioni sul carrello
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aggiornamento quantità
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $itemId => $newQuantity) {
            if (is_numeric($newQuantity) && $newQuantity > 0) {
                $_SESSION['cart'][$itemId]['quantity'] = (int)$newQuantity;
            } elseif ($newQuantity <= 0) {
                // Se la quantità è 0 o negativa, rimuovi l'elemento
                unset($_SESSION['cart'][$itemId]);
            }
        }
        $messaggioCarrello = "Carrello aggiornato con successo";
    }

    // Rimozione articolo
    if (isset($_POST['remove_item'])) {
        $itemIdToRemove = $_POST['remove_item'];
        if (isset($_SESSION['cart'][$itemIdToRemove])) {
            unset($_SESSION['cart'][$itemIdToRemove]);
            $messaggioCarrello = "Articolo rimosso dal carrello";
        }
    }

    // Svuota carrello
    if (isset($_POST['empty_cart'])) {
        $_SESSION['cart'] = [];
        $messaggioCarrello = "Il carrello è stato svuotato";
    }
}

// Recupero dati degli articoli nel carrello
$cartItems = [];
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $itemId => $item) {
        if ($item['tipo'] === 'preassemblato') {
            // Recupero i dati del preassemblato
            $prodotto = fetchOne(
                "SELECT id, nome, prezzo, immagine FROM preassemblati WHERE id = ?",
                'i',
                [$itemId]
            );

            if ($prodotto) {
                $prezzo = $prodotto['prezzo'];

                // Aggiungi il prezzo delle personalizzazioni
                if (!empty($item['personalizzazioni'])) {
                    foreach ($item['personalizzazioni'] as $persId) {
                        $personalizzazione = fetchOne(
                            "SELECT prezzo FROM preassemblati_personalizzazioni WHERE id = ?",
                            'i',
                            [$persId]
                        );
                        if ($personalizzazione) {
                            $prezzo += $personalizzazione['prezzo'];
                        }
                    }
                }

                $subtotale = $prezzo * $item['quantity'];
                $totaleCarrello += $subtotale;

                // Recupero nomi delle personalizzazioni
                $personalizzazioniNomi = [];
                if (!empty($item['personalizzazioni'])) {
                    foreach ($item['personalizzazioni'] as $persId) {
                        $personalizzazione = fetchOne(
                            "SELECT nome FROM preassemblati_personalizzazioni WHERE id = ?",
                            'i',
                            [$persId]
                        );
                        if ($personalizzazione) {
                            $personalizzazioniNomi[] = $personalizzazione['nome'];
                        }
                    }
                }

                $cartItems[] = [
                    'id' => $itemId,
                    'nome' => $prodotto['nome'],
                    'prezzo' => $prezzo,
                    'subtotale' => $subtotale,
                    'quantity' => $item['quantity'],
                    'immagine' => $prodotto['immagine'],
                    'tipo' => 'preassemblato',
                    'colore' => $item['colore'] ?? 'Standard',
                    'personalizzazioni' => $personalizzazioniNomi
                ];
            }
        } else if ($item['tipo'] === 'componente') {
            // Per componenti singoli (da implementare in futuro)
            // Simile a preassemblati ma con query differente
        }
    }
}

// Titolo della pagina
$titoloPagina = "Il mio carrello";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titoloPagina; ?> - PC Componenti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../stili/navbar_footer.css">
    <link rel="stylesheet" href="../stili/carrello.css">
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

<div class="container my-5 pt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Carrello</li>
        </ol>
    </nav>

    <h1 class="mb-4">Il mio carrello</h1>

    <?php if (isset($messaggioCarrello) && !empty($messaggioCarrello)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $messaggioCarrello; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
            <h3>Il tuo carrello è vuoto</h3>
            <p class="text-muted">Sfoglia il catalogo per trovare prodotti da aggiungere al carrello.</p>
            <div class="mt-4">
                <a href="../pagine/preassemblati.php" class="btn btn-primary me-2">Esplora PC Preassemblati</a>
                <a href="../pagine/catalogo.php" class="btn btn-outline-secondary">Vai al Catalogo Componenti</a>
            </div>
        </div>
    <?php else: ?>
        <form method="post" id="cartForm">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Tabella prodotti carrello -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                    <tr>
                                        <th colspan="2">Prodotto</th>
                                        <th>Prezzo</th>
                                        <th>Quantità</th>
                                        <th>Subtotale</th>
                                        <th>Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr>
                                            <td width="80">
                                                <img src="<?php echo $item['immagine']; ?>" alt="<?php echo $item['nome']; ?>" class="img-thumbnail" width="70">
                                            </td>
                                            <td>
                                                <h6 class="mb-0"><?php echo $item['nome']; ?></h6>
                                                <?php if (!empty($item['colore'])): ?>
                                                    <small class="text-muted d-block">Colore: <?php echo $item['colore']; ?></small>
                                                <?php endif; ?>
                                                <?php if (!empty($item['personalizzazioni'])): ?>
                                                    <small class="text-muted d-block">Personalizzazioni: <?php echo implode(', ', $item['personalizzazioni']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>€<?php echo number_format($item['prezzo'], 2, ',', '.'); ?></td>
                                            <td>
                                                <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>"
                                                       min="1" max="10" class="form-control form-control-sm" style="width: 70px">
                                            </td>
                                            <td>€<?php echo number_format($item['subtotale'], 2, ',', '.'); ?></td>
                                            <td>
                                                <button type="submit" name="remove_item" value="<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button type="submit" name="update_cart" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Aggiorna carrello
                            </button>
                            <button type="submit" name="empty_cart" class="btn btn-outline-danger"
                                    onclick="return confirm('Sei sicuro di voler svuotare il carrello?')">
                                <i class="fas fa-trash me-1"></i> Svuota carrello
                            </button>
                        </div>
                    </div>

                    <!-- Continua acquisti -->
                    <div class="mb-4">
                        <a href="../pagine/preassemblati.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Continua acquisti
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Riepilogo carrello -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Riepilogo ordine</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotale</span>
                                <span>€<?php echo number_format($totaleCarrello, 2, ',', '.'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Spedizione</span>
                                <?php $costoSpedizione = ($totaleCarrello > 199) ? 0 : 9.99; ?>
                                <span><?php echo ($costoSpedizione > 0) ? '€' . number_format($costoSpedizione, 2, ',', '.') : 'Gratuita'; ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Totale</strong>
                                <strong>€<?php echo number_format($totaleCarrello + $costoSpedizione, 2, ',', '.'); ?></strong>
                            </div>

                            <?php if (!$loggedIn): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <a href="../pagine/login.php">Accedi</a> o <a href="../pagine/registrazione.php">Registrati</a> per completare l'acquisto
                                </div>
                            <?php endif; ?>

                            <button type="button" class="btn btn-primary btn-lg w-100"
                                <?php echo (!$loggedIn) ? 'disabled' : ''; ?>
                                    onclick="window.location.href='checkout.php';">
                                <i class="fas fa-credit-card me-2"></i> Procedi al checkout
                            </button>
                        </div>
                    </div>

                    <!-- Codice promozionale -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Codice promozionale</h5>
                        </div>
                        <div class="card-body">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Inserisci codice">
                                <button class="btn btn-outline-secondary" type="button">Applica</button>
                            </div>
                        </div>
                    </div>

                    <!-- Metodi di pagamento -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Metodi di pagamento</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <i class="fab fa-cc-visa fa-2x"></i>
                                <i class="fab fa-cc-mastercard fa-2x"></i>
                                <i class="fab fa-cc-amex fa-2x"></i>
                                <i class="fab fa-cc-paypal fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once '../pagine/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>