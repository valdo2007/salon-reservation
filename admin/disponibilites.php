
 <?php 
 require_once 'bonjour.php'
session_start();
require_once '../includes/db.php';
require_once '../includes/security.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$erreur  = ''; 

// ── TRAITEMENT ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $action = $_POST['action'] ?? '';

    // Ajouter une plage
    if ($action === 'ajouter') {
        $jour       = clean_int($_POST['jour_semaine'] ?? 0);
        $heure_debut = clean_string($_POST['heure_debut'] ?? '');
        $heure_fin   = clean_string($_POST['heure_fin']   ?? '');

        if (!$jour || !$heure_debut || !$heure_fin) {
            $erreur = 'Tous les champs sont obligatoires.';
        } elseif ($heure_debut >= $heure_fin) {
            $erreur = 'L\'heure de fin doit être après l\'heure de début.';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO disponibilites (jour_semaine, heure_debut, heure_fin, actif)
                VALUES (?, ?, ?, 1)
            ");
            $stmt->execute([$jour, $heure_debut, $heure_fin]);
            $message = 'Plage horaire ajoutée !';
        }
    }

    // Toggle actif
    if ($action === 'toggle') {
        $id = clean_int($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("UPDATE disponibilites SET actif = NOT actif WHERE id = ?")
                ->execute([$id]);
        }
        header('Location: disponibilites.php');
        exit;
    }

    // Supprimer
    if ($action === 'supprimer') {
        $id = clean_int($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM disponibilites WHERE id = ?")
                ->execute([$id]);
            $message = 'Plage supprimée.';
        }
    }
}

// ── Récupère les disponibilités ─────────────
$stmt = $pdo->query("
    SELECT * FROM disponibilites
    ORDER BY jour_semaine ASC, heure_debut ASC
");
$dispos = $stmt->fetchAll();

// Groupe par jour
$dispos_by_day = [];
foreach ($dispos as $d) {
    $dispos_by_day[$d['jour_semaine']][] = $d;
}

$jours = [
    1 => 'Lundi',
    2 => 'Mardi',
    3 => 'Mercredi',
    4 => 'Jeudi',
    5 => 'Vendredi',
    6 => 'Samedi',
    7 => 'Dimanche',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilités — Admin Coupe & Style</title>
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
            <a href="dashboard.php">📅 Réservations</a>
            <a href="services.php">✂️ Services</a>
            <a href="disponibilites.php" class="active">🕐 Disponibilités</a>
            <a href="logout.php" style="color:#f06080;">🚪 Déconnexion</a>
        </nav>
    </aside>

    <!-- ── CONTENU ── -->
    <main class="admin-main">

        <div class="admin-header">
            <h1>Gestion des disponibilités</h1>
            <p>Définissez les plages horaires d'ouverture du salon</p>
        </div>

        <?php if ($message) : ?>
        <div class="alert alert-success"><?= e($message) ?></div>
        <?php endif; ?>
        <?php if ($erreur) : ?>
        <div class="alert alert-error"><?= e($erreur) ?></div>
        <?php endif; ?>

        <div style="display:grid; grid-template-columns:1fr 1.5fr; gap:2rem; align-items:start;">

            <!-- ── FORMULAIRE AJOUT ── -->
            <div class="card">
                <h3 style="font-family:var(--font-serif); font-size:1.3rem;
                            margin-bottom:1.5rem; color:var(--foreground);">
                    ➕ Ajouter une plage
                </h3>

                <form method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="ajouter">

                    <div class="form-group">
                        <label class="form-label">Jour de la semaine *</label>
                        <select name="jour_semaine" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($jours as $num => $nom) : ?>
                            <option value="<?= $num ?>"><?= $nom ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label">Heure début *</label>
                            <input type="time" name="heure_debut"
                                   class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Heure fin *</label>
                            <input type="time" name="heure_fin"
                                   class="form-input" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary btn-primary-lg"
                            style="width:100%; margin-top:0.5rem;">
                        ➕ Ajouter
                    </button>
                </form>

                <!-- Info -->
                <div style="margin-top:1.5rem; padding:1rem;
                            background:rgba(0,206,209,0.06);
                            border:1px solid rgba(0,206,209,0.2);
                            border-radius:0.75rem;">
                    <p style="font-size:0.8rem; color:var(--muted-foreground); line-height:1.6;">
                        💡 Vous pouvez ajouter plusieurs plages par jour.<br>
                        Ex: <strong style="color:var(--primary);">9h-12h</strong>
                        et <strong style="color:var(--primary);">14h-18h</strong>
                        pour une pause déjeuner.
                    </p>
                </div>
            </div>

            <!-- ── PLANNING SEMAINE ── -->
            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                <?php foreach ($jours as $num => $nom) : ?>
                <div class="card" style="padding:1.25rem;">
                    <div style="display:flex; align-items:center;
                                justify-content:space-between; margin-bottom:0.75rem;">
                        <strong style="color:var(--foreground);"><?= $nom ?></strong>
                        <?php if (empty($dispos_by_day[$num])) : ?>
                        <span class="badge badge-annule">Fermé</span>
                        <?php else : ?>
                        <span class="badge badge-confirme">Ouvert</span>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($dispos_by_day[$num])) : ?>
                    <p style="font-size:0.85rem; color:var(--muted-foreground);">
                        Aucune plage horaire
                    </p>
                    <?php else : ?>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <?php foreach ($dispos_by_day[$num] as $d) : ?>
                        <div style="display:flex; align-items:center;
                                    justify-content:space-between;
                                    padding:0.5rem 0.75rem;
                                    background:rgba(0,206,209,0.06);
                                    border:1px solid rgba(0,206,209,0.15);
                                    border-radius:0.5rem;
                                    <?= !$d['actif'] ? 'opacity:0.4;' : '' ?>">
                            <span style="color:var(--primary); font-weight:500; font-size:0.9rem;">
                                🕐 <?= substr($d['heure_debut'],0,5) ?>
                                — <?= substr($d['heure_fin'],0,5) ?>
                            </span>
                            <div style="display:flex; gap:0.4rem;">
                                <!-- Toggle -->
                                <form method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <button type="submit"
                                            class="<?= $d['actif'] ? 'btn-en-attente' : 'btn-confirme' ?>"
                                            style="padding:0.25rem 0.6rem; font-size:0.75rem;">
                                        <?= $d['actif'] ? '⏸️' : '▶️' ?>
                                    </button>
                                </form>
                                <!-- Supprimer -->
                                <form method="POST" style="display:inline;"
                                      onsubmit="return confirm('Supprimer cette plage ?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="supprimer">
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <button type="submit" class="btn-annule"
                                            style="padding:0.25rem 0.6rem; font-size:0.75rem;">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </main>
</div>

</body>
</html> 
*/