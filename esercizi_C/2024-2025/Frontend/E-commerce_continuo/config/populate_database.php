<?php
require_once 'database.php';

try {
    // Inserisci categorie di esempio
    $categorie = ['CPU', 'GPU', 'RAM', 'Storage', 'PSU', 'Motherboard'];
    $marche = ['ASUS', 'MSI', 'Corsair', 'Samsung', 'Thermaltake', 'Gigabyte', 'SK hynix'];
    
    // Inserisci prodotti di esempio
    $prodotti = [
        [
            'nome' => 'ASUS ROG Strix Z690-E Gaming WiFi',
            'descrizione' => 'Scheda madre di fascia alta con supporto per processori Intel di 12a generazione.',
            'prezzo_base' => 399.99,
            'categoria' => 'Motherboard',
            'marca' => 'ASUS',
            'immagine_principale' => '../immagini/prodotti/asus_z690.jpg',
            'immagini' => json_encode([
                '../immagini/prodotti/asus_z690.jpg',
                '../immagini/prodotti/asus_z690_2.jpg',
                '../immagini/prodotti/asus_z690_3.jpg'
            ]),
            'specifiche' => json_encode([
                'Socket' => 'LGA 1700',
                'Chipset' => 'Intel Z690',
                'Formato' => 'ATX'
            ]),
            'specifiche_dettagliate' => json_encode([
                'Memoria supportata' => 'DDR5, fino a 6400MHz (OC)',
                'Slot PCIe' => '1x PCIe 5.0 x16, 1x PCIe 4.0 x16, 1x PCIe 3.0 x16',
                'Porte USB' => '2x USB 3.2 Gen 2x2 Type-C, 8x USB 3.2 Gen 2, 4x USB 3.2 Gen 1',
                'Rete' => 'Intel 2.5Gb Ethernet, WiFi 6E',
                'Audio' => 'SupremeFX 7.1 Surround Sound'
            ]),
            'varianti' => json_encode([])
        ],
        [
            'nome' => 'Gigabyte Z590 AORUS MASTER',
            'descrizione' => 'Scheda madre premium per processori Intel di 11a generazione con funzionalità avanzate.',
            'prezzo_base' => 349.99,
            'categoria' => 'Motherboard',
            'marca' => 'Gigabyte',
            'immagine_principale' => '../immagini/prodotti/gigabyte_z590.jpg',
            'immagini' => json_encode([
                '../immagini/prodotti/gigabyte_z590.jpg',
                '../immagini/prodotti/gigabyte_z590_2.jpg',
                '../immagini/prodotti/gigabyte_z590_3.jpg'
            ]),
            'specifiche' => json_encode([
                'Socket' => 'LGA 1200',
                'Chipset' => 'Intel Z590',
                'Formato' => 'ATX'
            ]),
            'specifiche_dettagliate' => json_encode([
                'Memoria supportata' => 'DDR4, fino a 5400MHz (OC)',
                'Slot PCIe' => '1x PCIe 4.0 x16, 1x PCIe 3.0 x16, 2x PCIe 3.0 x4',
                'Porte USB' => '1x USB 3.2 Gen 2x2 Type-C, 6x USB 3.2 Gen 2, 6x USB 3.2 Gen 1',
                'Rete' => 'Intel 2.5Gb Ethernet, WiFi 6',
                'Audio' => 'Realtek ALC1220-VB codec'
            ]),
            'varianti' => json_encode([])
        ],
        [
            'nome' => 'MSI GeForce RTX 4080 SUPRIM X',
            'descrizione' => 'Scheda grafica di fascia alta con architettura NVIDIA Ada Lovelace.',
            'prezzo_base' => 1299.99,
            'categoria' => 'GPU',
            'marca' => 'MSI',
            'immagine_principale' => '../immagini/prodotti/msi_rtx4080.jpg',
            'immagini' => json_encode([
                '../immagini/prodotti/msi_rtx4080.jpg',
                '../immagini/prodotti/msi_rtx4080_2.jpg',
                '../immagini/prodotti/msi_rtx4080_3.jpg'
            ]),
            'specifiche' => json_encode([
                'GPU' => 'NVIDIA GeForce RTX 4080',
                'VRAM' => '16GB GDDR6X',
                'Interfaccia' => 'PCIe 4.0'
            ]),
            'specifiche_dettagliate' => json_encode([
                'Core Clock' => '2205 MHz (Boost)',
                'Memoria Clock' => '22.4 Gbps',
                'Interfaccia Memoria' => '256-bit',
                'Porte' => '3x DisplayPort 1.4a, 1x HDMI 2.1',
                'Alimentazione' => '3x 8-pin PCIe',
                'Dimensioni' => '337 x 140 x 67 mm'
            ]),
            'varianti' => json_encode([])
        ],
        [
            'nome' => 'Corsair RM850x 80+ Gold',
            'descrizione' => 'Alimentatore modulare di alta qualità con certificazione 80+ Gold.',
            'prezzo_base' => 129.99,
            'categoria' => 'PSU',
            'marca' => 'Corsair',
            'immagine_principale' => '../immagini/prodotti/corsair_rm850x.jpg',
            'immagini' => json_encode([
                '../immagini/prodotti/corsair_rm850x.jpg',
                '../immagini/prodotti/corsair_rm850x_2.jpg',
                '../immagini/prodotti/corsair_rm850x_3.jpg'
            ]),
            'specifiche' => json_encode([
                'Potenza' => '850W',
                'Certificazione' => '80+ Gold',
                'Tipo' => 'Modulare completo'
            ]),
            'specifiche_dettagliate' => json_encode([
                'Efficienza' => 'Fino al 90% sotto carico tipico',
                'Ventola' => '135mm con modalità Zero RPM',
                'Protezioni' => 'OVP, UVP, OCP, OPP, SCP, OTP',
                'Connettori' => '1x ATX 24-pin, 2x EPS 8-pin, 6x PCIe 8-pin, 14x SATA, 4x Molex',
                'Dimensioni' => '150 x 86 x 160 mm'
            ]),
            'varianti' => json_encode([
                'potenza' => [
                    '750W' => ['prezzo_aggiuntivo' => -20],
                    '850W' => ['prezzo_aggiuntivo' => 0],
                    '1000W' => ['prezzo_aggiuntivo' => 40]
                ]
            ])
        ],
        [
            'nome' => 'Thermaltake Toughpower GF1 650W 80+ Gold',
            'descrizione' => 'Alimentatore modulare affidabile con certificazione 80+ Gold.',
            'prezzo_base' => 99.99,
            'categoria' => 'PSU',
            'marca' => 'Thermaltake',
            'immagine_principale' => '../immagini/prodotti/thermaltake_gf1.jpg',
            'immagini' => json_encode([
                '../immagini/prodotti/thermaltake_gf1.jpg',
                '../immagini/prodotti/thermaltake_gf1_2.jpg',
                '../immagini/prodotti/thermaltake_gf1_3.jpg'
            ]),
            'specifiche' => json_encode([
                'Potenza' => '650W',
                'Certificazione' => '80+ Gold',
                'Tipo' => 'Modulare completo'
            ]),
            'specifiche_dettagliate' => json_encode([
                'Efficienza' => 'Fino al 90% sotto carico tipico',
                'Ventola' => '140mm con controllo intelligente',
                'Protezioni' => 'OVP, UVP, OCP, OPP, SCP',
                'Connettori' => '1x ATX 24-pin, 1x EPS 8-pin, 4x PCIe 8-pin, 9x SATA, 4x Molex',
                'Dimensioni' => '150 x 86 x 160 mm'
            ]),
            'varianti' => json_encode([])
        ],
        [
            'nome' => 'Corsair Vengeance DDR5 32GB (2x16GB) 5200MHz',
            'descrizione' => 'Kit di memoria RAM DDR5 ad alte prestazioni per sistemi di nuova generazione.',
            'prezzo_base' => 189.99,
            'categoria' => 'RAM',
            'marca' => 'Corsair',
            'immagine_principale' => '../immagini/prodotti/corsair_vengeance_ddr5.jpg',
            'immagini' => json_encode([
                '../immagini/prodotti/corsair_vengeance_ddr5.jpg',
                '../immagini/prodotti/corsair_vengeance_ddr5_2.jpg',
                '../immagini/prodotti/corsair_vengeance_ddr5_3.jpg'
            ]),
            'specifiche' => json_encode([
                'Capacità' => '32GB (2x16GB)',
                'Tipo' => 'DDR5',
                'Velocità' => '5200MHz'
            ]),
            'specifiche_dettagliate' => json_encode([
                'Latenza' => 'CL40',
                'Voltaggio' => '1.25V',
                'Profilo XMP' => 'Intel XMP 3.0',
                'Dissipatore' => 'Alluminio',
                'Compatibilità' => 'Intel 12th Gen e successivi, AMD AM5'
            ]),
            'varianti' => json_encode([
                'colore' => [
                    'Nero' => ['prezzo_aggiuntivo' => 0],
                    'Bianco' => ['prezzo_aggiuntivo' => 5]
                ],
                'taglia' => [
                    '16GB (2x8GB)' => ['prezzo' => 99.99],
                    '32GB (2x16GB)' => ['prezzo' => 189.99],
                    '64GB (2x32GB)' => ['prezzo' => 349.99]
                ]
            ])
        ],
        [
            'nome' => 'Samsung 970 EVO Plus 1TB NVMe M.2',
            'descrizione' => 'SSD NVMe ad alte prestazioni con tecnologia V-NAND.',
            'prezzo_base' => 119.99,
            'categoria' => 'Storage',
            'marca' => 'Samsung',
            'immagine_principale' => '../immagini/prodotti/samsung_970evo.jpg',
            'immagini' => json_encode([
                '../immagini/prodotti/samsung_970evo.jpg',
                '../immagini/prodotti/samsung_970evo_2.jpg',
                '../immagini/prodotti/samsung_970evo_3.jpg'
            ]),
            'specifiche' => json_encode([
                'Capacità' => '1TB',
                'Interfaccia' => 'PCIe 3.0 x4, NVMe 1.3',
                'Fattore di forma' => 'M.2 2280'
            ]),
            'specifiche_dettagliate' => json_encode([
                'Lettura sequenziale' => 'Fino a 3,500 MB/s',
                'Scrittura sequenziale' => 'Fino a 3,300 MB/s',
                'IOPS lettura casuale' => 'Fino a 600,000',
                'IOPS scrittura casuale' => 'Fino a 550,000',
                'Durata' => '600 TBW',
                'MTBF' => '1.5 milioni di ore'
            ]),
            'varianti' => json_encode([
                'capacita' => [
                    '250GB' => ['prezzo_aggiuntivo' => -70],
                    '500GB' => ['prezzo_aggiuntivo' => -40],
                    '1TB' => ['prezzo_aggiuntivo' => 0],
                    '2TB' => ['prezzo_aggiuntivo' => 100]
                ]
            ])
        ],
        [
            'nome' => 'SK hynix Platinum P41 2TB NVMe SSD',
            'descrizione' => 'SSD NVMe PCIe 4.0 ad altissime prestazioni con controller proprietario.',
            'prezzo_base' => 229.99,
            'categoria' => 'Storage',
            'marca' => 'SK hynix',
            'immagine_principale' => '../immagini/prodotti/skhynix_p41.jpg',
            'immagini' => json_encode([
                '../immagini/prodotti/skhynix_p41.jpg',
                '../immagini/prodotti/skhynix_p41_2.jpg',
                '../immagini/prodotti/skhynix_p41_3.jpg'
            ]),
            'specifiche' => json_encode([
                'Capacità' => '2TB',
                'Interfaccia' => 'PCIe 4.0 x4, NVMe 1.4',
                'Fattore di forma' => 'M.2 2280'
            ]),
            'specifiche_dettagliate' => json_encode([
                'Lettura sequenziale' => 'Fino a 7,000 MB/s',
                'Scrittura sequenziale' => 'Fino a 6,500 MB/s',
                'IOPS lettura casuale' => 'Fino a 1,400,000',
                'IOPS scrittura casuale' => 'Fino a 1,300,000',
                'Durata' => '1200 TBW',
                'MTBF' => '1.6 milioni di ore'
            ]),
            'varianti' => json_encode([
                'capacita' => [
                    '500GB' => ['prezzo_aggiuntivo' => -130],
                    '1TB' => ['prezzo_aggiuntivo' => -80],
                    '2TB' => ['prezzo_aggiuntivo' => 0],
                    '4TB' => ['prezzo_aggiuntivo' => 200]
                ]
            ])
        ]
    ];
    
    // Inserisci i prodotti nel database
    $stmt = $pdo->prepare("
        INSERT INTO prodotti (
            nome, descrizione, prezzo_base, categoria, marca, 
            immagine_principale, immagini, specifiche, specifiche_dettagliate, varianti
        ) VALUES (
            :nome, :descrizione, :prezzo_base, :categoria, :marca, 
            :immagine_principale, :immagini, :specifiche, :specifiche_dettagliate, :varianti
        )
    ");
    
    foreach ($prodotti as $prodotto) {
        $stmt->execute($prodotto);
        echo "Prodotto '{$prodotto['nome']}' inserito con successo<br>";
    }
    
    // Inserisci coupon di esempio
    $coupon = [
        'codice' => 'Sconto10',
        'percentuale_sconto' => 10,
        'data_inizio' => date('Y-m-d'),
        'data_fine' => date('Y-m-d', strtotime('+30 days')),
        'attivo' => true
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO coupon (codice, percentuale_sconto, data_inizio, data_fine, attivo)
        VALUES (:codice, :percentuale_sconto, :data_inizio, :data_fine, :attivo)
    ");
    $stmt->execute($coupon);
    echo "Coupon '{$coupon['codice']}' inserito con successo<br>";
    
    $coupon = [
        'codice' => 'Sconto20',
        'percentuale_sconto' => 20,
        'data_inizio' => date('Y-m-d'),
        'data_fine' => date('Y-m-d', strtotime('+15 days')),
        'attivo' => true
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO coupon (codice, percentuale_sconto, data_inizio, data_fine, attivo)
        VALUES (:codice, :percentuale_sconto, :data_inizio, :data_fine, :attivo)
    ");
    $stmt->execute($coupon);
    echo "Coupon '{$coupon['codice']}' inserito con successo<br>";
    
    $coupon = [
        'codice' => 'Sconto30',
        'percentuale_sconto' => 30,
        'data_inizio' => date('Y-m-d'),
        'data_fine' => date('Y-m-d', strtotime('+7 days')),
        'attivo' => true
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO coupon (codice, percentuale_sconto, data_inizio, data_fine, attivo)
        VALUES (:codice, :percentuale_sconto, :data_inizio, :data_fine, :attivo)
    ");
    $stmt->execute($coupon);
    echo "Coupon '{$coupon['codice']}' inserito con successo<br>";
    
    // Inserisci bundle di esempio
    // Prima ottieni gli ID dei prodotti
    $stmt = $pdo->query("SELECT id, nome FROM prodotti");
    $prodotti_db = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $bundle = [
        'nome' => 'Bundle Gaming Ultimate',
        'descrizione' => 'Bundle completo per gaming di fascia alta con scheda grafica RTX 4080.',
        'prezzo_originale' => 1999.99,
        'prezzo_scontato' => 1799.99,
        'immagine' => '../immagini/bundle/gaming_bundle.jpg',
        'prodotti' => json_encode(array_slice(array_keys($prodotti_db), 0, 3)), // Primi 3 prodotti
        'data_scadenza' => date('Y-m-d H:i:s', strtotime('+10 days'))
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO bundle (
            nome, descrizione, prezzo_originale, prezzo_scontato, immagine, prodotti, data_scadenza
        ) VALUES (
            :nome, :descrizione, :prezzo_originale, :prezzo_scontato, :immagine, :prodotti, :data_scadenza
        )
    ");
    $stmt->execute($bundle);
    echo "Bundle '{$bundle['nome']}' inserito con successo<br>";
    
    $bundle = [
        'nome' => 'Bundle Storage Pro',
        'descrizione' => 'Bundle con SSD ad alte prestazioni per storage veloce e affidabile.',
        'prezzo_originale' => 399.99,
        'prezzo_scontato' => 349.99,
        'immagine' => '../immagini/bundle/storage_bundle.jpg',
        'prodotti' => json_encode(array_slice(array_keys($prodotti_db), 6, 2)), // Ultimi 2 prodotti (storage)
        'data_scadenza' => date('Y-m-d H:i:s', strtotime('+15 days'))
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO bundle (
            nome, descrizione, prezzo_originale, prezzo_scontato, immagine, prodotti, data_scadenza
        ) VALUES (
            :nome, :descrizione, :prezzo_originale, :prezzo_scontato, :immagine, :prodotti, :data_scadenza
        )
    ");
    $stmt->execute($bundle);
    echo "Bundle '{$bundle['nome']}' inserito con successo<br>";
    
    echo "Database popolato con successo!";
    
} catch(PDOException $e) {
    echo "Errore: " . $e->getMessage();
}
?>