/**
 * script.js - Główny punkt wejścia i orkiestrator modułów Raricart (Lejek 2026)
 * Architektura: Vanilla JS ES6+ z pełną separacją odpowiedzialności (SoC)
 * 
 * Moduły:
 *  - modules/translations.js - Słowniki wielojęzyczne (PL/EN/ES)
 *  - modules/i18n.js         - Dynamiczna podmiana treści (data-i18n)
 *  - modules/nav.js          - Pasek nawigacji, logo, hamburger i menu mobilne
 *  - modules/modals.js       - Okna modalne stacji live food i detale oferty
 *  - modules/gallery.js      - Dynamiczna siatka realizacji, lightbox i parallax
 *  - modules/contact.js      - Formularz, walidacja, autozapis leadów i toasty
 *  - modules/cookies.js      - Baner zgody RODO i opóźnione ładowanie GA4
 *  - modules/scroll.js       - Płynny scroll, deep linking (?goto= / #) i floating CTA
 */

import { translations } from './modules/translations.js';
import { initI18n } from './modules/i18n.js';
import { initNav } from './modules/nav.js';
import { initStationModals } from './modules/modals.js';
import { initGallery } from './modules/gallery.js';
import { initContactForm } from './modules/contact.js';
import { initCookies } from './modules/cookies.js';
import { initScrollFeatures } from './modules/scroll.js';

document.addEventListener('DOMContentLoaded', () => {
	// 1. Wielojęzyczność
	initI18n(translations);

	// 2. Nawigacja i menu mobilne
	initNav();

	// 3. Okna modalne stacji kulinarnych
	initStationModals(translations);

	// 4. Galeria zdjęć i pełny lightbox
	initGallery();

	// 5. Formularz kontaktowy, walidacja i lead capture
	initContactForm();

	// 6. Baner cookies i analityka
	initCookies();

	// 7. Przewijanie do sekcji i pływające CTA
	initScrollFeatures();
});
