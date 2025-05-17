<?php
// Includo il file di configurazione del database
global $db;
require_once '../conf_DB/primodb.php';
session_start();

// Ottieni l'utente corrente se loggato
$currentUser = isLoggedIn() ? getCurrentUser($db) : null;

// Verifica se l'utente è loggato e ha i permessi di admin
if (!isLoggedIn() || !isAdmin($currentUser)) {
    header("Location: ../index.php?login=required");
    exit();
}


// Funzione per pulire gli input
function cleanInput($data): string
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Array per memorizzare i messaggi
$messages = [];

// Gestione del form per l'inserimento di una visita
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_visita"])) {
    $titolo = cleanInput($_POST["titolo"]);
    $durata = intval($_POST["durata"]);
    $luogo = cleanInput($_POST["luogo"]);

    if (empty($titolo) || empty($durata) || empty($luogo)) {
        $messages['visita'] = "Errore: Tutti i campi sono obbligatori!";
    } else {
        try {
            $sql = "INSERT INTO visite (titolo, durata_media, luogo) VALUES (:titolo, :durata, :luogo)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':titolo', $titolo);
            $stmt->bindParam(':durata', $durata, PDO::PARAM_INT);
            $stmt->bindParam(':luogo', $luogo);

            if ($stmt->execute()) {
                $messages['visita'] = "Visita inserita con successo!";
            }
        } catch (PDOException $e) {
            $messages['visita'] = "Errore durante l'inserimento: " . $e->getMessage();
        }
    }
}

// Gestione del form per l'inserimento di una guida
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_guida"])) {
    $cognome = cleanInput($_POST["cognome"]);
    $nome = cleanInput($_POST["nome"]);
    $data_nascita = cleanInput($_POST["data_nascita"]);
    $luogo_nascita = cleanInput($_POST["luogo_nascita"]);
    $titolo_studio = cleanInput($_POST["titolo_studio"]);

    if (empty($cognome) || empty($nome) || empty($data_nascita) || empty($luogo_nascita) || empty($titolo_studio)) {
        $messages['guida'] = "Errore: Tutti i campi sono obbligatori!";
    } else {
        try {
            $db->beginTransaction();

            $sql = "INSERT INTO guide (cognome, nome, data_nascita, luogo_nascita, titolo_studio) VALUES (:cognome, :nome, :data_nascita, :luogo_nascita, :titolo_studio)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':cognome', $cognome);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':data_nascita', $data_nascita);
            $stmt->bindParam(':luogo_nascita', $luogo_nascita);
            $stmt->bindParam(':titolo_studio', $titolo_studio);

            if ($stmt->execute()) {
                $guida_id = $db->lastInsertId();
                $messages['guida'] = "Guida inserita con successo!";

                // Se sono state specificate delle lingue, le inseriamo
                if (isset($_POST["lingue"]) && isset($_POST["livelli"]) && !empty($_POST["lingue"][0])) {
                    $lingue = $_POST["lingue"];
                    $livelli = $_POST["livelli"];

                    for ($i = 0; $i < count($lingue); $i++) {
                        if (!empty($lingue[$i]) && !empty($livelli[$i])) {
                            $sql_lingua = "INSERT INTO lingue_guide (id_guida, lingua, livello_competenza) VALUES (:id_guida, :lingua, :livello)";
                            $stmt_lingua = $db->prepare($sql_lingua);
                            $stmt_lingua->bindParam(':id_guida', $guida_id, PDO::PARAM_INT);
                            $stmt_lingua->bindParam(':lingua', $lingue[$i]);
                            $stmt_lingua->bindParam(':livello', $livelli[$i]);
                            $stmt_lingua->execute();
                        }
                    }
                }

                $db->commit();
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $messages['guida'] = "Errore durante l'inserimento: " . $e->getMessage();
        }
    }
}

