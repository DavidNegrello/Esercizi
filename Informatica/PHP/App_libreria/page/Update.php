<?php
// Includere la connessione al database e il file delle funzioni
require '../conf_DB/operazioni.php';
include '../header.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ottieni i valori dal modulo
    $titolo = $_POST['titolo'];
    $autore = $_POST['autore'];
    $prezzo = $_POST['prezzo'];

    // Chiamare la funzione per aggiornare il prezzo
    if (updateBookPrice($titolo, $autore, $prezzo)) {
        $message = "Book price updated successfully!";
    } else {
        $message = "Error updating the book price. Please check if the book exists.";
    }
}
?>


<body>
<nav class="menu">
    <a href="../Crud.php">Home</a>
    <a href="../page/Create.php">Create</a>
    <a href="../page/Read.php">View Books</a>
    <a href="#">Update Price</a>
    <a href="../page/Delete.php">Remove Book</a>
</nav>

<!-- Sezione principale con il form di aggiornamento -->
<div class="container">
    <h1>Update Book Price</h1>
    <p>Enter the title and author of the book to update its price.</p>

    <!-- Mostra il messaggio di successo o errore -->
    <?php if ($message != ''): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Form per l'aggiornamento del prezzo -->
    <form action="Update.php" method="POST">
        <div class="form-group">
            <label for="titolo">Title</label>
            <input type="text" id="titolo" name="titolo" required placeholder="Enter the book title">
        </div>

        <div class="form-group">
            <label for="autore">Author</label>
            <input type="text" id="autore" name="autore" required placeholder="Enter the author's name">
        </div>

        <div class="form-group">
            <label for="prezzo">Price (€)</label>
            <input type="number" id="prezzo" name="prezzo" step="0.01" required placeholder="Enter the new price">
        </div>

        <button type="submit" class="submit-btn">Update Price</button>
    </form>
</div>
<?php
include '../footer.php';
?>