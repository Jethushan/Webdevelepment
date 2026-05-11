<?php
// ============================================================
// Drone-Criticus — Drone overzicht (drones.php)
// ============================================================

require_once '../includes/config.php';
require_once '../includes/sessie.php';

$paginaTitel = 'Alle Drones';
$pdo = getDatabaseVerbinding();

// Haal unieke merken op voor filter
$merken = $pdo->query('SELECT DISTINCT merk FROM drones ORDER BY merk')->fetchAll(PDO::FETCH_COLUMN);

// Haal alle drones op met gemiddelde score en aantal reviews
$stmt = $pdo->prepare('
    SELECT d.*,
           COALESCE(AVG(r.score), 0) AS gemiddelde,
           COUNT(r.id) AS aantal_reviews
    FROM drones d
    LEFT JOIN reviews r ON d.id = r.drone_id
    GROUP BY d.id
    ORDER BY d.merk, d.model
');
$stmt->execute();
$drones = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="pagina-kop container">
    <span class="sectie-label">Overzicht</span>
    <h1>Alle Drones</h1>
    <p style="color:var(--kleur-tekst-zacht); margin-top:0.5rem;">
        <?= count($drones) ?> modellen beschikbaar — zoek, filter en vergelijk.
    </p>
</div>

<section class="sectie container" style="padding-top:1rem;">

    <!-- Zoek & filter balk (JavaScript filtert live) -->
    <div class="zoek-filters">
        <input
            type="text"
            id="zoekInvoer"
            class="zoek-invoer"
            placeholder="Zoek op merk, model of beschrijving…"
            autocomplete="off"
        >

        <select id="filterMerk" class="filter-select">
            <option value="">Alle merken</option>
            <?php foreach ($merken as $merk): ?>
                <option value="<?= strtolower(saniteer($merk)) ?>">
                    <?= saniteer($merk) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="filterScore" class="filter-select">
            <option value="0">Alle scores</option>
            <option value="4">4+ sterren</option>
            <option value="5">5 sterren</option>
        </select>

        <span id="resultaatTeller" style="color:var(--kleur-tekst-extra);font-size:0.82rem;white-space:nowrap;">
            <?= count($drones) ?> drones gevonden
        </span>
    </div>

    <!-- Drone kaarten raster -->
    <div class="kaarten-raster">
        <?php foreach ($drones as $drone): ?>
            <?php
                $sterren  = round((float) $drone['gemiddelde']);
                $aantalRv = (int) $drone['aantal_reviews'];
            ?>
            <article
                class="kaart drone-kaart-item"
                data-merk="<?= strtolower(saniteer($drone['merk'])) ?>"
                data-score="<?= $sterren ?>"
            >
                <div class="kaart-afbeelding">🚁</div>
                <div class="kaart-inhoud">
                    <span class="kaart-merk"><?= saniteer($drone['merk']) ?></span>
                    <h2 class="kaart-model"><?= saniteer($drone['model']) ?></h2>

                    <?php if (!empty($drone['beschrijving'])): ?>
                        <p class="kaart-beschrijving">
                            <?= saniteer(substr($drone['beschrijving'], 0, 110)) ?>…
                        </p>
                    <?php endif; ?>

                    <div class="gemiddelde-score">
                        <span class="sterren">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="ster <?= $i <= $sterren ? 'actief' : '' ?>">★</span>
                            <?php endfor; ?>
                        </span>
                        <?php if ($aantalRv > 0): ?>
                            <span class="score-getal"><?= number_format((float) $drone['gemiddelde'], 1) ?></span>
                            <span>(<?= $aantalRv ?> review<?= $aantalRv !== 1 ? 's' : '' ?>)</span>
                        <?php else: ?>
                            <span style="color:var(--kleur-tekst-extra);">Nog geen reviews</span>
                        <?php endif; ?>
                    </div>

                    <div class="kaart-voettekst">
                        <?php if ($drone['prijs']): ?>
                            <span class="kaart-prijs">€<?= number_format($drone['prijs'], 2, ',', '.') ?></span>
                        <?php else: ?>
                            <span></span>
                        <?php endif; ?>
                        <a href="drone_detail.php?id=<?= (int) $drone['id'] ?>" class="knop knop-secundair">
                            Details →
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Geen resultaten bericht (verborgen bij aanvang) -->
    <div id="geenResultaten" class="geen-resultaten" style="display:none;">
        <span class="icoon">🔍</span>
        <h3>Geen drones gevonden</h3>
        <p>Pas je zoekterm of filters aan.</p>
    </div>

</section>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
