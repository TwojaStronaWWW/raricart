<?php
// parts/faq.php - Sekcja 10: Zoptymalizowany FAQ (Lejek 2026)
declare(strict_types=1);

$faqItems = [
    [
        'id' => 'q1',
        'number' => '01',
        'q' => 'Ile osób może obsłużyć stacja Raricart?',
        'a' => 'Od kameralnych uroczystości (20–30 osób) po duże eventy firmowe i wesela na 500+ gości. Nasz profesjonalny sprzęt gastronomiczny cechuje się bardzo wysoką wydajnością (np. płyty do mini pancakes wypiekają dziesiątki sztuk w kilka minut). Przy wydarzeniach powyżej 150–200 osób rozstawiamy 2 lub 3 stacje równolegle, dzięki czemu goście nie czekają w kolejkach.',
        'tag' => 'Pojemność & Skala'
    ],
    [
        'id' => 'q2',
        'number' => '02',
        'q' => 'Czy stacja może pojawić się na weselu lub poprawinach?',
        'a' => 'Zdecydowanie tak! To jeden z naszych najpopularniejszych formatów. Stacja live cooking rewelacyjnie sprawdza się jako słodki stół nowej generacji, popołudniowa strefa relaksu w ogrodzie, wieczorna atrakcja po torcie weselnym lub orzeźwienie na poprawinach. Dopasowujemy się do harmonogramu i estetyki sali weselnej.',
        'tag' => 'Wesela & Przyjęcia'
    ],
    [
        'id' => 'q3',
        'number' => '03',
        'q' => 'Czy dojeżdżacie poza Śląsk i Małopolskę?',
        'a' => 'Tak, działamy w całej Polsce! Naszą bazą jest Śląsk, ale regularnie podróżujemy do Krakowa, Wrocławia, Warszawy, Łodzi, Poznania i mniejszych miejscowości. Dojeżdżamy wszędzie tam, gdzie organizator potrzebuje kulinarnego efektu wow.',
        'tag' => 'Zasięg & Dojazd'
    ],
    [
        'id' => 'q4',
        'number' => '04',
        'q' => 'Czy można połączyć kilka stacji na jednym wydarzeniu?',
        'a' => 'Jak najbardziej! Często łączymy słodkie Mini Pancakes z orzeźwiającymi Lodami Włoskimi lub ekskluzywną Włoską Deską Serów i Wędlin. Dzięki temu goście mają różnorodne doświadczenie smakowe (zarówno na słodko, jak i wytrawnie) w ramach jednego spójnego serwisu.',
        'tag' => 'Łączenie Stacji'
    ],
    [
        'id' => 'q5',
        'number' => '05',
        'q' => 'Ile miejsca potrzebujecie i jakie warunki techniczne są wymagane?',
        'a' => 'Potrzebujemy jedynie ok. 2x2m lub 3x3m równej powierzchni (w sali, pod namiotem, na tarasie lub w plenerze) oraz dostępu do standardowego gniazdka 230V. Nie potrzebujemy zaplecza kuchennego ani bieżącej wody na stanowisku – jesteśmy w 100% samowystarczalni.',
        'tag' => 'Wymiary & Prąd'
    ],
    [
        'id' => 'q6',
        'number' => '06',
        'q' => 'Czy zapewniacie pełną obsługę, zastawę i sprzątanie?',
        'a' => 'Tak, zajmujemy się wszystkim od A do Z. W cenie usługi jest: transport, montaż stacji, elegancka obsługa w fartuchach, nielimitowane składniki i dodatki na czas serwisu, ekologiczne naczynia i sztućce oraz sprawny demontaż i pozostawienie idealnego porządku po evencie. Ty cieszysz się gośćmi – my dbamy o stację.',
        'tag' => 'Obsługa & Porządek'
    ],
    [
        'id' => 'q7',
        'number' => '07',
        'q' => 'Jak wygląda wycena i proces rezerwacji terminu?',
        'a' => 'Każdą ofertę przygotowujemy indywidualnie i całościowo – bez ukrytych kosztów i mylących stawek "od osoby". Wycena zależy od wybranego menu stacji, planowanej liczby gości, czasu trwania serwisu oraz lokalizacji. Wystarczy przesłać formularz kontaktowy, a w ciągu 24h otrzymasz konkretną propozycję. Rezerwacja terminu następuje po akceptacji oferty i wpłacie zaliczki.',
        'tag' => 'Wycena & Rezerwacja'
    ]
];
?>
<section id="faq" class="section section-faq" aria-label="Najczęściej zadawane pytania o stacje Raricart">
    <div class="faq-container">
        <div class="faq-header-box">
            <span class="faq-badge" data-i18n="faq.badge">ODPOWIEDZI NA NAJCZĘSTSZE PYTANIA</span>
            <h2 class="faq-title" data-i18n="faq.title">Wszystko, co chcesz wiedzieć przed rezerwacją stacji</h2>
            <p class="faq-subtitle" data-i18n="faq.subtitle">
                Przygotowaliśmy odpowiedzi na pytania, które najczęściej zadają nam pary młode, managerowie firm i koordynatorzy eventów.
            </p>
        </div>

        <div class="faq-accordion-wrapper">
            <?php foreach ($faqItems as $idx => $item): 
                $isOpen = ($idx === 0) ? 'open' : '';
            ?>
                <details class="faq-item" name="raricart-faq" <?php echo $isOpen; ?>>
                    <summary class="faq-summary">
                        <div class="faq-summary-left">
                            <span class="faq-number"><?php echo $item['number']; ?></span>
                            <span class="faq-tag"><?php echo htmlspecialchars($item['tag']); ?></span>
                            <h3 class="faq-question" data-i18n="faq.<?php echo $item['id']; ?>.title">
                                <?php echo htmlspecialchars($item['q']); ?>
                            </h3>
                        </div>
                        <div class="faq-icon-wrapper" aria-hidden="true">
                            <span class="faq-chevron">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </div>
                    </summary>
                    <div class="faq-answer-content">
                        <p data-i18n="faq.<?php echo $item['id']; ?>.desc">
                            <?php echo htmlspecialchars($item['a']); ?>
                        </p>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>

        <div class="faq-help-box">
            <div class="faq-help-content">
                <span class="faq-help-icon" aria-hidden="true">💬</span>
                <div class="faq-help-text">
                    <h3 class="faq-help-title" data-i18n="faq.help_title">Masz inne pytanie przed rezerwacją?</h3>
                    <p class="faq-help-desc" data-i18n="faq.help_desc">
                        Chętnie podpowiemy, która stacja sprawdzi się najlepiej na Twoim wydarzeniu.
                    </p>
                </div>
            </div>
            <div class="faq-help-actions">
                <a href="#kontakt" class="faq-help-btn primary" data-i18n="faq.help_cta">
                    <span>ZAPYTAJ W FORMULARZU</span>
                    <span class="btn-arrow" aria-hidden="true">→</span>
                </a>
                <a href="tel:+48883392688" class="faq-help-btn phone" aria-label="Zadzwoń do Raricart: +48 883 392 688">
                    <span>+48 883 392 688</span>
                </a>
            </div>
        </div>
    </div>
</section>
