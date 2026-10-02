<?php
// parts/hero.php - Sekcja Hero (Split-Screen) z wideo Live Food Station
$default_hero_vid = 'https://media.raricart.pl/content/hero.mp4';
$hero_vid = function_exists('get_val') ? get_val('hero_video', $default_hero_vid) : $default_hero_vid;
$root_dir = defined('BASE_DIR') ? BASE_DIR : dirname(__DIR__);
if ($hero_vid && !str_starts_with($hero_vid, 'http') && !file_exists($root_dir . '/' . ltrim($hero_vid, '/'))) {
    $hero_vid = $default_hero_vid;
} elseif ($hero_vid && !str_starts_with($hero_vid, '/') && !str_starts_with($hero_vid, 'http')) {
    $hero_vid = '/' . $hero_vid;
}
$hero_poster = function_exists('get_val') ? get_val('hero_image', 'https://media.raricart.pl/gallery/b40f9e32da2f0358_migrated.webp') : 'https://media.raricart.pl/gallery/b40f9e32da2f0358_migrated.webp';
?>
<section id="hero" class="hero-split">
    <div class="hero-container container">
        <div class="hero-content">
            <span class="hero-badge" data-i18n="hero.badge">Tam, gdzie smak spotyka emocje</span>
            <h1 class="hero-title" data-i18n="hero.title">Mobilne Live Food Station na Twoje wydarzenie</h1>
            <p class="hero-subtitle" data-i18n="hero.subtitle">Świeże desery i przekąski przygotowywane na żywo — z pełną obsługą i efektem WOW.</p>
            
            <div class="hero-tags">
                <span class="hero-tag" data-i18n="hero.tag_weddings">Wesela</span>
                <span class="hero-tag-sep">•</span>
                <span class="hero-tag" data-i18n="hero.tag_corporate">Eventy firmowe</span>
                <span class="hero-tag-sep">•</span>
                <span class="hero-tag" data-i18n="hero.tag_private">Przyjęcia</span>
                <span class="hero-tag-sep">•</span>
                <span class="hero-tag" data-i18n="hero.tag_outdoor">Eventy plenerowe</span>
            </div>

            <div class="hero-action">
                <a href="#kontakt" class="hero-cta-btn" aria-label="Sprawdź dostępność terminu" data-i18n="hero.cta">
                    SPRAWDŹ DOSTĘPNOŚĆ TERMINU
                </a>
                <p class="hero-microcopy" data-i18n="hero.microcopy">Podaj datę, miejsce i liczbę gości — przygotujemy indywidualną ofertę.</p>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-video-wrapper">
                <video class="hero-video" 
                       autoplay 
                       muted 
                       loop 
                       playsinline 
                       preload="auto" 
                       poster="<?php echo htmlspecialchars($hero_poster); ?>" 
                       aria-label="Prezentacja mobilnej stacji kulinarnej Raricart na żywo">
                    <source src="<?php echo htmlspecialchars($hero_vid); ?>" type="video/mp4">
                </video>
                <div class="hero-video-badge">
                    <span class="hero-video-badge-dot"></span>
                    <span>LIVE PREPARATION</span>
                </div>
            </div>
        </div>
    </div>
</section>
