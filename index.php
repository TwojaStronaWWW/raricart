<?php
require_once __DIR__ . '/api/core.php';

// Prevent PHP / LiteSpeed Caching - USUNIĘTE DLA OPTYMALIZACJI
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
// header("X-LiteSpeed-Cache-Control: no-cache"); 
// header("Clear-Site-Data: \"cache\"");

$jsonFile = __DIR__ . '/assets/data/gallery.json';

// Parallax backgrounds (CSS injection)
$gallery_bg = get_val('gallery_bg', '');
$why_us_bg = get_val('why_us_bg', '');

// 1. HEAD
include 'parts/head.php';

// 2. NAVBAR (Includes Video BG)
include 'parts/navbar.php';
?>

    <main class="content">

        <!-- 1. HERO SECTION (Split-Screen Lejek 2026) -->
        <?php include 'parts/hero.php'; ?>

        <!-- 2. TRUST BAR (Mini Sekcja Zaufania - Lejek 2026) -->
        <?php include 'parts/trust-bar.php'; ?>

        <!-- 3. STACJE („Co właściwie oferujemy?” - Lejek 2026) -->
        <?php include 'parts/stations.php'; ?>

        <!-- 4. GŁÓWNA SEKCJA SPRZEDAŻOWA (Dlaczego stacja zamiast cateringu? - Lejek 2026) -->
        <?php include 'parts/why-station.php'; ?>

        <!-- 5. PROCES („Jak to wygląda?” - 5 kroków współpracy - Lejek 2026) -->
        <?php include 'parts/process.php'; ?>

        <!-- 6. DLA KOGO? (Segmentacja wydarzeń - Lejek 2026) -->
        <?php include 'parts/audiences.php'; ?>

        <!-- 7. DLACZEGO RARICART? (6 esencjonalnych argumentów - Lejek 2026) -->
        <?php include 'parts/why-raricart.php'; ?>


        <!-- 8. REALIZACJE (Wyselekcjonowana galeria kadrów - Lejek 2026) -->
        <?php include 'parts/realizations.php'; ?>

        <!-- 9. OPINIE KLIENTÓW (Social Proof & Referencje - Lejek 2026) -->
        <?php include 'parts/reviews.php'; ?>

        <!-- 10. O NAS (Autentyczna Historia Założycielki - Lejek 2026) -->
        <?php include 'parts/about-story.php'; ?>

        <!-- 11. FAQ (Zoptymalizowane 7 pytań - Lejek 2026) -->
        <?php include 'parts/faq.php'; ?>

        <!-- 12. OSTATNIE CTA PRZED FORMULARZEM (Lejek 2026) -->
        <?php include 'parts/pre-form-cta.php'; ?>

        <!-- 13. FORMULARZ KONTAKTOWY (Skoncentrowany na konwersji - Lejek 2026) -->
        <?php include 'parts/contact.php'; ?>

    </main>

<?php
// 3. MODALS
include 'parts/modals.php';

// 4. Widżet pływający Zapytaj o wycenę
include 'parts/contact-button.php';

// 5. FOOTER (zawiera stopkę, skrypty oraz zamknięcie body i html)
include 'parts/footer.php';
?>