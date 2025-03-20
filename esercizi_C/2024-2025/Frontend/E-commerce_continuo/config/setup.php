<?php
// Script per creare le tabelle del database
require_once 'database.php';

try {
    // Tabella utenti
    $pdo->exec("CREATE TABLE IF NOT EXISTS utenti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        nome VARCHAR(50) NOT NULL,
        cognome VARCHAR(50) NOT NULL,
        data_registrazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Tabella sessioni
    $pdo->exec("CREATE TABLE IF NOT EXISTS sessioni (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(255) UNIQUE NOT NULL,
        utente_id INT NULL,
        data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_scadenza TIMESTAMP NULL,
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
    )");

    // Tabella prodotti
    $pdo->exec("CREATE TABLE IF NOT EXISTS prodotti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        descrizione TEXT,
        prezzo DECIMAL(10,2) NOT NULL,
        prezzo_base DECIMAL(10,2) NOT NULL,
        categoria VARCHAR(50) NOT NULL,
        marca VARCHAR(50) NOT NULL,
        immagine VARCHAR(255) NOT NULL
    )");

    // Tabella immagini_prodotto (per gestire più immagini per prodotto)
    $pdo->exec("CREATE TABLE IF NOT EXISTS immagini_prodotto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        prodotto_id INT NOT NULL,
        url VARCHAR(255) NOT NULL,
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
    )");

    // Tabella specifiche_prodotto
    $pdo->exec("CREATE TABLE IF NOT EXISTS specifiche_prodotto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        prodotto_id INT NOT NULL,
        chiave VARCHAR(50) NOT NULL,
        valore TEXT NOT NULL,
        tipo ENUM('base', 'dettagliata') DEFAULT 'base',
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
    )");

    // Tabella varianti_prodotto
    $pdo->exec("CREATE TABLE IF NOT EXISTS varianti_prodotto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        prodotto_id INT NOT NULL,
        tipo_variante VARCHAR(50) NOT NULL,
        valore VARCHAR(100) NOT NULL,
        prezzo_aggiuntivo DECIMAL(10,2) DEFAULT 0,
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
    )");

    // Tabella carrello
    $pdo->exec("CREATE TABLE IF NOT EXISTS carrello (
        id INT AUTO_INCREMENT PRIMARY KEY,
        utente_id INT NULL,
        sessione_id VARCHAR(255) NOT NULL,
        attivo BOOLEAN DEFAULT TRUE,
        data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE SET NULL
    )");

    // Tabella carrello_prodotti
    $pdo->exec("CREATE TABLE IF NOT EXISTS carrello_prodotti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        carrello_id INT NOT NULL,
        prodotto_id INT NOT NULL,
        quantita INT DEFAULT 1,
        prezzo_unitario DECIMAL(10,2) NOT NULL,
        varianti_selezionate JSON NULL,
        personalizzazioni JSON NULL,
        tipo VARCHAR(50) DEFAULT 'catalogo',
        attivo BOOLEAN DEFAULT TRUE,
        FOREIGN KEY (carrello_id) REFERENCES carrello(id) ON DELETE CASCADE,
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
    )");

    // Tabella ordini
    $pdo->exec("CREATE TABLE IF NOT EXISTS ordini (
        id INT AUTO_INCREMENT PRIMARY KEY,
        utente_id INT NULL,
        sessione_id VARCHAR(255) NOT NULL,
        nome VARCHAR(100) NOT NULL,
        indirizzo TEXT NOT NULL,
        email VARCHAR(100) NOT NULL,
        metodo_pagamento VARCHAR(50) NOT NULL,
        totale DECIMAL(10,2) NOT NULL,
        sconto DECIMAL(10,2) DEFAULT 0,
        stato VARCHAR(50) DEFAULT 'in attesa',
        data_ordine TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE SET NULL
    )");

    // Tabella ordini_prodotti
    $pdo->exec("CREATE TABLE IF NOT EXISTS ordini_prodotti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ordine_id INT NOT NULL,
        prodotto_id INT NOT NULL,
        nome_prodotto VARCHAR(100) NOT NULL,
        quantita INT DEFAULT 1,
        prezzo_unitario DECIMAL(10,2) NOT NULL,
        varianti_selezionate JSON NULL,
        personalizzazioni JSON NULL,
        FOREIGN KEY (ordine_id) REFERENCES ordini(id) ON DELETE CASCADE,
        FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE SET NULL
    )");

    // Tabella coupon
    $pdo->exec("CREATE TABLE IF NOT EXISTS coupon (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codice VARCHAR(50) UNIQUE NOT NULL,
        percentuale_sconto DECIMAL(5,2) NOT NULL,
        data_scadenza TIMESTAMP NULL
    )");

    echo "Database setup completato con successo!";
} catch (PDOException $e) {
    echo "Errore durante la creazione delle tabelle: " . $e->getMessage();
}
?>