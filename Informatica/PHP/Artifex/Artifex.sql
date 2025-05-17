CREATE DATABASE artifex_turismo;

USE artifex_turismo;


CREATE TABLE visite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(255) NOT NULL,
    durata_media INT NOT NULL,  -- durata in minuti
    luogo VARCHAR(255) NOT NULL
);


CREATE TABLE guide (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cognome VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    data_nascita DATE NOT NULL,
    luogo_nascita VARCHAR(255) NOT NULL,
    titolo_studio VARCHAR(255) NOT NULL
);

CREATE TABLE eventi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_visita INT NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    ora_inizio DATETIME NOT NULL,
    num_minimo_partecipanti INT NOT NULL,
    num_massimo_partecipanti INT NOT NULL,
    id_guida INT NOT NULL,  -- Ogni evento ha una guida associata
    FOREIGN KEY (id_visita) REFERENCES visite(id),
    FOREIGN KEY (id_guida) REFERENCES guide(id)
);

CREATE TABLE lingue_guide (
    id_guida INT NOT NULL,
    lingua VARCHAR(255) NOT NULL,
    livello_competenza ENUM('normale', 'avanzato', 'madre lingua') NOT NULL,
    PRIMARY KEY (id_guida, lingua),
    FOREIGN KEY (id_guida) REFERENCES guide(id)
);

CREATE TABLE Turisti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    nazionalita VARCHAR(100) NOT NULL,
    lingua_base VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL
);


CREATE TABLE IF NOT EXISTS utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    data_registrazione DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE prenotazioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_evento INT NOT NULL,
    id_utente INT NOT NULL,
    data_prenotazione DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_evento) REFERENCES eventi(id),
    FOREIGN KEY (id_utente) REFERENCES utenti(id)
);
