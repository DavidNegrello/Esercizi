<?php
require '../conf_DB/operazioni.php';
include '../header.php';

$sovrani = ReadSovrani(); // Ottiene i dati
?>

<body>
<nav class="menu">
    <a href="../Crud.php">Home</a>
    <a href="../page/Create.php">Create</a>
    <a href="#">View Sovereigns</a>
</nav>

<div class="container">
    <h1>Elenco dei Sovrani</h1>

    <table class="sovereign-table">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Inizio Regno</th>
            <th>Fine Regno</th>
            <th>Predecessore</th>
            <th>Successore</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($sovrani as $sovrano): ?>
            <tr>
                <td><?= $sovrano->nome ?></td>
                <td><?= $sovrano->inizio_regno ?></td>
                <td><?= $sovrano->fine_regno ?? 'In corso' ?></td>
                <td><?= $sovrano->predecessore ?? 'N/A' ?></td>
                <td><?= $sovrano->successore ?? 'N/A' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include '../footer.php';
?>
