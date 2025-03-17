create database ecommerce;

use ecommerce;

CREATE TABLE prodotto (
    id_prodotto INT PRIMARY key auto_increment,
    nome VARCHAR(100) NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    descrizione TEXT,
    id_categoria INT,
    id_marca int,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria),
    FOREIGN KEY (id_marca) REFERENCES marca(id_marca)
);

create table categoria(
id_categoria INT PRIMARY key auto_increment,
nome_categoria varchar(100)
);

create table marca(
id_marca INT PRIMARY key auto_increment,
nome_marca varchar(100)
);

CREATE TABLE filtro (
    id_filtro INT PRIMARY key auto_increment,
    tipo_filtro ENUM('ricerca', 'categorie', 'prezzo', 'marche') NOT NULL,
    valori JSON
);

CREATE TABLE filtro_prezzo (
    id_filtro INT,
    min DECIMAL(10, 2),
    max DECIMAL(10, 2),
    step DECIMAL(10, 2),
    FOREIGN KEY (id_filtro) REFERENCES filtro(id_filtro)
);

CREATE TABLE immagine (
   	id_immagine INT PRIMARY key auto_increment,
    id_prodotto INT,
	url_immagine varchar(255),
    FOREIGN KEY (id_prodotto) REFERENCES prodotto(id_prodotto)
);

create table specifiche_prodotto(
id_specifica INT PRIMARY key auto_increment,
id_prodotto int,
nome_specifica varchar(100),
valore_specifica varchar(300),
FOREIGN KEY (id_prodotto) REFERENCES prodotto(id_prodotto)
);

create table varianti_prodotto(
id_variante INT PRIMARY key auto_increment,
id_prodotto int,
nome_specifica varchar(100),
descrizione text,
prezzo_variante DECIMAL(10, 2) NOT NULL, 
FOREIGN KEY (id_prodotto) REFERENCES prodotto(id_prodotto)
);

create table personalizzazione(
id_personalizzazione INT PRIMARY key auto_increment,
id_prodotto int,
nome_personalizzazione varchar(100),
prezzo_personalizzazione DECIMAL(10, 2) NOT NULL, 
FOREIGN KEY (id_prodotto) REFERENCES prodotto(id_prodotto)
);

/*===============Inserimento file catalogo.json=================*/

INSERT INTO categoria (nome_categoria) VALUES 
('Scheda Madre'),
('GPU'),
('PSU'),
('RAM'),
('Storage');

INSERT INTO marca (nome_marca) VALUES 
('ASUS'),
('GIGABYTE'),
('MSI'),
('Corsair'),
('THERMALTAKE'),
('Samsung'),
('SK hynix');


-- Inserimento del filtro per 'ricerca'
INSERT INTO filtro (tipo_filtro, valori) VALUES 
('ricerca', '"Cerca un prodotto..."');

-- Inserimento del filtro per 'categorie'
INSERT INTO filtro (tipo_filtro, valori) VALUES 
('categorie', '["CPU", "GPU", "RAM", "PSU", "Storage", "Case", "Scheda Madre", "Raffreddamento"]');

-- Inserimento del filtro per 'prezzo'
INSERT INTO filtro (tipo_filtro, valori) VALUES 
('prezzo', '{"min": 0, "max": 2000, "step": 10}');

-- Inserimento del filtro per 'marche'
INSERT INTO filtro (tipo_filtro, valori) VALUES 
('marche', '["Intel", "AMD", "NVIDIA", "Corsair", "MSI", "ASUS", "NZXT", "Gigabyte", "Thermaltake", "Samsung", "SK hynix"]');


