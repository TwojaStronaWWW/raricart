<?php
declare(strict_types=1);

/**
 * Cloudflare Credentials & Configuration Template
 * 
 * Skopiuj ten plik jako `config/secrets.php` i uzupełnij swoimi wartościami z Cloudflare Dashboard.
 * UWAGA: Plik `config/secrets.php` zawiera poufne poświadczenia i NIE MOŻE być wersjonowany w Git.
 */

// Cloudflare Account ID (znajdziesz go w panelu Cloudflare po prawej stronie w "Account ID")
define('CF_ACCOUNT_ID', '');

// Rekomendowane: Cloudflare API Token (utwórz w Cloudflare: My Profile -> API Tokens -> Create Token)
// Wymagane uprawnienia do audytu: Zone (Read), DNS (Read), SSL/TLS (Read), Cache Purge (Purge)
define('CF_API_TOKEN', '');

// Opcjonalnie (jeśli używasz starszego Global API Key zamiast Tokena):
define('CF_GLOBAL_API_KEY', '');
define('CF_EMAIL', '');

// --- CLOUDFLARE R2 CREDENTIALS ---
define('R2_ACCOUNT_ID', '');
define('R2_ACCESS_KEY_ID', '');
define('R2_SECRET_ACCESS_KEY', '');
define('R2_BUCKET_NAME', 'raricart-media');
define('R2_PRIVATE_BUCKET_NAME', 'raricart-private');
define('R2_PUBLIC_DOMAIN', 'https://media.raricart.pl');
define('CDN_BASE_URL', 'https://media.raricart.pl');


