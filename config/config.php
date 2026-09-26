<?php
declare(strict_types=1);

// Application Paths
if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__));
}
if (!defined('DATA_DIR')) {
    define('DATA_DIR', BASE_DIR . '/assets/data');
}
if (!defined('UPLOADS_DIR')) {
    define('UPLOADS_DIR', BASE_DIR . '/assets/uploads');
}
if (!defined('GALLERY_DIR')) {
    define('GALLERY_DIR', BASE_DIR . '/assets/gallery');
}

// Load secrets first if present
$secretsPath = __DIR__ . '/secrets.php';
if (file_exists($secretsPath)) {
    require_once $secretsPath;
}

// Environment variable fallbacks (Twelve-Factor / CI/CD support)
$envMappings = [
    'CF_ACCOUNT_ID'          => 'CF_ACCOUNT_ID',
    'CF_API_TOKEN'           => 'CF_API_TOKEN',
    'CF_GLOBAL_API_KEY'      => 'CF_GLOBAL_API_KEY',
    'CF_EMAIL'               => 'CF_EMAIL',
    'R2_ACCOUNT_ID'          => 'R2_ACCOUNT_ID',
    'R2_ACCESS_KEY_ID'       => 'R2_ACCESS_KEY_ID',
    'R2_SECRET_ACCESS_KEY'   => 'R2_SECRET_ACCESS_KEY',
    'R2_BUCKET_NAME'         => 'R2_BUCKET_NAME',
    'R2_PRIVATE_BUCKET_NAME' => 'R2_PRIVATE_BUCKET_NAME',
    'R2_PUBLIC_DOMAIN'       => 'R2_PUBLIC_DOMAIN',
    'CDN_BASE_URL'           => 'CDN_BASE_URL',
];

foreach ($envMappings as $constName => $envKey) {
    if (!defined($constName)) {
        $val = getenv($envKey);
        if ($val !== false && $val !== '') {
            define($constName, $val);
        }
    }
}

// Defaults
if (!defined('R2_BUCKET_NAME')) {
    define('R2_BUCKET_NAME', 'raricart-media');
}
if (!defined('R2_PRIVATE_BUCKET_NAME')) {
    define('R2_PRIVATE_BUCKET_NAME', 'raricart-private');
}
if (!defined('R2_PUBLIC_DOMAIN')) {
    define('R2_PUBLIC_DOMAIN', 'https://media.raricart.pl');
}
if (!defined('CDN_BASE_URL')) {
    define('CDN_BASE_URL', 'https://media.raricart.pl');
}

