<?php
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');
require_once 'includes/db.php';
require_once 'includes/header.php';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

// Récupère la réservation avec le nom du service
$stmt = $pdo->prepare("
    SELECT r.*, s.nom as service_nom, s.prix_euros, s.duree_minutes
    FROM reservations r
    JOIN services s ON r.service_id = s.id
    WHERE r.id = ?
");
$stmt->execute([$id]);
$resa = $stmt->fetch();

if (!$resa) {
    header('Location: index.php');
    exit;
}
?>

<main style="padding: 5rem 0;">
<div class="container">
    <div style="max-width: 600px; margin: 0 auto; text-align: center;">

        <!-- Icône succès -->
        <div style="
            width: 80px; height: 80px;
            background: rgba(0, 206, 209, 0.15);
            border: 2px solid var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin: 0 auto 2rem;
        "><img src="assets/img/validation.jpg" alt=""></div>

        <span class="section-label">Réservation enregistrée</span>
        <h1 class="section-title" style="margin-top: 0.5rem;">
            Merci <?= htmlspecialchars($resa['prenom_client']) ?> !
        </h1>
        <p style="color: var(--muted-foreground); margin-bottom: 2.5rem;">
            Votre demande de rendez-vous a bien été enregistrée.<br>
            Vous recevrez une confirmation dès validation par le salon.
        </p>

        <!-- Récapitulatif -->
        <div class="recap-box" style="text-align: left; margin-bottom: 2.5rem;">
            <p style="color: var(--primary); font-weight: 600; 
                      font-size: 0.85rem; letter-spacing: 1px;
                      text-transform: uppercase; margin-bottom: 1.25rem;">
                Détails de votre réservation
            </p>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--muted-foreground);">Service</span>
                    <strong><?= htmlspecialchars($resa['service_nom']) ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--muted-foreground);">Date</span>
                    <strong><?= date('l d F Y', strtotime($resa['date_rdv'])) ?></strong>
                   
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--muted-foreground);">Heure</span>
                    <strong><?= substr($resa['heure_rdv'], 0, 5) ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--muted-foreground);">Durée</span>
                    <strong><?= $resa['duree_minutes'] ?> minutes</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--muted-foreground);">Prix</span>
                    <strong style="color:var(--primary);">
                        <?= number_format($resa['prix_euros'], 0) ?>€
                    </strong>
                </div>
                <div style="
                    border-top: 1px solid var(--border); 
                    padding-top: 0.75rem;
                    display:flex; justify-content:space-between;">
                    <span style="color:var(--muted-foreground);">Statut</span>
                    <span class="badge badge-en-attente">En attente de confirmation</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="index.php" class="btn-secondary">
                ← Retour à l'accueil
            </a>
            <a href="reservation.php" class="btn-primary">
                Nouvelle réservation
            </a>
        </div>

    </div>
</div>
</main>

<?php require_once 'includes/footer.php'; ?>