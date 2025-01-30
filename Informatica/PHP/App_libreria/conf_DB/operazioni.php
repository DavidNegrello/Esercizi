<?php

require 'DB_class.php';   // funzione per la configurazione
$config = require 'conf.php'; // così non scriviamo tutto dentro
$db = DB_class::getDB($config);   // ora si trova dentro la funzione

//============READ===========
function Read()
{
    global $db;

    $query = "SELECT * FROM libri";
    try {
        $stm = $db->prepare($query);
        $stm->execute();
        $libri = $stm->fetchAll(PDO::FETCH_OBJ);
        $stm->closeCursor();
        return $libri; // Ritorna i dati invece di stamparli
    } catch (Exception $eccezione) {
        logError($eccezione);
        return [];
    }
}






//=================READ complessa============
/*
$query="select nome,media from studenti where nome=:name";

try {
    $stm=$db->prepare($query);
    $stm->bindValue(":name","Azzurra");
    $stm->execute();
    while ($student=$stm->fetch()){
        echo "nome: ".$student->nome."<br>";
        echo "media: ".$student->media."<br>";
    }
    $stm->closeCursor();
}catch (Exception $eccezzione){
    echo "<br>";
    logError($eccezzione);
}

*/


//================CREATE========================


// Funzione per inserire un libro nel database
function insertBook($titolo, $autore, $genere, $prezzo, $anno_pubblicazione) {
    // Query di inserimento
    $query = "INSERT INTO libri (titolo, autore, genere, prezzo, anno_pubblicazione)
              VALUES (:titolo, :autore, :genere, :prezzo, :anno_pubblicazione)";
    global $db;
    try {
        $stm = $db->prepare($query);
        // Bind dei valori dai campi del form
        $stm->bindValue(":titolo", $titolo);
        $stm->bindValue(":autore", $autore);
        $stm->bindValue(":genere", $genere);
        $stm->bindValue(":prezzo", $prezzo);
        $stm->bindValue(":anno_pubblicazione", $anno_pubblicazione);

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





//=======================UPDATE=========================
function updateBookPrice($titolo, $autore, $prezzo) {
    // Query di aggiornamento solo per il prezzo
    $query = "UPDATE libri SET prezzo = :prezzo WHERE titolo = :titolo AND autore = :autore";
    global $db;

    try {
        $stm = $db->prepare($query);
        // Bind dei valori dai campi del form
        $stm->bindValue(":titolo", $titolo);
        $stm->bindValue(":autore", $autore);
        $stm->bindValue(":prezzo", $prezzo);

        // Esegui la query
        if ($stm->execute()) {
            return true; // Successo
        } else {
            throw new PDOException("Errore nell'esecuzione della query.");
        }
    } catch (Exception $e) {
        return false; // Errore
    }
}



//=======================DELETE=========================
function deleteBook($titolo, $autore) {
    // Query di eliminazione che usa titolo e autore
    $query = "DELETE FROM libri WHERE titolo = :titolo AND autore = :autore";
    global $db;

    try {
        $stm = $db->prepare($query);
        // Bind dei valori titolo e autore
        $stm->bindValue(":titolo", $titolo);
        $stm->bindValue(":autore", $autore);

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