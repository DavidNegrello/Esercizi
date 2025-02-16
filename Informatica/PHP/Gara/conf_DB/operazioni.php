<?php

require 'DB_class_gara.php';   // funzione per la configurazione
$config = require 'conf_auto.php'; // così non scriviamo tutto dentro
$db = DB_classGara::getDB($config);   // ora si trova dentro la funzione

//============READ===========

// Funzione per ottenere la lista delle case automobilistiche
function getCaseAutomobilistiche() {
    global $db;

    $query = "SELECT * FROM Casa_Automobilistica"; // Query per ottenere tutte le case automobilistiche

    try {
        $stm = $db->prepare($query);
        $stm->execute();
        $case = $stm->fetchAll(PDO::FETCH_OBJ); // Recupera i risultati come oggetti
        $stm->closeCursor();
        return $case; // Ritorna la lista delle case automobilistiche
    } catch (Exception $e) {
        logError($e); // Log dell'errore
        return []; // Ritorna un array vuoto in caso di errore
    }
}

// Funzione per ottenere la lista dei piloti
function getPiloti() {
    global $db;

    $query = "SELECT * FROM Pilota"; // Query per ottenere tutti i piloti

    try {
        $stm = $db->prepare($query);
        $stm->execute();
        $piloti = $stm->fetchAll(PDO::FETCH_OBJ); // Recupera i risultati come oggetti
        $stm->closeCursor();
        return $piloti; // Ritorna la lista dei piloti
    } catch (Exception $e) {
        logError($e); // Log dell'errore
        return []; // Ritorna un array vuoto in caso di errore
    }
}

// Funzione per ottenere i risultati di una gara specifica
function getGare() {
    global $db;  // Assicurati che la variabile globale di connessione al database sia definita

    // Query per ottenere tutte le gare
    $query = "SELECT * FROM Gara ORDER BY Data DESC"; // Ordina le gare dalla più recente

    try {
        // Prepara la query
        $stm = $db->prepare($query);
        // Esegui la query
        $stm->execute();
        // Recupera i risultati come oggetti
        $gare = $stm->fetchAll(PDO::FETCH_OBJ);
        $stm->closeCursor();

        // Restituisci i risultati delle gare
        return $gare;
    } catch (Exception $e) {
        // Gestisci eventuali errori
        logError($e);
        return [];
    }
}



// Funzione per ottenere la classifica generale dei piloti
function getClassificaPiloti() {
    global $db;

    $query = "SELECT P.ID_Pilota, P.Nome, P.Cognome, C.Nome AS Nome_Casa, SUM(R.Punti) AS Punti_Totali
              FROM Pilota P
              JOIN Risultati_gara R ON P.ID_Pilota = R.ID_Pilota
              JOIN Casa_Automobilistica C ON P.ID_Casa = C.ID_Casa
              GROUP BY P.ID_Pilota
              ORDER BY Punti_Totali DESC";

    try {
        $stm = $db->prepare($query);
        $stm->execute();
        $classifica = $stm->fetchAll(PDO::FETCH_OBJ);
        $stm->closeCursor();
        return $classifica;
    } catch (Exception $e) {
        logError($e);
        return [];
    }
}


function getPilotaByID($id) {
    global $db;

    $query = "SELECT P.ID_Pilota, P.Nome, P.Cognome, C.Nome AS Nome_Casa, SUM(R.Punti) AS Punti_Totali
              FROM Pilota P
              JOIN Risultati_gara R ON P.ID_Pilota = R.ID_Pilota
              JOIN Casa_Automobilistica C ON P.ID_Casa = C.ID_Casa
              WHERE P.ID_Pilota = :id
              GROUP BY P.ID_Pilota
              LIMIT 1";

    try {
        $stm = $db->prepare($query);
        $stm->bindParam(':id', $id);
        $stm->execute();
        $pilota = $stm->fetch(PDO::FETCH_OBJ);
        $stm->closeCursor();
        return $pilota;
    } catch (Exception $e) {
        logError($e);
        return null;
    }
}





//================CREATE========================

// Funzione per inserire una nuova casa automobilistica
function insertCasaAutomobilistica($nome, $colore) {
    global $db;

    $query = "INSERT INTO Casa_Automobilistica (Nome, ColoreLivrea) 
              VALUES (:nome, :colore)";

    try {
        $stm = $db->prepare($query);
        // Bind dei valori dai parametri della funzione
        $stm->bindValue(":nome", $nome);
        $stm->bindValue(":colore", $colore);

        // Esegui la query
        if ($stm->execute()) {
            return true; // Successo
        } else {
            throw new PDOException("Errore nell'esecuzione della query.");
        }
    } catch (Exception $e) {
        logError($e); // Log dell'errore
        return false; // Errore
    }
}


// Funzione per inserire un pilota nel database
function insertPilota($nome, $cognome, $id_casa) {
    // Query di inserimento per il pilota
    $query = "INSERT INTO Pilota (Nome, Cognome, ID_Casa)
              VALUES (:nome, :cognome, :id_casa)";

    global $db;
    try {
        $stm = $db->prepare($query);
        // Bind dei valori dai parametri della funzione
        $stm->bindValue(":nome", $nome);
        $stm->bindValue(":cognome", $cognome);
        $stm->bindValue(":id_casa", $id_casa);

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


// Funzione per inserire una gara nel database
function insertGara($nome, $data, $circuito)
{
    // Query di inserimento per la tabella Gara
    $query = "INSERT INTO Gara (Nome, Data, Circuito)
              VALUES (:nome, :data, :circuito)";

    global $db;
    try {
        $stm = $db->prepare($query);
        // Bind dei valori dai parametri della funzione
        $stm->bindValue(":nome", $nome);
        $stm->bindValue(":data", $data);
        $stm->bindValue(":circuito", $circuito);

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


// Funzione per inserire un risultato di gara nel database
function insertRisultato($id_gara, $id_pilota, $posizione, $punti) {
    // Query di inserimento
    $query = "INSERT INTO Risultati_gara (ID_Gara, ID_Pilota, Posizione, Punti)
              VALUES (:id_gara, :id_pilota, :posizione, :punti)";

    global $db;
    try {
        $stm = $db->prepare($query);
        // Bind dei valori dai parametri della funzione
        $stm->bindValue(":id_gara", $id_gara);
        $stm->bindValue(":id_pilota", $id_pilota);
        $stm->bindValue(":posizione", $posizione);
        $stm->bindValue(":punti", $punti);

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
function updatePunteggioPilota($id, $punteggio) {
    global $db;

    $query = "UPDATE Pilota SET Punti_Totali = :punteggio WHERE ID_Pilota = :id";

    try {
        $stm = $db->prepare($query);
        $stm->bindParam(':punteggio', $punteggio);
        $stm->bindParam(':id', $id);
        $stm->execute();
        $stm->closeCursor();
        return true;
    } catch (Exception $e) {
        logError($e);
        return false;
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