<?php
// Includere la connessione al database e il file delle funzioni
require '../conf_DB/operazioni.php';
include '../header.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ottieni i valori dal modulo
    $titolo = $_POST['titolo'];
    $autore = $_POST['autore'];

    // Chiamare la funzione per eliminare il libro
    if (deleteBook($titolo, $autore)) {
        $message = "Book deleted successfully!";
    } else {
        $message = "Error deleting the book. Please check if the book exists.";
    }
}
?>


<body>
<nav class="menu">
    <a href="../Crud.php">Home</a>
    <a href="../page/Create.php">Create</a>
    <a href="../page/Read.php">View Books</a>
    <a href="../page/Update.php">Update Price</a>
    <a href="#">Remove Book</a>
</nav>

<!-- Sezione principale con il form di eliminazione -->
<div class="container">
    <h1>Delete Book</h1>
    <p>Enter the title and author of the book you want to delete.</p>

    <!-- Mostra il messaggio di successo o errore -->
    <?php if ($message != ''): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Form per l'eliminazione del libro -->
    <form action="Delete.php" method="POST">
        <div class="form-group">
            <label for="titolo">Title</label>
            <input type="text" id="titolo" name="titolo" required placeholder="Enter the book title">
        </div>

        <div class="form-group">
            <label for="autore">Author</label>
            <input type="text" id="autore" name="autore" required placeholder="Enter the author's name">
        </div>

        <button type="submit" class="submit-btn">Delete</button>
    </form>
</div>
<?php
include '../footer.php';
?>