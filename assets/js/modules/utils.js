/**
 * modules/utils.js - Współdzielone narzędzia (shared utilities) dla modułów Raricart (Lejek 2026)
 */

/**
 * Blokuje lub odblokowuje scroll body — używane przez modals.js i gallery.js
 * @param {boolean} lock - true = zablokuj scroll, false = odblokuj
 */
export function toggleBodyScroll(lock) {
	if (lock) {
		document.body.classList.add('lightbox-open');
	} else {
		document.body.style.overflow = 'auto';
		document.body.classList.remove('lightbox-open');
	}
}
