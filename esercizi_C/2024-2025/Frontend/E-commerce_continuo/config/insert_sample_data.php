<?php
// Script per inserire dati di esempio nel database
require_once 'database.php';

try {
    // Inserisci categorie
    $categorie = ['Motherboard', 'GPU', 'PSU', 'RAM', 'Storage', 'CPU', 'Case', 'Cooling'];
    $categorieIds = [];
    
    foreach ($categorie as $categoria) {
        $stmt = $pdo->prepare("INSERT INTO categorie (nome) VALUES (:nome)");
        $stmt->execute(['nome' => $categoria]);
        $categorieIds[$categoria] = $pdo->lastInsertId();
    }
    
    // Inserisci marche
    $marche = ['ASUS', 'MSI', 'Corsair', 'Gigabyte', 'Samsung', 'SK hynix', 'Thermaltake', 'NVIDIA', 'AMD'];
    $marcheIds = [];
    
    foreach ($marche as $marca) {
        $stmt = $pdo->prepare("INSERT INTO marche (nome) VALUES (:nome)");
        $stmt->execute(['nome' => $marca]);
        $marcheIds[$marca] = $pdo->lastInsertId();
    }
    
    // Inserisci prodotti
    $prodotti = [
        [
            'nome' => 'ASUS ROG Strix Z690-E Gaming WiFi',
            'descrizione' => 'Scheda madre di fascia alta con supporto per processori Intel di 12a generazione.',
            'prezzo_base' => 399.99,
            'categoria' => 'Motherboard',
            'marca' => 'ASUS',
            'immagine_principale' => '../immagini/prodotti/asus_z690.jpg',
            'specifiche' => [
                'Socket' => 'LGA 1700',
                'Chipset' => 'Intel Z690',
                'Formato' => 'ATX'
            ],
            'specifiche_dettagliate' => [
                'Memoria supportata' => 'DDR5, fino a 6400MHz OC',
                'Slot PCIe' => '2x PCIe 5.0 x16, 1x PCIe 3.0 x16',
                'Porte SATA' => '6x SATA 6Gb/s',
                'M.2' => '4x M.2 (PCIe 4.0 x4)',
                'USB' => '2x USB 3.2 Gen 2x2, 4x USB 3.2 Gen 2, 6x USB 3.2 Gen 1',
                'LAN' => 'Intel 2.5Gb Ethernet',
                'Wireless' => 'WiFi 6E (802.11ax) + Bluetooth 5.2'
            ],
            'immagini' => [
                '../immagini/prodotti/asus_z690.jpg',
                '../immagini/prodotti/asus_z690_2.jpg',
                '../immagini/prodotti/asus_z690_3.jpg'
            ]
        ],
        [
            'nome' => 'MSI GeForce RTX 4080 SUPRIM X',
            'descrizione' => 'Scheda grafica di fascia alta con architettura NVIDIA Ada Lovelace.',
            'prezzo_base' => 1299.99,
            'categoria' => 'GPU',
            'marca' => 'MSI',
            'immagine_principale' => '../immagini/prodotti/msi_rtx4080.jpg',
            'specifiche' => [
                'GPU' => 'NVIDIA GeForce RTX 4080',
                'VRAM' => '16GB GDDR6X',
                'Bus' => 'PCIe 4.0'
            ],
            'specifiche_dettagliate' => [
                'Core Clock' => '2205 MHz (Boost)',
                'CUDA Cores' => '9728',
                'Memory Speed' => '22.4 Gbps',
                'Memory Interface' => '256-bit',
                'Output' => '3x DisplayPort 1.4a, 1x HDMI 2.1',
                'Alimentazione' => '3x 8-pin',
                'Dimensioni' => '337 x 140 x 67 mm'
            ],
            'immagini' => [
                '../immagini/prodotti/msi_rtx4080.jpg',
                '../immagini/prodotti/msi_rtx4080_2.jpg',
                '../immagini/prodotti/msi_rtx4080_3.jpg'
            ]
        ],
        [
            'nome' => 'Corsair RM850x 850W 80+ Gold',
            'descrizione' => 'Alimentatore modulare di alta qualità con certificazione 80+ Gold.',
            'prezzo_base' => 129.99,
            'categoria' => 'PSU',
            'marca' => 'Corsair',
            'immagine_principale' => '../immagini/prodotti/corsair_rm850x.jpg',
            'specifiche' => [
                'Potenza' => '850W',
                'Certificazione' => '80+ Gold',
                'Tipo' => 'Modulare completo'
            ],
            'specifiche_dettagliate' => [
                'Efficienza' => '90% sotto carico tipico',
                'Ventola' => '135mm con controllo termico',
                'Protezioni' => 'OVP, UVP, OCP, OPP, SCP',
                'Connettori' => '1x ATX 24-pin, 2x EPS 8-pin, 6x PCIe 8-pin, 10x SATA, 4x Molex',
                'Dimensioni' => '150 x 86 x 160 mm',
                'Garanzia' => '10 anni'
            ],
            'immagini' => [
                '../immagini/prodotti/corsair_rm850x.jpg',
                '../immagini/prodotti/corsair_rm850x_2.jpg'
            ],
            'varianti' => [
                ['tipo' => 'potenza', 'valore' => '750W', 'prezzo' => 0],
                ['tipo' => 'potenza', 'valore' => '850W', 'prezzo' => 20],
                ['tipo' => 'potenza', 'valore' => '1000W', 'prezzo' => 40]
            ]
        ],
        [
            'nome' => 'Corsair Vengeance DDR5 32GB (2x16GB) 5200MHz',
            'descrizione' => 'Kit di memoria RAM DDR5 ad alte prestazioni.',
            'prezzo_base' => 189.99,
            'categoria' => 'RAM',
            'marca' => 'Corsair',
            'immagine_principale' => '../immagini/prodotti/corsair_vengeance_ddr5.jpg',
            'specifiche' => [
                'Capacità' => '32GB (2x16GB)',
                'Tipo' => 'DDR5',
                'Velocità' => '5200MHz'
            ],
            'specifiche_dettagliate' => [
                'Latenza' => 'CL40',
                'Tensione' => '1.25V',
                'Profilo XMP' => 'Intel XMP 3.0',
                'Dissipatore' => 'Alluminio',
                'Compatibilità' => 'Intel 12th/13th Gen, AMD Ryzen 7000'
            ],
            'immagini' => [
                '../immagini/prodotti/corsair_vengeance_ddr5.jpg',
                '../immagini/prodotti/corsair_vengeance_ddr5_2.jpg'
            ],
            'varianti' => [
                ['tipo' => 'colore', 'valore' => 'Nero', 'prezzo' => 0],
                ['tipo' => 'colore', 'valore' => 'Bianco', 'prezzo' => 0],
                ['tipo' => 'taglia', 'valore' => '16GB (2x8GB)', 'prezzo' => -60],
                ['tipo' => 'taglia', 'valore' => '32GB (2x16GB)', 'prezzo' => 0],
                ['tipo' => 'taglia', 'valore' => '64GB (2x32GB)', 'prezzo' => 120]
            ]
        ],
        [
            'nome' => 'Samsung 970 EVO Plus 1TB NVMe M.2',
            'descrizione' => 'SSD NVMe ad alte prestazioni con interfaccia PCIe 3.0.',
            'prezzo_base' => 119.99,
            'categoria' => 'Storage',
            'marca' => 'Samsung',
            'immagine_principale' => '../immagini/prodotti/samsung_970evo.jpg',
            'specifiche' => [
                'Capacità' => '1TB',
                'Interfaccia' => 'PCIe 3.0 x4, NVMe 1.3',
                'Formato' => 'M.2 2280'
            ],
            'specifiche_dettagliate' => [
                'Lettura sequenziale' => 'fino a 3,500 MB/s',
                'Scrittura sequenziale' => 'fino a 3,300 MB/s',
                'IOPS lettura casuale' => 'fino a 600,000',
                'IOPS scrittura casuale' => 'fino a 550,000',
                'NAND' => 'Samsung V-NAND 3-bit MLC',
                'Controller' => 'Samsung Phoenix',
                'DRAM Cache' => '1GB LPDDR4',
                'TBW' => '600 TB',
                'Garanzia' => '5 anni'
            ],
            'immagini' => [
                '../immagini/prodotti/samsung_970evo.jpg',
                '../immagini/prodotti/samsung_970evo_2.jpg'
            ],
            'varianti' => [
                ['tipo' => 'capacita', 'valore' => '250GB', 'prezzo' => -60],
                ['tipo' => 'capacita', 'valore' => '500GB', 'prezzo' => -30],
                ['tipo' => 'capacita', 'valore' => '1TB', 'prezzo' => 0],
                ['tipo' => 'capacita', 'valore' => '2TB', 'prezzo' => 100]
            ]
        ]
    ];
    
    // Inserisci i prodotti nel database
    foreach ($prodotti as $prodotto) {
        // Inserisci il prodotto base
        $stmt = $pdo->prepare("
            INSERT INTO prodotti (nome, descrizione, prezzo_base, categoria_id, marca_id, immagine_principale)
            VALUES (:nome, :descrizione, :prezzo_base, :categoria_id, :marca_id, :immagine_principale)
        ");
        $stmt->execute([
            'nome' => $prodotto['nome'],
            'descrizione' => $prodotto['descrizione'],
            'prezzo_base' => $prodotto['prezzo_base'],
            'categoria_id' => $categorieIds[$prodotto['categoria']],
            'marca_id' => $marcheIds[$prodotto['marca']],
            'immagine_principale' => $prodotto['immagine_principale']
        ]);
        
        $prodottoId = $pdo->lastInsertId();
        
        // Inserisci le immagini
        if (isset($prodotto['immagini'])) {
            foreach ($prodotto['immagini'] as $index => $immagine) {
                $stmt = $pdo->prepare("
                    INSERT INTO immagini_prodotto (prodotto_id, url_immagine, ordine)
                    VALUES (:prodotto_id, :url_immagine, :ordine)
                ");
                $stmt->execute([
                    'prodotto_id' => $prodottoId,
                    'url_immagine' => $immagine,
                    'ordine' => $index
                ]);
            }
        }
        
        // Inserisci le specifiche base
        if (isset($prodotto['specifiche'])) {
            foreach ($prodotto['specifiche'] as $chiave => $valore) {
                $stmt = $pdo->prepare("
                    INSERT INTO specifiche_prodotto (prodotto_id, chiave, valore, tipo)
                    VALUES (:prodotto_id, :chiave, :valore, 'base')
                ");
                $stmt->execute([
                    'prodotto_id' => $prodottoId,
                    'chiave' => $chiave,
                    'valore' => $valore
                ]);
            }
        }
        
        // Inserisci le specifiche dettagliate
        if (isset($prodotto['specifiche_dettagliate'])) {
            foreach ($prodotto['specifiche_dettagliate'] as $chiave => $valore) {
                $stmt = $pdo->prepare("
                    INSERT INTO specifiche_prodotto (prodotto_id, chiave, valore, tipo)
                    VALUES (:prodotto_id, :chiave, :valore, 'dettagliata')
                ");
                $stmt->execute([
                    'prodotto_id' => $prodottoId,
                    'chiave' => $chiave,
                    'valore' => $valore
                ]);
            }
        }
        
        // Inserisci le varianti
        if (isset($prodotto['varianti'])) {
            foreach ($prodotto['varianti'] as $variante) {
                $stmt = $pdo->prepare("
                    INSERT INTO varianti_prodotto (prodotto_id, tipo_variante, valore, prezzo_aggiuntivo)
                    VALUES (:prodotto_id, :tipo_variante, :valore, :prezzo_aggiuntivo)
                ");
                $stmt->execute([
                    'prodotto_id' => $prodottoId,
                    'tipo_variante' => $variante['tipo'],
                    'valore' => $variante['valore'],
                    'prezzo_aggiuntivo' => $variante['prezzo']
                ]);
            }
        }
    }
    
    // Inserisci coupon di esempio
    $coupon = [
        ['codice' => 'Sconto10', 'percentuale_sconto' => 10],
        ['codice' => 'Sconto20', 'percentuale_sconto' => 20],
        ['codice' => 'Sconto30', 'percentuale_sconto' => 30]
    ];
    
    foreach ($coupon as $c) {
        $stmt = $pdo->prepare("
            INSERT INTO coupon (codice, percentuale_sconto, attivo)
            VALUES (:codice, :percentuale_sconto, TRUE)
        ");
        $stmt->execute([
            'codice' => $c['codice'],
            'percentuale_sconto' => $c['percentuale_sconto']
        ]);
    }
    
    echo "Dati di esempio inseriti con successo!";
    
} catch (PDOException $e) {
    echo "Errore nell'inserimento dei dati: " . $e->getMessage();
}
?>