-- Popolamento della tabella filtro_prezzo (assumiamo che l'ID del filtro per prezzo sia 3)
INSERT INTO filtro_prezzo (id_filtro, min, max, step) VALUES 
(3, 0, 2000, 10);


INSERT INTO prodotto (nome, prezzo, descrizione, id_categoria, id_marca) VALUES
('ASUS ROG Strix Z690-E Gaming WiFi', 379.99, 'Scheda Madre ATX, Intel Z690, LGA1700, DDR5, PCI 5.0, WiFi 6E, 4xM.2, 6xSATA', 1, 1),
('ASUS ROG Strix Z690-A Gaming WiFi', 349.99, 'Scheda Madre ATX, Intel Z690, LGA1700, DDR5, PCI 5.0, WiFi 6E, 4xM.2, 6xSATA', 1, 1),
('GIGABYTE Z490 AORUS MASTER', 449.99, 'Scheda Madre ATX, Intel Z490, LGA1200, DDR4, PCI 3.0, WiFi 6, 3xM.2, 6xSATA', 1, 2),
('GIGABYTE Z490 AORUS Elite AC', 229.99, 'Scheda Madre ATX, Intel Z490, LGA1200, DDR4, PCI 3.0, WiFi 5, 3xM.2, 6xSATA', 1, 2),
('MSI GeForce RTX 4080 16Go', 1599.99, 'Scheda Grafica, NVIDIA GeForce RTX 4080, 16GB GDDR6X', 2, 3),
('MSI GeForce RTX 4080 16GB SUPRIM', 1799.99, 'Scheda Grafica, NVIDIA GeForce RTX 4080, 16GB GDDR6X', 2, 3),
('Corsair RM850X 80 Plus Gold', 139.99, 'Alimentatore 850W, 80 Plus Gold, Completamente Modulare', 3, 4),
('THERMALTAKE ALIM. TOUGHPOWER GF1 750W ARGB FULL MODULAR 80 PLUS GOLD', 119.99, 'Alimentatore 750W, 80 Plus Gold, Completamente Modulare', 3, 5),
('CORSAIR VENGEANCE RGB DDR5 RAM 32GB (2x16GB) 5200MHz', 199.99, 'Memoria RAM DDR5, 32GB, 5200MHz', 4, 4),
('Samsung 970 EVO Plus PCIe NVMe M.2', 129.99, 'SSD PCIe NVMe, 1TB, M.2', 5, 6),
('SK hynix Platinum P41 NVMe SSD', 399.99, 'SSD PCIe NVMe, 1TB, M.2', 5, 7);


INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(1, '../immagini/motherboard/z690/1/1.png'),
(2, '../immagini/motherboard/z690/2/1.png'),
(3, '../immagini/motherboard/gigabyte/1/1.png'),
(4, '../immagini/motherboard/gigabyte/2/1.png'),
(5, '../immagini/GPU/4080/1/1.png'),
(6, '../immagini/GPU/4080/2/1.png'),
(7, '../immagini/PSU/1/1/1.png'),
(8, '../immagini/PSU/2/1.png'),
(9, '../immagini/RAM/2/bianco/1.png'),
(10, '../immagini/SSD/1/1.png'),
(11, '../immagini/SSD/2/1.png');


/*===============Inserimento file preassemblati.json=================*/

-- Assicurati di inserire la categoria "PC Fissi" e "Laptop"
INSERT INTO categoria (nome_categoria) VALUES
('PC Fissi'),
('Laptop');


INSERT INTO prodotto (nome, prezzo, descrizione, id_categoria, id_marca) VALUES
('PC Gaming Entry Level', 899.99, 'Intel Core i5-12400F / 32 GB / 1 TB SSD / RTX4060', 6, NULL),  -- NULL per la marca, aggiungi un valore se necessario
('PC Gaming Mid Range', 1699.99, 'Intel Core i5-14600KF/32GB/2TB SSD/RTX 4070 Super', 6, NULL),
('PC Gaming High End', 2569.99, 'AMD Ryzen 7 7800X3D/32GB/2TB SSD/RTX 4070 Ti SUPER', 6, NULL),
('PC Workstation', 3599.99, 'Intel Intel Core Ultra 9 285K / 64 GB RAM / 2 TB SSD / RTX 5080 + Windows 11 Pro', 6, NULL),
('Laptop Gaming Entry Level', 1056.99, 'ASUS TUF Gaming A15 2023 FA507NV-LP041 AMD Ryzen 7 7735HS/16GB/1TB SSD/RTX 4060/15.6', 7, NULL),
('Laptop Gaming Mid Range', 1699.99, 'Intel® Core™ i7 i7-13620H 32 GB DDR5-SDRAM 1 TB SSD NVIDIA GeForce RTX 4070', 7, NULL),
('Laptop Gaming High End', 2269.00, 'Gigabyte AORUS 17H BXF-74ES554SH Intel Core i7-13700H/16GB/1TB SSD/RTX 4080/17.3', 7, NULL),
('Laptop Workstation', 2999.99, 'ROG Zephyrus M16 GU604', 7, NULL);


INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(12, '../immagini/preassemblati/1/nero/1.jpeg'),
(13, '../immagini/preassemblati/2/1.jpeg'),
(14, '../immagini/preassemblati/3/1.jpeg'),
(15, '../immagini/preassemblati/4/1.jpeg'),
(16, '../immagini/preassemblati/5/1.jpeg'),
(17, '../immagini/preassemblati/6/1.jpeg'),
(18, '../immagini/preassemblati/7/1.jpeg'),
(19, '../immagini/preassemblati/8/1.jpeg');



/*===============Inserimento file contenuti_catalogo.json=================*/

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(1, 'Processore supportato', 'LGA 1700'),
(1, 'Tecnologia di memoria', 'DDR5'),
(1, 'Memoria massima supportata', '128 GB'),
(1, 'Clock di Memoria', '2133 MHz'),
(1, 'Interfaccia scheda grafica', 'PCI Express'),
(1, 'Tipo wireless', '802.11a/b/g/n/ac, Bluetooth, 802.11ax'),
(1, 'Numero di porte USB 2.0', '9'),
(1, 'Numero di porte HDMI', '1'),
(1, 'Numero di porte Ethernet', '1');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(2, 'Processore supportato', 'LGA 1700'),
(2, 'Tecnologia di memoria', 'DDR5'),
(2, 'Memoria massima supportata', '128 GB'),
(2, 'Clock di Memoria', '2133 MHz'),
(2, 'Interfaccia scheda grafica', 'PCI Express'),
(2, 'Tipo wireless', '802.11a/b/g/n/ac, Bluetooth, 802.11ax'),
(2, 'Numero di porte USB 2.0', '9'),
(2, 'Numero di porte HDMI', '1'),
(2, 'Numero di porte Ethernet', '1');

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(1, 'Colore', 'Nero', 379.99),
(1, 'Colore', 'Bianco', 379.99);

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(2, 'Colore', 'Nero', 379.99),
(2, 'Colore', 'Bianco', 379.99);

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(3, 'Processore supportato', 'LGA 1200'),
(3, 'Tecnologia di memoria', 'DDR4'),
(3, 'Memoria massima supportata', '128 GB'),
(3, 'Clock di Memoria', '5000 MHz'),
(3, 'Interfaccia scheda grafica', 'PCI Express');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(4, 'Processore supportato', 'LGA 1200'),
(4, 'Tecnologia di memoria', 'DDR4'),
(4, 'Clock di Memoria', '5000 MHz'),
(4, 'Interfaccia scheda grafica', 'PCI Express'),
(4, 'Tipo di connettività', 'Wi-Fi'),
(4, 'Tipo wireless', 'Bluetooth'),
(4, 'Numero di porte USB 2.0', '4'),
(4, 'Numero di porte HDMI', '1'),
(4, 'Piattaforma Hardware', 'Non specifico per macchina'),
(4, 'Le batterie sono incluse', 'No');

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(3, 'Colore', 'Nero', 449.99);

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(4, 'Colore', 'Nero', 229.99);

INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(3, '../immagini/motherboard/gigabyte/1/1.png'),
(3, '../immagini/motherboard/gigabyte/1/2.png'),
(3, '../immagini/motherboard/gigabyte/1/3.png');

INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(4, '../immagini/motherboard/gigabyte/2/1.png'),
(4, '../immagini/motherboard/gigabyte/2/2.png'),
(4, '../immagini/motherboard/gigabyte/2/3.png');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(5, 'Risoluzione', '5120x2880 (5K) at 60Hz'),
(5, 'Clock di Memoria', '1700 Modificatore sconosciuto'),
(5, 'Coprocessore grafico', 'NVIDIA GeForce RTX 4080'),
(5, 'Grafica Chipset Brand', 'NVIDIA'),
(5, 'Descrizione scheda grafica', 'Dedicato'),
(5, 'Tipo memoria scheda grafica', 'GDDR6X'),
(5, 'Dimensioni memoria scheda grafica', '16 GB'),
(5, 'Interfaccia scheda grafica', 'PCI-Express x16'),
(5, 'Le batterie sono incluse', 'No');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(6, 'Risoluzione', '5120x2880 (5K) at 60Hz'),
(6, 'Clock di Memoria', '1700 Modificatore sconosciuto'),
(6, 'Coprocessore grafico', 'NVIDIA GeForce RTX 4080'),
(6, 'Grafica Chipset Brand', 'NVIDIA'),
(6, 'Descrizione scheda grafica', 'Dedicato'),
(6, 'Tipo memoria scheda grafica', 'GDDR6X'),
(6, 'Dimensioni memoria scheda grafica', '16 GB'),
(6, 'Interfaccia scheda grafica', 'PCI-Express x16'),
(6, 'Le batterie sono incluse', 'No');

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(5, 'Colore', 'Nero', 1599.99),
(5, 'Colore', 'Argento', 1599.99);

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(6, 'Colore', 'Nero', 1599.99),
(6, 'Colore', 'Argento', 1599.99);

INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(5, '../immagini/GPU/4080/1/1.png'),
(5, '../immagini/GPU/4080/1/2.png'),
(5, '../immagini/GPU/4080/2/1.png'),
(5, '../immagini/GPU/4080/2/2.png');

INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(6, '../immagini/GPU/4080/1/1.png'),
(6, '../immagini/GPU/4080/1/2.png'),
(6, '../immagini/GPU/4080/2/1.png'),
(6, '../immagini/GPU/4080/2/2.png');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(7, 'Fattore di forma', 'ATX'),
(7, 'Tipologia di memoria computer', 'DIMM'),
(7, 'Voltaggio', '220 Volt'),
(7, 'Wattaggio', '750 Watt');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(8, 'Fattore di forma', 'ATX'),
(8, 'Voltaggio', '240 Volt'),
(8, 'Wattaggio', '750 watt');

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(7, 'Wattaggio', '750W', 139.99),
(7, 'Wattaggio', '850W', 139.99),
(7, 'Wattaggio', '1000W', 139.99);

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(8, 'Wattaggio', '750W', 119.99);

INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(7, '../immagini/PSU/1/1/1.png'),
(7, '../immagini/PSU/1/2.png'),
(7, '../immagini/PSU/1/3.png'),
(7, '../immagini/PSU/1/3/1.png'),
(7, '../immagini/PSU/1/2.png'),
(7, '../immagini/PSU/1/3.png');

INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(8, '../immagini/PSU/2/1.png'),
(8, '../immagini/PSU/2/2.png'),
(8, '../immagini/PSU/2/3.png');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(9, 'Fattore di forma', 'DIMM'),
(9, 'Dimensioni RAM', '32 GB'),
(9, 'Tecnologia di memoria', 'DDR5'),
(9, 'Tipologia di memoria computer', 'DDR5 SDRAM'),
(9, 'Clock di Memoria', '5200 MHz'),
(9, 'Voltaggio', '1,25 Volt');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(10, 'Fattore di forma', 'M.2 (2280)'),
(10, 'Interfaccia Hard-Disk', 'PCIE x 4'),
(10, 'Piattaforma Hardware', 'PC'),
(10, 'Peso articolo', '8 g');

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(9, 'Colore', 'Bianco', 199.99),
(9, 'Colore', 'Nero', 199.99),
(9, 'Dimensioni RAM', '2x16GB', 199.99),
(9, 'Dimensioni RAM', '2x32GB', 399.99);

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(10, 'Capacità', '250GB', 59.99),
(10, 'Capacità', '500GB', 89.99),
(10, 'Capacità', '1TB', 129.99);

INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(9, '../immagini/RAM/2/bianco/1.png'),
(9, '../immagini/RAM/2/bianco/2.png'),
(9, '../immagini/RAM/2/bianco/3.png'),
(9, '../immagini/RAM/2/nero/1.png'),
(9, '../immagini/RAM/2/nero/2.png'),
(9, '../immagini/RAM/2/nero/3.png');


INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(10, '../immagini/SSD/1/1.png'),
(10, '../immagini/SSD/1/2.png'),
(10, '../immagini/SSD/1/3.png');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(11, 'Fattore di forma', 'M.2'),
(11, 'Interfaccia Hard-Disk', 'PCIE x 4'),
(11, 'Voltaggio', '3,3 Volt'),
(11, 'Peso articolo', '7 g');

INSERT INTO varianti_prodotto (id_prodotto, nome_specifica, descrizione, prezzo_variante) VALUES
(11, 'Capacità', '500GB', 169.99),
(11, 'Capacità', '1TB', 249.99),
(11, 'Capacità', '2TB', 399.99);

INSERT INTO immagine (id_prodotto, url_immagine) VALUES
(11, '../immagini/SSD/2/1.png'),
(11, '../immagini/SSD/2/2.png'),
(11, '../immagini/SSD/2/3.png');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica) VALUES
(11, 'Marca', 'SK hynix'),
(11, 'Produttore', 'SK hynix'),
(11, 'Serie', 'SHPP41'),
(11, 'Colore', 'Multicolore'),
(11, 'Dimensioni prodotto', '8 x 2,2 x 0,22 cm; 7 grammi'),
(11, 'Numero modello articolo', 'SHPP41-2000GM-2'),
(11, 'Le batterie sono incluse', 'No'),
(11, 'Aggiornamenti software garantiti fino a', 'sconosciuto');


