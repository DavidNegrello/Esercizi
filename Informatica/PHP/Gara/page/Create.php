<?php
// Includere la connessione al database e il file delle funzioni
require '../conf_DB/operazioni.php';
include '../header.php';

// Variabili per i messaggi
$message = '';
$pilotiEsistenti = getPiloti(); // Funzione per ottenere i piloti esistenti
$caseEsistenti = getCaseAutomobilistiche(); // Funzione per ottenere le case automobilistiche
$gareEsistenti = getGare(); // Funzione per ottenere le gare esistenti

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Variabili per i dati da inserire
    $nomeGara = $_POST['nome_gara'] ?? null;
    $dataGara = $_POST['data_gara'] ?? null;
    $circuito = $_POST['circuito'] ?? null;
    $nomePilota = $_POST['nome_pilota'] ?? null;
    $cognomePilota = $_POST['cognome_pilota'] ?? null;
    $idCasaPilota = $_POST['id_casa_pilota'] ?? null;
    $idGaraRisultato = $_POST['id_gara_risultato'] ?? null;
    $idPilotaRisultato = $_POST['id_pilota_risultato'] ?? null;
    $posizione = $_POST['posizione'] ?? null;
    $punti = $_POST['punti'] ?? null;
    $nomeCasa = $_POST['nome_casa'] ?? null;
    $coloreLivrea = $_POST['colore_livrea'] ?? null;

    // Inserimento Casa Automobilistica
    if (isset($nomeCasa, $coloreLivrea)) {
        if (insertCasaAutomobilistica($nomeCasa, $coloreLivrea)) {
            $message = "Car manufacturer added successfully!";
            $caseEsistenti = getCaseAutomobilistiche(); // Ricarica le case automobilistiche
        } else {
            $message = "Error adding the car manufacturer!";
        }
    }

    // Inserimento Pilota
    if (isset($nomePilota, $cognomePilota, $idCasaPilota)) {
        if (insertPilota($nomePilota, $cognomePilota, $idCasaPilota)) {
            $message = "Driver added successfully!";
            // Dopo aver aggiunto il pilota, possiamo procedere alla gara
        } else {
            $message = "Error adding the driver!";
        }
    }

    // Inserimento Gara
    if (isset($nomeGara, $dataGara, $circuito)) {
        if (insertGara($nomeGara, $dataGara, $circuito)) {
            $message = "Race added successfully!";
            $gareEsistenti = getGare(); // Ricarica le gare
        } else {
            $message = "Error adding the race!";
        }
    }

    // Inserimento Risultato Gara
    if (isset($idGaraRisultato, $idPilotaRisultato, $posizione, $punti)) {
        // Verifica se il pilota esiste prima di aggiungere un risultato
        if (in_array($idPilotaRisultato, array_column($pilotiEsistenti, 'ID_Pilota'))) {
            if (insertRisultato($idGaraRisultato, $idPilotaRisultato, $posizione, $punti)) {
                $message = "Race result added successfully!";
            } else {
                $message = "Error adding the race result!";
            }
        } else {
            $message = "Driver does not exist!";
        }
    }
}
?>

<body>
<nav class="menu">
    <a href="../Crud.php">Home</a>
    <a href="#">Create</a>
    <a href="../page/Read.php">View Results</a>
    <a href="../page/Update.php">Update Result</a>
</nav>

<!-- Sezione principale con i form di inserimento -->
<div class="container">
    <h1>Insert Data</h1>
    <p>Please fill in the details to add a new race, driver, or race result.</p>

    <!-- Messaggio di successo o errore -->
    <?php if ($message != ''): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Form Casa Automobilistica -->
    <h2>Insert Car Manufacturer</h2>
    <form action="Create.php" method="POST">
        <div class="form-group">
            <label for="nome_casa">Car Manufacturer Name</label>
            <input type="text" id="nome_casa" name="nome_casa" required placeholder="Enter the car manufacturer's name">
        </div>
        <div class="form-group">
            <label for="colore_livrea">Livery Color</label>
            <input type="text" id="colore_livrea" name="colore_livrea" required placeholder="Enter the car manufacturer's livery color">
        </div>
        <button type="submit" class="submit-btn">Submit</button>
    </form>

    <!-- Form Pilota -->
    <?php if ($message === "Car manufacturer added successfully!"): ?>
        <h2>Insert Driver</h2>
        <form action="Create.php" method="POST">
            <div class="form-group">
                <label for="nome_pilota">Driver's First Name</label>
                <input type="text" id="nome_pilota" name="nome_pilota" required placeholder="Enter the driver's first name">
            </div>
            <div class="form-group">
                <label for="cognome_pilota">Driver's Last Name</label>
                <input type="text" id="cognome_pilota" name="cognome_pilota" required placeholder="Enter the driver's last name">
            </div>
            <div class="form-group">
                <label for="id_casa_pilota">Car Manufacturer</label>
                <select id="id_casa_pilota" name="id_casa_pilota" required>
                    <?php foreach ($caseEsistenti as $casa): ?>
                        <option value="<?= $casa->ID_Casa ?>"><?= $casa->Nome ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="submit-btn">Submit</button>
        </form>
    <?php endif; ?>

    <!-- Form Gara -->
    <?php if ($message === "Driver added successfully!"): ?>
        <h2>Insert Race</h2>
        <form action="Create.php" method="POST">
            <div class="form-group">
                <label for="nome_gara">Race Name</label>
                <input type="text" id="nome_gara" name="nome_gara" required placeholder="Enter the race name">
            </div>
            <div class="form-group">
                <label for="data_gara">Race Date</label>
                <input type="date" id="data_gara" name="data_gara" required placeholder="Enter the race date">
            </div>
            <div class="form-group">
                <label for="circuito">Circuit</label>
                <input type="text" id="circuito" name="circuito" required placeholder="Enter the circuit">
            </div>
            <button type="submit" class="submit-btn">Submit</button>
        </form>
    <?php endif; ?>

    <!-- Form Risultato Gara -->
    <?php if ($message === "Race added successfully!"): ?>
        <h2>Insert Race Result</h2>
        <form action="Create.php" method="POST">
            <div class="form-group">
                <label for="id_gara_risultato">Race ID</label>
                <select id="id_gara_risultato" name="id_gara_risultato" required>
                    <?php foreach ($gareEsistenti as $gara): ?>
                        <option value="<?= $gara->ID_Gara ?>"><?= $gara->Nome ?> - <?= $gara->Data ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_pilota_risultato">Driver ID</label>
                <select id="id_pilota_risultato" name="id_pilota_risultato" required>
                    <?php foreach ($pilotiEsistenti as $pilota): ?>
                        <option value="<?= $pilota->ID_Pilota ?>"><?= $pilota->Nome ?> <?= $pilota->Cognome ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="posizione">Position</label>
                <input type="number" id="posizione" name="posizione" required placeholder="Enter the position (1st, 2nd, etc.)">
            </div>
            <div class="form-group">
                <label for="punti">Points</label>
                <input type="number" id="punti" name="punti" required placeholder="Enter the points awarded">
            </div>
            <button type="submit" class="submit-btn">Submit</button>
        </form>
    <?php endif; ?>
</div>

<?php
include '../footer.php';
?>
