-- Creazione del database
CREATE DATABASE IF NOT EXISTS ecommerce_pc;
USE ecommerce_pc;

-- Tabella utenti
CREATE TABLE IF NOT EXISTS utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    indirizzo VARCHAR(255),
    citta VARCHAR(100),
    cap VARCHAR(10),
    telefono VARCHAR(20),
    data_registrazione DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_accesso DATETIME,
    attivo BOOLEAN DEFAULT TRUE
);

-- Tabella sessioni
CREATE TABLE IF NOT EXISTS sessioni (
    id VARCHAR(50) PRIMARY KEY,
    utente_id INT,
    data_creazione DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_scadenza DATETIME,
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
);

-- Tabella categorie
CREATE TABLE IF NOT EXISTS categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descrizione TEXT,
    immagine VARCHAR(255),
    attivo BOOLEAN DEFAULT TRUE
);

-- Tabella marche
CREATE TABLE IF NOT EXISTS marche (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descrizione TEXT,
    logo VARCHAR(255),
    attivo BOOLEAN DEFAULT TRUE
);

-- Tabella prodotti
CREATE TABLE IF NOT EXISTS prodotti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descrizione TEXT,
    prezzo DECIMAL(10, 2) NOT NULL,
    prezzo_scontato DECIMAL(10, 2),
    categoria_id INT,
    marca_id INT,
    immagine VARCHAR(255),
    disponibilita INT DEFAULT 0,
    data_inserimento DATETIME DEFAULT CURRENT_TIMESTAMP,
    attivo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (categoria_id) REFERENCES categorie(id),
    FOREIGN KEY (marca_id) REFERENCES marche(id)
);

-- Tabella immagini prodotti
CREATE TABLE IF NOT EXISTS immagini_prodotti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prodotto_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    ordine INT DEFAULT 0,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
);

-- Tabella specifiche prodotti
CREATE TABLE IF NOT EXISTS specifiche_prodotti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prodotto_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    valore TEXT NOT NULL,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
);

-- Tabella varianti prodotti
CREATE TABLE IF NOT EXISTS varianti_prodotti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prodotto_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL, -- es. "colore", "potenza", "capacita"
    valore VARCHAR(100) NOT NULL,
    prezzo_aggiuntivo DECIMAL(10, 2) DEFAULT 0,
    disponibilita INT DEFAULT 0,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE CASCADE
);

-- Tabella preassemblati
CREATE TABLE IF NOT EXISTS preassemblati (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descrizione TEXT,
    prezzo_base DECIMAL(10, 2) NOT NULL,
    immagine VARCHAR(255),
    disponibilita INT DEFAULT 0,
    data_inserimento DATETIME DEFAULT CURRENT_TIMESTAMP,
    attivo BOOLEAN DEFAULT TRUE
);

-- Tabella componenti preassemblati
CREATE TABLE IF NOT EXISTS componenti_preassemblati (
    id INT AUTO_INCREMENT PRIMARY KEY,
    preassemblato_id INT NOT NULL,
    prodotto_id INT NOT NULL,
    quantita INT DEFAULT 1,
    FOREIGN KEY (preassemblato_id) REFERENCES preassemblati(id) ON DELETE CASCADE,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id)
);

-- Tabella personalizzazioni
CREATE TABLE IF NOT EXISTS personalizzazioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descrizione TEXT,
    prezzo DECIMAL(10, 2) NOT NULL,
    categoria_id INT,
    attivo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (categoria_id) REFERENCES categorie(id)
);

-- Tabella bundle
CREATE TABLE IF NOT EXISTS bundle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descrizione TEXT,
    prezzo_originale DECIMAL(10, 2) NOT NULL,
    prezzo_scontato DECIMAL(10, 2) NOT NULL,
    immagine VARCHAR(255),
    data_inizio DATETIME,
    data_fine DATETIME,
    attivo BOOLEAN DEFAULT TRUE
);

-- Tabella bundle_prodotti
CREATE TABLE IF NOT EXISTS bundle_prodotti (
    bundle_id INT NOT NULL,
    prodotto_id INT NOT NULL,
    PRIMARY KEY (bundle_id, prodotto_id),
    FOREIGN KEY (bundle_id) REFERENCES bundle(id) ON DELETE CASCADE,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id)
);

