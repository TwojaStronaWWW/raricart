<?php
require_once 'auth.php';
check_login();

// Prevent Caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Initial Status Fetch for PHP rendering
$status_file = __DIR__ . '/../assets/data/status.json';
$current_status = ['text' => '', 'enabled' => false];
if (file_exists($status_file)) {
    $current_status = json_decode(file_get_contents($status_file), true);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora - Raricart</title>
    <link rel="stylesheet" href="admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
</head>
<body>

<div class="header">
    <h1>Raricart Admin</h1>
    <a href="logout.php" class="logout-btn">Wyloguj</a>
</div>

<div class="container">
    <!-- Availability Manager -->
    <div class="card">
        <h2>Zarządzanie Dostępnością</h2>
        <p style="color:#666; font-size:0.9rem; margin-bottom:1rem;">Ten komunikat pojawi się na górze strony głównej.</p>
        <div class="status-group">
            <input type="text" id="statusText" class="status-input" placeholder="np. Zostały ostatnie 2 terminy na sierpień!" value="<?php echo htmlspecialchars($current_status['text']); ?>">
            <label class="toggle-switch">
                <input type="checkbox" id="statusToggle" <?php echo $current_status['enabled'] ? 'checked' : ''; ?>>
                <span class="slider"></span>
            </label>
            <button class="action-btn" onclick="saveStatus()">Zapisz</button>
        </div>
        </div>
    </div>

    <!-- --- SITE MEDIA SECTION --- -->
    <div class="card section-media">
        <h2>Media Strony</h2>
        <p style="margin-bottom: 1.5rem; opacity: 0.8;">Zarządzaj zdjęciami i wideo w kluczowych sekcjach.</p>

        <div class="media-grid">
            <!-- Hero Video -->
            <div class="media-item">
                <h4>Wideo w tle (Hero)</h4>
                <div class="media-preview video-preview" id="preview-hero_video">
                    <span>Brak wideo</span>
                </div>
                <label class="btn btn-secondary upload-btn">
                    Zmień wideo (MP4)
                    <input type="file" onchange="uploadContent(this, 'hero_video', 'video')" accept="video/mp4,video/webm">
                </label>
            </div>

            <!-- About Us Image -->
            <div class="media-item">
                <h4>O Nas (Zdjęcie)</h4>
                <div class="media-preview" id="preview-about_image"></div>
                <label class="btn btn-secondary upload-btn">
                    Wgraj zdjęcie
                    <input type="file" onchange="uploadContent(this, 'about_image', 'image')" accept="image/*">
                </label>
            </div>

            <!-- Offer Main Image -->
            <div class="media-item">
                <h4>Oferta (Tło Główne)</h4>
                <div class="media-preview" id="preview-offer_main_image"></div>
                <label class="btn btn-secondary upload-btn">
                    Wgraj zdjęcie
                    <input type="file" onchange="uploadContent(this, 'offer_main_image', 'image')" accept="image/*">
                </label>
            </div>

            <!-- Gallery Background (New) -->
            <div class="media-item">
                <h4>Galeria (Tło Parallaksy)</h4>
                <div class="media-preview" id="preview-gallery_bg"></div>
                <label class="btn btn-secondary upload-btn">
                    Wgraj zdjęcie
                    <input type="file" onchange="uploadContent(this, 'gallery_bg', 'image')" accept="image/*">
                </label>
            </div>

            <!-- Why Us Background (New) -->
            <div class="media-item">
                <h4>Sztuka kulinarna (Parallax)</h4>
                <div class="media-preview" id="preview-why_us_bg"></div>
                <label class="btn btn-secondary upload-btn">
                    Wgraj zdjęcie
                    <input type="file" onchange="uploadContent(this, 'why_us_bg', 'image')" accept="image/*">
                </label>
            </div>
        </div>

        <h3 style="margin-top:2rem; font-size: 1.1rem; border-top: 1px solid #eee; padding-top: 1rem;">Karty Oferty</h3>
        <div class="media-grid">
             <!-- Offer Cards -->
             <div class="media-item">
                <h4>Pancakes</h4>
                <div class="media-preview" id="preview-offer_cards-pancakes"></div>
                <label class="btn btn-secondary upload-btn">
                    Zmień
                    <input type="file" onchange="uploadContent(this, 'offer_cards.pancakes', 'image')" accept="image/*">
                </label>
            </div>
            <div class="media-item">
                <h4>Lody</h4>
                <div class="media-preview" id="preview-offer_cards-icecream"></div>
                <label class="btn btn-secondary upload-btn">
                    Zmień
                    <input type="file" onchange="uploadContent(this, 'offer_cards.icecream', 'image')" accept="image/*">
                </label>
            </div>
            <div class="media-item">
                <h4>Deska Serów</h4>
                <div class="media-preview" id="preview-offer_cards-cheese"></div>
                <label class="btn btn-secondary upload-btn">
                    Zmień
                    <input type="file" onchange="uploadContent(this, 'offer_cards.cheese', 'image')" accept="image/*">
                </label>
            </div>
        </div>
        
        <h3 style="margin-top:2rem; font-size: 1.1rem; border-top: 1px solid #eee; padding-top: 1rem;">Zdjęcia w Modalu Oferty (Szczegóły)</h3>
        <div class="media-grid">
             <!-- Offer Modal Images -->
             <div class="media-item">
                <h4>Pancakes (Modal)</h4>
                <div class="media-preview" id="preview-offer_modals-pancakes"></div>
                <label class="btn btn-secondary upload-btn">
                    Zmień
                    <input type="file" onchange="uploadContent(this, 'offer_modals.pancakes', 'image')" accept="image/*">
                </label>
            </div>
            <div class="media-item">
                <h4>Lody (Modal)</h4>
                <div class="media-preview" id="preview-offer_modals-icecream"></div>
                <label class="btn btn-secondary upload-btn">
                    Zmień
                    <input type="file" onchange="uploadContent(this, 'offer_modals.icecream', 'image')" accept="image/*">
                </label>
            </div>
            <div class="media-item">
                <h4>Deska Serów (Modal)</h4>
                <div class="media-preview" id="preview-offer_modals-cheese"></div>
                <label class="btn btn-secondary upload-btn">
                    Zmień
                    <input type="file" onchange="uploadContent(this, 'offer_modals.cheese', 'image')" accept="image/*">
                </label>
            </div>
    </div>

    <!-- Image Upload -->
    <div class="card">
        <h2>Dodaj Zdjęcia (Inteligentne Wgrywanie)</h2>
        <div id="drop-zone">
            <p>Przeciągnij zdjęcia tutaj lub kliknij, aby wybrać</p>
        </div>
        <input type="file" id="fileInput" multiple accept="image/*" style="display:none">
        <div id="upload-progress"><div id="progress-bar"></div></div>
        <div id="status-msg" style="margin-top:10px; text-align:center;"></div>
    </div>

    <!-- Gallery List -->
    <div class="card">
        <h2>Twoja Galeria</h2>
        <div class="gallery-grid" id="galleryList">
            <!-- Items loaded via JS -->
        </div>
    </div>

    <!-- Leads Management -->
    <div class="card" style="border-left: 4px solid #28a745;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="margin:0; padding:0; border:none; margin-bottom:0.5rem;">Baza Klientów (Excel)</h2>
                <p style="color:#666; font-size:0.9rem; margin:0;">Pobierz listę wszystkich zgłoszeń z formularza.</p>
            </div>
            <a href="download_leads.php" class="action-btn" style="text-decoration:none; display:flex; align-items:center; gap:8px; background:#28a745; font-weight:600;">
                <span>📥</span> Pobierz .CSV
            </a>
        </div>
    </div>

    <!-- Security / Account Management -->
    <div class="card">
        <h2>Konto Administratora</h2>
        <p style="margin-bottom:1rem; opacity:0.8;">Zalogowany jako: <strong><?php echo htmlspecialchars(get_current_user_email()); ?></strong></p>
        
        <div class="status-group" style="display:block;">
            <h4 style="margin:0 0 10px 0; font-size:0.95rem;">Zmiana Hasła</h4>
            <div style="display:grid; gap:10px; max-width:400px;">
                <input type="password" id="oldPass" class="status-input" placeholder="Stare hasło">
                <input type="password" id="newPass" class="status-input" placeholder="Nowe hasło">
                <input type="password" id="confirmPass" class="status-input" placeholder="Potwierdź nowe hasło">
                <button class="action-btn" onclick="changePassword()">Zmień Hasło</button>
            </div>
        </div>
    </div>
</div>

<script>
function changePassword() {
    const oldPass = document.getElementById('oldPass').value;
    const newPass = document.getElementById('newPass').value;
    const confirmPass = document.getElementById('confirmPass').value;
    
    if(!oldPass || !newPass) {
        alert('Podaj hasła.');
        return;
    }
    
    fetch('change_password.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ old_password: oldPass, new_password: newPass, confirm_password: confirmPass })
    })
    .then(r => r.json())
    .then(data => {
        alert((data.status === 'success' ? '✅ ' : '❌ ') + data.message);
        if(data.status === 'success') {
            document.getElementById('oldPass').value = '';
            document.getElementById('newPass').value = '';
            document.getElementById('confirmPass').value = '';
        }
    });
}
</script>