/*===============Inserimento file contenuti_pc.json=================*/

INSERT INTO personalizzazione (id_prodotto, nome_personalizzazione, prezzo_personalizzazione) 
VALUES 
(12, 'Aggiungi GPU GeForce RTX 4070', 500.00),
(12, 'Aggiungi memoria RAM 64GB', 250.00),
(12, 'Aggiungi un secondo SSD da 1TB', 120.00),
(12, 'Upgrade CPU Intel Core i7-12700K', 350.00),
(12, 'Aggiungi sistema di raffreddamento a liquido', 150.00),
(12, 'Personalizzazione estetica RGB con pannelli laterali in vetro temperato', 80.00);

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica)
VALUES
(12, 'Custodia', 'MSI MAG Forge 100M Vetro temperato USB 3.2 RGB Nero'),
(12, 'Alimentazione', 'PSU Forgeon serie SI 650W 80+ Bronzo'),
(12, 'Scheda madre', 'Gigabyte B760M DS3H DDR4'),
(12, 'Disco rigido', 'WD Blue SN580 SSD M.2 PCIe 4.0 NVMe da 1 TB'),
(12, 'Scheda grafica', 'GeForce RTX 4060 WINDFORCE 8GB GDDR6 DLSS3'),
(12, 'Porte HDMI/Display', '2 porte HDMI/2 porte Display'),
(12, 'Scheda audio', 'Integrata'),
(12, 'Raffreddamento della CPU', 'ventola CPU Tempest Cooler 4Pipes 120mm RGB nera'),
(12, 'Scheda di rete', 'Adattatore PCIe ASUS PCE-AX1800 AX1800 WiFi 6 + Bluetooth 5.2'),
(12, 'Collegamenti frontali', '2 porte USB 3.2 Gen1 tipo A, 1 audio HD/1 microfono'),
(12, 'Collegamenti posteriori', '2 porte USB 2.0/1.1, 1 porta per tastiera/mouse PS/2, 1 porta D-Sub, 1 porta HDMI, 2 porte di visualizzazione, 3 porte USB 3.2 di prima generazione, 1 porta USB Type-C®, compatibile con USB 3.2 Gen 2, 1 porta RJ-45, 3 connettori audio'),
(12, 'Dimensioni', '421 (P) x 210 (L) x 499 (A) mm');

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica)
VALUES
(13, 'Sorgente', '850W (o simile, a seconda della disponibilità)'),
(13, 'Processore', 'Intel Core i5 14600KF'),
(13, 'Scheda Madre', 'Z690/B650 (o simile, a seconda della disponibilità)'),
(13, 'RAM', '32 GB DDR4'),
(13, 'Raffreddamento A Liquido', '360mm'),
(13, 'Magazzinaggio', 'SSD M.2 da 2TB'),
(13, 'Scheda Grafica', 'RTX 4070 Super'),
(13, 'Wi-Fi', 'Sì'),
(13, 'Sistema Operativo', 'Windows 11 PRO installato e attivato');


