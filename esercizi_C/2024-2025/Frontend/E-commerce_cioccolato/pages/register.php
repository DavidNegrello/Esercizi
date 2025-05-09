<?php
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrati - Mondo Cioccolato</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/login.css">
    <style>
        :root {
            --chocolate-dark: #3c1414;
            --chocolate-medium: #6b4226;
            --chocolate-light: #a67c52;
            --chocolate-milk: #d4a76a;
            --chocolate-white: #f8ebd8;
        }

        body {
            background-color: var(--chocolate-white);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)),
            url("../img/culone.png");
            background-size: cover;
            background-position: center;
        }

        .registration-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(107, 66, 38, 0.3);
        }

        .chocolate-sidebar {
            background: linear-gradient(135deg, var(--chocolate-dark), var(--chocolate-medium));
            color: var(--chocolate-white);
            position: relative;
        }

        .chocolate-sidebar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("../img/culone.png");
            background-size: cover;
            opacity: 0.2;
        }

        .btn-chocolate {
            background-color: var(--chocolate-medium);
            border-color: var(--chocolate-medium);
            color: white;
        }

        .btn-chocolate:hover {
            background-color: var(--chocolate-dark);
            border-color: var(--chocolate-dark);
            color: white;
        }

        .btn-outline-chocolate {
            color: var(--chocolate-medium);
            border-color: var(--chocolate-medium);
            background-color: transparent;
        }

        .btn-outline-chocolate:hover {
            background-color: var(--chocolate-medium);
            color: white;
        }

        .form-control:focus {
            border-color: var(--chocolate-light);
            box-shadow: 0 0 0 0.25rem rgba(107, 66, 38, 0.25);
        }

        .chocolate-link {
            color: var(--chocolate-medium);
            text-decoration: none;
        }

        .chocolate-link:hover {
            color: var(--chocolate-dark);
            text-decoration: underline;
        }

        .logo-text {
            font-family: 'Georgia', serif;
            font-weight: bold;
            font-style: italic;
        }

        .logo-text a {
            color: var(--chocolate-white);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .logo-text a:hover {
            color: var(--chocolate-milk);
            text-decoration: none;
        }

        .home-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="registration-container">
                <div class="row g-0">
                    <!-- Sidebar con tema cioccolato -->
                    <div class="col-md-5 chocolate-sidebar d-flex flex-column align-items-center justify-content-center p-4 text-center position-relative">
                        <div class="position-relative z-index-1">
                            <h2 class="mb-4 logo-text">Mondo Cioccolato</h2>
                            <p class="fs-5 mb-4">Diventa parte della nostra famiglia</p>
                            <p>Registrati ora per accedere a sconti esclusivi, ricette speciali e tutte le delizie del nostro mondo di cioccolato.</p>
                        </div>
                    </div>

                    <!-- Form di registrazione -->
                    <div class="col-md-7 bg-white p-5">
                        <div class="text-center mb-4">
                            <h3 class="mb-2">Crea un account</h3>
                            <p class="text-muted">Compila il form per registrarti</p>
                        </div>

                        <form>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="nome" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="nome" placeholder="Il tuo nome" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="cognome" class="form-label">Cognome</label>
                                    <input type="text" class="form-control" id="cognome" placeholder="Il tuo cognome" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="nome@esempio.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" placeholder="Crea una password" required>
                                <small class="text-muted">La password deve contenere almeno 8 caratteri, una lettera maiuscola e un numero</small>
                            </div>
                            <div class="mb-4">
                                <label for="conferma-password" class="form-label">Conferma Password</label>
                                <input type="password" class="form-control" id="conferma-password" placeholder="Conferma la tua password" required>
                            </div>
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="privacy" required>
                                <label class="form-check-label" for="privacy">Ho letto e accetto la <a href="#" class="chocolate-link">Privacy Policy</a> e i <a href="#" class="chocolate-link">Termini di Servizio</a></label>
                            </div>
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="newsletter">
                                <label class="form-check-label" for="newsletter">Desidero ricevere newsletter e offerte speciali</label>
                            </div>
                            <button type="submit" class="btn btn-chocolate w-100 py-2 mb-4">Registrati ora</button>

                            <div class="text-center">
                                <p class="mt-3">Hai già un account? <a href="login.php" class="chocolate-link">Accedi</a></p>
                            </div>

                            <!-- Tasto alternativo per tornare alla home -->
                            <div class="text-center mt-4">
                                <a href="../index.php" class="btn btn-outline-chocolate">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door me-2" viewBox="0 0 16 16">
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