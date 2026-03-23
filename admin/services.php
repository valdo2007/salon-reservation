<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/security.php';

// Protection
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$erreur  = '';

// ── TRAITEMENT FORMULAIRES ──────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $action = $_POST['action'] ?? '';

    // ── Ajouter un service ──
    if ($action === 'ajouter') {
        $nom         = clean_string($_POST['nom']         ?? '');
        $description = clean_string($_POST['description'] ?? '');
        $duree       = clean_int($_POST['duree_minutes']  ?? 0);
        $prix        = (float)($_POST['prix_euros']       ?? 0);

        if (empty($nom) || !$duree || !$prix) {
            $erreur = 'Tous les champs obligatoires doivent être remplis.';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO services (nom, description, duree_minutes, prix_euros, actif)
                VALUES (?, ?, ?, ?, 1)
            ");
            $stmt->execute([$nom, $description, $duree, $prix]);
            $message = "Service \"$nom\" ajouté avec succès !";
        }
    }

    // ── Modifier un service ──
    if ($action === 'modifier') {
        $id          = clean_int($_POST['id']             ?? 0);
        $nom         = clean_string($_POST['nom']         ?? '');
        $description = clean_string($_POST['description'] ?? '');
        $duree       = clean_int($_POST['duree_minutes']  ?? 0);
        $prix        = (float)($_POST['prix_euros']       ?? 0);

        if (!$id || empty($nom) || !$duree || !$prix) {
            $erreur = 'Données invalides.';
        } else {
            $stmt = $pdo->prepare("
                UPDATE services
                SET nom=?, description=?, duree_minutes=?, prix_euros=?
                WHERE id=?
            ");
            $stmt->execute([$nom, $description, $duree, $prix, $id]);
            $message = "Service mis à jour avec succès !";
        }
    }

    // ── Activer / Désactiver ──
    if ($action === 'toggle') {
        $id = clean_int($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $pdo->prepare("
                UPDATE services SET actif = NOT actif WHERE id = ?
            ");
            $stmt->execute([$id]);
        }
        header('Location: services.php');
        exit;
    }

    // ── Supprimer ──
    if ($action === 'supprimer') {
        $id = clean_int($_POST['id'] ?? 0);
        if ($id) {
            // Vérifie qu'aucune réservation future n'est liée
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM reservations
                WHERE service_id = ?
                AND date_rdv >= CURDATE()
                AND statut != 'annule'
            ");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                $erreur = 'Impossible de supprimer : des réservations futures sont liées à ce service.';
            } else {
                $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);
                $message = 'Service supprimé.';
            }
        }
    }
}

