<?php
require 'function.php';
require 'DBconf.php';   //funzione per la configurazione
$config= require 'database_conf.php'; //così non scriviamo tutto dentro
$db = DBconf::getDB($config);   //ora si trova dentro la funzione

//============READ===========

/**
 * Ottiene i prossimi eventi con informazioni sulla visita e sulla guida
 * @param object $db - Connessione al database
 * @param int $limit - Numero massimo di eventi da recuperare
 * @return array - Risultati della query
 */
function getProssimiEventi($db, $limit = 5) {
    $sql = "SELECT e.id, e.prezzo, e.ora_inizio, e.num_minimo_partecipanti, 
                  e.num_massimo_partecipanti, v.titolo, v.durata_media, v.luogo,
                  g.nome, g.cognome
           FROM eventi e
           JOIN visite v ON e.id_visita = v.id
           JOIN guide g ON e.id_guida = g.id
           WHERE e.ora_inizio > NOW()
           ORDER BY e.ora_inizio ASC
           LIMIT :limit"; // usa il nome del parametro

    $stmt = $db->prepare($sql);

    // Usa bindValue() con PDO
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);  // Associa il parametro :limit

    $stmt->execute();
    // Usa fetchAll() per ottenere tutti i risultati

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Ottiene il conteggio totale delle visite disponibili
 * @param object $db - Connessione al database
 * @return int - Numero totale di visite
 */
function getTotaleVisite($db) {
    $sql = "SELECT COUNT(*) as totale FROM visite";
    $stmt = $db->prepare($sql);  // prepara la query
    $stmt->execute();  // esegui la query
    $row = $stmt->fetch(PDO::FETCH_ASSOC);  // recupera il risultato come array associativo

    return $row['totale'];  // restituisce il totale delle visite
}


/**
 * Ottiene il conteggio totale delle guide
 * @param object $db - Connessione al database
 * @return int - Numero totale di guide
 */
function getTotaleGuide($db) {
    $sql = "SELECT COUNT(*) as totale FROM guide";
    $stmt = $db->prepare($sql);  // prepara la query
    $stmt->execute();  // esegue la query
    $row = $stmt->fetch(PDO::FETCH_ASSOC);  // ottiene il risultato come array associativo

    return $row['totale'];  // restituisce il totale delle guide
}


/**
 * Ottiene il conteggio totale dei turisti
 * @param object $db - Connessione al database
 * @return int - Numero totale di turisti
 */
function getTotaleTuristi($db) {
    $sql = "SELECT COUNT(*) as totale FROM Turisti";  // query per ottenere il numero totale dei turisti
    $stmt = $db->prepare($sql);  // prepara la query
    $stmt->execute();  // esegue la query
    $row = $stmt->fetch(PDO::FETCH_ASSOC);  // ottiene il risultato come array associativo

    return $row['totale'];  // restituisce il totale dei turisti
}





/**
 * Ottiene tutte le visite disponibili
 * @param object $db - Connessione al database
 * @return array - Risultati della query
 */
function getVisite($db)
{
    $sql = "SELECT * FROM visite ORDER BY titolo ASC";
    return $db->query($sql); // This returns a PDOStatement object, not an array
}

/**
 * Ottiene tutte le guide disponibili
 * @param object $db - Connessione al database
 * @return array - Risultati della query
 */
function getGuide($db) {
    $sql = "SELECT g.*, COUNT(lg.lingua) as num_lingue
           FROM guide g
           LEFT JOIN lingue_guide lg ON g.id = lg.id_guida
           GROUP BY g.id
           ORDER BY g.cognome, g.nome ASC";

    return $db->query($sql);
}

/**
 * Registra un nuovo utente
 * @param object $db - Connessione al database
 * @param string $username - Nome utente
 * @param string $password - Password
 * @param string $email - Email
 * @return array - Risultato dell'operazione
 */
function registerUser($db, $username, $password, $email) {
    // Validazione
    if (empty($username) || empty($password) || empty($email)) {
        return ['success' => false, 'message' => 'Tutti i campi sono obbligatori'];
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare("INSERT INTO utenti (username, password, email) VALUES (:username, :password, :email)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registrazione completata con successo'];
        }
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            return ['success' => false, 'message' => 'Username o email già esistenti'];
        }
        return ['success' => false, 'message' => 'Errore durante la registrazione: ' . $e->getMessage()];
    }
}

/**
 * Effettua il login di un utente
 * @param object $db - Connessione al database
 * @param string $username - Nome utente
 * @param string $password - Password
 * @return array - Risultato dell'operazione
 */
function loginUser($db, $username, $password) {
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Inserisci username e password'];
    }

    try {
        $stmt = $db->prepare("SELECT id, username, password FROM utenti WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $row['password'])) {
                return [
                    'success' => true,
                    'user' => [
                        'id' => $row['id'],
                        'username' => $row['username']
                    ]
                ];
            } else {
                return ['success' => false, 'message' => 'Password errata'];
            }
        } else {
            return ['success' => false, 'message' => 'Username non trovato'];
        }
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Errore durante il login: ' . $e->getMessage()];
    }
}

/**
 * Verifica se l'utente è loggato
 * @return bool - True se l'utente è loggato, false altrimenti
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Ottiene i dati dell'utente corrente
 * @param object $db - Connessione al database
 * @return array|null - Dati dell'utente o null se non loggato
 */
function getCurrentUser($db) {
    if (!isLoggedIn()) {
        return null;
    }

    try {
        $stmt = $db->prepare("SELECT * FROM utenti WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log dell'errore o gestione appropriata
        return null;
    }
}

/**
 * Verifica se l'utente corrente è un amministratore
 * @param array $user - Array contenente i dati dell'utente
 * @return bool - True se l'utente è admin, false altrimenti
 */
function isAdmin($user) {
    // Verifica che l'utente esista e abbia un username
    if (!isset($user['username'])) {
        return false;
    }

    // Controlla se l'username è 'admin' (case insensitive)
    return strtolower($user['username']) === 'admin';
}
