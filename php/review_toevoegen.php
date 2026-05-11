<?php
// ============================================================
// Drone-Criticus — Review toevoegen (review_toevoegen.php)
// PRG-patroon: verwerk POST, redirect daarna met resultaat
// ============================================================

require_once '../includes/config.php';
require_once '../includes/sessie.php';

$pdo = getDatabaseVerbinding();
$paginaTitel = 'Review Schrijven';

// Haal alle drones op voor het keuzemenu
$drones = $pdo->query('SELECT id, merk, model FROM drones ORDER BY merk, model')->fetchAll();

// Vooraf geselecteerde drone via GET-parameter
$voorafDroneId = filter_input(INPUT_GET, 'drone_id', FILTER_VALIDATE_INT) ?: 0;

$fouten  = [];
$succes  = false;
$invoer  = [
    'drone_id' => $voorafDroneId,
    'naam'     => '',
    'email'    => '',
    'titel'    => '',
    'inhoud'   => '',
    'score'    => 0,
];

// ── POST-verwerking ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') 

    {

        // Invoer ophalen en saniteren
        $invoer['drone_id'] = filter_input(INPUT_POST, 'drone_id', FILTER_VALIDATE_INT) ?: 0;
        $invoer['naam']     = trim($_POST['naam']    ?? '');
        $invoer['email']    = trim($_POST['email']   ?? '');
        $invoer['titel']    = trim($_POST['titel']   ?? '');
        $invoer['inhoud']   = trim($_POST['inhoud']  ?? '');
        $invoer['score']    = filter_input(INPUT_POST, 'score', FILTER_VALIDATE_INT) ?: 0;

        // Validatie
        if (!$invoer['drone_id']) {
            $fouten[] = 'Kies een drone model.';
        }
        if (mb_strlen($invoer['naam']) < 2 || mb_strlen($invoer['naam']) > 100) {
            $fouten[] = 'Naam moet tussen 2 en 100 tekens zijn.';
        }
        if (!filter_var($invoer['email'], FILTER_VALIDATE_EMAIL)) {
            $fouten[] = 'Voer een geldig e-mailadres in.';
        }
        if (mb_strlen($invoer['titel']) < 5 || mb_strlen($invoer['titel']) > 200) {
            $fouten[] = 'Titel moet tussen 5 en 200 tekens zijn.';
        }
        if (mb_strlen($invoer['inhoud']) < 20) {
            $fouten[] = 'Review moet minimaal 20 tekens zijn.';
        }
        if ($invoer['score'] < 1 || $invoer['score'] > 5) {
            $fouten[] = 'Kies een score van 1 tot 5 sterren.';
        }

        // Controleer of drone_id bestaat
        if (!$fouten) {
            $check = $pdo->prepare('SELECT id FROM drones WHERE id = ?');
            $check->execute([$invoer['drone_id']]);
            if (!$check->fetch()) {
                $fouten[] = 'Geselecteerde drone bestaat niet.';
            }
        }

        // Opslaan in database (prepared statement = veilig tegen SQL-injection)
        if (!$fouten) {
            $stmt = $pdo->prepare('
                INSERT INTO reviews (drone_id, naam, email, titel, inhoud, score)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $invoer['drone_id'],
                $invoer['naam'],
                $invoer['email'],
                $invoer['titel'],
                $invoer['inhoud'],
                $invoer['score'],
            ]);

            // PRG: redirect om dubbele verzending te voorkomen
        header('Location: drone_detail.php?id=' . $invoer['drone_id'] . '&succes=1');
        exit;
    }
}

// Controleer succes na redirect
if (isset($_GET['succes']) && $_GET['succes'] === '1') {
    $succes = true;
}

require_once '../includes/header.php';
?>

<div class="pagina-kop container">
    <span class="sectie-label">Community</span>
    <h1>Review Schrijven</h1>
    <p style="color:var(--kleur-tekst-zacht);margin-top:0.5rem;">
        Deel jouw eerlijke ervaring met de community.
    </p>
</div>

