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


-- ================================ Catalogo_dettaglio==========================
-- Tabella per le descrizioni dei prodotti
CREATE TABLE IF NOT EXISTS prodotti_descrizioni (
    prodotto_id INT NOT NULL,
    descrizione TEXT NOT NULL,
    PRIMARY KEY (prodotto_id),
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id)
);

-- Tabella per le specifiche dei prodotti
CREATE TABLE IF NOT EXISTS specifiche (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- Tabella per il collegamento tra prodotti e specifiche
CREATE TABLE IF NOT EXISTS prodotti_specifiche (
    prodotto_id INT NOT NULL,
    specifica_id INT NOT NULL,
    valore VARCHAR(255) NOT NULL,
    PRIMARY KEY (prodotto_id, specifica_id),
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id),
    FOREIGN KEY (specifica_id) REFERENCES specifiche(id)
);

-- Tabella per le immagini multiple dei prodotti
CREATE TABLE IF NOT EXISTS prodotti_immagini (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prodotto_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    is_principale BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id)
);

-- Tabella per i colori disponibili
CREATE TABLE IF NOT EXISTS colori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
);

-- Tabella per collegare prodotti e colori disponibili
CREATE TABLE IF NOT EXISTS prodotti_colori (
    prodotto_id INT NOT NULL,
    colore_id INT NOT NULL,
    PRIMARY KEY (prodotto_id, colore_id),
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id),
    FOREIGN KEY (colore_id) REFERENCES colori(id)
);

-- Tabella per le varianti di colore dei prodotti e relative immagini
CREATE TABLE IF NOT EXISTS varianti_colore (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prodotto_id INT NOT NULL,
    colore_id INT NOT NULL,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id),
    FOREIGN KEY (colore_id) REFERENCES colori(id),
    UNIQUE (prodotto_id, colore_id)
);

-- Tabella per le immagini delle varianti di colore
CREATE TABLE IF NOT EXISTS varianti_colore_immagini (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variante_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    FOREIGN KEY (variante_id) REFERENCES varianti_colore(id)
);

-- Tabella per le capacità (per prodotti come SSD)
CREATE TABLE IF NOT EXISTS capacita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
);

-- Tabella per collegare prodotti e capacità
CREATE TABLE IF NOT EXISTS prodotti_capacita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prodotto_id INT NOT NULL,
    capacita_id INT NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id),
    FOREIGN KEY (capacita_id) REFERENCES capacita(id),
    UNIQUE (prodotto_id, capacita_id)
);

-- Tabella per le taglie (per prodotti come RAM)
CREATE TABLE IF NOT EXISTS taglie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descrizione VARCHAR(255)
);

-- Tabella per collegare prodotti e taglie
CREATE TABLE IF NOT EXISTS prodotti_taglie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prodotto_id INT NOT NULL,
    taglia_id INT NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id),
    FOREIGN KEY (taglia_id) REFERENCES taglie(id),
    UNIQUE (prodotto_id, taglia_id)
);

-- Tabella per le specifiche dettagliate dei prodotti
CREATE TABLE IF NOT EXISTS specifiche_dettagliate (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- Tabella per il collegamento tra prodotti e specifiche dettagliate
CREATE TABLE IF NOT EXISTS prodotti_specifiche_dettagliate (
    prodotto_id INT NOT NULL,
    specifica_dettagliata_id INT NOT NULL,
    valore VARCHAR(255) NOT NULL,
    PRIMARY KEY (prodotto_id, specifica_dettagliata_id),
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id),
    FOREIGN KEY (specifica_dettagliata_id) REFERENCES specifiche_dettagliate(id)
);

-- Tabella per le varianti di wattaggio (per prodotti come PSU)
CREATE TABLE IF NOT EXISTS varianti_wattaggio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prodotto_id INT NOT NULL,
    wattaggio VARCHAR(10) NOT NULL,
    FOREIGN KEY (prodotto_id) REFERENCES prodotti(id),
    UNIQUE (prodotto_id, wattaggio)
);

-- Tabella per le immagini delle varianti di wattaggio
CREATE TABLE IF NOT EXISTS varianti_wattaggio_immagini (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variante_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    FOREIGN KEY (variante_id) REFERENCES varianti_wattaggio(id)
);

-- Inserimento delle descrizioni dei prodotti
INSERT INTO prodotti_descrizioni (prodotto_id, descrizione) VALUES
(1, 'ASUS ROG Strix Z690-E Gaming WiFi Scheda Madre ATX, Intel Z690, LGA1700, DDR5, PCI 5.0, WiFi 6E (802.11ax), Intel 2.5Gb Ethernet, ROG SupremeFX 7.1, 4xM.2, 6xSATA 6GB/s, Aura Sync RGB, Nero'),
(2, 'ASUS ROG Strix Z690-E Gaming WiFi Scheda Madre ATX, Intel Z690, LGA1700, DDR5, PCI 5.0, WiFi 6E (802.11ax), Intel 2.5Gb Ethernet, ROG SupremeFX 7.1, 4xM.2, 6xSATA 6GB/s, Aura Sync RGB, Nero'),
(3, 'GIGABYTE Z490 AORUS MASTER Intel LGA1200 ATX Motherboard (14+1 Phases, NanoCarbon Baseplate, Intel 2.5GbE LAN, AQUANTIA 10GbE LAN, HDMI, USB 3.2 Gen2, USB Type-C, RGB Fusion 2.0)'),
(4, 'GIGABYTE Z490 AORUS Elite AC Intel LGA 1200 ATX Motherboard (12+1 Phases, Intel 2.5GbE LAN, HDMI, USB 3.2 Gen2, USB Type-C, RGB Fusion 2.0)'),
(5, 'MSI GeForce RTX 4080 16GB SUPRIM Scheda Video Gaming - NVIDIA RTX 4080, 16 GB GDDR6X'),
(6, 'MSI GeForce RTX 4080 16GB SUPRIM Scheda Video Gaming - NVIDIA RTX 4080, 16 GB GDDR6X'),
(7, 'Corsair RM750x 80 PLUS Gold Alimentatore 750 Watt ATX Completamente Modulare, Ventola Levitazione Magnetica 135 mm, Ampiamente Compatibili, Condensatori Giapponesi, EU, Nero'),
(8, 'THERMALTAKE ALIM. TOUGHPOWER GF1 750W ARGB FULL MODULAR 80 PLUS GOLD'),
(9, 'CORSAIR VENGEANCE RGB DDR5 RAM 32GB (2x16GB) 5200MHz CL40 Intel XMP Compatibile iCUE Memoria per Computer - Nero (CMH32GX5M2B5200C40)'),
(10, 'Samsung 970 EVO Plus 1 TB PCIe NVMe M.2 (2280) Internal Solid State Drive (SSD) (MMZ-V7S1T0BW ), Black'),
(11, 'SK hynix Platinum P41 NVMe SSD 2TB PCIe 4.0 7000 MB/s in lettura 6500 MB/s in scrittura M.2 2280 Interno SSD per Gaming, compatibile con Notebook PC Desktop e giochi');

