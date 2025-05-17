<?php
// Include the database connection file
global $db;
require '../conf_DB/primodb.php';

// Page title
$pageTitle = "Elenco Visite Disponibili";

// Get all available visits/tours
$stmt = getVisite($db); // This returns a PDO statement
$visite = $stmt->fetchAll(PDO::FETCH_ASSOC); // Convert to associative array

// Count the total number of visits for statistics
$totaleVisite = getTotaleVisite($db);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artifex Turismo - <?php echo $pageTitle; ?></title>
    <!-- Bootstrap if you're using it -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome per le icone -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container mt-4">
    <h1><?php echo $pageTitle; ?></h1>

    <div class="row mb-4">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Statistiche</h5>
                    <p class="card-text">Totale visite disponibili: <strong><?php echo $totaleVisite; ?></strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Titolo</th>
                    <th>Durata Media (min)</th>
                    <th>Luogo</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($visite) > 0): ?>
                    <?php foreach ($visite as $visita): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($visita['id']); ?></td>
                            <td><?php echo htmlspecialchars($visita['titolo']); ?></td>
                            <td><?php echo htmlspecialchars($visita['durata_media']); ?></td>
                            <td><?php echo htmlspecialchars($visita['luogo']); ?></td>
                            <td>
                                <a href="dettaglio_visita.php?id=<?php echo $visita['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-info-circle"></i> Dettagli
                                </a>
                                <a href="eventi_visita.php?id_visita=<?php echo $visita['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-calendar"></i> Eventi
                                </a>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $visita['id']; ?>">
                                    <i class="fas fa-trash"></i> Elimina
                                </button>

                                <!-- Modal di conferma eliminazione -->
                                <div class="modal fade" id="deleteModal<?php echo $visita['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $visita['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel<?php echo $visita['id']; ?>">Conferma eliminazione</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Sei sicuro di voler eliminare la visita "<strong><?php echo htmlspecialchars($visita['titolo']); ?></strong>"?
                                                <p class="text-danger mt-2">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Attenzione: questa azione è irreversibile e cancellerà anche tutti gli eventi associati a questa visita.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                                <a href="elimina_visita.php?id=<?php echo $visita['id']; ?>&token=<?php echo md5(session_id()); ?>" class="btn btn-danger">
                                                    Conferma eliminazione
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Nessuna visita disponibile</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Link to add a new visit (if you have admin functionality) -->
    <div class="row mt-3">
        <div class="col">
            <a href="insert.php" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Aggiungi Nuova Visita
            </a>
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Torna alla Home
            </a>
        </div>
    </div>
</div>

<!-- Bootstrap JS if needed -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>