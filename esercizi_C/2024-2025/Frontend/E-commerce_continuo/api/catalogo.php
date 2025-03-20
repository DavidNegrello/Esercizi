<?php
require_once '../config/database.php';
require_once '../includes/Session.php';

// Inizializza la sessione
$session = new Session($pdo);

// Ottieni i parametri di filtro
$categoria = $_GET['categoria'] ?? null;
$marca = $_GET['marca'] ?? null;
$prezzo_max = $_GET['prezzo_max'] ?? null;
$ricerca = $_GET['ricerca'] ?? null;

// Costruisci la query SQL
$sql = "SELECT * FROM prodotti WHERE 1=1";
$params = [];

if ($categoria) {
    $sql .= " AND categoria = :categoria";
    $params['categoria'] = $categoria;
}

if ($marca) {
    $sql .= " AND marca = :marca";
    $params['marca'] = $marca;
}

if ($prezzo_max) {
    $sql .= " AND prezzo_base <= :prezzo_max";
    $params['prezzo_max'] = $prezzo_max;
}

if ($ricerca) {
    $sql .= " AND (nome LIKE :ricerca OR descrizione LIKE :ricerca)";
    $params['ricerca'] = "%$ricerca%";
}

// Esegui la query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$prodotti = $stmt->fetchAll();

// Ottieni le categorie e marche disponibili per i filtri
$stmt = $pdo->query("SELECT DISTINCT categoria FROM prodotti ORDER BY categoria");
$categorie = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->query("SELECT DISTINCT marca FROM prodotti ORDER BY marca");
$marche = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->query("SELECT MIN(prezzo_base) as min, MAX(prezzo_base) as max FROM prodotti");
$prezzi = $stmt->fetch();

// Prepara la risposta
$response = [
    'prodotti' => $prodotti,
    'sidebar' => [
        'titolo' => 'Filtri',
        'filtri' => [
            'ricerca' => 'Cerca prodotti...',
            'categorie' => $categorie,
            'marche' => $marche,
            'prezzo' => [
                'min' => $prezzi['min'] ?? 0,
                'max' => $prezzi['max'] ?? 1000,
                'step' => 10
            ]
        ]
    ]
];

// Invia la risposta come JSON
header('Content-Type: application/json');
echo json_encode($response);
?>