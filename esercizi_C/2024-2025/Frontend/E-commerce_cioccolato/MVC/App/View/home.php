<?php
$title='Dolce Vita Cioccolato';
require 'header.php';
?>
    <!-- Hero Section -->
    <section class="hero text-center text-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold playfair mb-4">Il paradiso del cioccolato artigianale</h1>
                    <p class="lead mb-5">Scopri la nostra selezione esclusiva di cioccolato artigianale italiano, realizzato con ingredienti di prima qualità e passione per l'eccellenza.</p>
                    <a href="#" class="btn btn-primary btn-lg px-5 py-3">Scopri i nostri prodotti</a>
                </div>
            </div>
        </div>
    </section>
    <!-- Features Section -->
    <section class="py-5">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-flower1"></i>
                        </div>
                        <h3 class="h4 mb-3">Ingredienti naturali</h3>
                        <p class="text-muted mb-0">Utilizziamo solo ingredienti naturali e sostenibili provenienti da coltivazioni etiche e certificate.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h3 class="h4 mb-3">Spedizione rapida</h3>
                        <p class="text-muted mb-0">Consegna gratuita in tutta Italia per ordini superiori a €50 e packaging speciale termico.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-award"></i>
                        </div>
                        <h3 class="h4 mb-3">Qualità premiata</h3>
                        <p class="text-muted mb-0">I nostri maestri cioccolatieri hanno ricevuto numerosi riconoscimenti internazionali.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5" style="background-color: var(--accent-bg);">
        <div class="container py-4">
            <h2 class="text-center display-5 playfair mb-5">Le nostre categorie</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="category-card h-100">
                        <img src="/api/placeholder/400/300" alt="Tavolette di cioccolato" class="img-fluid w-100 h-100 object-fit-cover">
                        <div class="category-overlay">Tavolette</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="category-card h-100">
                        <img src="/api/placeholder/400/300" alt="Praline" class="img-fluid w-100 h-100 object-fit-cover">
                        <div class="category-overlay">Praline</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="category-card h-100">
                        <img src="/api/placeholder/400/300" alt="Cioccolata calda" class="img-fluid w-100 h-100 object-fit-cover">
                        <div class="category-overlay">Cioccolata calda</div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="category-card h-100">
                        <img src="/api/placeholder/400/300" alt="Confezioni regalo" class="img-fluid w-100 h-100 object-fit-cover">
                        <div class="category-overlay">Confezioni regalo</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bestsellers Section -->
    <section class="py-5">
        <div class="container py-4">
            <h2 class="text-center display-5 playfair mb-5">I più venduti</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="/api/placeholder/400/300" alt="Tavoletta fondente 75%" class="card-img-top">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Tavoletta fondente 75%</h5>
                            <p class="card-text text-muted flex-grow-1">Un classico intramontabile, intenso e aromatico.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">€6.90</span>
                                <button class="btn btn-sm" style="background-color: var(--primary); color: white;">
                                    <i class="bi bi-cart-plus"></i> Aggiungi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="/api/placeholder/400/300" alt="Praline assortite" class="card-img-top">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Praline assortite</h5>
                            <p class="card-text text-muted flex-grow-1">Selezione di 12 praline con diversi ripieni.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">€18.50</span>
                                <button class="btn btn-sm" style="background-color: var(--primary); color: white;">
                                    <i class="bi bi-cart-plus"></i> Aggiungi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="/api/placeholder/400/300" alt="Cioccolato al pistacchio" class="card-img-top">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Cioccolato al pistacchio</h5>
                            <p class="card-text text-muted flex-grow-1">Cioccolato bianco con crema di pistacchio siciliano.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">€8.90</span>
                                <button class="btn btn-sm" style="background-color: var(--primary); color: white;">
                                    <i class="bi bi-cart-plus"></i> Aggiungi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="/api/placeholder/400/300" alt="Box degustazione" class="card-img-top">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Box degustazione</h5>
                            <p class="card-text text-muted flex-grow-1">Set di 4 mini tavolette con diverse percentuali di cacao.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">€14.90</span>
                                <button class="btn btn-sm" style="background-color: var(--primary); color: white;">
                                    <i class="bi bi-cart-plus"></i> Aggiungi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="<?=$productPage?>" class="btn btn-outline-primary btn-lg px-5">Vedi tutti i prodotti</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials py-5" style="color: white;">
        <div class="container py-4">
            <h2 class="text-center display-5 playfair mb-5">Cosa dicono i nostri clienti</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card card h-100 p-4">
                        <div class="card-body">
                            <i class="bi bi-quote display-6 mb-3"></i>
                            <p class="card-text" style="color: white;">Il miglior cioccolato che abbia mai assaggiato! La tavoletta fondente 85% è semplicemente sublime.</p>
                            <div class="d-flex align-items-center mt-4">
                                <img src="/api/placeholder/50/50" alt="Cliente" class="rounded-circle me-3" width="50" height="50">
                                <div>
                                    <h6 class="mb-0">Marco B.</h6>
                                    <small>Cliente dal 2023</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card card h-100 p-4">
                        <div class="card-body">
                            <i class="bi bi-quote display-6 mb-3"></i>
                            <p class="card-text" style="color: white;">Ho regalato una confezione delle praline assortite e sono state un successo! Packaging elegante e gusto eccezionale.</p>
                            <div class="d-flex align-items-center mt-4">
                                <img src="/api/placeholder/50/50" alt="Cliente" class="rounded-circle me-3" width="50" height="50">
                                <div>
                                    <h6 class="mb-0">Giulia M.</h6>
                                    <small>Cliente dal 2022</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card card h-100 p-4">
                        <div class="card-body">
                            <i class="bi bi-quote display-6 mb-3"></i>
                            <p class="card-text" style="color: white;">La spedizione è stata velocissima e il cioccolato è arrivato in perfette condizioni nonostante il caldo. Servizio impeccabile!</p>
                            <div class="d-flex align-items-center mt-4">
                                <img src="/api/placeholder/50/50" alt="Cliente" class="rounded-circle me-3" width="50" height="50">
                                <div>
                                    <h6 class="mb-0">Roberto L.</h6>
                                    <small>Cliente dal 2024</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter py-5">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <h2 class="playfair mb-4">Resta aggiornato</h2>
                    <p class="mb-4">Iscriviti alla nostra newsletter per ricevere offerte esclusive, nuovi prodotti e ricette a base di cioccolato.</p>
                    <form class="row g-3 justify-content-center">
                        <div class="col-8">
                            <input type="email" class="form-control form-control-lg" placeholder="La tua email" required>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary w-100 h-100">Iscriviti</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php
require 'footer.php';
?>