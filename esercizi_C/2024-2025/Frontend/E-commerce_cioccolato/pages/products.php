<?php
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/product.css">
    <title>Products</title>
</head>
<body>
<!-- Header/Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Dolce Vita Cioccolato</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Prodotti</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Chi Siamo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contatti</a>
                </li>
            </ul>
            <div class="d-flex">
                <a href="../pages/login.php" class="btn btn-outline-light me-2">Login</a>
                <a href="#" class="btn btn-outline-light"><i class="bi bi-cart3"></i> <span class="badge bg-secondary">0</span></a>
            </div>
        </div>
    </div>
</nav>
<!-- Header -->
<header class="product-header">
    <h1>La Magia del Cioccolato</h1>
    <p>Scopri i nostri deliziosi prodotti artigianali al cioccolato</p>
</header>

<!-- Barra dei filtri -->
<div class="container mt-4">
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
                <!-- Prodotto 1 -->
                <div class="col-md-4">
                    <div class="card product-card">
                        <img src="https://via.placeholder.com/300x200?text=Cioccolato+Fondente" alt="Cioccolato Fondente">
                        <div class="card-body">
                            <h5 class="card-title">Cioccolato Fondente</h5>
                            <p class="card-text">Un cioccolato fondente ricco di gusto, perfetto per gli amanti del cioccolato intenso.</p>
                            <p><strong>€5,00</strong></p>
                            <a href="#" class="btn">Aggiungi al carrello</a>
                        </div>
                    </div>
                </div>

                <!-- Prodotto 2 -->
                <div class="col-md-4">
                    <div class="card product-card">
                        <img src="../img/spongebob.jfif" alt="Cioccolato al Latte">
                        <div class="card-body">
                            <h5 class="card-title">Cioccolato al Latte</h5>
                            <p class="card-text">Un morbido cioccolato al latte, ideale per ogni momento della giornata.</p>
                            <p><strong>€4,50</strong></p>
                            <a href="#" class="btn">Aggiungi al carrello</a>
                        </div>
                    </div>
                </div>

                <!-- Prodotto 3 -->
                <div class="col-md-4">
                    <div class="card product-card">
                        <img src="https://via.placeholder.com/300x200?text=Cioccolato+Bianco" alt="Cioccolato Bianco">
                        <div class="card-body">
                            <h5 class="card-title">Cioccolato Bianco</h5>
                            <p class="card-text">Cioccolato bianco ricco e cremoso, ideale per chi ama il dolce più delicato.</p>
                            <p><strong>€6,00</strong></p>
                            <a href="#" class="btn">Aggiungi al carrello</a>
                        </div>
                    </div>
                </div>

                <!-- Aggiungi altri prodotti in modo simile... -->
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="pt-5">
    <div class="container pb-4">
        <div class="row g-4">
            <div class="col-lg-4 mb-4">
                <h3 class="h5 mb-4">Dolce Vita Cioccolato</h3>
                <p class="mb-4">Cioccolato artigianale italiano dal 2010. Passione e qualità in ogni morso.</p>
                <div class="social-icons">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            <div class="col-md-4 col-lg-2 mb-4">
                <h3 class="h5 mb-4">Acquisti</h3>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="#">Tutti i prodotti</a></li>
                    <li class="nav-item mb-2"><a href="#">Novità</a></li>
                    <li class="nav-item mb-2"><a href="#">Bestseller</a></li>
                    <li class="nav-item mb-2"><a href="#">Offerte</a></li>
                    <li class="nav-item mb-2"><a href="#">Confezioni regalo</a></li>
                </ul>
            </div>
            <div class="col-md-4 col-lg-2 mb-4">
                <h3 class="h5 mb-4">Informazioni</h3>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="#">Chi siamo</a></li>
                    <li class="nav-item mb-2"><a href="#">I nostri ingredienti</a></li>
                    <li class="nav-item mb-2"><a href="#">Processo produttivo</a></li>
                    <li class="nav-item mb-2"><a href="#">Blog</a></li>
                    <li class="nav-item mb-2"><a href="#">Contatti</a></li>
                </ul>
            </div>
            <div class="col-md-4 col-lg-4 mb-4">
                <h3 class="h5 mb-4">Supporto</h3>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="#">FAQ</a></li>
                    <li class="nav-item mb-2"><a href="#">Spedizioni</a></li>
                    <li class="nav-item mb-2"><a href="#">Resi</a></li>
                    <li class="nav-item mb-2"><a href="#">Privacy Policy</a></li>
                    <li class="nav-item mb-2"><a href="#">Termini e condizioni</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom py-3">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Dolce Vita Cioccolato. Tutti i diritti riservati.</p>
        </div>
    </div>
</footer>>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

