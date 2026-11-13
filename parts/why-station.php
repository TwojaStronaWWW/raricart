<?php
declare(strict_types=1);
// parts/why-station.php - Sekcja 4: Główna Sekcja Sprzedażowa (Dlaczego stacja zamiast cateringu?)
$why_station_img = function_exists('get_val') ? get_val('offer_main_image', 'https://media.raricart.pl/gallery/b8135a7105f1d9d0_migrated.webp') : 'https://media.raricart.pl/gallery/b8135a7105f1d9d0_migrated.webp';
?>
<section id="dlaczego-stacja" class="section why-station-section" aria-label="Dlaczego warto wybrać mobilną stację">
    <div class="why-station-container">
        <div class="why-station-visual">
            <div class="why-station-img-wrapper">
                <img src="<?php echo htmlspecialchars($why_station_img); ?>" 
                     alt="Raricart Live Food Experience - mobilna stacja z logo Raricart" 
                     loading="lazy" 
                     class="why-station-img">
            </div>
        </div>

        <div class="why-station-content">
            <span class="why-station-badge" data-i18n="why_station.badge">Dlaczego warto wybrać mobilną stację?</span>
            <h2 class="why-station-title" data-i18n="why_station.title">ZAMIAST KLASYCZNEGO BUFETU.<br>MIEJSCE, KTÓRE PRZYCIĄGA GOŚCI.</h2>
            <p class="why-station-intro" data-i18n="why_station.intro">
                Nowoczesne przyjęcia odchodzą od ciężkich, zastawionych stołów na rzecz lżejszych form. Nasze stanowisko wnosi na salę świeżość, ruch i dyskretną obsługę przez cały czas trwania serwisu.
            </p>

            <div class="why-station-pillars">
                <!-- Filar 1: PERFEKCYJNA EKSPOZYCJA -->
                <div class="why-station-pillar">
                    <div class="pillar-num">01</div>
                    <div class="pillar-text">
                        <h3 class="pillar-title" data-i18n="why_station.pillar1_title">PERFEKCYJNA EKSPOZYCJA</h3>
                        <p class="pillar-desc" data-i18n="why_station.pillar1_desc">Na bieżąco uzupełniamy produkty i dbamy o porządek, dzięki czemu nawet pod koniec wieczoru strefa wygląda tak samo apetycznie, jak na samym początku.</p>
                    </div>
                </div>

                <!-- Filar 2: SWOBODNA ATMOSFERA -->
                <div class="why-station-pillar">
                    <div class="pillar-num">02</div>
                    <div class="pillar-text">
                        <h3 class="pillar-title" data-i18n="why_station.pillar2_title">SWOBODNA ATMOSFERA</h3>
                        <p class="pillar-desc" data-i18n="why_station.pillar2_desc">Wózek staje się naturalnym punktem spotkań, który sprzyja krótkim rozmowom przy wyborze dodatków i wprowadza na przyjęcie lekki, niewymuszony klimat.</p>
                    </div>
                </div>

                <!-- Filar 3: ESTETYKA I PERSONALIZACJA -->
                <div class="why-station-pillar">
                    <div class="pillar-num">03</div>
                    <div class="pillar-text">
                        <h3 class="pillar-title" data-i18n="why_station.pillar3_title">ESTETYKA I PERSONALIZACJA</h3>
                        <p class="pillar-desc" data-i18n="why_station.pillar3_desc">Wszystkie elementy robocze ukryliśmy w jasnej, eleganckiej zabudowie, dzięki czemu stoisko prezentuje się lekko i szykownie z każdej strony. Na życzenie personalizujemy jego oprawę pod stylistykę uroczystości lub identyfikację firmy.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
