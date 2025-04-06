use catalogo_pc_parts;

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