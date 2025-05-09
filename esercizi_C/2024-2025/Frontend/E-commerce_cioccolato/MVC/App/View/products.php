<?php
$title = 'Prodotti';
require 'header.php';
?>
<!-- Header con padding aggiuntivo sopra -->
<header class="product-header pt-5 pb-3"> <!-- Aggiunto pt-5 (padding-top) -->
    <h1>La Magia del Cioccolato</h1>
    <p>Scopri i nostri deliziosi prodotti artigianali al cioccolato</p>
</header>

<!-- Barra dei filtri con margine aggiuntivo -->
<div class="container mt-5 mb-4"> <!-- Aumentato mt-4 a mt-5 -->
    <div class="row">
        <div class="col-md-3">
            <div class="filter-section">
                <h4>Filtra i prodotti</h4>
                <form>
                    <div class="mb-3">
                        <label for="filterType" class="form-label">Tipo di Cioccolato</label>
                        <select class="form-select" id="filterType">
                            <option selected>Seleziona...</option>
                            <option value="fondente">Cioccolato Fondente</option>
                            <option value="latte">Cioccolato al Latte</option>
                            <option value="bianco">Cioccolato Bianco</option>
                            <option value="nocciole">Cioccolato con Nocciole</option>
                            <option value="caramello">Cioccolato al Caramello</option>
                            <option value="arancia">Cioccolato all'Arancia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filterPrice" class="form-label">Fascia di Prezzo</label>
                        <select class="form-select" id="filterPrice">
                            <option selected>Seleziona...</option>
                            <option value="5">€0 - €5</option>
                            <option value="10">€5 - €10</option>
                            <option value="15">€10 - €15</option>
                        </select>
                    </div>
                    <button type="submit" class="btn filter-btn w-100">Applica Filtri</button>
                </form>
            </div>
        </div>

        <div class="col-md-9">
            <div class="row g-4">
                <!-- Prodotti con margine superiore -->
                <div class="row row-gap-4 mt-3"> <!-- Aggiunto mt-3 -->
                    <?php foreach ($cioccolati as $cioccolato): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card product-card h-100"> <!-- Aggiunto h-100 per altezza uniforme -->
                                <?php 
                                $config = require 'appConfig.php';
                                $baseUrl = $config['baseURL'] . $config['prjName'];
                                
                                if (!empty($cioccolato['immagine'])): 
                                    $imgPath = htmlspecialchars($cioccolato['immagine']) . '.jpg';
                                ?>
                                    <img src="<?= $baseUrl . $imgPath ?>"
                                         class="card-img-top"
                                         alt="<?= htmlspecialchars($cioccolato['nome'] ?? '') ?>"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/300x200?text=<?= urlencode($cioccolato['nome'] ?? '') ?>';">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/300x200?text=<?= urlencode($cioccolato['nome'] ?? '') ?>"
                                         class="card-img-top"
                                         alt="<?= htmlspecialchars($cioccolato['nome'] ?? '') ?>">
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($cioccolato['nome'] ?? '') ?></h5>
                                    <p class="card-text flex-grow-1">
                                        <?php
                                        $descrizioni = [
                                            1 => "Fondente intenso con %d%% cacao, per veri amanti del cioccolato puro.",
                                            2 => "Crema al latte vellutata, perfetta per chi ama il gusto dolce e avvolgente.",
                                            3 => "Bianco cremoso e delicato, con note di vaniglia e latte.",
                                            4 => "Ruby dal colore naturale rosa, con un gusto fruttato unico.",
                                            5 => "Con aromi naturali che esaltano il gusto del cacao di alta qualità."
                                        ];
                                        $tipo_id = $cioccolato['tipo_id'] ?? 1;
                                        $percentuale = rand(60, 99);
                                        echo sprintf($descrizioni[$tipo_id] ?? "Cioccolato premium di alta qualità.", $percentuale);
                                        ?>
                                    </p>
                                    <p class="price fw-bold">€ <?= number_format($cioccolato['prezzo_vendita'] ?? 0, 2) ?></p>
                                    <a href="#" class="btn btn-add-to-cart align-self-start">
                                        <i class="bi bi-cart-plus"></i> Aggiungi al carrello
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require 'footer.php';
?>
