<?php
// Security & Production config
ini_set('display_errors', 0);
error_reporting(E_ALL);
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

// Prevent PHP / LiteSpeed Caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("X-LiteSpeed-Cache-Control: no-cache"); // Specific for LH.pl / LiteSpeed
header("Clear-Site-Data: \"cache\""); // Instruct browser to clear its cache

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
    $val = $content[$key] ?? $default;
    // Cache busting oparty na czasie modyfikacji pliku lokalnego (jeśli to ścieżka lokalna)
    if ($val !== $default && strpos($val, '?') === false) {
        $local_path = __DIR__ . '/' . ltrim($val, '/');
        if (file_exists($local_path)) {
            $val .= '?v=' . filemtime($local_path);
        } else {
            // Jeśli to zewnętrzny URL lub plik nie istnieje, nie dodajemy time() który wymusza pobranie co sekundę
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
                    <img src="<?php echo get_val('about_image', 'https://images.unsplash.com/photo-1566737236500-c8ac43014a67?w=1200'); ?>"
                        alt="Raricart Live Food Experience - Goście cieszący się wydarzeniem" loading="lazy" class="premium-img">
                </div>
                <div class="premium-col-text">
                    <h2 data-i18n="about.title" class="premium-title">Nie tworzymy cateringu, lecz doświadczenie.</h2>
                    
                    <p class="premium-text" data-i18n="about.p1">
                        Tworzymy mobilne stacje degustacyjne, które stają się ozdobą każdego wydarzenia. 
                        To nie tylko jedzenie, to <span class="highlight">subtelny, dopracowany i pełen charakteru</span> element scenografii.
                    </p>
                    
                    <p class="premium-text" data-i18n="about.p2">
                        Serwujemy lekkie, świeże kompozycje, od puszystych mini pancakes, przez autentyczne włoskie lody, 
                        aż po aromatyczne deski serów. Budujemy atmosferę klasy i swobody, w której Twoi goście poczują się wyjątkowo.
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
                    <img src="<?php echo get_val('offer_main_image', 'assets/images/offer_main.webp'); ?>"
                        alt="Mobilna stacja gastronomiczna Raricart" loading="lazy" class="premium-image">
                </div>
                <div class="premium-text-card">
                    <h2 data-i18n="offer.intro_title" class="premium-title">NIE GOTUJEMY DAŃ, TWORZYMY CHWILE, KTÓRE ŁĄCZĄ LUDZI</h2>
                    <div class="premium-body">
                        <p data-i18n="offer.p1">
                            Nasze stacje stają się miejscem rozmów, uśmiechów i zdjęć, a my dbamy o każdy szczegół, od montażu po ostatni serwis, abyś mógł cieszyć się wydarzeniem tak samo jak Twoi goście.
                        </p>
                        <p data-i18n="offer.p2">
                            Obsługujemy eventy firmowe, wesela, gale i prywatne przyjęcia, współpracując zarówno z agencjami, jak i klientami indywidualnymi.
                        </p>
                        <p data-i18n="offer.p3">
                            W każdym projekcie kierujemy się zasadą, że smak i estetyka mają tę samą wartość, razem tworzą atmosferę, której nikt nie zapomina. Z Raricart zyskujesz nie tylko catering, ale spójny, piękny element scenografii Twojego wydarzenia, który smakuje tak dobrze, jak wygląda.
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
                    <div class="offer-image"
                        style="background-image: url('<?php echo get_val('offer_cards.pancakes', 'https://images.unsplash.com/photo-1598214886806-c87b84b7078b?w=800'); ?>');"
                        role="img" aria-label="Mini Pancakes"></div>
                    <div class="offer-content">
                        <h3 data-i18n="offer.cards.pancakes.title">Mini Pancakes</h3>
                        <p data-i18n="offer.cards.pancakes.desc">Słodka stacja, która angażuje gości i&nbsp;staje
                            się sercem wydarzenia.</p>
                    </div>
                </article>
                <article class="offer-card" data-offer="icecream">
                    <div class="offer-image"
                        style="background-image: url('<?php echo get_val('offer_cards.icecream', 'https://images.unsplash.com/photo-1560008581-09826d1de69e?w=800'); ?>');"
                        role="img" aria-label="Lody"></div>
                    <div class="offer-content">
                        <h3 data-i18n="offer.cards.icecream.title">Lody Włoskie</h3>
                        <p data-i18n="offer.cards.icecream.desc">Orzeźwiająca stacja, która zachwyca gości
                            i&nbsp;buduje atmosferę.</p>
                    </div>
                </article>
                <article class="offer-card" data-offer="cheese">
                    <div class="offer-image"
                        style="background-image: url('<?php echo get_val('offer_cards.cheese', 'https://images.unsplash.com/photo-1631379578550-7038263db699?w=800'); ?>');"
                        role="img" aria-label="Deska Serów"></div>
                    <div class="offer-content">
                        <h3 data-i18n="offer.cards.cheese.title">Deska Serów</h3>
                        <p data-i18n="offer.cards.cheese.desc">Fascynująca strefa smaku z&nbsp;włoskimi serami
                            i&nbsp;wędlinami.</p>
                    </div>
                </article>
            </div>

        </section>

        <!-- Realizacje -->
        <section id="realizacje" class="section">
            <h2 data-i18n="gallery.title">GALERIA REALIZACJI</h2>
            <div class="gallery-grid" id="dynamicGalleryGrid">
                <?php
                // Dynamic Gallery Rendering (PHP Side)
                $galleryFiles = [];
                if (file_exists($jsonFile)) {
                    $galleryFiles = json_decode(file_get_contents($jsonFile), true);
                    if (!is_array($galleryFiles)) $galleryFiles = [];
                }
                
                // Fallback if JSON empty
                if (empty($galleryFiles)) {
                    $galDir = __DIR__ . '/assets/gallery/';
                    if (is_dir($galDir)) {
                        $files = scandir($galDir);
                        foreach ($files as $f) {
                            if ($f !== '.' && $f !== '..') {
                                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                                    $galleryFiles[] = 'assets/gallery/' . $f;
                                }
                            }
                        }
                    }
                }

                $colsCount = 5;
                $columns = array_fill(0, $colsCount, []);
                foreach ($galleryFiles as $idx => $src) {
                    $columns[$idx % $colsCount][] = ['src' => $src, 'index' => $idx];
                }

                foreach ($columns as $cIdx => $colItems):
                    $isParallax = ($cIdx % 2 !== 0) ? 'parallax' : 'static';
                ?>
                    <div class="gallery-column <?php echo $isParallax; ?>">
                        <?php foreach ($colItems as $item): ?>
                            <div class="gallery-item" data-index="<?php echo $item['index']; ?>">
                                <img src="<?php echo $item['src']; ?>" alt="Realizacja <?php echo $item['index'] + 1; ?>" loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faq" class="section">
            <h2 data-i18n="faq.title">FAQ - Najczęściej Zadawane Pytania</h2>
            <article class="faq-item">
                <h3 data-i18n="faq.q1.title">Jak daleko możecie dojechać z Waszą "Droną Eventową"?</h3>
                <p data-i18n="faq.q1.desc">Obsługujemy wydarzenia w całej Polsce, a w przypadku większych imprez,
                    jesteśmy otwarci na wyjazdy
                    zagraniczne. Nasze mobilne stanowiska pozwalają nam dotrzeć do niemal każdej, nawet najbardziej
                    wymagającej lokalizacji – od centrum miasta po odległe tereny piknikowe.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q2.title">Czy oferujecie opcje wegetariańskie, wegańskie lub bezglutenowe?</h3>
                <p data-i18n="faq.q2.desc">Tak! Nasze menu jest w pełni elastyczne i dostosowujemy je do wymagań
                    dietetycznych gości. W trakcie
                    planowania menu z Klientem, zawsze proponujemy szeroki wybór opcji wege, wegańskich oraz dań bez
                    glutenu, używając specjalnie dedykowanych składników.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q3.title">Jakie są wymagania techniczne na miejscu eventu?</h3>
                <p data-i18n="faq.q3.desc">Nasze mobilne stacje są w dużej mierze samowystarczalne. Potrzebujemy jedynie
                    stabilnej, równej
                    powierzchni. Dostęp do prądu jest mile widziany, ale posiadamy własne ciche generatory, które możemy
                    uruchomić w razie potrzeby.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q4.title">Jaki jest minimalny czas rezerwacji?</h3>
                <p data-i18n="faq.q4.desc">Zalecamy rezerwację na minimum 2-3 miesiące przed planowaną datą, szczególnie
                    w sezonie letnim, aby
                    zagwarantować dostępność terminów i najlepszych składników. Oczywiście, w miarę możliwości
                    obsługujemy także last minute!</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q5.title">Czy zapewniacie obsługę kelnerską?</h3>
                <p data-i18n="faq.q5.desc">Tak! W ramach kompleksowej obsługi zapewniamy profesjonalny zespół kelnerski,
                    który zadba o komfort
                    Twoich gości. Nasi kelnerzy są doświadczeni w obsłudze eventów różnego typu i gwarantują najwyższy
                    poziom usług.</p>
            </article>
            <article class="faq-item">
                <h3 data-i18n="faq.q6.title">Ile osób możecie obsłużyć jednocześnie?</h3>
                <p data-i18n="faq.q6.desc">Nasze mobilne stacje są skalowalne – od kameralnych spotkań dla 20 osób, po
                    wielkie eventy dla
                    kilkuset gości. W zależności od wielkości wydarzenia, dostosowujemy liczbę stanowisk i personelu,
                    aby zapewnić płynną obsługę bez kolejek.</p>
            </article>
        </section>

        <!-- Dlaczego My -->
        <section id="dlaczego" class="section section-fullwidth">
            <div class="parallax-why" style="<?php if($why_us_bg) echo "background-image: url('$why_us_bg');"; ?>">
                <h2 data-i18n="why.title">SZTUKA KULINARNYCH DOŚWIADCZEŃ</h2>
                <div class="parallax-content">
                    <div class="parallax-card">
                        <h3 data-i18n="why.cards.1.title">EFEKT „WOW" Z KLASĄ</h3>
                        <p data-i18n="why.cards.1.desc">Na oczach gości serwujemy ciepłe pancakes prosto z patelni,
                            nalewamy cremowe lody i
                            komponujemy deski serów - wszystko świeże, personalizowane i dopracowane w detalach. Proces
                            live station przyciąga uwagę, integruje uczestników i tworzy naturalne punkty spotkań, gdzie
                            przy apetycznym widoku rodzą się rozmowy. Elegancki, mobilny design stacji podnosi prestiż
                            wydarzenia, łącząc estetykę premium z czystą przyjemnością dla zmysłów.</p>
                    </div>
                    <div class="parallax-card">
                        <h3 data-i18n="why.cards.2.title">MNIEJ LOGISTYKI, WIĘCEJ SPOKOJU</h3>
                        <p data-i18n="why.cards.2.desc">Raricart przejmuje całość: dojazd, montaż stacji, serwowanie
                            podczas eventu, demontaż i
                            perfekcyjny porządek po zakończeniu. Nie wymagamy zaplecza kuchennego - mobilne stacje
                            działają wszędzie: w loftach, ogrodach, halach czy nietypowych przestrzeniach eventowych.
                            Zespół synchronizuje serwis z harmonogramem, dba o płynny przepływ gości i minimalizuje
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
            <h2 data-i18n="contact.title">Zapytaj o dostępność terminu<br>i stwórzmy razem strefę smaku,<br>o której
                Twoi goście długo nie
                zapomną.</h2>
            <div class="progress-container">
                <div id="form-progress"></div>
            </div>
            <p id="progress-text" style="text-align: center; margin-bottom: 1rem; color: #666; font-size: 0.9rem;"
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

// 4. MODALS (Moved outside footer to ensure top Z-Index)
include 'parts/modals.php';
?>



<!-- tesr p-->