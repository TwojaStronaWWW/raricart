<?php
// Security & Production config
ini_set('display_errors', 0);
error_reporting(E_ALL);
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

// Prevent PHP / LiteSpeed Caching - USUNIĘTE DLA OPTYMALIZACJI
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
// header("X-LiteSpeed-Cache-Control: no-cache"); 
// header("Clear-Site-Data: \"cache\"");

clearstatcache();

$jsonFile = __DIR__ . '/assets/data/gallery.json';

// Load dynamic content on server side to prevent flickering
$content_file = __DIR__ . '/assets/data/content.json';
$content = [];
if (file_exists($content_file)) {
    $content = json_decode(file_get_contents($content_file), true);
    if (!is_array($content)) $content = [];
}

function get_val($key, $default) {
    global $content;
    
    // Obsługa zagnieżdżonych kluczy (np. offer_cards.pancakes)
    $keys = explode('.', $key);
    $val = $content;
    
    foreach ($keys as $k) {
        if (is_array($val) && isset($val[$k])) {
            $val = $val[$k];
        } else {
            return $default;
        }
    }
    
    // Cache busting oparty na czasie modyfikacji pliku lokalnego
    if ($val !== $default && strpos($val, '?') === false) {
        $local_path = __DIR__ . '/' . ltrim($val, '/');
        if (file_exists($local_path)) {
            $val .= '?v=' . filemtime($local_path);
        }
    }
    return $val;
}

// Parallax backgrounds (CSS injection)
$gallery_bg = get_val('gallery_bg', '');
$why_us_bg = get_val('why_us_bg', '');

// 1. HEAD
include 'parts/head.php';

