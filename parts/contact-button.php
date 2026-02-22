<?php
// parts/contact-button.php
?>
<a href="/#kontakt" class="floating-cta" id="floatingCta" aria-label="Kontakt">
    <span class="floating-cta-icon">✉</span>
    <span class="cta-text-desktop">ZAPYTAJ O WYCENĘ</span>
    <span class="cta-text-mobile">KONTAKT</span>
</a>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const floatingCta = document.getElementById('floatingCta');
        if (!floatingCta) return;

        let lastScrollCta = window.scrollY;

        window.addEventListener('scroll', () => {
            const y = window.scrollY;
            
            // Pokaż po przescrollowaniu 300px
            if (y > 300) {
                floatingCta.classList.add('visible');
            } else {
                floatingCta.classList.remove('visible');
            }

            // Ukryj, jeśli stopka (footer) jest widoczna na ekranie
            const footer = document.querySelector('footer');
            if (footer) {
                const footerTop = footer.getBoundingClientRect().top;
                if (footerTop < window.innerHeight) {
                    floatingCta.classList.remove('visible');
                }
            }
            
            lastScrollCta = y;
        }, { passive: true });
    });
</script>