-- Inserimento dei colori
INSERT INTO colori (nome) VALUES 
('Nero'), ('Bianco'), ('Argento'), ('Multicolore');

-- Collegamento prodotti e colori
INSERT INTO prodotti_colori (prodotto_id, colore_id) VALUES
(1, (SELECT id FROM colori WHERE nome = 'Nero')),
(1, (SELECT id FROM colori WHERE nome = 'Bianco')),
(2, (SELECT id FROM colori WHERE nome = 'Nero')),
(2, (SELECT id FROM colori WHERE nome = 'Bianco')),
(3, (SELECT id FROM colori WHERE nome = 'Nero')),
(4, (SELECT id FROM colori WHERE nome = 'Nero')),
(5, (SELECT id FROM colori WHERE nome = 'Nero')),
(5, (SELECT id FROM colori WHERE nome = 'Argento')),
(6, (SELECT id FROM colori WHERE nome = 'Nero')),
(6, (SELECT id FROM colori WHERE nome = 'Argento')),
(9, (SELECT id FROM colori WHERE nome = 'Nero')),
(9, (SELECT id FROM colori WHERE nome = 'Bianco')),
(10, (SELECT id FROM colori WHERE nome = 'Nero')),
(11, (SELECT id FROM colori WHERE nome = 'Multicolore'));

-- Inserimento capacità per SSD
INSERT INTO capacita (nome) VALUES 
('250GB'), ('500GB'), ('1TB'), ('2TB');

-- Collegamento prodotti e capacità con prezzi
INSERT INTO prodotti_capacita (prodotto_id, capacita_id, prezzo) VALUES
(10, (SELECT id FROM capacita WHERE nome = '250GB'), 59.99),
(10, (SELECT id FROM capacita WHERE nome = '500GB'), 89.99),
(10, (SELECT id FROM capacita WHERE nome = '1TB'), 129.99),
(11, (SELECT id FROM capacita WHERE nome = '500GB'), 169.99),
(11, (SELECT id FROM capacita WHERE nome = '1TB'), 249.99),
(11, (SELECT id FROM capacita WHERE nome = '2TB'), 399.99);

-- Inserimento taglie per RAM
INSERT INTO taglie (nome, descrizione) VALUES 
('2x16GB', '2x16GB per un totale di 32GB'),
('2x32GB', '2x32GB per un totale di 64GB');

-- Collegamento prodotti e taglie con prezzi
INSERT INTO prodotti_taglie (prodotto_id, taglia_id, prezzo) VALUES
(9, (SELECT id FROM taglie WHERE nome = '2x16GB'), 199.99),
(9, (SELECT id FROM taglie WHERE nome = '2x32GB'), 399.99);

-- Inserimento specifiche comuni
INSERT INTO specifiche (nome) VALUES
('Processore supportato'), ('Tecnologia di memoria'), ('Memoria massima supportata'),
('Clock di Memoria'), ('Interfaccia scheda grafica'), ('Tipo wireless'),
('Numero di porte USB 2.0'), ('Numero di porte HDMI'), ('Numero di porte Ethernet'),
('Risoluzione'), ('Coprocessore grafico'), ('Grafica Chipset Brand'),
('Descrizione scheda grafica'), ('Tipo memoria scheda grafica'), ('Dimensioni memoria scheda grafica'),
('Fattore di forma'), ('Voltaggio'), ('Wattaggio'), ('Dimensioni RAM'),
('Tipologia di memoria computer'), ('Interfaccia Hard-Disk'), ('Piattaforma Hardware'),
('Tipo di connettività'), ('Le batterie sono incluse'), ('Peso articolo');

-- Raccolta ID delle specifiche per inserimenti successivi
SET @processore_id = (SELECT id FROM specifiche WHERE nome = 'Processore supportato');
SET @tecnologia_memoria_id = (SELECT id FROM specifiche WHERE nome = 'Tecnologia di memoria');
SET @max_memoria_id = (SELECT id FROM specifiche WHERE nome = 'Memoria massima supportata');
SET @clock_memoria_id = (SELECT id FROM specifiche WHERE nome = 'Clock di Memoria');
SET @interfaccia_grafica_id = (SELECT id FROM specifiche WHERE nome = 'Interfaccia scheda grafica');
SET @wireless_id = (SELECT id FROM specifiche WHERE nome = 'Tipo wireless');
SET @usb_id = (SELECT id FROM specifiche WHERE nome = 'Numero di porte USB 2.0');
SET @hdmi_id = (SELECT id FROM specifiche WHERE nome = 'Numero di porte HDMI');
SET @ethernet_id = (SELECT id FROM specifiche WHERE nome = 'Numero di porte Ethernet');
SET @risoluzione_id = (SELECT id FROM specifiche WHERE nome = 'Risoluzione');
SET @coprocessore_id = (SELECT id FROM specifiche WHERE nome = 'Coprocessore grafico');
SET @chipset_id = (SELECT id FROM specifiche WHERE nome = 'Grafica Chipset Brand');
SET @desc_grafica_id = (SELECT id FROM specifiche WHERE nome = 'Descrizione scheda grafica');
SET @tipo_memoria_grafica_id = (SELECT id FROM specifiche WHERE nome = 'Tipo memoria scheda grafica');
SET @dim_memoria_grafica_id = (SELECT id FROM specifiche WHERE nome = 'Dimensioni memoria scheda grafica');
SET @fattore_forma_id = (SELECT id FROM specifiche WHERE nome = 'Fattore di forma');
SET @voltaggio_id = (SELECT id FROM specifiche WHERE nome = 'Voltaggio');
SET @wattaggio_id = (SELECT id FROM specifiche WHERE nome = 'Wattaggio');
SET @dim_ram_id = (SELECT id FROM specifiche WHERE nome = 'Dimensioni RAM');
SET @tipo_memoria_computer_id = (SELECT id FROM specifiche WHERE nome = 'Tipologia di memoria computer');
SET @interfaccia_hd_id = (SELECT id FROM specifiche WHERE nome = 'Interfaccia Hard-Disk');
SET @piattaforma_id = (SELECT id FROM specifiche WHERE nome = 'Piattaforma Hardware');
SET @connettivita_id = (SELECT id FROM specifiche WHERE nome = 'Tipo di connettività');
SET @batterie_id = (SELECT id FROM specifiche WHERE nome = 'Le batterie sono incluse');
SET @peso_id = (SELECT id FROM specifiche WHERE nome = 'Peso articolo');

