<?php
// parts/contact.php - Sekcja 13: Odchudzony Formularz Kontaktowy (Lejek 2026)
?>
<section id="kontakt" class="section section-contact" aria-label="Formularz kontaktowy i zapytanie o termin">
    <div class="contact-header-box">
        <span class="contact-badge" data-i18n="contact.badge">REZERWACJA & WYCENA</span>
        <h2 class="contact-title" data-i18n="contact.title.short">Opowiedz nam o swoim wydarzeniu.<br>Przygotujemy indywidualną wycenę.</h2>
        <p class="contact-subtitle" data-i18n="contact.subtitle">
            Wypełnij poniższe pola — sprawdzimy nasz kalendarz i wrócimy z precyzyjną kalkulacją w ciągu 24h.
        </p>
        
        <div class="progress-container">
            <div id="form-progress"></div>
        </div>
        <p id="progress-text" class="progress-text" data-i18n="form.progress_text">
            Uzupełnij dane, abyśmy mogli przygotować ofertę (0%)
        </p>
    </div>

    <form id="form" class="contact-form" novalidate>
        <div id="availability-notice" class="availability-notice"></div>
        
        <!-- Honeypot for bots -->
        <input type="text" name="website_check" class="honeypot-field" tabindex="-1" autocomplete="off">

        <div class="form-grid">
            <!-- Row 1: Imię i Email -->
            <div class="form-group">
                <label for="name" data-i18n="form.name">Imię i Nazwisko *</label>
                <input type="text" id="name" name="name" placeholder="np. Anna Kowalska" required>
            </div>
            <div class="form-group">
                <label for="email" data-i18n="form.email">Email *</label>
                <input type="email" id="email" name="email" placeholder="np. anna@domena.pl" required>
            </div>

            <!-- Row 2: Telefon i Data -->
            <div class="form-group">
                <label for="phone" data-i18n="form.phone">Telefon (ułatwia szybki kontakt)</label>
                <input type="tel" id="phone" name="phone" placeholder="np. +48 500 600 700">
            </div>
            <div class="form-group">
                <label for="date" data-i18n="form.date">Data Wydarzenia *</label>
                <input type="date" id="date" name="date" required>
            </div>

            <!-- Row 3: Miejsce i Liczba Gości -->
            <div class="form-group">
                <label for="location" data-i18n="form.location">Miejsce Wydarzenia *</label>
                <input type="text" id="location" name="location" placeholder="np. Katowice, Hotel Monopol / plener" data-i18n-placeholder="form.location_placeholder" required>
            </div>
            <div class="form-group">
                <label for="guests" data-i18n="form.guests_label">Liczba Gości *</label>
                <input type="number" id="guests" name="guests" min="1" max="9999" placeholder="np. 80" data-i18n-placeholder="form.guests_placeholder" required>
            </div>

            <!-- Row 4: Rodzaj Wydarzenia -->
            <div class="form-group full-width">
                <label for="event_type" data-i18n="form.event_type">Rodzaj Wydarzenia *</label>
                <select id="event_type" name="event_type" required>
                    <option value="" data-i18n="form.select_placeholder">Wybierz rodzaj wydarzenia...</option>
                    <option value="wedding" data-i18n="form.types.wedding">Wesele / Poprawiny</option>
                    <option value="corporate" data-i18n="form.types.corporate">Event Firmowy / Gala</option>
                    <option value="private" data-i18n="form.types.private">Przyjęcie Prywatne (Urodziny, Ogród)</option>
                    <option value="festival" data-i18n="form.types.festival">Festiwal / Piknik / Plener</option>
                    <option value="other" data-i18n="form.types.other">Inne wydarzenie</option>
                </select>
            </div>

            <!-- Row 5: Wybór Stacji -->
            <div class="form-group full-width">
                <label data-i18n="form.stations">Która stacja Cię interesuje? *</label>
                <div class="checkbox-group">
                    <label class="checkbox-item" for="p">
                        <input type="checkbox" id="p" name="stations" value="pancakes">
                        <span data-i18n="form.st_pancakes">Mini Pancakes</span>
                    </label>
                    <label class="checkbox-item" for="l">
                        <input type="checkbox" id="l" name="stations" value="lody">
                        <span data-i18n="form.st_icecream">Lody Włoskie</span>
                    </label>
                    <label class="checkbox-item" for="s">
                        <input type="checkbox" id="s" name="stations" value="sery">
                        <span data-i18n="form.st_cheese">Deska Serów</span>
                    </label>
                    <label class="checkbox-item" for="u">
                        <input type="checkbox" id="u" name="stations" value="unsure">
                        <span data-i18n="form.st_unsure">Jeszcze nie wiem / do ustalenia</span>
                    </label>
                </div>
            </div>

            <!-- Row 6: Dodatkowe informacje -->
            <div class="form-group full-width">
                <label for="message" data-i18n="form.message">Dodatkowe Informacje (opcjonalnie)</label>
                <textarea id="message" name="message" rows="3" placeholder="Styl przyjęcia, preferowane godziny serwisu lub dodatkowe pytania..." data-i18n-placeholder="form.message_placeholder"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="cta-primary form-submit-btn" data-i18n="form.submit">
                <span>SPRAWDŹ DOSTĘPNOŚĆ I OTRZYMAJ WYCENĘ</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
            <p class="form-privacy-note" data-i18n="form.privacy_note">
                🔒 Twoje dane są bezpieczne. Otrzymasz bezpłatną, niezobowiązującą wycenę w ciągu 24h.
            </p>
        </div>
    </form>
</section>
