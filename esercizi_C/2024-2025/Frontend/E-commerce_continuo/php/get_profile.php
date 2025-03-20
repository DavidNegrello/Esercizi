<?php
header('Content-Type: application/json');

// Ottieni l'ID del profilo
$profileId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$profileId) {
    echo json_encode(['error' => 'ID profilo mancante']);
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
    echo json_encode(['error' => 'Connessione al database fallita: ' . $conn->connect_error]);
    exit;
}

// Cerca il profilo nel database
$stmt = $conn->prepare("SELECT * FROM profili_utenti WHERE id = ?");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Profilo trovato
    $row = $result->fetch_assoc();
    $tipoProfilo = $row['tipo_profilo'];
    $preferenze = json_decode($row['preferenze'], true);
    
    // Ottieni la configurazione consigliata in base al tipo di profilo
    $configurazione = getConfigurazioneProfilo($conn, $tipoProfilo, $preferenze);
    
    // Ottieni l'abbonamento consigliato
    $abbonamento = getAbbonamentoProfilo($conn, $tipoProfilo);
    
    // Ottieni i prodotti consigliati
    $prodotti = getProdottiConsigliati($conn, $tipoProfilo, $preferenze);
    
    // Crea l'oggetto profilo
    $profilo = [
        'tipo' => $tipoProfilo,
        'descrizione' => getDescrizioneProfilo($tipoProfilo),
        'configurazione' => $configurazione,
        'abbonamento' => $abbonamento,
        'prodotti' => $prodotti
    ];
    
    echo json_encode($profilo);
} else {
    // Profilo non trovato, cerca nei profili temporanei
    $stmt = $conn->prepare("SELECT * FROM profili_temporanei WHERE id = ?");
    $stmt->bind_param("i", $profileId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Profilo temporaneo trovato
        $row = $result->fetch_assoc();
        $tipoProfilo = $row['tipo_profilo'];
        $preferenze = json_decode($row['preferenze'], true);
        
        // Ottieni la configurazione consigliata in base al tipo di profilo
        $configurazione = getConfigurazioneProfilo($conn, $tipoProfilo, $preferenze);
        
        // Ottieni l'abbonamento consigliato
        $abbonamento = getAbbonamentoProfilo($conn, $tipoProfilo);
        
        // Ottieni i prodotti consigliati
        $prodotti = getProdottiConsigliati($conn, $tipoProfilo, $preferenze);
        
        // Crea l'oggetto profilo
        $profilo = [
            'tipo' => $tipoProfilo,
            'descrizione' => getDescrizioneProfilo($tipoProfilo),
            'configurazione' => $configurazione,
            'abbonamento' => $abbonamento,
            'prodotti' => $prodotti
        ];
        
        echo json_encode($profilo);
    } else {
        // Profilo non trovato
        echo json_encode(['error' => 'Profilo non trovato']);
    }
}

$stmt->close();
$conn->close();

/**
 * Ottiene la descrizione del profilo
 */
function getDescrizioneProfilo($tipoProfilo) {
    switch ($tipoProfilo) {
        case 'gamer':
            return 'Sei un appassionato di gaming che cerca prestazioni elevate per i giochi più recenti.';
        case 'creativo':
            return 'Sei un creativo che ha bisogno di potenza per editing video, foto e grafica.';
        case 'sviluppatore':
            return 'Sei uno sviluppatore che necessita di un sistema affidabile per programmare e testare.';
        case 'business':
            return 'Sei un professionista che cerca un PC affidabile per il lavoro quotidiano.';
        case 'casual':
            return 'Sei un utente che utilizza il PC principalmente per navigare e attività leggere.';
        default:
            return 'Abbiamo analizzato le tue risposte e creato un profilo personalizzato.';
    }
}

/**
 * Ottiene la configurazione consigliata in base al tipo di profilo
 */
