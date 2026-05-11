// ============================================================
// Drone-Criticus — Sterrenratingssysteem (sterren.js)
// Interactief sterren-invoerformulier + statische weergave
// ============================================================

(function () {
    'use strict';

    // --------------------------------------------------------
    // 1. Interactief sterren-invoerformulier
    //    Verwacht: <div class="ster-invoer" data-invoer="score">
    //              met 5 <span class="ster-invoer-ster" data-waarde="N">★</span>
    //              en een verborgen <input type="hidden" id="score">
    // --------------------------------------------------------
    function initialiseerSterInvoer() {
        var containers = document.querySelectorAll('.ster-invoer');

        containers.forEach(function (container) {
            var invoerNaam = container.getAttribute('data-invoer');
            var invoerVeld = document.getElementById(invoerNaam);
            var sterren    = container.querySelectorAll('.ster-invoer-ster');
            var geselecteerd = 0;

            if (!invoerVeld || sterren.length === 0) return;

            // Hover: markeer sterren tot en met de aangewezen ster
            sterren.forEach(function (ster, index) {
                ster.addEventListener('mouseenter', function () {
                    markeerSterren(sterren, index + 1, 'hover');
                });

                ster.addEventListener('mouseleave', function () {
                    markeerSterren(sterren, geselecteerd, 'geselecteerd');
                });

                // Klik: sla de keuze op in het verborgen invoerveld
                ster.addEventListener('click', function () {
                    geselecteerd = index + 1;
                    invoerVeld.value = geselecteerd;
                    markeerSterren(sterren, geselecteerd, 'geselecteerd');

                    // Kleine animatie: "pulse" op de geselecteerde ster
                    ster.classList.add('ster-pulse');
                    setTimeout(function () {
                        ster.classList.remove('ster-pulse');
                    }, 300);

                    // Update zichtbaar label
                    var label = container.querySelector('.ster-label');
                    if (label) {
                        var labels = ['', 'Slecht', 'Matig', 'Goed', 'Heel goed', 'Uitstekend'];
                        label.textContent = labels[geselecteerd] || '';
                    }
                });
            });
        });
    }

    // Hulpfunctie: klassen instellen op sterrenset
    function markeerSterren(sterren, aantalActief, modus) {
        sterren.forEach(function (ster, i) {
            ster.classList.remove('hover', 'geselecteerd');
            if (i < aantalActief) {
                ster.classList.add(modus);
            }
        });
    }

    // --------------------------------------------------------
    // 2. Zoekfunctie — live filter op drone-overzichtspagina
    //    Verwacht: #zoekInvoer en .drone-kaart-item elementen
    // --------------------------------------------------------
    function initialiseerZoekfunctie() {
        var zoekInvoer = document.getElementById('zoekInvoer');
        var filterMerk = document.getElementById('filterMerk');
        var filterScore = document.getElementById('filterScore');

        if (!zoekInvoer && !filterMerk && !filterScore) return;

        var kaarten = document.querySelectorAll('.drone-kaart-item');
        var teller  = document.getElementById('resultaatTeller');

        function filterKaarten() {
            var zoekterm   = zoekInvoer  ? zoekInvoer.value.toLowerCase().trim()  : '';
            var merkFilter = filterMerk  ? filterMerk.value.toLowerCase()          : '';
            var scoreFilter = filterScore ? parseInt(filterScore.value, 10)        : 0;

            var zichtbaar = 0;

            kaarten.forEach(function (kaart) {
                var tekst  = kaart.textContent.toLowerCase();
                var merk   = (kaart.getAttribute('data-merk')  || '').toLowerCase();
                var score  = parseInt(kaart.getAttribute('data-score') || '0', 10);

                var toonZoek  = !zoekterm   || tekst.includes(zoekterm);
                var toonMerk  = !merkFilter || merk === merkFilter;
                var toonScore = !scoreFilter || score >= scoreFilter;

                if (toonZoek && toonMerk && toonScore) {
                    kaart.style.display = '';
                    kaart.style.animation = 'fadeInUp 0.3s ease both';
                    zichtbaar++;
                } else {
                    kaart.style.display = 'none';
                }
            });

            if (teller) {
                teller.textContent = zichtbaar + ' drone' + (zichtbaar !== 1 ? 's' : '') + ' gevonden';
            }

            // Toon/verberg "geen resultaten"-bericht
            var geenResultaten = document.getElementById('geenResultaten');
            if (geenResultaten) {
                geenResultaten.style.display = zichtbaar === 0 ? 'block' : 'none';
            }
        }

        if (zoekInvoer)  zoekInvoer.addEventListener('input',  filterKaarten);
        if (filterMerk)  filterMerk.addEventListener('change', filterKaarten);
        if (filterScore) filterScore.addEventListener('change', filterKaarten);
    }

    // --------------------------------------------------------
    // 3. Formuliervalidatie — review formulier
    //    Geeft visuele feedback voor verplichte velden
    // --------------------------------------------------------
    function initialiseerFormulierValidatie() {
        var formulier = document.getElementById('reviewFormulier');
        if (!formulier) return;

        formulier.addEventListener('submit', function (e) {
            var score = document.getElementById('score');
            var foutmelding = document.getElementById('scoreFout');

            if (score && (!score.value || score.value === '0')) {
                e.preventDefault();
                if (foutmelding) {
                    foutmelding.style.display = 'block';
                    foutmelding.textContent   = 'Kies een score van 1 tot 5 sterren.';
                }
                // Scroll naar sterren-invoer
                var sterInvoer = document.querySelector('.ster-invoer');
                if (sterInvoer) {
                    sterInvoer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    // --------------------------------------------------------
    // 4. Meldingen automatisch verbergen na 4 seconden
    // --------------------------------------------------------
    function initialiseerMeldingen() {
        var meldingen = document.querySelectorAll('.melding');
        meldingen.forEach(function (melding) {
            setTimeout(function () {
                melding.style.transition = 'opacity 0.5s ease';
                melding.style.opacity    = '0';
                setTimeout(function () {
                    melding.style.display = 'none';
                }, 500);
            }, 4000);
        });
    }

    // --------------------------------------------------------
    // Initialiseer alles na het laden van de DOM
    // --------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        initialiseerSterInvoer();
        initialiseerZoekfunctie();
        initialiseerFormulierValidatie();
        initialiseerMeldingen();
    });

})();
