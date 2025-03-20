<?php
require_once 'database.php';

try {
    // Inserisci alcuni coupon di esempio
    $pdo->exec("INSERT INTO coupon (codice, percentuale_sconto) VALUES 
                ('Sconto10', 10),
                ('Sconto20', 20),
                ('Sconto30', 30)");
    
    // Inserisci alcuni prodotti di esempio
    $stmt = $pdo->prepare("INSERT INTO prodotti 
                          (nome, descrizione, prezzo, prezzo_base, categoria, marca, immagine) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // Prodotto 1: Scheda madre
    $stmt->execute([
        'ASUS ROG Strix Z690-E Gaming',
        'Scheda madre di fascia alta per processori Intel di 12a generazione con supporto DDR5 e PCIe 5.0',
        399.99,
        399.99,
        'Scheda Madre',
        'ASUS',
        '../immagini/catalogo/asus_rog_z690.jpg'
    ]);
    $moboId = $pdo->lastInsertId();
    
    // Aggiungi immagini per la scheda madre
    $pdo->exec("INSERT INTO immagini_prodotto (prodotto_id, url) VALUES 
                ($moboId, '../immagini/catalogo/asus_rog_z690.jpg'),
                ($moboId, '../immagini/catalogo/asus_rog_z690_2.jpg'),
                ($moboId, '../immagini/catalogo/asus_rog_z690_3.jpg')");
    
    // Aggiungi specifiche per la scheda madre
    $pdo->exec("INSERT INTO specifiche_prodotto (prodotto_id, chiave, valore, tipo) VALUES 
                ($moboId, 'Socket', 'LGA 1700', 'base'),
                ($moboId, 'Chipset', 'Intel Z690', 'base'),
                ($moboId, 'Formato', 'ATX', 'base'),
                ($moboId, 'RAM supportata', 'DDR5, fino a 128GB', 'dettagliata'),
                ($moboId, 'Slot PCIe', '1x PCIe 5.0 x16, 1x PCIe 4.0 x16, 1x PCIe 3.0 x16', 'dettagliata'),
                ($moboId, 'Porte USB', '2x USB 3.2 Gen 2x2 Type-C, 6x USB 3.2 Gen 2, 8x USB 3.2 Gen 1', 'dettagliata'),
                ($moboId, 'LAN', '2.5 Gigabit LAN', 'dettagliata'),
                ($moboId, 'WiFi', 'WiFi 6E', 'dettagliata'),
                ($moboId, 'Bluetooth', 'Bluetooth 5.2', 'dettagliata')");
    
    // Prodotto 2: CPU
    $stmt->execute([
        'Intel Core i9-12900K',
        'Processore di punta Intel di 12a generazione con 16 core e 24 thread',
        589.99,
        589.99,
        'CPU',
        'Intel',
        '../immagini/catalogo/intel_i9_12900k.jpg'
    ]);
    $cpuId = $pdo->lastInsertId();
    
    // Aggiungi immagini per la CPU
    $pdo->exec("INSERT INTO immagini_prodotto (prodotto_id, url) VALUES 
                ($cpuId, '../immagini/catalogo/intel_i9_12900k.jpg'),
                ($cpuId, '../immagini/catalogo/intel_i9_12900k_2.jpg')");
    
    // Aggiungi specifiche per la CPU
    $pdo->exec("INSERT INTO specifiche_prodotto (prodotto_id, chiave, valore, tipo) VALUES 
                ($cpuId, 'Core/Thread', '16/24', 'base'),
                ($cpuId, 'Frequenza base', '3.2 GHz', 'base'),
                ($cpuId, 'Frequenza turbo', '5.2 GHz', 'base'),
                ($cpuId, 'Socket', 'LGA 1700', 'dettagliata'),
                ($cpuId, 'TDP', '125W', 'dettagliata'),
                ($cpuId, 'Cache', '30MB Intel Smart Cache', 'dettagliata'),
                ($cpuId, 'Grafica integrata', 'Intel UHD Graphics 770', 'dettagliata'),
                ($cpuId, 'Supporto memoria', 'DDR5-4800, DDR4-3200', 'dettagliata')");
    
    // Prodotto 3: RAM
    $stmt->execute([
        'Corsair Vengeance RGB Pro',
        'Memoria RAM DDR4 ad alte prestazioni con illuminazione RGB personalizzabile',
        199.99,
        199.99,
        'RAM',
        'Corsair',
        '../immagini/catalogo/corsair_vengeance_rgb.jpg'
    ]);
    $ramId = $pdo->lastInsertId();
    
    // Aggiungi immagini per la RAM
    $pdo->exec("INSERT INTO immagini_prodotto (prodotto_id, url) VALUES 
                ($ramId, '../immagini/catalogo/corsair_vengeance_rgb.jpg'),
                ($ramId, '../immagini/catalogo/corsair_vengeance_rgb_2.jpg')");
    
    // Aggiungi specifiche per la RAM
    $pdo->exec("INSERT INTO specifiche_prodotto (prodotto_id, chiave, valore, tipo) VALUES 
                ($ramId, 'Tipo', 'DDR4', 'base'),
                ($ramId, 'Velocità', '3600 MHz', 'base'),
                ($ramId, 'Latenza', 'CL18', 'base'),
                ($ramId, 'Tensione', '1.35V', 'dettagliata'),
                ($ramId, 'Illuminazione', 'RGB', 'dettagliata'),
                ($ramId, 'Compatibilità', 'Intel XMP 2.0', 'dettagliata')");
    
    // Aggiungi varianti per la RAM (colori e taglie)
    $pdo->exec("INSERT INTO varianti_prodotto (prodotto_id, tipo_variante, valore, prezzo_aggiuntivo) VALUES 
                ($ramId, 'colore', 'Nero', 0),
                ($ramId, 'colore', 'Bianco', 0),
                ($ramId, 'taglia', '16GB (2x8GB)', 0),
                ($ramId, 'taglia', '32GB (2x16GB)', 100),
                ($ramId, 'taglia', '64GB (2x32GB)', 300)");
    
    // Prodotto 4: SSD
    $stmt->execute([
        'Samsung 980 PRO',
        'SSD NVMe PCIe 4.0 ad altissime prestazioni per gaming e applicazioni professionali',
        229.99,
        229.99,
        'Storage',
        'Samsung',
        '../immagini/catalogo/samsung_980pro.jpg'
    ]);
    $ssdId = $pdo->lastInsertId();
    
    // Aggiungi immagini per l'SSD
    $pdo->exec("INSERT INTO immagini_prodotto (prodotto_id, url) VALUES 
                ($ssdId, '../immagini/catalogo/samsung_980pro.jpg'),
                ($ssdId, '../immagini/catalogo/samsung_980pro_2.jpg')");
    
    // Aggiungi specifiche per l'SSD
    $pdo->exec("INSERT INTO specifiche_prodotto (prodotto_id, chiave, valore, tipo) VALUES 
                ($ssdId, 'Interfaccia', 'PCIe 4.0 x4', 'base'),
                ($ssdId, 'Lettura sequenziale', 'Fino a 7000 MB/s', 'base'),
                ($ssdId, 'Scrittura sequenziale', 'Fino a 5000 MB/s', 'base'),
                ($ssdId, 'Controller', 'Samsung Elpis', 'dettagliata'),
                ($ssdId, 'NAND', 'V-NAND 3-bit MLC', 'dettagliata'),
                ($ssdId, 'DRAM', 'LPDDR4', 'dettagliata'),
                ($ssdId, 'Fattore di forma', 'M.2 2280', 'dettagliata'),
                ($ssdId, 'TBW', '600 TB', 'dettagliata')");
    
    // Aggiungi varianti per l'SSD (capacità)
    $pdo->exec("INSERT INTO varianti_prodotto (prodotto_id, tipo_variante, valore, prezzo_aggiuntivo) VALUES 
                ($ssdId, 'capacita', '500GB', -80),
                ($ssdId, 'capacita', '1TB', 0),
                ($ssdId, 'capacita', '2TB', 200)");
    
    // Prodotto 5: Alimentatore
    $stmt->execute([
        'Corsair RM850x',
        'Alimentatore completamente modulare da 850W con certificazione 80 PLUS Gold',
        149.99,
        149.99,
        'PSU',
        'Corsair',
        '../immagini/catalogo/corsair_rm850x.jpg'
    ]);
    $psuId = $pdo->lastInsertId();
    
    // Aggiungi immagini per l'alimentatore
    $pdo->exec("INSERT INTO immagini_prodotto (prodotto_id, url) VALUES 
                ($psuId, '../immagini/catalogo/corsair_rm850x.jpg'),
                ($psuId, '../immagini/catalogo/corsair_rm850x_2.jpg')");
    
    // Aggiungi specifiche per l'alimentatore
    $pdo->exec("INSERT INTO specifiche_prodotto (prodotto_id, chiave, valore, tipo) VALUES 
                ($psuId, 'Potenza', '850W', 'base'),
                ($psuId, 'Certificazione', '80 PLUS Gold', 'base'),
                ($psuId, 'Modularità', 'Completamente modulare', 'base'),
                ($psuId, 'Ventola', '135mm con modalità Zero RPM', 'dettagliata'),
                ($psuId, 'Connettori PCIe', '4x 8-pin (6+2)', 'dettagliata'),
                ($psuId, 'Connettori SATA', '12x', 'dettagliata'),
                ($psuId, 'Connettori CPU', '2x 8-pin (4+4)', 'dettagliata'),
                ($psuId, 'Efficienza', '90% sotto carico tipico', 'dettagliata')");
    
    // Aggiungi varianti per l'alimentatore (potenza)
    $pdo->exec("INSERT INTO varianti_prodotto (prodotto_id, tipo_variante, valore, prezzo_aggiuntivo) VALUES 
                ($psuId, 'potenza', '750W', -20),
                ($psuId, 'potenza', '850W', 0),
                ($psuId, 'potenza', '1000W', 40)");
    
    echo "Dati di esempio inseriti con successo!";
} catch (PDOException $e) {
    echo "Errore durante l'inserimento dei dati di esempio: " . $e->getMessage();
}
?>