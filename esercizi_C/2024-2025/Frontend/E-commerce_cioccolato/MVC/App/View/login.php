<?php
$appConfig = require dirname(__DIR__, 2) . '/appConfig.php';
$baseUrl = $appConfig['baseURL'] . $appConfig['prjName'];
$homePage = $baseUrl . 'mvc/home';
$href = $appConfig['baseURL'] . $appConfig['prjName'] . $appConfig['cssLogin'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi - Mondo Cioccolato</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $href ?>">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="login-container">
                <div class="row g-0">
                    <!-- Sidebar con tema cioccolato -->
                    <div class="col-md-5 chocolate-sidebar d-flex flex-column align-items-center justify-content-center p-4 text-center position-relative">
                        <div class="position-relative z-index-1">
                            <h2 class="mb-4 logo-text">Mondo Cioccolato</h2>
                            <p class="fs-5 mb-4">Delizioso in ogni momento</p>
                            <p>Entra nel mondo del cioccolato artigianale più pregiato e scopri un'esperienza di gusto
                                unica.</p>
                        </div>
                    </div>

                    <!-- Form di login -->
                    <div class="col-md-7 bg-white p-5">
                        <div class="text-center mb-4">
                            <h3 class="mb-2">Bentornato!</h3>
                            <p class="text-muted">Accedi al tuo account</p>
                        </div>

                        <form>
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="nome@esempio.com"
                                       required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password"
                                       placeholder="Inserisci la tua password" required>
                            </div>
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Ricordami</label>
                            </div>
                            <button type="submit" class="btn btn-chocolate w-100 py-2 mb-4">Accedi</button>

                            <div class="text-center">
                                <p><a href="#" class="chocolate-link">Password dimenticata?</a></p>
                                <p class="mt-3">Non hai un account? <a href="#" class="chocolate-link">Registrati
                                        ora</a></p>
                            </div>
                            <!-- Tasto alternativo per tornare alla home -->
                            <div class="text-center mt-4">
                                <a href="<?= $homePage ?>" class="btn btn-outline-chocolate">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-house-door me-2" viewBox="0 0 16 16">
                                        <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z"/>
                                    </svg>
                                    Torna alla Homepage
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <small class="text-muted">&copy; 2025 Mondo Cioccolato - Tutti i diritti riservati</small>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>