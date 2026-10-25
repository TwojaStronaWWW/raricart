<?php
// parts/head.php
?>
<!DOCTYPE html>
<html lang="pl">

<head>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Security: CSP & Honeypot setup -->
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self' https://media.raricart.pl https://images.unsplash.com https://media.istockphoto.com; media-src 'self' https://media.raricart.pl data: blob:; script-src 'self' 'unsafe-inline' https://www.googletagmanager.com https://ai.twojastronawww.pl; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' https: data: blob:; connect-src 'self' https://media.raricart.pl https://www.google-analytics.com https://ai.twojastronawww.pl; frame-ancestors 'self';">

    <?php
    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
    if ($host === 'test.raricart.pl') {
        echo '<meta name="robots" content="noindex, nofollow">';
    }
    $titleToDisplay = $pageTitle ?? 'Raricart | Mobilne Live Food Station na wesela i eventy';
    $reqUriMeta = $_SERVER['REQUEST_URI'] ?? '';
    $scriptNameMeta = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($scriptNameMeta, 'pakiety.php') !== false || strpos($reqUriMeta, '/pakiety') !== false) {
        $canonicalUrl = 'https://raricart.pl/pakiety';
    } elseif (strpos($scriptNameMeta, 'polityka-prywatnosci.php') !== false || strpos($reqUriMeta, '/polityka-prywatnosci') !== false) {
        $canonicalUrl = 'https://raricart.pl/polityka-prywatnosci';
    } else {
        $canonicalUrl = 'https://raricart.pl/';
    }
    ?>

    <!-- SEO -->
    <title><?php echo htmlspecialchars($titleToDisplay); ?></title>
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

    <!-- PWA & Mobile Chrome Tinting -->
    <meta name="theme-color" content="#f5f1eb">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="apple-touch-icon" href="<?php echo get_val('favicon', 'https://media.raricart.pl/images/logo_optimized.png'); ?>">

    <!-- Styles & Fonts -->
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="<?php echo get_val('favicon', 'https://media.raricart.pl/images/logo_optimized.png'); ?>">

    <!-- Critical CSS: Blocking load to prevent Layout Shift (CLS 1.0 fix) -->
    <link rel="stylesheet" href="/assets/css/styles.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/styles.css'); ?>">

    <!-- Preload Fonts (Optimized for Critical Chain) -->
    <!-- Comfortaa Light (300) - Main Body Text -->
    <link rel="preload" href="/assets/fonts/Comfortaa-Light.ttf" as="font" type="font/ttf" crossorigin>
    <!-- Comfortaa Regular (400) -->
    <link rel="preload" href="/assets/fonts/Comfortaa-Regular.ttf" as="font" type="font/ttf" crossorigin>
    <!-- Comfortaa SemiBold (600) - Navigation & Emphasis -->
    <link rel="preload" href="/assets/fonts/Comfortaa-SemiBold.ttf" as="font" type="font/ttf" crossorigin>
    
    <!-- Playfair Regular (400) - Intro Branding -->
    <link rel="preload" href="/assets/fonts/Playfair-Regular.ttf" as="font" type="font/ttf" crossorigin>
    <!-- Playfair SemiBold (600) - Main Headings -->
    <link rel="preload" href="/assets/fonts/Playfair-SemiBold.ttf" as="font" type="font/ttf" crossorigin>

    <!-- Preload Critical Image (LCP) -->
    <link rel="preload" href="https://media.raricart.pl/images/logo_optimized.png" as="image">

    <!-- Preload Station Modal Images (High priority instant cache for desktop & mobile) -->
    <link rel="preload" href="<?php echo get_val('offer_modals.pancakes', 'https://media.raricart.pl/gallery/6d13d603eaeb8d57_migrated.webp'); ?>" as="image" type="image/webp">
    <link rel="preload" href="<?php echo get_val('offer_modals.icecream', 'https://media.raricart.pl/gallery/9b9579c8c4ed20b2_migrated.webp'); ?>" as="image" type="image/webp">
    <link rel="preload" href="<?php echo get_val('offer_modals.cheese', 'https://media.raricart.pl/gallery/cdd87e53948f5762_migrated.webp'); ?>" as="image" type="image/webp">

    <!-- Scripts -->

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FoodEstablishment",
      "name": "Raricart Live Food Station",
      "description": "Mobilne stacje gastronomiczne i degustacyjne na wesela, eventy firmowe i przyjęcia (Mini Pancakes, Lody Włoskie, Deski Serów i Wędlin).",
      "address": {
        "@type": "PostalAddress",
        "addressCountry": "PL"
      },
      "telephone": "+48883392688",
      "email": "kontakt@raricart.pl",
      "priceRange": "3000 PLN - 20030 PLN",
      "servesCuisine": ["Desserts", "Ice Cream", "Pancakes", "Cheese Boards", "Live Cooking"],
      "url": "https://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'raricart.pl'); ?>",
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Pakiety Mobilnych Stacji Gastronomicznych Raricart",
        "itemListElement": [
          {
            "@type": "Offer",
            "name": "Stacja Lody Włoskie Live Station",
            "description": "Kremowe lody włoskie serwowane na żywo z bogatym wyborem owoców, sosów i chrupiących posypek (od 50 do 150+ osób).",
            "priceSpecification": {
              "@type": "PriceSpecification",
              "minPrice": "3000",
              "maxPrice": "5550",
              "priceCurrency": "PLN"
            },
            "url": "https://raricart.pl/pakiety"
          },
          {
            "@type": "Offer",
            "name": "Stacja Mini Pancakes Live Station",
            "description": "Ciepłe, złociste mini naleśniczki wypiekane na żywo przy gościach z owocami, kremami czekoladowymi i posypkami (od 50 do 150+ osób).",
            "priceSpecification": {
              "@type": "PriceSpecification",
              "minPrice": "3150",
              "maxPrice": "5850",
              "priceCurrency": "PLN"
            },
            "url": "https://raricart.pl/pakiety"
          },
          {
            "@type": "Offer",
            "name": "Stacja Deska Serów i Wędlin Live Station",
            "description": "Strefa degustacyjna obejmująca 12 wyselekcjonowanych pozycji włoskich serów i dojrzewających wędlin premium do samodzielnego komponowania mini desek (od 50 do 150+ osób).",
            "priceSpecification": {
              "@type": "PriceSpecification",
              "minPrice": "3250",
              "maxPrice": "6300",
              "priceCurrency": "PLN"
            },
            "url": "https://raricart.pl/pakiety"
          },
          {
            "@type": "Offer",
            "name": "Pakiet Kompleksowy 3 Stanowiska (Mini Pancakes + Lody Włoskie + Deska Serów)",
            "description": "Pełna strefa live food z rabatem -10% dla 50–200 osób.",
            "priceSpecification": {
              "@type": "PriceSpecification",
              "minPrice": "8870",
              "maxPrice": "20030",
              "priceCurrency": "PLN"
            },
            "url": "https://raricart.pl/pakiety"
          }
        ]
      }
    }
    </script>
    <script>
        window.siteContentConfig = <?php echo json_encode($content ?? []); ?>;
    </script>
    <!-- Hash Navigation: Block native scroll BEFORE layout renders -->
    <script>
        if (window.location.hash) {
            // Prevent browser's native hash scroll (fires before defer scripts)
            if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
            // Immediately pin to top — our JS will handle scrolling after sections are revealed
            window.scrollTo(0, 0);
        }
    </script>
  <link rel="alternate" type="text/markdown" href="llms.txt" title="Baza wiedzy dla asystentów AI (llms.txt)">
</head>

<?php
$reqUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$is_home_page = (strpos($scriptName, 'pakiety.php') === false && strpos($reqUri, '/pakiety') === false);
?>
<body <?php echo $is_home_page ? 'class="is-homepage"' : ''; ?>>