// ── Récupère tous les services ──────────────
$services = $pdo->query("
    SELECT s.*,
           COUNT(r.id) as nb_reservations
    FROM services s
    LEFT JOIN reservations r ON r.service_id = s.id
    GROUP BY s.id
    ORDER BY s.actif DESC, s.prix_euros ASC
")->fetchAll();

// Service à modifier (si on clique sur Modifier)
$service_edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([clean_int($_GET['edit'])]);
    $service_edit = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services — Admin Coupe & Style</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="admin-layout">

    <!-- ── SIDEBAR ── -->
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <div class="logo">
                <div class="logo-circle">C&S</div>
                <span class="logo-text">Coupe & Style</span>
            </div>
        </div>
        <nav class="admin-nav">
    <a href="dashboard.php"> Réservations</a>
    <a href="services.php"> Services</a>
  
    <a href="logout.php" style="color:#f06080;"> Déconnexion</a>
</nav>
    </aside>

    <!-- ── CONTENU ── -->
    <main class="admin-main">

        <div class="admin-header">
            <h1>Gestion des services</h1>
            <p>Ajoutez, modifiez ou désactivez les services du salon</p>
        </div>

        <?php if ($message) : ?>
        <div class="alert alert-success"><?= e($message) ?></div>
        <?php endif; ?>

        <?php if ($erreur) : ?>
        <div class="alert alert-error"><?= e($erreur) ?></div>
        <?php endif; ?>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem; align-items:start;">

            <!-- ── FORMULAIRE AJOUT / MODIF ── -->
            <div class="card">
                <h3 style="font-family:var(--font-serif); font-size:1.3rem;
                            margin-bottom:1.5rem; color:var(--foreground);">
                    <?= $service_edit ? '✏️ Modifier le service' : ' Ajouter un service' ?>
                </h3>

                <form method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action"
                           value="<?= $service_edit ? 'modifier' : 'ajouter' ?>">
                    <?php if ($service_edit) : ?>
                    <input type="hidden" name="id" value="<?= $service_edit['id'] ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label">Nom du service *</label>
                        <input type="text" name="nom" class="form-input"
                               placeholder="Ex: Coupe homme"
                               value="<?= e($service_edit['nom'] ?? '') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-input form-textarea"
                                  rows="3"
                                  placeholder="Description du service..."><?= e($service_edit['description'] ?? '') ?></textarea>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label">Durée (minutes) *</label>
                            <input type="number" name="duree_minutes" class="form-input"
                                   placeholder="30" min="5" max="480"
                                   value="<?= e($service_edit['duree_minutes'] ?? '') ?>"
                                   required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Prix (€) *</label>
                            <input type="number" name="prix_euros" class="form-input"
                                   placeholder="25" min="1" step="0.01"
                                   value="<?= e($service_edit['prix_euros'] ?? '') ?>"
                                   required>
                        </div>
                    </div>

                    <div style="display:flex; gap:0.75rem; margin-top:0.5rem;">
                        <button type="submit" class="btn-primary btn-primary-lg">
                            <?= $service_edit ? '💾 Enregistrer' : ' Ajouter' ?>
                        </button>
                        <?php if ($service_edit) : ?>
                        <a href="services.php" class="btn-secondary">Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- ── LISTE DES SERVICES ── -->
            <div>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Durée</th>
                                <th>Prix</th>
                                <th>Résa</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($services as $s) : ?>
                        <tr style="<?= !$s['actif'] ? 'opacity:0.5;' : '' ?>">
                            <td>
                                <strong><?= e($s['nom']) ?></strong>
                                <?php if ($s['description']) : ?>
                                <div style="font-size:0.75rem; color:var(--muted-foreground);
                                            margin-top:2px;">
                                    <?= e(substr($s['description'], 0, 40)) ?>...
                                </div>
                                <?php endif; ?>
                            </td>
                            <td><?= $s['duree_minutes'] ?> min</td>
                            <td style="color:var(--primary); font-weight:600;">
                                <?= number_format($s['prix_euros'], 0) ?>€
                            </td>
                            <td style="color:var(--muted-foreground);">
                                <?= $s['nb_reservations'] ?>
                            </td>
                            <td>
                                <?php if ($s['actif']) : ?>
                                <span class="badge badge-confirme">Actif</span>
                                <?php else : ?>
                                <span class="badge badge-annule">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                    <!-- Modifier -->
                                    <a href="?edit=<?= $s['id'] ?>"
                                       class="btn-en-attente"
                                       style="padding:0.3rem 0.75rem; font-size:0.75rem;
                                              border-radius:9999px;">
                                        Modifier
                                    </a>
                                    <!-- Toggle actif -->
                                    <!-- <form method="POST" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <button type="submit"
                                                class="<?= $s['actif'] ? 'btn-annule' : 'btn-confirme' ?>"
                                                style="padding:0.3rem 0.75rem; font-size:0.75rem;">
                                            <?= $s['actif'] ? '⏸️' : '▶️' ?>
                                        </button>
                                    </form> -->
                                    <!-- Supprimer -->
                                    <form method="POST" style="display:inline;"
                                          onsubmit="return confirm('Supprimer ce service ?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="supprimer">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn-annule"
                                                style="padding:0.3rem 0.75rem; font-size:0.75rem;">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>

</body>
</html>

