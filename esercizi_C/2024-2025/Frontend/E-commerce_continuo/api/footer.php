<?php
// Dati del footer
$footer = [
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

// Invia la risposta come JSON
header('Content-Type: application/json');
echo json_encode($footer);
?>