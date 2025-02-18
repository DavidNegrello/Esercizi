<?php
require '../conf_DB/operazioni.php';
include '../header.php';

// Verifica se è stato scelto un parametro tramite GET
$scelta = isset($_GET['scelta']) ? $_GET['scelta'] : 'piloti'; // Default è piloti
?>

<body>
<!--============MENU=============-->
<nav class="menu">
    <a href="../Crud.php">Home</a>
    <a href="../page/Create.php">Aggiungi Libro</a>
    <a href="../page/Read.php">Visualizza Libri</a>
    <a href="../page/Update.php">Aggiorna Prezzo</a>
</nav>
<div class="container">
    <h1>Visualizza Dati del Campionato</h1>
    <p>Scegli cosa visualizzare:</p>

    <!-- Selezione cosa visualizzare -->
    <form action="Read.php" method="GET">
        <div class="form-group">
            <label for="scelta">Cosa visualizzare:</label>
            <select name="scelta" id="scelta" class="form-control" required>
                <option value="piloti" <?= $scelta == 'piloti' ? 'selected' : ''; ?>>Piloti</option>
                <option value="gare" <?= $scelta == 'gare' ? 'selected' : ''; ?>>Gare</option> <!-- Aggiungi l'opzione per le gare -->
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Visualizza</button>
    </form>

    <!-- Sezione per visualizzare i Piloti -->
    <?php
    if ($scelta == 'piloti') {
        echo "<h2>🏎️ Lista Piloti</h2>";
        $piloti = getClassificaPiloti(); // Funzione per ottenere la classifica piloti
        if (!empty($piloti)) {
            echo "<table class='table table-bordered'>";
            echo "<tr><th>Posizione</th><th>Pilota</th><th>Squadra</th><th>Punti</th></tr>";
            $pos = 1;
            foreach ($piloti as $pilota) {
                echo "<tr>
                            <td>{$pos}</td>
                            <td>{$pilota->Nome} {$pilota->Cognome}</td>
                            <td>{$pilota->Nome_Casa}</td>
                            <td>{$pilota->Punti_Totali}</td>
                          </tr>";
                $pos++;
            }
            echo "</table>";
        } else {
            echo "<p>Non ci sono piloti registrati.</p>";
        }
    }

    // Sezione per visualizzare le Gare
    elseif ($scelta == 'gare') {
        echo "<h2>🏁 Lista Gare</h2>";
        $gare = getGare(); // Funzione per ottenere tutte le gare
        if (!empty($gare)) {
            echo "<table class='table table-bordered'>";
            echo "<tr><th>Gara</th><th>Data</th><th>Circuito</th></tr>";
            foreach ($gare as $gara) {
                echo "<tr>
                            <td>{$gara->Nome}</td>
                            <td>{$gara->Data}</td>
                            <td>{$gara->Circuito}</td>
                          </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Non ci sono gare disponibili.</p>";
        }
    }
    ?>
</div>
</body>



<?php
include '../footer.php';
?>
