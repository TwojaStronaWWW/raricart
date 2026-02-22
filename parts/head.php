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
        content="default-src 'self' https://images.unsplash.com https://media.istockphoto.com; script-src 'self' 'unsafe-inline' https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' https: data:; connect-src 'self' https://www.google-analytics.com;">

    <?php
    $host = strtolower($_SERVER['HTTP_HOST']);
    if ($host === 'test.raricart.pl') {
        echo '<meta name="robots" content="noindex, nofollow">';
    }
    ?>

    <!-- SEO -->
    <title>Raricart - Live Food Station & Catering</title>
    <meta name="description"
        content="Raricart to wyjątkowe mobilne stacje degustacyjne na wesela, eventy firmowe i festiwale. Slow BBQ, azjatycki wok, desery fine dining. Obsługa eventów w całej Polsce.">
    <meta name="keywords"
        content="catering eventowy, mobilne stacje gastronomiczne, live cooking, BBQ na event, catering na wesele, food truck, live food station">
    <meta name="author" content="Raricart">


    <!-- Open Graph -->
    <meta property="og:title" content="Raricart - Live Food Station | Mobilne Stacje Degustacyjne">
    <meta property="og:description"
        content="Tam, gdzie smak spotyka emocje, a prostota staje się elegancją. Tworzymy doświadczenie kulinarne, które zostaje w pamięci.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://raricart.pl">
    <meta property="og:image" content="https://raricart.pl/Logo.png">

    <!-- Styles & Fonts -->
    <link rel="canonical" href="https://raricart.pl">
    <link rel="icon" type="image/png" href="<?php echo get_val('favicon', '/assets/images/logo_optimized.png'); ?>">

    <!-- Critical CSS: Blocking load to prevent Layout Shift (CLS 1.0 fix) -->
    <link rel="stylesheet" href="/assets/css/styles.css?v=<?php echo time(); ?>">

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
    <link rel="preload" href="/assets/images/logo_optimized.png" as="image">

    <!-- Scripts -->

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FoodEstablishment",
      "name": "Raricart Live Food Station",
      "description": "Mobilne stacje degustacyjne na eventy",
      "address": {
        "@type": "PostalAddress",
        "addressCountry": "PL"
      },
      "telephone": "+48883392688",
      "email": "kontakt@raricart.pl",
      "priceRange": "$$",
      "servesCuisine": ["BBQ", "Asian", "Desserts"],
      "url": "https://<?php echo $_SERVER['HTTP_HOST']; ?>"
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
</head>

<?php
$is_home_page = (strpos($_SERVER['SCRIPT_NAME'], 'pakiety.php') === false && strpos($_SERVER['REQUEST_URI'], '/pakiety') === false);
?>
<body <?php echo $is_home_page ? 'class="is-homepage"' : ''; ?>>
