<?php
// ============================================================
// Drone-Criticus — Database configuratie
// ============================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DB_HOST', 'ID498100_dronecriticus.db.webhosting.be');
define('DB_NAME', 'ID498100_dronecriticus');
define('DB_USER', 'ID498100_dronecriticus');
define('DB_PASS', 'Sarusan21');
define('DB_CHARSET', 'utf8mb4');

// Applicatie instellingen
define('SITE_NAAM', 'Drone-Criticus');
define('SITE_URL', 'http://localhost/drone-criticus');


function getDatabaseVerbinding(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $opties = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opties);
        } 
        catch (PDOException $e) {
            // Geen technische foutdetails tonen aan bezoeker
            die('<p class="fout">Databaseverbinding mislukt. Probeer later opnieuw.</p>');
        }
    }

    return $pdo;
}
