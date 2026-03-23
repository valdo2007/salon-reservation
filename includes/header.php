<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupe & Style — Salon de coiffure premium</title>
    <link rel="stylesheet" href="/salon-coiffure/assets/css/style.css">
    <link rel="icon" type="image/jpeg" href="assets/img/Capture d'écran 2026-03-23 144839.png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="site-header">
    <div class="container">
        <div class="header-inner">

            <!-- Logo -->
            <a href="/salon-coiffure/index.php" class="logo">
                <div class="logo-circle">C&S</div>
                <span class="logo-text">Coupe & Style</span>
            </a>

            <!-- Navigation -->
            <nav class="main-nav" id="mainNav">
                <a href="index.php" class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">Accueil</a>
                <a href="index.php#services" class="nav-link">Services</a>
                <a href="index.php#horaires" class="nav-link">Horaires</a>
                <a href="index.php#contact" class="nav-link">Contact</a>
            </nav>

           <!-- CTA -->
               <!-- Boutons header -->
<div style="display:flex; align-items:center; gap:0.75rem;">
    <a href="/salon-coiffure/admin/index.php" class="btn-secondary" 
       style="padding:0.6rem 1.25rem; font-size:0.85rem;">
         Admin
    </a>
    <a href="/salon-coiffure/reservation.php" class="btn-primary">
        Réserver
    </a>
</div>
            <!-- Burger menu mobile -->
            <button class="burger" id="burgerBtn" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div>
    </div>
</header>