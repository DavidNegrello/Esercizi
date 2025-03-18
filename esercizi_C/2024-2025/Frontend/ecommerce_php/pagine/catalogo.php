<?php
require_once '../conf_DB/operazioni.php'; // Connessione al database
require_once '../data/valori.json';


// Recupero i filtri dalla base di dati
$filtri = ReadFiltri();

// Esempio di come passare i filtri per leggere i prodotti
$filters = [
    'ricerca' => $_GET['ricerca'] ?? '',
    'categorie' => $_GET['categorie'] ?? [],
    'marche' => $_GET['marche'] ?? [],
    'prezzo_min' => $_GET['prezzo_min'] ?? 0,
    'prezzo_max' => $_GET['prezzo_max'] ?? 2000
];

// Recupero i prodotti in base ai filtri
$prodotti = ReadProdotti($filters);

// Verifica se ci sono prodotti
echo "<pre>";
print_r($prodotti);
echo "</pre>";


// Ora puoi visualizzare i prodotti
foreach ($prodotti as $prodotto) {
    echo "Nome: " . $prodotto['nome'] . "<br>";
    echo "Prezzo: €" . $prodotto['prezzo'] . "<br>";
    echo "Categoria: " . $prodotto['categoria'] . "<br>";
    echo "Marca: " . $prodotto['marca'] . "<br>";
    echo "<img src='" . $prodotto['immagine'] . "' alt='" . $prodotto['nome'] . "' /><br>";
}

?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Componenti - PC Componenti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../stili/home.css">
    <link rel="stylesheet" href="../stili/catalogo.css">
    <link rel="icon" type="image/png" href="../immagini/favicon_io/favicon.ico">
    <!--Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../index.html">
            <img src="../immagini/favicon_io/favicon.ico" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
PC Componenti
</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="../pagine/catalogo.php">Catalogo</a></li>
                <li class="nav-item"><a class="nav-link" href="../pagine/preassemblati.php">Preassemblati</a></li>
                <!-- Icona Carrello -->
                <li class="nav-item">
                    <a class="nav-link" href="../pagine/carrello.php"><i class="fas fa-shopping-cart"></i> Carrello</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Catalogo -->
<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <!-- Sidebar con filtri -->
        <aside id="sidebar" class="col-md-3 bg-light p-3">
            <h5>Filtri</h5>

            <?php foreach ($filtri as $filtro): ?>
                <?php if ($filtro['tipo_filtro'] == 'ricerca'): ?>
                    <input type="text" id="search-bar" class="form-control mb-3" placeholder="<?= htmlspecialchars($filtro['valori']) ?>">
                <?php elseif ($filtro['tipo_filtro'] == 'categorie'): ?>
                    <h6>Categorie</h6>
                    <ul id="category-list" class="list-unstyled">
                        <?php foreach (json_decode($filtro['valori'], true) as $categoria): ?>
                            <li><input type="checkbox" class="category-filter" value="<?= htmlspecialchars($categoria) ?>"> <?= htmlspecialchars($categoria) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif ($filtro['tipo_filtro'] == 'prezzo'): ?>
                    <?php $prezzo = json_decode($filtro['valori'], true); ?>
                    <h6>Prezzo</h6>
                    <input type="range" id="price-filter" class="form-range" min="<?= $prezzo['min'] ?>" max="<?= $prezzo['max'] ?>" step="<?= $prezzo['step'] ?>">
                    <span id="price-label"><?= $prezzo['min'] ?> - <?= $prezzo['max'] ?>€</span>
                <?php elseif ($filtro['tipo_filtro'] == 'marche'): ?>
                    <h6>Marca</h6>
                    <ul id="brand-list" class="list-unstyled">
                        <?php foreach (json_decode($filtro['valori'], true) as $marca): ?>
                            <li><input type="checkbox" class="brand-filter" value="<?= htmlspecialchars($marca) ?>"> <?= htmlspecialchars($marca) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endforeach; ?>
        </aside>

        <!-- Area prodotti -->
        <main class="col-md-9">
            <h2 class="text-center">Catalogo Prodotti</h2>
            <div id="catalogo-prodotti" class="row">
                <?php foreach ($prodotti as $prodotto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($prodotto['immagine'] ?: '../immagini/no-image.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($prodotto['nome']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($prodotto['nome']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($prodotto['categoria']) ?> - <?= htmlspecialchars($prodotto['marca']) ?></p>
                                <p class="card-text fw-bold"><?= htmlspecialchars($prodotto['prezzo']) ?>€</p>
                                <a href="dettaglio_catalogo.php?id=<?= $prodotto['id_prodotto'] ?>" class="btn btn-primary">Visualizza</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>




<footer class="bg-dark text-light pt-4 pb-2">
    <div class="container text-center">
        <!-- Social -->
        <h5>Seguici</h5>
        <div id="footer-social" class="mb-3"></div>

        <!-- Contatti -->
        <p>Email: <a id="footer-email" class="text-light text-decoration-none"></a></p>

        <!-- Copyright -->
        <p id="footer-copyright" class="mt-3 mb-0"></p>
    </div>
</footer>

<script>
    document.querySelectorAll('.category-filter, .brand-filter, #price-filter, #search-bar').forEach(element => {
        element.addEventListener('change', function() {
            // Ottieni i filtri selezionati
            let filters = {
                ricerca: document.getElementById('search-bar').value,
                categorie: Array.from(document.querySelectorAll('.category-filter:checked')).map(el => el.value),
                marche: Array.from(document.querySelectorAll('.brand-filter:checked')).map(el => el.value),
                prezzo_min: document.getElementById('price-filter').min,
                prezzo_max: document.getElementById('price-filter').max
            };

            // Crea la query string da inviare
            let queryString = new URLSearchParams(filters).toString();

            // Aggiungi la query string all'URL
            let url = 'catalogo.php?' + queryString;

            // Carica i prodotti filtrati con la nuova URL
            window.location.href = url;
        });
    });

    // Mantenere i filtri selezionati quando la pagina si ricarica
    window.addEventListener('load', function() {
        const filters = new URLSearchParams(window.location.search);

        // Ricerca
        if (filters.get('ricerca')) {
            document.getElementById('search-bar').value = filters.get('ricerca');
        }

        // Categorie
        const categorie = filters.getAll('categorie');
        document.querySelectorAll('.category-filter').forEach(checkbox => {
            checkbox.checked = categorie.includes(checkbox.value);
        });

        // Marche
        const marche = filters.getAll('marche');
        document.querySelectorAll('.brand-filter').forEach(checkbox => {
            checkbox.checked = marche.includes(checkbox.value);
        });

        // Prezzo
        const prezzoMin = filters.get('prezzo_min') || document.getElementById('price-filter').min;
        const prezzoMax = filters.get('prezzo_max') || document.getElementById('price-filter').max;
        document.getElementById('price-filter').value = prezzoMax;
        document.getElementById('price-label').textContent = prezzoMin + " - " + prezzoMax + "€";
    });



</script>

<script src="../js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
