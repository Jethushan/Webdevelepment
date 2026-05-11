<?php
// ============================================================
// Drone-Criticus — Drone toevoegen (drone_toevoegen.php)
// Enkel admins
// ============================================================

require_once '../includes/config.php';
require_once '../includes/sessie.php';

vereisAdmin();

$pdo = getDatabaseVerbinding();
$paginaTitel = 'Drone Toevoegen';

$fouten = [];
$invoer = [
    'merk'        => '',
    'model'       => '',
    'beschrijving'=> '',
    'prijs'       => '',
];

// ── POST-verwerking ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    {
        $invoer['merk']         = trim($_POST['merk']         ?? '');
        $invoer['model']        = trim($_POST['model']        ?? '');
        $invoer['beschrijving'] = trim($_POST['beschrijving'] ?? '');
        $invoer['prijs']        = trim($_POST['prijs']        ?? '');

        // Validatie
        if (mb_strlen($invoer['merk']) < 2)  $fouten[] = 'Merk is verplicht (min. 2 tekens).';
        if (mb_strlen($invoer['model']) < 2) $fouten[] = 'Model is verplicht (min. 2 tekens).';

        $prijs = null;
        if ($invoer['prijs'] !== '') {
            $prijs = filter_var($invoer['prijs'], FILTER_VALIDATE_FLOAT);
            if ($prijs === false || $prijs < 0) {
                $fouten[] = 'Ongeldige prijs.';
                $prijs = null;
            }
        }

        if (!$fouten) {
            $stmt = $pdo->prepare('
                INSERT INTO drones (merk, model, beschrijving, prijs)
                VALUES (?, ?, ?, ?)
            ');
            $stmt->execute([
                $invoer['merk'],
                $invoer['model'],
                $invoer['beschrijving'] ?: null,
                $prijs,
            ]);

            header('Location: dashboard.php?drone_toegevoegd=1');
            exit;
        }
    }
}

require_once '../includes/header.php';
?>

<div class="pagina-kop container">
    <a href="dashboard.php" style="color:var(--kleur-tekst-zacht); font-size:0.85rem;">← Terug naar dashboard</a>
    <h1 style="margin-top:0.5rem;">Drone Toevoegen</h1>
</div>

<section class="sectie container" style="padding-top:1rem; padding-bottom:4rem;">

    <?php if (!empty($fouten)): ?>
        <div class="melding melding-fout" style="max-width:640px;margin:0 auto 1.5rem;">
            <strong>Corrigeer de volgende fouten:</strong>
            <ul style="margin-top:0.5rem;padding-left:1.2rem;">
                <?php foreach ($fouten as $fout): ?>
                    <li><?= saniteer($fout) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="drone_toevoegen.php" class="formulier-kaart">


        <div class="formulier-groep">
            <label for="merk">Merk *</label>
            <input type="text" name="merk" id="merk" value="<?= saniteer($invoer['merk']) ?>" required maxlength="100">
        </div>

        <div class="formulier-groep">
            <label for="model">Model *</label>
            <input type="text" name="model" id="model" value="<?= saniteer($invoer['model']) ?>" required maxlength="150">
        </div>

        <div class="formulier-groep">
            <label for="beschrijving">Beschrijving</label>
            <textarea name="beschrijving" id="beschrijving" placeholder="Korte beschrijving van dit drone model…"><?= saniteer($invoer['beschrijving']) ?></textarea>
        </div>

        <div class="formulier-groep">
            <label for="prijs">Adviesprijs (€)</label>
            <input type="number" name="prijs" id="prijs" value="<?= saniteer($invoer['prijs']) ?>" step="0.01" min="0" placeholder="bv. 759.00">
        </div>

        <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
            <a href="dashboard.php" class="knop knop-secundair">Annuleren</a>
            <button type="submit" class="knop knop-primair">Drone toevoegen →</button>
        </div>
    </form>

</section>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
