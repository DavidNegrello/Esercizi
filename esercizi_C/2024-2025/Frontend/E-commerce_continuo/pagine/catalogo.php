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


// Inclusione del file di configurazione del database
require_once '../conf/db_config.php';

// Ottenimento di tutte le categorie
$categorie = fetchAll("SELECT * FROM categorie ORDER BY nome");

// Ottenimento di tutte le marche
$marche = fetchAll("SELECT * FROM marche ORDER BY nome");

// Ottenimento delle impostazioni dei filtri
$filtri = fetchOne("SELECT * FROM filtri_impostazioni LIMIT 1");

// Inizializzazione dei parametri di ricerca
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$marca_filtro = isset($_GET['marca']) ? $_GET['marca'] : '';
$prezzo_min = isset($_GET['prezzo_min']) ? $_GET['prezzo_min'] : $filtri['prezzo_min'];
$prezzo_max = isset($_GET['prezzo_max']) ? $_GET['prezzo_max'] : $filtri['prezzo_max'];
$ricerca = isset($_GET['ricerca']) ? $_GET['ricerca'] : '';

// Costruzione della query in base ai filtri
$sql_prodotti = "SELECT p.*, c.nome as categoria_nome, m.nome as marca_nome 
                FROM prodotti p
                JOIN categorie c ON p.categoria_id = c.id
                JOIN marche m ON p.marca_id = m.id
                WHERE 1=1";

$types = "";
$params = [];

if (!empty($categoria_filtro)) {
    $sql_prodotti .= " AND c.id = ?";
    $types .= "i";
    $params[] = intval($categoria_filtro);
}

if (!empty($marca_filtro)) {
    $sql_prodotti .= " AND m.id = ?";
    $types .= "i";
    $params[] = intval($marca_filtro);
}

if (!empty($prezzo_min)) {
    $sql_prodotti .= " AND p.prezzo >= ?";
    $types .= "d";
    $params[] = floatval($prezzo_min);
}

if (!empty($prezzo_max)) {
    $sql_prodotti .= " AND p.prezzo <= ?";
    $types .= "d";
    $params[] = floatval($prezzo_max);
}

if (!empty($ricerca)) {
    $sql_prodotti .= " AND (p.nome LIKE ? OR c.nome LIKE ? OR m.nome LIKE ?)";
    $types .= "sss";
    $ricerca_param = "%{$ricerca}%";
    $params[] = $ricerca_param;
    $params[] = $ricerca_param;
    $params[] = $ricerca_param;
}

$sql_prodotti .= " ORDER BY p.nome";
$prodotti = fetchAll($sql_prodotti, $types, $params);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogo Componenti PC</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- File style-->
    <link rel="stylesheet" href="../stili/catalogo.css">
    <link rel="stylesheet" href="../stili/navbar_footer.css">
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

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar con filtri -->
        <div class="col-lg-3 mb-4">
            <div class="card sidebar">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filtri</h5>
                </div>
                <div class="card-body">
                    <form action="" method="GET">
                        <!-- Ricerca -->
                        <div class="filter-section">
                            <label for="ricerca" class="form-label">Cerca un prodotto</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="ricerca" name="ricerca" placeholder="Cerca un prodotto..." value="<?php echo htmlspecialchars($ricerca); ?>">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Categorie -->
                        <div class="filter-section">
                            <label class="form-label">Categorie</label>
                            <select class="form-select" name="categoria">
                                <option value="">Tutte le categorie</option>
                                <?php foreach ($categorie as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($categoria_filtro == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Marche -->
                        <div class="filter-section">
                            <label class="form-label">Marche</label>
                            <select class="form-select" name="marca">
                                <option value="">Tutte le marche</option>
                                <?php foreach ($marche as $mar): ?>
                                    <option value="<?php echo $mar['id']; ?>" <?php echo ($marca_filtro == $mar['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($mar['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Prezzo -->
                        <div class="filter-section">
                            <label class="form-label">Prezzo</label>
                            <div class="range-values mb-2">
                                <span>€<?php echo $filtri['prezzo_min']; ?></span>
                                <span>€<?php echo $filtri['prezzo_max']; ?></span>
                            </div>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="number" class="form-control" name="prezzo_min" placeholder="Min" value="<?php echo htmlspecialchars($prezzo_min); ?>" min="<?php echo $filtri['prezzo_min']; ?>" max="<?php echo $filtri['prezzo_max']; ?>" step="<?php echo $filtri['prezzo_step']; ?>">
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control" name="prezzo_max" placeholder="Max" value="<?php echo htmlspecialchars($prezzo_max); ?>" min="<?php echo $filtri['prezzo_min']; ?>" max="<?php echo $filtri['prezzo_max']; ?>" step="<?php echo $filtri['prezzo_step']; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Pulsanti -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Applica Filtri</button>
                            <a href="catalogo.php" class="btn btn-outline-secondary">Reimposta Filtri</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contenuto principale -->
        <div class="col-lg-9">
            <div class="mb-4">
                <h1 class="mb-3">Catalogo Componenti PC</h1>
                <p class="text-muted">
                    <?php echo count($prodotti); ?> prodotti trovati
                    <?php if (!empty($categoria_filtro) || !empty($marca_filtro) || !empty($ricerca) || $prezzo_min > $filtri['prezzo_min'] || $prezzo_max < $filtri['prezzo_max']): ?>
                        con i filtri applicati
                    <?php endif; ?>
                </p>
            </div>

            <?php if (empty($prodotti)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Nessun prodotto trovato con i filtri selezionati.
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($prodotti as $prodotto): ?>
                        <div class="col">
                            <div class="card product-card h-100">
                                <div class="product-img-container p-3">
                                    <?php
                                    $base_path = dirname(__DIR__); // Torna indietro di una directory
                                    $percorso_immagine = $base_path . '/' . str_replace('../', '', $prodotto['immagine']);
                                    if (file_exists($percorso_immagine)):
                                        ?>
                                        <img src="<?php echo htmlspecialchars($prodotto['immagine']); ?>" class="product-img" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>">
                                    <?php else: ?>
                                        <img src="placeholder.png" class="product-img" alt="Immagine non disponibile">
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($prodotto['nome']); ?></h5>
                                    <p class="card-text">
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($prodotto['categoria_nome']); ?></span>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($prodotto['marca_nome']); ?></span>
                                    </p>
                                    <p class="price">€<?php echo number_format($prodotto['prezzo'], 2, ',', '.'); ?></p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0" >
                                    <div class="d-grid" >
                                        <a href="dettaglio_catalogo.php?id=<?php echo $prodotto['id']; ?> " class="btn btn-primary" >
                                            <i class="fas fa-eye me-2" ></i>Visualizza
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once '../pagine/footer.php'; ?>
<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>