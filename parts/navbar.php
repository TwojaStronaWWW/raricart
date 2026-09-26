<?php
// parts/navbar.php

// Fix 11d: Subdomain Logic
// Detect protocol and current host
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://";
$host = $_SERVER['HTTP_HOST'];

// Detect if we are on packages subpage (checking URI for /pakiety, or script name)
$on_packages = strpos($_SERVER['REQUEST_URI'], '/pakiety') !== false || strpos($_SERVER['SCRIPT_NAME'], 'pakiety.php') !== false;

if ($on_packages) {
    // We are on packages subpage
    $is_home = false;
    
    // Links (Full URLs)
    $base_url = '/'; // Go to main domain root
    $assets_path = '/assets'; 
    $packages_link = '#';
} else {
    // We are on Main Domain (e.g. raricart.pl)
    $is_home = true;
    
    // Links (Local)
    $base_url = '/'; 
    $assets_path = '/assets'; // Absolute assets
    
    $packages_link = '/pakiety';
}

function nav_link($anchor) {
    global $base_url;
    // ensure base_url ends with / if it's a domain, or is empty
    return $base_url . $anchor; 
}

// Removed forced subpage states to align with homepage dynamic scrolling logic
$nav_init_class = '';
$logo_init_class = '';
$nav_style = '';
$logo_style = '';
$bg_style = '';
?>
    <style>
        /* Zapobiega zlewaniu się ciemnego tekstu z szałwiowym tłem przy najechaniu */
        .nav-dark .nav-side a:hover {
            color: #ffffff !important;
        }
        
        /* Skalowanie odstępów i czcionek, by linki NIGDY nie najechały na logo przy zwężaniu okna */
        @media (max-width: 1400px) {
            #nav { padding: 0 15px !important; }
            .nav-side { gap: 15px !important; }
            .nav-side a { font-size: 0.85rem !important; letter-spacing: 0px !important; }
            .cta-nav { padding: 8px 12px !important; }
        }
        
        @media (max-width: 1150px) {
            .nav-side { gap: 8px !important; }
            .nav-side a { font-size: 0.75rem !important; }
        }
    </style>

    <!-- Header Container -->
    <header id="main-header" class="new-header">
        <div class="new-header-bg"></div>
        <div class="nav-bg" id="navBg" style="display:none;"></div>
        
        <div class="hamburger <?php echo $nav_init_class; ?>" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <nav id="nav" class="navbar-new visible nav-dark">
            <ul class="nav-side nav-left">
                <li><a href="<?php echo nav_link('#hero'); ?>" aria-label="Strona główna">RARICART</a></li>
                <li><a href="<?php echo nav_link('#oferta'); ?>" aria-label="Oferta">OFERTA</a></li>
                <li><a href="<?php echo nav_link('#proces'); ?>" aria-label="Jak to działa">JAK TO DZIAŁA</a></li>
                <li><a href="<?php echo nav_link('#dlakogo'); ?>" aria-label="Dla kogo">DLA KOGO</a></li>
            </ul>

            <div class="<?php echo $logo_init_class; ?> navbar-new-brand" id="nav-brand">
                <img src="https://media.raricart.pl/images/logo_optimized.png" class="brand-logo" alt="Raricart Live Food Station Logo"
                    style="cursor:pointer;" fetchpriority="high">
            </div>

            <ul class="nav-side nav-right">
                <li><a href="<?php echo nav_link('#onas'); ?>" aria-label="O nas">O NAS</a></li>
                <li><a href="<?php echo nav_link('#realizacje'); ?>" aria-label="Realizacje">REALIZACJE</a></li>
                <li><a href="<?php echo nav_link('#faq'); ?>" aria-label="FAQ">FAQ</a></li>
                <li>
                    <a href="<?php echo nav_link('#kontakt'); ?>" class="cta-nav" aria-label="Sprawdź termin">
                        SPRAWDŹ TERMIN
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </li>
            </ul>

            <div class="lang-switch" style="display: none;">
                <button class="lang-btn active" data-lang="pl">PL</button>
                <span class="sep">|</span>
                <button class="lang-btn" data-lang="en">EN</button>
                <span class="sep">|</span>
                <button class="lang-btn" data-lang="es">ES</button>
            </div>
        </nav>
    </header>
    <!-- Spacer for fixed header -->
    <div style="height: 100px;"></div>

