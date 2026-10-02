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
            <span class="stations-badge" data-i18n="stations.badge">Co właściwie oferujemy?</span>
            <h2 class="stations-title" data-i18n="stations.title">Wybierz swoją stację</h2>
            <p class="stations-intro" data-i18n="stations.intro">
                Nie jesteśmy klasycznym bufetem. Nasze stacje przygotowują jedzenie na miejscu, na oczach gości. Każdy może wybrać swoje dodatki i stworzyć własną kompozycję.
            </p>
            <div class="stations-header-cta">
                <a href="<?php echo function_exists('nav_link') ? nav_link('pakiety.php') : 'pakiety.php'; ?>" class="stations-sub-btn" aria-label="Zobacz całą ofertę i pakiety" data-i18n="stations.see_all">
                    <span>ZOBACZ CAŁĄ OFERTĘ & PAKIETY</span>
                    <span class="btn-arrow" aria-hidden="true">→</span>
                </a>
            </div>
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
                    <div class="station-live-badge">
                        <span class="station-dot"></span>
                        <span>LIVE STATION</span>
                    </div>
                </div>
                <div class="offer-content station-content">
                    <h3 class="station-name" data-i18n="offer.cards.pancakes.title">Mini Pancakes</h3>
                    <p class="station-desc" data-i18n="stations.pancakes_desc">Ciepłe, puszyste mini pancakes z dodatkami i sosami przygotowywane na oczach gości.</p>
                    <div class="station-tags">
                        <span class="station-tag">Truskawki</span>
                        <span class="station-tag">Nutella</span>
                        <span class="station-tag">Pistacja</span>
                        <span class="station-tag">Chrupiące posypki</span>
                    </div>
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
                    <div class="station-live-badge">
                        <span class="station-dot"></span>
                        <span>LIVE STATION</span>
                    </div>
                </div>
                <div class="offer-content station-content">
                    <h3 class="station-name" data-i18n="offer.cards.icecream.title">Lody Włoskie</h3>
                    <p class="station-desc" data-i18n="stations.icecream_desc">Kremowe lody włoskie, świeże owoce, rzemieślnicze sosy i chrupiące dodatki.</p>
                    <div class="station-tags">
                        <span class="station-tag">Świeże owoce</span>
                        <span class="station-tag">Belgijska czekolada</span>
                        <span class="station-tag">Rzemieślnicze sosy</span>
                        <span class="station-tag">Wafle</span>
                    </div>
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
                    <div class="station-live-badge">
                        <span class="station-dot"></span>
                        <span>LIVE STATION</span>
                    </div>
                </div>
                <div class="offer-content station-content">
                    <h3 class="station-name" data-i18n="offer.cards.cheese.title">Deska Serów</h3>
                    <p class="station-desc" data-i18n="stations.cheese_desc">Starannie dobrane sery, wędliny i wytrawne dodatki w eleganckiej oprawie.</p>
                    <div class="station-tags">
                        <span class="station-tag">Włoskie sery</span>
                        <span class="station-tag">Prosciutto</span>
                        <span class="station-tag">Oliwki i orzechy</span>
                        <span class="station-tag">Konfitury</span>
                    </div>
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
                    <strong>Chcesz więcej niż jedną atrakcję?</strong> Możesz dowolnie łączyć stacje na jednym evencie — przygotujemy dla Ciebie indywidualny pakiet.
                </p>
            </div>
            <a href="#kontakt" class="stations-footer-btn" data-i18n="stations.cta_check">SPRAWDŹ DOSTĘPNOŚĆ STACJI</a>
        </div>
    </div>
</section>
