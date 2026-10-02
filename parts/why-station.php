<?php
// parts/why-station.php - Sekcja 4: Główna Sekcja Sprzedażowa (Dlaczego stacja zamiast cateringu?)
$why_station_img = function_exists('get_val') ? get_val('offer_main_image', 'https://media.raricart.pl/gallery/b8135a7105f1d9d0_migrated.webp') : 'https://media.raricart.pl/gallery/b8135a7105f1d9d0_migrated.webp';
?>
<section id="onas" class="section why-station-section" aria-label="Dlaczego stacja zamiast cateringu">
    <div class="why-station-container">
        <div class="why-station-visual">
            <div class="why-station-img-wrapper">
                <img src="<?php echo htmlspecialchars($why_station_img); ?>" 
                     alt="Raricart Live Food Experience - goście cieszący się stacją gastronomiczną" 
                     loading="lazy" 
                     class="why-station-img">
                <div class="why-station-badge-floating">
                    <span class="why-station-badge-number">100%</span>
                    <span class="why-station-badge-label" data-i18n="why_station.stat_label">Na oczach gości</span>
                </div>
            </div>
        </div>

        <div class="why-station-content">
            <span class="why-station-badge" data-i18n="why_station.badge">Dlaczego stacja zamiast cateringu?</span>
            <h2 class="why-station-title" data-i18n="why_station.title">Nie tylko jedzenie. Atrakcja dla Twoich gości.</h2>
            <p class="why-station-intro" data-i18n="why_station.intro">
                Tradycyjny catering często stoi w podgrzewaczach i czeka na gości. My tworzymy kulinarne show, które angażuje zmysły i staje się naturalnym centrum rozmów na Twoim przyjęciu.
            </p>

            <div class="why-station-pillars">
                <!-- Filar 1: SMAK -->
                <div class="why-station-pillar">
                    <div class="pillar-num">01</div>
                    <div class="pillar-text">
                        <h3 class="pillar-title" data-i18n="why_station.pillar1_title">SMAK</h3>
                        <p class="pillar-desc" data-i18n="why_station.pillar1_desc">Świeże produkty przygotowywane i serwowane na bieżąco na oczach gości — bez kompromisów i bez odgrzewania.</p>
                    </div>
                </div>

                <!-- Filar 2: DOŚWIADCZENIE -->
                <div class="why-station-pillar">
                    <div class="pillar-num">02</div>
                    <div class="pillar-text">
                        <h3 class="pillar-title" data-i18n="why_station.pillar2_title">DOŚWIADCZENIE</h3>
                        <p class="pillar-desc" data-i18n="why_station.pillar2_desc">Goście z zachwytem obserwują proces przygotowania, rozmawiają z obsługą i sami komponują swój wymarzony deser.</p>
                    </div>
                </div>

                <!-- Filar 3: ESTETYKA -->
                <div class="why-station-pillar">
                    <div class="pillar-num">03</div>
                    <div class="pillar-text">
                        <h3 class="pillar-title" data-i18n="why_station.pillar3_title">ESTETYKA</h3>
                        <p class="pillar-desc" data-i18n="why_station.pillar3_desc">Dopracowana, elegancka stacja mobilna staje się spójną, fotogeniczną częścią aranżacji Twojej sali lub pleneru.</p>
                    </div>
                </div>
            </div>

            <!-- Korzyści dla organizatora -->
            <div class="why-station-benefit-box">
                <span class="benefit-box-tag" data-i18n="why_station.benefit_box_label">Bezstresowa organizacja</span>
                <p class="benefit-box-lead" data-i18n="why_station.benefit_box_p1">
                    A Ty? Nie musisz organizować obsługi, przygotowywać stanowiska ani martwić się o sprzątanie.
                </p>
                <div class="benefit-box-rhythm" data-i18n="why_station.benefit_box_rhythm">
                    Przyjeżdżamy. Przygotowujemy. Serwujemy. Sprzątamy.
                </div>
                <p class="benefit-box-summary" data-i18n="why_station.benefit_box_p2">
                    Ty zajmujesz się swoimi gośćmi. My zajmujemy się całą stacją.
                </p>
            </div>

            <div class="why-station-action">
                <a href="#kontakt" class="why-station-cta-btn" data-i18n="why_station.cta_btn" aria-label="Chcę taką stację na swoim wydarzeniu">
                    <span>CHCĘ TAKĄ STACJĘ NA SWOIM WYDARZENIU</span>
                    <span class="btn-arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>
</section>
