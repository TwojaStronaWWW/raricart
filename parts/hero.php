<?php
// parts/hero.php - Sekcja Hero (Split-Screen) zgodna ze specyfikacją 2026
$hero_img = function_exists('get_val') ? get_val('hero_image', 'https://media.raricart.pl/gallery/b40f9e32da2f0358_migrated.webp') : 'https://media.raricart.pl/gallery/b40f9e32da2f0358_migrated.webp';
?>
<section id="hero" class="hero-split">
    <div class="hero-container container">
        <div class="hero-content">
            <span class="hero-badge">Tam, gdzie smak spotyka emocje</span>
            <h1 class="hero-title">Mobilne Live Food Station na Twoje wydarzenie</h1>
            <p class="hero-subtitle">Świeże desery i przekąski przygotowywane na żywo — z pełną obsługą i efektem WOW.</p>
            
            <div class="hero-tags">
                <span class="hero-tag">Wesela</span>
                <span class="hero-tag-sep">•</span>
                <span class="hero-tag">Eventy firmowe</span>
                <span class="hero-tag-sep">•</span>
                <span class="hero-tag">Przyjęcia</span>
                <span class="hero-tag-sep">•</span>
                <span class="hero-tag">Eventy plenerowe</span>
            </div>

            <div class="hero-action">
                <a href="#kontakt" class="hero-cta-btn" aria-label="Sprawdź dostępność terminu">
                    SPRAWDŹ DOSTĘPNOŚĆ TERMINU
                </a>
                <p class="hero-microcopy">Podaj datę, miejsce i liczbę gości — przygotujemy indywidualną ofertę.</p>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-image-wrapper">
                <img src="<?php echo htmlspecialchars($hero_img); ?>" 
                     alt="Raricart Live Food Station z gośćmi i świeżymi deserami" 
                     class="hero-img"
                     fetchpriority="high"
                     loading="eager"
                     width="800"
                     height="600">
            </div>
        </div>
    </div>
</section>
