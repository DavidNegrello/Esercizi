<?php
require_once 'database.php';

try {
    // Verifica se il database esiste già
    $pdo->query("USE ecommerce_db");
    echo "Database ecommerce_db selezionato.<br>";
    
    // Importa i dati del catalogo
    echo "<h2>Importazione dati del catalogo</h2>";
    $catalogoJson = file_get_contents('../data/catalogo.json');
    if (!$catalogoJson) {
        throw new Exception("File catalogo.json non trovato");
    }
    
    $catalogo = json_decode($catalogoJson, true);
    if (!$catalogo || !isset($catalogo['prodotti'])) {
        throw new Exception("Formato del file catalogo.json non valido");
    }
    
    // Importa i prodotti del catalogo
    $stmt = $pdo->prepare("
        INSERT INTO prodotti (
            id, nome, descrizione, prezzo_base, categoria, marca, 
            immagine_principale, data_creazione
        ) VALUES (
            :id, :nome, :descrizione, :prezzo_base, :categoria, :marca, 
            :immagine_principale, NOW()
        ) ON DUPLICATE KEY UPDATE
            nome = VALUES(nome),
            descrizione = VALUES(descrizione),
            prezzo_base = VALUES(prezzo_base),
            categoria = VALUES(categoria),
            marca = VALUES(marca),
            immagine_principale = VALUES(immagine_principale)
    ");
    
    foreach ($catalogo['prodotti'] as $prodotto) {
        $params = [
            'id' => $prodotto['id'],
            'nome' => $prodotto['nome'],
            'descrizione' => $prodotto['descrizione'] ?? '',
            'prezzo_base' => $prodotto['prezzo'],
            'categoria' => $prodotto['categoria'],
            'marca' => $prodotto['marca'],
            'immagine_principale' => $prodotto['immagine']
        ];
        
        $stmt->execute($params);
        echo "Prodotto ID {$prodotto['id']} ({$prodotto['nome']}) importato.<br>";
    }
    
    // Importa i dettagli dei prodotti
    echo "<h2>Importazione dettagli dei prodotti</h2>";
    $dettagliJson = file_get_contents('../data/contenuti_catalogo.json');
    if (!$dettagliJson) {
        throw new Exception("File contenuti_catalogo.json non trovato");
    }
    
    $dettagli = json_decode($dettagliJson, true);
    if (!$dettagli || !isset($dettagli['prodotti'])) {
        throw new Exception("Formato del file contenuti_catalogo.json non valido");
    }
    
    $stmt = $pdo->prepare("
        UPDATE prodotti SET
            immagini = :immagini,
            specifiche = :specifiche,
            specifiche_dettagliate = :specifiche_dettagliate,
            varianti = :varianti
        WHERE id = :id
    ");
    
    foreach ($dettagli['prodotti'] as $prodotto) {
        $params = [
            'id' => $prodotto['id'],
            'immagini' => json_encode($prodotto['immagini'] ?? []),
            'specifiche' => json_encode($prodotto['specifiche'] ?? []),
            'specifiche_dettagliate' => json_encode($prodotto['specifiche_dettagliate'] ?? []),
            'varianti' => json_encode($prodotto['varianti'] ?? [])
        ];
        
        $stmt->execute($params);
        echo "Dettagli per prodotto ID {$prodotto['id']} importati.<br>";
    }
    
    // Importa i bundle
    echo "<h2>Importazione bundle</h2>";
    $bundleJson = file_get_contents('../data/bundle.json');
    if ($bundleJson) {
        $bundleData = json_decode($bundleJson, true);
        
        if ($bundleData && isset($bundleData['bundle'])) {
            $stmt = $pdo->prepare("
                INSERT INTO bundle (
                    id, nome, descrizione, prezzo_originale, prezzo_scontato, 
                    immagine, prodotti, data_scadenza
                ) VALUES (
                    :id, :nome, :descrizione, :prezzo_originale, :prezzo_scontato, 
                    :immagine, :prodotti, :data_scadenza
                ) ON DUPLICATE KEY UPDATE
                    nome = VALUES(nome),
                    descrizione = VALUES(descrizione),
                    prezzo_originale = VALUES(prezzo_originale),
                    prezzo_scontato = VALUES(prezzo_scontato),
                    immagine = VALUES(immagine),
                    prodotti = VALUES(prodotti),
                    data_scadenza = VALUES(data_scadenza)
            ");
            
            foreach ($bundleData['bundle'] as $index => $bundle) {
                // Calcola una data di scadenza (30 giorni da oggi)
                $dataScadenza = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                $params = [
                    'id' => $index + 1,
                    'nome' => $bundle['nome'],
                    'descrizione' => $bundle['descrizione'] ?? '',
                    'prezzo_originale' => $bundle['prezzo_originale'],
                    'prezzo_scontato' => $bundle['prezzo_scontato'],
                    'immagine' => $bundle['immagine'],
                    'prodotti' => json_encode($bundle['prodotti'] ?? []),
                    'data_scadenza' => $dataScadenza
                ];
                
                $stmt->execute($params);
                echo "Bundle '{$bundle['nome']}' importato.<br>";
            }
        }
    }
    
    // Importa i coupon di esempio
    echo "<h2>Creazione coupon di esempio</h2>";
    $stmt = $pdo->prepare("
        INSERT INTO coupon (
            codice, percentuale_sconto, data_inizio, data_fine, attivo
        ) VALUES (
            :codice, :percentuale_sconto, :data_inizio, :data_fine, :attivo
        ) ON DUPLICATE KEY UPDATE
            percentuale_sconto = VALUES(percentuale_sconto),
            data_inizio = VALUES(data_inizio),
            data_fine = VALUES(data_fine),
            attivo = VALUES(attivo)
    ");
    
    $coupon = [
        'codice' => 'WELCOME10',
        'percentuale_sconto' => 10,
        'data_inizio' => date('Y-m-d'),
        'data_fine' => date('Y-m-d', strtotime('+30 days')),
        'attivo' => true
    ];
    $stmt->execute($coupon);
    echo "Coupon '{$coupon['codice']}' creato.<br>";
    
    $coupon = [
        'codice' => 'SUMMER20',
        'percentuale_sconto' => 20,
        'data_inizio' => date('Y-m-d'),
        'data_fine' => date('Y-m-d', strtotime('+15 days')),
        'attivo' => true
    ];
    $stmt->execute($coupon);
    echo "Coupon '{$coupon['codice']}' creato.<br>";
    
    echo "<h2>Importazione completata con successo!</h2>";
    
} catch (Exception $e) {
    echo "<h2>Errore durante l'importazione:</h2>";
    echo "<p>{$e->getMessage()}</p>";
}
?>