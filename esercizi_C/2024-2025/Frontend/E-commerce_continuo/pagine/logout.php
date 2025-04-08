<?php
// Avvia la sessione se non è già avviata
session_start();

// Cancella tutte le variabili di sessione
$_SESSION = array();

// Se si sta usando un cookie di sessione, lo distrugge
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distrugge la sessione
session_destroy();

// Reindirizza alla pagina principale con un messaggio di logout
header("Location: ../index.php?logout=success");
exit();
?>
