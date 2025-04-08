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
 * Pagina dei PC preassemblati
 *
 * Visualizza tutti i PC preassemblati disponibili con opzioni di filtro
 */

// Importa la configurazione del database
require_once '../conf/db_config.php';

// Filtro categoria e prezzo
$categoria_filtro = isset($_GET['categoria']) ? sanitizeInput($_GET['categoria']) : '';
$prezzo_min = isset($_GET['prezzo_min']) ? floatval($_GET['prezzo_min']) : 0;
$prezzo_max = isset($_GET['prezzo_max']) ? floatval($_GET['prezzo_max']) : 4000;

// Costruzione della query in base ai filtri
$where_clauses = [];
$params = [];
$types = "";

if (!empty($categoria_filtro)) {
    $where_clauses[] = "categoria = ?";
    $params[] = $categoria_filtro;
    $types .= "s";
}

$where_clauses[] = "prezzo BETWEEN ? AND ?";
$params[] = $prezzo_min;
$params[] = $prezzo_max;
$types .= "dd";

$sql = "SELECT * FROM preassemblati";
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY categoria, prezzo";

// Esecuzione della query con i filtri
$preassemblati = fetchAll($sql, $types, $params);

// Ottieni categorie distinte per il filtro
$categorie = fetchAll("SELECT DISTINCT categoria FROM preassemblati ORDER BY categoria ASC");
$categorie_list = array_column($categorie, 'categoria');
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Preassemblati - HardwareTech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../stili/navbar_footer.css">
    <link rel="stylesheet" href="../stili/preassemblati.css">
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

<div class="container py-5">
    <h1 class="text-center mb-5">PC Preassemblati</h1>

    <!-- Sezione filtri -->
    <div class="filter-section mb-4">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-4">
                <label for="categoria" class="form-label">Categoria</label>
                <select name="categoria" id="categoria" class="form-select">
                    <option value="">Tutte le categorie</option>
                    <?php foreach ($categorie_list as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($categoria_filtro == $cat) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="prezzo_min" class="form-label">Prezzo Minimo</label>
                <input type="number" name="prezzo_min" id="prezzo_min" class="form-control" value="<?php echo $prezzo_min; ?>" min="0" step="50">
            </div>
            <div class="col-md-4">
                <label for="prezzo_max" class="form-label">Prezzo Massimo</label>
                <input type="number" name="prezzo_max" id="prezzo_max" class="form-control" value="<?php echo $prezzo_max; ?>" min="0" step="50">
            </div>
            <div class="col-12 text-center mt-3">
                <button type="submit" class="btn btn-primary">Applica Filtri</button>
                <a href="preassemblati.php" class="btn btn-outline-secondary ms-2">Reimposta Filtri</a>
            </div>
        </form>
    </div>

    <!-- Visualizzazione dei risultati -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        <?php if (!empty($preassemblati)): ?>
            <?php foreach($preassemblati as $pc): ?>
                <div class="col">
                    <div class="card h-100 position-relative">
                        <span class="category-badge"><?php echo htmlspecialchars($pc['categoria']); ?></span>
                        <img src="<?php echo htmlspecialchars($pc['immagine']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($pc['nome']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($pc['nome']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($pc['descrizione']); ?></p>
                            <p class="price"><?php echo number_format($pc['prezzo'], 2, ',', '.'); ?> €</p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 text-center">
                            <a href="dettaglio_preassemblato.php?id=<?php echo $pc['id']; ?>" class="btn btn-primary">Dettagli</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    Nessun prodotto trovato con i filtri selezionati.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../pagine/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>