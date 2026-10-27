<?php
if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/auth.php';
    check_login();
}
echo password_hash('admin123', PASSWORD_DEFAULT);