INSERT INTO personalizzazione (id_prodotto, nome_personalizzazione, prezzo_personalizzazione)
VALUES
(13, 'Aggiungi GPU GeForce RTX 4080', 1500),
(13, 'Aggiungi memoria RAM 64GB', 250),
(13, 'Aggiungi un secondo SSD da 1TB', 120),
(13, 'Upgrade CPU Intel Core i7-12700K', 350),
(13, 'Aggiungi sistema di raffreddamento a liquido', 150),
(13, 'Personalizzazione estetica RGB con pannelli laterali in vetro temperato', 80);

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica)
VALUES
(14, 'Scatola', 'Cougar Semitower FV270 RGB Nero'),
(14, 'Alimentazione', 'PCIE 5.0 80 Plus Gold completamente modulare da 750 W'),
(14, 'Processore', 'AMD Ryzen 7 7800X3D (8 core, fino a 5,0 GHz)'),
(14, 'Scheda Madre', 'X670 GAMING WIFI ATX'),
(14, 'Disco rigido', 'SSD M.2 PCIe NVMe da 2 TB'),
(14, 'Memoria RAM', '32 GB di RAM DDR5 6000 MHz RGB'),
(14, 'Grafica', 'RTX 4070 Ti SUPER 16GB GDDR6X'),
(14, 'Raffreddamento', 'ARGB liquido da 360 mm + 3 ventole ARGB da 120 mm'),
(14, 'Connettività', 'WiFi/Ethernet integrati'),
(14, 'Sistema Operativo', 'Windows 11 Pro 64Bits installato e attivato con licenza');


