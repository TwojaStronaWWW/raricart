<?php
// admin/migrate_users.php

$configFile = __DIR__ . '/../assets/data/config.json';

if (!file_exists($configFile)) {
    die("Config file not found. Run setup.php first.");
}

$config = json_decode(file_get_contents($configFile), true);

// Check if already migrated
if (isset($config['users'])) {
    die("Already migrated to multi-user system.");
}

// Old hash (or default if missing)
$oldHash = $config['password_hash'] ?? password_hash('admin123', PASSWORD_DEFAULT);

// New Structure
$newConfig = [
    'users' => [
        'kontakt@raricart.pl' => $oldHash, // Preserve existing password
        'pomoc@raricart.pl' => password_hash('pomoc123', PASSWORD_DEFAULT) // Default for help account
    ]
];

// Backup old config
copy($configFile, $configFile . '.bak');

// Save new config which REPLACES old structure completely regarding Auth
// We might want to keep other settings if any exists, but currently config.json is just auth.
file_put_contents($configFile, json_encode($newConfig, JSON_PRETTY_PRINT));

echo "Migration successful! Users 'kontakt@raricart.pl' and 'pomoc@raricart.pl' created.";
?>
