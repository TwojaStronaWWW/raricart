'use strict';

(function () {
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') || '' : '';
    }

    function updateCsrfToken(newToken) {
        if (!newToken) return;
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            meta.setAttribute('content', newToken);
        }
        document.querySelectorAll('input[name="csrf_token"]').forEach((input) => {
            input.value = newToken;
        });
    }

    // Flash message dismiss button
    document.querySelectorAll('[data-dismiss-parent]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (btn.parentElement) {
                btn.parentElement.remove();
            }
        });
    });

    // --- Password Change ---
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', () => {
            const oldPassEl = document.getElementById('oldPass');
            const newPassEl = document.getElementById('newPass');
            const confirmPassEl = document.getElementById('confirmPass');
            const oldPass = oldPassEl ? oldPassEl.value : '';
            const newPass = newPassEl ? newPassEl.value : '';
            const confirmPass = confirmPassEl ? confirmPassEl.value : '';

            if (!oldPass || !newPass || !confirmPass) {
                alert('Wypełnij wszystkie pola hasła.');
                return;
            }

            fetch('change_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify({
                    old_password: oldPass,
                    new_password: newPass,
                    confirm_password: confirmPass
                })
            })
                .then((r) => r.json())
                .then((data) => {
                    alert((data.status === 'success' ? '✅ ' : '❌ ') + (data.message || ''));
                    if (data.status === 'success') {
                        if (data.csrf_token) {
                            updateCsrfToken(data.csrf_token);
                        }
                        if (document.getElementById('forcedPasswordCard')) {
                            window.location.reload();
                            return;
                        }
                        if (oldPassEl) oldPassEl.value = '';
                        if (newPassEl) newPassEl.value = '';
                        if (confirmPassEl) confirmPassEl.value = '';
                    }
                })
                .catch((err) => {
                    console.error('Błąd zmiany hasła:', err);
                    alert('Wystąpił błąd połączenia podczas zmiany hasła.');
                });
        });
    }

    // If forced password change screen is active, stop initializing CMS modules
    if (document.getElementById('forcedPasswordCard')) {
        return;
    }

    // --- Availability / Status Manager ---
    const saveStatusBtn = document.getElementById('saveStatusBtn');
    if (saveStatusBtn) {
        saveStatusBtn.addEventListener('click', () => {
            const textEl = document.getElementById('statusText');
            const toggleEl = document.getElementById('statusToggle');
            const text = textEl ? textEl.value : '';
            const enabled = Boolean(toggleEl && toggleEl.checked);

            fetch('save_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify({ text, enabled })
            })
                .then((r) => r.json())
                .then((data) => {
                    if (data.status === 'success') {
                        alert('Zapisano pomyślnie!');
                    } else {
                        alert('Błąd zapisu: ' + (data.message || 'Nieznany błąd'));
                    }
                })
                .catch((err) => {
                    console.error('Błąd zapisu statusu:', err);
                    alert('Błąd zapisu statusu.');
                });
        });
    }

    // --- Helper: Safe Relative/HTTPS Media URL ---
    function resolveMediaPreviewUrl(src) {
        if (!src || typeof src !== 'string') return '';
        const trimmed = src.trim();
        if (/^https?:\/\//i.test(trimmed) || trimmed.startsWith('//')) {
            return trimmed;
        }
        const cleanPath = trimmed.replace(/^(\.\.\/|\/)+/, '');
        return '../' + cleanPath;
    }

    // --- Gallery Manager ---
    let galleryImages = [];

    function initDragAndDrop() {
        const el = document.getElementById('galleryList');
        if (!el || typeof window.Sortable !== 'function') return;
        if (el.sortable) {
            el.sortable.destroy();
        }
        el.sortable = new window.Sortable(el, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function () {
                const newOrder = [];
                document.querySelectorAll('#galleryList .gallery-item').forEach((itemEl) => {
                    const raw = itemEl.getAttribute('data-item') || '';
                    try {
                        newOrder.push(JSON.parse(raw));
                    } catch {
                        newOrder.push(raw);
                    }
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
                'X-CSRF-Token': getCsrfToken()
            },
            body: JSON.stringify({ images: galleryImages })
        })
            .then((r) => r.json())
            .then((data) => {
                if (data.status !== 'success') {
                    console.error('Save failed:', data);
                }
            })
            .catch((err) => console.error('Save error:', err));
    }

    function deleteImageInGrid(index) {
        if (!confirm('Czy na pewno usunąć to zdjęcie?')) return;

        const item = galleryImages[index];
        const fileToDelete = typeof item === 'string' ? item : (item && (item.url || item.path || '')) || '';

        galleryImages.splice(index, 1);
        renderGallery();
        saveGalleryState();

        if (fileToDelete) {
            fetch('delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify({ file: fileToDelete })
            }).catch((err) => console.error('Delete error:', err));
        }
    }

    function renderGallery() {
        const grid = document.getElementById('galleryList');
        if (!grid) return;
        grid.textContent = '';

        galleryImages.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = 'gallery-item';
            div.setAttribute('data-index', String(index));
            div.setAttribute('data-item', typeof item === 'object' ? JSON.stringify(item) : String(item));

            const rawSrc = typeof item === 'string' ? item : (item.url || item.src || '');
            const displaySrc = resolveMediaPreviewUrl(rawSrc);

            const img = document.createElement('img');
            img.src = displaySrc;
            img.loading = 'lazy';
            img.alt = 'Realizacja ' + (index + 1);

            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.className = 'delete-btn';
            delBtn.title = 'Usuń';
            delBtn.textContent = '×';
            delBtn.addEventListener('click', () => deleteImageInGrid(index));

            div.appendChild(img);
            div.appendChild(delBtn);
            grid.appendChild(div);
        });

        initDragAndDrop();
    }

    function loadGallery() {
        fetch('../api/get_gallery.php')
            .then((r) => r.json())
            .then((images) => {
                galleryImages = Array.isArray(images) ? images : [];
                renderGallery();
            })
            .catch((err) => console.error('Gallery load error:', err));
    }

    // --- Image Resizer & Bulk Gallery Uploader ---
    function resizeImage(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onerror = () => reject(new Error('Nie udało się odczytać pliku.'));
            reader.onload = (e) => {
                const img = new Image();
                img.onerror = () => reject(new Error('Nieprawidłowy plik obrazu.'));
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
                    } else if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }

                    canvas.width = Math.max(1, Math.round(width));
                    canvas.height = Math.max(1, Math.round(height));
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                    let outputType = file.type;
                    if (outputType !== 'image/png' && outputType !== 'image/webp') {
                        outputType = 'image/jpeg';
                    }

                    canvas.toBlob((blob) => {
                        if (blob) resolve(blob);
                        else reject(new Error('Błąd konwersji obrazu.'));
                    }, outputType, 0.95);
                };
                img.src = String(e.target.result || '');
            };
            reader.readAsDataURL(file);
        });
    }

    function uploadFile(blob, filename) {
        const formData = new FormData();
        formData.append('image', blob, filename);
        formData.append('target', 'gallery');

        return fetch('upload.php', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': getCsrfToken()
            },
            body: formData
        });
    }

    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('fileInput');
    const progressBar = document.getElementById('progress-bar');
    const progressContainer = document.getElementById('upload-progress');

    async function handleFiles(files) {
        if (!files || files.length === 0) return;

        if (progressContainer) progressContainer.classList.add('is-visible');

        let completed = 0;
        const total = files.length;
        const newImages = [];

        for (const file of files) {
            try {
                const resizedBlob = await resizeImage(file);
                const response = await uploadFile(resizedBlob, file.name);
                const textResponse = await response.text();

                let result;
                try {
                    result = JSON.parse(textResponse);
                } catch {
                    console.error('Server Raw Response:', textResponse);
                    throw new Error('Serwer zwrócił nieprawidłową odpowiedź JSON.');
                }

                if (result.status === 'success') {
                    newImages.push({
                        id: Math.random().toString(16).substring(2, 18),
                        url: result.file,
                        src: result.file,
                        path: result.path || result.file,
                        created_at: new Date().toISOString(),
                        original_name: file.name
                    });
                } else {
                    alert('Błąd: ' + (result.message || 'Nie udało się wgrać pliku.'));
                }

                completed++;
                if (progressBar) {
                    progressBar.style.setProperty('width', (completed / total * 100) + '%');
                }
            } catch (err) {
                console.error(err);
                alert('Błąd przetwarzania: ' + err.message);
            }
        }

        if (newImages.length > 0) {
            galleryImages = [...newImages, ...galleryImages];
            saveGalleryState();
        }

        setTimeout(() => {
            if (progressContainer) progressContainer.classList.remove('is-visible');
            if (progressBar) progressBar.style.setProperty('width', '0%');
            renderGallery();
            alert('Wgrywanie zakończone!');
        }, 500);
    }

    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });
        dropZone.addEventListener('dragenter', () => dropZone.classList.add('hover'));
        dropZone.addEventListener('dragover', () => dropZone.classList.add('hover'));
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('hover'));
        dropZone.addEventListener('drop', (e) => {
            dropZone.classList.remove('hover');
            if (e.dataTransfer && e.dataTransfer.files) {
                handleFiles(e.dataTransfer.files);
            }
        });
        fileInput.addEventListener('change', (e) => {
            if (e.target && e.target.files) {
                handleFiles(e.target.files);
            }
        });
    }

    // --- Site Media Management (SSR-bootstrapped from #initialSiteContent without HTTP access to assets/data/) ---
    let siteContent = {};
    const initialContentEl = document.getElementById('initialSiteContent');
    if (initialContentEl && initialContentEl.textContent) {
        try {
            const parsed = JSON.parse(initialContentEl.textContent);
            if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                siteContent = parsed;
            }
        } catch (err) {
            console.error('Błąd parsowania initialSiteContent:', err);
        }
    }

    function renderContentPreviews() {
        const setPrev = (id, src, type = 'image') => {
            const el = document.getElementById('preview-' + id);
            if (!el) return;
            el.textContent = '';

            const displaySrc = resolveMediaPreviewUrl(src);
            if (displaySrc) {
                if (type === 'video') {
                    const video = document.createElement('video');
                    video.src = displaySrc;
                    video.muted = true;
                    video.loop = true;
                    video.autoplay = true;
                    video.playsInline = true;
                    el.appendChild(video);
                } else {
                    const img = document.createElement('img');
                    img.src = displaySrc;
                    img.alt = id;
                    el.appendChild(img);
                }
            } else {
                const span = document.createElement('span');
                span.textContent = 'Brak';
                el.appendChild(span);
            }
        };

        setPrev('hero_video', siteContent.hero_video, 'video');
        setPrev('about_image', siteContent.about_image);
        setPrev('offer_main_image', siteContent.offer_main_image);
        setPrev('gallery_bg', siteContent.gallery_bg);
        setPrev('why_us_bg', siteContent.why_us_bg);

        if (siteContent.offer_cards) {
            setPrev('offer_cards-pancakes', siteContent.offer_cards.pancakes);
            setPrev('offer_cards-icecream', siteContent.offer_cards.icecream);
            setPrev('offer_cards-cheese', siteContent.offer_cards.cheese);
        }

        if (siteContent.offer_modals) {
            setPrev('offer_modals-pancakes', siteContent.offer_modals.pancakes);
            setPrev('offer_modals-icecream', siteContent.offer_modals.icecream);
            setPrev('offer_modals-cheese', siteContent.offer_modals.cheese);
        }
    }

    function saveContentState() {
        fetch('save_content.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': getCsrfToken()
            },
            body: JSON.stringify(siteContent)
        })
            .then((r) => r.json())
            .then((data) => {
                if (data.status !== 'success') {
                    alert('Błąd zapisu stanu mediów: ' + (data.message || 'Nieznany błąd'));
                }
            })
            .catch((err) => {
                console.error('Błąd wywołania zapisu:', err);
                alert('Wystąpił błąd podczas zapisu struktury mediów.');
            });
    }

    async function uploadContent(input, key, type) {
        const file = input.files && input.files[0];
        if (!file) return;

        const labelEl = input.closest('label');
        const labelTextNode = labelEl ? Array.from(labelEl.childNodes).find((n) => n.nodeType === Node.TEXT_NODE) : null;
        const originalText = labelTextNode ? labelTextNode.nodeValue : '';
        if (labelTextNode) {
            labelTextNode.nodeValue = 'Wgrywanie...';
        }

        try {
            let uploadData;
            if (type === 'image' && file.type.startsWith('image/')) {
                uploadData = await resizeImage(file);
            } else {
                uploadData = file;
            }

            const formData = new FormData();
            formData.append('image', uploadData, file.name || 'upload.webp');
            formData.append('target', 'content');

            const res = await fetch('upload.php', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': getCsrfToken()
                },
                body: formData
            });
            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch {
                throw new Error('Nieprawidłowa odpowiedź JSON serwera.');
            }

            if (data.status === 'success') {
                if (key.includes('.')) {
                    const [parent, child] = key.split('.');
                    if (!siteContent[parent] || typeof siteContent[parent] !== 'object') {
                        siteContent[parent] = {};
                    }
                    siteContent[parent][child] = data.file;
                } else {
                    siteContent[key] = data.file;
                }
                renderContentPreviews();
                saveContentState();
            } else {
                alert('Błąd: ' + (data.message || 'Nie udało się wgrać pliku.'));
            }
        } catch (err) {
            alert('Błąd uploadu: ' + err.message);
        } finally {
            if (labelTextNode) {
                labelTextNode.nodeValue = originalText;
            }
            input.value = '';
        }
    }

    document.querySelectorAll('input[data-content-key]').forEach((input) => {
        input.addEventListener('change', () => {
            const key = input.getAttribute('data-content-key') || '';
            const mediaType = input.getAttribute('data-media-type') || 'image';
            if (key) {
                uploadContent(input, key, mediaType);
            }
        });
    });

    renderContentPreviews();
    loadGallery();
})();
