/**
 * modules/scroll.js - Moduł płynnego przewijania, deep-linków, osi rytuału kulinarnego i paralaksy (V7 Noma Monograph)
 */

export function initScrollFeatures() {
	// 1. Intersection Observer for Section & Monograph Act Reveals
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
		.querySelectorAll('.section, .section-premium, .station-card, .process-step-card, .why-station-pillar, .review-card')
		.forEach(s => sectionObserver.observe(s));

	// 2. SCROLL TO SECTION (query param ?goto= OR hash #)
	const gotoParam = new URLSearchParams(window.location.search).get('goto');
	const scrollTarget = gotoParam ? '#' + gotoParam : window.location.hash;

	if (scrollTarget && scrollTarget !== '#') {
		const NAVBAR_OFFSET = 100;

		document.querySelectorAll('.section, .section-premium').forEach(s => {
			s.style.transition = 'none';
			s.classList.add('visible');
		});

		const scrollToTarget = () => {
			try {
				const el = document.querySelector(scrollTarget);
				if (el) {
					const y = el.getBoundingClientRect().top + window.pageYOffset - NAVBAR_OFFSET;
					window.scrollTo({ top: Math.max(0, y), behavior: 'instant' });
				}
			} catch (e) {
				// Invalid selector safeguard
			}
		};

		scrollToTarget();

		window.addEventListener('load', () => {
			scrollToTarget();
			if (gotoParam) {
				const cleanUrl = window.location.pathname + '#' + gotoParam;
				history.replaceState(null, '', cleanUrl);
			}
			if ('scrollRestoration' in history) history.scrollRestoration = 'auto';
		});

		requestAnimationFrame(() => {
			requestAnimationFrame(() => {
				document.querySelectorAll('.section, .section-premium').forEach(s => {
					s.style.transition = '';
				});
			});
		});
	}

	// 3. Scroll-Scrubbed Motion: Vertical Culinary Thread (.process-steps) & Fine-Dining Parallax
	const processSteps = document.querySelector('.process-steps');
	const stepCards = processSteps ? Array.from(processSteps.querySelectorAll('.process-step-card')) : [];
	const parallaxMedia = Array.from(
		document.querySelectorAll('.station-image-wrapper .offer-image-img, .why-station-img, .about-story-img')
	);
	const prefersReducedMotion =
		typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	let scrollTicking = false;

	const updateMonographScroll = () => {
		const vh = window.innerHeight || 800;

		// A. Vertical Culinary Thread progress (0% -> 100%)
		if (processSteps) {
			const rect = processSteps.getBoundingClientRect();
			const triggerPoint = vh * 0.62;
			const rawProgress = (triggerPoint - rect.top) / Math.max(1, rect.height);
			const clampedProgress = Math.max(0, Math.min(1, rawProgress));
			processSteps.style.setProperty('--process-progress', `${(clampedProgress * 100).toFixed(2)}%`);

			stepCards.forEach(card => {
				const cardRect = card.getBoundingClientRect();
				const cardTrigger = cardRect.top + cardRect.height * 0.35;
				if (cardTrigger <= triggerPoint) {
					card.classList.add('step-active');
				} else {
					card.classList.remove('step-active');
				}
			});
		}

		// B. Subtle Culinary Monograph Parallax with cubic-bezier(0.16, 1, 0.3, 1)
		if (!prefersReducedMotion && parallaxMedia.length > 0) {
			parallaxMedia.forEach(img => {
				const rect = img.getBoundingClientRect();
				if (rect.top < vh && rect.bottom > 0) {
					const centerOffset = (rect.top + rect.height / 2 - vh / 2) / (vh / 2);
					const shiftY = Math.max(-22, Math.min(22, centerOffset * -18));
					img.style.setProperty('--monograph-parallax-y', `${shiftY.toFixed(2)}px`);
				}
			});
		}

		scrollTicking = false;
	};

	window.addEventListener(
		'scroll',
		() => {
			if (!scrollTicking) {
				requestAnimationFrame(updateMonographScroll);
				scrollTicking = true;
			}
		},
		{ passive: true }
	);

	updateMonographScroll();

	// 4. Floating CTA Button Visibility Handler
	const floatingCta = document.getElementById('floatingCta');
	if (floatingCta) {
		const footer = document.querySelector('footer');
		window.addEventListener(
			'scroll',
			() => {
				const y = window.scrollY || document.documentElement.scrollTop;
				if (y > 300) {
					floatingCta.classList.add('visible');
				} else {
					floatingCta.classList.remove('visible');
				}

				if (footer) {
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
