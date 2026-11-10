<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/leads-store.php';

// Zezwól na załadowanie dashboardu w trybie wymuszonej zmiany hasła (renderowany jest wtedy wyłącznie formularz zmiany hasła)
check_login(true);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$mustChangePassword = !empty($_SESSION['must_change_password']);
$csrfToken = (string)($_SESSION['csrf_token'] ?? '');

$status_file = __DIR__ . '/../assets/data/status.json';
$current_status = ['text' => '', 'enabled' => false];
if (!$mustChangePassword && file_exists($status_file)) {
    $loadedStatus = read_json_file($status_file);
    if (!empty($loadedStatus)) {
        $current_status = [
            'text' => (string)($loadedStatus['text'] ?? ''),
            'enabled' => !empty($loadedStatus['enabled']),
        ];
    }
}

$content_file = __DIR__ . '/../assets/data/content.json';
$content_dist = __DIR__ . '/../assets/data/content.json.dist';
$initial_content = [];
if (!$mustChangePassword) {
    if (file_exists($content_file)) {
        $initial_content = read_json_file($content_file);
    } elseif (file_exists($content_dist)) {
        $initial_content = read_json_file($content_dist);
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora - Raricart</title>
    <link rel="stylesheet" href="admin.css">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
    <?php if (!$mustChangePassword): ?>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" defer></script>
    <?php endif; ?>
    <script src="admin.js" defer></script>
</head>
<body>

<div class="header">
    <h1>Raricart Admin</h1>
    <form method="POST" action="logout.php" class="inline-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="logout-btn">Wyloguj</button>
    </form>
</div>

<div class="container">
    <?php if (!empty($_GET['msg'])): ?>
        <div class="card card-flash" role="status">
            <span><?php echo htmlspecialchars((string)$_GET['msg'], ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="flash-close-btn" data-dismiss-parent aria-label="Zamknij komunikat">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($mustChangePassword): ?>
        <div class="card card-forced-password" id="forcedPasswordCard">
            <h2 class="heading-danger">Wymagana zmiana hasła</h2>
            <p class="text-spaced-lg">Ze względów bezpieczeństwa musisz zmienić swoje hasło tymczasowe przed uzyskaniem dostępu do funkcji panelu administracyjnego.</p>

            <div class="status-group status-group-block">
                <div class="password-grid-full">
                    <input type="password" id="oldPass" class="status-input" placeholder="Obecne (tymczasowe) hasło" autocomplete="current-password">
                    <input type="password" id="newPass" class="status-input" placeholder="Nowe hasło (min. 8 znaków)" minlength="8" autocomplete="new-password">
                    <input type="password" id="confirmPass" class="status-input" placeholder="Potwierdź nowe hasło" minlength="8" autocomplete="new-password">
                    <button type="button" class="action-btn" id="changePasswordBtn">Zmień i przejdź do panelu</button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <script type="application/json" id="initialSiteContent"><?php echo json_encode($initial_content, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>

        <!-- Availability Manager -->
        <div class="card">
            <h2>Zarządzanie Dostępnością</h2>
            <p class="text-muted-desc">Ten komunikat pojawi się na górze strony głównej.</p>
            <div class="status-group">
                <input type="text" id="statusText" class="status-input" placeholder="np. Zostały ostatnie 2 terminy na sierpień!" value="<?php echo htmlspecialchars($current_status['text'], ENT_QUOTES, 'UTF-8'); ?>">
                <label class="toggle-switch">
                    <input type="checkbox" id="statusToggle" <?php echo $current_status['enabled'] ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
                <button type="button" class="action-btn" id="saveStatusBtn">Zapisz</button>
            </div>
        </div>

        <!-- Site Media Section -->
        <div class="card section-media">
            <h2>Media Strony</h2>
            <p class="text-spaced-lg">Zarządzaj zdjęciami i wideo w kluczowych sekcjach.</p>

            <div class="media-grid">
                <div class="media-item">
                    <h4>Wideo w tle (Hero)</h4>
                    <div class="media-preview video-preview" id="preview-hero_video">
                        <span>Brak wideo</span>
                    </div>
                    <label class="btn btn-secondary upload-btn">
                        Zmień wideo (MP4/WebM)
                        <input type="file" data-content-key="hero_video" data-media-type="video" accept="video/mp4,video/webm">
                    </label>
                </div>

                <div class="media-item">
                    <h4>O Nas (Zdjęcie)</h4>
                    <div class="media-preview" id="preview-about_image"></div>
                    <label class="btn btn-secondary upload-btn">
                        Wgraj zdjęcie
                        <input type="file" data-content-key="about_image" data-media-type="image" accept="image/*">
                    </label>
                </div>

                <div class="media-item">
                    <h4>Oferta (Tło Główne)</h4>
                    <div class="media-preview" id="preview-offer_main_image"></div>
                    <label class="btn btn-secondary upload-btn">
                        Wgraj zdjęcie
                        <input type="file" data-content-key="offer_main_image" data-media-type="image" accept="image/*">
                    </label>
                </div>

                <div class="media-item">
                    <h4>Galeria (Tło Parallaksy)</h4>
                    <div class="media-preview" id="preview-gallery_bg"></div>
                    <label class="btn btn-secondary upload-btn">
                        Wgraj zdjęcie
                        <input type="file" data-content-key="gallery_bg" data-media-type="image" accept="image/*">
                    </label>
                </div>

                <div class="media-item">
                    <h4>Sztuka kulinarna (Parallax)</h4>
                    <div class="media-preview" id="preview-why_us_bg"></div>
                    <label class="btn btn-secondary upload-btn">
                        Wgraj zdjęcie
                        <input type="file" data-content-key="why_us_bg" data-media-type="image" accept="image/*">
                    </label>
                </div>
            </div>

            <h3 class="section-subhead">Karty Oferty</h3>
            <div class="media-grid">
                <div class="media-item">
                    <h4>Pancakes</h4>
                    <div class="media-preview" id="preview-offer_cards-pancakes"></div>
                    <label class="btn btn-secondary upload-btn">
                        Zmień
                        <input type="file" data-content-key="offer_cards.pancakes" data-media-type="image" accept="image/*">
                    </label>
                </div>
                <div class="media-item">
                    <h4>Lody</h4>
                    <div class="media-preview" id="preview-offer_cards-icecream"></div>
                    <label class="btn btn-secondary upload-btn">
                        Zmień
                        <input type="file" data-content-key="offer_cards.icecream" data-media-type="image" accept="image/*">
                    </label>
                </div>
                <div class="media-item">
                    <h4>Deska Serów</h4>
                    <div class="media-preview" id="preview-offer_cards-cheese"></div>
                    <label class="btn btn-secondary upload-btn">
                        Zmień
                        <input type="file" data-content-key="offer_cards.cheese" data-media-type="image" accept="image/*">
                    </label>
                </div>
            </div>

            <h3 class="section-subhead">Zdjęcia w Modalu Oferty (Szczegóły)</h3>
            <div class="media-grid">
                <div class="media-item">
                    <h4>Pancakes (Modal)</h4>
                    <div class="media-preview" id="preview-offer_modals-pancakes"></div>
                    <label class="btn btn-secondary upload-btn">
                        Zmień
                        <input type="file" data-content-key="offer_modals.pancakes" data-media-type="image" accept="image/*">
                    </label>
                </div>
                <div class="media-item">
                    <h4>Lody (Modal)</h4>
                    <div class="media-preview" id="preview-offer_modals-icecream"></div>
                    <label class="btn btn-secondary upload-btn">
                        Zmień
                        <input type="file" data-content-key="offer_modals.icecream" data-media-type="image" accept="image/*">
                    </label>
                </div>
                <div class="media-item">
                    <h4>Deska Serów (Modal)</h4>
                    <div class="media-preview" id="preview-offer_modals-cheese"></div>
                    <label class="btn btn-secondary upload-btn">
                        Zmień
                        <input type="file" data-content-key="offer_modals.cheese" data-media-type="image" accept="image/*">
                    </label>
                </div>
            </div>
        </div>

        <!-- Image Upload -->
        <div class="card">
            <h2>Dodaj Zdjęcia (Inteligentne Wgrywanie)</h2>
            <div id="drop-zone">
                <p>Przeciągnij zdjęcia tutaj lub kliknij, aby wybrać</p>
            </div>
            <input type="file" id="fileInput" class="visually-hidden-input" multiple accept="image/*">
            <div id="upload-progress"><div id="progress-bar"></div></div>
            <div id="status-msg" class="status-msg-box"></div>
        </div>

        <!-- Gallery List -->
        <div class="card">
            <h2>Twoja Galeria</h2>
            <div class="gallery-grid" id="galleryList"></div>
        </div>

        <!-- Leads Management -->
        <div class="card card-leads">
            <div class="leads-row">
                <div>
                    <h2 class="leads-title">Baza Klientów (Excel)</h2>
                    <p class="leads-desc">Zarządzaj zgłoszeniami z formularza kontaktowego.</p>
                    <div class="leads-r2-badge">
                        <span aria-hidden="true">🛡️</span>
                        <span>Prywatny magazyn Cloudflare R2: <strong>raricart-private</strong> (Automatyczna kopia zapasowa)</span>
                    </div>
                </div>
                <div class="leads-actions">
                    <form method="POST" action="sync_leads.php" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="action-btn btn-sync" title="Wykonaj natychmiastową synchronizację z prywatnym R2">
                            <span aria-hidden="true">🔄</span> Synchronizuj z R2
                        </button>
                    </form>
                    <a href="download_leads.php" class="action-btn btn-download">
                        <span aria-hidden="true">📥</span> Pobierz .CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Security / Account Management -->
        <div class="card">
            <h2>Konto Administratora</h2>
            <p class="text-spaced-lg">Zalogowany jako: <strong><?php echo htmlspecialchars(get_current_user_email(), ENT_QUOTES, 'UTF-8'); ?></strong></p>

            <div class="status-group status-group-block">
                <h4 class="password-subhead">Zmiana Hasła</h4>
                <div class="password-grid">
                    <input type="password" id="oldPass" class="status-input" placeholder="Stare hasło" autocomplete="current-password">
                    <input type="password" id="newPass" class="status-input" placeholder="Nowe hasło (min. 8 znaków)" minlength="8" autocomplete="new-password">
                    <input type="password" id="confirmPass" class="status-input" placeholder="Potwierdź nowe hasło" minlength="8" autocomplete="new-password">
                    <button type="button" class="action-btn" id="changePasswordBtn">Zmień Hasło</button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