INSERT INTO personalizzazione (id_prodotto, nome_personalizzazione, prezzo_personalizzazione)
VALUES
(14, 'Aggiungi GPU GeForce RTX 4090', 1200),
(14, 'Aggiungi 64GB di RAM DDR5 6000 MHz RGB', 400),
(14, 'Aggiungi un secondo SSD da 2TB', 200),
(14, 'Upgrade a processore AMD Ryzen 9 7900X', 800),
(14, 'Aggiungi sistema di raffreddamento a liquido ARGB 480mm', 250),
(14, 'Personalizzazione estetica con pannelli laterali in vetro temperato RGB', 100);


INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica)
VALUES
(15, 'Case', 'Nfortec Ursa ARGB Mid Tower E-ATX Vetro temperato USB-C Nero PC Tower'),
(15, 'Alimentazione', 'Alimentatore Forgeon Bolt PSU 850W 80+ Gold completamente modulare'),
(15, 'Processore', 'Intel Core Ultra 9 285K 3.2/5.7GHz'),
(15, 'Scheda Madre', 'MSI Z890 GAMING PLUS WIFI'),
(15, 'Disco rigido', 'Forgeon SSD 2 TB 7400 MB/S NVMe PCIe 4.0 M.2 Gen4'),
(15, 'RAM', 'Corsair Vengeance RGB DDR5 6000MHz 64GB 2x32GB CL30 Dual AMD EXPO e memoria Intel XMP'),
(15, 'Scheda Grafica', 'RTX 5080 16GB Gaming GDDR7 DLSS4'),
(15, 'Uscite Video', '1x HDMI / 3x DisplayPort'),
(15, 'Scheda Audio', 'Integrata'),
(15, 'Scheda di Rete', 'Integrata'),
(15, 'Raffreddamento CPU', 'Kit di raffreddamento a liquido Nfortec ATRIA X 360mm Nero');

