<?php
// Includi la configurazione del database
require_once '../config/database.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

// Verifica che l'ID del prodotto sia stato fornito
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID prodotto non valido o mancante']);
    exit;
}

$prodottoId = (int)$_GET['id'];

try {
    // Ottieni i dettagli del prodotto
    $stmt = $pdo->prepare("
        SELECT p.id, p.nome, p.descrizione, p.prezzo_base, 
               c.nome as categoria, m.nome as marca
        FROM prodotti p
        LEFT JOIN categorie c ON p.categoria_id = c.id
        LEFT JOIN marche m ON p.marca_id = m.id
        WHERE p.id = :id
    ");
    $stmt->execute(['id' => $prodottoId]);
    $prodotto = $stmt->fetch();
    
    if (!$prodotto) {
        http_response_code(404);
        echo json_encode(['error' => 'Prodotto non trovato']);
        exit;
    }
    
    // Ottieni le immagini del prodotto
    $stmt = $pdo->prepare("
        SELECT url_immagine
        FROM immagini_prodotto
        WHERE prodotto_id = :id
        ORDER BY ordine ASC
    ");
    $stmt->execute(['id' => $prodottoId]);
    $immagini = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Se non ci sono immagini, usa l'immagine principale
    if (empty($immagini)) {
        $immagini = [$prodotto['immagine_principale']];
    }
    
    // Ottieni le specifiche base del prodotto
    $stmt = $pdo->prepare("
        SELECT chiave, valore
        FROM specifiche_prodotto
        WHERE prodotto_id = :id AND tipo = 'base'
    ");
    $stmt->execute(['id' => $prodottoId]);
    $specifiche = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Ottieni le specifiche dettagliate del prodotto
    $stmt = $pdo->prepare("
        SELECT chiave, valore
        FROM specifiche_prodotto
        WHERE prodotto_id = :id AND tipo = 'dettagliata'
    ");
    $stmt->execute(['id' => $prodottoId]);
    $specificheDettagliate = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Ottieni le varianti del prodotto
    $stmt = $pdo->prepare("
        SELECT tipo_variante, valore, prezzo_aggiuntivo
        FROM varianti_prodotto
        WHERE prodotto_id = :id
    ");
    $stmt->execute(['id' => $prodottoId]);
    $variantiRaw = $stmt->fetchAll();
    
    // Organizza le varianti per tipo
    $varianti = [];
    $colori = [];
    $taglie = [];
    $capacita = [];
    
    foreach ($variantiRaw as $variante) {
        $tipo = $variante['tipo_variante'];
        $valore = $variante['valore'];
        $prezzo = $variante['prezzo_aggiuntivo'];
        
        if ($tipo === 'potenza') {
            $varianti[$valore] = $prezzo;
        } elseif ($tipo === 'colore') {
            $colori[] = $valore;
        } elseif ($tipo === 'taglia') {
            $taglie[$valore] = ['prezzo' => $prodotto['prezzo_base'] + $prezzo];
        } elseif ($tipo === 'capacita') {
            $capacita[] = $valore;
        }
    }
    
    // Costruisci la risposta completa
    $prodottoCompleto = [
        'id' => $prodotto['id'],
        'nome' => $prodotto['nome'],
        'descrizione' => $prodotto['descrizione'],
        'prezzo_base' => $prodotto['prezzo_base'],
        'categoria' => $prodotto['categoria'],
        'marca' => $prodotto['marca'],
        'immagini' => $immagini,
        'specifiche' => $specifiche,
        'specifiche_dettagliate' => $specificheDettagliate
    ];
    
    // Aggiungi le varianti se presenti
    if (!empty($varianti)) {
        $prodottoCompleto['varianti'] = $varianti;
    }
    
    if (!empty($colori)) {
        $prodottoCompleto['colori'] = $colori;
    }
    
    if (!empty($taglie)) {
        $prodottoCompleto['taglie'] = $taglie;
    }
    
    if (!empty($capacita)) {
        $prodottoCompleto['capacita'] = $capacita;
    }
    
    echo json_encode($prodottoCompleto);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore nel caricamento del prodotto: ' . $e->getMessage()]);
}
?>