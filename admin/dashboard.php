<?php
session_start();
require_once '../includes/db.php';

// Protection : si pas connecté → login
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// ── Traitement changement de statut ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'], $_POST['statut'])) {
    $statuts_valides = ['en_attente', 'confirme', 'annule'];
    $nouveau_statut  = $_POST['statut'];
    $reservation_id  = (int)$_POST['reservation_id'];

    if (in_array($nouveau_statut, $statuts_valides)) {
        $stmt = $pdo->prepare("UPDATE reservations SET statut = ? WHERE id = ?");
        $stmt->execute([$nouveau_statut, $reservation_id]);
    }

    header('Location: dashboard.php');
    exit;
}

// ── Filtres ──
$filtre_statut = $_GET['statut'] ?? 'tous';
$filtre_date   = $_GET['date']   ?? '';

$where  = [];
$params = [];

if ($filtre_statut !== 'tous') {
    $where[]  = 'r.statut = ?';
    $params[] = $filtre_statut;
}
if ($filtre_date) {
    $where[]  = 'r.date_rdv = ?';
    $params[] = $filtre_date;
}

$sql = "
    SELECT r.*, s.nom as service_nom, s.prix_euros
    FROM reservations r
    JOIN services s ON r.service_id = s.id
";
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY r.date_rdv ASC, r.heure_rdv ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll();

// ── Compteurs pour les stats ──
$stats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(statut = 'en_attente') as en_attente,
        SUM(statut = 'confirme')   as confirme,
        SUM(statut = 'annule')     as annule
    FROM reservations
")->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Coupe & Style</title>
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
    <a href="disponibilites.php"> Disponibilités</a>  
    <a href="logout.php" style="color:#f06080;"> Déconnexion</a>
</nav>
    </aside>

    <!-- ── CONTENU PRINCIPAL ── -->
    <main class="admin-main">

        <!-- Header -->
        <div class="admin-header">
            <h1>Tableau de bord</h1>
            <p>Bonjour <strong><?= htmlspecialchars($_SESSION['admin_user']) ?></strong>
               — <?= date('l d F Y') ?></p>
        </div>

        <!-- Stats -->
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:2rem;">
            <?php
            $stat_items = [
                ['label'=>'Total',      'value'=>$stats['total'],      'color'=>'var(--primary)'],
                ['label'=>'En attente', 'value'=>$stats['en_attente'], 'color'=>'var(--secondary)'],
                ['label'=>'Confirmées', 'value'=>$stats['confirme'],   'color'=>'#00CED1'],
                ['label'=>'Annulées',   'value'=>$stats['annule'],     'color'=>'#f06080'],
            ];
            foreach ($stat_items as $s) : ?>
            <div class="card" style="text-align:center; padding:1.5rem;">
                <div style="font-size:2rem; font-weight:700; color:<?= $s['color'] ?>;">
                    <?= $s['value'] ?>
                </div>
                <div style="font-size:0.85rem; color:var(--muted-foreground); margin-top:0.25rem;">
                    <?= $s['label'] ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Filtres -->
        <div style="display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; align-items:center;">
            <div style="display:flex; gap:0.5rem;">
                <?php
                $filtres = ['tous'=>'Tous', 'en_attente'=>'En attente', 'confirme'=>'Confirmés', 'annule'=>'Annulés'];
                foreach ($filtres as $val => $label) :
                    $active = $filtre_statut === $val;
                ?>
                <a href="?statut=<?= $val ?><?= $filtre_date ? '&date='.$filtre_date : '' ?>"
                   class="<?= $active ? 'btn-primary' : 'btn-secondary' ?>"
                   style="padding:0.4rem 1rem; font-size:0.85rem;">
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Filtre date -->
            <form method="GET" style="display:flex; gap:0.5rem; align-items:center;">
                <input type="hidden" name="statut" value="<?= htmlspecialchars($filtre_statut) ?>">
                <input type="date" name="date" value="<?= htmlspecialchars($filtre_date) ?>"
                       class="form-input" style="width:auto; padding:0.4rem 0.75rem;">
                <button type="submit" class="btn-primary" style="padding:0.4rem 1rem; font-size:0.85rem;">
                    Filtrer
                </button>
                <?php if ($filtre_date) : ?>
                <a href="?statut=<?= $filtre_statut ?>"
                   class="btn-ghost" style="font-size:0.85rem;">✕</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tableau réservations -->
        <div class="table-wrapper">
            <?php if (empty($reservations)) : ?>
            <div style="text-align:center; padding:3rem; color:var(--muted-foreground);">
                Aucune réservation trouvée.
            </div>
            <?php else : ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Date & Heure</th>
                        <th>Contact</th>
                        <th>Statut actuel</th>
                        <th>Changer statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $r) : ?>
                    <tr>
                        <td style="color:var(--muted-foreground);">#<?= $r['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($r['prenom_client'].' '.$r['nom_client']) ?></strong>
                        </td>
                        <td>
                            <?= htmlspecialchars($r['service_nom']) ?>
                            <div style="font-size:0.8rem; color:var(--primary);">
                                <?= number_format($r['prix_euros'], 0) ?>€
                            </div>
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($r['date_rdv'])) ?>
                            <div style="font-size:0.85rem; color:var(--muted-foreground);">
                                <?= substr($r['heure_rdv'], 0, 5) ?>
                            </div>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($r['email_client']) ?></div>
                            <div style="font-size:0.85rem; color:var(--muted-foreground);">
                                <?= htmlspecialchars($r['telephone']) ?>
                            </div>
                        </td>
                        <td>
                            <?php
                            $badge_class = match($r['statut']) {
                                'confirme'   => 'badge-confirme',
                                'annule'     => 'badge-annule',
                                default      => 'badge-en-attente',
                            };
                            $badge_label = match($r['statut']) {
                                'confirme'   => 'Confirmé',
                                'annule'     => 'Annulé',
                                default      => 'En attente',
                            };
                            ?>
                            <span class="badge <?= $badge_class ?>"><?= $badge_label ?></span>
                        </td>
                        <td>
                            <!-- Les 3 boutons de statut -->
                            <div class="statut-actions">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="statut" value="confirme">
                                    <button type="submit"
                                            class="btn-confirme <?= $r['statut']==='confirme' ? 'active' : '' ?>"
                                            <?= $r['statut']==='confirme' ? 'disabled' : '' ?>>
                                        ✅ Confirmé
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="statut" value="en_attente">
                                    <button type="submit"
                                            class="btn-en-attente <?= $r['statut']==='en_attente' ? 'active' : '' ?>"
                                            <?= $r['statut']==='en_attente' ? 'disabled' : '' ?>>
                                        ⏳ En attente
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="statut" value="annule">
                                    <button type="submit"
                                            class="btn-annule <?= $r['statut']==='annule' ? 'active' : '' ?>"
                                            <?= $r['statut']==='annule' ? 'disabled' : '' ?>>
                                        ❌ Annulé
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

    </main>
</div>

</body>
</html>