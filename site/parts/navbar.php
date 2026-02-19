<?php
// parts/navbar.php

// Determine context
$current_page = $_SERVER['REQUEST_URI'];
// Assume home unless we are explicitly in /packages (fixes issues with subdirectories like /raricart.pl/)
$is_home = strpos($current_page, '/packages') === false;

// Helpers for paths
$assets_path = '/site/assets'; // Absolute path for assets
$base_url = $is_home ? '' : '/site'; // Link prefix for non-home pages

function nav_link($anchor) {
    global $base_url;
    return $base_url . $anchor;
}

// Initial classes for subpages
$nav_init_class = $is_home ? '' : 'visible no-transition';
$logo_init_class = $is_home ? '' : 'moving no-transition';

// Force final state on subpages + ENABLE POINTER EVENTS
$nav_style = $is_home ? '' : 'style="opacity: 1 !important; visibility: visible !important; pointer-events: auto !important;"';
// NO inline transform for logo - let CSS (.hero-logo.moving) handle responsive sizing/positioning
// .no-transition class prevents animation
$logo_style = $is_home ? '' : '';
$bg_style = $is_home ? '' : 'style="transition: none !important;"';
?>
    <!-- Backgrounds -->
    <?php if ($is_home): ?>
    <div class="video-background" id="videoBg">
        <video autoplay muted loop playsinline aria-label="Tło wideo przedstawiające przygotowanie potraw">
            <source src="<?php echo $assets_path; ?>/video/hero.mp4" type="video/mp4">
        </video>

        <div class="video-overlay"></div>
    </div>
    <?php else: ?>
    <!-- Static background for subpages -->
    <div class="nav-bg visible" id="navBg" <?php echo $bg_style; ?>></div>
    <?php endif; ?>


    <!-- Header Container -->
    <header id="main-header">
        <?php if ($is_home): ?>
        <div class="nav-bg" id="navBg"></div>
        <?php endif; ?>
        
        <!-- Branding & Nav -->
        <?php $brand_text_style = $is_home ? '' : 'style="display: none !important;"'; ?>
        <h1 class="new-brand" id="brandText1" data-i18n="hero.title1" <?php echo $brand_text_style; ?>>TAM, GDZIE SMAK SPOTYKA EMOCJE, A PROSTOTA STAJE
            SIĘ ELEGANCJĄ...</h1>
        <div class="new-brand" id="brandText2" data-i18n="hero.title2" <?php echo $brand_text_style; ?>>...TAM ZACZYNA SIĘ <span class="logo-pulse">RARICART</span></div>

        <div class="hamburger <?php echo $nav_init_class; ?>" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="hero-logo <?php echo $logo_init_class; ?>" id="brand" <?php echo $logo_style; ?>>
            <img src="<?php echo $assets_path; ?>/images/logo_optimized.png" class="brand-logo" alt="Raricart Live Food Station Logo"
                width="342" height="250" style="cursor:pointer" fetchpriority="high">
        </div>

        <nav id="nav" class="<?php echo $nav_init_class; ?>" <?php echo $nav_style; ?>>
            <ul class="nav-side nav-left">
                <li><a href="<?php echo nav_link('#onas'); ?>" aria-label="Przejdź do sekcji O Nas" data-i18n="nav.about">O Nas</a></li>
                <li><a href="<?php echo nav_link('#oferta-lista'); ?>" aria-label="Przejdź do sekcji Oferta" data-i18n="nav.offer">Oferta</a></li>
                <li><a href="<?php echo nav_link('#realizacje'); ?>" aria-label="Przejdź do sekcji Galeria Realizacji"
                        data-i18n="nav.gallery">Galeria</a></li>
                <li><a href="<?php echo nav_link('#faq'); ?>" aria-label="Przejdź do sekcji FAQ" data-i18n="nav.faq">FAQ</a></li>
            </ul>

            <!-- Logo removed from here -->

            <ul class="nav-side nav-right">
                <li><a href="<?php echo nav_link('#dlaczego'); ?>" aria-label="Przejdź do sekcji Co Nas Wyróżnia" data-i18n="nav.why_us">Co Nas
                        Wyróżnia</a></li>
                <li><a href="/packages" aria-label="Zobacz Pakiety" data-i18n="nav.packages">Pakiety</a></li>
                <li><a href="<?php echo nav_link('#kontakt'); ?>" aria-label="Przejdź do sekcji Kontakt" data-i18n="nav.contact">Kontakt</a></li>
            </ul>

            <div class="lang-switch">
                <button class="lang-btn active" data-lang="pl">PL</button>
                <span class="sep">|</span>
                <button class="lang-btn" data-lang="en">EN</button>
                <span class="sep">|</span>
                <button class="lang-btn" data-lang="es">ES</button>
            </div>
        </nav>

        <?php if ($is_home): ?>
        <div class="scroll-indicator" id="scroll"><span data-i18n="hero.scroll">Przewiń w dół</span><span
                class="scroll-arrow">↓</span></div>
        <?php endif; ?>
    </header>
    <?php if ($is_home): ?>
    <div class="spacer"></div>
    <?php endif; ?>
