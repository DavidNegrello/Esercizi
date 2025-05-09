<?php
$title = 'Checkout';
require 'header.php';
session_start();
$subtotale = 0;
$spedizione = 5.90; // Costo base spedizione
$sconto = 0.00; // Sconto fisso (potresti renderlo dinamico)

?>

    <!-- Hero Section Modificata -->
    <section class="hero-small text-center" style="background-color: #5a3921; padding: 4rem 0;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold playfair mb-4 text-white">Il tuo ordine</h1>
                    <p class="lead mb-5 text-white">Rivedi i tuoi prodotti e completa l'acquisto</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cart & Checkout Section -->
    <section class="py-5">
        <div class="container py-4">
            <div class="row">
                <!-- Cart Items Column -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h3 class="playfair mb-4">I tuoi prodotti</h3>

                            <!-- Cart Items -->
                            <?php if (!empty($_SESSION['carrello'])): ?>
                                <?php foreach ($_SESSION['carrello'] as $prodotto): ?>
                                    <div class="cart-item border-bottom py-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <img src="<?= htmlspecialchars($prodotto['img']) ?>"
                                                     alt="<?= htmlspecialchars($prodotto['nome']) ?>"
                                                     class="img-fluid rounded">
                                            </div>
                                            <div class="col-md-4">
                                                <h5 class="mb-1"><?= htmlspecialchars($prodotto['nome']) ?></h5>
                                                <p class="text-muted mb-0"><?= htmlspecialchars($prodotto['descrizione']) ?></p>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group quantity-selector">
                                                    <button class="btn btn-outline-secondary minus-btn" type="button">
                                                        -
                                                    </button>
                                                    <input type="text" class="form-control text-center quantity-input"
                                                           value="<?= htmlspecialchars($prodotto['quantita']) ?>">
                                                    <button class="btn btn-outline-secondary plus-btn" type="button">+
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <span class="product-price">€<?= number_format($prodotto['prezzo'], 2) ?></span>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button class="btn btn-link text-danger remove-btn"
                                                        data-id="<?= htmlspecialchars($prodotto['id']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-info">Il carrello è vuoto</div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="<?= $productPage ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Continua lo shopping
                                </a>
                                <button class="btn btn-light">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Aggiorna carrello
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping & Payment Forms -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h3 class="playfair mb-4">Dati di spedizione</h3>

                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label">Nome</label>
                                        <input type="text" class="form-control" id="firstName" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">Cognome</label>
                                        <input type="text" class="form-control" id="lastName" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="address" class="form-label">Indirizzo</label>
                                        <input type="text" class="form-control" id="address"
                                               placeholder="Via e numero civico" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="city" class="form-label">Città</label>
                                        <input type="text" class="form-control" id="city" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="state" class="form-label">Provincia</label>
                                        <input type="text" class="form-control" id="state" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="zip" class="form-label">CAP</label>
                                        <input type="text" class="form-control" id="zip" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Telefono</label>
                                        <input type="tel" class="form-control" id="phone">
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="save-info">
                                            <label class="form-check-label" for="save-info">
                                                Salva queste informazioni per la prossima volta
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="playfair mb-4">Metodo di pagamento</h3>

                            <div class="payment-methods">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard"
                                           checked>
                                    <label class="form-check-label" for="creditCard">
                                        <i class="bi bi-credit-card fs-5 me-2"></i> Carta di credito
                                    </label>
                                </div>

                                <div class="credit-card-form">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="cardNumber" class="form-label">Numero carta</label>
                                            <input type="text" class="form-control" id="cardNumber"
                                                   placeholder="1234 5678 9012 3456">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="cardName" class="form-label">Intestatario</label>
                                            <input type="text" class="form-control" id="cardName"
                                                   placeholder="Nome come sulla carta">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="cardExpiry" class="form-label">Scadenza</label>
                                            <input type="text" class="form-control" id="cardExpiry" placeholder="MM/AA">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="cardCvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="cardCvv" placeholder="123">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="paypal">
                                    <label class="form-check-label" for="paypal">
                                        <i class="bi bi-paypal fs-5 me-2"></i> PayPal
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="bankTransfer">
                                    <label class="form-check-label" for="bankTransfer">
                                        <i class="bi bi-bank fs-5 me-2"></i> Bonifico bancario
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Column Modificato -->
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-bottom" style="top: 10%;">
                        <div class="card-body">
                            <h3 class="playfair mb-4">Riepilogo ordine</h3>
                            <?php if (!empty($_SESSION['carrello'])): ?>
                                <div class="order-summary-items">
                                    <?php
                                    foreach ($_SESSION['carrello'] as $prodotto):
                                        $totaleProdotto = $prodotto['prezzo'] * $prodotto['quantita'];
                                        $subtotale += $totaleProdotto;
                                        ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span><?= htmlspecialchars($prodotto['nome']) ?> x<?= $prodotto['quantita'] ?></span>
                                            <span>€<?= number_format($totaleProdotto, 2) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">Il carrello è vuoto</div>
                            <?php endif; ?>

                            <div class="border-top pt-3 mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotale</span>
                                    <span>€<?= number_format($subtotale, 2) ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Spedizione</span>
                                    <span>
                        <?php
                        // Spedizione gratuita sopra 50€
                        if ($subtotale > 50) {
                            echo '<span class="text-success">Gratuita</span>';
                            $spedizione = 0;
                        } else {
                            echo '€' . number_format($spedizione, 2);
                        }
                        ?>
                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Sconto</span>
                                    <span class="text-success">-€<?= number_format($sconto, 2) ?></span>
                                </div>

                                <div class="border-top pt-3 mb-4">
                                    <?php
                                    $totale = $subtotale + $spedizione - $sconto;
                                    $mancanti = max(0, 50 - $subtotale); // Calcolo per spedizione gratuita
                                    ?>
                                    <div class="d-flex justify-content-between fw-bold fs-5">
                                        <span>Totale</span>
                                        <span>€<?= number_format($totale, 2) ?></span>
                                    </div>
                                    <small class="text-muted d-block">IVA inclusa</small>
                                </div>

                                <div class="mb-3">
                                    <label for="coupon" class="form-label">Codice promozionale</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="coupon"
                                               placeholder="Inserisci codice">
                                        <button class="btn btn-outline-secondary" type="button">Applica</button>
                                    </div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label" for="termsCheck">
                                        Accetto i <a href="#">Termini e condizioni</a> e l'<a href="#">Informativa sulla
                                            privacy</a>
                                    </label>
                                </div>

                                <button class="btn btn-primary w-100 py-3 mt-2">
                                    Completa l'ordine <i class="bi bi-arrow-right ms-2"></i>
                                </button>

                                <?php if ($subtotale < 50): ?>
                                    <div class="mt-3 text-center">
                                        <small class="text-muted">Spedizione gratuita per ordini superiori a €50</small>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: <?= min(100, ($subtotale / 50) * 100) ?>%;"
                                                 aria-valuenow="<?= ($subtotale / 50) * 100 ?>"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="text-muted">Mancano €<?= number_format($mancanti, 2) ?> per la
                                            spedizione gratuita</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="bi bi-shield-lock me-2"></i>Pagamento sicuro</h5>
                        <p class="small text-muted mb-3">Tutti i pagamenti vengono elaborati in modo sicuro. Non
                            memorizziamo i dettagli della tua carta di credito.</p>
                        <div class="d-flex justify-content-between">
                            <img src="/api/placeholder/50/30" alt="Visa" class="img-fluid" style="height: 30px;">
                            <img src="/api/placeholder/50/30" alt="Mastercard" class="img-fluid" style="height: 30px;">
                            <img src="/api/placeholder/50/30" alt="PayPal" class="img-fluid" style="height: 30px;">
                            <img src="/api/placeholder/50/30" alt="Apple Pay" class="img-fluid" style="height: 30px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

<?php
require 'footer.php';
?>