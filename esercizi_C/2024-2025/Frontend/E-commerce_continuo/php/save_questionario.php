<?php
header('Content-Type: application/json');

// Ricevi i dati JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['sessionId']) || !isset($data['risposte'])) {
    echo json_encode(['success' => false, 'error' => 'Dati mancanti']);
    exit;
}

// Configurazione del database
$servername = "localhost";
$username = "root";  // Cambia con il tuo username
$password = "";      // Cambia con la tua password
$dbname = "ecommerce_pc";  // Nome del database

// Crea connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica connessione
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Connessione al database fallita: ' . $conn->connect_error]);
    exit;
}

// Estrai i dati
$sessionId = $data['sessionId'];
$risposte = $data['risposte'];

// Verifica se l'ID sessione corrisponde a un utente
$userId = null;
$stmt = $conn->prepare("SELECT utente_id FROM sessioni WHERE id = ?");
$stmt->bind_param("s", $sessionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userId = $row['utente_id'];
}
$stmt->close();

// Salva le risposte nel database
$success = true;
$conn->begin_transaction();

try {
    foreach ($risposte as $questionarioId => $risposta) {
        // Converti array in JSON se necessario
        if (is_array($risposta)) {
            $risposta = json_encode($risposta);
        }
        
        $stmt = $conn->prepare("INSERT INTO risposte_questionario (utente_id, sessione_id, questionario_id, risposta) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $sessionId, $questionarioId, $risposta);
        $stmt->execute();
        $stmt->close();
    }
    
    // Determina il profilo utente in base alle risposte
    $profileId = determineUserProfile($conn, $risposte, $userId, $sessionId);
    
    $conn->commit();
    echo json_encode(['success' => true, 'profileId' => $profileId]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => 'Errore nel salvataggio delle risposte: ' . $e->getMessage()]);
}

$conn->close();

/**
 * Determina il profilo utente in base alle risposte
 * 
 * @param mysqli $conn Connessione al database
 * @param array $risposte Risposte al questionario
 * @param int|null $userId ID utente (se disponibile)
 * @param string $sessionId ID sessione
 * @return int ID del profilo creato
 */
function determineUserProfile($conn, $risposte, $userId, $sessionId) {
    // Logica per determinare il tipo di profilo
    $tipoProfilo = "casual"; // Default
    $preferenze = [];
    
    // Analizza le risposte per determinare il profilo
    // Questa è una logica semplificata, in un'implementazione reale sarebbe più complessa
    
    // Esempio: determina il profilo in base allo scopo principale (domanda 1)
    if (isset($risposte[1])) {
        $scopoPrincipale = $risposte[1];
        
        if ($scopoPrincipale === "Gaming") {
            $tipoProfilo = "gamer";
            $preferenze["focus"] = "prestazioni_grafiche";
        } elseif ($scopoPrincipale === "Editing video/foto") {
            $tipoProfilo = "creativo";
            $preferenze["focus"] = "multitasking";
        } elseif ($scopoPrincipale === "Programmazione") {
            $tipoProfilo = "sviluppatore";
            $preferenze["focus"] = "cpu_ram";
        } elseif ($scopoPrincipale === "Lavoro d'ufficio") {
            $tipoProfilo = "business";
            $preferenze["focus"] = "affidabilita";
        }
    }
    
    // Esempio: aggiungi preferenze di budget (domanda 2)
    if (isset($risposte[2])) {
        $budget = $risposte[2];
        $preferenze["budget"] = $budget;
    }
    
    // Esempio: aggiungi software preferiti (domanda 3)
    if (isset($risposte[3])) {
        $preferenze["software"] = $risposte[3];
    }
    
    // Esempio: aggiungi caratteristiche importanti (domanda 4)
    if (isset($risposte[4]) && is_array($risposte[4])) {
        $preferenze["caratteristiche"] = $risposte[4];
    }
    
    // Esempio: aggiungi preferenze di archiviazione (domanda 5)
    if (isset($risposte[5])) {
        $preferenze["archiviazione"] = $risposte[5];
    }
    
    // Converti le preferenze in JSON
    $preferenzeJson = json_encode($preferenze);
    
    // Se l'utente è loggato, salva il profilo
    if ($userId) {
        // Verifica se esiste già un profilo per questo utente
        $stmt = $conn->prepare("SELECT id FROM profili_utenti WHERE utente_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Aggiorna il profilo esistente
            $row = $result->fetch_assoc();
            $profileId = $row['id'];
            
            $stmt = $conn->prepare("UPDATE profili_utenti SET tipo_profilo = ?, preferenze = ?, data_aggiornamento = NOW() WHERE id = ?");
            $stmt->bind_param("ssi", $tipoProfilo, $preferenzeJson, $profileId);
            $stmt->execute();
        } else {
            // Crea un nuovo profilo
            $stmt = $conn->prepare("INSERT INTO profili_utenti (utente_id, tipo_profilo, preferenze) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $tipoProfilo, $preferenzeJson);
            $stmt->execute();
            $profileId = $conn->insert_id;
        }
        
        $stmt->close();
    } else {
        // Se l'utente non è loggato, salva temporaneamente il profilo nella sessione
        $stmt = $conn->prepare("INSERT INTO profili_temporanei (sessione_id, tipo_profilo, preferenze) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $sessionId, $tipoProfilo, $preferenzeJson);
        $stmt->execute();
        $profileId = $conn->insert_id;
        $stmt->close();
    }
    
    return $profileId;
}
?>