<?php
// ============================================================
// Drone-Criticus — Sessie & authenticatie hulpfuncties
// ============================================================

require_once __DIR__ . '/config.php';

// Start sessie als die nog niet gestart is
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Geeft terug of de bezoeker ingelogd is.
 */
function isIngelogd(): bool {
    return isset($_SESSION['gebruiker_id']) && !empty($_SESSION['gebruiker_id']);
}

/**
 * Geeft terug of de ingelogde gebruiker een admin is.
 */
function isAdmin(): bool {
    return isIngelogd() && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

/**
 * Stuur niet-ingelogde bezoekers door naar de loginpagina.
 */
function vereisLogin(): void {
    if (!isIngelogd()) {
        header('Location: ../php/login.php');
        exit;
    }
}

/**
 * Stuur niet-admins door naar de hoofdpagina.
 */
function vereisAdmin(): void {
    if (!isAdmin()) {
        header('Location: ../index.php?fout=geen_toegang');
        exit;
    }
}

/**
 * Verwijder alle sessiedata en log de gebruiker uit.
 */
function uitloggen(): void {
    $_SESSION = [];
    session_destroy();
    header('Location: ../index.php');
    exit;
}

/**
 * Saniteer gebruikersinvoer (HTML escapen).
 */
function saniteer(string $invoer): string {
    return htmlspecialchars(trim($invoer), ENT_QUOTES, 'UTF-8');
}


