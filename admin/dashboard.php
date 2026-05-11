<?php
// ============================================================
// Drone-Criticus — Admin dashboard (dashboard.php)
// ============================================================

require_once '../includes/config.php';
require_once '../includes/sessie.php';

vereisAdmin(); // Stuurt niet-admins door

$paginaTitel = 'Admin Dashboard';
$pdo = getDatabaseVerbinding();

// Statistieken
$aantalDrones  = (int) $pdo->query('SELECT COUNT(*) FROM drones')->fetchColumn();
$aantalReviews = (int) $pdo->query('SELECT COUNT(*) FROM reviews')->fetchColumn();
$aantalGebruikers = (int) $pdo->query('SELECT COUNT(*) FROM gebruikers')->fetchColumn();

// Recente reviews (voor beheer)
$stmt = $pdo->prepare('
    SELECT r.*, d.merk, d.model
    FROM reviews r
    JOIN drones d ON r.drone_id = d.id
    ORDER BY r.aangemaakt_op DESC
    LIMIT 10
');
$stmt->execute();
$reviews = $stmt->fetchAll();

// Succes-/foutmelding na actie
$melding = '';
if (isset($_GET['verwijderd'])) {
    $melding = '<div class="melding melding-succes">Review succesvol verwijderd.</div>';
}
if (isset($_GET['fout'])) {
    $melding = '<div class="melding melding-fout">Er is een fout opgetreden.</div>';
}

require_once '../includes/header.php';
?>

<div class="pagina-kop container">
    <span class="sectie-label">Beheerder</span>
    <h1>Admin Dashboard</h1>
    <p style="color:var(--kleur-tekst-zacht);margin-top:0.5rem;">
        Welkom, <strong><?= saniteer($_SESSION['gebruikersnaam']) ?></strong>.
        Hier beheer je de volledige site.
    </p>
</div>

<section class="sectie container" style="padding-top:1rem;">

    <!-- Statistieken -->
    <div class="stats-rij" style="margin-bottom:2.5rem;">
        <div class="stat-badge">
            <span class="stat-getal"><?= $aantalDrones ?></span>
            <span class="stat-label">Drone modellen</span>
        </div>
        <div class="stat-badge">
            <span class="stat-getal"><?= $aantalReviews ?></span>
            <span class="stat-label">Reviews</span>
        </div>
        <div class="stat-badge">
            <span class="stat-getal"><?= $aantalGebruikers ?></span>
            <span class="stat-label">Gebruikers</span>
        </div>
    </div>

    <!-- Snelle acties -->
    <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:2.5rem;">
        <a href="drone_toevoegen.php" class="knop knop-primair">+ Drone toevoegen</a>
        <a href="../php/drones.php" class="knop knop-secundair">Bekijk site →</a>
    </div>

    <?= $melding ?>

    <!-- Reviews tabel -->
    <h2 style="margin-bottom:1.25rem;">Recente reviews</h2>

    <?php if (empty($reviews)): ?>
        <div class="geen-resultaten">
            <span class="icoon">📝</span>
            <h3>Nog geen reviews</h3>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto; background:var(--kleur-kaart); border:1px solid var(--kleur-rand); border-radius:var(--radius-midden);">
            <table class="admin-tabel">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Drone</th>
                        <th>Naam</th>
                        <th>Titel</th>
                        <th>Score</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td style="white-space:nowrap;">
                                <?= date('d/m/Y', strtotime($review['aangemaakt_op'])) ?>
                            </td>
                            <td>
                                <a href="../php/drone_detail.php?id=<?= $review['drone_id'] ?>">
                                    <?= saniteer($review['merk']) ?> <?= saniteer($review['model']) ?>
                                </a>
                            </td>
                            <td><?= saniteer($review['naam']) ?></td>
                            <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <?= saniteer($review['titel']) ?>
                            </td>
                            <td>
                                <span class="badge badge-score"><?= (int) $review['score'] ?>/5</span>
                            </td>
                            <td>
                                <a
                                    href="review_verwijderen.php?id=<?= (int) $review['id'] ?>&terug=dashboard"
                                    class="knop knop-gevaar"
                                    onclick="return confirm('Review van <?= saniteer($review['naam']) ?> verwijderen?');"
                                >
                                    Verwijderen
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</section>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
