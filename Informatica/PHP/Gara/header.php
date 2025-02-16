<?php
function percorso()
{
    // Ottieni il nome del file corrente
    $currentPage = basename($_SERVER['PHP_SELF']);

    // Verifica se la pagina è dentro la cartella "page"
    if (str_contains($_SERVER['PHP_SELF'], '/page/')) {
        // Se la pagina è dentro la cartella "page", usa il percorso relativo dal livello superiore
        return '../css/home.css';
    } else {
        // Se non è dentro la cartella "page", usa il percorso assoluto dal root del server
        return 'css/home.css';  // Percorso relativo dalla root del server web
    }
}
?>


<!-- header.php -->
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?= percorso(); ?>">
    <title>Home - Gestione Biblioteca</title>
</head>
