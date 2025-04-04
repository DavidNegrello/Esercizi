<?php
/**
 * Configurazione del database
 *
 * Questo file contiene le configurazioni necessarie per connettersi al database
 * e fornisce funzioni per interagire con il database.
 */

// Configurazione del database
define('DB_HOST', 'localhost');      // Hostname del server MySQL
define('DB_NAME', 'catalogo_pc_parts');  // Nome del database
define('DB_USER', 'root');         // Username per l'accesso al database
define('DB_PASS', '');       // Password per l'accesso al database
define('DB_CHARSET', 'utf8mb4');     // Set di caratteri

/**
 * Ottiene una connessione al database
 *
 * @return mysqli Connessione al database
 */
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verifica connessione
    if ($conn->connect_error) {
        die("Errore di connessione al database: " . $conn->connect_error);
    }

    // Imposta charset
    $conn->set_charset(DB_CHARSET);

    return $conn;
}

/**
 * Esegue una query e restituisce tutti i risultati come array associativo
 *
 * @param string $sql Query SQL da eseguire
 * @param string $types (opzionale) Tipi di parametri per il prepared statement
 * @param array $params (opzionale) Array di parametri per il prepared statement
 * @return array Array associativo con i risultati
 */
function fetchAll($sql, $types = '', $params = []) {
    $conn = getDbConnection();
    $result = [];

    if (empty($params)) {
        // Query semplice senza parametri
        $query = $conn->query($sql);
        if ($query) {
            $result = $query->fetch_all(MYSQLI_ASSOC);
        }
    } else {
        // Query con prepared statement
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            if (!empty($types) && !empty($params)) {
                // Utilizzo di bind_param con riferimenti
                $bindParams = array($types);
                for ($i = 0; $i < count($params); $i++) {
                    $bindParams[] = &$params[$i];
                }
                call_user_func_array(array($stmt, 'bind_param'), $bindParams);
            }

            $stmt->execute();
            $query = $stmt->get_result();

            if ($query) {
                $result = $query->fetch_all(MYSQLI_ASSOC);
            }

            $stmt->close();
        }
    }

    $conn->close();
    return $result;
}

/**
 * Esegue una query e restituisce una singola riga come array associativo
 *
 * @param string $sql Query SQL da eseguire
 * @param string $types (opzionale) Tipi di parametri per il prepared statement
 * @param array $params (opzionale) Array di parametri per il prepared statement
 * @return array|null Array associativo con una riga o null se non ci sono risultati
 */
function fetchOne($sql, $types = '', $params = []) {
    $results = fetchAll($sql, $types, $params);
    return (!empty($results)) ? $results[0] : null;
}

/**
 * Esegue una query di inserimento, aggiornamento o eliminazione
 *
 * @param string $sql Query SQL da eseguire
 * @param string $types (opzionale) Tipi di parametri per il prepared statement
 * @param array $params (opzionale) Array di parametri per il prepared statement
 * @return int|bool Numero di righe modificate o false in caso di errore
 */
function executeQuery($sql, $types = '', $params = []) {
    $conn = getDbConnection();
    $result = false;

    if (empty($params)) {
        // Query semplice senza parametri
        $result = $conn->query($sql);
        if ($result) {
            $result = $conn->affected_rows;
        }
    } else {
        // Query con prepared statement
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            if (!empty($types) && !empty($params)) {
                // Utilizzo di bind_param con riferimenti
                $bindParams = array($types);
                for ($i = 0; $i < count($params); $i++) {
                    $bindParams[] = &$params[$i];
                }
                call_user_func_array(array($stmt, 'bind_param'), $bindParams);
            }

            $stmt->execute();
            $result = $stmt->affected_rows;
            $stmt->close();
        }
    }

    $conn->close();
    return $result;
}

/**
 * Ottiene l'ultimo ID inserito
 *
 * @return int L'ID dell'ultima riga inserita
 */
function getLastInsertId() {
    $conn = getDbConnection();
    $lastId = $conn->insert_id;
    $conn->close();
    return $lastId;
}

/**
 * Funzione per sanitizzare l'input
 *
 * @param string $data Dati da sanitizzare
 * @return string Dati sanitizzati
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>