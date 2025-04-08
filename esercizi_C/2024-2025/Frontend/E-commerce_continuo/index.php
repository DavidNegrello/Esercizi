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
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PC Componenti - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="stili/home.css">
    <link rel="icon" type="image/png" href="immagini/favicon_io/favicon.ico">
    <link rel="stylesheet" href="stili/navbar_footer.css">
    <!--Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <!-- Logo e Brand -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="immagini/favicon_io/favicon.ico" alt="Logo" width="30" height="30" class="me-2">
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
                    <a class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>"
                       href="index.php" <?php echo $currentPage == 'index.php' ? 'aria-current="page"' : ''; ?>>Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="pagine/catalogo.php" aria-expanded="false">
                        Componenti
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'preassemblati.php' ? 'active' : ''; ?>"
                       href="pagine/preassemblati.php">PC Preassemblati</a>
                </li>
            </ul>

            <!-- Carrello e Login/Utente a destra -->
            <div class="d-flex align-items-center">
                <!-- Carrello con counter dinamico -->
                <div class="position-relative me-3">
                    <a class="btn btn-outline-light btn-sm position-relative" href="pagine/carrello.php">
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
                            <li><a class="dropdown-item" href="pagine/profilo.php"><i class="fas fa-id-card me-2"></i>Il mio profilo</a></li>
                            <li><a class="dropdown-item" href="pagine/ordini.php"><i class="fas fa-box me-2"></i>I miei ordini</a></li>
                            <li><a class="dropdown-item" href="pagine/wishlist.php"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="pagine/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="pagine/login.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-sign-in-alt me-1"></i> Accedi
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title" id="hero-title">Benvenuto su PC Componenti</h1>
            <p class="hero-desc" id="hero-desc">Scopri i migliori componenti per il tuo PC</p>
            <a href="#" class="btn hero-btn" id="hero-btn">Esplora</a>
        </div>
    </div>
</section>

<!-- Prodotti più acquistati -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title mb-4">Prodotti più acquistati</h2>
        <div class="row" id="prodotti-popolari">
            <!-- I prodotti verranno caricati qui -->
        </div>
    </div>
</section>

<!-- Offerte speciali -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title" id="titolo-offerte">Offerte Speciali</h2>
        <div class="row" id="offerte-speciali">
            <!-- Le offerte verranno caricate qui -->
        </div>
    </div>
</section>

<!-- Prodotti in evidenza -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title mb-4">Prodotti in evidenza</h2>
        <div class="row g-4" id="prodotti-evidenza">
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm product-card">
                    <div class="product-image-container">
                        <img src="immagini/prodotti/cpu_ryzen.jpg" class="card-img-top product-image" alt="AMD Ryzen 9">
                        <div class="product-overlay">
                            <a href="pagine/dettaglio_catalogo.html?id=1" class="btn btn-primary">Vedi dettagli</a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">CPU</span>
                            <span class="text-warning">
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star-half-alt"></i>
</span>
                        </div>
                        <h5 class="card-title">AMD Ryzen 9 5900X</h5>
                        <p class="card-text text-muted small">Processore 12 core, 24 thread, fino a 4.8GHz</p>
                        <div class="mt-auto">
                            <p class="card-text fw-bold text-primary fs-5">499.99€</p>
                            <a href="pagine/dettaglio_catalogo.html?id=1" class="btn btn-outline-primary w-100">
                                <i class="fas fa-eye me-2"></i>Vedi dettagli
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm product-card">
                    <div class="product-image-container">
                        <img src="immagini/prodotti/gpu_rtx.jpg" class="card-img-top product-image" alt="NVIDIA RTX 3080">
                        <div class="product-overlay">
                            <a href="pagine/dettaglio_catalogo.html?id=2" class="btn btn-primary">Vedi dettagli</a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-success">GPU</span>
                            <span class="text-warning">
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
</span>
                        </div>
                        <h5 class="card-title">NVIDIA RTX 3080</h5>
                        <p class="card-text text-muted small">Scheda grafica 10GB GDDR6X, Ray Tracing</p>
                        <div class="mt-auto">
                            <p class="card-text fw-bold text-primary fs-5">799.99€</p>
                            <a href="pagine/dettaglio_catalogo.html?id=2" class="btn btn-outline-primary w-100">
                                <i class="fas fa-eye me-2"></i>Vedi dettagli
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm product-card">
                    <div class="product-image-container">
                        <img src="immagini/prodotti/ram_corsair.jpg" class="card-img-top product-image" alt="Corsair Vengeance RGB">
                        <div class="product-overlay">
                            <a href="pagine/dettaglio_catalogo.html?id=3" class="btn btn-primary">Vedi dettagli</a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-danger">RAM</span>
                            <span class="text-warning">
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="far fa-star"></i>
</span>
                        </div>
                        <h5 class="card-title">Corsair Vengeance RGB</h5>
                        <p class="card-text text-muted small">32GB (2x16GB) DDR4 3600MHz, RGB</p>
                        <div class="mt-auto">
                            <p class="card-text fw-bold text-primary fs-5">189.99€</p>
                            <a href="pagine/dettaglio_catalogo.html?id=3" class="btn btn-outline-primary w-100">
                                <i class="fas fa-eye me-2"></i>Vedi dettagli
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm product-card">
                    <div class="product-image-container">
                        <img src="immagini/prodotti/ssd_samsung.jpg" class="card-img-top product-image" alt="Samsung 970 EVO Plus">
                        <div class="product-overlay">
                            <a href="pagine/dettaglio_catalogo.html?id=4" class="btn btn-primary">Vedi dettagli</a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-info text-dark">Storage</span>
                            <span class="text-warning">
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star"></i>
<i class="fas fa-star-half-alt"></i>
</span>
                        </div>
                        <h5 class="card-title">Samsung 970 EVO Plus</h5>
                        <p class="card-text text-muted small">SSD NVMe M.2 1TB, 3500MB/s</p>
                        <div class="mt-auto">
                            <p class="card-text fw-bold text-primary fs-5">149.99€</p>
                            <a href="pagine/dettaglio_catalogo.html?id=4" class="btn btn-outline-primary w-100">
                                <i class="fas fa-eye me-2"></i>Vedi dettagli
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Perché sceglierci -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title mb-5">Perché scegliere PC Componenti</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <h4 class="mt-4">Spedizione Veloce</h4>
                    <p class="text-muted">Consegna in 24/48 ore in tutta Italia</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="mt-4">Garanzia Estesa</h4>
                    <p class="text-muted">Tutti i prodotti con garanzia di 2 anni</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="mt-4">Supporto Tecnico</h4>
                    <p class="text-muted">Assistenza tecnica 7 giorni su 7</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h4 class="mt-4">Pagamenti Sicuri</h4>
                    <p class="text-muted">Transazioni protette e sicure</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="py-5 newsletter-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h2 class="mb-4">Iscriviti alla nostra Newsletter</h2>
                <p class="text-muted mb-4">Ricevi in anteprima offerte esclusive e novità sui prodotti</p>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="La tua email" aria-label="Email">
                    <button class="btn btn-primary" type="button">Iscriviti</button>
                </div>
                <p class="small text-muted">Ci impegniamo a rispettare la tua privacy. Non condivideremo mai i tuoi dati.</p>
            </div>
        </div>
    </div>
</section>


<?php require_once 'pagine/footer.php'; ?>


<script src="js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>