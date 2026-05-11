<?php
// ============================================================
// Drone-Criticus — Inlogpagina (login.php)
// PRG-patroon met sessiebeheer
// ============================================================

require_once '../includes/config.php';
require_once '../includes/sessie.php';

// Al ingelogd? Stuur door naar de hoofdpagina
if (isIngelogd()) {
    header('Location: ../index.php');
    exit;
}

$paginaTitel = 'Inloggen';
$pdo = getDatabaseVerbinding();

$fouten      = [];
$gebruiksnaam = '';

// ── POST-verwerking ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    {
        $gebruiksnaam = trim($_POST['gebruikersnaam'] ?? '');
        $wachtwoord   = $_POST['wachtwoord'] ?? '';

        if (empty($gebruiksnaam) || empty($wachtwoord)) {
            $fouten[] = 'Vul beide velden in.';
        } else {
            // Haal gebruiker op via prepared statement
            $stmt = $pdo->prepare('SELECT * FROM gebruikers WHERE gebruikersnaam = ? LIMIT 1');
            $stmt->execute([$gebruiksnaam]);
            $gebruiker = $stmt->fetch();

            // Verifieer wachtwoord met password_verify (bcrypt)
            if (!$gebruiker || !password_verify($wachtwoord, $gebruiker['wachtwoord_hash'])) {
                // Generieke foutmelding (geen info over wat fout is)
                $fouten[] = 'Ongeldig gebruikersnaam of wachtwoord.';
                // Kleine vertraging tegen brute-force
                sleep(1);
            } else {
                // Login gelukt: sessie aanmaken
                session_regenerate_id(true); // Sessiefixatie voorkomen
                $_SESSION['gebruiker_id']   = $gebruiker['id'];
                $_SESSION['gebruikersnaam'] = $gebruiker['gebruikersnaam'];
                $_SESSION['rol']            = $gebruiker['rol'];

                // Redirect op basis van rol
                if ($gebruiker['rol'] === 'admin') {
                    header('Location: ../admin/dashboard.php');
                } else {
                    header('Location: ../index.php');
                }
                exit;
            }
        }
    }
}

require_once '../includes/header.php';
?>

<div class="pagina-kop container" style="text-align:center;">
    <span class="sectie-label">Beheerderstoegang</span>
    <h1>Inloggen</h1>
    <p style="color:var(--kleur-tekst-zacht); margin-top:0.5rem;">
        Enkel voor beheerders van Drone-Criticus.
    </p>
</div>

<section class="sectie container" style="padding-top:1rem; padding-bottom:4rem;">

    <?php if (!empty($fouten)): ?>
        <div class="melding melding-fout" style="max-width:640px; margin:0 auto 1.5rem;">
            <?= saniteer($fouten[0]) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="formulier-kaart">


        <div class="formulier-groep">
            <label for="gebruikersnaam">Gebruikersnaam</label>
            <input
                type="text"
                name="gebruikersnaam"
                id="gebruikersnaam"
                value="<?= saniteer($gebruiksnaam) ?>"
                autocomplete="username"
                required
                autofocus
            >
        </div>

        <div class="formulier-groep">
            <label for="wachtwoord">Wachtwoord</label>
            <input
                type="password"
                name="wachtwoord"
                id="wachtwoord"
                autocomplete="current-password"
                required
            >
        </div>

        <button type="submit" class="knop knop-primair" style="width:100%; justify-content:center; margin-top:0.5rem;">
            Inloggen →
        </button>

        <p style="text-align:center; margin-top:1.25rem; font-size:0.82rem; color:var(--kleur-tekst-extra);">
            Enkel toegankelijk voor beheerders.
        </p>
    </form>

</section>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