INSERT INTO personalizzazione (id_prodotto, nome_personalizzazione, prezzo_personalizzazione)
VALUES
(15, 'Aggiungi GPU RTX 5090', 2500),
(15, 'Aggiungi 128GB di RAM DDR5', 450),
(15, 'Aggiungi un secondo SSD 2TB', 250),
(15, 'Upgrade al raffreddamento a liquido 480mm', 300),
(15, 'Aggiungi 5 anni di garanzia estesa', 200);

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica)
VALUES
(16, 'Processore', 'AMD Ryzen 7 7735HS (8C/OctaCore 3,2/4,75 GHz, 20 MB)'),
(16, 'Memoria RAM', '16 GB SO-DIMM DDR5 4800 MHz'),
(16, 'Archiviazione', 'SSD M.2 NVMe PCI Express 4.0 da 1 TB'),
(16, 'Display', '15,6 pollici, FHD (1920 x 1080) 16:9, livello Value IPS, display antiriflesso, sRGB:100%, Adobe:75,35%, frequenza di aggiornamento:144Hz, G-Sync, interruttore MUX + NVIDIA® Advanced Ottimo'),
(16, 'GPU', 'NVIDIA® GeForce RTX™ 4060, 2420 MHz* a 140 W (clock boost 2370 MHz+50 MHz OC, 115 W+25 W boost dinamico), 8 GB GDDR6'),
(16, 'Connettività', 'LAN Ethernet 10/100/1000Mbps, Wi-Fi 6 (802.11ax) 2x2 + Bluetooth 5.2'),
(16, 'Webcam', 'HD720p'),
(16, 'Audio', '2 altoparlanti / Microfono integrato / Dolby Atmos / Cancellazione del rumore AI / Certificazione Hi-Res'),
(16, 'Tastiera', 'Bubblegum retroilluminata RGB, spagnola'),
(16, 'Touchpad', 'Pannello multitouch'),
(16, 'Batteria', '90 Wh, 4 celle, ioni di litio'),
(16, 'Connessioni', '2 porte USB 3.2 Gen 1 (3.1 Gen 1) * 2, 1 USB 2.0, 1 audio/microfono, 1 RJ-45, 1 HDMI 2.1, 1 jack audio combinato per cuffie/microfono da 3,5 mm'),
(16, 'Sicurezza', 'Password amministratore BIOS e protezione password utente, Slot Kensington');

