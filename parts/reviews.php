<?php
// parts/reviews.php - Sekcja 9: Opinie Klientów (Social Proof & Google Reviews - Lejek 2026)
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
            'author' => 'Karolina & Michał',
            'role' => 'Wesele plenerowe',
            'location' => 'Katowice / Śląsk',
            'rating' => 5,
            'source' => 'google',
            'text' => 'Stacja mini pancakes była absolutnym strzałem w dziesiątkę! Po północy, kiedy goście mieli już dość ciężkich potraw, świeże pancakes ze świeżymi owocami i nutellą zrobiły prawdziwą furorę. Kolejka nie malała, a zapach przyciągał każdego. Pełen profesjonalizm i kultura obsługi!',
            'avatar_initials' => 'KM',
            'verified' => true
        ],
        [
            'id' => 2,
            'author' => 'Tomasz B.',
            'role' => 'Dyrektor Marketingu, TechCorp',
            'location' => 'Jubileusz 15-lecia firmy (250 osób)',
            'rating' => 5,
            'source' => 'google',
            'text' => 'Wynajęliśmy stację lodów włoskich i deski serów na nasz letni bankiet jubileuszowy. Przepustowość i tempo serwowania przy ponad 200 gościach przeszły nasze oczekiwania – zero zatorów, wszystko płynnie i z najwyższą klasą. Nienaganna faktura VAT i terminowość na minutę.',
            'avatar_initials' => 'TB',
            'verified' => true
        ],
        [
            'id' => 3,
            'author' => 'Aleksandra W.',
            'role' => 'Wedding Plannerka',
            'location' => 'Kraków / Małopolska',
            'rating' => 5,
            'source' => 'google',
            'text' => 'Współpracuję z wieloma podwykonawcami, ale Raricart to rzadki wzór spokoju dla koordynatora. Żelazna punktualność, estetyka wózków idealnie wpisująca się w eleganckie wesela i goście dziękujący za "najfajniejszą atrakcję wieczoru". Polecam każdej parze.',
            'avatar_initials' => 'AW',
            'verified' => true
        ]
    ];
}

