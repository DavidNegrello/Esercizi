<?php
global $currentUser, $db;
require_once '../conf_DB/primodb.php';
session_start();
// Ottieni l'utente corrente se loggato
$currentUser = isLoggedIn() ? getCurrentUser($db) : null;
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$evento_id = intval($_GET['id']);

try {
    // Recupera i dettagli completi dell'evento
    $sql = "SELECT e.*, v.titolo, v.durata_media, v.luogo, 
                   g.nome AS guida_nome, g.cognome AS guida_cognome,
                   GROUP_CONCAT(CONCAT(lg.lingua, ' (', lg.livello_competenza, ')') SEPARATOR ', ') AS lingue_guida
            FROM eventi e
            JOIN visite v ON e.id_visita = v.id
            JOIN guide g ON e.id_guida = g.id
            LEFT JOIN lingue_guide lg ON g.id = lg.id_guida
            WHERE e.id = :id
            GROUP BY e.id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $evento_id, PDO::PARAM_INT);
    $stmt->execute();
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        header("Location: ../index.php");
        exit();
    }

    // Verifica disponibilità posti
    $sql_posti = "SELECT COUNT(*) AS prenotati FROM prenotazioni WHERE id_evento = :id_evento";
    $stmt_posti = $db->prepare($sql_posti);
    $stmt_posti->bindParam(':id_evento', $evento_id, PDO::PARAM_INT);
    $stmt_posti->execute();
    $prenotati = $stmt_posti->fetch(PDO::FETCH_ASSOC)['prenotati'];
    $posti_disponibili = $evento['num_massimo_partecipanti'] - $prenotati;

} catch (PDOException $e) {
    die("Errore nel recupero dell'evento: " . $e->getMessage());
}

// Gestione generazione PDF
if (isset($_POST['genera_pdf'])) {
    require ('../vendor/tecnickcom/tcpdf/tcpdf.php');

    // Verifica prenotazione
    try {
        $sql_prenotazione = "SELECT * FROM prenotazioni 
                            WHERE id_evento = :id_evento AND id_utente = :id_utente";
        $stmt_pren = $db->prepare($sql_prenotazione);
        $stmt_pren->bindParam(':id_evento', $evento_id, PDO::PARAM_INT);
        $stmt_pren->bindParam(':id_utente', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt_pren->execute();
        $prenotazione = $stmt_pren->fetch(PDO::FETCH_ASSOC);

        if (!$prenotazione) {
            $_SESSION['error'] = "Devi prima prenotare l'evento per generare il biglietto";
            header("Location: evento.php?id=".$evento_id);
            exit();
        }
    } catch (PDOException $e) {
        die("Errore verifica prenotazione: " . $e->getMessage());
    }


    $pdf = new TCPDF();
    $pdf->AddPage();

    // Intestazione colorata
    $pdf->SetFillColor(27, 90, 190); // Blu Artifex
    $pdf->Rect(0, 0, 210, 30, 'F');
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Helvetica', 'B', 20);
    $pdf->Cell(0, 20, 'ARTIFEX TURISMO', 0, 1, 'C');

    // Contenuto
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Helvetica', '', 14);
    $pdf->Ln(15);

    $pdf->Cell(0, 10, "Biglietto per: {$evento['titolo']}", 0, 1, 'L');
    $pdf->Cell(0, 10, "Data e ora: ".date('d/m/Y H:i', strtotime($evento['ora_inizio'])), 0, 1, 'L');
    $pdf->Cell(0, 10, "Luogo: {$evento['luogo']}", 0, 1, 'L');
    $pdf->Ln(5);

    $pdf->Cell(0, 10, "Nome: {$_SESSION['username']}", 0, 1, 'L');
    $pdf->Cell(0, 10, "Codice: ART-{$prenotazione['id']}", 0, 1, 'L');
    $pdf->Ln(10);

    // QR Code con i dati
    $testo_qr = "Artifex Turismo\nEvento: {$evento['titolo']}\nData: ".date('d/m/Y H:i', strtotime($evento['ora_inizio']))."\nPartecipante: {$_SESSION['username']}\nCodice: ART-{$prenotazione['id']}";
    $pdf->write2DBarcode($testo_qr, 'QRCODE,L', 140, 70, 50, 50);

    // Linea divisoria
    $pdf->Line(10, 130, 200, 130);

    // Footer
    $pdf->SetFont('Helvetica', 'I', 10);
    $pdf->Cell(0, 10, "Biglietto generato il: ".date('d/m/Y H:i'), 0, 1, 'C');

    // Output
    $pdf->Output("biglietto_{$evento_id}.pdf", 'I');

    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($evento['titolo']); ?> - Artifex Turismo</title>
    <link rel="stylesheet" href="../styles/evento.css">
    <link rel="stylesheet" href="../styles/home.css">
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

<div class="container">
    <div class="evento-container">
        <div class="evento-header">
            <h1><?php echo htmlspecialchars($evento['titolo']); ?></h1>
            <p class="evento-meta">
                <?php echo date('d/m/Y H:i', strtotime($evento['ora_inizio'])); ?> -
                <?php echo htmlspecialchars($evento['luogo']); ?>
            </p>
        </div>

        <div class="posti-disponibili">
            Posti disponibili: <?php echo $posti_disponibili; ?> / <?php echo $evento['num_massimo_partecipanti']; ?>
        </div>

        <div class="evento-details">
            <div class="detail-box">
                <h3>Dettagli Visita</h3>
                <p><strong>Durata:</strong> <?php echo htmlspecialchars($evento['durata_media']); ?> minuti</p>
                <p><strong>Luogo:</strong> <?php echo htmlspecialchars($evento['luogo']); ?></p>
                <p><strong>Prezzo:</strong> € <?php echo number_format($evento['prezzo'], 2, ',', '.'); ?></p>
                <p><strong>Partecipanti:</strong> min <?php echo $evento['num_minimo_partecipanti']; ?> -
                    max <?php echo $evento['num_massimo_partecipanti']; ?></p>
            </div>

            <div class="detail-box">
                <h3>Informazioni Guida</h3>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($evento['guida_cognome'] . ' ' . $evento['guida_nome']); ?></p>
                <?php if (!empty($evento['lingue_guida'])): ?>
                    <p><strong>Lingue:</strong> <?php echo htmlspecialchars($evento['lingue_guida']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isLoggedIn()): ?>
            <div class="evento-actions">
                <?php if ($posti_disponibili > 0): ?>
                    <a href="prenota.php?id=<?php echo $evento_id; ?>" class="btn btn-prenota">Prenota Ora</a>
                <?php else: ?>
                    <button class="btn" style="background:#ccc;" disabled>Esaurito</button>
                <?php endif; ?>

                <form method="post" style="display:inline;">
                    <button type="submit" name="genera_pdf" class="btn btn-pdf">Genera Biglietto PDF</button>
                </form>
            </div>

            <div class="biglietto-preview">
                <h3>Anteprima Biglietto</h3>
                <p><strong>Evento:</strong> <?php echo htmlspecialchars($evento['titolo']); ?></p>
                <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($evento['ora_inizio'])); ?></p>
                <p><strong>Partecipante:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p><small>Il biglietto completo sarà disponibile dopo la prenotazione</small></p>
            </div>
        <?php else: ?>
            <div class="alert">
                <p>Devi effettuare il <a href="login.php?redirect=evento.php?id=<?php echo $evento_id; ?>">login</a> per prenotare o generare il biglietto.</p>
            </div>
        <?php endif; ?>
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