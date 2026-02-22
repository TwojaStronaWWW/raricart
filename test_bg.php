<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$jsonFile = __DIR__ . '/site/assets/data/content.json';
if(file_exists($jsonFile)) {
    $content = json_decode(file_get_contents($jsonFile), true);
    echo "<h1>Content JSON:</h1><pre>" . print_r($content, true) . "</pre>";

    if(!empty($content['gallery_bg'])) {
        $path = __DIR__ . '/site/' . ltrim($content['gallery_bg'], '/');
        echo "<h2>Gallery BG Path:</h2>" . $path . "<br>";
        echo "Exists? : " . (file_exists($path) ? 'YES' : 'NO') . "<br>";
    }
} else {
    echo "<h1>Error:</h1> Cannot find content.json at " . $jsonFile;
}
?>
