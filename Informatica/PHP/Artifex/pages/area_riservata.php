<?php
global $db;
require_once '../conf_DB/primodb.php';
session_start();

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user = getCurrentUser($db);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Area Riservata</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .user-info { background-color: #f4f4f4; padding: 20px; margin: 20px 0; }
        .logout { color: red; text-decoration: none; }
        .eventi-list { margin-top: 20px; }
    </style>
</head>
<body>
<h2>Benvenuto, <?php echo htmlspecialchars($user['username']); ?>!</h2>

<div class="user-info">
    <h3>Le tue informazioni:</h3>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
</div>

<div class="statistiche">
    <h3>Statistiche:</h3>
    <p>Visite disponibili: <?php echo getTotaleVisite($db); ?></p>
    <p>Guide disponibili: <?php echo getTotaleGuide($db); ?></p>
    <p>Turisti registrati: <?php echo getTotaleTuristi($db); ?></p>
</div>

<div class="eventi-list">
    <h3>Prossimi eventi:</h3>
    <?php
    $eventi = getProssimiEventi($db);
    if ($eventi && count($eventi) > 0): ?>
        <ul>
            <?php foreach ($eventi as $evento): ?>
                <li>
                    <strong><?php echo htmlspecialchars($evento['titolo']); ?></strong> -
                    <?php echo htmlspecialchars($evento['luogo']); ?> -
                    <?php echo date('d/m/Y H:i', strtotime($evento['ora_inizio'])); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nessun evento in programma</p>
    <?php endif; ?>
</div>

<a href="logout.php" class="logout">Logout</a>
</body>
</html>