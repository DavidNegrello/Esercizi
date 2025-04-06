<?php
// Connessione al database
require_once '../conf/db_config.php';

// Verifica che l'ID prodotto sia stato passato
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: catalogo.php');
    exit;
}

$prodotto_id = intval($_GET['id']);

// Query per recuperare le informazioni di base del prodotto
// Update your main product query
$prodotto = fetchOne("
    SELECT p.id, p.nome, p.marca_id, p.prezzo_base, c.nome AS categoria_nome, pd.descrizione,
           m.nome AS marca  /* Add this line to get the brand name */
    FROM prodotti p
    JOIN categorie c ON p.categoria_id = c.id
    LEFT JOIN prodotti_descrizioni pd ON p.id = pd.prodotto_id
    JOIN marche m ON p.marca_id = m.id  /* Add this join */
    WHERE p.id = ?
", "i", [$prodotto_id]);

// Se il prodotto non esiste, redirect alla pagina del catalogo
if (!$prodotto) {
    header('Location: catalogo.php');
    exit;
}

// Recupero le immagini del prodotto
$immagini = fetchAll("
    SELECT url, is_principale 
    FROM prodotti_immagini 
    WHERE prodotto_id = ?
    ORDER BY is_principale DESC
", "i", [$prodotto_id]);

// Recupero le varianti di colore disponibili
$varianti_colore = fetchAll("
    SELECT vc.id, c.nome, c.id AS colore_id
    FROM varianti_colore vc
    JOIN colori c ON vc.colore_id = c.id
    WHERE vc.prodotto_id = ?
", "i", [$prodotto_id]);

// Recupero le varianti di wattaggio disponibili (per PSU)
$varianti_wattaggio = fetchAll("
    SELECT id, wattaggio
    FROM varianti_wattaggio
    WHERE prodotto_id = ?
", "i", [$prodotto_id]);

// Recupero le capacità disponibili (per SSD)
$varianti_capacita = fetchAll("
    SELECT pc.capacita_id, c.nome, pc.prezzo
    FROM prodotti_capacita pc
    JOIN capacita c ON pc.capacita_id = c.id
    WHERE pc.prodotto_id = ?
", "i", [$prodotto_id]);

// Recupero le taglie disponibili (per RAM)
$varianti_taglie = fetchAll("
    SELECT pt.taglia_id, t.nome, t.descrizione, pt.prezzo
    FROM prodotti_taglie pt
    JOIN taglie t ON pt.taglia_id = t.id
    WHERE pt.prodotto_id = ?
", "i", [$prodotto_id]);

// Recupero le specifiche tecniche
$specifiche = fetchAll("
    SELECT s.nome, ps.valore
    FROM prodotti_specifiche ps
    JOIN specifiche s ON ps.specifica_id = s.id
    WHERE ps.prodotto_id = ?
", "i", [$prodotto_id]);

// Gestisci le selezioni dell'utente
$colore_selezionato = isset($_GET['colore']) ? intval($_GET['colore']) : (count($varianti_colore) > 0 ? $varianti_colore[0]['colore_id'] : null);
$wattaggio_selezionato = isset($_GET['wattaggio']) ? $_GET['wattaggio'] : (count($varianti_wattaggio) > 0 ? $varianti_wattaggio[0]['wattaggio'] : null);
$capacita_selezionata = isset($_GET['capacita']) ? intval($_GET['capacita']) : (count($varianti_capacita) > 0 ? $varianti_capacita[0]['capacita_id'] : null);
$taglia_selezionata = isset($_GET['taglia']) ? intval($_GET['taglia']) : (count($varianti_taglie) > 0 ? $varianti_taglie[0]['taglia_id'] : null);

// Calcola il prezzo finale in base alle varianti selezionate
$prezzo_finale = $prodotto['prezzo_base'];

// Aggiorna il prezzo se c'è una variante di capacità
if ($capacita_selezionata) {
    foreach ($varianti_capacita as $variante) {
        if ($variante['capacita_id'] == $capacita_selezionata) {
            $prezzo_finale = $variante['prezzo'];
            break;
        }
    }
}

// Aggiorna il prezzo se c'è una variante di taglia
if ($taglia_selezionata) {
    foreach ($varianti_taglie as $variante) {
        if ($variante['taglia_id'] == $taglia_selezionata) {
            $prezzo_finale = $variante['prezzo'];
            break;
        }
    }
}

// Recupera le immagini per la variante di colore selezionata
$immagini_variante = [];
if ($colore_selezionato) {
    $immagini_variante = fetchAll("
        SELECT vci.url
        FROM varianti_colore_immagini vci
        JOIN varianti_colore vc ON vci.variante_id = vc.id
        WHERE vc.prodotto_id = ? AND vc.colore_id = ?
    ", "ii", [$prodotto_id, $colore_selezionato]);
}

// Se ci sono immagini per la variante selezionata, usa quelle
if (count($immagini_variante) > 0) {
    $immagini = $immagini_variante;
}

// Recupera le immagini per la variante di wattaggio selezionata
if ($wattaggio_selezionato) {
    $immagini_wattaggio = fetchAll("
        SELECT vwi.url
        FROM varianti_wattaggio_immagini vwi
        JOIN varianti_wattaggio vw ON vwi.variante_id = vw.id
        WHERE vw.prodotto_id = ? AND vw.wattaggio = ?
    ", "is", [$prodotto_id, $wattaggio_selezionato]);

    // Se ci sono immagini per la variante di wattaggio, usa quelle
    if (count($immagini_wattaggio) > 0) {
        $immagini = $immagini_wattaggio;
    }
}

// Funzione per ottenere l'immagine principale
function getMainImage($images) {
    foreach ($images as $img) {
        if (isset($img['is_principale']) && $img['is_principale']) {
            return $img['url'];
        }
    }
    return count($images) > 0 ? $images[0]['url'] : 'img/placeholder.png';
}

$main_image = getMainImage($immagini);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($prodotto['nome']); ?> - Dettaglio Prodotto</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>


<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.html">Home</a></li>
            <li class="breadcrumb-item"><a href="catalogo.php">Catalogo</a></li>
            <li class="breadcrumb-item"><a href="catalogo.php?categoria=<?php echo $prodotto['categoria_nome']; ?>"><?php echo htmlspecialchars($prodotto['categoria_nome']); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($prodotto['nome']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Immagini prodotto -->
        <div class="col-md-6">
            <div class="product-images">
                <div class="main-image mb-3">
                    <img src="<?php echo htmlspecialchars($main_image); ?>" class="img-fluid" id="main-product-image" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>">
                </div>
                <div class="thumbnail-images d-flex overflow-auto">
                    <?php foreach ($immagini as $img): ?>
                        <div class="thumbnail me-2" data-image="<?php echo htmlspecialchars($img['url']); ?>">
                            <img src="<?php echo htmlspecialchars($img['url']); ?>" class="img-thumbnail" alt="Thumbnail">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Dettagli prodotto -->
        <div class="col-md-6">
            <h1><?php echo htmlspecialchars($prodotto['nome']); ?></h1>
            <p class="text-muted">Marca: <?php echo htmlspecialchars($prodotto['marca']); ?></p>

            <div class="price-container mb-4">
                <h2 class="price">€<?php echo number_format($prezzo_finale, 2, ',', '.'); ?></h2>
            </div>

            <form method="post" action="carrello.php">
                <input type="hidden" name="prodotto_id" value="<?php echo $prodotto_id; ?>">

                <!-- Selezione colore -->
                <?php if (count($varianti_colore) > 0): ?>
                    <div class="mb-3">
                        <label class="form-label">Colore:</label>
                        <div class="color-options">
                            <?php foreach ($varianti_colore as $variante): ?>
                                <a href="?id=<?php echo $prodotto_id; ?>&colore=<?php echo $variante['colore_id']; ?><?php echo $wattaggio_selezionato ? '&wattaggio=' . $wattaggio_selezionato : ''; ?><?php echo $capacita_selezionata ? '&capacita=' . $capacita_selezionata : ''; ?><?php echo $taglia_selezionata ? '&taglia=' . $taglia_selezionata : ''; ?>"
                                   class="color-option <?php echo $colore_selezionato == $variante['colore_id'] ? 'selected' : ''; ?>">
                                    <span class="color-box" style="background-color: <?php echo strtolower($variante['nome']); ?>"></span>
                                    <span class="color-name"><?php echo htmlspecialchars($variante['nome']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="colore_id" value="<?php echo $colore_selezionato; ?>">
                    </div>
                <?php endif; ?>

                <!-- Selezione wattaggio -->
                <?php if (count($varianti_wattaggio) > 0): ?>
                    <div class="mb-3">
                        <label class="form-label">Wattaggio:</label>
                        <div class="btn-group" role="group">
                            <?php foreach ($varianti_wattaggio as $variante): ?>
                                <a href="?id=<?php echo $prodotto_id; ?><?php echo $colore_selezionato ? '&colore=' . $colore_selezionato : ''; ?>&wattaggio=<?php echo urlencode($variante['wattaggio']); ?><?php echo $capacita_selezionata ? '&capacita=' . $capacita_selezionata : ''; ?><?php echo $taglia_selezionata ? '&taglia=' . $taglia_selezionata : ''; ?>"
                                   class="btn <?php echo $wattaggio_selezionato == $variante['wattaggio'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <?php echo htmlspecialchars($variante['wattaggio']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="wattaggio" value="<?php echo htmlspecialchars($wattaggio_selezionato); ?>">
                    </div>
                <?php endif; ?>

                <!-- Selezione capacità -->
                <?php if (count($varianti_capacita) > 0): ?>
                    <div class="mb-3">
                        <label class="form-label">Capacità:</label>
                        <div class="btn-group" role="group">
                            <?php foreach ($varianti_capacita as $variante): ?>
                                <a href="?id=<?php echo $prodotto_id; ?><?php echo $colore_selezionato ? '&colore=' . $colore_selezionato : ''; ?><?php echo $wattaggio_selezionato ? '&wattaggio=' . $wattaggio_selezionato : ''; ?>&capacita=<?php echo $variante['capacita_id']; ?><?php echo $taglia_selezionata ? '&taglia=' . $taglia_selezionata : ''; ?>"
                                   class="btn <?php echo $capacita_selezionata == $variante['capacita_id'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <?php echo htmlspecialchars($variante['nome']); ?> - €<?php echo number_format($variante['prezzo'], 2, ',', '.'); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="capacita_id" value="<?php echo $capacita_selezionata; ?>">
                    </div>
                <?php endif; ?>

                <!-- Selezione taglia -->
                <?php if (count($varianti_taglie) > 0): ?>
                    <div class="mb-3">
                        <label class="form-label">Configurazione:</label>
                        <div class="btn-group" role="group">
                            <?php foreach ($varianti_taglie as $variante): ?>
                                <a href="?id=<?php echo $prodotto_id; ?><?php echo $colore_selezionato ? '&colore=' . $colore_selezionato : ''; ?><?php echo $wattaggio_selezionato ? '&wattaggio=' . $wattaggio_selezionato : ''; ?><?php echo $capacita_selezionata ? '&capacita=' . $capacita_selezionata : ''; ?>&taglia=<?php echo $variante['taglia_id']; ?>"
                                   class="btn <?php echo $taglia_selezionata == $variante['taglia_id'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <?php echo htmlspecialchars($variante['nome']); ?> - €<?php echo number_format($variante['prezzo'], 2, ',', '.'); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="taglia_id" value="<?php echo $taglia_selezionata; ?>">
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantità:</label>
                    <input type="number" id="quantity" name="quantita" class="form-control" value="1" min="1" style="width: 100px;">
                </div>

                <button type="submit" name="aggiungi_carrello" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Aggiungi al carrello
                </button>
            </form>

            <!-- Descrizione prodotto -->
            <div class="product-description mt-4">
                <h3>Descrizione</h3>
                <p><?php echo nl2br(htmlspecialchars($prodotto['descrizione'])); ?></p>
            </div>
        </div>
    </div>

    <!-- Specifiche tecniche -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Specifiche tecniche</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                    <?php foreach ($specifiche as $specifica): ?>
                        <tr>
                            <th width="30%"><?php echo htmlspecialchars($specifica['nome']); ?></th>
                            <td><?php echo htmlspecialchars($specifica['valore']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Prodotti correlati -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Prodotti correlati</h3>
            <div class="row">
                <?php
                // Query per prodotti correlati (stessa categoria)
                $prodotti_correlati = fetchAll("
                     SELECT p.id, p.nome, m.nome AS marca, p.prezzo_base, pi.url AS immagine
    FROM prodotti p
    JOIN prodotti_immagini pi ON p.id = pi.prodotto_id
    JOIN marche m ON p.marca_id = m.id  /* Add this join */
    WHERE p.categoria_id = (SELECT categoria_id FROM prodotti WHERE id = ?)
    AND p.id != ?
    AND pi.is_principale = 1
    LIMIT 4
                ", "ii", [$prodotto_id, $prodotto_id]);

                foreach ($prodotti_correlati as $related):
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($related['immagine']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($related['nome']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($related['nome']); ?></h5>
                                <p class="card-text">€<?php echo number_format($related['prezzo_base'], 2, ',', '.'); ?></p>
                                <a href="dettaglio_catalogo.php?id=<?php echo $related['id']; ?>" class="btn btn-secondary">Visualizza</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>



<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script per cambiare l'immagine principale quando si clicca su una thumbnail
    document.addEventListener('DOMContentLoaded', function() {
        const thumbnails = document.querySelectorAll('.thumbnail');
        const mainImage = document.getElementById('main-product-image');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const imgSrc = this.dataset.image;
                mainImage.src = imgSrc;

                // Toglie la classe active da tutte le thumbnails e la aggiunge a quella cliccata
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>
</body>
</html>