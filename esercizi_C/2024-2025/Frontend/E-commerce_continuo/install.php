<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installazione E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Installazione E-commerce PHP</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
                        $error = false;
                        $message = '';

                        // Funzione per verificare la connessione al database
                        function testConnection($host, $dbname, $username, $password) {
                            try {
                                $dsn = "mysql:host=$host";
                                $pdo = new PDO($dsn, $username, $password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                return true;
                            } catch (PDOException $e) {
                                return false;
                            }
                        }

                        // Funzione per creare il database
                        function createDatabase($host, $dbname, $username, $password) {
                            try {
                                $dsn = "mysql:host=$host";
                                $pdo = new PDO($dsn, $username, $password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Crea il database se non esiste
                                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                                return true;
                            } catch (PDOException $e) {
                                return false;
                            }
                        }

                        // Funzione per salvare la configurazione del database
                        function saveConfig($host, $dbname, $username, $password) {
                            $config = <<<EOT
<?php
// Configurazione del database
\$host = '$host';
\$db_name = '$dbname';
\$username = '$username';
\$password = '$password';
\$charset = 'utf8mb4';

\$dsn = "mysql:host=\$host;dbname=\$db_name;charset=\$charset";
\$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    \$pdo = new PDO(\$dsn, \$username, \$password, \$options);
} catch (PDOException \$e) {
    throw new PDOException(\$e->getMessage(), (int)\$e->getCode());
}
?>
EOT;
                            
                            return file_put_contents('config/database.php', $config) !== false;
                        }

                        // Gestione dei passaggi di installazione
                        switch ($step) {
                            case 1:
                                // Verifica dei requisiti
                                $requirements = [
                                    'PHP >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
                                    'PDO Extension' => extension_loaded('pdo'),
                                    'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
                                    'JSON Extension' => extension_loaded('json'),
                                    'Permessi di scrittura' => is_writable('config')
                                ];
                                
                                $allRequirementsMet = !in_array(false, $requirements);
                                
                                echo '<h4>Verifica dei requisiti</h4>';
                                echo '<ul class="list-group mb-4">';
                                foreach ($requirements as $requirement => $satisfied) {
                                    $class = $satisfied ? 'list-group-item-success' : 'list-group-item-danger';
                                    $icon = $satisfied ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
                                    echo "<li class=\"list-group-item $class\">$requirement: $icon</li>";
                                }
                                echo '</ul>';
                                
                                if ($allRequirementsMet) {
                                    echo '<div class="alert alert-success">Tutti i requisiti sono soddisfatti!</div>';
                                    echo '<a href="?step=2" class="btn btn-primary">Continua</a>';
                                } else {
                                    echo '<div class="alert alert-danger">Alcuni requisiti non sono soddisfatti. Risolvi i problemi prima di continuare.</div>';
                                }
                                break;
                                
                            case 2:
                                // Configurazione del database
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $host = $_POST['host'] ?? 'localhost';
                                    $dbname = $_POST['dbname'] ?? 'ecommerce_db';
                                    $username = $_POST['username'] ?? 'root';
                                    $password = $_POST['password'] ?? '';
                                    
                                    // Verifica la connessione
                                    if (testConnection($host, $dbname, $username, $password)) {
                                        // Salva la configurazione
                                        if (saveConfig($host, $dbname, $username, $password)) {
                                            // Crea il database se non esiste
                                            if (createDatabase($host, $dbname, $username, $password)) {
                                                header('Location: ?step=3');
                                                exit;
                                            } else {
                                                $error = true;
                                                $message = "Impossibile creare il database. Verifica che l'utente abbia i permessi necessari.";
                                            }
                                        } else {
                                            $error = true;
                                            $message = "Impossibile salvare il file di configurazione. Verifica i permessi di scrittura.";
                                        }
                                    } else {
                                        $error = true;
                                        $message = "Impossibile connettersi al database. Verifica le credenziali.";
                                    }
                                }
                                
                                if ($error) {
                                    echo '<div class="alert alert-danger">' . $message . '</div>';
                                }
                                
                                echo '<h4>Configurazione del database</h4>';
                                echo '<form method="post" action="?step=2">';
                                echo '<div class="mb-3">';
                                echo '<label for="host" class="form-label">Host</label>';
                                echo '<input type="text" class="form-control" id="host" name="host" value="localhost" required>';
                                echo '</div>';
                                echo '<div class="mb-3">';
                                echo '<label for="dbname" class="form-label">Nome database</label>';
                                echo '<input type="text" class="form-control" id="dbname" name="dbname" value="ecommerce_db" required>';
                                echo '</div>';
                                echo '<div class="mb-3">';
                                echo '<label for="username" class="form-label">Username</label>';
                                echo '<input type="text" class="form-control" id="username" name="username" value="root" required>';
                                echo '</div>';
                                echo '<div class="mb-3">';
                                echo '<label for="password" class="form-label">Password</label>';
                                echo '<input type="password" class="form-control" id="password" name="password">';
                                echo '</div>';
                                echo '<button type="submit" class="btn btn-primary">Verifica connessione</button>';
                                echo '</form>';
                                break;
                                
                            case 3:
                                // Creazione delle tabelle
                                $setupSuccess = false;
                                $sampleDataSuccess = false;
                                
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    if (isset($_POST['setup_tables'])) {
                                        // Esegui lo script di setup
                                        ob_start();
                                        require_once 'config/setup.php';
                                        ob_end_clean();
                                        $setupSuccess = true;
                                    } elseif (isset($_POST['sample_data'])) {
                                        // Inserisci i dati di esempio
                                        ob_start();
                                        require_once 'config/sample_data.php';
                                        ob_end_clean();
                                        $sampleDataSuccess = true;
                                    }
                                }
                                
                                echo '<h4>Creazione delle tabelle del database</h4>';
                                
                                if ($setupSuccess) {
                                    echo '<div class="alert alert-success">Tabelle create con successo!</div>';
                                }
                                
                                if ($sampleDataSuccess) {
                                    echo '<div class="alert alert-success">Dati di esempio inseriti con successo!</div>';
                                }
                                
                                echo '<form method="post" action="?step=3">';
                                echo '<p>Crea le tabelle necessarie per il funzionamento dell\'e-commerce.</p>';
                                echo '<button type="submit" name="setup_tables" class="btn btn-primary mb-3">Crea tabelle</button>';
                                echo '</form>';
                                
                                echo '<form method="post" action="?step=3">';
                                echo '<p>Inserisci alcuni dati di esempio per testare l\'e-commerce.</p>';
                                echo '<button type="submit" name="sample_data" class="btn btn-secondary mb-3">Inserisci dati di esempio</button>';
                                echo '</form>';
                                
                                echo '<a href="?step=4" class="btn btn-success">Continua</a>';
                                break;
                                
                            case 4:
                                // Completamento dell'installazione
                                echo '<div class="text-center">';
                                echo '<i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>';
                                echo '<h4 class="mt-3">Installazione completata!</h4>';
                                echo '<p>L\'e-commerce è stato installato con successo.</p>';
                                echo '<div class="mt-4">';
                                echo '<a href="index.html" class="btn btn-primary">Vai al sito</a>';
                                echo '</div>';
                                echo '</div>';
                                break;
                        }
                        ?>
                    </div>
                    <div class="card-footer text-muted">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $step * 25; ?>%" aria-valuenow="<?php echo $step * 25; ?>" aria-valuemin="0" aria-valuemax="100">Passo <?php echo $step; ?> di 4</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>