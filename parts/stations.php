<?php
// parts/stations.php - Sekcja 3: „Co właściwie oferujemy?” — Stacje
$pancakes_img = function_exists('get_val') ? get_val('offer_cards.pancakes', 'https://media.raricart.pl/gallery/7e983f317a6f7fcd_migrated.webp') : 'https://media.raricart.pl/gallery/7e983f317a6f7fcd_migrated.webp';
$icecream_img = function_exists('get_val') ? get_val('offer_cards.icecream', 'https://media.raricart.pl/gallery/5164c04607df0960_migrated.webp') : 'https://media.raricart.pl/gallery/5164c04607df0960_migrated.webp';
$cheese_img = function_exists('get_val') ? get_val('offer_cards.cheese', 'https://media.raricart.pl/gallery/09e3cc5855e0b574_migrated.webp') : 'https://media.raricart.pl/gallery/09e3cc5855e0b574_migrated.webp';
?>
<section id="oferta-lista" class="section stations-section" aria-label="Wybierz swoją stację">
    <div id="oferta" class="station-anchor" aria-hidden="true"></div>
    <div class="stations-container">
        <div class="stations-header">
            <span class="stations-badge" data-i18n="stations.badge">Jedna formuła, różne odsłony</span>
            <h2 class="stations-title" data-i18n="stations.title">WYBIERZ SWOJĄ STACJĘ</h2>
        </div>

        <div class="stations-grid offer-grid">
            <!-- 1. MINI PANCAKES -->
            <article class="offer-card station-card" data-offer="pancakes" tabindex="0" role="button" aria-label="Poznaj stację Mini Pancakes">
                <div class="offer-image-wrapper station-image-wrapper">
                    <span class="station-category-badge" data-i18n="stations.badge_sweet">Na słodko</span>
                    <img src="<?php echo htmlspecialchars($pancakes_img); ?>" 
                         alt="Mini Pancakes przygotowywane na żywo" 
                         loading="lazy" 
                         class="offer-image-img">
                </div>
                <div class="offer-content station-content">
                    <h3 class="station-name" data-i18n="offer.cards.pancakes.title">Mini Pancakes</h3>
                    <p class="station-desc" data-i18n="stations.pancakes_desc">Gorące, puszyste placuszki prosto z płyty to pachnąca alternatywa dla słodkiego stołu, po którą goście wracają kilka razy.</p>
                    <div class="station-action">
                        <span class="station-btn">
                            <span data-i18n="stations.explore_btn">POZNAJ STACJĘ</span>
                            <span class="station-btn-arrow" aria-hidden="true">→</span>
                        </span>
                    </div>
                </div>
            </article>

            <!-- 2. LODY WŁOSKIE -->
            <article class="offer-card station-card" data-offer="icecream" tabindex="0" role="button" aria-label="Poznaj stację Lody Włoskie">
                <div class="offer-image-wrapper station-image-wrapper">
                    <span class="station-category-badge" data-i18n="stations.badge_refresh">Orzeźwienie</span>
                    <img src="<?php echo htmlspecialchars($icecream_img); ?>" 
                         alt="Lody Włoskie serwowane na żywo" 
                         loading="lazy" 
                         class="offer-image-img">
                </div>
                <div class="offer-content station-content">
                    <h3 class="station-name" data-i18n="offer.cards.icecream.title">Lody Włoskie</h3>
                    <p class="station-desc" data-i18n="stations.icecream_desc">Kremowe lody śmietankowe z bogactwem owoców, gęstych polew i posypek, z których każdy gość komponuje swój własny deser.</p>
                    <div class="station-action">
                        <span class="station-btn">
                            <span data-i18n="stations.explore_btn">POZNAJ STACJĘ</span>
                            <span class="station-btn-arrow" aria-hidden="true">→</span>
                        </span>
                    </div>
                </div>
            </article>

            <!-- 3. DESKA SERÓW -->
            <article class="offer-card station-card" data-offer="cheese" tabindex="0" role="button" aria-label="Poznaj stację Deska Serów">
                <div class="offer-image-wrapper station-image-wrapper">
                    <span class="station-category-badge" data-i18n="stations.badge_savory">Wytrawnie</span>
                    <img src="<?php echo htmlspecialchars($cheese_img); ?>" 
                         alt="Deska Serów i włoskich wędlin" 
                         loading="lazy" 
                         class="offer-image-img">
                </div>
                <div class="offer-content station-content">
                    <h3 class="station-name" data-i18n="offer.cards.cheese.title">Deska Serów</h3>
                    <p class="station-desc" data-i18n="stations.cheese_desc">Włoskie sery, dojrzewające wędliny i wytrawne przekąski tworzą elegancką strefę dla gości, którzy zamiast słodyczy wolą wyraziste smaki.</p>
                    <div class="station-action">
                        <span class="station-btn">
                            <span data-i18n="stations.explore_btn">POZNAJ STACJĘ</span>
                            <span class="station-btn-arrow" aria-hidden="true">→</span>
                        </span>
                    </div>
                </div>
            </article>
        </div>

        <!-- Banner łączonych stacji -->
        <div class="stations-footer-banner">
            <div class="stations-footer-content">
                <div class="stations-footer-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                    </svg>
                </div>
                <p class="stations-footer-text" data-i18n="stations.combine_note">
                    <strong>Chcesz połączyć strefę słodką z wytrawną?</strong> Wybierając dwie lub trzy stacje na jedno przyjęcie, otrzymujesz spójną oprawę wizualną oraz preferencyjną wycenę w pakiecie.
                </p>
            </div>
            <a href="#kontakt" class="stations-footer-btn" data-i18n="stations.cta_check">WYCEŃ PAKIET STACJI</a>
        </div>
    </div>
</section>
