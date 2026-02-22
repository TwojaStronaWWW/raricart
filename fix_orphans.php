<?php
$files = [
    __DIR__ . '/index.php',
    __DIR__ . '/assets/js/script.js'
];

$orphans = ['a', 'i', 'o', 'u', 'w', 'z', 'A', 'I', 'O', 'U', 'W', 'Z'];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Find single letters (a, i, o, u, w, z) that:
        // 1. Are preceded by a space, newline, or > (HTML tag boundary)
        // 2. Are followed by one or more whitespace characters
        // Replace the following whitespace(s) with &nbsp;
        // Exception: ensure we don't break HTML tags, assuming basic content. 
        // We use a positive lookbehind for space/newline/bracket.
        $changed = 0;
        $newContent = preg_replace_callback(
            '/(^|\s|>)([aAiIoOuUwWzZ])\s+/',
            function ($matches) use (&$changed) {
                $changed++;
                return $matches[1] . $matches[2] . '&nbsp;';
            },
            $content
        );
        
        if ($changed > 0) {
            file_put_contents($file, $newContent);
            echo "Updated $file ($changed replacements)\n";
        } else {
            echo "No changes needed in $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}
echo "Done.\n";