-- Inserimento di alcune specifiche dei prodotti (esempi rappresentativi)
INSERT INTO prodotti_specifiche (prodotto_id, specifica_id, valore) VALUES
-- Scheda madre ASUS ROG Strix Z690-E
(1, @processore_id, 'LGA 1700'),
(1, @tecnologia_memoria_id, 'DDR5'),
(1, @max_memoria_id, '128 GB'),
(1, @clock_memoria_id, '2133 MHz'),
(1, @interfaccia_grafica_id, 'PCI Express'),
(1, @wireless_id, '802.11a/b/g/n/ac, Bluetooth, 802.11ax'),
(1, @usb_id, '9'),
(1, @hdmi_id, '1'),
(1, @ethernet_id, '1'),

-- GPU MSI GeForce RTX 4080
(5, @risoluzione_id, '5120x2880 (5K) at 60Hz'),
(5, @clock_memoria_id, '1700 Modificatore sconosciuto'),
(5, @coprocessore_id, 'NVIDIA GeForce RTX 4080'),
(5, @chipset_id, 'NVIDIA'),
(5, @desc_grafica_id, 'Dedicato'),
(5, @tipo_memoria_grafica_id, 'GDDR6X'),
(5, @dim_memoria_grafica_id, '16 GB'),
(5, @interfaccia_grafica_id, 'PCI-Express x16'),
(5, @batterie_id, 'No'),

-- RAM Corsair Vengeance RGB
(9, @fattore_forma_id, 'DIMM'),
(9, @dim_ram_id, '32 GB'),
(9, @tecnologia_memoria_id, 'DDR5'),
(9, @tipo_memoria_computer_id, 'DDR5 SDRAM'),
(9, @clock_memoria_id, '5200 MHz'),
(9, @voltaggio_id, '1,25 Volt'),

-- SSD Samsung 970 EVO Plus
(10, @fattore_forma_id, 'M.2 (2280)'),
(10, @interfaccia_hd_id, 'PCIE x 4'),
(10, @piattaforma_id, 'PC'),
(10, @peso_id, '8 g');

-- Inserimento delle immagini per ogni prodotto
INSERT INTO prodotti_immagini (prodotto_id, url, is_principale) VALUES
(1, '../immagini/motherboard/z690/1/1.png', TRUE),
(1, '../immagini/motherboard/z690/1/2.png', FALSE),
(1, '../immagini/motherboard/z690/1/3.png', FALSE),
(2, '../immagini/motherboard/z690/2/1.png', TRUE),
(2, '../immagini/motherboard/z690/2/2.png', FALSE),
(2, '../immagini/motherboard/z690/2/3.png', FALSE),
(3, '../immagini/motherboard/gigabyte/1/1.png', TRUE),
(3, '../immagini/motherboard/gigabyte/1/2.png', FALSE),
(3, '../immagini/motherboard/gigabyte/1/3.png', FALSE),
(4, '../immagini/motherboard/gigabyte/2/1.png', TRUE),
(4, '../immagini/motherboard/gigabyte/2/2.png', FALSE),
(4, '../immagini/motherboard/gigabyte/2/3.png', FALSE),
(5, '../immagini/GPU/4080/1/1.png', TRUE),
(5, '../immagini/GPU/4080/1/2.png', FALSE),
(6, '../immagini/GPU/4080/2/1.png', TRUE),
(6, '../immagini/GPU/4080/2/2.png', FALSE),
(7, '../immagini/PSU/1/1/1.png', TRUE),
(7, '../immagini/PSU/1/2.png', FALSE),
(7, '../immagini/PSU/1/3.png', FALSE),
(8, '../immagini/PSU/2/1.png', TRUE),
(8, '../immagini/PSU/2/2.png', FALSE),
(8, '../immagini/PSU/2/3.png', FALSE),
(9, '../immagini/RAM/2/bianco/1.png', TRUE),
(9, '../immagini/RAM/2/bianco/2.png', FALSE),
(9, '../immagini/RAM/2/bianco/3.png', FALSE),
(10, '../immagini/SSD/1/1.png', TRUE),
(10, '../immagini/SSD/1/2.png', FALSE),
(10, '../immagini/SSD/1/3.png', FALSE),
(11, '../immagini/SSD/2/1.png', TRUE),
(11, '../immagini/SSD/2/2.png', FALSE),
(11, '../immagini/SSD/2/3.png', FALSE);

-- Inserimento delle varianti di colore
INSERT INTO varianti_colore (prodotto_id, colore_id) VALUES
(1, (SELECT id FROM colori WHERE nome = 'Nero')),
(1, (SELECT id FROM colori WHERE nome = 'Bianco')),
(2, (SELECT id FROM colori WHERE nome = 'Nero')),
(2, (SELECT id FROM colori WHERE nome = 'Bianco')),
(5, (SELECT id FROM colori WHERE nome = 'Nero')),
(5, (SELECT id FROM colori WHERE nome = 'Argento')),
(6, (SELECT id FROM colori WHERE nome = 'Nero')),
(6, (SELECT id FROM colori WHERE nome = 'Argento')),
(9, (SELECT id FROM colori WHERE nome = 'Nero')),
(9, (SELECT id FROM colori WHERE nome = 'Bianco'));

-- Recupero degli ID delle varianti per inserire le immagini
SET @var_1_nero = (SELECT id FROM varianti_colore WHERE prodotto_id = 1 AND colore_id = (SELECT id FROM colori WHERE nome = 'Nero'));
SET @var_1_bianco = (SELECT id FROM varianti_colore WHERE prodotto_id = 1 AND colore_id = (SELECT id FROM colori WHERE nome = 'Bianco'));
SET @var_9_nero = (SELECT id FROM varianti_colore WHERE prodotto_id = 9 AND colore_id = (SELECT id FROM colori WHERE nome = 'Nero'));
SET @var_9_bianco = (SELECT id FROM varianti_colore WHERE prodotto_id = 9 AND colore_id = (SELECT id FROM colori WHERE nome = 'Bianco'));

