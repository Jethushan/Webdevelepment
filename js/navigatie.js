// ============================================================
// Drone-Criticus — Navigatie (navigatie.js)
// jQuery wordt geladen via CDN in de pagina's die het nodig hebben.
// Dit script vereist jQuery.
// ============================================================

$(function () {
    const $header    = $('.site-header');
    const $hamburger = $('#hamburger');
    const $nav       = $('#hoofdNav');

    // --- Scrolleffect: header krijgt klasse "scrolled" ---
    $(window).on('scroll.header', function () {
        if ($(this).scrollTop() > 30) {
            $header.addClass('scrolled');
        } else {
            $header.removeClass('scrolled');
        }
    });

    // --- Hamburger: mobiel menu togglen ---
    $hamburger.on('click', function () {
        const isOpen = $nav.hasClass('open');

        if (isOpen) {
            // Sluit menu met fade-out
            $nav.fadeOut(200, function () {
                $nav.removeClass('open').css('display', '');
            });
        } else {
            // Open menu met slide-down effect
            $nav.addClass('open').hide().fadeIn(250);
        }

        $hamburger.toggleClass('open');
        $('body').toggleClass('menu-open');
    });

    // --- Sluit mobiel menu bij klik op nav-link ---
    $nav.find('.nav-link').on('click', function () {
        if ($nav.hasClass('open')) {
            $nav.fadeOut(150, function () {
                $nav.removeClass('open').css('display', '');
            });
            $hamburger.removeClass('open');
            $('body').removeClass('menu-open');
        }
    });

    // --- Sluit menu bij klik buiten de nav ---
    $(document).on('click.nav', function (e) {
        if (
            $nav.hasClass('open') &&
            !$(e.target).closest('.hoofd-nav, .hamburger').length
        ) {
            $nav.fadeOut(150, function () {
                $nav.removeClass('open').css('display', '');
            });
            $hamburger.removeClass('open');
            $('body').removeClass('menu-open');
        }
    });

    // --- Actieve navlink markeren op basis van huidige URL ---
    const huidigePad = window.location.pathname.split('/').pop();
    $('.nav-link').each(function () {
        const href = $(this).attr('href').split('/').pop().split('?')[0];
        if (href === huidigePad || (huidigePad === '' && href === 'index.php')) {
            $(this).addClass('actief');
        }
    });

    // --- jQuery nav-link hover animatie (onderlijning via jQuery animate) ---
    $('.nav-link').not('.nav-login, .nav-logout').on('mouseenter', function () {
        $(this).stop(true).animate({ paddingLeft: '1rem' }, 150);
    }).on('mouseleave', function () {
        $(this).stop(true).animate({ paddingLeft: '0.9rem' }, 150);
    });
});
