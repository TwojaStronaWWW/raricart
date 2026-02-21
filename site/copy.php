<?php
function copy_recursive($src, $dst) {
    if (!is_dir($dst)) {
        mkdir($dst, 0777, true);
    }
    $dir = opendir($src);
    while(false !== ($file = readdir($dir))) {
        if ($file != '.' && $file != '..') {
            if (is_dir($src . '/' . $file)) {
                copy_recursive($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

copy_recursive(__DIR__ . '/../packages/assets/gallery', __DIR__ . '/assets/gallery');
copy(__DIR__ . '/../packages/assets/css/style.css', __DIR__ . '/assets/css/pakiety-style.css');

echo "COPIED";
