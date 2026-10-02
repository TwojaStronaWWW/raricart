<?php
// parts/about-story.php - Sekcja 11: O Nas — Autentyczna Historia Założycielki (Lejek 2026)
$about_story_img = function_exists('get_val') ? get_val('about_image', 'https://media.raricart.pl/gallery/b40f9e32da2f0358_migrated.webp') : 'https://media.raricart.pl/gallery/b40f9e32da2f0358_migrated.webp';
?>
<section id="onas" class="section section-about-story" aria-label="O Nas - Historia Założycielki Raricart">
    <div class="about-story-container">
        <!-- Visual Column: Autentyczne zdjęcie z pływającą kartą misji -->
        <div class="about-story-visual">
            <div class="about-story-img-wrapper">
                <img src="<?php echo htmlspecialchars($about_story_img); ?>" 
                     alt="Założycielka Raricart przy mobilnej stacji gastronomicznej live food" 
                     loading="lazy" 
                     class="about-story-img premium-img">
                <div class="about-story-floating-card">
                    <div class="about-floating-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                    <div class="about-floating-text">
                        <span class="about-floating-title" data-i18n="about_story.floating_title">Pasja do detalu</span>
                        <span class="about-floating-desc" data-i18n="about_story.floating_desc">Każdy event traktujemy indywidualnie</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Column: Osobista, autentyczna opowieść założycielki -->
        <div class="about-story-content">
            <span class="about-story-badge" data-i18n="about_story.badge">LUDZIE ZA STACJĄ</span>
            <h2 class="about-story-title" data-i18n="about_story.title">Cześć, tu Raricart.</h2>
            
            <div class="about-story-text">
                <p class="about-lead" data-i18n="about_story.lead">
                    Raricart powstało z prostego pomysłu: żeby jedzenie podczas wydarzenia było czymś więcej niż tylko poczęstunkiem.
                </p>
                <p data-i18n="about_story.p1">
                    Chciałam stworzyć stacje, które przyciągają ludzi, dają im możliwość wyboru i jednocześnie pięknie wpisują się w charakter wydarzenia.
                </p>
                <p data-i18n="about_story.p2">
                    Dlatego każdą realizację traktuję jako połączenie dobrego jedzenia, estetyki i świetnej obsługi. Dziś Raricart pojawia się na weselach, eventach firmowych i prywatnych przyjęciach.
                </p>
                <blockquote class="about-story-quote">
                    <p data-i18n="about_story.quote">„A moim celem za każdym razem jest ten sam: żeby Twoi goście powiedzieli: «Wow, ale to było dobre».”</p>
                </blockquote>
            </div>

            <!-- Founder Profile & CTA Action -->
            <div class="about-story-footer">
                <div class="about-founder-profile">
                    <div class="about-founder-info">
                        <span class="about-founder-name">Katarzyna</span>
                        <span class="about-founder-role" data-i18n="about_story.role">Założycielka Raricart & Pasjonatka Estetyki Kulinariów</span>
                    </div>
                </div>
                <div class="about-story-cta">
                    <a href="#kontakt" class="btn-about-story" data-i18n="about_story.cta">
                        <span>Poznaj Raricart</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
