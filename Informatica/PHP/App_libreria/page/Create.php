<?php
// Includere la connessione al database e il file delle funzioni
require '../conf_DB/operazioni.php';

include '../header.php';

// Variabili per i messaggi
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ottieni i valori dal modulo
    $titolo = $_POST['titolo'];
    $autore = $_POST['autore'];
    $genere = $_POST['genere'];
    $prezzo = $_POST['prezzo'];
    $anno_pubblicazione = $_POST['anno_pubblicazione'];

    // Chiamare la funzione per inserire il libro nel database
    if (insertBook($titolo, $autore, $genere, $prezzo, $anno_pubblicazione)) {
        $message = "Book added successfully!";
    } else {
        $message = "Error adding the book!";
    }
}
?>

<body>
<nav class="menu">
    <a href="../Crud.php">Home</a>
    <a href="#">Create</a>
    <a href="../page/Read.php">View Books</a>
    <a href="../page/Update.php">Update Price</a>
    <a href="../page/Delete.php">Remove Book</a>
</nav>

<!-- Sezione principale con il form di inserimento -->
<div class="container">
    <h1>Create New Book</h1>
    <p>Please fill in the details of the book you want to add to the library.</p>

    <?php if ($message != ''): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Form per l'inserimento del libro -->
    <form action="Create.php" method="POST">
        <div class="form-group">
            <label for="titolo">Title</label>
            <input type="text" id="titolo" name="titolo" required placeholder="Enter the book title">
        </div>

        <div class="form-group">
            <label for="autore">Author</label>
            <input type="text" id="autore" name="autore" required placeholder="Enter the author's name">
        </div>

        <div class="form-group">
            <label for="genere">Genre</label>
            <input type="text" id="genere" name="genere" required placeholder="Enter the genre">
        </div>

        <div class="form-group">
            <label for="prezzo">Price (€)</label>
            <input type="number" id="prezzo" name="prezzo" step="0.01" required placeholder="Enter the price">
        </div>

        <div class="form-group">
            <label for="anno_pubblicazione">Publication Year</label>
            <input type="number" id="anno_pubblicazione" name="anno_pubblicazione" required placeholder="Enter the publication year">
        </div>

        <button type="submit" class="submit-btn">Submit</button>
    </form>
</div>

<?php
include '../footer.php';
?>








