<?php
// parts/reviews.php - Sekcja 9: Opinie z Google (Przesuwna karuzela kafelków inspirowana Land-Book)
declare(strict_types=1);

$reviewsFile = __DIR__ . '/../assets/data/reviews.json';
$reviewsDist = __DIR__ . '/../assets/data/reviews.json.dist';
$reviewsList = [];

if (file_exists($reviewsFile) && filesize($reviewsFile) > 10) {
    $raw = (string)file_get_contents($reviewsFile);
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $reviewsList = $decoded;
    }
} elseif (file_exists($reviewsDist) && filesize($reviewsDist) > 10) {
    $raw = (string)file_get_contents($reviewsDist);
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $reviewsList = $decoded;
    }
}

// Fallback zabezpieczający w przypadku braku pliku
if (empty($reviewsList)) {
    $reviewsList = [
        [
            'id' => 1,
            'author' => 'Emilia Samoraj',
            'role' => 'Opinia z Google',
            'location' => '5/5 ★',
            'rating' => 5,
            'source' => 'google',
            'text' => 'Jestem pod wrażeniem estetyki i smaku serwowanych produktów. Super stoiska. Parasol daje taki włoski klimat. Miałam możliwość spróbować już każdej z opcji – naleśników, lodów oraz deski serów na targach ślubnych, na których byłam z przyjaciółką. Bliska mi osoba z naszego polecenia zdecydowała się na lody z dodatkami na urodzinach. Goście byli zaskoczeni 🥰 Bardzo polecam! A ślub dopiero przed nami 🕊️🕊️',
            'avatar_initials' => 'ES',
            'verified' => true
        ],
        [
            'id' => 2,
            'author' => 'Jenny Wyderka',
            'role' => 'Opinia z Google',
            'location' => '5/5 ★',
            'rating' => 5,
            'source' => 'google',
            'text' => 'Jestem zachwycona! 💞 Emilia jest przesympatyczną osobą i ma świetne pomysły, pracuje na oryginalnych produktach i zapewnia niezapomniane wspomnienia z imprez 🌷',
            'avatar_initials' => 'JW',
            'verified' => true
        ],
        [
            'id' => 3,
            'author' => 'Anna Słomka',
            'role' => 'Opinia z Google',
            'location' => '5/5 ★',
            'rating' => 5,
            'source' => 'google',
            'text' => 'Super sprawa na imprezę, pancake i lody pycha 😋, nie wspomnę o czekoladzie i reszcie dodatkach, bardzo profesjonalna i miła obsługa. Rozkładanie i składanie stanowisk bezszelestnie. 🙂 Polecam!',
            'avatar_initials' => 'AS',
            'verified' => true
        ]
    ];
}

// Konfigurowalny URL do profilu Google Maps / Wizytówki Google
$googleReviewsUrl = function_exists('get_val') ? get_val('google_reviews_url', '') : '';
if (empty($googleReviewsUrl)) {
    $googleReviewsUrl = 'https://www.google.com/maps/place/Raricart/@51.953761,19.1343692,6z/data=!3m1!4b1!4m6!3m5!1s0x69af180207d122d:0xe20e8bd1466a3884!8m2!3d51.953761!4d19.1343692!16s%2Fg%2F11mzpd4pwn?hl=pl';
}
?>
<section id="opinie" class="section section-reviews" aria-label="Opinie klientów o Raricart">
    <div class="reviews-container">
        <div class="reviews-header-box">
            <span class="reviews-badge" data-i18n="reviews.badge">OPINIE Z GOOGLE</span>
            <h2 class="reviews-title" data-i18n="reviews.title">ZAUFANIE, KTÓRYM OBDARZYLI NAS KLIENCI.</h2>

            <div class="reviews-toolbar">
                <!-- Pasek oceny: G 5.0 ★★★★★ | Wizytówka Google -->
                <div class="reviews-google-pill" aria-label="Potwierdzone opinie w Wizytówce Google">
                    <span class="reviews-google-g" aria-hidden="true">
                        <svg class="google-icon" viewBox="0 0 24 24" width="16" height="16">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.03h3.88c2.27-2.09 3.665-5.17 3.665-9.12z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.03c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.13C3.28 21.43 7.33 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.13z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.28 2.57 1.25 6.58l4.03 3.13c.95-2.83 3.6-4.96 6.72-4.96z"/>
                        </svg>
                    </span>
                    <span class="reviews-google-rating">5.0</span>
                    <span class="reviews-trust-stars" aria-label="5 na 5 gwiazdek">★★★★★</span>
                    <span class="reviews-google-sep" aria-hidden="true">|</span>
                    <a href="<?php echo htmlspecialchars($googleReviewsUrl); ?>" target="_blank" rel="noopener noreferrer" class="reviews-google-btn" aria-label="Otwórz Wizytówkę Google Raricart">
                        <span data-i18n="reviews.see_google_maps">Wizytówka Google</span>
                        <span class="ext-arrow" aria-hidden="true">↗</span>
                    </a>
                </div>

                <!-- Kontrolki przewijania kafelków -->
                <div class="reviews-slider-controls" aria-label="Nawigacja karuzeli opinii">
                    <button type="button" class="reviews-nav-btn prev" id="reviewsPrevBtn" aria-label="Poprzednie opinie">←</button>
                    <button type="button" class="reviews-nav-btn next" id="reviewsNextBtn" aria-label="Następne opinie">→</button>
                </div>
            </div>
        </div>

        <div class="reviews-slider-viewport" id="reviewsSliderViewport">
            <div class="reviews-grid reviews-track" id="reviewsTrack">
                <?php foreach ($reviewsList as $review): 
                    $author = htmlspecialchars($review['author'] ?? 'Klient Raricart');
                    $role = htmlspecialchars($review['role'] ?? 'Opinia z Google');
                    $text = htmlspecialchars($review['text'] ?? '');
                    $initials = htmlspecialchars($review['avatar_initials'] ?? 'RC');
                    $rating = (int)($review['rating'] ?? 5);
                ?>
                    <article class="review-card">
                        <div class="review-card-top">
                            <div class="review-stars" aria-label="Ocena: <?php echo $rating; ?> na 5">
                                <?php for ($i = 0; $i < $rating; $i++): ?>
                                    <span class="star-glyph" aria-hidden="true">★</span>
                                <?php endfor; ?>
                            </div>
                            <a href="<?php echo htmlspecialchars($googleReviewsUrl); ?>" target="_blank" rel="noopener noreferrer" class="review-google-badge" title="Zweryfikowana opinia w Google">
                                <svg class="google-icon-sm" viewBox="0 0 24 24" width="13" height="13" aria-hidden="true">
                                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.03h3.88c2.27-2.09 3.665-5.17 3.665-9.12z"/>
                                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.03c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.13C3.28 21.43 7.33 24 12 24z"/>
                                    <path fill="#FBBC05" d="M5.28 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.13z"/>
                                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.28 2.57 1.25 6.58l4.03 3.13c.95-2.83 3.6-4.96 6.72-4.96z"/>
                                </svg>
                                <span>Google</span>
                            </a>
                        </div>

                        <div class="review-quote-wrapper">
                            <blockquote class="review-quote-text">
                                <?php echo $text; ?>
                            </blockquote>
                        </div>

                        <div class="review-card-footer">
                            <div class="review-avatar" aria-hidden="true">
                                <?php echo $initials; ?>
                            </div>
                            <div class="review-author-info">
                                <cite class="review-author-name"><?php echo $author; ?></cite>
                                <span class="review-author-meta"><?php echo $role; ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
