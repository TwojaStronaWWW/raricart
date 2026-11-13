<?php
declare(strict_types=1);

// [REF-05 / OWASP PHP2Shell Guard]: Wyrejestrowanie niebezpiecznych wrapperów strumieniowych (phar://, ftp://, expect://) i blokada CGI injection
foreach (['phar', 'ftp', 'ftps', 'expect'] as $__unsafeWrapper) {
    if (in_array($__unsafeWrapper, stream_get_wrappers(), true)) {
        @stream_wrapper_unregister($__unsafeWrapper);
    }
}
$__rawQs = (string)($_SERVER['QUERY_STRING'] ?? '');
if ($__rawQs !== '' && preg_match('/(^|&)(%2d|%ad|-)(d|n|s|w)\b|allow_url_include|auto_prepend_file|auto_append_file|php:\/\/|phar:\/\//i', $__rawQs)) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * Zwraca katalog przechowywania liczników Rate Limit i blokad IP Penalty Box.
 * Wspiera izolację testów jednostkowych przez RARICART_TEST_RATE_LIMIT_DIR.
 */
function rate_limit_dir(): string {
    $customDir = getenv('RARICART_TEST_RATE_LIMIT_DIR');
    if (is_string($customDir) && trim($customDir) !== '') {
        $dir = rtrim(trim($customDir), '/\\');
        if (!is_dir($dir)) {
            @mkdir($dir, 0750, true);
        }
        return is_dir($dir) ? $dir : sys_get_temp_dir();
    }

    $dir = __DIR__ . '/rate_limits';
    if (!file_exists($dir)) {
        if (!@mkdir($dir, 0750, true)) {
            return sys_get_temp_dir();
        }
        @file_put_contents(
            $dir . '/.htaccess',
            "Options -Indexes\n<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Order allow,deny\n    Deny from all\n</IfModule>\n"
        );
        @file_put_contents($dir . '/index.html', '');
    }
    return $dir;
}

/**
 * Pobiera rzeczywisty adres IP klienta za Cloudflare (weryfikując obecność HTTP_CF_RAY) lub REMOTE_ADDR.
 */
function get_client_ip(): string {
    if (!empty($_SERVER['HTTP_CF_RAY']) && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $cfIp = trim((string)$_SERVER['HTTP_CF_CONNECTING_IP']);
        if (filter_var($cfIp, FILTER_VALIDATE_IP) !== false) {
            return $cfIp;
        }
    }

    $remoteAddr = trim((string)($_SERVER['REMOTE_ADDR'] ?? ''));
    if (filter_var($remoteAddr, FILTER_VALIDATE_IP) !== false) {
        return $remoteAddr;
    }

    return '0.0.0.0';
}

/**
 * Anonimizacja adresu IP zgodna z RODO (IPv4 /24 -> .0, IPv6 /48).
 */
function mask_ip(string $ip): string {
    $ip = trim($ip);
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
        $parts = explode('.', $ip);
        $parts[3] = '0';
        return implode('.', $parts);
    }

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
        $packed = @inet_pton($ip);
        if ($packed !== false && strlen($packed) === 16) {
            $masked = substr($packed, 0, 6) . str_repeat("\0", 10);
            $unpacked = @inet_ntop($masked);
            if ($unpacked !== false) {
                return $unpacked;
            }
        }
    }

    return '0.0.0.0';
}

/**
 * Sprawdza, czy adres IP znajduje się w aktywnym 15-minutowym IP Penalty Box.
 */
function is_ip_banned(string $ip): bool {
    $dir = rate_limit_dir();
    $file = $dir . '/ban_' . substr(hash('sha256', $ip), 0, 24) . '.json';
    if (!is_file($file)) {
        return false;
    }

    $fp = @fopen($file, 'rb');
    if (!$fp) {
        return false;
    }

    $banned = false;
    $expired = false;
    if (@flock($fp, LOCK_SH)) {
        $raw = stream_get_contents($fp);
        if (is_string($raw) && $raw !== '') {
            $data = json_decode($raw, true, 16);
            $expiresAt = (int)($data['expires_at'] ?? 0);
            if ($expiresAt > time()) {
                $banned = true;
            } else {
                $expired = true;
            }
        }
        @flock($fp, LOCK_UN);
    }
    fclose($fp);

    if ($expired) {
        @unlink($file);
    }

    return $banned;
}

/**
 * Umieszcza adres IP w 15-minutowym IP Penalty Box (domyślnie 900s) z atomową blokadą LOCK_EX.
 */
