<?php
declare(strict_types=1);
// parts/realizations.php - Sekcja 8: Realizacje („W blasku przyjęć” - Latające zdjęcia Parallax)
$galleryFiles = [];
$jsonFile = __DIR__ . '/../assets/data/gallery.json';
if (function_exists('read_json_file_shared')) {
    $galleryFiles = read_json_file_shared($jsonFile) ?? [];
} elseif (is_file($jsonFile)) {
    $fp = @fopen($jsonFile, 'rb');
    if ($fp && @flock($fp, LOCK_SH)) {
        $rawGallery = (string)stream_get_contents($fp);
        @flock($fp, LOCK_UN);
        $decodedGallery = json_decode($rawGallery, true, 16);
        if (is_array($decodedGallery)) {
            $galleryFiles = $decodedGallery;
        }
    }
    if ($fp) {
        fclose($fp);
    }
}
$totalImages = count($galleryFiles);

// Wyselekcjonowane 8 najlepszych kadrów na stronę główną (4 kolumny x 2 rzędy)
$maxDisplay = min(8, $totalImages);
$displayFiles = array_slice($galleryFiles, 0, $maxDisplay);

$colsCount = ($totalImages > 0) ? min(4, max(1, $maxDisplay)) : 4;
$columns = array_fill(0, $colsCount, []);
foreach ($displayFiles as $idx => $item) {
    $src = is_string($item) ? $item : ($item['url'] ?? $item['src'] ?? '');
    if (!empty($src)) {
        $columns[$idx % $colsCount][] = ['src' => $src, 'index' => $idx];
    }
}
?>
<section id="realizacje" class="section section-realizations" aria-label="Galeria realizacji Raricart">
    <div class="realizations-header-box">
        <h2 class="realizations-title" data-i18n="gallery.title">W BLASKU PRZYJĘĆ</h2>
        <p class="realizations-subtitle" data-i18n="gallery.subtitle">
            Zobacz wybrane kadry z naszych realizacji.
        </p>
    </div>

    <?php
    // Bezpieczne przekazanie pełnej listy zdjęć do natychmiastowej inicjalizacji lightboxa
    $allImagesList = [];
    foreach ($galleryFiles as $gf) {
        $u = is_string($gf) ? $gf : ($gf['url'] ?? $gf['src'] ?? '');
        if (!empty($u)) $allImagesList[] = $u;
    }
    ?>
    <script type="application/json" id="galleryInitialData"><?php echo json_encode($allImagesList, JSON_UNESCAPED_SLASHES); ?></script>

    <div id="realizacje-parallax" class="section-gallery-parallax">
        <div class="gallery-grid" id="dynamicGalleryGrid">
            <?php foreach ($columns as $cIdx => $colItems): 
                $parallaxClass = ($cIdx % 2 !== 0) ? 'parallax' : '';
            ?>
                <div class="gallery-column <?php echo $parallaxClass; ?>">
                    <?php foreach ($colItems as $item): ?>
                        <div class="gallery-item in-view" data-index="<?php echo $item['index']; ?>" role="button" tabindex="0" aria-label="Powiększ zdjęcie realizacji <?php echo $item['index'] + 1; ?>">
                            <img src="<?php echo htmlspecialchars($item['src']); ?>" 
                                 loading="lazy" 
                                 alt="Realizacja Raricart <?php echo $item['index'] + 1; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="realizations-action">
            <button type="button" class="realizations-more-btn" id="openFullGalleryBtn" aria-label="Otwórz pełną galerię realizacji">
                <span data-i18n="gallery.see_more">PRZEJDŹ DO GALERII</span>
                <span class="btn-arrow" aria-hidden="true">→</span>
            </button>
        </div>
    </div>
</section>
