    <!-- Modals -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <button class="modal-close" aria-label="Zamknij modal">&times;</button>
            <img id="modalImg" src="" alt="">
            <div class="modal-description">
                <h3 id="modalTitle"></h3>
                <div id="modalText"></div>
            </div>
        </div>
    </div>

    <div id="galleryModal" class="gallery-modal">
        <div class="gallery-modal-wrapper">
             <button class="gallery-modal-close" aria-label="Zamknij galerię">&times;</button>
             <img id="galleryModalImg" src="" alt="Powiększone zdjęcie realizacji">
        </div>
        <button class="gallery-nav gallery-prev" aria-label="Poprzednie zdjęcie">‹</button>
        <button class="gallery-nav gallery-next" aria-label="Następne zdjęcie">›</button>
    </div>

    <!-- Cookie Banner -->
    <div id="cookie-banner" class="cookie-banner" style="display: none;">
        <div class="cookie-content">
            <p data-i18n="cookies.text">
                Ta strona używa plików cookies, aby zapewnić najlepszą jakość. Korzystając ze strony, zgadzasz
                się na ich użycie.
            </p>
            <div class="cookie-buttons">
                <button id="reject-cookies" class="cta-secondary small-btn"
                    data-i18n="cookies.reject">Odrzuć</button>
                <button id="accept-cookies" class="cta-primary small-btn" data-i18n="cookies.accept">Akceptuję</button>
            </div>
        </div>
    </div>