<section class="sectie container" style="padding-top:1rem; padding-bottom:4rem;">

    <?php if ($succes): ?>
        <div class="melding melding-succes">
            ✓ Je review is succesvol gepubliceerd. Bedankt!
        </div>
    <?php endif; ?>

    <?php if (!empty($fouten)): ?>
        <div class="melding melding-fout">
            <strong>Corrigeer de volgende fouten:</strong>
            <ul style="margin-top:0.5rem;padding-left:1.2rem;">
                <?php foreach ($fouten as $fout): ?>
                    <li><?= saniteer($fout) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form
        id="reviewFormulier"
        method="POST"
        action="review_toevoegen.php"
        novalidate
    >


        <div class="formulier-kaart">

            <!-- Drone kiezen -->
            <div class="formulier-groep">
                <label for="drone_id">Drone model *</label>
                <select name="drone_id" id="drone_id" required>
                    <option value="">— Kies een drone —</option>
                    <?php foreach ($drones as $d): ?>
                        <option
                            value="<?= (int) $d['id'] ?>"
                            <?= ($invoer['drone_id'] == $d['id']) ? 'selected' : '' ?>
                        >
                            <?= saniteer($d['merk']) ?> <?= saniteer($d['model']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Naam -->
            <div class="formulier-groep">
                <label for="naam">Jouw naam *</label>
                <input
                    type="text"
                    name="naam"
                    id="naam"
                    value="<?= saniteer($invoer['naam']) ?>"
                    placeholder="Voornaam Achternaam"
                    maxlength="100"
                    required
                >
            </div>

            <!-- E-mail -->
            <div class="formulier-groep">
                <label for="email">E-mailadres * <span style="color:var(--kleur-tekst-extra);font-size:0.75em;">(niet openbaar)</span></label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="<?= saniteer($invoer['email']) ?>"
                    placeholder="jij@voorbeeld.be"
                    maxlength="150"
                    required
                >
            </div>

            <!-- Titel -->
            <div class="formulier-groep">
                <label for="titel">Titel van je review *</label>
                <input
                    type="text"
                    name="titel"
                    id="titel"
                    value="<?= saniteer($invoer['titel']) ?>"
                    placeholder="Geef je review een pakkende titel"
                    maxlength="200"
                    required
                >
            </div>

            <!-- Review tekst -->
            <div class="formulier-groep">
                <label for="inhoud">Jouw review * <span style="color:var(--kleur-tekst-extra);font-size:0.75em;">(min. 20 tekens)</span></label>
                <textarea
                    name="inhoud"
                    id="inhoud"
                    placeholder="Beschrijf jouw ervaringen. Wat vond je goed? Wat kon beter? Zou je het aanraden?"
                    maxlength="5000"
                    required
                ><?= saniteer($invoer['inhoud']) ?></textarea>
            </div>

            <!-- Sterrenrating (interactief via JS) -->
            <div class="formulier-groep">
                <label>Score * </label>
                <input type="hidden" name="score" id="score" value="<?= (int) $invoer['score'] ?>">

                <div class="ster-invoer" data-invoer="score" style="margin-bottom:0.5rem;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span
                            class="ster-invoer-ster <?= $i <= $invoer['score'] ? 'geselecteerd' : '' ?>"
                            data-waarde="<?= $i ?>"
                            title="<?= $i ?> ster<?= $i > 1 ? 'ren' : '' ?>"
                        >★</span>
                    <?php endfor; ?>
                    <span class="ster-label" style="
                        margin-left:0.75rem;
                        font-size:0.85rem;
                        color:var(--kleur-tekst-zacht);
                        min-width:80px;
                        display:inline-block;
                    "></span>
                </div>

                <div id="scoreFout" class="melding melding-fout" style="display:none;margin-top:0.5rem;"></div>
            </div>

            <!-- Verzenden -->
            <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
                <a href="drones.php" class="knop knop-secundair">Annuleren</a>
                <button type="submit" class="knop knop-primair">Review publiceren →</button>
            </div>

        </div>
    </form>

</section>

<!-- Inlinestijlen voor interactieve sterren -->
<style>
.ster-invoer { display: inline-flex; align-items: center; }

.ster-invoer-ster {
    font-size: 2rem;
    color: var(--kleur-tekst-extra);
    cursor: pointer;
    transition: color 0.15s ease, transform 0.15s ease;
    padding: 0 2px;
    user-select: none;
}

.ster-invoer-ster.hover,
.ster-invoer-ster.geselecteerd {
    color: var(--kleur-ster);
}

.ster-invoer-ster:hover {
    transform: scale(1.2);
}

@keyframes ster-pulse {
    0%   { transform: scale(1); }
    50%  { transform: scale(1.4); }
    100% { transform: scale(1); }
}

.ster-pulse {
    animation: ster-pulse 0.3s ease;
}
</style>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
