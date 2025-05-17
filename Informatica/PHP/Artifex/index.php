<?php
// Includo il file di configurazione del database
global $db;
require_once 'conf_DB/primodb.php';
session_start();

// Recupero le statistiche generali
$totale_visite = getTotaleVisite($db);
$totale_guide = getTotaleGuide($db);
$totale_turisti = getTotaleTuristi($db);

// Recupero i prossimi eventi
$prossimi_eventi = getProssimiEventi($db, 5);

// Ottieni l'utente corrente se loggato
$currentUser = isLoggedIn() ? getCurrentUser($db) : null;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artifex Turismo - Home</title>
    <link rel="stylesheet" href="styles/home.css">
</head>
<body>
<header>
    <div class="container">
        <h1>Artifex Turismo</h1>
        <p>Scopri l'arte e la cultura con le nostre visite guidate</p>
    </div>
</header>

<nav>
    <div class="container">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="pages/insert.php">Gestione</a></li>
            <li><a href="pages/visite.php">Visite</a></li>
            <li><a href="pages/guide.php">Guide</a></li>
            <li><a href="pages/eventi.php">Eventi</a></li>
        </ul>

        <div class="user-menu">
            <?php if (isLoggedIn()): ?>
                <span class="user-greeting">Ciao, <?php echo htmlspecialchars($currentUser['username']); ?></span>
                <div class="user-actions">
                    <a href="pages/area_riservata.php" class="btn secondary">Area Riservata</a>
                    <a href="pages/logout.php" class="btn">Logout</a>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="pages/login.php" class="btn secondary">Login</a>
                    <a href="pages/registrazione.php" class="btn">Registrati</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
        <div class="alert success">
            Registrazione completata con successo! Effettua il login.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['login']) && $_GET['login'] == 'required'): ?>
        <div class="alert warning">
            Per accedere a questa pagina devi effettuare il login.
        </div>
    <?php endif; ?>

    <div class="dashboard">
        <div class="stat-card">
            <div class="label">Visite Disponibili</div>
            <div class="number"><?php echo $totale_visite; ?></div>
            <a href="pages/visite.php" class="btn secondary">Dettagli</a>
        </div>

        <div class="stat-card">
            <div class="label">Guide Professionali</div>
            <div class="number"><?php echo $totale_guide; ?></div>
            <a href="pages/guide.php" class="btn secondary">Dettagli</a>
        </div>

        <div class="stat-card">
            <div class="label">Turisti Registrati</div>
            <div class="number"><?php echo $totale_turisti; ?></div>
            <a href="pages/turisti.php" class="btn secondary">Dettagli</a>
        </div>
    </div>

    <div class="section">
        <h2>Prossimi Eventi</h2>
        <?php if (count($prossimi_eventi) > 0): ?>
            <table class="events-table">
                <thead>
                <tr>
                    <th>Visita</th>
                    <th>Guida</th>
                    <th>Data e Ora</th>
                    <th>Luogo</th>
                    <th>Prezzo</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($prossimi_eventi as $evento): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($evento['titolo']); ?></td>
                        <td><?php echo htmlspecialchars($evento['nome'] . ' ' . $evento['cognome']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($evento['ora_inizio'])); ?></td>
                        <td><?php echo htmlspecialchars($evento['luogo']); ?></td>
                        <td><?php echo number_format($evento['prezzo'], 2, ',', '.') . ' €'; ?></td>
                        <td>
                            <a href="pages/eventi.php?id=<?php echo $evento['id']; ?>" class="btn">Dettagli</a>
                            <?php if (isLoggedIn()): ?>
                                <a href="pages/prenota.php?id=<?php echo $evento['id']; ?>" class="btn primary">Prenota</a>
                            <?php else: ?>
                                <a href="pages/login.php?redirect=prenota.php?id=<?php echo $evento['id']; ?>" class="btn primary">Accedi per prenotare</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Non ci sono eventi programmati al momento.</p>
        <?php endif; ?>
    </div>

    <?php if (!isLoggedIn()): ?>
        <div class="cta-section">
            <h2>Unisciti alla nostra comunità</h2>
            <p>Registrati per prenotare eventi, salvare le tue visite preferite e molto altro</p>
            <div class="auth-actions">
                <a href="pages/registrazione.php" class="btn">Registrati</a>
                <a href="pages/login.php" class="btn secondary">Hai già un account? Accedi</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Artifex Turismo. Tutti i diritti riservati.</p>
        <p>Sistema di gestione del turismo culturale</p>
    </div>
</footer>
</body>
</html>