/**
 * modules/scroll.js - Moduł płynnego przewijania, deep-linków i widżetu pływającego CTA (Lejek 2026)
 */

export function initScrollFeatures() {
	// 1. Intersection Observer for Section Reveals
	const sectionObserver = new IntersectionObserver(
		entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					entry.target.classList.add('visible');
				}
			});
		},
		{ threshold: 0.1, rootMargin: '0px 0px -50px 0px' }
	);

	document.querySelectorAll('.section, .section-premium').forEach(s => sectionObserver.observe(s));

	// 2. SCROLL TO SECTION (query param ?goto= OR hash #)
	const gotoParam = new URLSearchParams(window.location.search).get('goto');
	const scrollTarget = gotoParam ? '#' + gotoParam : window.location.hash;

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
					const y = el.getBoundingClientRect().top + window.pageYOffset - NAVBAR_OFFSET;
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
			if (gotoParam) {
				const cleanUrl = window.location.pathname + '#' + gotoParam;
				history.replaceState(null, '', cleanUrl);
			}
			if ('scrollRestoration' in history) history.scrollRestoration = 'auto';
		});

		// Re-enable CSS transitions
		requestAnimationFrame(() => {
			requestAnimationFrame(() => {
				document.querySelectorAll('.section, .section-premium').forEach(s => {
					s.style.transition = '';
				});
			});
		});
	}

	// 3. Floating CTA Button Visibility Handler
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