<script>

// --- Status Manager ---
function saveStatus() {
    const text = document.getElementById('statusText').value;
    const enabled = document.getElementById('statusToggle').checked;
    
    fetch('save_status.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ text, enabled })
    })
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') alert('Zapisano pomyślnie!');
        else alert('Błąd zapisu');
    });
}

// --- Gallery Manager ---


// Current gallery state
let galleryImages = [];

function loadGallery() {
    fetch('../api/gallery.php')
    .then(r => r.json())
    .then(images => {
        galleryImages = images; // Store state
        renderGallery();
    });
}

function renderGallery() {
    const grid = document.getElementById('galleryList');
    grid.innerHTML = '';
    galleryImages.forEach((src, index) => {
        const div = document.createElement('div');
        div.className = 'gallery-item';
        div.setAttribute('data-src', src); // Ważne dla SortableJS
        
        let displaySrc;
        if (src.startsWith('http') || src.startsWith('//')) {
             displaySrc = src;
        } else {
             const cleanPath = src.replace(/^(\.\.\/)+/, '');
             displaySrc = '../' + cleanPath;
        }
        
        div.innerHTML = `
            <img src="${displaySrc}" loading="lazy">
            <button class="delete-btn" onclick="deleteImageInGrid(${index})" title="Usuń">×</button>
        `;
        grid.appendChild(div);
    });

    initDragAndDrop();
}

