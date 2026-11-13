<?php
declare(strict_types=1);
// parts/head.php
$host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
$titleToDisplay = (string)($pageTitle ?? 'Raricart | Mobilne Live Food Station na wesela i eventy');
$reqUriMeta = (string)($_SERVER['REQUEST_URI'] ?? '');
$scriptNameMeta = (string)($_SERVER['SCRIPT_NAME'] ?? '');
if (strpos($scriptNameMeta, 'pakiety.php') !== false || strpos($reqUriMeta, '/pakiety') !== false) {
    $canonicalUrl = 'https://raricart.pl/pakiety';
} elseif (strpos($scriptNameMeta, 'polityka-prywatnosci.php') !== false || strpos($reqUriMeta, '/polityka-prywatnosci') !== false) {
    $canonicalUrl = 'https://raricart.pl/polityka-prywatnosci';
} else {
    $canonicalUrl = 'https://raricart.pl/';
}
$schemaDomain = in_array($host, ['raricart.pl', 'test.raricart.pl'], true) ? $host : 'raricart.pl';
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titleToDisplay); ?></title>

    <!-- [REF-02] Preconnect & Critical CSS (Capo.js optimal order) -->
    <link rel="preconnect" href="https://media.raricart.pl" crossorigin>
    <link rel="stylesheet" href="/assets/css/styles.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/styles.css'); ?>">

    <!-- Preload Fonts (Optimized for Critical Chain) -->
    <link rel="preload" href="/assets/fonts/Comfortaa-Light.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/assets/fonts/Comfortaa-Regular.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/assets/fonts/Comfortaa-SemiBold.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/assets/fonts/Playfair-Regular.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/assets/fonts/Playfair-SemiBold.ttf" as="font" type="font/ttf" crossorigin>

    <!-- Preload Critical Image (LCP) & Station Modal Images -->
    <link rel="preload" href="https://media.raricart.pl/images/logo_optimized.png" as="image">
    <link rel="preload" href="<?php echo get_val('offer_modals.pancakes', 'https://media.raricart.pl/gallery/6d13d603eaeb8d57_migrated.webp'); ?>" as="image" type="image/webp">
    <link rel="preload" href="<?php echo get_val('offer_modals.icecream', 'https://media.raricart.pl/gallery/9b9579c8c4ed20b2_migrated.webp'); ?>" as="image" type="image/webp">
    <link rel="preload" href="<?php echo get_val('offer_modals.cheese', 'https://media.raricart.pl/gallery/cdd87e53948f5762_migrated.webp'); ?>" as="image" type="image/webp">

    <!-- Security: CSP -->
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self' https://media.raricart.pl https://images.unsplash.com https://media.istockphoto.com; media-src 'self' https://media.raricart.pl data: blob:; script-src 'self' 'unsafe-inline' https://www.googletagmanager.com https://ai.twojastronawww.pl; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' https: data: blob:; connect-src 'self' https://media.raricart.pl https://www.google-analytics.com https://ai.twojastronawww.pl; frame-ancestors 'self';">

    <?php if ($host === 'test.raricart.pl'): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>

    <!-- SEO -->
    <meta name="description"
        content="Mobilne stacje gastronomiczne na wesela, eventy firmowe i przyjęcia. Mini pancakes, lody włoskie i deski serów przygotowywane na żywo. Sprawdź dostępność terminu.">
    <meta name="keywords"
        content="live food station, mobilne stacje gastronomiczne, stacja gastronomiczna na wesele, atrakcje na wesele, catering na wesele, mini pancakes na wesele, lody włoskie na wesele, stacja z lodami, słodki stół alternatywa, catering na event firmowy, atrakcje na event firmowy, mobilny catering, food station na event, stacje gastronomiczne na eventy">
    <meta name="author" content="Raricart">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($titleToDisplay); ?>">
    <meta property="og:description"
        content="Mobilne stacje gastronomiczne na wesela, eventy firmowe i przyjęcia. Mini pancakes, lody włoskie i deski serów przygotowywane na żywo. Sprawdź dostępność terminu.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:image" content="https://media.raricart.pl/images/Logo.png">

    <!-- Twitter / X Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($titleToDisplay); ?>">
    <meta name="twitter:description" content="Mobilne stacje gastronomiczne na wesela, eventy firmowe i przyjęcia. Mini pancakes, lody włoskie i deski serów przygotowywane na żywo. Sprawdź dostępność terminu.">
    <meta name="twitter:image" content="https://media.raricart.pl/images/Logo.png">

    <!-- PWA, Canonical, Icons & GEO llms.txt ([REF-07]) -->
    <meta name="theme-color" content="#f5f1eb">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="apple-touch-icon" href="<?php echo get_val('favicon', 'https://media.raricart.pl/images/logo_optimized.png'); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="<?php echo get_val('favicon', 'https://media.raricart.pl/images/logo_optimized.png'); ?>">
    <link rel="alternate" type="text/markdown" href="/llms.txt" title="Baza wiedzy dla asystentów AI (llms.txt)">

    <!-- Scripts & Importmap -->
    <?php
    $jsModulesDir = __DIR__ . '/../assets/js/modules';
    $jsModuleFiles = ['translations.js', 'i18n.js', 'nav.js', 'modals.js', 'gallery.js', 'contact.js', 'cookies.js', 'scroll.js', 'utils.js'];
    $maxJsMtime = @filemtime(__DIR__ . '/../assets/js/script.js') ?: 1;
    $importMapEntries = [];
    foreach ($jsModuleFiles as $modFile) {
        $modPath = $jsModulesDir . '/' . $modFile;
        $mtime = @filemtime($modPath) ?: $maxJsMtime;
        if ($mtime > $maxJsMtime) {
            $maxJsMtime = $mtime;
        }
        $importMapEntries['/assets/js/modules/' . $modFile] = '/assets/js/modules/' . $modFile . '?v=' . $mtime;
    }
    ?>
    <script type="importmap"><?php echo json_encode(['imports' => $importMapEntries], JSON_UNESCAPED_SLASHES); ?></script>

    <!-- [REF-02] Speculation Rules API (0ms navigation with API/Admin exclusions) -->
    <script type="speculationrules">
    {
      "prerender": [
        {
          "where": {
            "and": [
              { "href_matches": "/*" },
              { "not": { "href_matches": "/api/*" } },
              { "not": { "href_matches": "/admin/*" } },
              { "not": { "href_matches": "/logout*" } },
              { "not": { "selector_matches": "[aria-hidden=\"true\"], [data-bot-trap]" } }
            ]
          },
          "eagerness": "moderate"
        }
      ]
    }
    </script>

    <!-- [REF-08] Schema.org Full @graph (FoodEstablishment + WebSite + FAQPage) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "FoodEstablishment",
          "@id": "https://<?php echo $schemaDomain; ?>/#organization",
          "name": "Raricart Live Food Station",
          "description": "Mobilne stacje gastronomiczne i degustacyjne na wesela, eventy firmowe i przyjęcia (Mini Pancakes, Lody Włoskie, Deski Serów i Wędlin).",
          "url": "https://<?php echo $schemaDomain; ?>/",
          "logo": "https://media.raricart.pl/images/Logo.png",
          "image": "https://media.raricart.pl/images/Logo.png",
          "telephone": "+48883392688",
          "email": "kontakt@raricart.pl",
          "priceRange": "$$",
          "address": {
            "@type": "PostalAddress",
            "addressRegion": "Śląskie",
            "addressCountry": "PL"
          },
          "servesCuisine": ["Desserts", "Ice Cream", "Pancakes", "Cheese Boards", "Live Cooking"],
          "sameAs": [
            "https://www.instagram.com/raricart.pl/",
            "https://www.facebook.com/raricart"
          ],
          "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Mobilne Stanowiska Kulinarne Raricart",
            "itemListElement": [
              {
                "@type": "Offer",
                "name": "Lody włoskie",
                "description": "Aksamitne lody o smaku śmietankowym, kręcone na bieżąco podczas przyjęcia ze strefą dodatków (sosy, polewy, posypki i owoce). Wycena indywidualna.",
                "url": "https://raricart.pl/#oferta"
              },
              {
                "@type": "Offer",
                "name": "Mini pancakes",
                "description": "Gorące, puszyste placuszki wypiekane na miejscu na oczach gości z owocami, sosami i chrupiącymi dodatkami. Wycena indywidualna.",
                "url": "https://raricart.pl/#oferta"
              },
              {
                "@type": "Offer",
                "name": "Deska serów i wędlin",
                "description": "Bogato zaaranżowana strefa wyselekcjonowanych serów, dojrzewających włoskich wędlin oraz świeżych owoców, orzechów, oliwek i konfitur. Wycena indywidualna.",
                "url": "https://raricart.pl/#oferta"
              }
            ]
          }
        },
        {
          "@type": "WebSite",
          "@id": "https://<?php echo $schemaDomain; ?>/#website",
          "url": "https://<?php echo $schemaDomain; ?>/",
          "name": "Raricart Live Food Station",
          "inLanguage": "pl-PL",
          "publisher": {
            "@id": "https://<?php echo $schemaDomain; ?>/#organization"
          }
        },
        {
          "@type": "FAQPage",
          "@id": "https://<?php echo $schemaDomain; ?>/#faq",
          "mainEntity": [
            {
              "@type": "Question",
              "name": "Na jaką liczbę gości sprawdzi się stoisko Raricart?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Obsługujemy zarówno kameralne przyjęcia na 20–30 osób, jak i duże wesela oraz spotkania firmowe do ponad 500 uczestników. Czas trwania serwisu oraz zakres oferty dopasowujemy indywidualnie."
              }
            },
            {
              "@type": "Question",
              "name": "Czego potrzebujecie na miejscu, aby rozstawić stoisko?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Potrzebujemy kawałka równego podłoża (ok. 2x2 m) w sali lub w plenerze oraz dostępu do standardowego gniazdka elektrycznego 230V."
              }
            },
            {
              "@type": "Question",
              "name": "Czy można połączyć kilka stoisk na jednym przyjęciu?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Tak! Bardzo często łączymy np. Mini Pancakes lub Lody Włoskie ze słodką strefą oraz wytrawną Deską Serów i Wędlin w ramach jednego pakietu eventowego."
              }
            }
          ]
        }
      ]
    }
    </script>
    <script>
        window.siteContentConfig = <?php echo json_encode($content ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <!-- Hash Navigation: Block native scroll BEFORE layout renders -->
    <script>
        if (window.location.hash) {
            if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
            window.scrollTo(0, 0);
        }
    </script>
</head>

<?php
$reqUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$is_packages_page = (strpos($scriptName, 'pakiety.php') !== false || strpos($reqUri, '/pakiety') !== false);
$is_home_page = !$is_packages_page;
?>
<body class="<?php echo $is_home_page ? 'is-homepage' : 'is-packages-page'; ?>">
