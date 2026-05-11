<?php
// ============================================================
// Drone-Criticus — Drone detailpagina (drone_detail.php)
// ============================================================

require_once '../includes/config.php';
require_once '../includes/sessie.php';

$pdo = getDatabaseVerbinding();
$droneId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$droneId) {
    header('Location: drones.php');
    exit;
}

// Haal drone op
$stmt = $pdo->prepare('
    SELECT d.*,
           COALESCE(AVG(r.score), 0) AS gemiddelde,
           COUNT(r.id) AS aantal_reviews
    FROM drones d
    LEFT JOIN reviews r ON d.id = r.drone_id
    WHERE d.id = ?
    GROUP BY d.id
');
$stmt->execute([$droneId]);
$drone = $stmt->fetch();

if (!$drone) {
    header('Location: drones.php');
    exit;
}

// Haal reviews op voor deze drone
$stmt = $pdo->prepare('
    SELECT * FROM reviews
    WHERE drone_id = ?
    ORDER BY aangemaakt_op DESC
');
$stmt->execute([$droneId]);
$reviews = $stmt->fetchAll();

$paginaTitel = saniteer($drone['merk']) . ' ' . saniteer($drone['model']);
$gemiddelde  = (float) $drone['gemiddelde'];
$sterren     = round($gemiddelde);

// Verwijdersucces- of foutmelding
$melding = '';
if (isset($_GET['verwijderd']) && $_GET['verwijderd'] === '1') {
    $melding = '<div class="melding melding-succes">Review succesvol verwijderd.</div>';
}

require_once '../includes/header.php';
?>

<div class="pagina-kop container">
    <a href="drones.php" style="color:var(--kleur-tekst-zacht); font-size:0.85rem;">← Terug naar overzicht</a>
</div>

<section class="sectie container" style="padding-top:1rem;">

    <!-- Drone info kaart -->
    <div style="
        display:grid;
        grid-template-columns: 280px 1fr;
        gap:2rem;
        background:var(--kleur-kaart);
        border:1px solid var(--kleur-rand);
        border-radius:var(--radius-groot);
        overflow:hidden;
        margin-bottom:3rem;
        box-shadow:var(--schaduw-kaart);
    ">
        <!-- Afbeelding -->
        <div class="kaart-afbeelding" style="height:100%;border-radius:0;font-size:5rem;">🚁</div>

        <!-- Info -->
        <div style="padding:2rem 2rem 2rem 0;">
            <span class="kaart-merk"><?= saniteer($drone['merk']) ?></span>
            <h1 style="font-size:clamp(2rem,4vw,3.5rem);margin-bottom:1rem;">
                <?= saniteer($drone['model']) ?>
            </h1>

            <!-- Sterren & score -->
            <div class="gemiddelde-score" style="font-size:1rem; margin-bottom:1.5rem;">
                <span class="sterren" style="font-size:1.4rem;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="ster <?= $i <= $sterren ? 'actief' : '' ?>">★</span>
                    <?php endfor; ?>
                </span>
                <?php if ($drone['aantal_reviews'] > 0): ?>
                    <span class="score-getal" style="font-size:1.2rem;">
                        <?= number_format($gemiddelde, 1) ?>
                    </span>
                    <span>(<?= (int) $drone['aantal_reviews'] ?> reviews)</span>
                <?php else: ?>
                    <span style="color:var(--kleur-tekst-extra);">Nog geen reviews</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($drone['beschrijving'])): ?>
                <p style="color:var(--kleur-tekst-zacht); max-width:480px; margin-bottom:1.5rem;">
                    <?= saniteer($drone['beschrijving']) ?>
                </p>
            <?php endif; ?>

            <div style="display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;">
                <?php if ($drone['prijs']): ?>
                    <span style="font-family:var(--lettertype-display);font-size:2rem;color:var(--kleur-accent);">
                        €<?= number_format($drone['prijs'], 2, ',', '.') ?>
                    </span>
                <?php endif; ?>
                <a href="review_toevoegen.php?drone_id=<?= $droneId ?>" class="knop knop-primair">
                    Review schrijven
                </a>
            </div>
        </div>
    </div>

    <!-- Reviews -->
    <div>
        <h2 style="margin-bottom:1.5rem;">
            Reviews
            <?php if (!empty($reviews)): ?>
                <span style="color:var(--kleur-tekst-extra);font-size:1rem;font-family:var(--lettertype-tekst);font-weight:400;">
                    (<?= count($reviews) ?>)
                </span>
            <?php endif; ?>
        </h2>

        <?= $melding ?>

        <?php if (empty($reviews)): ?>
            <div class="geen-resultaten">
                <span class="icoon">📝</span>
                <h3>Nog geen reviews</h3>
                <p>Wees de eerste die dit model beoordeelt!</p>
                <a href="review_toevoegen.php?drone_id=<?= $droneId ?>" class="knop knop-primair" style="margin-top:1rem;">
                    Review schrijven
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <article class="review-kaart fade-in">
                    <div class="review-hoofd">
                        <div>
                            <span class="review-naam"><?= saniteer($review['naam']) ?></span>
                            <?php if (isAdmin()): ?>
                                <span style="color:var(--kleur-tekst-extra);font-size:0.78rem;margin-left:0.5rem;">
                                    &lt;<?= saniteer($review['email']) ?>&gt;
                                </span>
                            <?php endif; ?>
                        </div>
                        <div style="display:flex;align-items:center;gap:1rem;">
                            <div class="sterren">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="ster <?= $i <= $review['score'] ? 'actief' : '' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <span class="review-datum">
                                <?= date('d M Y', strtotime($review['aangemaakt_op'])) ?>
                            </span>
                            <?php if (isAdmin()): ?>
                                <a
                                    href="../admin/review_verwijderen.php?id=<?= (int) $review['id'] ?>&drone_id=<?= $droneId ?>"
                                    class="knop knop-gevaar"
                                    onclick="return confirm('Review van <?= saniteer($review['naam']) ?> verwijderen?');"
                                >
                                    Verwijderen
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h4 class="review-titel"><?= saniteer($review['titel']) ?></h4>
                    <p class="review-inhoud"><?= nl2br(saniteer($review['inhoud'])) ?></p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</section>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
