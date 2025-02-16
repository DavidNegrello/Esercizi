<?php
require '../conf_DB/operazioni.php';
include '../header.php';

$message = '';

// Controlla se è stato passato un ID pilota tramite GET
if (isset($_GET['pilota_id'])) {
    $pilota_id = $_GET['pilota_id'];

    // Ottieni i dati del pilota
    $pilota = getPilotaByID($pilota_id);

    // Verifica se il pilota esiste
    if ($pilota) {
        // Controlla se il modulo è stato inviato
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nuovo_punteggio = $_POST['punteggio'];

            // Funzione per aggiornare il punteggio
            if (updatePunteggioPilota($pilota_id, $nuovo_punteggio)) {
                $message = "Punteggio aggiornato con successo!";
            } else {
                $message = "Errore nell'aggiornamento del punteggio. Verifica se il pilota esiste.";
            }
        }
    } else {
        $message = "Pilota non trovato.";
    }
}

?>

<body>
<!-- Menu di navigazione -->
<nav class="menu">
    <a href="../Crud.php">Home</a>
    <a href="../page/Create.php">Aggiungi Pilota</a>
    <a href="../page/Update.php">Aggiorna Punteggio</a>
    <a href="../page/Delete.php">Rimuovi Pilota</a>
</nav>

<!-- Sezione principale con la lista dei piloti -->
<div class="container">
    <h1>Modifica Punteggio Pilota</h1>

    <!-- Mostra il messaggio di successo o errore -->
    <?php if ($message != ''): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Lista dei piloti -->
    <h2>Lista Piloti</h2>
    <?php
    // Ottieni la classifica dei piloti
    $piloti = getClassificaPiloti();
    if (!empty($piloti)) {
        echo "<table class='table table-bordered'>";
        echo "<tr><th>Posizione</th><th>Nome Pilota</th><th>Squadra</th><th>Punteggio</th><th>Modifica</th></tr>";
        foreach ($piloti as $index => $pilota) {
            echo "<tr>
                    <td>" . ($index + 1) . "</td>
                    <td>{$pilota->Nome} {$pilota->Cognome}</td>
                    <td>{$pilota->Nome_Casa}</td>
                    <td>{$pilota->Punti_Totali}</td>
                    <td><a href='Update.php?pilota_id={$pilota->ID_Pilota}'>Modifica</a></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nessun pilota trovato.</p>";
    }
    ?>

    <!-- Se il pilota è stato selezionato per la modifica, mostra il modulo di aggiornamento -->
    <?php if (isset($pilota)): ?>
        <h2>Aggiorna Punteggio per <?php echo "{$pilota->Nome} {$pilota->Cognome}"; ?></h2>
        <form action="Update.php?pilota_id=<?php echo $pilota->ID_Pilota; ?>" method="POST">
            <div class="form-group">
                <label for="punteggio">Punteggio Attuale</label>
                <input type="number" id="punteggio" name="punteggio" value="<?php echo $pilota->Punti_Totali; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Aggiorna Punteggio</button>
        </form>
    <?php endif; ?>

</div>

<?php include '../footer.php'; ?>
</body>
