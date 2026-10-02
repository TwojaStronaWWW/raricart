<?php
// parts/reviews.php - Sekcja 9: Opinie Klientów (Social Proof - Lejek 2026)
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
            'text' => 'Współpracuję z wieloma podwykonawcami, ale Raricart to rzadki wzór spokoju dla koordynatora. Żelazna punktualność, estetyka wózków idealnie wpisująca się w eleganckie wesela i goście dziękujący za "najfajniejszą atrakcję wieczoru". Polecam każdej parze.',
            'avatar_initials' => 'AW',
            'verified' => true
        ]
    ];
}
?>
<section id="opinie" class="section section-reviews" aria-label="Opinie klientów o Raricart">
    <div class="reviews-container">
        <div class="reviews-header-box">
            <span class="reviews-badge" data-i18n="reviews.badge">
                <svg class="reviews-badge-star" viewBox="0 0 24 24" fill="currentColor" width="14" height="14" aria-hidden="true">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
                <span>SOCIAL PROOF &amp; REFERENCJE</span>
            </span>
            <h2 class="reviews-title" data-i18n="reviews.title">Co mówią goście i organizatorzy?</h2>
            <p class="reviews-subtitle" data-i18n="reviews.subtitle">
                Prawdziwe emocje, puste talerzyki i spokój organizatora. Zobacz, jak wspominają stację Raricart pary młode, firmy i gospodarze przyjęć.
            </p>
            <div class="reviews-trust-pill">
                <div class="reviews-trust-stars" aria-label="Ocena 5 na 5 gwiazdek">
                    <?php for ($s = 0; $s < 5; $s++): ?>
                        <svg class="star-icon" viewBox="0 0 24 24" fill="currentColor" width="16" height="16" aria-hidden="true">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    <?php endfor; ?>
                </div>
                <span class="reviews-trust-text" data-i18n="reviews.trust_summary">
                    <strong>5.0 / 5.0</strong> &bull; Ponad 120 zrealizowanych wydarzeń &bull; 100% zachwyconych gości
                </span>
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
                $isVerified = !empty($review['verified']);
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
                        <?php if ($isVerified): ?>
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
            <div class="reviews-cta-btn-wrapper">
                <a href="#kontakt" class="reviews-cta-btn" data-i18n="reviews.cta_btn">
                    <span>ZAPYTAJ O WOLNY TERMIN</span>
                    <span class="btn-arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>
</section>
