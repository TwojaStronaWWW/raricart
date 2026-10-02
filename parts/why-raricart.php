<?php
// parts/why-raricart.php - Sekcja 7: „Dlaczego Raricart?” (6 esencjonalnych argumentów - Lejek 2026)
$why_raricart_bg = function_exists('get_val') ? get_val('why_us_bg', '') : '';
?>
<section id="dlaczego" class="section section-why-raricart">
    <div class="parallax-why" <?php if($why_raricart_bg): ?>style="--bg-image: url('<?php echo htmlspecialchars($why_raricart_bg); ?>');"<?php endif; ?>>
        <div class="why-raricart-container">
            <!-- Nagłówek sekcji -->
            <div class="why-raricart-header">
                <span class="why-raricart-badge" data-i18n="why_raricart.badge">6 MOCNYCH PRZEWAG</span>
                <h2 class="why-raricart-title" data-i18n="why_raricart.title">Dlaczego właśnie Raricart?</h2>
                <p class="why-raricart-subtitle" data-i18n="why_raricart.subtitle">
                    Nie jesteśmy klasycznym cateringiem w bemarach. Zobacz 6 kluczowych powodów, dla których goście tak bardzo zapamiętują nasze stacje live food.
                </p>
            </div>

            <!-- 6 Esencjonalnych Kart Argumentów -->
            <div class="why-raricart-grid">
                <!-- 1. Świeżość -->
                <div class="why-raricart-card">
                    <div class="card-head">
                        <span class="card-num">01</span>
                        <div class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="card-title" data-i18n="why_raricart.card1_title">Świeżość na żywo</h3>
                    <p class="card-desc" data-i18n="why_raricart.card1_desc">
                        Każda porcja powstaje na bieżąco, na oczach Twoich gości. Ciepłe pancakes prosto z płyty, świeżo kręcone lody i pachnące dodatki — bez odgrzewania.
                    </p>
                </div>

                <!-- 2. Swoboda wyboru -->
                <div class="why-raricart-card">
                    <div class="card-head">
                        <span class="card-num">02</span>
                        <div class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 8v8M8 12h8"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="card-title" data-i18n="why_raricart.card2_title">Swoboda wyboru</h3>
                    <p class="card-desc" data-i18n="why_raricart.card2_desc">
                        Goście sami decydują o swojej kompozycji: autorskie sosy, świeże owoce, chrupiące posypki i unikalne smaki. Każdy tworzy dokładnie to, na co ma ochotę.
                    </p>
                </div>

                <!-- 3. Efekt WOW -->
                <div class="why-raricart-card">
                    <div class="card-head">
                        <span class="card-num">03</span>
                        <div class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                    </div>
                    <h3 class="card-title" data-i18n="why_raricart.card3_title">Efekt WOW i integracja</h3>
                    <p class="card-desc" data-i18n="why_raricart.card3_desc">
                        Live cooking przyciąga wzrok, zachwyca zapachem i naturalnie skupia wokół siebie gości. To atrakcja, przy której rodzą się uśmiechy i pamiątkowe zdjęcia.
                    </p>
                </div>

                <!-- 4. Estetyka -->
                <div class="why-raricart-card">
                    <div class="card-head">
                        <span class="card-num">04</span>
                        <div class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                        </div>
                    </div>
                    <h3 class="card-title" data-i18n="why_raricart.card4_title">Estetyka w każdym detalu</h3>
                    <p class="card-desc" data-i18n="why_raricart.card4_desc">
                        Nasze stoiska to eleganckie meble eventowe wykonane z dbałością o detal. Stają się spójną, fotogeniczną częścią aranżacji Twojej sali lub pleneru.
                    </p>
                </div>

                <!-- 5. Wygoda -->
                <div class="why-raricart-card">
                    <div class="card-head">
                        <span class="card-num">05</span>
                        <div class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                    </div>
                    <h3 class="card-title" data-i18n="why_raricart.card5_title">Wygoda organizatora</h3>
                    <p class="card-desc" data-i18n="why_raricart.card5_desc">
                        Dojazd, montaż, sprzęt, obsługa, serwis i sprzątanie po evencie — w 100% po naszej stronie. Ty skupiasz się na gościach i spokojnie cieszysz się imprezą.
                    </p>
                </div>

                <!-- 6. Elastyczność -->
                <div class="why-raricart-card">
                    <div class="card-head">
                        <span class="card-num">06</span>
                        <div class="card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                <polyline points="2 17 12 22 22 17"></polyline>
                                <polyline points="2 12 12 17 22 12"></polyline>
                            </svg>
                        </div>
                    </div>
                    <h3 class="card-title" data-i18n="why_raricart.card6_title">Pełna elastyczność</h3>
                    <p class="card-desc" data-i18n="why_raricart.card6_desc">
                        Jedna stacja czy pakiet kilku smaków? Dopasowujemy ofertę, godziny serwisu i menu do charakteru przyjęcia — od 20 do ponad 500 gości.
                    </p>
                </div>
            </div>

            <!-- Dolna belka CTA -->
            <div class="why-raricart-action">
                <a href="#kontakt" class="why-raricart-cta-btn" data-i18n="why_raricart.cta_btn" aria-label="Sprawdź dostępność na swoje wydarzenie">
                    <span>SPRAWDŹ DOSTĘPNOŚĆ NA SWOJE WYDARZENIE</span>
                    <span class="btn-arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>
</section>
