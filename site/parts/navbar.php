<?php
// parts/navbar.php

// Determine context
$current_page = $_SERVER['REQUEST_URI'];
// Assume home unless we are explicitly in /packages (fixes issues with subdirectories like /raricart.pl/)
$is_home = strpos($current_page, '/packages') === false;

// Helpers for paths
// Helpers for paths
// Use relative paths to allow hosting in subdirectories
$assets_path = $is_home ? 'site/assets' : '../site/assets';
// Link prefix: if home, anchor only. If subpage, go back to root index.php
$base_url = $is_home ? '' : '../index.php';

function nav_link($anchor) {
    global $base_url;
    // Ensure anchor starts with #
    return $base_url . $anchor;
}

// ... existing code ...

            <ul class="nav-side nav-right">
                <li><a href="<?php echo nav_link('#dlaczego'); ?>" aria-label="Przejdź do sekcji Co Nas Wyróżnia" data-i18n="nav.why_us">Co Nas
                        Wyróżnia</a></li>
                <!-- Relative link to packages -->
                <li><a href="<?php echo $is_home ? 'packages/index.php' : '#'; ?>" aria-label="Zobacz Pakiety" data-i18n="nav.packages">Pakiety</a></li>
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
