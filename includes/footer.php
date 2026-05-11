<?php
// ============================================================
// Drone-Criticus — Footer
// ============================================================

// Basispad relatief aan de huidige pagina berekenen
$scriptPad  = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
$rootPad    = str_replace('\\', '/', realpath(__DIR__ . '/..')) . '/';
$relatief   = str_replace($rootPad, '', $scriptPad);
$diepte     = substr_count($relatief, '/');
$basis      = str_repeat('../', $diepte);
?>
</main>

<footer class="site-footer">
    <div class="footer-inhoud">
        <div class="footer-kolom">
            <p class="footer-logo">◈ DroneCriticus</p>
            <p class="footer-slogan">Eerlijke reviews van drone-enthousiastelingen.</p>
        </div>
        <div class="footer-kolom">
            <h4>Navigatie</h4>
            <ul>
                <li><a href="<?= $basis ?>index.php">Home</a></li>
                <li><a href="<?= $basis ?>php/drones.php">Alle Drones</a></li>
                <li><a href="<?= $basis ?>php/review_toevoegen.php">Review Schrijven</a></li>
            </ul>
        </div>
        <div class="footer-kolom">
            <h4>Merken</h4>
            <ul>
                <li><a href="<?= $basis ?>php/drones.php?merk=DJI">DJI</a></li>
                <li><a href="<?= $basis ?>php/drones.php?merk=Autel">Autel</a></li>
                <li><a href="<?= $basis ?>php/drones.php?merk=Parrot">Parrot</a></li>
            </ul>
        </div>
        <div class="footer-kolom">
            <p class="footer-copyright">
                &copy; <?= date('Y') ?> Drone-Criticus<br>
                Jethushan Jeyaranjan<br>
                VIVES Elektronica-ICT
            </p>
        </div>
    </div>
</footer>

<script src="<?= $basis ?>js/navigatie.js"></script>
<script src="<?= $basis ?>js/sterren.js"></script>
</body>
</html>
