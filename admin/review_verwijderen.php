<?php
// ============================================================
// Drone-Criticus — Review verwijderen (review_verwijderen.php)
// Enkel admins. Redirect daarna (PRG-patroon).
// ============================================================

require_once '../includes/config.php';
require_once '../includes/sessie.php';

vereisAdmin();

$pdo = getDatabaseVerbinding();

$reviewId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$droneId  = filter_input(INPUT_GET, 'drone_id', FILTER_VALIDATE_INT);
$terug    = $_GET['terug'] ?? 'drone';

if (!$reviewId) {
    header('Location: dashboard.php?fout=ongeldig_id');
    exit;
}

// Haal review op om drone_id te kennen (voor redirect)
$stmt = $pdo->prepare('SELECT drone_id FROM reviews WHERE id = ?');
$stmt->execute([$reviewId]);
$review = $stmt->fetch();

if (!$review) {
    header('Location: dashboard.php?fout=niet_gevonden');
    exit;
}

// Verwijder review
$stmt = $pdo->prepare('DELETE FROM reviews WHERE id = ?');
$stmt->execute([$reviewId]);

// Redirect terug
if ($terug === 'dashboard') {
    header('Location: dashboard.php?verwijderd=1');
} else {
    $doelDroneId = $droneId ?: $review['drone_id'];
    header('Location: ../php/drone_detail.php?id=' . (int) $doelDroneId . '&verwijderd=1');
}
exit;
