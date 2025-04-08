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
$successo = '';
$email = '';
$nome = '';
$cognome = '';

// Determina pagina attiva
$currentPage = basename($_SERVER['PHP_SELF']);

// Conteggio articoli nel carrello
$cartCount = 0;
if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $items) {
        $cartCount += $items['quantity'];
    }
}

// Gestione della registrazione
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupero dati form
    $email = trim($_POST['email']);
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $password = $_POST['password'];
    $confermaPassword = $_POST['conferma_password'];

    // Validazione
    if (empty($email) || empty($nome) || empty($cognome) || empty($password)) {
        $errore = "Tutti i campi sono obbligatori.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errore = "Formato email non valido.";
    } elseif (strlen($password) < 8) {
        $errore = "La password deve contenere almeno 8 caratteri.";
    } elseif ($password !== $confermaPassword) {
        $errore = "Le password non corrispondono.";
    } else {
        // Verifica se l'email esiste già
        $esisteEmail = fetchOne(
            "SELECT id FROM utenti WHERE email = ?",
            's',
            [$email]
        );

        if ($esisteEmail) {
            $errore = "L'email inserita è già registrata.";
        } else {
            // Hash della password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Inserimento nel database
            $result = executeQuery(
                "INSERT INTO utenti (email, nome, cognome, password) VALUES (?, ?, ?, ?)",
                'ssss',
                [$email, $nome, $cognome, $passwordHash]
            );


            if ($result) {
                // Registrazione completata con successo
                $successo = "Registrazione completata con successo! Ora puoi accedere.";

                // Opzionale: Login automatico dopo registrazione
                $nuovoUtente = fetchOne(
                    "SELECT id, email, nome FROM utenti WHERE email = ?",
                    's',
                    [$email]
                );

                if ($nuovoUtente) {
                    $_SESSION['user_id'] = $nuovoUtente['id'];
                    $_SESSION['email'] = $nuovoUtente['email'];
                    $_SESSION['username'] = $nuovoUtente['nome'];

                    // Reindirizzamento
                    header("Location: ../index.php?registrazione=successo");
                    exit();
                }
            } else {
                $errore = "Si è verificato un errore durante la registrazione. Riprova più tardi.";
            }
        }
    }
}

// Titolo della pagina
$titoloPagina = "Registrazione";
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
    <link rel="stylesheet" href="../stili/registrazione.css">
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

                <!-- Pulsante accedi -->
                <a href="../pagine/login.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-sign-in-alt me-1"></i> Accedi
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
            <li class="breadcrumb-item active" aria-current="page">Registrazione</li>
        </ol>
    </nav>

    <div class="registration-container">
        <div class="text-center mb-4">
            <i class="fas fa-user-plus registration-icon"></i>
            <h1 class="h3 mb-3 fw-normal">Crea un nuovo account</h1>
        </div>

        <?php if (!empty($errore)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $errore; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($successo)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $successo; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header py-3">
                <h5 class="mb-0">Informazioni personali</h5>
            </div>
            <div class="card-body p-4">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="form-text">Useremo questa email per comunicazioni importanti</div>
                    </div>

                    <div class="row">
                        <!-- Nome -->
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="nome" name="nome"
                                       value="<?php echo htmlspecialchars($nome); ?>" required>
                            </div>
                        </div>

                        <!-- Cognome -->
                        <div class="col-md-6 mb-3">
                            <label for="cognome" class="form-label">Cognome <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="cognome" name="cognome"
                                       value="<?php echo htmlspecialchars($cognome); ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password"
                                   minlength="8" required onkeyup="checkPasswordStrength(this.value)">
                            <button class="btn btn-outline-secondary" type="button" id="showPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        <div class="password-hint mt-1">
                            La password deve contenere almeno 8 caratteri. Si consiglia di usare lettere maiuscole, minuscole, numeri e caratteri speciali.
                        </div>
                    </div>

                    <!-- Conferma Password -->
                    <div class="mb-3">
                        <label for="conferma_password" class="form-label">Conferma Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="conferma_password" name="conferma_password"
                                   minlength="8" required onkeyup="checkPasswordMatch()">
                            <button class="btn btn-outline-secondary" type="button" id="showConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text" id="passwordMatchMessage"></div>
                    </div>

                    <!-- Termini e Condizioni -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            Accetto i <a href="../pagine/termini-condizioni.php" target="_blank">Termini e Condizioni</a> e la <a href="../pagine/privacy-policy.php" target="_blank">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Newsletter -->
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter">
                        <label class="form-check-label" for="newsletter">
                            Desidero ricevere offerte e novità via email
                        </label>
                    </div>

                    <!-- Pulsante Registrazione -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-user-plus me-2"></i>Crea Account
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer py-3 border-0">
                <div class="text-center">
                    Hai già un account? <a href="login.php" class="text-decoration-none">Accedi</a>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <p class="text-muted">Oppure registrati con</p>
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
<script src="../js/registrazione.js"></script>
</body>
</html>