INSERT INTO personalizzazione (id_prodotto, nome_personalizzazione, prezzo_personalizzazione)
VALUES
(16, 'Aggiungi 32GB di RAM', 150),
(16, 'Aggiungi SSD da 512GB', 100);

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica)
VALUES
(17, 'Processore', 'Intel® Core™ i7-13620H (10 core, frequenza turbo massima 4,90 GHz, Intel® Smart Cache da 24 MB)'),
(17, 'Memoria', '32 GB DDR5-SDRAM, Velocità memoria: 5200 MHz'),
(17, 'Archiviazione', 'SSD PCI Express 4.0 da 1 TB, NVMe'),
(17, 'Grafica', 'NVIDIA GeForce RTX 4070, Memoria Grafica Dedicata: 8 GB GDDR6'),
(17, 'Audio', '2 altoparlanti incorporati, Potenza altoparlante: 2 W'),
(17, 'Macchina fotografica', 'Fotocamera frontale HD, Velocità acquisizione video: 30 fps'),
(17, 'Rete', 'Wi-Fi 6 (802.11ax), Bluetooth 5.2'),
(17, 'Connettività', 'USB 2.0: 1, USB 3.2 Gen 1 (3.1 Gen 1) Tipo A: 2, USB 3.2 Gen 1 (3.1 Gen 1) Tipo C: 1, Ethernet LAN (RJ-45): 1, HDMI: 1'),
(17, 'Tastiera', 'Tastiera numerica, retroilluminata RGB a 4 zone'),
(17, 'Batteria', '3 celle, Capacità della batteria: 53,5 Wh'),
(17, 'Gestione energetica', 'Adattatore dissipazione di potenza AC: 240 W'),
(17, 'Dimensioni e peso', 'Larghezza: 359 mm, Profondità: 259 mm, Altezza: 24,9 mm, Peso: 2,25 kg');