// Gestione del form per l'inserimento di un evento
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_evento"])) {
    $id_visita = intval($_POST["id_visita"]);
    $prezzo = floatval($_POST["prezzo"]);
    $ora_inizio = cleanInput($_POST["ora_inizio"]);
    $num_minimo = intval($_POST["num_minimo"]);
    $num_massimo = intval($_POST["num_massimo"]);
    $id_guida = intval($_POST["id_guida"]);

    if (empty($id_visita) || empty($prezzo) || empty($ora_inizio) || empty($num_minimo) || empty($num_massimo) || empty($id_guida)) {
        $messages['evento'] = "Errore: Tutti i campi sono obbligatori!";
    } else {
        try {
            $sql = "INSERT INTO eventi (id_visita, prezzo, ora_inizio, num_minimo_partecipanti, num_massimo_partecipanti, id_guida) 
                    VALUES (:id_visita, :prezzo, :ora_inizio, :num_minimo, :num_massimo, :id_guida)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_visita', $id_visita, PDO::PARAM_INT);
            $stmt->bindParam(':prezzo', $prezzo);
            $stmt->bindParam(':ora_inizio', $ora_inizio);
            $stmt->bindParam(':num_minimo', $num_minimo, PDO::PARAM_INT);
            $stmt->bindParam(':num_massimo', $num_massimo, PDO::PARAM_INT);
            $stmt->bindParam(':id_guida', $id_guida, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $messages['evento'] = "Evento inserito con successo!";
            }
        } catch (PDOException $e) {
            $messages['evento'] = "Errore durante l'inserimento: " . $e->getMessage();
        }
    }
}

// Gestione del form per l'inserimento di un turista
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_turista"])) {
    $nome = cleanInput($_POST["nome_turista"]);
    $nazionalita = cleanInput($_POST["nazionalita"]);
    $lingua_base = cleanInput($_POST["lingua_base"]);
    $email = cleanInput($_POST["email"]);
    $telefono = cleanInput($_POST["telefono"]);

    if (empty($nome) || empty($nazionalita) || empty($lingua_base) || empty($email) || empty($telefono)) {
        $messages['turista'] = "Errore: Tutti i campi sono obbligatori!";
    } else {
        try {
            $sql = "INSERT INTO Turisti (nome, nazionalita, lingua_base, email, telefono) 
                    VALUES (:nome, :nazionalita, :lingua_base, :email, :telefono)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':nazionalita', $nazionalita);
            $stmt->bindParam(':lingua_base', $lingua_base);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);

            if ($stmt->execute()) {
                $messages['turista'] = "Turista inserito con successo!";
            }
        } catch (PDOException $e) {
            $messages['turista'] = "Errore durante l'inserimento: " . $e->getMessage();
        }
    }
}

// Utilizziamo le funzioni già presenti in primodb.php per ottenere i dati
// Ottieni tutte le visite
try {
    $visite = [];
    $result_visite = getVisite($db);
    while ($row = $result_visite->fetch(PDO::FETCH_ASSOC)) {
        $visite[] = $row;
    }
} catch (PDOException $e) {
    $messages['error'] = "Errore nel caricamento delle visite: " . $e->getMessage();
}

// Ottieni tutte le guide
try {
    $guide = [];
    $result_guide = getGuide($db);
    while ($row = $result_guide->fetch(PDO::FETCH_ASSOC)) {
        $guide[] = $row;
    }
} catch (PDOException $e) {
    $messages['error'] = "Errore nel caricamento delle guide: " . $e->getMessage();
}

// Ottieni l'utente corrente se loggato
$currentUser = isLoggedIn() ? getCurrentUser($db) : null;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Artifex Turismo</title>
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/insert.css">
    <style>
        /* Stili aggiuntivi per la navbar */
        nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        nav ul {
            display: flex;
            gap: 15px;
            margin: 0;
            padding: 0;
            list-style: none;
            flex-grow: 1;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: auto;
        }

        .auth-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
            white-space: nowrap;
        }

        .btn.secondary {
            background-color: #6c757d;
        }

        /* Stili per i messaggi */
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>Artifex Turismo</h1>
        <p>Scopri l'arte e la cultura con le nostre visite guidate</p>
    </div>
