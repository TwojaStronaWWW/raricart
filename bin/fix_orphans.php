<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Forbidden: CLI only.\n");
}

$rootDir = dirname(__DIR__);
$files = [
    $rootDir . '/index.php',
    $rootDir . '/assets/js/script.js'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = (string)file_get_contents($file);
        $changed = 0;
        $newContent = (string)preg_replace_callback(
            '/(^|\s|>)([aAiIoOuUwWzZ])\s+/',
            static function (array $matches) use (&$changed): string {
                $changed++;
                return $matches[1] . $matches[2] . '&nbsp;';
            },
            $content
        );

        if ($changed > 0) {
            file_put_contents($file, $newContent, LOCK_EX);
            echo "Updated $file ($changed replacements)\n";
        } else {
            echo "No changes needed in $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}
echo "Done.\n";
