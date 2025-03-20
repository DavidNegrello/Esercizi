<?php
require_once '../config/database.php';

// Funzione per ottenere tutti i prodotti del catalogo
function getAllProducts() {
    global $pdo;
    
    $stmt = $pdo->query("
        SELECT p.id, p.nome, p.descrizione, p.prezzo, p.categoria, p.marca, p.immagine
        FROM prodotti p
        ORDER BY p.id DESC
    ");
    
    return $stmt->fetchAll();
}

// Funzione per ottenere i prodotti filtrati
function getFilteredProducts($search = '', $maxPrice = null, $categories = [], $brands = []) {
    global $pdo;
    
    $query = "
        SELECT p.id, p.nome, p.descrizione, p.prezzo, p.categoria, p.marca, p.immagine
        FROM prodotti p
        WHERE 1=1
    ";
    
    $params = [];
    
    // Filtro per ricerca testuale
    if (!empty($search)) {
        $query .= " AND (p.nome LIKE ? OR p.descrizione LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Filtro per prezzo massimo
    if (!empty($maxPrice)) {
        $query .= " AND p.prezzo <= ?";
        $params[] = $maxPrice;
    }
    
    // Filtro per categorie
    if (!empty($categories)) {
        $placeholders = implode(',', array_fill(0, count($categories), '?'));
        $query .= " AND p.categoria IN ($placeholders)";
        $params = array_merge($params, $categories);
    }
    
    // Filtro per marche
    if (!empty($brands)) {
        $placeholders = implode(',', array_fill(0, count($brands), '?'));
        $query .= " AND p.marca IN ($placeholders)";
        $params = array_merge($params, $brands);
    }
    
    $query .= " ORDER BY p.id DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

// Funzione per ottenere un singolo prodotto con tutti i dettagli
function getProductDetails($productId) {
    global $pdo;
    
    // Ottieni i dati base del prodotto
    $stmt = $pdo->prepare("
        SELECT p.id, p.nome, p.descrizione, p.prezzo, p.prezzo_base, p.categoria, p.marca, p.immagine
        FROM prodotti p
        WHERE p.id = ?
    ");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        return null;
    }
    
    // Ottieni tutte le immagini del prodotto
    $stmt = $pdo->prepare("
        SELECT url FROM immagini_prodotto WHERE prodotto_id = ?
    ");
    $stmt->execute([$productId]);
    $product['immagini'] = array_column($stmt->fetchAll(), 'url');
    
    // Se non ci sono immagini aggiuntive, usa l'immagine principale
    if (empty($product['immagini'])) {
        $product['immagini'] = [$product['immagine']];
    }
    
    // Ottieni le specifiche base
    $stmt = $pdo->prepare("
        SELECT chiave, valore FROM specifiche_prodotto 
        WHERE prodotto_id = ? AND tipo = 'base'
    ");
    $stmt->execute([$productId]);
    $specifiche = $stmt->fetchAll();
    
    $product['specifiche'] = [];
    foreach ($specifiche as $spec) {
        $product['specifiche'][$spec['chiave']] = $spec['valore'];
    }
    
    // Ottieni le specifiche dettagliate
    $stmt = $pdo->prepare("
        SELECT chiave, valore FROM specifiche_prodotto 
        WHERE prodotto_id = ? AND tipo = 'dettagliata'
    ");
    $stmt->execute([$productId]);
    $specificheDettagliate = $stmt->fetchAll();
    
    $product['specifiche_dettagliate'] = [];
    foreach ($specificheDettagliate as $spec) {
        $product['specifiche_dettagliate'][$spec['chiave']] = $spec['valore'];
    }
    
    // Ottieni le varianti del prodotto
    $stmt = $pdo->prepare("
        SELECT tipo_variante, valore, prezzo_aggiuntivo 
        FROM varianti_prodotto 
        WHERE prodotto_id = ?
    ");
    $stmt->execute([$productId]);
    $varianti = $stmt->fetchAll();
    
    // Organizza le varianti per tipo
    $product['varianti'] = [];
    foreach ($varianti as $variante) {
        $tipo = $variante['tipo_variante'];
        $valore = $variante['valore'];
        $prezzo = $variante['prezzo_aggiuntivo'];
        
        if (!isset($product['varianti'][$tipo])) {
            $product['varianti'][$tipo] = [];
        }
        
        if ($tipo === 'potenza' || $tipo === 'capacita') {
            $product['varianti'][$tipo][$valore] = ['prezzo' => $product['prezzo_base'] + $prezzo];
        } else if ($tipo === 'taglia') {
            $product['varianti'][$tipo][$valore] = ['prezzo' => $product['prezzo_base'] + $prezzo];
        } else if ($tipo === 'colore') {
            $product['colori'][] = $valore;
        }
    }
    
    return $product;
}

// Funzione per ottenere le categorie disponibili
function getCategories() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT DISTINCT categoria FROM prodotti ORDER BY categoria");
    return array_column($stmt->fetchAll(), 'categoria');
}

// Funzione per ottenere le marche disponibili
function getBrands() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT DISTINCT marca FROM prodotti ORDER BY marca");
    return array_column($stmt->fetchAll(), 'marca');
}

// Gestione delle richieste API
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    
    switch ($_GET['action'] ?? '') {
        case 'all':
            $products = getAllProducts();
            echo json_encode(['success' => true, 'prodotti' => $products]);
            break;
            
        case 'filter':
            $search = $_GET['search'] ?? '';
            $maxPrice = isset($_GET['maxPrice']) ? floatval($_GET['maxPrice']) : null;
            $categories = isset($_GET['categories']) ? explode(',', $_GET['categories']) : [];
            $brands = isset($_GET['brands']) ? explode(',', $_GET['brands']) : [];
            
            $products = getFilteredProducts($search, $maxPrice, $categories, $brands);
            echo json_encode(['success' => true, 'prodotti' => $products]);
            break;
            
        case 'detail':
            $productId = $_GET['id'] ?? 0;
            $product = getProductDetails($productId);
            
            if ($product) {
                echo json_encode(['success' => true, 'prodotto' => $product]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Prodotto non trovato']);
            }
            break;
            
        case 'categories':
            $categories = getCategories();
            echo json_encode(['success' => true, 'categorie' => $categories]);
            break;
            
        case 'brands':
            $brands = getBrands();
            echo json_encode(['success' => true, 'marche' => $brands]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Azione non valida']);
    }
}
?>