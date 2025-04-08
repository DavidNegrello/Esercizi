<?php
// Gestione sessione utente
session_start();

// Reindirizza se già loggato
if(isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Inclusione del file di configurazione del database
require_once '../conf/db_config.php';

// Inizializzazione variabili
$errore = '';
$email = '';

// Determina pagina attiva
$currentPage = basename($_SERVER['PHP_SELF']);

// Conteggio articoli nel carrello
$cartCount = 0;
if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $items) {
        $cartCount += $items['quantity'];
    }
}

// Gestione del login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupero dati form
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validazione base
    if (empty($email) || empty($password)) {
        $errore = "Inserisci sia email che password.";
    } else {
        // Verifica credenziali nel database
        $utente = fetchOne(
            "SELECT id, email, nome, password FROM utenti WHERE email = ?",
            's',
            [$email]
        );

        if ($utente && password_verify($password, $utente['password'])) {
            // Login riuscito, imposta variabili di sessione
            $_SESSION['user_id'] = $utente['id'];
            $_SESSION['email'] = $utente['email'];
            $_SESSION['username'] = $utente['nome']; // o altra logica per determinare username

            // Redirect dopo login
            $redirectTo = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '../index.php';
            unset($_SESSION['redirect_after_login']); // Pulisci la variabile di reindirizzamento

            header("Location: $redirectTo");
            exit();
        } else {
            $errore = "Email o password non validi.";
        }
    }
}

// Titolo della pagina
$titoloPagina = "Accedi";
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
    <link rel="stylesheet" href="../stili/login.css">

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

            <!-- Carrello a destra -->
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

                <!-- Pulsante registrazione -->
                <a href="../pagine/registrazione.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-user-plus me-1"></i> Registrati
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container my-5 pt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Accedi</li>
        </ol>
    </nav>

    <div class="login-container">
        <div class="text-center mb-4">
            <i class="fas fa-user-circle login-icon"></i>
            <h1 class="h3 mb-3 fw-normal">Accedi al tuo account</h1>
        </div>

        <?php if (!empty($errore)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $errore; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h5 class="mb-0">Inserisci le tue credenziali</h5>
            </div>
            <div class="card-body p-4">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="showPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">Ricordami</label>
                        <a href="recupero_password.php" class="float-end">Password dimenticata?</a>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-sign-in-alt me-2"></i>Accedi
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer py-3 border-0">
                <div class="text-center">
                    Non hai un account? <a href="registrazione.php" class="text-decoration-none">Registrati</a>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <p class="text-muted">Oppure accedi con</p>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary social-login-btn">
                            <i class="fab fa-google"></i> Continua con Google
                        </button>
                        <button class="btn btn-outline-primary social-login-btn">
                            <i class="fab fa-facebook-f"></i> Continua con Facebook
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/login.js"></script>
</body>
</html>
