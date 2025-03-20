<?php
// Imposta l'header per JSON
header('Content-Type: application/json');

// Dati del footer
$footerData = [
    'social' => [
        [
            'icon' => 'fab fa-facebook',
            'link' => 'https://facebook.com'
        ],
        [
            'icon' => 'fab fa-twitter',
            'link' => 'https://twitter.com'
        ],
        [
            'icon' => 'fab fa-instagram',
            'link' => 'https://instagram.com'
        ],
        [
            'icon' => 'fab fa-youtube',
            'link' => 'https://youtube.com'
        ]
    ],
    'email' => 'info@pccomponenti.it',
    'copyright' => '&copy; ' . date('Y') . ' PC Componenti. Tutti i diritti riservati.'
];

echo json_encode($footerData);
?>