</header>

<nav>
    <div class="container">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="insert.php">Gestione</a></li>
            <li><a href="visite.php">Visite</a></li>
            <li><a href="guide.php">Guide</a></li>
            <li><a href="eventi.php">Eventi</a></li>
        </ul>
        <div class="user-menu">
                <span class="user-greeting">Ciao, <?php echo htmlspecialchars($currentUser['username']); ?></span>
                <div class="user-actions">
                    <a href="area_riservata.php" class="btn secondary">Area Riservata</a>
                    <a href="logout.php" class="btn">Logout</a>
                </div>
                <div class="auth-buttons">
                    <a href="login.php" class="btn secondary">Login</a>
                    <a href="registrazione.php" class="btn">Registrati</a>
                </div>
        </div>
    </div>
</nav>

<div class="container">
    <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
        <div class="alert success">
            Registrazione completata con successo! Effettua il login.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['login']) && $_GET['login'] == 'required'): ?>
        <div class="alert warning">
            Per accedere a questa pagina devi effettuare il login.
        </div>
    <?php endif; ?>

    <h1>Gestione Artifex Turismo</h1>

    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'Visite')" id="defaultOpen">Visite</button>
        <button class="tablinks" onclick="openTab(event, 'Guide')">Guide</button>
        <button class="tablinks" onclick="openTab(event, 'Eventi')">Eventi</button>
        <button class="tablinks" onclick="openTab(event, 'Turisti')">Turisti</button>
    </div>

    <!-- TAB VISITE -->
    <div id="Visite" class="tabcontent">
        <h2>Inserimento Nuova Visita</h2>

        <?php if (isset($messages['visita'])): ?>
            <div class="<?php echo (str_contains($messages['visita'], 'Errore')) ? 'error' : 'success'; ?>">
                <?php echo $messages['visita']; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="titolo">Titolo:</label>
                <input type="text" id="titolo" name="titolo" required>
            </div>

            <div class="form-group">
                <label for="durata">Durata (minuti):</label>
                <input type="number" id="durata" name="durata" min="1" required>
            </div>

            <div class="form-group">
                <label for="luogo">Luogo:</label>
                <input type="text" id="luogo" name="luogo" required>
            </div>

            <input type="submit" name="submit_visita" value="Inserisci Visita" class="btn">
        </form>
    </div>

    <!-- TAB GUIDE -->
    <div id="Guide" class="tabcontent">
        <h2>Inserimento Nuova Guida</h2>

        <?php if (isset($messages['guida'])): ?>
            <div class="<?php echo (str_contains($messages['guida'], 'Errore')) ? 'error' : 'success'; ?>">
                <?php echo $messages['guida']; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="cognome">Cognome:</label>
                <input type="text" id="cognome" name="cognome" required>
            </div>

            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-group">
                <label for="data_nascita">Data di Nascita:</label>
                <input type="date" id="data_nascita" name="data_nascita" required>
            </div>

            <div class="form-group">
                <label for="luogo_nascita">Luogo di Nascita:</label>
                <input type="text" id="luogo_nascita" name="luogo_nascita" required>
            </div>

            <div class="form-group">
                <label for="titolo_studio">Titolo di Studio:</label>
                <input type="text" id="titolo_studio" name="titolo_studio" required>
            </div>

            <div class="lingue-container">
                <h3>Lingue Conosciute</h3>
                <div id="lingue-wrapper">
                    <div class="lingua-entry">
                        <input type="text" name="lingue[]" placeholder="Lingua">
                        <select name="livelli[]">
                            <option value="">-- Seleziona livello --</option>
                            <option value="normale">Normale</option>
                            <option value="avanzato">Avanzato</option>
                            <option value="madre lingua">Madre Lingua</option>
                        </select>
                        <button type="button" class="remove-lingua-btn" onclick="this.parentElement.remove()">-</button>
                    </div>
                </div>
                <button type="button" class="add-lingua-btn" onclick="aggiungiLingua()">+ Aggiungi Lingua</button>
            </div>

            <input type="submit" name="submit_guida" value="Inserisci Guida" class="btn">
        </form>
    </div>

    <!-- TAB EVENTI -->
    <div id="Eventi" class="tabcontent">
        <h2>Inserimento Nuovo Evento</h2>

        <?php if (isset($messages['evento'])): ?>
            <div class="<?php echo (str_contains($messages['evento'], 'Errore')) ? 'error' : 'success'; ?>">
                <?php echo $messages['evento']; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="id_visita">Visita:</label>
                <select id="id_visita" name="id_visita" required>
                    <option value="">-- Seleziona una visita --</option>
                    <?php foreach ($visite as $visita): ?>
                        <option value="<?php echo $visita['id']; ?>"><?php echo htmlspecialchars($visita['titolo']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="prezzo">Prezzo (€):</label>
                <input type="number" id="prezzo" name="prezzo" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="ora_inizio">Data e Ora Inizio:</label>
                <input type="datetime-local" id="ora_inizio" name="ora_inizio" required>
            </div>

            <div class="form-group">
                <label for="num_minimo">Numero Minimo Partecipanti:</label>
                <input type="number" id="num_minimo" name="num_minimo" min="1" required>
            </div>

            <div class="form-group">
                <label for="num_massimo">Numero Massimo Partecipanti:</label>
                <input type="number" id="num_massimo" name="num_massimo" min="1" required>
            </div>

            <div class="form-group">
                <label for="id_guida">Guida:</label>
                <select id="id_guida" name="id_guida" required>
                    <option value="">-- Seleziona una guida --</option>
                    <?php foreach ($guide as $guida): ?>
                        <option value="<?php echo $guida['id']; ?>"><?php echo htmlspecialchars($guida['cognome'] . ' ' . $guida['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="submit" name="submit_evento" value="Inserisci Evento" class="btn">
        </form>
    </div>

    <!-- TAB TURISTI -->
    <div id="Turisti" class="tabcontent">
        <h2>Inserimento Nuovo Turista</h2>

        <?php if (isset($messages['turista'])): ?>
            <div class="<?php echo (str_contains($messages['turista'], 'Errore')) ? 'error' : 'success'; ?>">
                <?php echo $messages['turista']; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="nome_turista">Nome Completo:</label>
                <input type="text" id="nome_turista" name="nome_turista" required>
            </div>

            <div class="form-group">
                <label for="nazionalita">Nazionalità:</label>
                <input type="text" id="nazionalita" name="nazionalita" required>
            </div>

            <div class="form-group">
                <label for="lingua_base">Lingua Base:</label>
                <input type="text" id="lingua_base" name="lingua_base" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="telefono">Telefono:</label>
                <input type="tel" id="telefono" name="telefono" required>
            </div>

            <input type="submit" name="submit_turista" value="Inserisci Turista" class="btn">
        </form>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        function aggiungiLingua() {
            var wrapper = document.getElementById("lingue-wrapper");
            var newEntry = document.createElement("div");
            newEntry.className = "lingua-entry";
            newEntry.innerHTML = `
                    <input type="text" name="lingue[]" placeholder="Lingua">
                    <select name="livelli[]">
                        <option value="">-- Seleziona livello --</option>
                        <option value="normale">Normale</option>
                        <option value="avanzato">Avanzato</option>
                        <option value="madre lingua">Madre Lingua</option>
                    </select>
                    <button type="button" class="remove-lingua-btn" onclick="this.parentElement.remove()">-</button>
                `;
            wrapper.appendChild(newEntry);
        }

        // Apri la prima tab di default
        document.getElementById("defaultOpen").click();
    </script>
</div>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Artifex Turismo. Tutti i diritti riservati.</p>
        <p>Sistema di gestione del turismo culturale</p>
    </div>
</footer>
</body>
</html>