function getConfigurazioneProfilo($conn, $tipoProfilo, $preferenze) {
    // In un'implementazione reale, questa funzione dovrebbe interrogare il database
    // per ottenere componenti adatti al profilo dell'utente
    
    // Per ora, restituiamo configurazioni predefinite
    switch ($tipoProfilo) {
        case 'gamer':
            return [
                ['nome' => 'Processore Intel Core i7-12700K', 'prezzo' => 399.99],
                ['nome' => 'Scheda Video NVIDIA RTX 3070', 'prezzo' => 599.99],
                ['nome' => 'RAM 32GB DDR4 3600MHz', 'prezzo' => 159.99],
                ['nome' => 'SSD NVMe 1TB', 'prezzo' => 129.99],
                ['nome' => 'Alimentatore 750W Gold', 'prezzo' => 99.99]
            ];
        case 'creativo':
            return [
                ['nome' => 'Processore AMD Ryzen 9 5900X', 'prezzo' => 449.99],
                ['nome' => 'Scheda Video NVIDIA RTX 3080', 'prezzo' => 799.99],
                ['nome' => 'RAM 64GB DDR4 3200MHz', 'prezzo' => 259.99],
                ['nome' => 'SSD NVMe 2TB', 'prezzo' => 229.99],
                ['nome' => 'Alimentatore 850W Platinum', 'prezzo' => 149.99]
            ];
        case 'sviluppatore':
            return [
                ['nome' => 'Processore Intel Core i9-12900K', 'prezzo' => 549.99],
                ['nome' => 'Scheda Video NVIDIA RTX 3060', 'prezzo' => 399.99],
                ['nome' => 'RAM 32GB DDR4 3200MHz', 'prezzo' => 159.99],
                ['nome' => 'SSD NVMe 1TB', 'prezzo' => 129.99],
                ['nome' => 'Alimentatore 650W Gold', 'prezzo' => 89.99]
            ];
        case 'business':
            return [
                ['nome' => 'Processore Intel Core i5-12600K', 'prezzo' => 299.99],
                ['nome' => 'Scheda Video NVIDIA RTX 3050', 'prezzo' => 249.99],
                ['nome' => 'RAM 16GB DDR4 3200MHz', 'prezzo' => 89.99],
                ['nome' => 'SSD NVMe 500GB', 'prezzo' => 79.99],
                ['nome' => 'Alimentatore 550W Bronze', 'prezzo' => 59.99]
            ];
        case 'casual':
            return [
                ['nome' => 'Processore Intel Core i3-12100', 'prezzo' => 149.99],
                ['nome' => 'Scheda Video Integrata Intel UHD', 'prezzo' => 0],
                ['nome' => 'RAM 8GB DDR4 2666MHz', 'prezzo' => 49.99],
                ['nome' => 'SSD SATA 256GB', 'prezzo' => 39.99],
                ['nome' => 'Alimentatore 450W', 'prezzo' => 39.99]
            ];
        default:
            return [];
    }
}

/**
 * Ottiene l'abbonamento consigliato in base al tipo di profilo
 */
function getAbbonamentoProfilo($conn, $tipoProfilo) {
    // In un'implementazione reale, questa funzione dovrebbe interrogare il database
    // per ottenere abbonamenti adatti al profilo dell'utente
    
    // Per ora, restituiamo abbonamenti predefiniti
    switch ($tipoProfilo) {
        case 'gamer':
            return [
                'nome' => 'Gaming Pro',
                'descrizione' => 'Ricevi ogni mese nuovi componenti e accessori gaming selezionati per te.',
                'prezzo' => 49.99,
                'durata' => 'mensile'
            ];
        case 'creativo':
            return [
                'nome' => 'Creative Suite',
                'descrizione' => 'Ricevi ogni mese nuovi strumenti e accessori per la tua creatività.',
                'prezzo' => 59.99,
                'durata' => 'mensile'
            ];
        case 'sviluppatore':
            return [
                'nome' => 'Dev Tools',
                'descrizione' => 'Ricevi ogni mese nuovi strumenti e accessori per lo sviluppo software.',
                'prezzo' => 39.99,
                'durata' => 'mensile'
            ];
        case 'business':
            return [
                'nome' => 'Business Essentials',
                'descrizione' => 'Ricevi ogni mese nuovi strumenti e accessori per il tuo lavoro.',
                'prezzo' => 29.99,
                'durata' => 'mensile'
            ];
        case 'casual':
            return [
                'nome' => 'Casual Tech',
                'descrizione' => 'Ricevi ogni mese nuovi accessori tech per il tuo uso quotidiano.',
                'prezzo' => 19.99,
                'durata' => 'mensile'
            ];
        default:
            return null;
    }
}

