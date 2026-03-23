<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$date       = $_GET['date']       ?? '';
$service_id = $_GET['service_id'] ?? 0;

if (!$date) {
    echo json_encode(['creneaux' => []]);
    exit;
}

// Jour de la semaine (1=Lundi, 7=Dimanche)
$jour = (int)date('N', strtotime($date));

// Récupère les plages d'ouverture pour ce jour
$stmt = $pdo->prepare("
    SELECT heure_debut, heure_fin 
    FROM disponibilites 
    WHERE jour_semaine = ? AND actif = 1
    ORDER BY heure_debut
");
$stmt->execute([$jour]);
$plages = $stmt->fetchAll();

if (empty($plages)) {
    echo json_encode(['creneaux' => []]);
    exit;
}

// Durée du service
$duree = 30; // défaut
if ($service_id) {
    $stmt2 = $pdo->prepare("SELECT duree_minutes FROM services WHERE id = ?");
    $stmt2->execute([$service_id]);
    $svc = $stmt2->fetch();
    if ($svc) $duree = (int)$svc['duree_minutes'];
}

// Récupère les réservations existantes ce jour
$stmt3 = $pdo->prepare("
    SELECT heure_rdv FROM reservations 
    WHERE date_rdv = ? AND statut != 'annule'
");
$stmt3->execute([$date]);
$reservees = array_column($stmt3->fetchAll(), 'heure_rdv');

// Génère les créneaux toutes les 30 min dans chaque plage
$creneaux = [];
foreach ($plages as $plage) {
    $debut = strtotime($date . ' ' . $plage['heure_debut']);
    $fin   = strtotime($date . ' ' . $plage['heure_fin']);

    $current = $debut;
    while ($current + ($duree * 60) <= $fin) {
        $heure = date('H:i', $current);
        $creneaux[] = [
            'heure'      => $heure,
            'disponible' => !in_array($heure . ':00', $reservees) && !in_array($heure, $reservees),
        ];
        $current += 30 * 60; // +30 minutes
    }
}

echo json_encode(['creneaux' => $creneaux]);