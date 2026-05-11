<?php
// ============================================================
// Drone-Criticus — Header (navigatie + HTML head)
// ============================================================
// Verwacht dat $paginaTitel is ingesteld voor de aanroep.

require_once __DIR__ . '/sessie.php';

$paginaTitel = $paginaTitel ?? 'Drone-Criticus';


// Basispad relatief aan de huidige pagina berekenen
$scriptPad  = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
$rootPad    = str_replace('\\', '/', realpath(__DIR__ . '/..')) . '/';
$relatief   = str_replace($rootPad, '', $scriptPad);
$diepte     = substr_count($relatief, '/');
$basis      = str_repeat('../', $diepte);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= saniteer($paginaTitel) ?> | Drone-Criticus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,700;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $basis ?>css/stijl.css">
    <link rel="stylesheet" href="<?= $basis ?>css/navigatie.css">
</head>
<body>

<header class="site-header">
    <div class="header-inhoud">
        <a href="<?= $basis ?>index.php" class="logo">
            <span class="logo-icoon">◈</span>
            <span class="logo-tekst">Drone<span class="logo-accent">Criticus</span></span>
        </a>

        <nav class="hoofd-nav" id="hoofdNav">
            <ul class="nav-lijst">
                <li><a href="<?= $basis ?>index.php" class="nav-link">Home</a></li>
                <li><a href="<?= $basis ?>php/drones.php" class="nav-link">Alle Drones</a></li>
                <li><a href="<?= $basis ?>php/review_toevoegen.php" class="nav-link">Review Schrijven</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="<?= $basis ?>admin/dashboard.php" class="nav-link nav-admin">Admin</a></li>
                <?php endif; ?>
                <?php if (isIngelogd()): ?>
                    <li>
                        <a href="<?= $basis ?>php/uitloggen.php" class="nav-link nav-logout">
                            Uitloggen (<?= saniteer($_SESSION['gebruikersnaam'] ?? '') ?>)
                        </a>
                    </li>
                <?php else: ?>
                    <li><a href="<?= $basis ?>php/login.php" class="nav-link nav-login">Inloggen</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <button class="hamburger" id="hamburger" aria-label="Menu openen">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<main class="hoofd-inhoud">
