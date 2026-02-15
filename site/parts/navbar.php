<?php
// parts/navbar.php
?>
    <!-- Backgrounds -->
    <div class="video-background" id="videoBg">
        <video autoplay muted loop playsinline aria-label="Tło wideo przedstawiające przygotowanie potraw">
            <source src="<?php echo get_val('hero_video', 'assets/video/hero.mp4'); ?>" type="video/mp4">
        </video>

        <div class="video-overlay"></div>
    </div>


    <!-- Header Container -->
    <header id="main-header">
        <div class="nav-bg" id="navBg"></div>
        <!-- Branding & Nav -->
        <h1 class="new-brand" id="brandText1" data-i18n="hero.title1">TAM, GDZIE SMAK SPOTYKA EMOCJE, A PROSTOTA STAJE
            SIĘ ELEGANCJĄ...</h1>
        <div class="new-brand" id="brandText2" data-i18n="hero.title2">...TAM ZACZYNA SIĘ <span class="logo-pulse">RARICART</span></div>

        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="hero-logo" id="brand">
            <img src="assets/images/logo_optimized.png" class="brand-logo" alt="Raricart Live Food Station Logo"
                width="342" height="250" style="cursor:pointer" fetchpriority="high">
        </div>

        <nav id="nav">
            <ul class="nav-side nav-left">
                <li><a href="#onas" aria-label="Przejdź do sekcji O Nas" data-i18n="nav.about">O Nas</a></li>
                <li><a href="#oferta" aria-label="Przejdź do sekcji Oferta" data-i18n="nav.offer">Oferta</a></li>
                <li><a href="#realizacje" aria-label="Przejdź do sekcji Galeria Realizacji"
                        data-i18n="nav.gallery">Galeria</a></li>
            </ul>

            <!-- Logo removed from here -->

            <ul class="nav-side nav-right">
                <li><a href="#faq" aria-label="Przejdź do sekcji FAQ" data-i18n="nav.faq">FAQ</a></li>
                <li><a href="#dlaczego" aria-label="Przejdź do sekcji Co Nas Wyróżnia" data-i18n="nav.why_us">Co Nas
                        Wyróżnia</a></li>
                <li><a href="#kontakt" aria-label="Przejdź do sekcji Kontakt" data-i18n="nav.contact">Kontakt</a></li>
            </ul>

            <div class="lang-switch">
                <button class="lang-btn active" data-lang="pl">PL</button>
                <span class="sep">|</span>
                <button class="lang-btn" data-lang="en">EN</button>
                <span class="sep">|</span>
                <button class="lang-btn" data-lang="es">ES</button>
            </div>
        </nav>

        <div class="scroll-indicator" id="scroll"><span data-i18n="hero.scroll">Przewiń w dół</span><span
                class="scroll-arrow">↓</span></div>
    </header>
    <div class="spacer"></div>
