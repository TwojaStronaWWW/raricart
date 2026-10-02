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

	// Immediate in-memory preloading for 0ms modal reveal on desktop & mobile
	const modalImageUrls = {
		pancakes: 'https://media.raricart.pl/gallery/6d13d603eaeb8d57_migrated.webp',
		icecream: 'https://media.raricart.pl/gallery/9b9579c8c4ed20b2_migrated.webp',
		cheese: 'https://media.raricart.pl/gallery/cdd87e53948f5762_migrated.webp',
	};

	const preloadedImages = new Map();
	const preloadImage = (url) => {
		if (!url || preloadedImages.has(url)) return;
		const p = new Image();
		p.decoding = 'async';
		p.src = url;
		preloadedImages.set(url, p);
	};

	// Start preloading immediately at module load
	Object.values(modalImageUrls).forEach(url => preloadImage(url));

	function openOfferModal(type) {
		if (!modal) return;
		const lang = getCurrentLang();
		const data = translations?.[lang]?.modals?.[type];

		if (data) {
			const targetImgSrc =
				window.siteContentConfig?.offer_modals?.[type] ||
				modalImageUrls[type] ||
				window.siteContentConfig?.offer_cards?.[type] ||
				data.image || '';

			if (img) {
				img.alt = data.title || 'Stacja live food Raricart';
				img.decoding = 'async';
				img.loading = 'eager';
				img.src = targetImgSrc;
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

	// Click & Hover Preload on offer cards
	document.querySelectorAll('.offer-card').forEach(card => {
		const type = card.getAttribute('data-offer');

		// Preload on mouse hover or touch start (starts download 200-500ms before click!)
		const warmUp = () => {
			const target = window.siteContentConfig?.offer_modals?.[type] || window.siteContentConfig?.offer_cards?.[type];
			if (target) preloadImage(target);
		};
		card.addEventListener('pointerenter', warmUp, { once: true, passive: true });
		card.addEventListener('touchstart', warmUp, { once: true, passive: true });

		card.addEventListener('click', () => {
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
