<?php

require 'DB_class.php';   // funzione per la configurazione
$config = require 'conf.php'; // così non scriviamo tutto dentro
$db = DB_class::getDB($config);   // ora si trova dentro la funzione


//============READ===========
function ReadSovrani()
{
    global $db;

    $query = "SELECT * FROM Sovrani ORDER BY inizio_regno ASC";
    try {
        $stm = $db->prepare($query);
        $stm->execute();
        while ($sovrano = $stm->fetch(PDO::FETCH_OBJ)) {
            yield $sovrano; // Usa yield per restituire i risultati uno alla volta
        }
        $stm->closeCursor();
    } catch (Exception $eccezione) {
        logError($eccezione);
    }
}




//================CREATE========================


// Funzione per inserire un sovrano nel database
function insertSovrano($nome, $inizio_regno, $fine_regno, $immagine, $predecessore, $successore) {
    global $db;

    // Query di inserimento
    $query = "INSERT INTO Sovrani (nome, inizio_regno, fine_regno, immagine, predecessore, successore)
              VALUES (:nome, :inizio_regno, :fine_regno, :immagine, :predecessore, :successore)";

    try {
        $stm = $db->prepare($query);

        // Bind dei valori dai parametri
        $stm->bindValue(":nome", $nome);
        $stm->bindValue(":inizio_regno", $inizio_regno);
        $stm->bindValue(":fine_regno", $fine_regno);
        $stm->bindValue(":immagine", $immagine);
        $stm->bindValue(":predecessore", $predecessore, PDO::PARAM_STR);
        $stm->bindValue(":successore", $successore, PDO::PARAM_STR);

        // Esegui la query
        if ($stm->execute()) {
            return true; // Successo
        } else {
            throw new PDOException("Errore nell'esecuzione della query.");
        }
    } catch (Exception $e) {
        logError($e);
        return false; // Errore
    }
}








//=============FUNZIONE LOG===============
function logError(Exception $exception): void
{
    echo "Errore nel database";
    error_log($exception->getMessage() . '***' . date('Y-m-d:i:s') . "\n", message_type: 3, destination: '../log/dberror.log');
}