INSERT INTO personalizzazione (id_prodotto, nome_personalizzazione, prezzo_personalizzazione)
VALUES
(17, 'Aggiungi SSD 512GB', 80),
(17, 'Aggiungi 16GB di RAM', 90),
(17, 'Aggiungi Sistema operativo Windows 11 Pro', 140),
(17, 'Aggiungi garanzia estesa di 2 anni', 100);

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica)
VALUES
(18, 'Processore', 'Intel® Core™ i7-13700H di tredicesima generazione da 5,0 GHz (24 MB di cache, fino a 5,0 GHz, 6 P-core e 8 E-core)'),
(18, 'Memoria', 'RAM DDR5 8GB*2 (DDR5-4800, massimo 64 GB, 2 slot DDR5)'),
(18, 'Archiviazione', 'Gen4 1 TB, 2 slot SSD M.2 (tipo 2280, supporta 2 NVMe™ PCIe® Gen 4.0x4)'),
(18, 'Display', 'FHD da 17,3" con cornice sottile 1920x1080, 360 Hz, 100% sRGB, certificato TÜV Rheinland'),
(18, 'Grafica', 'NVIDIA® GeForce RTX™ 4080 GPU per laptop 12 GB GDDR6, Boost Clock 2010 MHz, Potenza grafica massima 150 W'),
(18, 'Connettività', 'Wi-Fi 6E (802.11ax) (Tripla banda) 2x2, Bluetooth® V5.2'),
(18, 'Fotocamera', 'Webcam HD (720p), Microfono array integrato, Supporto per l\'autenticazione facciale di Windows Hello'),
(18, 'Batteria', 'Batteria ai polimeri di litio 99Wh'),
(18, 'Connessioni', 'Lato sinistro: 1 RJ45, 1 HDMI 2.1, 1 Mini DP 1.4 (120 Hz), 1 USB 3.2 Gen1 (tipo A); Lato destro: 1 ingresso CC, 1 Thunderbolt™ 4 (tipo C), 1 USB 3.2 Gen1 (tipo A), 1 jack combinato audio'),
(18, 'Sistema operativo', 'Windows 11 Home a 64 bit'),
(18, 'Tastiera', 'Tastiera RGB Fusion stile isola con controllo retroilluminato per tasto, macro per tasto'),
(18, 'Dimensioni e peso', '39,8 cm (L) x 25,4 cm (P) x 3,0 cm (A), Peso: 2,7 kg'),
(18, 'Colore', 'Nero');

INSERT INTO personalizzazione (id_prodotto, nome_personalizzazione, prezzo_personalizzazione)
VALUES
(18, 'Aggiungi SSD 1TB', 120),
(18, 'Aggiungi 32GB di RAM', 150),
(18, 'Aggiungi garanzia estesa di 3 anni', 200);

INSERT INTO specifiche_prodotto (id_prodotto, nome_specifica, valore_specifica)
VALUES
(19, 'Processore', 'Intel® Core™ i9-13900H 13th Gen da 2,6 GHz (24M Cache, fino a 5,4 GHz, 14 core: 6 core P e 8 core E)'),
(19, 'Scheda Video', 'NVIDIA® GeForce RTX™ 4080 Laptop GPU (542 AI TOPs), ROG Boost: 1715MHz* at 145W (1665MHz Boost Clock+50MHz OC, 120W+25W Dynamic Boost, 125W+25W in Manual Mode), 12GB GDDR6'),
(19, 'Display', 'ROG Nebula HDR Display, 16 pollici, WQXGA (2560 x 1600) 16:10, Mini LED, Display antiriflesso, DCI-P3: 100%, Tempo di risposta: 3ms, Convalidato Pantone, MUX Switch + NVIDIA® Advanced Optimus'),
(19, 'Memoria', '32GB DDR5-4800 SO-DIMM, Capacità massima: 64GB, Supporta la memoria a doppio canale'),
(19, 'Archiviazione', '1TB (512GB + 512GB PCIe® 4.0 NVMe™ M.2 Performance SSD RAID 0)'),
(19, 'Tastiera', 'Tastiera Chiclet retroilluminata a 1 zona RGB'),
(19, 'Webcam', 'Telecamera IR 1080P FHD per Windows Hello'),
(19, 'Audio', 'Tecnologia Smart Amp, Dolby Atmos, Tecnologia di cancellazione del rumore AI, 2x 2W tweeter'),
(19, 'Batteria', '90WHrs, 4S1P, 4-cell Li-ion'),
(19, 'Peso', '2.10 Kg (4.63 lbs)'),
(19, 'Dimensioni', '35.5 x 24.6 x 1.99 ~ 2.23 cm (13.98" x 9.69" x 0.78" ~ 0.88")'),
(19, 'Inclusi nella confezione', 'Alimentatore aggiuntivo universale TYPE-C, 100W CA, Uscita: 20V DC, 5A, 100W, Ingresso: 100~240V CA 50/60Hz');

INSERT INTO personalizzazione (id_prodotto, nome_personalizzazione, prezzo_personalizzazione)
VALUES
(19, 'Aggiungi 64GB di RAM', 300),
(19, 'Aggiungi 2TB SSD', 400),
(19, 'Aggiungi garanzia estesa di 2 anni', 250);
