<?php

require 'DB_class.php';   // funzione per la configurazione
$config = require 'conf.php'; // così non scriviamo tutto dentro
$db = DB_class::getDB($config);   // ora si trova dentro la funzione


//============READ===========
// Funzione per leggere i prodotti con i filtri
function ReadProdotti($filters = [])
{
    global $db;

    // Query base per recuperare i prodotti
    $query = "SELECT p.id_prodotto, p.nome, p.prezzo, p.descrizione, 
       c.nome_categoria AS categoria, m.nome_marca AS marca, 
       (SELECT url_immagine FROM immagine WHERE id_prodotto = p.id_prodotto LIMIT 1) AS immagine
FROM prodotto p
LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
LEFT JOIN marca m ON p.id_marca = m.id_marca
WHERE p.nome LIKE '%ricerca%' AND c.nome_categoria IN ('CPU', 'GPU')
";

    // Array per memorizzare le condizioni della query
    $conditions = [];

    // Filtro per la ricerca
    if (!empty($filters['ricerca'])) {
        $conditions[] = "p.nome LIKE :ricerca";
    }

    // Filtro per categoria
    if (!empty($filters['categorie']) && is_array($filters['categorie'])) {
        $placeholders = implode(',', array_fill(0, count($filters['categorie']), '?'));
        $conditions[] = "c.nome_categoria IN ($placeholders)";
    }

    // Filtro per marca
    if (!empty($filters['marche']) && is_array($filters['marche'])) {
        $placeholders = implode(',', array_fill(0, count($filters['marche']), '?'));
        $conditions[] = "m.nome_marca IN ($placeholders)";
    }

    // Filtro per prezzo
    if (isset($filters['prezzo_min']) && isset($filters['prezzo_max'])) {
        $conditions[] = "p.prezzo BETWEEN :prezzo_min AND :prezzo_max";
    }

    // Aggiungere le condizioni alla query
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    // Ordinamento per ID prodotto
    $query .= " ORDER BY p.id_prodotto ASC";

    try {
        // Prepara la query
        $stm = $db->prepare($query);

        // Binding dei parametri
        $bindCount = 1;

        // Bind del parametro di ricerca
        if (!empty($filters['ricerca'])) {
            $stm->bindValue($bindCount++, "%" . $filters['ricerca'] . "%", PDO::PARAM_STR);
        }

        // Bind per le categorie
        if (!empty($filters['categorie'])) {
            foreach ($filters['categorie'] as $categoria) {
                $stm->bindValue($bindCount++, $categoria, PDO::PARAM_STR);
            }
        }

        // Bind per le marche
        if (!empty($filters['marche'])) {
            foreach ($filters['marche'] as $marca) {
                $stm->bindValue($bindCount++, $marca, PDO::PARAM_STR);
            }
        }

        // Bind per il prezzo
        if (isset($filters['prezzo_min']) && isset($filters['prezzo_max'])) {
            $stm->bindValue($bindCount++, $filters['prezzo_min'], PDO::PARAM_INT);
            $stm->bindValue($bindCount++, $filters['prezzo_max'], PDO::PARAM_INT);
        }

        // Esegui la query
        $stm->execute();

        // Restituisci i risultati come array associativo
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Errore nella lettura dei prodotti: " . $e->getMessage());
        return [];
    }
}

// Funzione per leggere i filtri
function ReadFiltri()
{
    global $db;

    // Query per ottenere i filtri
    $query = "SELECT tipo_filtro, valori FROM filtro";

    try {
        $stm = $db->prepare($query);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Errore nella lettura dei filtri: " . $e->getMessage());
        return [];
    }
}


/*
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
*/







//=============FUNZIONE LOG===============
function logError(Exception $exception): void
{
    echo "Errore nel database";
    error_log($exception->getMessage() . '***' . date('Y-m-d:i:s') . "\n", message_type: 3, destination: '../log/dberror.log');
}