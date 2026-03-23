

<?php

session_start();
require_once 'includes/security.php';

require_once 'includes/db.php';
require_once 'includes/header.php';

// Récupère les services depuis la BDD
$stmt = $pdo->query("SELECT * FROM services WHERE actif = 1 ORDER BY prix_euros ASC");
$services = $stmt->fetchAll();

// Pré-sélection si on vient d'un bouton "Réserver ce service"
$service_preselect = isset($_GET['service']) ? (int)$_GET['service'] : 0;
?>

<main style="padding: 4rem 0;">
<div class="container">

    <!-- Titre page -->
    <div class="section-header">
        <span class="section-label">Votre rendez-vous</span>
        <h1 class="section-title">Prendre rendez-vous</h1>
        <p class="section-subtitle">Réservez en quelques clics, 24h/24</p>
    </div>

    <!-- ── STEPPER ── -->
    <div class="stepper">
        <div class="step active" id="step-indicator-1">
            <div class="step-circle">1</div>
            <span class="step-label">Service</span>
        </div>
        <div class="step-line"></div>
        <div class="step" id="step-indicator-2">
            <div class="step-circle">2</div>
            <span class="step-label">Créneau</span>
        </div>
        <div class="step-line"></div>
        <div class="step" id="step-indicator-3">
            <div class="step-circle">3</div>
            <span class="step-label">Coordonnées</span>
        </div>
    </div>

    <!-- ── FORMULAIRE ── -->
    <form id="reservationForm" method="POST" action="traitement_reservation.php">
    <?= csrf_field() ?>

        <!-- ═══ ÉTAPE 1 : Choix du service ═══ -->
        <div class="form-step active" id="step-1">
            <h2 class="step-title">Choisissez votre service</h2>

            <div class="services-choix">
                <?php foreach ($services as $service) : ?>
                <label class="service-option <?= $service_preselect === $service['id'] ? 'selected' : '' ?>">
                    <input type="radio"
                           name="service_id"
                           value="<?= $service['id'] ?>"
                           data-duree="<?= $service['duree_minutes'] ?>"
                           data-nom="<?= htmlspecialchars($service['nom']) ?>"
                           <?= $service_preselect === $service['id'] ? 'checked' : '' ?>>
                    <div class="service-option-inner">
                        <div class="service-option-info">
                            <span class="service-option-nom"><?= htmlspecialchars($service['nom']) ?></span>
                            <span class="service-option-desc"><?= htmlspecialchars($service['description']) ?></span>
                        </div>
                        <div class="service-option-meta">
                            <span class="service-option-prix"><?= number_format($service['prix_euros'], 0) ?>€</span>
                            <span class="service-option-duree">⏱ <?= $service['duree_minutes'] ?> min</span>
                        </div>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>

            <div class="step-actions">
                <button type="button" class="btn-primary btn-primary-lg" id="btn-step1-next">
                    Continuer →
                </button>
            </div>
        </div>

        <!-- ═══ ÉTAPE 2 : Choix du créneau ═══ -->
        <div class="form-step" id="step-2">
            <h2 class="step-title">Choisissez un créneau</h2>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem;">

                <!-- Calendrier -->
                <div>
                    <label class="form-label">Date du rendez-vous</label>
                    <input type="date"
                           name="date_rdv"
                           id="date_rdv"
                           class="form-input"
                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                           required>
                </div>

                <!-- Créneaux horaires -->
                <div>
                    <label class="form-label">Heure disponible</label>
                    <div class="creneaux-grid" id="creneaux-container">
                        <p style="color:var(--muted-foreground); font-size:0.9rem;">
                            Sélectionnez d'abord une date
                        </p>
                    </div>
                </div>
            </div>

            <input type="hidden" name="heure_rdv" id="heure_rdv_hidden">

            <div class="step-actions">
                <button type="button" class="btn-secondary" id="btn-step2-prev">
                    ← Retour
                </button>
                <button type="button" class="btn-primary btn-primary-lg" id="btn-step2-next">
                    Continuer →
                </button>
            </div>
        </div>

        <!-- ═══ ÉTAPE 3 : Coordonnées ═══ -->
        <div class="form-step" id="step-3">
            <h2 class="step-title">Vos coordonnées</h2>

            <!-- Récapitulatif -->
            <div class="recap-box" id="recap-box">
                <p style="color:var(--muted-foreground); font-size:0.85rem; margin-bottom:0.5rem;">
                    Récapitulatif de votre réservation
                </p>
                <div style="display:flex; gap:2rem; flex-wrap:wrap;">
                    <span> <strong id="recap-service">—</strong></span>
                    <span><strong id="recap-date">—</strong></span>
                    <span>🕐 <strong id="recap-heure">—</strong></span>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom_client" class="form-input"
                           placeholder="Dupont" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="prenom_client" class="form-input"
                           placeholder="Sophie" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email_client" class="form-input"
                           placeholder="sophie@email.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Téléphone *</label>
                    <input type="tel" name="telephone" class="form-input"
                           placeholder="06 12 34 56 78" required>
                </div>
            </div>

            <div class="step-actions">
                <button type="button" class="btn-secondary" id="btn-step3-prev">
                    ← Retour
                </button>
                <button type="submit" class="btn-primary btn-primary-lg">
                     Confirmer ma réservation
                </button>
            </div>
        </div>

    </form>
</div>
</main>

<?php require_once 'includes/footer.php'; ?>