<?php
// parts/realizations.php - Sekcja 8: Realizacje (Wyselekcjonowane kadry z życia stacji - Lejek 2026)
$galleryFiles = [];
$jsonFile = __DIR__ . '/../assets/data/gallery.json';
if (file_exists($jsonFile)) {
    $galleryFiles = json_decode(file_get_contents($jsonFile), true);
    if (!is_array($galleryFiles)) $galleryFiles = [];
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

$gallery_bg = function_exists('get_val') ? get_val('gallery_bg', '') : '';
?>
<section id="realizacje" class="section section-realizations" aria-label="Galeria realizacji Raricart">
    <div class="realizations-header-box">
        <span class="realizations-badge" data-i18n="gallery.badge">AUTENTYCZNE KADRY</span>
        <h2 class="realizations-title" data-i18n="gallery.title">Zobacz Raricart podczas wydarzeń</h2>
        <p class="realizations-subtitle" data-i18n="gallery.subtitle">
            Tak wygląda stacja, kiedy zaczyna się wydarzenie. Świeże produkty, przygotowanie na żywo, własne kompozycje i goście, którzy naprawdę chcą podejść do stacji.
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

    <div id="realizacje-parallax" class="section-gallery-parallax" <?php if($gallery_bg): ?>style="--bg-image: url('<?php echo htmlspecialchars($gallery_bg); ?>');"<?php endif; ?>>
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
                <span data-i18n="gallery.see_more">ZOBACZ WIĘCEJ REALIZACJI</span>
                <span class="realizations-count">(<?php echo $totalImages; ?>)</span>
                <span class="btn-arrow" aria-hidden="true">→</span>
            </button>
        </div>
    </div>
</section>
