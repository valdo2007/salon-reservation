<?php require_once 'includes/db.php'; ?>
<?php require_once 'includes/header.php'; ?>
<link rel="icon" href="assets/img/Capture d'écran 2026-03-23 144839.png" type="image/png">

<section class="hero">
    <div class="container">
        <div class="hero-inner">
            <span class="hero-badge">✦ Salon Premium à Orléans by Ronny and Cheickh </span>
            <h1>L'excellence au service<br>de <span>votre style</span></h1>
            <p>Réservez votre rendez-vous en ligne 24h/24, 7j/7.<br>
               Nos coiffeurs experts vous accueillent dans un cadre raffiné.</p>
            <div class="hero-actions">
                <a href="/salon-coiffure/reservation.php" class="btn-primary btn-primary-lg">
                    Réserver maintenant
                </a>
                <a href="#services" class="btn-secondary">
                    Nos services
                </a>
            </div>
        </div>
    </div>
</section>


<section class="section" id="services">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Ce que nous proposons</span>
            <h2 class="section-title">Nos services</h2>
            <p class="section-subtitle">
                Des prestations haut de gamme adaptées à chaque client
            </p>
        </div>

        <?php
        $stmt = $pdo->query("SELECT * FROM services WHERE actif = 1 ORDER BY prix_euros ASC");
        $services = $stmt->fetchAll();
        ?>

        <div class="services-grid">
            <?php foreach ($services as $service) : ?>
            <div class="card">
                <div class="card-icon"> <img src="assets/img/Capture d'écran 2026-03-23 144839.png" alt="" srcset=""> </div>
                <h3 class="card-title"><?= htmlspecialchars($service['nom']) ?></h3>
                <p class="card-desc"><?= htmlspecialchars($service['description']) ?></p>
                <div class="card-price"><?= number_format($service['prix_euros'], 0) ?>€</div>
                <div class="card-duration">⏱ <?= $service['duree_minutes'] ?> minutes</div>
                <a href="/salon-coiffure/reservation.php?service=<?= $service['id'] ?>"
                   class="btn-primary" style="margin-top:1.25rem; display:inline-block;">
                    Réserver ce service
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<section class="section section-alt" id="horaires">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Quand nous trouver</span>
            <h2 class="section-title">Horaires d'ouverture</h2>
        </div>

        <div class="horaires-grid">
            <?php
$jours = [
    1 => 'Lundi',
    2 => 'Mardi', 
    3 => 'Mercredi',
    4 => 'Jeudi',
    5 => 'Vendredi',
    6 => 'Samedi',
    7 => 'Dimanche',
];

$stmt2 = $pdo->query("
    SELECT jour_semaine,
           MIN(heure_debut) as ouverture,
           MAX(heure_fin)   as fermeture
    FROM disponibilites
    WHERE actif = 1
    GROUP BY jour_semaine
    ORDER BY jour_semaine
");
$dispos_by_day = [];
while ($row = $stmt2->fetch()) {
    $dispos_by_day[$row['jour_semaine']] = $row;
}
?>

<?php foreach ($jours as $num => $nom) : ?>
<div class="horaire-item">
    <span class="horaire-day"><?= $nom ?></span>
    <?php if (isset($dispos_by_day[$num])) : ?>
        <span class="horaire-time">
            <?= substr($dispos_by_day[$num]['ouverture'], 0, 5) ?>
            –
            <?= substr($dispos_by_day[$num]['fermeture'], 0, 5) ?>
        </span>
    <?php else : ?>
        <span class="horaire-closed">Fermé</span>
    <?php endif; ?>
</div>
<?php endforeach; ?>
            
        </div>
    </div>
</section>


<section class="section" id="avis">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Ils nous font confiance</span>
            <h2 class="section-title">Avis clients</h2>
        </div>

        <div class="services-grid">
            <?php
            $temoignages = [
                ['nom' => 'Sophie M.',   'note' => '⭐⭐⭐⭐⭐', 'texte' => 'Un salon exceptionnel ! Le personnel est attentionné et le résultat toujours parfait. Je recommande vivement.'],
                ['nom' => 'Thomas K.',   'note' => '⭐⭐⭐⭐⭐', 'texte' => 'Très professionnel, ambiance agréable. La réservation en ligne est super pratique, plus besoin d\'appeler.'],
                ['nom' => 'Léa D.',      'note' => '⭐⭐⭐⭐⭐', 'texte' => 'Ma coloriste est une artiste ! Je ressors toujours ravie. Le salon est moderne et élégant.'],
            ];
            ?>
            <?php foreach ($temoignages as $t) : ?>
            <div class="card">
                <div style="font-size:1.2rem; margin-bottom:0.75rem;"><?= $t['note'] ?></div>
                <p class="card-desc">"<?= $t['texte'] ?>"</p>
                <p style="color:var(--primary); font-weight:500; font-size:0.9rem; margin-top:1rem;">
                    — <?= $t['nom'] ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-alt" id="contact">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Nous trouver</span>
            <h2 class="section-title">Contact & Adresse</h2>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem; max-width:700px; margin:0 auto;">
            <div class="card" style="text-align:center;">
                <div class="card-icon" style="margin:0 auto 1rem;"><img src="assets/img/Capture d'écran 2026-03-23 145214.png" alt="" srcset=""></div>
                <h3 class="card-title">Adresse</h3>
                <p class="card-desc">Saint jean de la Ruelle<br>45100 Orléans</p>
            </div>
            <div class="card" style="text-align:center;">
                <div class="card-icon" style="margin:0 auto 1rem;"><img src="assets/img/tel.jpg" alt="" srcset=""></div>
                <h3 class="card-title">Téléphone</h3>
                <p class="card-desc">01 23 45 67 89<br>
                <span style="font-size:0.8rem; color:var(--muted-foreground);">
                    Ou réservez directement en ligne
                </span></p>
            </div>
        </div>

        <div style="text-align:center; margin-top:3rem;">
            <h3 style="font-family:var(--font-serif); font-size:1.8rem; margin-bottom:1rem;">
                Prêt à prendre soin de vous ?
            </h3>
            <a href="/salon-coiffure/reservation.php" class="btn-primary btn-primary-lg">
                Réserver mon rendez-vous
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>