-- Inserimento delle immagini per le varianti di colore (esempi)
INSERT INTO varianti_colore_immagini (variante_id, url) VALUES
(@var_1_nero, '../immagini/motherboard/z690/1/1.png'),
(@var_1_nero, '../immagini/motherboard/z690/1/2.png'),
(@var_1_nero, '../immagini/motherboard/z690/1/3.png'),
(@var_1_bianco, '../immagini/motherboard/z690/2/1.png'),
(@var_1_bianco, '../immagini/motherboard/z690/2/2.png'),
(@var_1_bianco, '../immagini/motherboard/z690/2/3.png'),
(@var_9_nero, '../immagini/RAM/2/nero/1.png'),
(@var_9_nero, '../immagini/RAM/2/nero/2.png'),
(@var_9_nero, '../immagini/RAM/2/nero/3.png'),
(@var_9_bianco, '../immagini/RAM/2/bianco/1.png'),
(@var_9_bianco, '../immagini/RAM/2/bianco/2.png'),
(@var_9_bianco, '../immagini/RAM/2/bianco/3.png');

-- Inserimento delle varianti di wattaggio per PSU
INSERT INTO varianti_wattaggio (prodotto_id, wattaggio) VALUES
(7, '750W'),
(7, '850W'),
(7, '1000W'),
(8, '750W');

-- Recupero degli ID delle varianti di wattaggio per inserire le immagini
SET @var_7_750 = (SELECT id FROM varianti_wattaggio WHERE prodotto_id = 7 AND wattaggio = '750W');
SET @var_7_850 = (SELECT id FROM varianti_wattaggio WHERE prodotto_id = 7 AND wattaggio = '850W');
SET @var_7_1000 = (SELECT id FROM varianti_wattaggio WHERE prodotto_id = 7 AND wattaggio = '1000W');
SET @var_8_750 = (SELECT id FROM varianti_wattaggio WHERE prodotto_id = 8 AND wattaggio = '750W');

-- Inserimento delle immagini per le varianti di wattaggio
INSERT INTO varianti_wattaggio_immagini (variante_id, url) VALUES
(@var_7_750, '../immagini/PSU/1/3/1.png'),
(@var_7_750, '../immagini/PSU/1/2.png'),
(@var_7_750, '../immagini/PSU/1/3.png'),
(@var_7_850, '../immagini/PSU/1/1/1.png'),
(@var_7_850, '../immagini/PSU/1/2.png'),
(@var_7_850, '../immagini/PSU/1/3.png'),
(@var_7_1000, '../immagini/PSU/1/2/1.png'),
(@var_7_1000, '../immagini/PSU/1/2.png'),
(@var_7_1000, '../immagini/PSU/1/3.png'),
(@var_8_750, '../immagini/PSU/2/1.png'),
(@var_8_750, '../immagini/PSU/2/2.png'),
(@var_8_750, '../immagini/PSU/2/3.png');


-- ================================ Preassemblati==========================
-- Aggiungiamo la tabella dei PC preassemblati al database esistente

CREATE TABLE IF NOT EXISTS catalogo_pc_parts.preassemblati (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(50) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descrizione TEXT NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    immagine VARCHAR(255) NOT NULL
);

-- Inseriamo i dati dal file JSON
INSERT INTO catalogo_pc_parts.preassemblati (id, categoria, nome, descrizione, prezzo, immagine) VALUES
(20, 'PC Fissi', 'PC Gaming Entry Level', 'Intel Core i5-12400F / 32 GB / 1 TB SSD / RTX4060', 899.99, '../immagini/preassemblati/1/nero/1.jpeg'),
(21, 'PC Fissi', 'PC Gaming Mid Range', 'Intel Core i5-14600KF/32GB/2TB SSD/RTX 4070 Super', 1699.99, '../immagini/preassemblati/2/1.jpeg'),
(22, 'PC Fissi', 'PC Gaming High End', 'AMD Ryzen 7 7800X3D/32GB/2TB SSD/RTX 4070 Ti SUPER', 2569.99, '../immagini/preassemblati/3/1.jpeg'),
(23, 'PC Fissi', 'PC Workstation', 'Intel Intel Core Ultra 9 285K / 64 GB RAM / 2 TB SSD / RTX 5080 + Windows 11 Pro', 3599.99, '../immagini/preassemblati/4/1.jpeg'),
(24, 'Laptop', 'Laptop Gaming Entry Level', 'ASUS TUF Gaming A15 2023 FA507NV-LP041 AMD Ryzen 7 7735HS/16GB/1TB SSD/RTX 4060/15.6', 1056.99, '../immagini/preassemblati/5/1.jpeg'),
(25, 'Laptop', 'Laptop Gaming Mid Range', 'Intel® Core™ i7 i7-13620H 32 GB DDR5-SDRAM 1 TB SSD NVIDIA GeForce RTX 4070', 1699.99, '../immagini/preassemblati/6/1.jpeg'),
(26, 'Laptop', 'Laptop Gaming High End', 'Gigabyte AORUS 17H BXF-74ES554SH Intel Core i7-13700H/16GB/1TB SSD/RTX 4080/17.3', 2269.00, '../immagini/preassemblati/7/1.jpeg'),
(27, 'Laptop', 'Laptop Workstation', 'ROG Zephyrus M16 GU604', 2999.99, '../immagini/preassemblati/8/1.jpeg');

-- Tabella per le specifiche dettagliate
CREATE TABLE IF NOT EXISTS preassemblati_specifiche_base (
    preassemblato_id INT NOT NULL,
    nome_specifica VARCHAR(100) NOT NULL,
    valore VARCHAR(255) NOT NULL,
    PRIMARY KEY (preassemblato_id, nome_specifica),
    FOREIGN KEY (preassemblato_id) REFERENCES preassemblati(id)
);

-- Tabella per le specifiche dettagliate
CREATE TABLE IF NOT EXISTS preassemblati_specifiche_dettagliate (
    preassemblato_id INT NOT NULL,
    nome_specifica VARCHAR(100) NOT NULL,
    valore VARCHAR(255) NOT NULL,
    PRIMARY KEY (preassemblato_id, nome_specifica),
    FOREIGN KEY (preassemblato_id) REFERENCES preassemblati(id)
);

