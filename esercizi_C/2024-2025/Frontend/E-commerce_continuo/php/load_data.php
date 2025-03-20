<?php
header('Content-Type: application/json');

// Funzione per caricare i dati dal database
function loadDataFromDatabase($table) {
    // Configurazione del database
    $servername = "localhost";
    $username = "root";  // Cambia con il tuo username
    $password = "";      // Cambia con la tua password
    $dbname = "ecommerce_pc";  // Nome del database

    // Crea connessione
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica connessione
    if ($conn->connect_error) {
        return json_encode([
            'error' => 'Connessione al database fallita: ' . $conn->connect_error
        ]);
    }

    // Seleziona i dati in base alla tabella richiesta
    switch ($table) {
        case 'prodotti':
            $sql = "SELECT * FROM prodotti WHERE attivo = 1";
            break;
        case 'categorie':
            $sql = "SELECT * FROM categorie WHERE attivo = 1";
            break;
        case 'marche':
            $sql = "SELECT * FROM marche WHERE attivo = 1";
            break;
        case 'offerte':
            $sql = "SELECT * FROM offerte WHERE attivo = 1 AND data_fine > NOW()";
            break;
        case 'bundle':
            $sql = "SELECT b.*, GROUP_CONCAT(p.nome SEPARATOR '|') as prodotti_nomi 
                    FROM bundle b 
                    JOIN bundle_prodotti bp ON b.id = bp.bundle_id 
                    JOIN prodotti p ON bp.prodotto_id = p.id 
                    WHERE b.attivo = 1 
                    GROUP BY b.id";
            break;
        default:
            return json_encode(['error' => 'Tabella non valida']);
    }

    $result = $conn->query($sql);
    $data = [];

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Gestione speciale per i bundle
            if ($table === 'bundle' && isset($row['prodotti_nomi'])) {
                $row['prodotti'] = explode('|', $row['prodotti_nomi']);
                unset($row['prodotti_nomi']);
            }
            $data[] = $row;
        }
    }

    $conn->close();
    return json_encode($data);
}

// Ottieni il tipo di dati richiesto
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Carica i dati richiesti
echo loadDataFromDatabase($type);
?>