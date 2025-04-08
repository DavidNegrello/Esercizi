<?php
// Gestione sessione utente
session_start();
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : '';

// Reindirizza alla pagina di login se l'utente non è loggato
if (!$loggedIn) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit();
}

// Conteggio articoli nel carrello
$cartCount = 0;
$totaleCarrello = 0;
$costoSpedizione = 0;

if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $items) {
        $cartCount += $items['quantity'];
    }
}

// Se il carrello è vuoto, reindirizza alla pagina del carrello
if($cartCount == 0) {
    header("Location: carrello.php");
    exit();
}

// Inclusione del file di configurazione del database
require_once '../conf/db_config.php';

// Determina pagina attiva
$currentPage = basename($_SERVER['PHP_SELF']);

// Recupero dati per il riepilogo dell'ordine
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

                $cartItems[] = [
                    'nome' => $prodotto['nome'],
                    'prezzo' => $prezzo,
                    'quantity' => $item['quantity'],
                    'subtotale' => $subtotale
                ];
            }
        }
    }
}

// Calcola spese di spedizione
$costoSpedizione = ($totaleCarrello > 199) ? 0 : 9.99;
$totaleOrdine = $totaleCarrello + $costoSpedizione;

// Gestione del form di checkout
$messaggioSuccesso = '';
$errore = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validazione semplice
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $indirizzo = trim($_POST['indirizzo']);
    $citta = trim($_POST['citta']);
    $cap = trim($_POST['cap']);
    $telefono = trim($_POST['telefono']);
    $metodoPagamento = $_POST['metodo_pagamento'];

    // Verifica che i campi obbligatori siano compilati
    if (empty($nome) || empty($cognome) || empty($indirizzo) || empty($citta) || empty($cap) || empty($telefono) || empty($metodoPagamento)) {
        $errore = "Tutti i campi sono obbligatori.";
    } else {
        // In questo punto inseriresti l'ordine nel database se avessi una tabella ordini
        // Per questa implementazione semplice, simuliamo un ordine di successo

        // Svuota il carrello
        $_SESSION['cart'] = [];

        // Imposta un messaggio di successo nella sessione
        $_SESSION['ordine_completato'] = true;

        // Reindirizza alla home
        header("Location: ../index.php?ordine=completato");
        exit();
    }
}

// Titolo della pagina
$titoloPagina = "Checkout";
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
    <link rel="stylesheet" href="../stili/checkout.css">
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

                <!-- Menu Utente -->
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
            </div>
        </div>
    </div>
</nav>

<div class="container my-5 pt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="carrello.php">Carrello</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <div class="text-center mb-4">
        <i class="fas fa-credit-card checkout-icon"></i>
        <h1 class="h3">Completa il tuo ordine</h1>
        <p class="text-muted">Inserisci i tuoi dati per completare l'acquisto</p>
    </div>

    <?php if (!empty($errore)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $errore; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informazioni di spedizione</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="checkoutForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cognome" class="form-label">Cognome <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cognome" name="cognome" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="indirizzo" class="form-label">Indirizzo di spedizione <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="indirizzo" name="indirizzo" placeholder="Via/Piazza e numero civico" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="citta" class="form-label">Città <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="citta" name="citta" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cap" class="form-label">CAP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cap" name="cap" required pattern="[0-9]{5}" maxlength="5">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Telefono <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" required>
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Note per la consegna (opzionale)</label>
                            <textarea class="form-control" id="note" name="note" rows="3" placeholder="Inserisci eventuali note o istruzioni per la consegna"></textarea>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Metodo di pagamento</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="metodo_pagamento" id="carta" value="carta" checked>
                                    <label class="form-check-label" for="carta">
                                        <i class="fab fa-cc-visa payment-icon"></i>
                                        <i class="fab fa-cc-mastercard payment-icon"></i>
                                        <span>Carta di credito/debito</span>
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="metodo_pagamento" id="paypal" value="paypal">
                                    <label class="form-check-label" for="paypal">
                                        <i class="fab fa-paypal payment-icon"></i>
                                        <span>PayPal</span>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="metodo_pagamento" id="contrassegno" value="contrassegno">
                                    <label class="form-check-label" for="contrassegno">
                                        <i class="fas fa-money-bill-wave payment-icon"></i>
                                        <span>Contrassegno</span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-light bg-light">
                                    <div class="card-body">
                                        <p class="card-text small text-muted">
                                            <i class="fas fa-shield-alt me-2"></i>
                                            I tuoi dati di pagamento sono protetti da crittografia SSL.
                                            Non salviamo i dettagli della tua carta.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="accettaTermini" required>
                            <label class="form-check-label" for="accettaTermini">
                                Accetto i <a href="../pagine/termini-condizioni.php" target="_blank">Termini e Condizioni</a> e la <a href="../pagine/privacy-policy.php" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Riepilogo ordine</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo $item['nome']; ?> &times; <?php echo $item['quantity']; ?></span>
                            <span>€<?php echo number_format($item['subtotale'], 2, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotale</span>
                        <span>€<?php echo number_format($totaleCarrello, 2, ',', '.'); ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Spedizione</span>
                        <span><?php echo ($costoSpedizione > 0) ? '€' . number_format($costoSpedizione, 2, ',', '.') : 'Gratuita'; ?></span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-3">
                        <strong>Totale</strong>
                        <strong>€<?php echo number_format($totaleOrdine, 2, ',', '.'); ?></strong>
                    </div>

                    <button type="submit" form="checkoutForm" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-lock me-2"></i> Completa l'ordine
                    </button>

                    <div class="text-center mt-3">
                        <a href="carrello.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Torna al carrello
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">Hai un codice sconto?</h6>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Inserisci codice">
                        <button class="btn btn-outline-secondary" type="button">Applica</button>
                    </div>
                    <div class="text-center text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        I codici sconto vengono applicati al totale dell'ordine
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../pagine/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>