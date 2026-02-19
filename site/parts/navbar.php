<?php
// parts/navbar.php

// Fix 11d: Subdomain Logic
// Detect protocol and current host
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://";
$host = $_SERVER['HTTP_HOST'];

// Detect if we are on packages subdomain ('test.pakiety' or 'pakiety')
$on_packages = strpos($host, 'pakiety.') !== false;

if ($on_packages) {
    // We are on packages subdomain (e.g. test.pakiety.raricart.pl)
    $is_home = false;
    
    // Determine Main Domain (e.g. test.raricart.pl)
    // Remove 'pakiety.' from host
    $main_host = str_replace('pakiety.', '', $host);
    
    // Links (Full URLs)
    $base_url = $protocol . $main_host . '/'; // Go to main domain root
    $assets_path = $protocol . $main_host . '/assets'; // Hotlink assets from main domain (assumes main domain points to site/)
    $packages_link = '#';
} else {
    // We are on Main Domain (e.g. test.raricart.pl)
    $is_home = true;
    $main_host = $host;
    
    // Links (Local)
    $base_url = ''; 
    $assets_path = './assets'; // Local assets
    
    // Construct packages URL
    // Insert 'pakiety.' into subdomain structure
    if (strpos($host, 'test.') === 0) {
       $packages_host = 'test.pakiety.' . substr($host, 5);
    } else {
       $packages_host = 'pakiety.' . $host;
    }
    
    $packages_link = $protocol . $packages_host . '/';
}



function nav_link($anchor) {
    global $base_url;
    // ensure base_url ends with / if it's a domain, or is empty
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

            <ul class="nav-side nav-right">
                <li><a href="<?php echo nav_link('#dlaczego'); ?>" aria-label="Przejdź do sekcji Co Nas Wyróżnia" data-i18n="nav.why_us">Co Nas
                        Wyróżnia</a></li>
                <!-- Dynamic link to packages -->
                <li><a href="<?php echo $packages_link; ?>" aria-label="Zobacz Pakiety" data-i18n="nav.packages">Pakiety</a></li>
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