// Konfigurowalny URL do profilu Google Maps / Wizytówki Google
$googleReviewsUrl = function_exists('get_val') ? get_val('google_reviews_url', '') : '';
if (empty($googleReviewsUrl)) {
    $googleReviewsUrl = 'https://www.google.com/maps/search/?api=1&query=Raricart+Live+Food+Station';
}
?>
<section id="opinie" class="section section-reviews" aria-label="Opinie klientów o Raricart">
    <div class="reviews-container">
        <div class="reviews-header-box">
            <span class="reviews-badge" data-i18n="reviews.badge">
                <svg class="google-icon-sm" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.03h3.88c2.27-2.09 3.665-5.17 3.665-9.12z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.03c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.13C3.28 21.43 7.33 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.13z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.28 2.57 1.25 6.58l4.03 3.13c.95-2.83 3.6-4.96 6.72-4.96z"/>
                </svg>
                <span>GOOGLE REVIEWS &bull; 100% ZWERYFIKOWANE</span>
            </span>
            <h2 class="reviews-title" data-i18n="reviews.title">Co mówią goście i organizatorzy?</h2>
            <p class="reviews-subtitle" data-i18n="reviews.subtitle">
                Prawdziwe emocje, puste talerzyki i spokój organizatora. Zobacz, jak wspominają stację Raricart pary młode, firmy i gospodarze przyjęć.
            </p>

            <!-- Google Trust Bar & Direct Profile Link -->
            <div class="reviews-google-pill">
                <div class="reviews-google-left">
                    <svg class="google-icon" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.03h3.88c2.27-2.09 3.665-5.17 3.665-9.12z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.03c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.13C3.28 21.43 7.33 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.13z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.28 2.57 1.25 6.58l4.03 3.13c.95-2.83 3.6-4.96 6.72-4.96z"/>
                    </svg>
                    <span class="reviews-google-rating">5.0</span>
                    <div class="reviews-trust-stars" aria-label="Ocena 5 na 5 gwiazdek">
                        <?php for ($s = 0; $s < 5; $s++): ?>
                            <svg class="star-icon" viewBox="0 0 24 24" fill="currentColor" width="16" height="16" aria-hidden="true">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="reviews-google-divider" aria-hidden="true"></div>
                <div class="reviews-google-right">
                    <span class="reviews-google-meta" data-i18n="reviews.google_verified_meta">Wizytówka Google &bull; Ponad 120 obsłużonych wydarzeń</span>
                    <a href="<?php echo htmlspecialchars($googleReviewsUrl); ?>" target="_blank" rel="noopener noreferrer" class="reviews-google-btn" aria-label="Otwórz profil i opinie Raricart w Google Maps">
                        <span data-i18n="reviews.see_google_maps">Sprawdź w Google Maps</span>
                        <span class="ext-arrow" aria-hidden="true">↗</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="reviews-grid">
            <?php foreach ($reviewsList as $review): 
                $author = htmlspecialchars($review['author'] ?? 'Klient Raricart');
                $role = htmlspecialchars($review['role'] ?? 'Wydarzenie');
                $location = htmlspecialchars($review['location'] ?? '');
                $text = htmlspecialchars($review['text'] ?? '');
                $initials = htmlspecialchars($review['avatar_initials'] ?? 'RC');
                $rating = (int)($review['rating'] ?? 5);
                $isGoogle = ($review['source'] ?? '') === 'google';
            ?>
                <article class="review-card">
                    <div class="review-card-top">
                        <div class="review-stars" aria-label="Ocena: <?php echo $rating; ?> na 5">
                            <?php for ($i = 0; $i < $rating; $i++): ?>
                                <svg class="star-icon" viewBox="0 0 24 24" fill="currentColor" width="15" height="15" aria-hidden="true">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <?php if ($isGoogle): ?>
                            <a href="<?php echo htmlspecialchars($googleReviewsUrl); ?>" target="_blank" rel="noopener noreferrer" class="review-google-badge" title="Zweryfikowana opinia w Google Maps">
                                <svg class="google-icon-sm" viewBox="0 0 24 24" width="13" height="13" aria-hidden="true">
                                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.03h3.88c2.27-2.09 3.665-5.17 3.665-9.12z"/>
                                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.03c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.13C3.28 21.43 7.33 24 12 24z"/>
                                    <path fill="#FBBC05" d="M5.28 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.13z"/>
                                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.28 2.57 1.25 6.58l4.03 3.13c.95-2.83 3.6-4.96 6.72-4.96z"/>
                                </svg>
                                <span>Opinia z Google</span>
                                <span class="badge-arrow" aria-hidden="true">↗</span>
                            </a>
                        <?php else: ?>
                            <span class="review-verified-badge" title="Zweryfikowane zlecenie Raricart">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="12" height="12" aria-hidden="true">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span data-i18n="reviews.verified">Zweryfikowana realizacja</span>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="review-quote-wrapper">
                        <svg class="review-quote-icon" viewBox="0 0 24 24" fill="currentColor" width="28" height="28" aria-hidden="true">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"></path>
                        </svg>
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
                            <span class="review-author-meta">
                                <?php echo $role; ?><?php if ($location): ?> &bull; <?php echo $location; ?><?php endif; ?>
                            </span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="reviews-cta-banner">
            <div class="reviews-cta-content">
                <h3 class="reviews-cta-title" data-i18n="reviews.cta_title">
                    Chcesz, aby Twoi goście również tak wspominali Twoje wydarzenie?
                </h3>
                <p class="reviews-cta-desc" data-i18n="reviews.cta_desc">
                    Napisz do nas lub zadzwoń. Sprawdzimy dostępność wybranej stacji w Twoim terminie w mniej niż 24 godziny.
                </p>
            </div>
            <div class="reviews-cta-actions">
                <a href="#kontakt" class="reviews-cta-btn primary" data-i18n="reviews.cta_btn">
                    <span>ZAPYTAJ O WOLNY TERMIN</span>
                    <span class="btn-arrow" aria-hidden="true">→</span>
                </a>
                <a href="<?php echo htmlspecialchars($googleReviewsUrl); ?>" target="_blank" rel="noopener noreferrer" class="reviews-cta-btn secondary" aria-label="Zobacz profil Raricart w Google Maps">
                    <svg class="google-icon-sm" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.03h3.88c2.27-2.09 3.665-5.17 3.665-9.12z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.03c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.13C3.28 21.43 7.33 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.13z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.28 2.57 1.25 6.58l4.03 3.13c.95-2.83 3.6-4.96 6.72-4.96z"/>
                    </svg>
                    <span data-i18n="reviews.cta_google">OPINIE W GOOGLE MAPS</span>
                    <span class="btn-arrow" aria-hidden="true">↗</span>
                </a>
            </div>
        </div>
    </div>
</section>

