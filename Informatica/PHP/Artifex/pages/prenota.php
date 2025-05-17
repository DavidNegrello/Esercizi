<?php
global $db, $currentUser;
require_once '../conf_DB/primodb.php';
session_start();
$currentUser = isLoggedIn() ? getCurrentUser($db) : null;
// Verifica login
if (!isLoggedIn()) {
    header("Location: login.php?redirect=prenota.php" . (isset($_GET['id']) ? '?id='.$_GET['id'] : ''));
    exit();
}

// Verifica ID evento
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$evento_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

try {
    // Recupera dettagli evento
    $sql_evento = "SELECT e.*, v.titolo, v.luogo, 
                          g.nome AS guida_nome, g.cognome AS guida_cognome,
                          COUNT(p.id) AS prenotati
                   FROM eventi e
                   JOIN visite v ON e.id_visita = v.id
                   JOIN guide g ON e.id_guida = g.id
                   LEFT JOIN prenotazioni p ON e.id = p.id_evento
                   WHERE e.id = :id
                   GROUP BY e.id";

    $stmt_evento = $db->prepare($sql_evento);
    $stmt_evento->bindParam(':id', $evento_id, PDO::PARAM_INT);
    $stmt_evento->execute();
    $evento = $stmt_evento->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        header("Location: ../index.php");
        exit();
    }

    // Calcola posti disponibili
    $posti_disponibili = $evento['num_massimo_partecipanti'] - $evento['prenotati'];

    // Gestione prenotazione
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['conferma_prenotazione'])) {
        if ($posti_disponibili <= 0) {
            $_SESSION['error'] = "Non ci sono più posti disponibili per questo evento!";
            header("Location: evento.php?id=".$evento_id);
            exit();
        }

        // Verifica se l'utente ha già prenotato
        $sql_check = "SELECT id FROM prenotazioni WHERE id_evento = :id_evento AND id_utente = :id_utente";
        $stmt_check = $db->prepare($sql_check);
        $stmt_check->bindParam(':id_evento', $evento_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':id_utente', $user_id, PDO::PARAM_INT);
        $stmt_check->execute();

        if ($stmt_check->fetch()) {
            $_SESSION['error'] = "Hai già prenotato questo evento!";
            header("Location: evento.php?id=".$evento_id);
            exit();
        }

        // Crea la prenotazione
        $sql_insert = "INSERT INTO prenotazioni (id_evento, id_utente) VALUES (:id_evento, :id_utente)";
        $stmt_insert = $db->prepare($sql_insert);
        $stmt_insert->bindParam(':id_evento', $evento_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':id_utente', $user_id, PDO::PARAM_INT);

        if ($stmt_insert->execute()) {
            $_SESSION['success'] = "Prenotazione confermata! Puoi ora generare il tuo biglietto.";
            header("Location: evento.php?id=".$evento_id);
            exit();
        } else {
            $_SESSION['error'] = "Errore durante la prenotazione. Riprova più tardi.";
            header("Location: evento.php?id=".$evento_id);
            exit();
        }
    }

} catch (PDOException $e) {
    die("Errore database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prenota Evento - Artifex Turismo</title>
    <link rel="stylesheet" href="../styles/prenotazione.css">
    <link rel="stylesheet" href="../styles/home.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container1 {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a5276;
            text-align: center;
            margin-bottom: 30px;
        }
        .evento-info {
            background: #eaf2f8;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #2874a6;
        }
        .detail-value {
            flex: 1;
        }
        .posti-disponibili {
            text-align: center;
            font-size: 1.2em;
            padding: 10px;
            background: #d5f5e3;
            border-radius: 5px;
            margin: 20px 0;
        }
        .prenotazione-form {
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #2e86c1;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #1a5276;
        }
        .btn-annulla {
            background: #e74c3c;
            margin-left: 15px;
        }
        .btn-annulla:hover {
            background: #c0392b;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-error {
            background: #fadbd8;
            color: #c0392b;
        }
    </style>
</head>
<body>
<nav>
    <div class="container">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="insert.php">Gestione</a></li>
            <li><a href="visite.php">Visite</a></li>
            <li><a href="guide.php">Guide</a></li>
            <li><a href="eventi.php">Eventi</a></li>
        </ul>

        <div class="user-menu">
            <?php if (isLoggedIn()): ?>
                <span class="user-greeting">Ciao, <?php echo htmlspecialchars($currentUser['username']); ?></span>
                <div class="user-actions">
                    <a href="area_riservata.php" class="btn secondary">Area Riservata</a>
                    <a href="logout.php" class="btn">Logout</a>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="login.php" class="btn secondary">Login</a>
                    <a href="registrazione.php" class="btn">Registrati</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container1">
    <h1>Conferma Prenotazione</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="evento-info">
        <h2><?php echo htmlspecialchars($evento['titolo']); ?></h2>

        <div class="detail-row">
            <div class="detail-label">Data e Ora:</div>
            <div class="detail-value"><?php echo date('d/m/Y H:i', strtotime($evento['ora_inizio'])); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Luogo:</div>
            <div class="detail-value"><?php echo htmlspecialchars($evento['luogo']); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Guida:</div>
            <div class="detail-value"><?php echo htmlspecialchars($evento['guida_cognome'] . ' ' . $evento['guida_nome']); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Prezzo:</div>
            <div class="detail-value">€ <?php echo number_format($evento['prezzo'], 2, ',', '.'); ?></div>
        </div>
    </div>

    <div class="posti-disponibili">
        Posti disponibili: <?php echo $posti_disponibili; ?> su <?php echo $evento['num_massimo_partecipanti']; ?>
    </div>

    <div class="prenotazione-form">
        <form method="post">
            <p>Confermi di voler prenotare questo evento?</p>

            <?php if ($posti_disponibili > 0): ?>
                <button type="submit" name="conferma_prenotazione" class="btn">Conferma Prenotazione</button>
                <a href="eventi.php?id=<?php echo $evento_id; ?>" class="btn btn-annulla">Annulla</a>
            <?php else: ?>
                <p class="alert alert-error">Non ci sono più posti disponibili per questo evento.</p>
                <a href="eventi.php?id=<?php echo $evento_id; ?>" class="btn btn-annulla">Torna all'evento</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Artifex Turismo. Tutti i diritti riservati.</p>
        <p>Sistema di gestione del turismo culturale</p>
    </div>
</footer>
</body>
</html>