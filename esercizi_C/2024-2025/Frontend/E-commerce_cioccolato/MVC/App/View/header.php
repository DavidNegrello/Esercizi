<?php
$appConfig= require dirname(__DIR__, 2) . '/appConfig.php';
$baseUrl = $appConfig['baseURL'].$appConfig['prjName'];
$homePage = $baseUrl.'mvc/home';
$productPage = $baseUrl.'mvc/products';
$loginPage = $baseUrl.'mvc/login';
$carrelloPage = $baseUrl.'mvc/carrello';
$homeStyles=$appConfig['baseURL'].$appConfig['prjName'].$appConfig['cssHome'];
$productStyles = $appConfig['baseURL'].$appConfig['prjName'].$appConfig['cssProduct'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="<?=$homeStyles?>">
    <link rel="stylesheet" href="<?=$productStyles?>">
</head>
<body>
<!-- Header/Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="z-index: 999">
    <div class="container">
        <a class="navbar-brand" href="<?=$homePage?>">Dolce Vita Cioccolato</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?=$homePage?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?=$productPage?>">Prodotti</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Chi Siamo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contatti</a>
                </li>
            </ul>
            <div class="d-flex">
                <a href="<?=$loginPage?>" class="btn btn-outline-light me-2">Login</a>
                <a href="<?=$carrelloPage?>" class="btn btn-outline-light"><i class="bi bi-cart3"></i> <span class="badge bg-secondary">0</span></a>
            </div>
        </div>
    </div>
</nav>


