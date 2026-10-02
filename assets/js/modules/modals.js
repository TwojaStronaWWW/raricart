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

	// Preload cache for all station modal images (0ms display latency)
	const preloadedImages = new Map();
	const preloadImage = (url) => {
		if (!url || preloadedImages.has(url)) return;
		const p = new Image();
		p.src = url;
		preloadedImages.set(url, p);
	};

	const preloadAllModalImages = () => {
		const modals = window.siteContentConfig?.offer_modals || {};
		const cards = window.siteContentConfig?.offer_cards || {};
		['pancakes', 'icecream', 'cheese'].forEach(type => {
			if (modals[type]) preloadImage(modals[type]);
			if (cards[type]) preloadImage(cards[type]);
		});
	};

	if ('requestIdleCallback' in window) {
		requestIdleCallback(preloadAllModalImages, { timeout: 1500 });
	} else {
		setTimeout(preloadAllModalImages, 150);
	}

	function openOfferModal(type) {
		if (!modal) return;
		const lang = getCurrentLang();
		const data = translations?.[lang]?.modals?.[type];

		if (data) {
			let targetImgSrc = '';

			// 1. Try Specific Modal Image from Config (No cache-busting timestamp!)
			if (
				window.siteContentConfig &&
				window.siteContentConfig.offer_modals &&
				window.siteContentConfig.offer_modals[type]
			) {
				targetImgSrc = window.siteContentConfig.offer_modals[type];
			}
			// 2. Fallback to Card Image from Config
			else if (
				window.siteContentConfig &&
				window.siteContentConfig.offer_cards &&
				window.siteContentConfig.offer_cards[type]
			) {
				targetImgSrc = window.siteContentConfig.offer_cards[type];
			}

			// 3. Fallback: Current card image already rendered in DOM (zero network delay)
			const card = document.querySelector(`.offer-card[data-offer="${type}"]`);
			const cardImg = card ? card.querySelector('.offer-image-img') : null;
			const cardImgSrc = cardImg ? cardImg.src : '';

			const finalSrc = targetImgSrc || cardImgSrc || data.image || '';

			if (img) {
				img.alt = data.title || 'Stacja live food Raricart';

				// If already in browser cache or same src, set directly
				if (cardImgSrc && targetImgSrc && targetImgSrc !== cardImgSrc) {
					// Show the card image instantly while checking if target image is decoded
					const cached = preloadedImages.get(targetImgSrc);
					if (cached && cached.complete && cached.naturalWidth > 0) {
						img.src = targetImgSrc;
					} else {
						img.src = cardImgSrc; // Instant 0ms preview from card!
						const hiRes = new Image();
						hiRes.onload = () => {
							if (modal.classList.contains('active')) {
								img.src = targetImgSrc;
							}
						};
						hiRes.src = targetImgSrc;
					}
				} else {
					img.src = finalSrc;
				}
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
