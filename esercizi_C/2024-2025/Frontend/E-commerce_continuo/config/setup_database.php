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
    
    // Creazione tabella prodotti
    $sql = "CREATE TABLE IF NOT EXISTS prodotti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descrizione TEXT,
        prezzo_base DECIMAL(10,2) NOT NULL,
        categoria VARCHAR(100) NOT NULL,
        marca VARCHAR(100) NOT NULL,
        immagine_principale VARCHAR(255),
        immagini JSON, /* Array di URL delle immagini */
        specifiche JSON, /* Specifiche base come JSON */
        specifiche_dettagliate JSON, /* Specifiche dettagliate come JSON */
        varianti JSON, /* Varianti come JSON (potenza, colore, taglia, capacità) */
        data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Tabella prodotti creata con successo<br>";
    
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
        prodotto_id INT,
        quantita INT DEFAULT 1,
        prezzo_unitario DECIMAL(10,2) NOT NULL,
        varianti JSON NULL, /* Memorizza le varianti selezionate come JSON */
        tipo VARCHAR(50) DEFAULT 'catalogo', /* catalogo, preassemblato, bundle */
        attivo BOOLEAN DEFAULT TRUE,
        data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sessione_id) REFERENCES sessioni(id) ON DELETE CASCADE,
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE,
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Tabella carrello creata con successo<br>";
    
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
        prodotti JSON, /* Array di ID prodotti */
        data_scadenza DATETIME NULL
    )";
    $pdo->exec($sql);
    echo "Tabella bundle creata con successo<br>";
    
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
        prodotti JSON, /* Dettagli dei prodotti ordinati come JSON */
        stato VARCHAR(50) DEFAULT 'in attesa',
        data_ordine TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE SET NULL,
        FOREIGN KEY (sessione_id) REFERENCES sessioni(id) ON DELETE SET NULL,
        FOREIGN KEY (coupon_id) REFERENCES coupon(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    echo "Tabella ordini creata con successo<br>";
    
    // Creazione tabella questionario
    $sql = "CREATE TABLE IF NOT EXISTS questionario (
        id INT AUTO_INCREMENT PRIMARY KEY,
        utente_id INT,
        risposte JSON, /* Domande e risposte come JSON */
        profilo_risultante VARCHAR(100),
        data_compilazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabella questionario creata con successo<br>";
    
    // Creazione tabella abbonamenti
    $sql = "CREATE TABLE IF NOT EXISTS abbonamenti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        utente_id INT,
        nome VARCHAR(100) NOT NULL,
        descrizione TEXT,
        prezzo_mensile DECIMAL(10,2) NOT NULL,
        profilo_target VARCHAR(100),
        data_inizio DATE NOT NULL,
        data_fine DATE NULL,
        stato VARCHAR(50) DEFAULT 'attivo',
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabella abbonamenti creata con successo<br>";
    
    echo "Setup del database completato con successo!";
    
} catch(PDOException $e) {
    echo "Errore: " . $e->getMessage();
}
?>