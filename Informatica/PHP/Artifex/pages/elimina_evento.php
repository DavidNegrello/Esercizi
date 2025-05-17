<?php
// Includo il file di configurazione del database
global $db;
require_once '../conf_DB/primodb.php';

// Verifico che l'ID sia stato passato
if (empty($_GET['id'])) {
    // Reindirizzo all'indice con un messaggio di errore
    header('Location: ../index.php?errore=id_mancante');
    exit;
}

$id_evento = intval($_GET['id']);

// Preparo ed eseguo la query per eliminare l'evento
try {
    $sql = "DELETE FROM eventi WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id_evento, PDO::PARAM_INT);
    $result = $stmt->execute();

    if ($result && $stmt->rowCount() > 0) {
        // Reindirizzo all'indice con un messaggio di successo
        header('Location: ../index.php?success=evento_eliminato');
    } else {
        // Reindirizzo all'indice con un messaggio di errore
        header('Location: ../index.php?errore=evento_non_trovato');
    }
} catch (PDOException $e) {
    // In caso di errore del database
    header('Location: ../index.php?errore=database&message=' . urlencode($e->getMessage()));
}

exit;
?>