function initDragAndDrop() {
    const el = document.getElementById('galleryList');
    if(el.sortable) el.sortable.destroy(); // Cleanup old instance if exists

    el.sortable = new Sortable(el, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function () {
            const newOrder = [];
            document.querySelectorAll('#galleryList .gallery-item').forEach(item => {
                newOrder.push(item.getAttribute('data-src'));
            });

            galleryImages = newOrder;
            saveGalleryState();
        }
    });
}

function saveGalleryState() {
    fetch('save_gallery.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ images: galleryImages })
    })
    .then(r => r.json())
    .then(data => {
        if(data.status !== 'success') console.error('Save failed:', data);
    })
    .catch(err => console.error('Save error:', err));
}

function deleteImageInGrid(index) {
    if(!confirm('Czy na pewno usunąć to zdjęcie?')) return;
    
    const imagePath = galleryImages[index];
    
    // 1. Remove from state
    galleryImages.splice(index, 1);
    renderGallery();
    saveGalleryState();
    
    // 2. If it's a local file, try to delete securely
    if (!imagePath.startsWith('http')) {
        fetch('delete.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ file: imagePath })
        });
    }
}

// --- Image Uploader (Client-Side Resizing) ---
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('fileInput');
const progressBar = document.getElementById('progress-bar');
const progressContainer = document.getElementById('upload-progress');

// Click to Upload
dropZone.addEventListener('click', () => fileInput.click());

// Drag & Drop
const events = ['dragenter', 'dragover', 'dragleave', 'drop'];
events.forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

dropZone.addEventListener('dragenter', () => dropZone.classList.add('hover'));
dropZone.addEventListener('dragover', () => dropZone.classList.add('hover'));
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('hover'));
dropZone.addEventListener('drop', (e) => {
    dropZone.classList.remove('hover');
    handleFiles(e.dataTransfer.files);
});

fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