/**
 * Ottiene i prodotti consigliati in base al tipo di profilo
 */
function getProdottiConsigliati($conn, $tipoProfilo, $preferenze) {
    // In un'implementazione reale, questa funzione dovrebbe interrogare il database
    // per ottenere prodotti adatti al profilo dell'utente
    
    // Per ora, restituiamo prodotti predefiniti
    switch ($tipoProfilo) {
        case 'gamer':
            return [
                ['id' => 1, 'nome' => 'Mouse Gaming RGB', 'descrizione' => 'Mouse ad alta precisione con illuminazione RGB', 'prezzo' => 59.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 2, 'nome' => 'Tastiera Meccanica', 'descrizione' => 'Tastiera meccanica con switch Cherry MX', 'prezzo' => 89.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 3, 'nome' => 'Cuffie Gaming 7.1', 'descrizione' => 'Cuffie con audio surround 7.1', 'prezzo' => 79.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 4, 'nome' => 'Mousepad XXL', 'descrizione' => 'Tappetino per mouse di grandi dimensioni', 'prezzo' => 29.99, 'immagine' => 'https://via.placeholder.com/150']
            ];
        case 'creativo':
            return [
                ['id' => 5, 'nome' => 'Tavoletta Grafica', 'descrizione' => 'Tavoletta grafica con penna sensibile alla pressione', 'prezzo' => 149.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 6, 'nome' => 'Monitor 4K IPS', 'descrizione' => 'Monitor 4K con pannello IPS per colori accurati', 'prezzo' => 349.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 7, 'nome' => 'Hard Disk Esterno 4TB', 'descrizione' => 'Hard disk esterno per backup e archiviazione', 'prezzo' => 119.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 8, 'nome' => 'Calibratore Colore', 'descrizione' => 'Dispositivo per la calibrazione del colore del monitor', 'prezzo' => 179.99, 'immagine' => 'https://via.placeholder.com/150']
            ];
        case 'sviluppatore':
            return [
                ['id' => 9, 'nome' => 'Tastiera Ergonomica', 'descrizione' => 'Tastiera ergonomica per lunghe sessioni di digitazione', 'prezzo' => 129.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 10, 'nome' => 'Monitor Ultrawide', 'descrizione' => 'Monitor ultrawide per multitasking', 'prezzo' => 399.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 11, 'nome' => 'Dock USB-C', 'descrizione' => 'Dock USB-C con multiple porte per connettere dispositivi', 'prezzo' => 89.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 12, 'nome' => 'Sedia Ergonomica', 'descrizione' => 'Sedia ergonomica per il massimo comfort', 'prezzo' => 249.99, 'immagine' => 'https://via.placeholder.com/150']
            ];
        case 'business':
            return [
                ['id' => 13, 'nome' => 'Webcam HD', 'descrizione' => 'Webcam HD per videoconferenze', 'prezzo' => 69.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 14, 'nome' => 'Cuffie con Microfono', 'descrizione' => 'Cuffie con microfono per chiamate chiare', 'prezzo' => 99.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 15, 'nome' => 'Stampante Multifunzione', 'descrizione' => 'Stampante, scanner e fotocopiatrice in un unico dispositivo', 'prezzo' => 179.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 16, 'nome' => 'Hard Disk Esterno 2TB', 'descrizione' => 'Hard disk esterno per backup e archiviazione', 'prezzo' => 79.99, 'immagine' => 'https://via.placeholder.com/150']
            ];
        case 'casual':
            return [
                ['id' => 17, 'nome' => 'Mouse Wireless', 'descrizione' => 'Mouse wireless compatto e comodo', 'prezzo' => 29.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 18, 'nome' => 'Tastiera Wireless', 'descrizione' => 'Tastiera wireless compatta', 'prezzo' => 39.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 19, 'nome' => 'Cuffie Bluetooth', 'descrizione' => 'Cuffie bluetooth con microfono integrato', 'prezzo' => 49.99, 'immagine' => 'https://via.placeholder.com/150'],
                ['id' => 20, 'nome' => 'Altoparlanti USB', 'descrizione' => 'Altoparlanti USB compatti per il tuo PC', 'prezzo' => 19.99, 'immagine' => 'https://via.placeholder.com/150']
            ];
        default:
            return [];
    }
}
?>