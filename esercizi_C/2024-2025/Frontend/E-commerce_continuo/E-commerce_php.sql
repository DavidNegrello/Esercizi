-- Creazione del database
CREATE DATABASE IF NOT EXISTS catalogo_pc_parts;
USE catalogo_pc_parts;

-- Creazione della tabella delle categorie
CREATE TABLE IF NOT EXISTS categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
);

-- Creazione della tabella delle marche
CREATE TABLE IF NOT EXISTS marche (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
);

-- Creazione della tabella dei prodotti
CREATE TABLE IF NOT EXISTS prodotti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    categoria_id INT NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    marca_id INT NOT NULL,
    immagine VARCHAR(255),
    FOREIGN KEY (categoria_id) REFERENCES categorie(id),
    FOREIGN KEY (marca_id) REFERENCES marche(id)
);

-- Creazione della tabella delle impostazioni filtri
CREATE TABLE IF NOT EXISTS filtri_impostazioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(50) NOT NULL,
    prezzo_min DECIMAL(10, 2) NOT NULL,
    prezzo_max DECIMAL(10, 2) NOT NULL,
    prezzo_step DECIMAL(10, 2) NOT NULL
);

-- Inserimento delle categorie
INSERT INTO categorie (nome) VALUES 
('CPU'), ('GPU'), ('RAM'), ('PSU'), ('Storage'), ('Case'), ('Scheda Madre'), ('Raffreddamento');

-- Inserimento delle marche
INSERT INTO marche (nome) VALUES 
('Intel'), ('AMD'), ('NVIDIA'), ('Corsair'), ('MSI'), ('ASUS'), ('NZXT'), ('Gigabyte'), ('Thermaltake'), ('Samsung'), ('SK hynix');

-- Inserimento delle impostazioni dei filtri
INSERT INTO filtri_impostazioni (titolo, prezzo_min, prezzo_max, prezzo_step) VALUES 
('Filtri', 0, 2000, 10);

-- Inserimento dei prodotti
INSERT INTO prodotti (id, nome, categoria_id, prezzo, marca_id, immagine) VALUES
(1, 'ASUS ROG Strix Z690-E Gaming WiFi', (SELECT id FROM categorie WHERE nome = 'Scheda Madre'), 379.99, (SELECT id FROM marche WHERE nome = 'ASUS'), '../immagini/motherboard/z690/1/1.png'),
(2, 'ASUS ROG Strix Z690-A Gaming WiFi', (SELECT id FROM categorie WHERE nome = 'Scheda Madre'), 349.99, (SELECT id FROM marche WHERE nome = 'ASUS'), '../immagini/motherboard/z690/2/1.png'),
(3, 'GIGABYTE Z490 AORUS MASTER', (SELECT id FROM categorie WHERE nome = 'Scheda Madre'), 449.99, (SELECT id FROM marche WHERE nome = 'Gigabyte'), '../immagini/motherboard/gigabyte/1/1.png'),
(4, 'GIGABYTE Z490 AORUS Elite AC', (SELECT id FROM categorie WHERE nome = 'Scheda Madre'), 229.99, (SELECT id FROM marche WHERE nome = 'Gigabyte'), '../immagini/motherboard/gigabyte/2/1.png'),
(5, 'MSI GeForce RTX 4080 16Go', (SELECT id FROM categorie WHERE nome = 'GPU'), 1599.99, (SELECT id FROM marche WHERE nome = 'MSI'), '../immagini/GPU/4080/1/1.png'),
(6, 'MSI GeForce RTX 4080 16GB SUPRIM', (SELECT id FROM categorie WHERE nome = 'GPU'), 1799.99, (SELECT id FROM marche WHERE nome = 'MSI'), '../immagini/GPU/4080/2/1.png'),
(7, 'Corsair RM850X 80 Plus Gold', (SELECT id FROM categorie WHERE nome = 'PSU'), 139.99, (SELECT id FROM marche WHERE nome = 'Corsair'), '../immagini/PSU/1/1/1.png'),
(8, 'THERMALTAKE ALIM. TOUGHPOWER GF1 750W ARGB FULL MODULAR 80 PLUS GOLD', (SELECT id FROM categorie WHERE nome = 'PSU'), 119.99, (SELECT id FROM marche WHERE nome = 'Thermaltake'), '../immagini/PSU/2/1.png'),
(9, 'CORSAIR VENGEANCE RGB DDR5 RAM 32GB (2x16GB) 5200MHz', (SELECT id FROM categorie WHERE nome = 'RAM'), 199.99, (SELECT id FROM marche WHERE nome = 'Corsair'), '../immagini/RAM/2/bianco/1.png'),
(10, 'Samsung 970 EVO Plus PCIe NVMe M.2', (SELECT id FROM categorie WHERE nome = 'Storage'), 129.99, (SELECT id FROM marche WHERE nome = 'Samsung'), '../immagini/SSD/1/1.png'),
(11, 'SK hynix Platinum P41 NVMe SSD', (SELECT id FROM categorie WHERE nome = 'Storage'), 399.99, (SELECT id FROM marche WHERE nome = 'SK hynix'), '../immagini/SSD/2/1.png');