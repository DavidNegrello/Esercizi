<?php
require '../conf_DB/operazioni.php';
include '../header.php';
$libri = Read(); // Ottiene i dati
?>


<body>
<nav class="menu">
    <a href="../Crud.php">Home</a>
    <a href="../page/Create.php">Create</a>
    <a href="#">View Books</a>
    <a href="../page/Update.php">Update Price</a>
    <a href="../page/Delete.php">Remove Book</a>
</nav>


<div class="container">
    <h1>Elenco dei Libri</h1>

    <table class="book-table">
        <thead>
        <tr>
            <th>Titolo</th>
            <th>Autore</th>
            <th>Genere</th>
            <th>Prezzo (€)</th>
            <th>Anno di Pubblicazione</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($libri as $libro): ?>
            <tr>
                <td><?= $libro->titolo ?></td>
                <td><?= $libro->autore ?></td>
                <td><?= $libro->genere ?></td>
                <td>€<?= number_format($libro->prezzo, 2) ?></td>
                <td><?= $libro->anno_pubblicazione ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
include '../footer.php';
?>