-- Tabella per le immagini aggiuntive
CREATE TABLE IF NOT EXISTS preassemblati_immagini (
    id INT AUTO_INCREMENT PRIMARY KEY,
    preassemblato_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    is_principale BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (preassemblato_id) REFERENCES preassemblati(id)
);

-- Tabella per i colori disponibili
CREATE TABLE IF NOT EXISTS preassemblati_colori (
    preassemblato_id INT NOT NULL,
    colore VARCHAR(50) NOT NULL,
    PRIMARY KEY (preassemblato_id, colore),
    FOREIGN KEY (preassemblato_id) REFERENCES preassemblati(id)
);

-- Tabella per le immagini di varianti colore
CREATE TABLE IF NOT EXISTS preassemblati_varianti_colore (
    id INT AUTO_INCREMENT PRIMARY KEY,
    preassemblato_id INT NOT NULL,
    colore VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    FOREIGN KEY (preassemblato_id) REFERENCES preassemblati(id)
);

-- Tabella per le opzioni di personalizzazione
CREATE TABLE IF NOT EXISTS preassemblati_personalizzazioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    preassemblato_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (preassemblato_id) REFERENCES preassemblati(id)
);

-- Inserimento delle immagini principali e secondarie
-- PC Gaming Entry Level (ID: 20)
INSERT INTO preassemblati_immagini (preassemblato_id, url, is_principale) VALUES
(20, '../immagini/preassemblati/1/nero/1.jpeg', TRUE),
(20, '../immagini/preassemblati/1/nero/2.jpeg', FALSE);

-- PC Gaming Mid Range (ID: 21)
INSERT INTO preassemblati_immagini (preassemblato_id, url, is_principale) VALUES
(21, '../immagini/preassemblati/2/1.jpeg', TRUE),
(21, '../immagini/preassemblati/2/2.jpeg', FALSE);

-- PC Gaming High End (ID: 22)
INSERT INTO preassemblati_immagini (preassemblato_id, url, is_principale) VALUES
(22, '../immagini/preassemblati/3/1.jpeg', TRUE),
(22, '../immagini/preassemblati/3/2.jpeg', FALSE);

-- PC Workstation (ID: 23)
INSERT INTO preassemblati_immagini (preassemblato_id, url, is_principale) VALUES
(23, '../immagini/preassemblati/4/1.jpeg', TRUE),
(23, '../immagini/preassemblati/4/2.jpeg', FALSE),
(23, '../immagini/preassemblati/4/6.jpeg', FALSE),
(23, '../immagini/preassemblati/4/8.jpeg', FALSE);

-- Laptop Gaming Entry Level (ID: 24)
INSERT INTO preassemblati_immagini (preassemblato_id, url, is_principale) VALUES
(24, '../immagini/preassemblati/5/1.jpeg', TRUE),
(24, '../immagini/preassemblati/5/2.jpeg', FALSE),
(24, '../immagini/preassemblati/5/6.jpeg', FALSE);

-- Laptop Gaming Mid Range (ID: 25)
INSERT INTO preassemblati_immagini (preassemblato_id, url, is_principale) VALUES
(25, '../immagini/preassemblati/6/1.jpeg', TRUE),
(25, '../immagini/preassemblati/6/2.jpeg', FALSE);

-- Laptop Gaming High End (ID: 26)
INSERT INTO preassemblati_immagini (preassemblato_id, url, is_principale) VALUES
(26, '../immagini/preassemblati/7/1.jpeg', TRUE),
(26, '../immagini/preassemblati/7/2.jpeg', FALSE),
(26, '../immagini/preassemblati/7/4.jpeg', FALSE);

-- Laptop Workstation (ID: 27)
INSERT INTO preassemblati_immagini (preassemblato_id, url, is_principale) VALUES
(27, '../immagini/preassemblati/8/1.jpeg', TRUE),
(27, '../immagini/preassemblati/8/2.jpeg', FALSE);

-- Inserimento delle informazioni aggiuntive per PC Gaming Entry Level
INSERT INTO preassemblati_immagini (preassemblato_id, url, is_principale) VALUES
(20, '../immagini/preassemblati/1/2.jpeg', FALSE),
(20, '../immagini/preassemblati/1/3.jpeg', FALSE),
(20, '../immagini/preassemblati/1/4.jpeg', FALSE);

-- Inserimento dei colori disponibili
INSERT INTO preassemblati_colori (preassemblato_id, colore) VALUES
(20, 'Nero'),
(20, 'Bianco');

-- Inserimento delle varianti colore per PC Gaming Entry Level
INSERT INTO preassemblati_varianti_colore (preassemblato_id, colore, url) VALUES
(20, 'Nero', '../immagini/preassemblati/1/nero/1.jpeg'),
(20, 'Nero', '../immagini/preassemblati/1/nero/2.jpeg'),
(20, 'Bianco', '../immagini/preassemblati/1/bianco/1.jpeg'),
(20, 'Bianco', '../immagini/preassemblati/1/bianco/2.jpeg');

-- Inserimento delle specifiche di base per ogni prodotto
-- PC Gaming Entry Level (ID: 20)
INSERT INTO preassemblati_specifiche_base (preassemblato_id, nome_specifica, valore) VALUES
(20, 'Processore', 'Intel Core i5-12400F'),
(20, 'Scheda Video', 'NVIDIA RTX 4060'),
(20, 'RAM', '32GB'),
(20, 'Archiviazione', '1TB SSD'),
(20, 'Sistema Operativo', 'Windows 11');

-- PC Gaming Mid Range (ID: 21)
INSERT INTO preassemblati_specifiche_base (preassemblato_id, nome_specifica, valore) VALUES
(21, 'Processore', 'Intel Core i5-14600KF'),
(21, 'Scheda Video', 'NVIDIA RTX 4070 Super'),
(21, 'RAM', '32GB'),
(21, 'Archiviazione', '2TB SSD'),
(21, 'Sistema Operativo', 'Windows 11 Pro');

-- PC Gaming High End (ID: 22)
INSERT INTO preassemblati_specifiche_base (preassemblato_id, nome_specifica, valore) VALUES
(22, 'Processore', 'AMD Ryzen 7 7800X3D'),
(22, 'Scheda Video', 'NVIDIA RTX 4070 Ti SUPER'),
(22, 'RAM', '32GB'),
(22, 'Archiviazione', '2TB SSD'),
(22, 'Sistema Operativo', 'Windows 11 Pro');

