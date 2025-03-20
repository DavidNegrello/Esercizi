<?php
// Includi la configurazione del database
require_once '../config/database.php';

// Imposta l'header per JSON
header('Content-Type: application/json');

try {
    // Ottieni le categorie
    $stmt = $pdo->query("SELECT id, nome FROM categorie ORDER BY nome");
    $categorie = $stmt->fetchAll();
    
    // Ottieni le marche
    $stmt = $pdo->query("SELECT id, nome FROM marche ORDER BY nome");
    $marche = $stmt->fetchAll();
    
    // Ottieni il prezzo minimo e massimo
    $stmt = $pdo->query("SELECT MIN(prezzo_base) as min, MAX(prezzo_base) as max FROM prodotti");
    $prezzi = $stmt->fetch();
    
    // Ottieni tutti i prodotti con le loro categorie e marche
    $stmt = $pdo->query("
        SELECT p.id, p.nome, p.prezzo_base as prezzo, p.immagine_principale as immagine,
               c.nome as categoria, m.nome as marca
        FROM prodotti p
        LEFT JOIN categorie c ON p.categoria_id = c.id
        LEFT JOIN marche m ON p.marca_id = m.id
        ORDER BY p.id DESC
    ");
    $prodotti = $stmt->fetchAll();
    
    // Costruisci la risposta JSON
    $response = [
        'sidebar' => [
            'titolo' => 'Filtri',
            'filtri' => [
                'ricerca' => 'Cerca prodotti...',
                'categorie' => array_column($categorie, 'nome'),
                'marche' => array_column($marche, 'nome'),
                'prezzo' => [
                    'min' => (int)$prezzi['min'],
                    'max' => (int)$prezzi['max'],
                    'step' => 10
                ]
            ]
        ],
        'prodotti' => $prodotti
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    // In caso di errore, restituisci un messaggio di errore
    http_response_code(500);
    echo json_encode(['error' => 'Errore nel caricamento del catalogo: ' . $e->getMessage()]);
}
?>