async function handleFiles(files) {
    if (files.length === 0) return;
    
    progressContainer.style.display = 'block';
    
    let completed = 0;
    const total = files.length;
    let newImages = [];
    
    for (const file of files) {
        try {
            const resizedBlob = await resizeImage(file);
            const response = await uploadFile(resizedBlob, file.name);
            const textResponse = await response.text();
            
            let result;
            try {
                result = JSON.parse(textResponse);
            } catch (e) {
                console.error("Server Raw Response:", textResponse); // Debug logic
                throw new Error("Server returned invalid JSON. Check console.");
            }
            
            if (result.status === 'success') {
                newImages.push(result.file);
            } else {
                alert('Błąd: ' + result.message);
            }
            
            completed++;
            progressBar.style.width = (completed / total * 100) + '%';
        } catch (err) {
            console.error(err);
            alert('Błąd przetwarzania: ' + err.message);
        }
    }
    
    // Add new images to start of gallery
    // Add new images to start of gallery
    if (newImages.length > 0) {
        galleryImages = [...newImages, ...galleryImages];
        saveGalleryState();
    }
    
    setTimeout(() => {
        progressContainer.style.display = 'none';
        progressBar.style.width = '0%';
        renderGallery();
        alert('Wgrywanie zakończone!');
    }, 500);
}

// --- SITE MEDIA MANAGEMENT ---
let siteContent = {};

function loadSiteContent() {
    fetch('../assets/data/content.json')
    .then(r => r.json())
    .then(data => {
        siteContent = data || {};
        renderContentPreviews();
    })
    .catch(() => console.log('No content config found, using defaults'));
}

function renderContentPreviews() {
    // Helper to set preview
    const setPrev = (id, src, type='image') => {
        const el = document.getElementById('preview-' + id);
        if(!el) return;
        el.innerHTML = '';
        
        let displaySrc;
        if(src) {
             if (src.startsWith('http') || src.startsWith('//')) {
                 displaySrc = src;
             } else {
                 const cleanPath = src.replace(/^(\.\.\/)+/, '');
                 displaySrc = '../' + cleanPath;
             }
             
            if(type === 'video') {
                el.innerHTML = `<video src="${displaySrc}" muted loop autoplay playsinline></video>`;
            } else {
                el.innerHTML = `<img src="${displaySrc}" />`;
            }
        } else {
            el.innerHTML = `<span>Brak</span>`;
        }
    };

    setPrev('hero_video', siteContent.hero_video, 'video');
    setPrev('about_image', siteContent.about_image);
    setPrev('offer_main_image', siteContent.offer_main_image);
    setPrev('gallery_bg', siteContent.gallery_bg);
    setPrev('why_us_bg', siteContent.why_us_bg);
    
    if(siteContent.offer_cards) {
        setPrev('offer_cards-pancakes', siteContent.offer_cards.pancakes);
        setPrev('offer_cards-icecream', siteContent.offer_cards.icecream);
        setPrev('offer_cards-cheese', siteContent.offer_cards.cheese);
    }

    if(siteContent.offer_modals) {
        setPrev('offer_modals-pancakes', siteContent.offer_modals.pancakes);
        setPrev('offer_modals-icecream', siteContent.offer_modals.icecream);
        setPrev('offer_modals-cheese', siteContent.offer_modals.cheese);
    }
}

async function uploadContent(input, key, type='image') {
    const file = input.files[0];
    if(!file) return;

    const formData = new FormData();
    formData.append('image', file);
    formData.append('target', 'content'); // Output to assets/content

    const btn = input.parentElement;
    const originalText = btn.innerText;
    btn.innerText = 'Wgrywanie...';

    try {
        const res = await fetch('upload.php', { 
            method: 'POST', 
            headers: {
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData 
        });
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); } catch(e) { throw new Error('Invalid Server JSON'); }

        if(data.status === 'success') {
            // Update State
            if(key.includes('.')) {
                // Nested key (offer_cards.pancakes)
                const [parent, child] = key.split('.');
                if(!siteContent[parent]) siteContent[parent] = {};
                siteContent[parent][child] = data.file;
            } else {
                siteContent[key] = data.file;
            }
            
            // Render & Save
            renderContentPreviews();
            saveContentState();
        } else {
            alert('Błąd: ' + data.message);
        }
    } catch(err) {
        alert('Błąd uploadu: ' + err.message);
    } finally {
        btn.innerText = originalText;
        btn.appendChild(input); // Re-attach input
    }
}

function saveContentState() {
    fetch('save_content.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(siteContent)
    });
}

// Init Content Load
loadSiteContent();



function resizeImage(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                const MAX_WIDTH = 2500;
                const MAX_HEIGHT = 2000;
                let width = img.width;
                let height = img.height;
                
                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }
                
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                
                canvas.toBlob((blob) => resolve(blob), 'image/jpeg', 0.95);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function uploadFile(blob, filename) {
    const formData = new FormData();
    formData.append('image', blob, filename);
    
    return fetch('upload.php', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    });
}

// Init
loadGallery();
</script>

</body>
</html>
