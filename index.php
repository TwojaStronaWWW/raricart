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

        <!-- Kontakt -->
        <section id="kontakt" class="section">
            <h2 data-i18n="contact.title.short">Opowiedz nam o&nbsp;swoim wydarzeniu.<br>Przygotujemy indywidualną wycenę.</h2>
            <div class="progress-container">
                <div id="form-progress"></div>
            </div>
            <p id="progress-text" class="progress-text"
                data-i18n="form.progress_text">
                Uzupełnij dane, abyśmy mogli przygotować ofertę (0%)</p>
            <form id="form" class="contact-form" novalidate>
                <div id="availability-notice" class="availability-notice"></div>
                <!-- Honeypot for bots -->
                <input type="text" name="website_check" class="honeypot-field" tabindex="-1"
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
                    <label for="location" data-i18n="form.location">Lokalizacja Wydarzenia *</label>
                    <input type="text" id="location" name="location"
                        placeholder="np. Warszawa, Hotel Marriott" data-i18n-placeholder="form.location_placeholder" required>
                </div>
                <div class="form-group">
                    <label for="guests" data-i18n="form.guests_label">Liczba Gości *</label>
                    <input type="number" id="guests" name="guests" min="1" max="9999"
                        placeholder="np. 80" data-i18n-placeholder="form.guests_placeholder" required>
                </div>
                <div class="form-group">
                    <label for="budget" data-i18n="form.budget">Budżet (PLN) *</label>
                    <input type="text" id="budget" name="budget"
                        placeholder="np. 2000 albo 5000 do 10000" data-i18n-placeholder="form.budget_placeholder" required>
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
                <div class="form-group">
                    <label for="contact_hours" data-i18n="form.contact_hours">Preferowane godziny kontaktu</label>
                    <input type="text" id="contact_hours" name="contact_hours"
                        placeholder="np. 10:00-14:00 lub po 18:00" data-i18n-placeholder="form.contact_hours_placeholder">
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