// 2. NAVBAR (Includes Video BG)
include 'parts/navbar.php';
?>

    <main class="content">

        <!-- O Nas -->
        <!-- O Nas -->
        <section id="onas" class="section-premium">
            <div class="premium-container">
                <div class="premium-col-image">
                    <img src="<?php echo get_val('about_image', '/assets/images/about_experience.webp'); ?>"
                        alt="Raricart Live Food Experience - Goście cieszący się wydarzeniem" loading="lazy" class="premium-img">
                </div>
                <div class="premium-col-text">
                    <h2 data-i18n="about.title" class="premium-title">Nie tworzymy cateringu, lecz doświadczenie.</h2>
                    
                    <p class="premium-text" data-i18n="about.p1">
                        Tworzymy mobilne stacje degustacyjne, które stają się ozdobą każdego wydarzenia. 
                        To nie tylko jedzenie, to <span class="highlight">subtelny, dopracowany i&nbsp;pełen charakteru</span> element scenografii.
                    </p>
                    
                    <p class="premium-text" data-i18n="about.p2">
                        Serwujemy lekkie, świeże kompozycje, od puszystych mini pancakes, przez autentyczne włoskie lody, 
                        aż po aromatyczne deski serów. Budujemy atmosferę klasy i&nbsp;swobody, w&nbsp;której Twoi goście poczują się wyjątkowo.
                    </p>

                    <div class="premium-footer">
                        <span class="premium-line"></span>
                        <span class="premium-label">Live Food Station</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Oferta Intro -->
        <section id="oferta" class="section-offer-intro">
            <div class="premium-overlap-container reverse">
                <div class="premium-image-box">
                    <img src="<?php echo get_val('offer_main_image', '/assets/images/offer_main.webp'); ?>"
                        alt="Mobilna stacja gastronomiczna Raricart" loading="lazy" class="premium-image">
                </div>
                <div class="premium-text-card">
                    <h2 data-i18n="offer.intro_title" class="premium-title">NIE GOTUJEMY DAŃ, TWORZYMY CHWILE, KTÓRE ŁĄCZĄ LUDZI</h2>
                    <div class="premium-body">
                        <p data-i18n="offer.p1">
                            Nasze stacje stają się miejscem rozmów, uśmiechów i&nbsp;zdjęć, a&nbsp;my dbamy o&nbsp;każdy szczegół, od montażu po ostatni serwis, abyś mógł cieszyć się wydarzeniem tak samo jak Twoi goście.
                        </p>
                        <p data-i18n="offer.p2">
                            Obsługujemy eventy firmowe, wesela, gale i&nbsp;prywatne przyjęcia, współpracując zarówno z&nbsp;agencjami, jak i&nbsp;klientami indywidualnymi.
                        </p>
                        <p data-i18n="offer.p3">
                            W&nbsp;każdym projekcie kierujemy się zasadą, że smak i&nbsp;estetyka mają tę samą wartość, razem tworzą atmosferę, której nikt nie zapomina. Z&nbsp;Raricart zyskujesz nie tylko catering, ale spójny, piękny element scenografii Twojego wydarzenia, który smakuje tak dobrze, jak wygląda.
                        </p>
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
                         <img src="<?php echo get_val('offer_cards.pancakes', '/assets/images/placeholder.webp'); ?>" 
                              alt="Mini Pancakes" loading="lazy" class="offer-image-img">
                    </div>
                    <div class="offer-content">
                        <h3 data-i18n="offer.cards.pancakes.title">Mini Pancakes</h3>
                        <p data-i18n="offer.cards.pancakes.desc">Słodka stacja, która angażuje gości i&nbsp;staje
                            się sercem wydarzenia.</p>
                    </div>
                </article>
                <article class="offer-card" data-offer="icecream">
                    <div class="offer-image-wrapper">
                        <img src="<?php echo get_val('offer_cards.icecream', '/assets/images/placeholder.webp'); ?>" 
                             alt="Lody Włoskie" loading="lazy" class="offer-image-img">
                    </div>
                    <div class="offer-content">
                        <h3 data-i18n="offer.cards.icecream.title">Lody Włoskie</h3>
                        <p data-i18n="offer.cards.icecream.desc">Orzeźwiająca stacja, która zachwyca gości
                            i&nbsp;buduje atmosferę.</p>
                    </div>
                </article>
                <article class="offer-card" data-offer="cheese">
                    <div class="offer-image-wrapper">
                        <img src="<?php echo get_val('offer_cards.cheese', '/assets/images/placeholder.webp'); ?>" 
                             alt="Deska Serów" loading="lazy" class="offer-image-img">
                    </div>
                    <div class="offer-content">
                        <h3 data-i18n="offer.cards.cheese.title">Deska Serów</h3>
                        <p data-i18n="offer.cards.cheese.desc">Fascynująca strefa smaku z&nbsp;włoskimi serami
                            i&nbsp;wędlinami.</p>
                    </div>
                </article>
            </div>

        </section>

        <!-- Realizacje -->
        <section id="realizacje" class="section" style="padding-top: 2rem; padding-bottom: 0;">
            <h2 data-i18n="gallery.title">GALERIA REALIZACJI</h2>
        </section>
        
        <section id="realizacje-parallax" class="section" style="<?php if($gallery_bg) echo "--bg-image: url('$gallery_bg');"; ?> padding-top: 0;">
            <div class="gallery-grid" id="dynamicGalleryGrid">
                <?php
                // Dynamic Gallery Rendering (PHP Side)
                $galleryFiles = [];
                if (file_exists($jsonFile)) {
                    $galleryFiles = json_decode(file_get_contents($jsonFile), true);
                    if (!is_array($galleryFiles)) $galleryFiles = [];
                }
                
                // Fallback removed - JSON is the single source of truth

                // Dynamic Columns Logic: Adapt to content size
                $totalImages = count($galleryFiles);
                // If few images, use fewer columns. Max 5.
                $colsCount = ($totalImages > 0) ? max(1, min(5, $totalImages)) : 5;
                
                // Disable parallax effect for small galleries to prevent glitches
                $enableParallax = ($totalImages >= 5);

                $columns = array_fill(0, $colsCount, []);
                foreach ($galleryFiles as $idx => $item) {
                    $src = '';
                    if (is_string($item)) {
                        $src = $item;
                    } elseif (is_array($item) && isset($item['src'])) {
                        $src = $item['src'];
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
        </section>

        <!-- FAQ -->
        <section id="faq" class="section">
            <h2 data-i18n="faq.title">FAQ - Najczęściej Zadawane Pytania</h2>
            <article class="faq-item">
                <h3 data-i18n="faq.q1.title">Czy jest ograniczona ilość porcji na osobę?</h3>
                <p data-i18n="faq.q1.desc">Nie, nie ma żadnych limitów! Goście mogą sięgać po świeże porcje ile tylko chcą. Nasze live food station to obfitość smaków przygotowywanych na żywo.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q2.title">Czy można przedłużyć czas trwania usługi?</h3>
                <p data-i18n="faq.q2.desc">Oczywiście! Elastyczność to nasza specjalność. Możesz przedłużyć usługę wcześniej, ustalając szczegóły, lub spontanicznie w trakcie eventu.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q3.title">W którym momencie wydarzenia najlepiej skorzystać ze stoiska Raricart?</h3>
                <p data-i18n="faq.q3.desc">Wybór należy do Ciebie - my idealnie się dopasujemy! Najczęściej stawiamy stoiska jako atrakcję na początek, podczas przerwy koktajlowej lub na deserowy finisz.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q4.title">Jak zarezerwować usługę Raricart?</h3>
                <p data-i18n="faq.q4.desc">To proste: skontaktuj się z nami przez formularz na stronie, e-mail lub telefon. Opowiedz o evencie, a w 24h prześlemy spersonalizowaną ofertę z menu i dostępnością. Rezerwacja z lekkim sercem!</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q5.title">Co jest potrzebne, by Raricart pojawiło się na Twoim evencie?</h3>
                <p data-i18n="faq.q5.desc">Tylko miejsce na nasze eleganckie stoisko (ok. 3x3m) i&nbsp;gniazdko prądu. Resztę załatwiamy my: dojazd, montaż, pełną obsługę, demontaż i&nbsp;sprzątanie. Zero zmartwień dla Ciebie.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q6.title">Jakie są ceny usług Raricart?</h3>
                <p data-i18n="faq.q6.desc">Ceny są elastyczne i&nbsp;zależą od menu, liczby gości oraz czasu trwania – od 150 zł/os. wzwyż dla premium live stations. Wyślij zapytanie, a&nbsp;przygotujemy transparentną wycenę.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q7.title">Czy obsługujecie eventy plenerowe i bez kuchni na miejscu?</h3>
                <p data-i18n="faq.q7.desc">Tak, jesteśmy mobilni na 100%! Dojedziemy wszędzie - na wesela w ogrodzie, firmowe pikniki czy gale pod chmurką. Bez zaplecza kuchennego? Żaden problem, nasze stoiska to kompletna, samodzielna magia kulinarna.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q8.title">Ile gości minimalnie obsługujecie?</h3>
                <p data-i18n="faq.q8.desc">Nie ma minimum – realizujemy zlecenia na każdą skalę! Od kameralnych imprez prywatnych (20+ osób) po duże eventy (500+). Dla mniejszych grup skalujemy jedno eleganckie stoisko z pełnym efektem "wow". Przy większych imprezach zalecamy więcej niż jedno stoisko – to poprawia jakość obsługi, skraca czas oczekiwania i minimalizuje kolejki.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q9.title">Jak zapewniacie higienę i&nbsp;bezpieczeństwo?</h3>
                <p data-i18n="faq.q9.desc">Jesteśmy certyfikowani (HACCP, Sanepid), z&nbsp;pełnym protokołem higieny na żywo. Świeże składniki, sterylne narzędzia i&nbsp;doświadczona obsługa.</p>
            </article>
        </section>

        <!-- Dlaczego My -->
        <section id="dlaczego" class="section section-fullwidth">
            <div class="parallax-why" style="<?php if($why_us_bg) echo "--bg-image: url('$why_us_bg');"; ?>">
                <h2 data-i18n="why.title">SZTUKA KULINARNYCH DOŚWIADCZEŃ</h2>
                <div class="parallax-content">
                    <div class="parallax-card">
                        <h3 data-i18n="why.cards.1.title">EFEKT „WOW" Z KLASĄ</h3>
                        <p data-i18n="why.cards.1.desc">Na oczach gości serwujemy ciepłe pancakes prosto z&nbsp;patelni,
                            nalewamy cremowe lody i
                            komponujemy deski serów - wszystko świeże, personalizowane i&nbsp;dopracowane w&nbsp;detalach. Proces
                            live station przyciąga uwagę, integruje uczestników i&nbsp;tworzy naturalne punkty spotkań, gdzie
                            przy apetycznym widoku rodzą się rozmowy. Elegancki, mobilny design stacji podnosi prestiż
                            wydarzenia, łącząc estetykę premium z&nbsp;czystą przyjemnością dla zmysłów.</p>
                    </div>
                    <div class="parallax-card">
                        <h3 data-i18n="why.cards.2.title">MNIEJ LOGISTYKI, WIĘCEJ SPOKOJU</h3>
                        <p data-i18n="why.cards.2.desc">Raricart przejmuje całość: dojazd, montaż stacji, serwowanie
                            podczas eventu, demontaż i
                            perfekcyjny porządek po zakończeniu. Nie wymagamy zaplecza kuchennego - mobilne stacje
                            działają wszędzie: w&nbsp;loftach, ogrodach, halach czy nietypowych przestrzeniach eventowych.
                            Zespół synchronizuje serwis z&nbsp;harmonogramem, dba o&nbsp;płynny przepływ gości i&nbsp;minimalizuje
                            kolejki.</p>
                    </div>
                    <div class="parallax-card">
                        <h3 data-i18n="why.cards.3.title">DOŚWIADCZENIE ZAMIAST BUFETU</h3>
                        <p data-i18n="why.cards.3.desc">W odróżnieniu od statycznego bufetu, nasze live stations
                            angażują: goście obserwują nalewanie
                            lodów, układanie pancakes i komponowanie desek serów, wybierając dodatki na bieżąco.
                            Wszystko serwowane porcjami „tu i teraz" - świeże, bez marnowania, idealnie dopasowane do
                            liczby i preferencji uczestników. Tematyczne stacje stają się magnesem na gości, budując
                            emocje i niezapomniane wspomnienia.</p>
                    </div>
                    <div class="parallax-card">
                        <h3 data-i18n="why.cards.4.title">BEZPIECZEŃSTWO, JAKOŚĆ, ESTETYKA</h3>
                        <p data-i18n="why.cards.4.desc">Przestrzegamy rygorystycznych standardów higieny i
                            bezpieczeństwa żywności, z naciskiem na
                            świeżość składników i perfekcyjną prezencję. Używamy wyselekcjonowanych produktów
                            serwowanych w optymalnej temperaturze. Każdy detal - od aranżacji stacji, przez zastawę, po
                            pracę zespołu - tworzy spójną scenografię, wzmacniającą wizerunek Twojego wydarzenia.</p>
                    </div>
                    <div class="parallax-card">
                        <h3 data-i18n="why.cards.5.title">PARTNER DLA WYMAGAJĄCYCH</h3>
                        <p data-i18n="why.cards.5.desc">Agencje eventowe zyskują niezawodnego partnera rozumiejącego
                            timing, layout i dynamikę dużych
                            wydarzeń. Firmy, pary młode i organizatorzy prywatnych imprez otrzymują rozwiązanie premium:
                            efekt „wow", emocje i pełną opiekę nad gośćmi. Właściciele lokali eventowych wzbogacają
                            ofertę o mobilne stacje bez inwestycji w sprzęt – gotowe do działania w dowolnej
                            przestrzeni.</p>
                    </div>
                    <div class="parallax-card">
                        <h3 data-i18n="why.cards.6.title">NAPISZ DO NAS</h3>
                        <p data-i18n="why.cards.6.desc">Twój event zasługuje na wyjątkowe live food station, które
                            stanie się jego wizytówką. Napisz
                            do nas już dziś - dopasujemy ofertę do Twojej wizji i zapewnimy termin. Razem stworzymy
                            doświadczenie, które goście będą wspominać z zachwytem!</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kontakt -->
        <section id="kontakt" class="section">
            <h2 data-i18n="contact.title.short">Opowiedz nam o&nbsp;swoim wydarzeniu.<br>Przygotujemy indywidualną wycenę.</h2>
            <div class="progress-container">
                <div id="form-progress"></div>
            </div>
            <p id="progress-text" style="text-align: center; margin-bottom: 2rem; color: #666; font-size: 0.9rem;"
                data-i18n="form.progress_text">
                Uzupełnij dane, abyśmy mogli przygotować ofertę (0%)</p>
            <form id="form" class="contact-form" novalidate>
                <div id="availability-notice" class="availability-notice" style="display:none"></div>
                <!-- Honeypot for bots -->
                <input type="text" name="website_check" style="display:none !important" tabindex="-1"
                    autocomplete="off">

                <div class="form-row">
                    <div class="form-group">
                        <label for="name" data-i18n="form.name">Imię i Nazwisko *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email" data-i18n="form.email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone" data-i18n="form.phone">Telefon *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="date" data-i18n="form.date">Data Wydarzenia *</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="guests" data-i18n="form.guests_label">Liczba Gości *</label>
                    <select id="guests" name="guests" required>
                        <option value="" data-i18n="form.select_placeholder">Wybierz...</option>
                        <option value="20-50">20-50 osób</option>
                        <option value="50-100">50-100 osób</option>
                        <option value="100-200">100-200 osób</option>
                        <option value="200+">Powyżej 200 osób</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="budget" data-i18n="form.budget">Budżet *</label>
                    <select id="budget" name="budget" required>
                        <option value="" data-i18n="form.select_placeholder">Wybierz...</option>
                        <option value="5-10k">5 000 - 10 000 PLN</option>
                        <option value="10-20k">10 000 - 20 000 PLN</option>
                        <option value="20-30k">20 000 - 30 000 PLN</option>
                        <option value="30k+">Powyżej 30 000 PLN</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="event_type" data-i18n="form.event_type">Rodzaj Wydarzenia *</label>
                    <select id="event_type" name="event_type" required>
                        <option value="" data-i18n="form.select_placeholder">Wybierz...</option>
                        <option value="wedding" data-i18n="form.types.wedding">Wesele</option>
                        <option value="corporate" data-i18n="form.types.corporate">Event Firmowy</option>
                        <option value="festival" data-i18n="form.types.festival">Festiwal/Piknik</option>
                        <option value="private" data-i18n="form.types.private">Przyjęcie Prywatne</option>
                <option value="other" data-i18n="form.types.other">Inne</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label data-i18n="form.stations">Interesujące Stacje *</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item"><input type="checkbox" id="p" name="stations" value="pancakes"><label
                                for="p" data-i18n="form.st_pancakes">Mini Pancakes</label></div>
                        <div class="checkbox-item"><input type="checkbox" id="l" name="stations" value="lody"><label
                                for="l" data-i18n="form.st_icecream">Lody Włoskie</label></div>
                        <div class="checkbox-item"><input type="checkbox" id="s" name="stations" value="sery"><label
                                for="s" data-i18n="form.st_cheese">Deska Serów</label></div>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label for="message" data-i18n="form.message">Dodatkowe Informacje</label>
                    <textarea id="message" name="message" rows="4"
                        placeholder="Opisz swoje potrzeby, pytania lub preferencje..."
                        data-i18n-placeholder="form.message_placeholder"></textarea>
                </div>
                <button type="submit" class="cta-primary" data-i18n="form.submit">Wyślij Zapytanie</button>
            </form>
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