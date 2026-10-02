/**
 * modules/modals.js - Moduł okien modalnych stacji kulinarnych (Lejek 2026)
 */

import { getCurrentLang } from './i18n.js';

export function initStationModals(translations) {
	const modal = document.getElementById('modal');
	const img = document.getElementById('modalImg');
	const title = document.getElementById('modalTitle');
	const text = document.getElementById('modalText');

	function toggleBodyScroll(lock) {
		if (lock) {
			document.body.classList.add('lightbox-open');
		} else {
			document.body.style.overflow = 'auto';
			document.body.classList.remove('lightbox-open');
		}
	}

	function openOfferModal(type) {
		if (!modal) return;
		const lang = getCurrentLang();
		const data = translations?.[lang]?.modals?.[type];

		if (data) {
			let imgSrc = data.image;

			// 1. Try Specific Modal Image from Config
			if (
				window.siteContentConfig &&
				window.siteContentConfig.offer_modals &&
				window.siteContentConfig.offer_modals[type]
			) {
				imgSrc = window.siteContentConfig.offer_modals[type] + '?v=' + Date.now();
			}
			// 2. Fallback to Card Image from Config
			else if (
				window.siteContentConfig &&
				window.siteContentConfig.offer_cards &&
				window.siteContentConfig.offer_cards[type]
			) {
				imgSrc = window.siteContentConfig.offer_cards[type] + '?v=' + Date.now();
			}

			if (img) {
				img.src = imgSrc || '';
				img.alt = data.title || 'Stacja live food Raricart';
			}
			if (title) {
				title.textContent = data.title || '';
			}
			if (text) {
				text.innerHTML = data.content || '';
			}

			modal.classList.add('active');
			toggleBodyScroll(true);

			// History API: Add state
			history.pushState({ modal: 'offer' }, '', '#offer-' + type);
		}
	}

	function closeModal() {
		if (modal) modal.classList.remove('active');
		toggleBodyScroll(false);

		// Clean URL hash if present
		if (window.location.hash.startsWith('#offer-')) {
			history.replaceState(null, '', window.location.pathname + window.location.search);
		}
	}

	// Click on offer cards
	document.querySelectorAll('.offer-card').forEach(card => {
		card.addEventListener('click', () => {
			const type = card.getAttribute('data-offer');
			if (type) openOfferModal(type);
		});
	});

	// Close on close button or click on backdrop
	if (modal) {
		modal.addEventListener('click', e => {
			if (
				e.target === modal ||
				e.target.classList.contains('modal-close') ||
				e.target.closest('.modal-close')
			) {
				if (history.state && history.state.modal === 'offer') {
					history.back();
				} else {
					closeModal();
				}
			}
		});
	}

	// Keyboard ESC listener
	document.addEventListener('keydown', e => {
		if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
			if (history.state && history.state.modal === 'offer') {
				history.back();
			} else {
				closeModal();
			}
		}
	});

	// Popstate handler
	window.addEventListener('popstate', e => {
		if (!e.state || e.state.modal !== 'offer') {
			closeModal();
		}
	});

	// Check if URL loaded with hash #offer-*
	const hash = window.location.hash;
	if (hash.startsWith('#offer-')) {
		const type = hash.replace('#offer-', '');
		setTimeout(() => openOfferModal(type), 200);
	}
}