-- Tabella carrello
CREATE TABLE IF NOT EXISTS carrello (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT,
    sessione_id VARCHAR(50),
    prodotto_id INT,
    preassemblato_id INT,
    bundle_id INT,
    quantita INT DEFAULT 1,
    varianti JSON, -- Memorizza le varianti selezionate come JSON
    personalizzazioni JSON, -- Memorizza le personalizzazioni come JSON
    prezzo_unitario DECIMAL(10, 2) NOT NULL,
    data_aggiunta DATETIME DEFAULT CURRENT_TIMESTAMP,
    attivo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE SET NULL,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE SET NULL,
    FOREIGN KEY (preassemblato_id) REFERENCES preassemblati(id) ON DELETE SET NULL,
    FOREIGN KEY (bundle_id) REFERENCES bundle(id) ON DELETE SET NULL,
    CHECK (prodotto_id IS NOT NULL OR preassemblato_id IS NOT NULL OR bundle_id IS NOT NULL)
);

-- Tabella ordini
CREATE TABLE IF NOT EXISTS ordini (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT,
    sessione_id VARCHAR(50),
    totale DECIMAL(10, 2) NOT NULL,
    stato VARCHAR(50) DEFAULT 'in attesa', -- in attesa, confermato, spedito, consegnato, annullato
    indirizzo_spedizione TEXT,
    metodo_pagamento VARCHAR(50),
    data_ordine DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_aggiornamento DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE SET NULL
);

-- Tabella dettagli ordini
CREATE TABLE IF NOT EXISTS dettagli_ordini (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ordine_id INT NOT NULL,
    prodotto_id INT,
    preassemblato_id INT,
    bundle_id INT,
    nome_prodotto VARCHAR(255) NOT NULL, -- Memorizza il nome al momento dell'acquisto
    quantita INT DEFAULT 1,
    prezzo_unitario DECIMAL(10, 2) NOT NULL,
    varianti JSON, -- Memorizza le varianti selezionate come JSON
    personalizzazioni JSON, -- Memorizza le personalizzazioni come JSON
    FOREIGN KEY (ordine_id) REFERENCES ordini(id) ON DELETE CASCADE,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE SET NULL,
    FOREIGN KEY (preassemblato_id) REFERENCES preassemblati(id) ON DELETE SET NULL,
    FOREIGN KEY (bundle_id) REFERENCES bundle(id) ON DELETE SET NULL,
    CHECK (prodotto_id IS NOT NULL OR preassemblato_id IS NOT NULL OR bundle_id IS NOT NULL)
);

-- Tabella questionario
CREATE TABLE IF NOT EXISTS questionario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domanda TEXT NOT NULL,
    tipo VARCHAR(50) NOT NULL, -- multipla, singola, testo, numero
    opzioni JSON, -- Per domande a scelta multipla/singola
    ordine INT DEFAULT 0,
    attivo BOOLEAN DEFAULT TRUE
);

-- Tabella risposte questionario
CREATE TABLE IF NOT EXISTS risposte_questionario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT,
    sessione_id VARCHAR(50),
    questionario_id INT NOT NULL,
    risposta TEXT NOT NULL,
    data_risposta DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE,
    FOREIGN KEY (questionario_id) REFERENCES questionario(id) ON DELETE CASCADE
);

-- Tabella profili utenti
CREATE TABLE IF NOT EXISTS profili_utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT NOT NULL,
    tipo_profilo VARCHAR(50) NOT NULL, -- es. "gamer", "professionale", "casual"
    preferenze JSON, -- Memorizza le preferenze come JSON
    data_creazione DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_aggiornamento DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
);

-- Tabella abbonamenti
CREATE TABLE IF NOT EXISTS abbonamenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descrizione TEXT,
    prezzo DECIMAL(10, 2) NOT NULL,
    durata INT NOT NULL, -- Durata in mesi
    attivo BOOLEAN DEFAULT TRUE
);

-- Tabella abbonamenti utenti
CREATE TABLE IF NOT EXISTS abbonamenti_utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT NOT NULL,
    abbonamento_id INT NOT NULL,
    data_inizio DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_fine DATETIME,
    stato VARCHAR(50) DEFAULT 'attivo', -- attivo, scaduto, annullato
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE,
    FOREIGN KEY (abbonamento_id) REFERENCES abbonamenti(id) ON DELETE CASCADE
);

-- Tabella spedizioni abbonamenti
CREATE TABLE IF NOT EXISTS spedizioni_abbonamenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    abbonamento_utente_id INT NOT NULL,
    data_spedizione DATETIME DEFAULT CURRENT_TIMESTAMP,
    prodotti JSON, -- Lista dei prodotti spediti
    stato VARCHAR(50) DEFAULT 'in preparazione', -- in preparazione, spedito, consegnato
    FOREIGN KEY (abbonamento_utente_id) REFERENCES abbonamenti_utenti(id) ON DELETE CASCADE
);