-- PC Workstation (ID: 23)
INSERT INTO preassemblati_specifiche_base (preassemblato_id, nome_specifica, valore) VALUES
(23, 'Processore', 'Intel Core Ultra 9 285K'),
(23, 'Scheda Video', 'NVIDIA RTX 5080'),
(23, 'RAM', '64GB'),
(23, 'Archiviazione', '2TB SSD'),
(23, 'Sistema Operativo', 'Windows 11 Pro');

-- Laptop Gaming Entry Level (ID: 24)
INSERT INTO preassemblati_specifiche_base (preassemblato_id, nome_specifica, valore) VALUES
(24, 'Processore', 'AMD Ryzen 7 7735HS'),
(24, 'Scheda Video', 'NVIDIA RTX 4060'),
(24, 'RAM', '16GB'),
(24, 'Archiviazione', '1TB SSD'),
(24, 'Schermo', '15.6"'),
(24, 'Sistema Operativo', 'SENZA SISTEMA OPERATIVO');

-- Laptop Gaming Mid Range (ID: 25)
INSERT INTO preassemblati_specifiche_base (preassemblato_id, nome_specifica, valore) VALUES
(25, 'Processore', 'Intel Core i7-13620H'),
(25, 'Scheda Video', 'NVIDIA RTX 4070'),
(25, 'RAM', '32GB DDR5'),
(25, 'Archiviazione', '1TB SSD'),
(25, 'Sistema Operativo', 'Windows 11 Pro');

-- Laptop Gaming High End (ID: 26)
INSERT INTO preassemblati_specifiche_base (preassemblato_id, nome_specifica, valore) VALUES
(26, 'Processore', 'Intel Core i7-13700H'),
(26, 'Scheda Video', 'NVIDIA RTX 4080'),
(26, 'RAM', '16GB'),
(26, 'Archiviazione', '1TB SSD'),
(26, 'Schermo', '17.3"'),
(26, 'Sistema Operativo', 'Windows 11');

-- Laptop Workstation (ID: 27)
INSERT INTO preassemblati_specifiche_base (preassemblato_id, nome_specifica, valore) VALUES
(27, 'Processore', 'Intel Core i9'),
(27, 'Scheda Video', 'NVIDIA RTX 4080'),
(27, 'RAM', '32GB'),
(27, 'Archiviazione', '1TB SSD'),
(27, 'Schermo', '16"'),
(27, 'Sistema Operativo', 'Windows 11 Pro');

-- Inserimento delle specifiche dettagliate per ogni prodotto
-- PC Gaming Entry Level (ID: 20)
INSERT INTO preassemblati_specifiche_dettagliate (preassemblato_id, nome_specifica, valore) VALUES
(20, 'Custodia', 'MSI MAG Forge 100M Vetro temperato USB 3.2 RGB Nero'),
(20, 'Alimentazione', 'PSU Forgeon serie SI 650W 80+ Bronzo'),
(20, 'Scheda madre', 'Gigabyte B760M DS3H DDR4'),
(20, 'Disco rigido', 'WD Blue SN580 SSD M.2 PCIe 4.0 NVMe da 1 TB'),
(20, 'Scheda grafica', 'GeForce RTX 4060 WINDFORCE 8GB GDDR6 DLSS3'),
(20, 'Porte HDMI/Display', '2 porte HDMI/2 porte Display'),
(20, 'Scheda audio', 'Integrata'),
(20, 'Raffreddamento della CPU', 'ventola CPU Tempest Cooler 4Pipes 120mm RGB nera'),
(20, 'Scheda di rete', 'Adattatore PCIe ASUS PCE-AX1800 AX1800 WiFi 6 + Bluetooth 5.2'),
(20, 'Collegamenti frontali', '2 porte USB 3.2 Gen1 tipo A, 1 audio HD/1 microfono'),
(20, 'Collegamenti posteriori', '2 porte USB 2.0/1.1, 1 porta per tastiera/mouse PS/2, 1 porta D-Sub, 1 porta HDMI, 2 porte di visualizzazione, 3 porte USB 3.2 di prima generazione, 1 porta USB Type-C®, compatibile con USB 3.2 Gen 2, 1 porta RJ-45, 3 connettori audio'),
(20, 'Dimensioni', '421 (P) x 210 (L) x 499 (A) mm');

-- PC Gaming Mid Range (ID: 21)
INSERT INTO preassemblati_specifiche_dettagliate (preassemblato_id, nome_specifica, valore) VALUES
(21, 'Sorgente', '850W (o simile, a seconda della disponibilità)'),
(21, 'Processore', 'Intel Core i5 14600KF'),
(21, 'Scheda Madre', 'Z690/B650 (o simile, a seconda della disponibilità)'),
(21, 'RAM', '32 GB DDR4'),
(21, 'Raffreddamento A Liquido', '360mm'),
(21, 'Magazzinaggio', 'SSD M.2 da 2TB'),
(21, 'Scheda Grafica', 'RTX 4070 Super'),
(21, 'Wi-Fi', 'Sì'),
(21, 'Sistema Operativo', 'Windows 11 PRO installato e attivato');

-- PC Gaming High End (ID: 22)
INSERT INTO preassemblati_specifiche_dettagliate (preassemblato_id, nome_specifica, valore) VALUES
(22, 'Scatola', 'Cougar Semitower FV270 RGB Nero'),
(22, 'Alimentazione', 'PCIE 5.0 80 Plus Gold completamente modulare da 750 W'),
(22, 'Processore', 'AMD Ryzen 7 7800X3D (8 core, fino a 5,0 GHz)'),
(22, 'Scheda Madre', 'X670 GAMING WIFI ATX'),
(22, 'Disco rigido', 'SSD M.2 PCIe NVMe da 2 TB'),
(22, 'Memoria RAM', '32 GB di RAM DDR5 6000 MHz RGB'),
(22, 'Grafica', 'RTX 4070 Ti SUPER 16GB GDDR6X'),
(22, 'Raffreddamento', 'ARGB liquido da 360 mm + 3 ventole ARGB da 120 mm'),
(22, 'Connettività', 'WiFi/Ethernet integrati'),
(22, 'Sistema Operativo', 'Windows 11 Pro 64Bits installato e attivato con licenza');

