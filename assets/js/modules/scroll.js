/**
 * modules/scroll.js - Moduł płynnego przewijania, deep-linków, scroll-scrubbed motion (Awwwards SOTD V3) i widżetu CTA
 */

export function initScrollFeatures() {
	// 1. Intersection Observer for Section Reveals
	if (typeof IntersectionObserver !== 'undefined') {
		const sectionObserver = new IntersectionObserver(
			entries => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.classList.add('visible');
					}
				});
			},
			{ threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
		);

		document
			.querySelectorAll('.section, .section-premium')
			.forEach(s => sectionObserver.observe(s));
	} else {
		document
			.querySelectorAll('.section, .section-premium')
			.forEach(s => s.classList.add('visible'));
	}

	// 2. Interactive Showcase Deck (Tryptyk Stacji Live Food - Awwwards V3)
	const stationCards = document.querySelectorAll('.station-card');
	if (stationCards.length > 0) {
		stationCards[0].classList.add('is-expanded');
		stationCards.forEach(card => {
			const activateCard = () => {
				stationCards.forEach(c => c.classList.remove('is-expanded'));
				card.classList.add('is-expanded');
			};
			card.addEventListener('mouseenter', activateCard, { passive: true });
			card.addEventListener('focusin', activateCard, { passive: true });
		});
	}

	// 3. Scroll-Scrubbed Motion Engine (CSS Custom Properties in rAF)
	const rootEl = document.documentElement;
	const heroSection = document.querySelector('.hero-split');
	const whySection = document.querySelector('.why-station-section');
	const processSection = document.querySelector('.process-section');
	const processCards = document.querySelectorAll('.process-step-card');
	const aboutSection = document.querySelector('.section-about-story');

	let scrubTicking = false;

	const updateScrollScrub = () => {
		const scrollY = window.scrollY || rootEl.scrollTop || 0;
		const vh = window.innerHeight || 900;
		const docHeight = Math.max(
			(document.body && document.body.scrollHeight) || 0,
			rootEl.scrollHeight || 0,
			vh + 1
		);
		const maxScroll = Math.max(1, docHeight - vh);
		const pageProgress = Math.min(1, Math.max(0, scrollY / maxScroll));

		if (rootEl && rootEl.style && typeof rootEl.style.setProperty === 'function') {
			rootEl.style.setProperty('--page-scroll-progress', `${(pageProgress * 100).toFixed(2)}%`);
		}

		// Hero full-bleed stage scrub
		if (heroSection && heroSection.style && typeof heroSection.getBoundingClientRect === 'function') {
			const rect = heroSection.getBoundingClientRect();
			if (rect.bottom > 0 && rect.top < vh) {
				const progress = Math.min(1, Math.max(0, -rect.top / Math.max(1, rect.height)));
				const scrubY = (progress * 48).toFixed(1);
				const scrubScale = (1.02 + progress * 0.06).toFixed(4);
				heroSection.style.setProperty('--scrub-y', `${scrubY}px`);
				heroSection.style.setProperty('--scrub-scale', scrubScale);
			}
		}

		// Why Station overlapping planes scrub
		if (whySection && whySection.style && typeof whySection.getBoundingClientRect === 'function') {
			const rect = whySection.getBoundingClientRect();
			if (rect.bottom > 0 && rect.top < vh) {
				const progress = (vh - rect.top) / Math.max(1, vh + rect.height);
				const offset = ((progress - 0.5) * 64).toFixed(1);
				whySection.style.setProperty('--scrub-y', `${offset}px`);
			}
		}

		// About Story subtle plane scrub
		if (aboutSection && aboutSection.style && typeof aboutSection.getBoundingClientRect === 'function') {
			const rect = aboutSection.getBoundingClientRect();
			if (rect.bottom > 0 && rect.top < vh) {
				const progress = (vh - rect.top) / Math.max(1, vh + rect.height);
				const offset = ((progress - 0.5) * 44).toFixed(1);
				aboutSection.style.setProperty('--scrub-y', `${offset}px`);
			}
		}

		// Process Vertical Culinary Thread progress
		if (processSection && processSection.style && typeof processSection.getBoundingClientRect === 'function') {
			const rect = processSection.getBoundingClientRect();
			if (rect.bottom > 0 && rect.top < vh) {
				const rawProgress = (vh * 0.78 - rect.top) / Math.max(1, rect.height * 0.82);
				const clamped = Math.min(1, Math.max(0.12, rawProgress));
				const pct = `${(clamped * 100).toFixed(1)}%`;
				processSection.style.setProperty('--process-fill', pct);
				processSection.style.setProperty('--process-progress', pct);

				if (processCards.length > 0) {
					const activeThreshold = Math.ceil(clamped * processCards.length);
					processCards.forEach((card, idx) => {
						if (idx < activeThreshold) {
							card.classList.add('step-active');
						} else {
							card.classList.remove('step-active');
						}
					});
				}
			}
		}

		scrubTicking = false;
	};

	window.addEventListener(
		'scroll',
		() => {
			if (!scrubTicking) {
				scrubTicking = true;
				if (typeof requestAnimationFrame === 'function') {
					requestAnimationFrame(updateScrollScrub);
				} else {
					updateScrollScrub();
				}
			}
		},
		{ passive: true }
	);

	updateScrollScrub();

	// 4. SCROLL TO SECTION (query param ?goto= OR hash #)
	const searchStr = (window.location && window.location.search) || '';
	const gotoParam = new URLSearchParams(searchStr).get('goto');
	const scrollTarget = gotoParam ? '#' + gotoParam : (window.location && window.location.hash) || '';

	if (scrollTarget && scrollTarget !== '#') {
		const NAVBAR_OFFSET = 100;

		// Force reveal sections for immediate visibility
		document.querySelectorAll('.section, .section-premium').forEach(s => {
			s.style.transition = 'none';
			s.classList.add('visible');
		});

		const scrollToTarget = () => {
			try {
				const el = document.querySelector(scrollTarget);
				if (el) {
					const y = el.getBoundingClientRect().top + (window.pageYOffset || 0) - NAVBAR_OFFSET;
					window.scrollTo({ top: Math.max(0, y), behavior: 'instant' });
				}
			} catch (e) {
				// Invalid selector safeguard
			}
		};

		// Scroll immediately
		scrollToTarget();

		// Re-scroll on full window load (after fonts and images settle layout)
		window.addEventListener('load', () => {
			scrollToTarget();
			if (gotoParam && typeof history !== 'undefined' && history.replaceState) {
				const cleanUrl = window.location.pathname + '#' + gotoParam;
				history.replaceState(null, '', cleanUrl);
			}
			if (typeof history !== 'undefined' && 'scrollRestoration' in history) {
				history.scrollRestoration = 'auto';
			}
		});

		// Re-enable CSS transitions
		if (typeof requestAnimationFrame === 'function') {
			requestAnimationFrame(() => {
				requestAnimationFrame(() => {
					document.querySelectorAll('.section, .section-premium').forEach(s => {
						s.style.transition = '';
					});
				});
			});
		}
	}

	// 5. Land-Book Style Horizontal Reviews Carousel (Buttons + Drag + Auto-Glide)
	const reviewsViewport = document.getElementById('reviewsSliderViewport');
	const reviewsPrevBtn = document.getElementById('reviewsPrevBtn');
	const reviewsNextBtn = document.getElementById('reviewsNextBtn');
	if (reviewsViewport && typeof reviewsViewport.addEventListener === 'function') {
		const getStepWidth = () => {
			const firstCard = reviewsViewport.querySelector ? reviewsViewport.querySelector('.review-card') : null;
			return firstCard && firstCard.offsetWidth ? firstCard.offsetWidth + 28 : 400;
		};

		const scrollReviewsBy = dir => {
			if (typeof reviewsViewport.scrollBy !== 'function') return;
			const step = getStepWidth();
			const maxScrollLeft = (reviewsViewport.scrollWidth || 0) - (reviewsViewport.clientWidth || 0);
			if (dir > 0 && (reviewsViewport.scrollLeft || 0) >= maxScrollLeft - 16) {
				reviewsViewport.scrollTo({ left: 0, behavior: 'smooth' });
			} else if (dir < 0 && (reviewsViewport.scrollLeft || 0) <= 16) {
				reviewsViewport.scrollTo({ left: Math.max(0, maxScrollLeft), behavior: 'smooth' });
			} else {
				reviewsViewport.scrollBy({ left: dir * step, behavior: 'smooth' });
			}
		};

		if (reviewsPrevBtn) {
			reviewsPrevBtn.addEventListener('click', () => scrollReviewsBy(-1));
		}
		if (reviewsNextBtn) {
			reviewsNextBtn.addEventListener('click', () => scrollReviewsBy(1));
		}

		// Mouse drag-to-scroll
		let isDown = false;
		let startX = 0;
		let scrollStart = 0;
		reviewsViewport.addEventListener('mousedown', e => {
			isDown = true;
			reviewsViewport.classList.add('is-dragging');
			startX = e.pageX - (reviewsViewport.offsetLeft || 0);
			scrollStart = reviewsViewport.scrollLeft || 0;
		});
		const stopDrag = () => {
			isDown = false;
			reviewsViewport.classList.remove('is-dragging');
		};
		reviewsViewport.addEventListener('mouseleave', stopDrag);
		reviewsViewport.addEventListener('mouseup', stopDrag);
		reviewsViewport.addEventListener('mousemove', e => {
			if (!isDown) return;
			e.preventDefault();
			const x = e.pageX - (reviewsViewport.offsetLeft || 0);
			const walk = (x - startX) * 1.4;
			reviewsViewport.scrollLeft = scrollStart - walk;
		});
	}

	// 6. Floating CTA Button Visibility Handler
	const floatingCta = document.getElementById('floatingCta');
	if (floatingCta) {
		const footer = document.querySelector('footer');
		window.addEventListener(
			'scroll',
			() => {
				const y = window.scrollY || (rootEl && rootEl.scrollTop) || 0;
				if (y > 300) {
					floatingCta.classList.add('visible');
				} else {
					floatingCta.classList.remove('visible');
				}

				if (footer && typeof footer.getBoundingClientRect === 'function') {
					const footerTop = footer.getBoundingClientRect().top;
					if (footerTop < window.innerHeight) {
						floatingCta.classList.remove('visible');
					}
				}
			},
			{ passive: true }
		);
	}
}
