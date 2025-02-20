<?php
require '../conf_DB/operazioni.php';
include '../header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $inizio_regno = $_POST['inizio_regno'];
    $fine_regno = $_POST['fine_regno'];
    $immagine = $_POST['immagine'];
    $predecessore = $_POST['predecessore'];
    $successore = $_POST['successore'];

    if (insertSovrano($nome, $inizio_regno, $fine_regno, $immagine, $predecessore, $successore)) {
        $message = "Sovrano aggiunto con successo!";
    } else {
        $message = "Errore nell'aggiungere il sovrano!";
    }
}
?>

<body>
<nav class="menu">
    <a href="../Crud.php">Home</a>
    <a href="#">Crea</a>
    <a href="../page/Read.php">Visualizza Sovrani</a>
</nav>

<div class="container">
    <h1>Inserisci Nuovo Sovrano</h1>
    <p>Compila i dettagli del sovrano che vuoi aggiungere alla dinastia.</p>

    <?php if ($message != ''): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="Create.php" method="POST">
        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required placeholder="Inserisci il nome del sovrano">
        </div>

        <div class="form-group">
            <label for="inizio_regno">Data Inizio Regno</label>
            <input type="date" id="inizio_regno" name="inizio_regno" required>
        </div>

        <div class="form-group">
            <label for="fine_regno">Data Fine Regno</label>
            <input type="date" id="fine_regno" name="fine_regno" placeholder="Data di fine del regno (opzionale)">
        </div>

        <div class="form-group">
            <label for="immagine">Immagine</label>
            <input type="text" id="immagine" name="immagine" placeholder="Link all'immagine del sovrano">
        </div>

        <div class="form-group">
            <label for="predecessore">Predecessore</label>
            <input type="text" id="predecessore" name="predecessore" placeholder="Nome del predecessore">
        </div>

        <div class="form-group">
            <label for="successore">Successore</label>
            <input type="text" id="successore" name="successore" placeholder="Nome del successore">
        </div>

        <button type="submit" class="submit-btn">Aggiungi Sovrano</button>
    </form>
</div>

<?php
include '../footer.php';
?>