-- PC Workstation (ID: 23)
INSERT INTO preassemblati_specifiche_dettagliate (preassemblato_id, nome_specifica, valore) VALUES
(23, 'Case', 'Nfortec Ursa ARGB Mid Tower E-ATX Vetro temperato USB-C Nero PC Tower'),
(23, 'Alimentazione', 'Alimentatore Forgeon Bolt PSU 850W 80+ Gold completamente modulare'),
(23, 'Processore', 'Intel Core Ultra 9 285K 3.2/5.7GHz'),
(23, 'Scheda Madre', 'MSI Z890 GAMING PLUS WIFI'),
(23, 'Disco rigido', 'Forgeon SSD 2 TB 7400 MB/S NVMe PCIe 4.0 M.2 Gen4'),
(23, 'RAM', 'Corsair Vengeance RGB DDR5 6000MHz 64GB 2x32GB CL30 Dual AMD EXPO e memoria Intel XMP'),
(23, 'Scheda Grafica', 'RTX 5080 16GB Gaming GDDR7 DLSS4'),
(23, 'Uscite Video', '1x HDMI / 3x DisplayPort'),
(23, 'Scheda Audio', 'Integrata'),
(23, 'Scheda di Rete', 'Integrata'),
(23, 'Raffreddamento CPU', 'Kit di raffreddamento a liquido Nfortec ATRIA X 360mm Nero');

-- Laptop Gaming Entry Level (ID: 24)
INSERT INTO preassemblati_specifiche_dettagliate (preassemblato_id, nome_specifica, valore) VALUES
(24, 'Processore', 'AMD Ryzen 7 7735HS (8C/OctaCore 3,2/4,75 GHz, 20 MB)'),
(24, 'Memoria RAM', '16 GB SO-DIMM DDR5 4800 MHz'),
(24, 'Archiviazione', 'SSD M.2 NVMe PCI Express 4.0 da 1 TB'),
(24, 'Display', '15,6 pollici, FHD (1920 x 1080) 16:9, livello Value IPS, display antiriflesso, sRGB:100%, Adobe:75,35%, frequenza di aggiornamento:144Hz, G-Sync, interruttore MUX + NVIDIA® Advanced Ottimo'),
(24, 'GPU', 'NVIDIA® GeForce RTX™ 4060, 2420 MHz* a 140 W (clock boost 2370 MHz+50 MHz OC, 115 W+25 W boost dinamico), 8 GB GDDR6'),
(24, 'Connettività', 'LAN Ethernet 10/100/1000Mbps, Wi-Fi 6 (802.11ax) 2x2 + Bluetooth 5.2'),
(24, 'Webcam', 'HD720p'),
(24, 'Audio', '2 altoparlanti / Microfono integrato / Dolby Atmos / Cancellazione del rumore AI / Certificazione Hi-Res'),
(24, 'Tastiera', 'Bubblegum retroilluminata RGB, spagnola'),
(24, 'Touchpad', 'Pannello multitouch'),
(24, 'Batteria', '90 Wh, 4 celle, ioni di litio'),
(24, 'Connessioni', '2 porte USB 3.2 Gen 1 (3.1 Gen 1) * 2, 1 USB 2.0, 1 audio/microfono, 1 RJ-45, 1 HDMI 2.1, 1 jack audio combinato per cuffie/microfono da 3,5 mm'),
(24, 'Sicurezza', 'Password amministratore BIOS e protezione password utente, Slot Kensington');

-- Laptop Gaming Mid Range (ID: 25)
INSERT INTO preassemblati_specifiche_dettagliate (preassemblato_id, nome_specifica, valore) VALUES
(25, 'Display', '15.6" FHD (1920*1080), 144Hz 45%NTSC IPS-Level'),
(25, 'Processore', 'Intel® Core™ i7-13620H (10 core, frequenza turbo massima 4,90 GHz, Intel® Smart Cache da 24 MB)'),
(25, 'Memoria', 'RAM installata: 32 GB, Tipo di RAM: DDR5-SDRAM, Velocità memoria: 5200 MHz, Struttura memoria: 2 x 16 GB, RAM massima supportata: 64 GB'),
(25, 'Archiviazione', 'Capacità totale di archiviazione: 1 TB, Supporto di memoria: SSD, Capacità SSD totale: 1 TB, Numero di SSD installati: 1, Interfaccia SSD: PCI Express 4.0, NVMe: Sì'),
(25, 'Grafica', 'Scheda grafica dedicata: NVIDIA GeForce RTX 4070, Memoria Grafica Dedicata: 8 GB, Tipo di memoria della scheda grafica: GDDR6, Scheda grafica integrata: Intel Iris Xe Graphics'),
(25, 'Audio', 'Numero di altoparlanti incorporati: 2, Potenza altoparlante: 2 W'),
(25, 'Macchina fotografica', 'Fotocamera frontale: Sì, Tipo HD della fotocamera anteriore: HD, Velocità acquisizione video: 30 fps'),
(25, 'Rete', 'Wi-Fi 6 (802.11ax), Bluetooth 5.2'),
(25, 'Connettività', 'USB 2.0: 1, USB 3.2 Gen 1 (3.1 Gen 1) Tipo A: 2, USB 3.2 Gen 1 (3.1 Gen 1) Tipo C: 1, Ethernet LAN (RJ-45): 1, HDMI: 1, Modalità alternativa DisplayPort USB Tipo C: Sì'),
(25, 'Tastiera', 'Tastiera numerica: Sì, Tastiera retroilluminata: Sì, Colore della retroilluminazione: RGB, Zona retroilluminata: RGB a 4 zone'),
(25, 'Batteria', 'Numero di celle della batteria: 3, Capacità della batteria: 53,5 Wh'),
(25, 'Gestione energetica', 'Adattatore dissipazione di potenza AC: 240 W'),
(25, 'Dimensioni e peso', 'Larghezza: 359 mm, Profondità: 259 mm, Altezza: 24,9 mm, Peso: 2,25 kg');

