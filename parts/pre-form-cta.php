<?php
// parts/pre-form-cta.php - Sekcja 12: Ostatnie CTA Przed Formularzem (Lejek 2026)
$pre_cta_bg = function_exists('get_val') ? get_val('why_us_bg', 'https://media.raricart.pl/gallery/749ae64eb81a3f80_migrated.webp') : 'https://media.raricart.pl/gallery/749ae64eb81a3f80_migrated.webp';
?>
<section class="section section-pre-cta" aria-label="Sprawdź dostępność na swoje wydarzenie">
    <div class="pre-cta-backdrop" style="background-image: url('<?php echo htmlspecialchars($pre_cta_bg); ?>');">
        <div class="pre-cta-overlay"></div>
    </div>
    
    <div class="pre-cta-container">
        <div class="pre-cta-card">
            <span class="pre-cta-badge" data-i18n="pre_cta.badge">GOTOWI NA KULINARNE WRAŻENIA?</span>
            <h2 class="pre-cta-title" data-i18n="pre_cta.title">Masz wydarzenie? Zróbmy na nim coś pysznego.</h2>
            <p class="pre-cta-subtitle" data-i18n="pre_cta.subtitle">
                Podaj nam datę, miejsce i liczbę gości. Sprawdzimy dostępność i przygotujemy dla Ciebie indywidualną ofertę.
            </p>
            
            <div class="pre-cta-actions">
                <a href="#kontakt" class="btn-pre-cta" id="preCtaCheckBtn" data-i18n="pre_cta.btn">
                    <span>SPRAWDŹ DOSTĘPNOŚĆ TERMINU</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <polyline points="19 12 12 19 5 12"></polyline>
                    </svg>
                </a>
            </div>
            
            <div class="pre-cta-microcopy">
                <span class="microcopy-icon">⏱️</span>
                <span data-i18n="pre_cta.microcopy">Odpowiemy z informacją o dostępności i propozycją dopasowaną do Twojego wydarzenia.</span>
            </div>
        </div>
    </div>
</section>
