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

        <!-- Hero Section (Nowa) -->
        <section id="hero" class="section section-hero">
            <div class="dlaczego-warto-container"> <!-- using a flex container that already exists in CSS or creating custom -->
                <div class="hero-left-col">
                    <p class="hero-tagline">Mobilne Live Food Station na Twoje wydarzenie</p>
                    <h1 class="hero-main-title">Świeże desery i przekąski przygotowywane na żywo — z pełną obsługą i efektem WOW.</h1>
                    <p class="hero-sub-desc">Wesela • Eventy firmowe • Przyjęcia • Eventy plenerowe</p>
                    <div class="hero-cta-wrapper">
                        <a href="#kontakt" class="hero-cta">Sprawdź dostępność terminu</a>
                        <p class="hero-note">Podaj datę, miejsce i liczbę gości — przygotujemy indywidualną ofertę.</p>
                    </div>
                </div>
                <div class="hero-right-col">
                    <?php 
                        $default_hero_vid = 'https://media.raricart.pl/content/hero.mp4';
                        $hero_vid = function_exists('get_val') ? get_val('hero_video', $default_hero_vid) : $default_hero_vid;
                    ?>
                    <video class="hero-video-bg" autoplay muted loop playsinline preload="auto">
                        <source src="<?php echo htmlspecialchars($hero_vid); ?>" type="video/mp4">
                    </video>
                </div>
            </div>
        </section>

        <!-- Trust Badges -->
        <section class="section-trust">
            <div class="trust-container">
                <div class="trust-item-new">
                    <svg class="trust-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    ŚWIEŻO PRZYGOTOWYWANE
                </div>
                <div class="trust-item-new">
                    <svg class="trust-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    WŁASNE KOMPOZYCJE
                </div>
                <div class="trust-item-new">
                    <svg class="trust-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    PEŁNA OBSŁUGA
                </div>
                <div class="trust-item-new">
                    <svg class="trust-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    MOBILNE STACJE
                </div>
            </div>
        </section>

        <!-- Oferta Intro -->
        <section id="oferta" class="section-offer-intro">
            <div class="premium-overlap-container reverse">
                <div class="premium-text-card" style="margin: 0 auto; text-align: center; max-width: 800px; padding: 4rem 2rem;">
                    <p class="hero-tagline" style="margin-bottom: 1rem;">Wybierz swoją stację</p>
                    <h2 class="premium-title">Nie jesteśmy klasycznym bufetem.</h2>
                    <div class="premium-body">
                        <p>
                            Nasze stacje przygotowują jedzenie na miejscu, na oczach gości. Każdy może wybrać swoje dodatki i stworzyć własną kompozycję.
                        </p>
                        <div class="hero-cta-wrapper" style="margin-top: 2rem;">
                            <a href="#oferta-lista" class="hero-cta" style="background: var(--color-accent-dark); color: white;">Zobacz całą ofertę</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Oferta Grid -->
        <section id="oferta-lista" class="section">
            <h2 data-i18n="offer.title">Nasza Oferta</h2>
            <div class="offer-grid">
                <article class="offer-card" data-offer="pancakes">
                    <div class="offer-image-wrapper">
                         <img src="<?php echo get_val('offer_cards.pancakes', 'https://media.raricart.pl/images/placeholder.webp'); ?>" 
                              alt="Mini Pancakes" loading="lazy" class="offer-image-img">
                    </div>
                    <div class="offer-content">
                        <h3 data-i18n="offer.cards.pancakes.title">MINI PANCAKES</h3>
                        <p data-i18n="offer.cards.pancakes.desc">Ciepłe, puszyste mini pancakes z dodatkami i sosami.</p>
                        <button class="hero-cta" style="margin-top:1rem; padding: 0.8rem 1.5rem; font-size: 0.9rem; cursor:pointer;" onclick="document.querySelector('.offer-card[data-offer=\'pancakes\']').click()">Poznaj stację →</button>
                    </div>
                </article>
                <article class="offer-card" data-offer="icecream">
                    <div class="offer-image-wrapper">
                        <img src="<?php echo get_val('offer_cards.icecream', 'https://media.raricart.pl/images/placeholder.webp'); ?>" 
                             alt="Lody Włoskie" loading="lazy" class="offer-image-img">
                    </div>
                    <div class="offer-content">
                        <h3 data-i18n="offer.cards.icecream.title">LODY WŁOSKIE</h3>
                        <p data-i18n="offer.cards.icecream.desc">Kremowe lody włoskie, świeże owoce, sosy i chrupiące dodatki.</p>
                        <button class="hero-cta" style="margin-top:1rem; padding: 0.8rem 1.5rem; font-size: 0.9rem; cursor:pointer;" onclick="document.querySelector('.offer-card[data-offer=\'icecream\']').click()">Poznaj stację →</button>
                    </div>
                </article>
                <article class="offer-card" data-offer="cheese">
                    <div class="offer-image-wrapper">
                        <img src="<?php echo get_val('offer_cards.cheese', 'https://media.raricart.pl/images/placeholder.webp'); ?>" 
                             alt="Deska Serów" loading="lazy" class="offer-image-img">
                    </div>
                    <div class="offer-content">
                        <h3 data-i18n="offer.cards.cheese.title">DESKA SERÓW</h3>
                        <p data-i18n="offer.cards.cheese.desc">Starannie dobrane sery, wędliny i wytrawne dodatki w eleganckiej oprawie.</p>
                        <button class="hero-cta" style="margin-top:1rem; padding: 0.8rem 1.5rem; font-size: 0.9rem; cursor:pointer;" onclick="document.querySelector('.offer-card[data-offer=\'cheese\']').click()">Poznaj stację →</button>
                    </div>
                </article>
            </div>

        </section>

        <!-- Dlaczego Raricart / Najważniejsza sekcja -->
        <section id="dlaczego" class="section-dlaczego-warto">
            <div class="dlaczego-warto-container">
                <div class="dlaczego-warto-col">
                    <img src="<?php echo get_val('why_us_bg', 'https://media.raricart.pl/images/placeholder.webp'); ?>" alt="Dlaczego my" loading="lazy" class="dlaczego-warto-img">
                </div>
                <div class="dlaczego-warto-col">
                    <p class="hero-tagline">Nie tylko jedzenie. Atrakcja dla Twoich gości.</p>
                    <h2 class="dlaczego-warto-title" style="margin-bottom: 2rem;">Raricart łączy trzy rzeczy:</h2>
                    
                    <h3 style="margin-bottom: 0.5rem; font-family: var(--font-secondary);">SMak</h3>
                    <p class="dlaczego-warto-desc">Świeże produkty przygotowywane i serwowane na miejscu.</p>
                    
                    <h3 style="margin-bottom: 0.5rem; font-family: var(--font-secondary);">DOŚWIADCZENIE</h3>
                    <p class="dlaczego-warto-desc">Goście obserwują przygotowanie i sami wybierają dodatki.</p>
                    
                    <h3 style="margin-bottom: 0.5rem; font-family: var(--font-secondary);">ESTETYKĘ</h3>
                    <p class="dlaczego-warto-desc" style="margin-bottom: 2rem;">Dopracowana stacja staje się częścią aranżacji wydarzenia.</p>
                    
                    <h3 style="margin-bottom: 0.5rem; font-family: var(--font-secondary);">A Ty?</h3>
                    <p class="dlaczego-warto-desc">Nie musisz organizować obsługi, przygotowywać stanowiska ani sprzątać po zakończeniu. Przyjeżdżamy. Przygotowujemy. Serwujemy. Sprzątamy. <strong>Ty zajmujesz się gośćmi. My zajmujemy się stacją.</strong></p>
                    
                    <div class="hero-cta-wrapper" style="margin-top: 2rem;">
                        <a href="#kontakt" class="hero-cta">Chcę taką stację na swoim wydarzeniu</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6 Argumentów -->
        <section class="section" style="background: var(--color-bg-alt); padding: 5rem 2rem;">
            <div style="max-width: 1200px; margin: 0 auto;">
                <h2 style="text-align: center; margin-bottom: 3rem; font-family: var(--font-secondary); font-size: 2.5rem; font-weight:600;">Co zyskujesz, wybierając Raricart?</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <h3 style="margin-bottom: 1rem; color: var(--color-accent-dark);">Świeżość</h3>
                        <p style="color: #555;">Produkty przygotowywane i serwowane na miejscu.</p>
                    </div>
                    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <h3 style="margin-bottom: 1rem; color: var(--color-accent-dark);">Swobodę wyboru</h3>
                        <p style="color: #555;">Goście sami komponują swoje desery lub przekąski.</p>
                    </div>
                    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <h3 style="margin-bottom: 1rem; color: var(--color-accent-dark);">Efekt WOW</h3>
                        <p style="color: #555;">Live cooking przyciąga uwagę i tworzy naturalne miejsce spotkań.</p>
                    </div>
                    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <h3 style="margin-bottom: 1rem; color: var(--color-accent-dark);">Estetykę</h3>
                        <p style="color: #555;">Stacja jest częścią aranżacji, a nie przypadkowym stołem z jedzeniem.</p>
                    </div>
                    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <h3 style="margin-bottom: 1rem; color: var(--color-accent-dark);">Wygodę</h3>
                        <p style="color: #555;">Dojazd, montaż, obsługa, serwis i demontaż są po naszej stronie.</p>
                    </div>
                    <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <h3 style="margin-bottom: 1rem; color: var(--color-accent-dark);">Elastyczność</h3>
                        <p style="color: #555;">Jedna stacja czy kilka — dopasowujemy realizację do Twojego wydarzenia.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Proces / Jak to działa -->
        <section id="proces" class="section-proces">
            <h2 class="dlaczego-warto-title" style="text-align: center; margin-bottom: 3rem;">Jak to wygląda?</h2>
            <div class="proces-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <div class="proces-step">
                    <div class="proces-num">01</div>
                    <h3 class="proces-heading">Opowiadasz nam o wydarzeniu</h3>
                    <p class="proces-desc">Data, miejsce, liczba gości i rodzaj imprezy.</p>
                </div>
                <div class="proces-step">
                    <div class="proces-num">02</div>
                    <h3 class="proces-heading">Dobieramy stację</h3>
                    <p class="proces-desc">Podpowiadamy, które rozwiązanie najlepiej sprawdzi się przy Twojej liczbie gości i charakterze wydarzenia.</p>
                </div>
                <div class="proces-step">
                    <div class="proces-num">03</div>
                    <h3 class="proces-heading">Przyjeżdżamy i przygotowujemy wszystko</h3>
                    <p class="proces-desc">Montaż, produkty, sprzęt i obsługa — wszystko jest po naszej stronie.</p>
                </div>
                <div class="proces-step">
                    <div class="proces-num">04</div>
                    <h3 class="proces-heading">Goście korzystają ze stacji</h3>
                    <p class="proces-desc">Wybierają dodatki, tworzą własne kompozycje i mogą wracać po kolejne porcje.</p>
                </div>
                <div class="proces-step">
                    <div class="proces-num">05</div>
                    <h3 class="proces-heading">My sprzątamy</h3>
                    <p class="proces-desc">Po zakończeniu serwisu demontujemy stanowisko i zostawiamy po sobie porządek.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 3rem;">
                <p style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem;">Prościej się nie da.</p>
                <a href="#kontakt" class="hero-cta">Zapytaj o swój termin</a>
            </div>
        </section>

        <!-- Dla Kogo -->
        <section id="dlakogo" class="section-dlakogo">
            <div class="dlakogo-container">
                <h2 class="dlakogo-title">Gdzie pojawia się Raricart?</h2>
                <div class="dlakogo-grid">
                    <div class="dlakogo-item">
                        <h3 class="dlakogo-heading">WESELA</h3>
                        <p class="dlakogo-desc">Słodka atrakcja, deser po obiedzie, nocna przekąska albo wyjątkowa strefa dla gości.</p>
                    </div>
                    <div class="dlakogo-item">
                        <h3 class="dlakogo-heading">EVENTY FIRMOWE</h3>
                        <p class="dlakogo-desc">Integracje, pikniki, konferencje, gale, jubileusze i premiery.</p>
                    </div>
                    <div class="dlakogo-item">
                        <h3 class="dlakogo-heading">PRZYJĘCIA PRYWATNE</h3>
                        <p class="dlakogo-desc">Urodziny, jubileusze, baby shower i przyjęcia w ogrodzie.</p>
                    </div>
                    <div class="dlakogo-item">
                        <h3 class="dlakogo-heading">EVENTY I AGENCJE</h3>
                        <p class="dlakogo-desc">Kompletna mobilna stacja gastronomiczna jako element realizacji dla klienta.</p>
                    </div>
                </div>
                <div class="dlakogo-footer" style="margin-top: 3rem;">
                    Organizujesz coś innego?<br>
                    <a href="#kontakt" class="hero-cta" style="margin-top: 1rem;">Napisz. Dopasujemy rozwiązanie do Twojego wydarzenia.</a>
                </div>
            </div>
        </section>

        <!-- Realizacje -->
        <section id="realizacje" class="section section-gallery-header">
            <h2 data-i18n="gallery.title" style="text-align: center; font-size: 2.5rem; font-family: var(--font-secondary);">Zobacz Raricart podczas wydarzeń</h2>
        </section>
        
        <section id="realizacje-parallax" class="section section-gallery-parallax" style="<?php if($gallery_bg) echo "--bg-image: url('$gallery_bg');"; ?>">
            <div class="gallery-grid" id="dynamicGalleryGrid">
                <?php
                // Dynamic Gallery Rendering (PHP Side)
                $galleryFiles = [];
                if (file_exists($jsonFile)) {
                    $galleryFiles = json_decode(file_get_contents($jsonFile), true);
                    if (!is_array($galleryFiles)) $galleryFiles = [];
                }
                
                // Fetch max 9 photos
                $galleryFiles = array_slice($galleryFiles, 0, 9);
                
                // Dynamic Columns Logic: Adapt to content size
                $totalImages = count($galleryFiles);
                // If few images, use fewer columns. Max 3 for 9 images looks better.
                $colsCount = ($totalImages > 0) ? max(1, min(3, $totalImages)) : 3;
                
                // Disable parallax effect for small galleries to prevent glitches
                $enableParallax = ($totalImages >= 6);

                $columns = array_fill(0, $colsCount, []);
                foreach ($galleryFiles as $idx => $item) {
                    $src = '';
                    if (is_string($item)) {
                        $src = $item;
                    } elseif (is_array($item)) {
                        $src = $item['url'] ?? $item['src'] ?? '';
                    }

                    if (!empty($src)) {
                        // Distribute among columns
                        $columns[$idx % $colsCount][] = ['src' => $src, 'index' => $idx];
                    }
                }

                foreach ($columns as $cIdx => $colItems):
                    // Add parallax class only if enough content
                    $parallaxClass = ($enableParallax && $cIdx % 2 !== 0) ? 'parallax' : '';
                    ?>
                    <div class="gallery-column <?php echo $parallaxClass; ?>">
                        <?php foreach ($colItems as $item): ?>
                            <img src="<?php echo htmlspecialchars($item['src']); ?>" 
                                 onclick="openGalleryModal(<?php echo $item['index']; ?>)" 
                                 loading="lazy" 
                                 alt="Realizacja Raricart">
                        <?php endforeach; ?>
                        
                        <?php 
                        // Duplicate content for infinite scroll ONLY if parallax is active
                        if ($enableParallax): 
                            foreach ($colItems as $item): ?>
                            <img src="<?php echo htmlspecialchars($item['src']); ?>" 
                                 onclick="openGalleryModal(<?php echo $item['index']; ?>)" 
                                 loading="lazy" 
                                 alt="Realizacja Raricart" aria-hidden="true">
                        <?php endforeach; 
                        endif; 
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 3rem; max-width: 800px; margin-left: auto; margin-right: auto; padding: 0 1rem;">
                <p style="font-size: 1.1rem; color: #555; margin-bottom: 1.5rem;">Tak wygląda stacja, kiedy zaczyna się wydarzenie.<br>Świeże produkty, przygotowanie na żywo, własne kompozycje i goście, którzy naprawdę chcą podejść do stacji.</p>
                <a href="#realizacje" class="hero-cta" style="background: transparent; color: var(--color-accent-dark); border: 2px solid var(--color-accent-dark);">Zobacz więcej realizacji →</a>
            </div>
        </section>


        <!-- O Nas -->
        <section id="onas" class="section-premium">
            <div class="premium-container">
                <div class="premium-col-image">
                    <img src="<?php echo get_val('about_image', 'https://media.raricart.pl/images/placeholder.webp'); ?>"
                        alt="Raricart Live Food Experience" loading="lazy" class="premium-img">
                </div>
                <div class="premium-col-text">
                    <h2 class="premium-title">Cześć, tu Raricart.</h2>
                    
                    <p class="premium-text">
                        Raricart powstało z prostego pomysłu: żeby jedzenie podczas wydarzenia było czymś więcej niż tylko poczęstunkiem.
                    </p>
                    
                    <p class="premium-text">
                        Chciałam stworzyć stacje, które przyciągają ludzi, dają im możliwość wyboru i jednocześnie pięknie wpisują się w charakter wydarzenia. Dlatego każdą realizację traktuję jako połączenie dobrego jedzenia, estetyki i świetnej obsługi.
                    </p>
                    
                    <p class="premium-text">
                        Dziś Raricart pojawia się na weselach, eventach firmowych i prywatnych przyjęciach. A moim celem za każdym razem jest ten sam: <strong>żeby Twoi goście powiedzieli: „Wow, ale to było dobre”.</strong>
                    </p>

                    <div class="hero-cta-wrapper" style="margin-top: 2rem;">
                        <a href="#kontakt" class="hero-cta">Poznaj Raricart</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faq" class="section">
            <h2 style="text-align: center; margin-bottom: 3rem; font-family: var(--font-secondary); font-size: 2.5rem;">Najczęściej Zadawane Pytania</h2>
            
            <article class="faq-item">
                <h3>Ile kosztuje Raricart?</h3>
                <p>Każde wydarzenie wyceniamy indywidualnie, ponieważ cena zależy od liczby gości, wybranych stacji, czasu serwisu i lokalizacji. Podaj nam szczegóły wydarzenia — przygotujemy konkretną, przejrzystą wycenę.</p>
            </article>

            <article class="faq-item">
                <h3>Ile osób może obsłużyć Raricart?</h3>
                <p>Obsługujemy wydarzenia od kilkudziesięciu do kilkuset osób. Dopasowujemy przepustowość stacji i liczbę obsługi do wielkości Twojego eventu, by serwis przebiegał płynnie i bez długich kolejek.</p>
            </article>

            <article class="faq-item">
                <h3>Czy stacja może pojawić się na weselu?</h3>
                <p>Tak! Raricart to świetna alternatywa dla klasycznego słodkiego stołu. Często pojawiamy się po obiedzie, w trakcie oczepin lub jako nocna atrakcja, tworząc efekt WOW i punkt spotkań dla gości.</p>
            </article>
            
            <article class="faq-item">
                <h3>Czy dojeżdżacie poza Śląsk?</h3>
                <p>Tak, działamy mobilnie i dojeżdżamy na wydarzenia w całej Polsce. Dojazd jest wyceniany indywidualnie na podstawie lokalizacji.</p>
            </article>

            <article class="faq-item">
                <h3>Czy można połączyć kilka stacji?</h3>
                <p>Zdecydowanie. Bardzo często łączymy np. Mini Pancakes i Lody Włoskie obok siebie, co pozwala zaspokoić różne gusta i buduje jeszcze bardziej okazałą strefę gastronomiczną.</p>
            </article>

            <article class="faq-item">
                <h3>Ile miejsca potrzebujecie?</h3>
                <p>Pojedyncza stacja jest kompaktowa — zazwyczaj potrzebujemy około 2x2 metry przestrzeni oraz dostępu do standardowego gniazdka prądowego (230V).</p>
            </article>

            <article class="faq-item">
                <h3>Czy zapewniacie obsługę?</h3>
                <p>Tak. Nasza usługa jest zawsze kompleksowa. Przyjeżdżamy z własną, profesjonalną obsługą, która na żywo przygotowuje i wydaje porcje Twoim gościom.</p>
            </article>

            <article class="faq-item">
                <h3>Jak wygląda rezerwacja terminu?</h3>
                <p>Wystarczy wypełnić krótki formularz poniżej. Sprawdzimy dostępność terminu i wyślemy Ci wycenę. Po akceptacji podpisujemy umowę i rezerwujemy datę.</p>
            </article>
        </section>

        <!-- Kontakt -->
        <section id="kontakt" class="section section-gallery-parallax" style="--bg-image: url('https://media.raricart.pl/images/placeholder.webp'); padding: 6rem 2rem; color: white; position: relative;">
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7);"></div>
            <div style="position: relative; z-index: 1; max-width: 800px; margin: 0 auto; text-align: center;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem; color: white; font-family: var(--font-secondary);">Masz wydarzenie?</h2>
                <h3 style="font-size: 2rem; margin-bottom: 2rem; color: var(--color-accent); font-family: var(--font-secondary);">Zróbmy na nim coś pysznego.</h3>
                <p style="font-size: 1.1rem; margin-bottom: 3rem; color: #eee;">Podaj nam datę, miejsce i liczbę gości. Sprawdzimy dostępność i przygotujemy dla Ciebie indywidualną ofertę. Odpowiemy z informacją o dostępności i propozycją dopasowaną do Twojego wydarzenia.</p>
            </div>
            
            <div style="position: relative; z-index: 1; background: white; padding: 3rem; border-radius: 12px; max-width: 800px; margin: 0 auto; color: var(--color-text);">
                <form id="form" class="contact-form" novalidate>
                    <div id="availability-notice" class="availability-notice" style="display:none"></div>
                    <!-- Honeypot for bots -->
                    <input type="text" name="website_check" class="honeypot-input" tabindex="-1" autocomplete="off">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Imię i Nazwisko *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Telefon *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="date">Data wydarzenia *</label>
                            <input type="date" id="date" name="date" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="location">Miejsce wydarzenia *</label>
                            <input type="text" id="location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="guests">Liczba gości *</label>
                            <input type="number" id="guests" name="guests" min="1" max="9999" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="event_type">Rodzaj wydarzenia *</label>
                        <select id="event_type" name="event_type" required>
                            <option value="">Wybierz...</option>
                            <option value="wedding">Wesele</option>
                            <option value="corporate">Event Firmowy</option>
                            <option value="private">Przyjęcie Prywatne</option>
                            <option value="other">Inne</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label>Która stacja Cię interesuje?</label>
                        <div class="checkbox-group">
                            <div class="checkbox-item"><input type="checkbox" id="p" name="stations" value="pancakes"><label for="p">Mini Pancakes</label></div>
                            <div class="checkbox-item"><input type="checkbox" id="l" name="stations" value="lody"><label for="l">Lody Włoskie</label></div>
                            <div class="checkbox-item"><input type="checkbox" id="s" name="stations" value="sery"><label for="s">Deska Serów</label></div>
                            <div class="checkbox-item"><input type="checkbox" id="u" name="stations" value="nie_wiem"><label for="u">Jeszcze nie wiem</label></div>
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label for="message">Dodatkowe informacje</label>
                        <textarea id="message" name="message" rows="4"></textarea>
                    </div>

                    <button type="submit" class="hero-cta" style="width: 100%; margin-top: 1rem; border: none; cursor: pointer;">SPRAWDŹ DOSTĘPNOŚĆ I OTRZYMAJ WYCENĘ</button>
                    <p class="form-note" style="text-align: center; margin-top: 1rem; font-size: 0.8rem; color: #666;">
                        Przesyłając formularz, wyrażasz zgodę na kontakt w celu obsługi zapytania.
                    </p>
                </form>
            </div>
        </section>

    </main>

<?php
// 3. FOOTER
include 'parts/footer.php';

// 4. MODALS
include 'parts/modals.php';

// Widżet pływający Zapytaj o wycenę
include 'parts/contact-button.php';
?>
<!-- efweg -->