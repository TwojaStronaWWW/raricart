<?php
// parts/contact.php - Sekcja 12: Formularz Wyceny i Rezerwacji (wg specyfikacji Emilii)
?>
<section id="kontakt" class="section section-contact" aria-label="Formularz wyceny i rezerwacji terminu">
    <div class="contact-header-box">
        <span class="contact-badge" data-i18n="contact.badge">WYCENA I REZERWACJA</span>
        <h2 class="contact-title" data-i18n="contact.title">OPOWIEDZ NAM O SWOIM PRZYJĘCIU.</h2>
        <div class="progress-container">
            <div id="form-progress"></div>
        </div>
        <p id="progress-text" class="progress-text" data-i18n="form.progress_start">Wypełnij pola poniżej (zajmie to mniej niż minutę)...</p>
    </div>

    <form class="contact-form" novalidate>
        <!-- Honeypot for bots -->
        <input type="text" name="website" class="honeypot-field" tabindex="-1" autocomplete="off">

        <!-- Availability Notice -->
        <div id="availability-notice" class="availability-notice"></div>

        <div class="form-grid" id="contactFormFields">
            <!-- Row 1: Name & Email -->
            <div class="form-group">
                <label for="name" data-i18n="form.name">Imię i nazwisko *</label>
                <input type="text" id="name" name="name" required autocomplete="name" placeholder="np. Anna Kowalska" data-i18n-placeholder="form.name_ph">
            </div>

            <div class="form-group">
                <label for="email" data-i18n="form.email">E-mail *</label>
                <input type="email" id="email" name="email" required autocomplete="email" placeholder="np. anna@domena.pl" data-i18n-placeholder="form.email_ph">
            </div>

            <!-- Row 2: Phone & Date -->
            <div class="form-group">
                <label for="phone" data-i18n="form.phone">Telefon</label>
                <input type="tel" id="phone" name="phone" autocomplete="tel" placeholder="np. +48 500 600 700" data-i18n-placeholder="form.phone_ph">
            </div>

            <div class="form-group">
                <label for="date" data-i18n="form.date">Data przyjęcia *</label>
                <input type="date" id="date" name="date" required>
            </div>

            <!-- Row 3: Location & Guests -->
            <div class="form-group">
                <label for="location" data-i18n="form.location">Miejsce przyjęcia *</label>
                <input type="text" id="location" name="location" required autocomplete="address-level2" placeholder="np. Katowice, Hotel Monopol / plener" data-i18n-placeholder="form.location_ph">
            </div>

            <div class="form-group">
                <label for="guests" data-i18n="form.guests">Liczba gości *</label>
                <input type="number" id="guests" name="guests" required min="1" placeholder="np. 80" data-i18n-placeholder="form.guests_ph">
            </div>

            <!-- Row 4: Event Type -->
            <div class="form-group full-width">
                <label for="event_type" data-i18n="form.event_type">Rodzaj przyjęcia *</label>
                <select id="event_type" name="event_type" required aria-label="Wybierz rodzaj przyjęcia">
                    <option value="" disabled selected data-i18n="form.event_select">Wybierz z listy...</option>
                    <option value="wedding" data-i18n="form.event_wedding">Wesele / Poprawiny</option>
                    <option value="corporate" data-i18n="form.event_corporate">Event firmowy / Konferencja / Gala B2B</option>
                    <option value="private" data-i18n="form.event_private">Przyjęcie prywatne (Urodziny, Komunia, Rocznica)</option>
                    <option value="agency" data-i18n="form.event_agency">Współpraca agencyjna / Wedding Planner</option>
                    <option value="other" data-i18n="form.event_other">Inne wydarzenie</option>
                </select>
            </div>

            <!-- Row 5: Stations (Checkboxes) -->
            <div class="form-group full-width">
                <label data-i18n="form.stations_label">Które stoisko Cię interesuje? *</label>
                <div class="checkbox-group">
                    <label class="checkbox-item">
                        <input type="checkbox" name="stations" value="pancakes">
                        <span data-i18n="form.station_pancakes">Mini pancakes</span>
                    </label>
                    <label class="checkbox-item">
                        <input type="checkbox" name="stations" value="lody">
                        <span data-i18n="form.station_ice">Lody włoskie</span>
                    </label>
                    <label class="checkbox-item">
                        <input type="checkbox" name="stations" value="sery">
                        <span data-i18n="form.station_cheese">Deska serów i wędlin</span>
                    </label>
                    <label class="checkbox-item">
                        <input type="checkbox" name="stations" value="nie_wiem">
                        <span data-i18n="form.stations_advise">Nie wiem jeszcze / chcę doradztwa</span>
                    </label>
                </div>
            </div>

            <!-- Row 6: Message -->
            <div class="form-group full-width">
                <label for="message" data-i18n="form.message">Dodatkowe informacje (opcjonalnie)</label>
                <textarea id="message" name="message" rows="3" placeholder="Opisz krótko harmonogram, motyw przewodni lub zadaj pytanie..." data-i18n-placeholder="form.message_ph"></textarea>
            </div>
        </div>

        <div class="form-actions" id="contactFormActions">
            <button type="submit" class="cta-primary form-submit-btn">
                <span data-i18n="form.submit">ZAPYTAJ O TERMIN I WYCENĘ</span>
                <span class="btn-arrow" aria-hidden="true">→</span>
            </button>
        </div>

        <!-- Komunikat potwierdzający po wysłaniu formularza (w miejscu formularza) -->
        <div id="formSuccessConfirmation" class="form-success-confirmation" hidden aria-live="polite">
            <h3 class="form-success-title" data-i18n="form.success_title">Dziękujemy za wiadomość.</h3>
            <p class="form-success-text" data-i18n="form.success_msg">
                Twoje zapytanie do nas dotarło. Sprawdzimy dostępność terminu i w ciągu 24 godzin wrócimy z indywidualną ofertą.
            </p>
        </div>
    </form>
</section>
