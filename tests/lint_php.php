<?php
declare(strict_types=1);

/**
 * Cross-platform PHP Syntax Linter
 * Scans all project PHP files and validates syntax using `php -l`.
 */

$rootDir = dirname(__DIR__);
$excludeDirs = ['vendor', '.git', 'drafts', 'node_modules'];

echo "=== WERYFIKACJA SKŁADNI PHP (PHP LINT) ===\n";

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

$filesChecked = 0;
$errors = [];

foreach ($iterator as $path => $file) {
    if ($file->isDir()) {
        continue;
    }

    $relative = str_replace([$rootDir . DIRECTORY_SEPARATOR, $rootDir . '/'], '', $path);
    $parts = explode(DIRECTORY_SEPARATOR, str_replace('/', DIRECTORY_SEPARATOR, $relative));

    $isExcluded = false;
    foreach ($parts as $p) {
        if (in_array($p, $excludeDirs, true)) {
            $isExcluded = true;
            break;
        }
    }
    if ($isExcluded) {
        continue;
    }

    if ($file->getExtension() === 'php') {
        $filesChecked++;
        $cmd = escapeshellcmd(PHP_BINARY) . ' -l ' . escapeshellarg($path);
        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            $errors[] = [
                'file' => $relative,
                'output' => implode("\n", $output)
            ];
            echo "  ❌ BŁĄD: $relative\n";
        }
    }
}

echo "Przeskanowano plików PHP: $filesChecked\n";

if (empty($errors)) {
    echo "✅ Wszystkie pliki PHP są poprawne syntaktycznie.\n";
    exit(0);
} else {
    echo "❌ Wykryto błędy składni w " . count($errors) . " plikach:\n";
    foreach ($errors as $e) {
        echo "Plik: {$e['file']}\n{$e['output']}\n\n";
    }
    exit(1);
}
