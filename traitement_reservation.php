<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: reservation.php');
    exit;
}

// Vérification CSRF
csrf_verify();

// Rate limiting
if (!check_rate_limit('reservation', 5, 60)) {
    $_SESSION['erreurs'] = ['Trop de tentatives. Attendez une minute.'];
    header('Location: reservation.php');
    exit;
}

// Récupération et nettoyage
$service_id    = clean_int($_POST['service_id']   ?? 0);
$date_rdv      = clean_string($_POST['date_rdv']  ?? '');
$heure_rdv     = clean_string($_POST['heure_rdv'] ?? '');
$nom_client    = clean_string($_POST['nom_client']    ?? '');
$prenom_client = clean_string($_POST['prenom_client'] ?? '');
$email_client  = clean_email($_POST['email_client']   ?? '');
$telephone     = clean_string($_POST['telephone']     ?? '');

// Validation
$erreurs = [];

if (!$service_id)                        $erreurs[] = 'Service invalide.';
if (!valid_date($date_rdv))              $erreurs[] = 'Date invalide.';
if (empty($heure_rdv))                   $erreurs[] = 'Heure manquante.';
if (empty($nom_client))                  $erreurs[] = 'Nom manquant.';
if (empty($prenom_client))               $erreurs[] = 'Prénom manquant.';
if (!valid_email($email_client))         $erreurs[] = 'Email invalide.';
if (empty($telephone))                   $erreurs[] = 'Téléphone manquant.';
if ($date_rdv < date('Y-m-d'))           $erreurs[] = 'La date ne peut pas être dans le passé.';

// Vérifie que le service existe
if ($service_id) {
    $stmt = $pdo->prepare("SELECT id FROM services WHERE id = ? AND actif = 1");
    $stmt->execute([$service_id]);
    if (!$stmt->fetch()) $erreurs[] = 'Service introuvable.';
}

if (!empty($erreurs)) {
    $_SESSION['erreurs'] = $erreurs;
    header('Location: reservation.php');
    exit;
}

// Vérifie créneau disponible
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM reservations
    WHERE date_rdv = ? AND heure_rdv = ? AND statut != 'annule'
");
$stmt->execute([$date_rdv, $heure_rdv]);
if ($stmt->fetchColumn() > 0) {
    $_SESSION['erreurs'] = ['Ce créneau vient d\'être pris. Choisissez-en un autre.'];
    header('Location: reservation.php');
    exit;
}

// Insertion en BDD
try {
    $stmt = $pdo->prepare("
        INSERT INTO reservations
            (service_id, date_rdv, heure_rdv, nom_client, prenom_client,
             email_client, telephone, statut)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'en_attente')
    ");
    $stmt->execute([
        $service_id, $date_rdv, $heure_rdv,
        $nom_client, $prenom_client, $email_client, $telephone
    ]);

    $reservation_id = $pdo->lastInsertId();

    header("Location: confirmation.php?id=$reservation_id");
    exit;

} catch (PDOException $e) {
    $_SESSION['erreurs'] = ['Erreur serveur. Veuillez réessayer.'];
    header('Location: reservation.php');
    exit;
}

