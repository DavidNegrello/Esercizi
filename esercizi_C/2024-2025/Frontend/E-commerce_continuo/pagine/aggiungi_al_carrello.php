<?php
/**
 * aggiungi_al_carrello.php
 *
 * Script per elaborare l'aggiunta di un prodotto al carrello.
 */

// Avvia la sessione
session_start();

// Inclusione del file di configurazione del database
require_once '../conf/db_config.php';

// Verifica che ci sia una richiesta POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verifica se è stato fornito un ID valido
    if (!isset($_POST['prodotto_id']) || !is_numeric($_POST['prodotto_id'])) {
        // Reindirizza con messaggio di errore
        header("Location: ../pagine/preassemblati.php?errore=id_non_valido");
        exit;
    }

    $prodottoId = (int)$_POST['prodotto_id'];
    $tipo = isset($_POST['tipo']) ? sanitizeInput($_POST['tipo']) : 'preassemblato';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $colore = isset($_POST['colore']) ? sanitizeInput($_POST['colore']) : null;

    // Validazione della quantità
    if ($quantity <= 0) {
        $quantity = 1;
    }

    // Recupero le personalizzazioni selezionate
    $personalizzazioni = isset($_POST['personalizzazioni']) && is_array($_POST['personalizzazioni']) ?
        array_map('intval', $_POST['personalizzazioni']) : [];

    // Verifica che il prodotto esista nel database
    if ($tipo === 'preassemblato') {
        $prodotto = fetchOne(
            "SELECT id, nome, prezzo FROM preassemblati WHERE id = ?",
            'i',
            [$prodottoId]
        );
    } else if ($tipo === 'componente') {
        // Per componenti singoli
        $prodotto = fetchOne(
            "SELECT id, nome, prezzo FROM componenti WHERE id = ?",
            'i',
            [$prodottoId]
        );
    }

    if (!$prodotto) {
        header("Location: ../pagine/preassemblati.php?errore=prodotto_non_trovato");
        exit;
    }

    // Inizializza l'array del carrello se non esiste
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Crea una chiave univoca che includa eventuali personalizzazioni e colore
    $itemKey = $prodottoId;

    // L'articolo è già nel carrello con le stesse personalizzazioni e colore?
    $itemExists = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($key == $prodottoId &&
            $item['tipo'] == $tipo &&
            $item['colore'] == $colore &&
            (!empty($personalizzazioni) && !empty($item['personalizzazioni'])) &&
            count(array_diff($personalizzazioni, $item['personalizzazioni'])) == 0 &&
            count(array_diff($item['personalizzazioni'], $personalizzazioni)) == 0) {

            // Aggiorna solo la quantità
            $_SESSION['cart'][$key]['quantity'] += $quantity;
            $itemExists = true;
            break;
        }
    }

    // Se l'articolo non esiste nel carrello o ha personalizzazioni diverse, aggiungilo
    if (!$itemExists) {
        $_SESSION['cart'][$itemKey] = [
            'tipo' => $tipo,
            'quantity' => $quantity,
            'colore' => $colore,
            'personalizzazioni' => $personalizzazioni
        ];
    }

    // Calcola il prezzo totale con personalizzazioni
    $prezzoTotale = $prodotto['prezzo'];
    if (!empty($personalizzazioni)) {
        foreach ($personalizzazioni as $persId) {
            $personalizzazione = fetchOne(
                "SELECT prezzo FROM preassemblati_personalizzazioni WHERE id = ?",
                'i',
                [$persId]
            );
            if ($personalizzazione) {
                $prezzoTotale += $personalizzazione['prezzo'];
            }
        }
    }

    // Prepara il messaggio di conferma
    $messaggioSuccesso = "Prodotto aggiunto al carrello. Prezzo totale: €" .
        number_format($prezzoTotale * $quantity, 2, ',', '.');

    // Reindirizza alla pagina del carrello con il messaggio di successo
    header("Location: ../pagine/carrello.php?messaggio=" . urlencode($messaggioSuccesso));
    exit;

} else {
    // Se non è una richiesta POST, reindirizza alla home
    header("Location: ../index.php");
    exit;
}
