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
            <span class="about-story-badge" data-i18n="about_story.badge">O NAS</span>
            <h2 class="about-story-title" data-i18n="about_story.title">POZNAJMY SIĘ BLIŻEJ.</h2>
            
            <div class="about-story-text">
                <p class="about-lead" data-i18n="about_story.lead">
                    Tworząc Raricart, chciałam wprowadzić na przyjęcia to, co sama cenię najbardziej: połączenie świetnego smaku, estetycznie podanych porcji i swobodnej atmosfery.
                </p>
                <p data-i18n="about_story.p1">
                    Zależało mi na formie, która wnosi więcej lekkości. Nasze stoiska zaprojektowałam tak, by dyskretnie dopełniały wystrój uroczystości i pozwalały każdemu skomponować porcję dokładnie tak, jak lubi. Sama dbam o dobór składników i spójny wygląd każdego elementu tak, by stanowisko od początku do samego końca serwisu było pełne i prezentowało się świeżo.
                </p>
                <p data-i18n="about_story.p2">
                    Za każdą realizacją stoję osobiście wraz z zespołem, który dzieli moje podejście do jakości i kultury pracy. Od naszej pierwszej rozmowy aż po zakończenie serwisu czuwam nad każdym szczegółem, zostawiając Ci przestrzeń na spokojne celebrowanie chwili z bliskimi.
                </p>
            </div>

            <!-- Founder Profile & CTA Action -->
            <div class="about-story-footer">
                <div class="about-founder-profile">
                    <div class="about-founder-info">
                        <span class="about-founder-name">Emilia</span>
                        <span class="about-founder-role" data-i18n="about_story.role">Założycielka Raricart</span>
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