-- Laptop Gaming High End (ID: 26)
INSERT INTO preassemblati_specifiche_dettagliate (preassemblato_id, nome_specifica, valore) VALUES
(26, 'Processore', 'Intel® Core™ i7-13700H di tredicesima generazione da 5,0 GHz (24 MB di cache, fino a 5,0 GHz, 6 P-core e 8 E-core)'),
(26, 'Memoria', 'RAM DDR5 8GB*2 (DDR5-4800, massimo 64 GB, 2 slot DDR5)'),
(26, 'Archiviazione', 'Gen4 1 TB, 2 slot SSD M.2 (tipo 2280, supporta 2 NVMe™ PCIe® Gen 4.0x4)'),
(26, 'Display', 'FHD da 17,3" con cornice sottile 1920x1080, 360 Hz, 100% sRGB, certificato TÜV Rheinland'),
(26, 'Grafica', 'NVIDIA® GeForce RTX™ 4080 GPU per laptop 12 GB GDDR6, Boost Clock 2010 MHz, Potenza grafica massima 150 W'),
(26, 'Connettività', 'Wi-Fi 6E (802.11ax) (Tripla banda) 2x2, Bluetooth® V5.2'),
(26, 'Fotocamera', 'Webcam HD (720p), Microfono array integrato, Supporto per l''autenticazione facciale di Windows Hello'),
(26, 'Batteria', 'Batteria ai polimeri di litio 99Wh'),
(26, 'Connessioni', 'Lato sinistro: 1 RJ45, 1 HDMI 2.1, 1 Mini DP 1.4 (120 Hz), 1 USB 3.2 Gen1 (tipo A); Lato destro: 1 ingresso CC, 1 Thunderbolt™ 4 (tipo C), 1 USB 3.2 Gen1 (tipo A), 1 jack combinato audio'),
(26, 'Sistema operativo', 'Windows 11 Home a 64 bit'),
(26, 'Tastiera', 'Tastiera RGB Fusion stile isola con controllo retroilluminato per tasto, macro per tasto'),
(26, 'Dimensioni e peso', '39,8 cm (L) x 25,4 cm (P) x 3,0 cm (A), Peso: 2,7 kg'),
(26, 'Colore', 'Nero');

-- Laptop Workstation (ID: 27)
INSERT INTO preassemblati_specifiche_dettagliate (preassemblato_id, nome_specifica, valore) VALUES
(27, 'Processore', 'Intel® Core™ i9-13900H 13th Gen da 2,6 GHz (24M Cache, fino a 5,4 GHz, 14 core: 6 core P e 8 core E)'),
(27, 'Scheda Video', 'NVIDIA® GeForce RTX™ 4080 Laptop GPU (542 AI TOPs), ROG Boost: 1715MHz* at 145W (1665MHz Boost Clock+50MHz OC, 120W+25W Dynamic Boost, 125W+25W in Manual Mode), 12GB GDDR6'),
(27, 'Display', 'ROG Nebula HDR Display, 16 pollici, WQXGA (2560 x 1600) 16:10, Mini LED, Display antiriflesso, DCI-P3: 100%, Tempo di risposta: 3ms, Convalidato Pantone, MUX Switch + NVIDIA® Advanced Optimus'),
(27, 'Memoria', '32GB DDR5-4800 SO-DIMM, Capacità massima: 64GB, Supporta la memoria a doppio canale'),
(27, 'Archiviazione', '1TB (512GB + 512GB PCIe® 4.0 NVMe™ M.2 Performance SSD RAID 0)'),
(27, 'Tastiera', 'Tastiera Chiclet retroilluminata a 1 zona RGB'),
(27, 'Webcam', 'Telecamera IR 1080P FHD per Windows Hello'),
(27, 'Audio', 'Tecnologia Smart Amp, Dolby Atmos, Tecnologia di cancellazione del rumore AI, 2x 2W tweeter'),
(27, 'Batteria', '90WHrs, 4S1P, 4-cell Li-ion'),
(27, 'Peso', '2.10 Kg (4.63 lbs)'),
(27, 'Dimensioni', '35.5 x 24.6 x 1.99 ~ 2.23 cm (13.98" x 9.69" x 0.78" ~ 0.88")'),
(27, 'Inclusi nella confezione', 'Alimentatore aggiuntivo universale TYPE-C, 100W CA, Uscita: 20V DC, 5A, 100W, Ingresso: 100~240V CA 50/60Hz');

-- Inserimento delle personalizzazioni per ogni prodotto
-- PC Gaming Entry Level (ID: 20)
INSERT INTO preassemblati_personalizzazioni (preassemblato_id, nome, prezzo) VALUES
(20, 'Aggiungi GPU GeForce RTX 4070', 500),
(20, 'Aggiungi memoria RAM 64GB', 250),
(20, 'Aggiungi un secondo SSD da 1TB', 120),
(20, 'Upgrade CPU Intel Core i7-12700K', 350),
(20, 'Aggiungi sistema di raffreddamento a liquido', 150),
(20, 'Personalizzazione estetica RGB con pannelli laterali in vetro temperato', 80);

-- PC Gaming Mid Range (ID: 21)
INSERT INTO preassemblati_personalizzazioni (preassemblato_id, nome, prezzo) VALUES
(21, 'Aggiungi GPU GeForce RTX 4080', 1500),
(21, 'Aggiungi memoria RAM 64GB', 250),
(21, 'Aggiungi un secondo SSD da 1TB', 120),
(21, 'Upgrade CPU Intel Core i7-12700K', 350),
(21, 'Aggiungi sistema di raffreddamento a liquido', 150),
(21, 'Personalizzazione estetica RGB con pannelli laterali in vetro temperato', 80);

-- PC Gaming High End (ID: 22)
INSERT INTO preassemblati_personalizzazioni (preassemblato_id, nome, prezzo) VALUES
(22, 'Aggiungi GPU GeForce RTX 4090', 1200),
(22, 'Aggiungi 64GB di RAM DDR5 6000 MHz RGB', 400),
(22, 'Aggiungi un secondo SSD da 2TB', 200),
(22, 'Upgrade a processore AMD Ryzen 9 7900X', 800),
(22, 'Aggiungi sistema di raffreddamento a liquido ARGB 480mm', 250),
(22, 'Personalizzazione estetica con pannelli laterali in vetro temperato RGB', 100);

-- PC Workstation (ID: 23)
INSERT INTO preassemblati_personalizzazioni (preassemblato_id, nome, prezzo) VALUES
(23, 'Aggiungi GPU RTX 5090', 2500),
(23, 'Aggiungi 128GB di RAM DDR5', 450),
(23, 'Aggiungi un secondo SSD 2TB', 250),
(23, 'Upgrade al raffreddamento a liquido 480mm', 300)




