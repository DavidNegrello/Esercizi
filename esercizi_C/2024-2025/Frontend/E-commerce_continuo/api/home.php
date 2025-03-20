<?php
require_once '../config/database.php';
require_once '../includes/Session.php';

// Inizializza la sessione
$session = new Session($pdo);

// Ottieni i prodotti più acquistati (i più recenti per semplicità)
$stmt = $pdo->query("SELECT * FROM prodotti ORDER BY data_creazione DESC LIMIT 3");
$prodotti_popolari = $stmt->fetchAll();

// Ottieni i bundle attivi
$stmt = $pdo->query("
    SELECT * FROM bundle 
    WHERE data_scadenza > NOW() 
    ORDER BY data_scadenza ASC 
    LIMIT 2
");
$bundles = $stmt->fetchAll();

// Per ogni bundle, ottieni i prodotti associati
foreach ($bundles as &$bundle) {
    $bundle['prodotti'] = json_decode($bundle['prodotti'], true);
    
    // Ottieni i dettagli dei prodotti
    $prodotti_dettagli = [];
    foreach ($bundle['prodotti'] as $prodotto_id) {
        $stmt = $pdo->prepare("SELECT id, nome, categoria FROM prodotti WHERE id = :id");
        $stmt->execute(['id' => $prodotto_id]);
        $prodotto = $stmt->fetch();
        if ($prodotto) {
            $prodotti_dettagli[] = $prodotto;
        }
    }
    $bundle['prodotti'] = $prodotti_dettagli;
}

// Prepara la risposta
$response = [
    'hero' => [
        'titolo' => 'Benvenuto su PC Componenti',
        'descrizione' => 'Il tuo negozio online per componenti PC di alta qualità. Trova tutto ciò di cui hai bisogno per costruire o aggiornare il tuo computer.',
        'bottone' => [
            'testo' => 'Esplora il Catalogo',
            'link' => 'pagine/catalogo.html'
        ]
    ],
    'prodotti_piu_acquistati' => $prodotti_popolari,
    'offerte_speciali' => [
        'titolo' => 'Offerte Speciali',
        'timer_testo' => 'Termina in:',
        'bottone' => 'Aggiungi al Carrello',
        'bundle' => $bundles
    ]
];

// Invia la risposta come JSON
header('Content-Type: application/json');
echo json_encode($response);
?>