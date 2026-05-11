<?php
// ============================================================
// Drone-Criticus — Hoofdpagina (index.php)
// ============================================================

require_once 'includes/config.php';
require_once 'includes/sessie.php';

$paginaTitel = 'Home';

// Haal statistieken op
$pdo = getDatabaseVerbinding();

$aantalDrones  = (int) $pdo->query('SELECT COUNT(*) FROM drones')->fetchColumn();
$aantalReviews = (int) $pdo->query('SELECT COUNT(*) FROM reviews')->fetchColumn();
$gemiddeldScore = (float) $pdo->query('SELECT COALESCE(AVG(score), 0) FROM reviews')->fetchColumn();

// Laatste 3 reviews met drone-info
$stmt = $pdo->prepare('
    SELECT r.*, d.merk, d.model
    FROM reviews r
    JOIN drones d ON r.drone_id = d.id
    ORDER BY r.aangemaakt_op DESC
    LIMIT 3
');
$stmt->execute();
$recenteReviews = $stmt->fetchAll();

// Top 3 hoogst beoordeelde drones
$stmt = $pdo->prepare('
    SELECT d.id, d.merk, d.model, d.prijs,
           COALESCE(AVG(r.score), 0) AS gemiddelde,
           COUNT(r.id) AS aantal_reviews
    FROM drones d
    LEFT JOIN reviews r ON d.id = r.drone_id
    GROUP BY d.id
    ORDER BY gemiddelde DESC, aantal_reviews DESC
    LIMIT 3
');
$stmt->execute();
$topDrones = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<!-- ── HERO ─────────────────────────────────────────────── -->
<section class="hero">
    <span class="hero-label">Drone Reviews &amp; Kritiek</span>
    <h1 class="hero-titel">
        Vlieg slimmer.<br>
        Kies <span class="accent">beter.</span>
    </h1>
    <p class="hero-ondertitel">
        Eerlijke ervaringen van echte drone-enthousiastelingen.
        Geen gesponsorde meningen. Alleen de waarheid.
    </p>
    <div class="knop-groep">
        <a href="php/drones.php" class="knop knop-primair">Bekijk alle drones</a>
        <a href="php/review_toevoegen.php" class="knop knop-secundair">Schrijf een review</a>
    </div>
</section>

<!-- ── STATISTIEKEN ──────────────────────────────────────── -->
<section class="sectie container">
    <div class="stats-rij">
        <div class="stat-badge fade-in">
            <span class="stat-getal"><?= $aantalDrones ?></span>
            <span class="stat-label">Drone modellen</span>
        </div>
        <div class="stat-badge fade-in">
            <span class="stat-getal"><?= $aantalReviews ?></span>
            <span class="stat-label">Gepubliceerde reviews</span>
        </div>
        <div class="stat-badge fade-in">
            <span class="stat-getal"><?= number_format($gemiddeldScore, 1) ?></span>
            <span class="stat-label">Gemiddelde score</span>
        </div>
    </div>
</section>

<!-- ── TOP DRONES ────────────────────────────────────────── -->
<section class="sectie container">
    <div class="sectie-kop">
        <span class="sectie-label">Topdrones</span>
        <h2>Hoogst beoordeeld</h2>
    </div>

    <div class="kaarten-raster">
        <?php foreach ($topDrones as $drone): ?>
            <?php
                $sterren  = round($drone['gemiddelde']);
                $aantalRv = (int) $drone['aantal_reviews'];
            ?>
            <article class="kaart fade-in">
                <div class="kaart-afbeelding">🚁</div>
                <div class="kaart-inhoud">
                    <span class="kaart-merk"><?= saniteer($drone['merk']) ?></span>
                    <h3 class="kaart-model"><?= saniteer($drone['model']) ?></h3>
                    <div class="gemiddelde-score">
                        <span class="sterren">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="ster <?= $i <= $sterren ? 'actief' : '' ?>">★</span>
                            <?php endfor; ?>
                        </span>
                        <span class="score-getal"><?= number_format($drone['gemiddelde'], 1) ?></span>
                        <span>(<?= $aantalRv ?> review<?= $aantalRv !== 1 ? 's' : '' ?>)</span>
                    </div>
                    <div class="kaart-voettekst">
                        <span class="kaart-prijs">€<?= number_format($drone['prijs'], 2, ',', '.') ?></span>
                        <a href="php/drone_detail.php?id=<?= $drone['id'] ?>" class="knop knop-secundair">
                            Bekijken →
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div style="text-align:center; margin-top:2rem;">
        <a href="php/drones.php" class="knop knop-secundair">Alle drones bekijken →</a>
    </div>
</section>

<!-- ── RECENTE REVIEWS ───────────────────────────────────── -->
<?php if (!empty($recenteReviews)): ?>
<section class="sectie container">
    <div class="sectie-kop">
        <span class="sectie-label">Vers van de community</span>
        <h2>Recente reviews</h2>
    </div>

    <?php foreach ($recenteReviews as $review): ?>
        <article class="review-kaart fade-in">
            <div class="review-hoofd">
                <div>
                    <span class="review-naam"><?= saniteer($review['naam']) ?></span>
                    <span style="color:var(--kleur-tekst-extra); font-size:0.8rem; margin-left:0.5rem;">
                        over <?= saniteer($review['merk']) ?> <?= saniteer($review['model']) ?>
                    </span>
                </div>
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="sterren">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="ster <?= $i <= $review['score'] ? 'actief' : '' ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <span class="review-datum">
                        <?= date('d M Y', strtotime($review['aangemaakt_op'])) ?>
                    </span>
                </div>
            </div>
            <h4 class="review-titel"><?= saniteer($review['titel']) ?></h4>
            <p class="review-inhoud"><?= saniteer(substr($review['inhoud'], 0, 200)) ?>...</p>
            <a href="php/drone_detail.php?id=<?= $review['drone_id'] ?>" style="font-size:0.82rem;">
                Lees meer →
            </a>
        </article>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<!-- ── CTA ───────────────────────────────────────────────── -->
<section class="sectie" style="text-align:center; padding-bottom:5rem;">
    <div class="container">
        <div style="
            background: linear-gradient(135deg, rgba(232,255,58,0.08), rgba(232,255,58,0.03));
            border: 1px solid rgba(232,255,58,0.2);
            border-radius: var(--radius-groot);
            padding: 4rem 2rem;
        ">
            <span class="sectie-label">Jouw mening telt</span>
            <h2 style="margin-bottom:1rem;">Heb je een drone getest?</h2>
            <p style="color:var(--kleur-tekst-zacht); max-width:480px; margin:0 auto 2rem;">
                Deel jouw eerlijke ervaringen met de community en help anderen
                de juiste keuze te maken.
            </p>
            <a href="php/review_toevoegen.php" class="knop knop-primair">Review schrijven →</a>
        </div>
    </div>
</section>

<!-- jQuery CDN (voor navigatie.js) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php require_once 'includes/footer.php'; ?>
