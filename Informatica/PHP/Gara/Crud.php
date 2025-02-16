<?php
include 'header.php';
?>

<body>
<!--============MENU=============-->
<nav class="menu">
    <a href="page/Create.php">Aggiungi Gara</a>
    <a href="page/Read.php">Visualizza Risultati</a>
    <a href="page/Update.php">Aggiorna Classifica</a>
    <a href="page/Delete.php">Rimuovi Gara</a>
</nav>

<!--===========CONTAINER==========-->
<div class="container">
    <h1>Benvenuto nel Sistema di Gestione del Campionato</h1>
    <p>Gestisci facilmente il tuo campionato automobilistico. Questo sistema ti permette di aggiungere gare, visualizzare i risultati, aggiornare la classifica dei piloti e rimuovere gare completate.</p>

    <!-- Mostra le funzionalità principali disponibili -->
    <div class="features">
        <h2>Funzionalità Principali:</h2>
        <ul>
            <li><strong>Aggiungi Gara:</strong> Aggiungi una nuova gara al campionato, specificando la data e i piloti partecipanti.</li>
            <li><strong>Visualizza Risultati:</strong> Visualizza i risultati delle gare, inclusi i tempi più veloci e le posizioni dei piloti.</li>
            <li><strong>Aggiorna Classifica:</strong> Aggiorna la classifica dei piloti in base ai risultati delle gare.</li>
            <li><strong>Rimuovi Gara:</strong> Rimuovi una gara dal campionato quando è completata o non più necessaria.</li>
        </ul>
    </div>

    <!-- Sezione con una breve descrizione delle ultime novità o eventi -->
    <div class="news">
        <h2>Novità dal Campionato:</h2>
        <p>Scopri le ultime novità e gli aggiornamenti dal campionato, con informazioni sulle gare più recenti, nuovi piloti e risultati da non perdere!</p>
    </div>

</div>
</body>

<?php
include 'footer.php';
?>
