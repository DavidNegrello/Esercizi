<?php

/*
 * // In un altro file PHP dove si verifica l'errore
header("Location: error_page.php?code=Errore 404&message=La pagina richiesta non è stata trovata.");
exit;
 */
// Imposta il titolo per la pagina
$title = "Errore - Dolce Vita Cioccolato";

// Prima includere le variabili di errore
$error_message = isset($_GET['message']) ? $_GET['message'] : "C'è stato un problema durante l'elaborazione della richiesta.";
$error_code = isset($_GET['code']) ? $_GET['code'] : "Errore";

// Poi includere l'header che contiene già i tag iniziali HTML, head, ecc.
require 'header.php';
?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow border-0 text-center">
                    <div class="card-body p-5">
                        <div class="display-1 text-danger mb-4">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <h1 class="h3 text-danger mb-3"><?php echo $error_code; ?></h1>
                        <p class="lead mb-4"><?php echo $error_message; ?></p>
                        <p class="text-muted mb-4">Ci scusiamo per l'inconveniente. Ti preghiamo di riprovare più tardi o contattare l'assistenza se il problema persiste.</p>
                        <a href="<?=$homePage?>" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-house-door me-2"></i>Torna alla Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
// Include il footer alla fine
require 'footer.php';
?>