function ban_client_ip(string $ip, int $seconds = 900, string $reason = 'security_blocked'): void {
    $dir = rate_limit_dir();
    $file = $dir . '/ban_' . substr(hash('sha256', $ip), 0, 24) . '.json';

    $fp = @fopen($file, 'c+b');
    if (!$fp) {
        return;
    }

    if (@flock($fp, LOCK_EX)) {
        $now = time();
        $payload = [
            'banned_at' => $now,
            'expires_at' => $now + max(60, $seconds),
            'reason' => substr($reason, 0, 64),
            'ip_masked' => mask_ip($ip),
        ];
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, (string)json_encode($payload));
        fflush($fp);
        @flock($fp, LOCK_UN);
    }
    fclose($fp);
}

/**
 * Strażnik SSRF (OWASP ASVS [REF-05]): weryfikuje schemat HTTPS oraz blokuje adresy prywatne,
 * loopback i link-local Cloud Metadata (169.254.169.254, 127.0.0.1, localhost, ::1, 0.0.0.0).
 */
function is_safe_https_url(string $url): bool {
    $url = trim($url);
    if ($url === '' || stripos($url, 'https://') !== 0) {
        return false;
    }
    $parts = parse_url($url);
    if (!is_array($parts) || strtolower((string)($parts['scheme'] ?? '')) !== 'https') {
        return false;
    }
    $host = strtolower(trim((string)($parts['host'] ?? ''), '[]'));
    if ($host === '' || in_array($host, ['localhost', '127.0.0.1', '0.0.0.0', '::1', '169.254.169.254', 'metadata.google.internal'], true)) {
        return false;
    }
    if (str_starts_with($host, '169.254.') || str_starts_with($host, '127.') || str_ends_with($host, '.internal') || str_ends_with($host, '.local')) {
        return false;
    }
    if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
    }
    return true;
}

/**
 * Mechanizm Rate Limiting oparty na blokadzie plikowej i Garbage Collectorze (ISO 27001 / Zero-PII SHA-256).
 * Chroni formularze i endpointy API przed atakami typu brute-force i DoS.
 */
function rate_limit(string $key, int $limit, int $seconds): bool {
    $dir = rate_limit_dir();

    // Zero-PII SHA-256: wycinamy cyfry/oktety IP z prefiksu, pozostawiając tylko nazwę akcji oraz skrót SHA-256 klucza (RODO)
    $actionPrefix = preg_replace('/[^a-zA-Z_]/', '', explode(':', $key)[0] ?? 'rate');
    $actionPrefix = trim((string)preg_replace('/_+/', '_', (string)$actionPrefix), '_');
    if ($actionPrefix === '') {
        $actionPrefix = 'rate';
    }
    $keyHash = hash('sha256', $key);
    $sanitizedKey = substr($actionPrefix, 0, 20) . '_' . substr($keyHash, 0, 24);
    $file = $dir . '/rate_' . $sanitizedKey . '.json';

    // Garbage Collector (1% prawdopodobieństwa)
    if (random_int(1, 100) === 1) {
        $files = array_merge(
            glob($dir . '/rate_*.json') ?: [],
            glob($dir . '/ban_*.json') ?: []
        );
        $now = time();
        foreach ($files as $f) {
            if ($now - (int)@filemtime($f) > 3600) {
                @unlink($f);
            }
        }
    }

    // Otwarcie pliku z blokadą wyłączną (exclusive lock)
    $fp = @fopen($file, 'c+b');
    if (!$fp) {
        return true; // Fail-open w razie problemów z systemem plików
    }

    if (!@flock($fp, LOCK_EX)) {
        fclose($fp);
        return true;
    }

    $current = [];
    $content = stream_get_contents($fp);
    if ($content !== false && $content !== '') {
        $decoded = json_decode($content, true, 16);
        if (is_array($decoded)) {
            $current = $decoded;
        }
    }

    $now = time();

    if (!isset($current['start_time']) || ($now - (int)$current['start_time'] > $seconds)) {
        $current = [
            'start_time' => $now,
            'count' => 0,
            'key_hash' => $keyHash
        ];
    }

    if ((int)$current['count'] >= $limit) {
        @flock($fp, LOCK_UN);
        fclose($fp);
        return false;
    }

    $current['count'] = (int)$current['count'] + 1;

    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string)json_encode($current));
    fflush($fp);

    @flock($fp, LOCK_UN);
    fclose($fp);

    return true;
}
