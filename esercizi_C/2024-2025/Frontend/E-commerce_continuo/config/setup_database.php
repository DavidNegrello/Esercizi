<?php
// Script per creare il database e le tabelle necessarie
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connessione al server MySQL senza selezionare un database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Creazione del database
    $sql = "CREATE DATABASE IF NOT EXISTS ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    echo "Database creato con successo<br>";
    
    // Seleziona il database
    $pdo->exec("USE ecommerce_db");
    
    // Creazione tabella categorie
    $sql = "CREATE TABLE IF NOT EXISTS categorie (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL UNIQUE
    )";
    $pdo->exec($sql);
    echo "Tabella categorie creata con successo<br>";
    
    // Creazione tabella marche
    $sql = "CREATE TABLE IF NOT EXISTS marche (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL UNIQUE
    )";
    $pdo->exec($sql);
    echo "Tabella marche creata con successo<br>";
    
    // Creazione tabella prodotti
    $sql = "CREATE TABLE IF NOT EXISTS prodotti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descrizione TEXT,
        prezzo_base DECIMAL(10,2) NOT NULL,
        categoria_id INT,
        marca_id INT,
        immagine_principale VARCHAR(255),
        data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (categoria_id) REFERENCES categorie(id),
        FOREIGN KEY (marca_id) REFERENCES marche(id)
    )";
    $pdo->exec($sql);
    echo "Tabella prodotti creata con successo<br>";
    
    // Creazione tabella immagini_prodotto
    $sql = "CREATE TABLE IF NOT EXISTS immagini_prodotto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        prodotto_id INT,
        url_immagine VARCHAR(255) NOT NULL,
        ordine INT DEFAULT 0,
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabella immagini_prodotto creata con successo<br>";
    
    // Creazione tabella specifiche_prodotto
    $sql = "CREATE TABLE IF NOT EXISTS specifiche_prodotto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        prodotto_id INT,
        chiave VARCHAR(100) NOT NULL,
        valore TEXT,
        tipo ENUM('base', 'dettagliata') DEFAULT 'base',
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabella specifiche_prodotto creata con successo<br>";
    
    // Creazione tabella varianti_prodotto
    $sql = "CREATE TABLE IF NOT EXISTS varianti_prodotto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        prodotto_id INT,
        tipo_variante VARCHAR(50) NOT NULL, /* potenza, colore, taglia, capacita */
        valore VARCHAR(100) NOT NULL,
        prezzo_aggiuntivo DECIMAL(10,2) DEFAULT 0.00,
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabella varianti_prodotto creata con successo<br>";
    
    // Creazione tabella utenti
    $sql = "CREATE TABLE IF NOT EXISTS utenti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        data_registrazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Tabella utenti creata con successo<br>";
    
    // Creazione tabella sessioni
    $sql = "CREATE TABLE IF NOT EXISTS sessioni (
        id VARCHAR(255) PRIMARY KEY,
        utente_id INT NULL,
        data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_scadenza TIMESTAMP,
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Tabella sessioni creata con successo<br>";
    
    // Creazione tabella carrello
    $sql = "CREATE TABLE IF NOT EXISTS carrello (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sessione_id VARCHAR(255),
        utente_id INT NULL,
        attivo BOOLEAN DEFAULT TRUE,
        data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sessione_id) REFERENCES sessioni(id) ON DELETE CASCADE,
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabella carrello creata con successo<br>";
    
    // Creazione tabella elementi_carrello
    $sql = "CREATE TABLE IF NOT EXISTS elementi_carrello (
        id INT AUTO_INCREMENT PRIMARY KEY,
        carrello_id INT,
        prodotto_id INT,
        quantita INT DEFAULT 1,
        prezzo_unitario DECIMAL(10,2) NOT NULL,
        varianti_json JSON NULL, /* Memorizza le varianti selezionate come JSON */
        tipo VARCHAR(50) DEFAULT 'catalogo', /* catalogo, preassemblato, bundle */
        FOREIGN KEY (carrello_id) REFERENCES carrello(id) ON DELETE CASCADE,
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Tabella elementi_carrello creata con successo<br>";
    
    // Creazione tabella coupon
    $sql = "CREATE TABLE IF NOT EXISTS coupon (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codice VARCHAR(50) NOT NULL UNIQUE,
        percentuale_sconto DECIMAL(5,2) NOT NULL,
        data_inizio DATE,
        data_fine DATE,
        attivo BOOLEAN DEFAULT TRUE
    )";
    $pdo->exec($sql);
    echo "Tabella coupon creata con successo<br>";
    
    // Creazione tabella bundle
    $sql = "CREATE TABLE IF NOT EXISTS bundle (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descrizione TEXT,
        prezzo_originale DECIMAL(10,2) NOT NULL,
        prezzo_scontato DECIMAL(10,2) NOT NULL,
        immagine VARCHAR(255),
        data_scadenza DATETIME NULL
    )";
    $pdo->exec($sql);
    echo "Tabella bundle creata con successo<br>";
    
    // Creazione tabella prodotti_bundle
    $sql = "CREATE TABLE IF NOT EXISTS prodotti_bundle (
        id INT AUTO_INCREMENT PRIMARY KEY,
        bundle_id INT,
        prodotto_id INT,
        FOREIGN KEY (bundle_id) REFERENCES bundle(id) ON DELETE CASCADE,
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabella prodotti_bundle creata con successo<br>";
    
    // Creazione tabella ordini
    $sql = "CREATE TABLE IF NOT EXISTS ordini (
        id INT AUTO_INCREMENT PRIMARY KEY,
        utente_id INT NULL,
        sessione_id VARCHAR(255) NULL,
        nome_cliente VARCHAR(100) NOT NULL,
        indirizzo VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        metodo_pagamento VARCHAR(50) NOT NULL,
        totale DECIMAL(10,2) NOT NULL,
        coupon_id INT NULL,
        stato VARCHAR(50) DEFAULT 'in attesa',
        data_ordine TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE SET NULL,
        FOREIGN KEY (sessione_id) REFERENCES sessioni(id) ON DELETE SET NULL,
        FOREIGN KEY (coupon_id) REFERENCES coupon(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Tabella ordini creata con successo<br>";
    
    // Creazione tabella dettagli_ordine
    $sql = "CREATE TABLE IF NOT EXISTS dettagli_ordine (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ordine_id INT,
        prodotto_id INT NULL,
        nome_prodotto VARCHAR(255) NOT NULL, /* Salviamo il nome anche se il prodotto viene eliminato */
        prezzo_unitario DECIMAL(10,2) NOT NULL,
        quantita INT DEFAULT 1,
        varianti_json JSON NULL,
        tipo VARCHAR(50) DEFAULT 'catalogo',
        FOREIGN KEY (ordine_id) REFERENCES ordini(id) ON DELETE CASCADE,
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Tabella dettagli_ordine creata con successo<br>";
    
    // Creazione tabella questionario
    $sql = "CREATE TABLE IF NOT EXISTS questionario (
        id INT AUTO_INCREMENT PRIMARY KEY,
        utente_id INT,
        domanda_1 TEXT,
        risposta_1 TEXT,
        domanda_2 TEXT,
        risposta_2 TEXT,
        domanda_3 TEXT,
        risposta_3 TEXT,
        profilo_risultante VARCHAR(100),
        data_compilazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabella questionario creata con successo<br>";
    
    // Creazione tabella abbonamenti
    $sql = "CREATE TABLE IF NOT EXISTS abbonamenti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        descrizione TEXT,
        prezzo_mensile DECIMAL(10,2) NOT NULL,
        profilo_target VARCHAR(100)
    )";
    $pdo->exec($sql);
    echo "Tabella abbonamenti creata con successo<br>";
    
    // Creazione tabella abbonamenti_utente
    $sql = "CREATE TABLE IF NOT EXISTS abbonamenti_utente (
        id INT AUTO_INCREMENT PRIMARY KEY,
        utente_id INT,
        abbonamento_id INT,
        data_inizio DATE NOT NULL,
        data_fine DATE NULL,
        stato VARCHAR(50) DEFAULT 'attivo',
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE,
        FOREIGN KEY (abbonamento_id) REFERENCES abbonamenti(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabella abbonamenti_utente creata con successo<br>";
    
    echo "Setup del database completato con successo!";
    
} catch(PDOException $e) {
    echo "Errore: " . $e->getMessage();
}
?>