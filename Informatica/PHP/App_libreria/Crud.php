<?php
include 'header.php';
?>

    <body>
    <!--============MENU=============-->
    <nav class="menu">
        <a href="page/Create.php">Aggiungi Libro</a>
        <a href="page/Read.php">Visualizza Libri</a>
        <a href="page/Update.php">Aggiorna Prezzo</a>
        <a href="page/Delete.php">Rimuovi Libro</a>
    </nav>
<!--===========CONTAINER==========-->
<div class="container">
    <h1>Benvenuto nella Gestione Biblioteca</h1>
    <p>Gestisci facilmente la tua biblioteca. Questo sistema ti permette di aggiungere, visualizzare, aggiornare e rimuovere libri dalla tua collezione.</p>

    <!-- Mostra le funzionalità principali disponibili -->
    <div class="features">
        <h2>Funzionalità Principali:</h2>
        <ul>
            <li><strong>Aggiungi Libro:</strong> Aggiungi un nuovo libro alla tua biblioteca.</li>
            <li><strong>Visualizza Libri:</strong> Visualizza un elenco di tutti i libri presenti nella tua biblioteca con i dettagli.</li>
            <li><strong>Aggiorna Prezzo:</strong> Aggiorna il prezzo di un libro nella tua collezione.</li>
            <li><strong>Rimuovi Libro:</strong> Rimuovi un libro dalla tua biblioteca quando non è più necessario.</li>
        </ul>
    </div>

    <!-- Opzionale: Aggiungi un'immagine decorativa o logo -->
    <div class="image">
        <img src="libreria.jpeg" alt="Biblioteca" style="max-width: 100%; height: auto; margin-top: 20px;">
    </div>


    </div>
    </body>

<?php
include 'footer.php';
?>