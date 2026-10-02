/**
 * modules/nav.js - Moduł nawigacji, paska górnego i menu mobilnego (Lejek 2026)
 */

export function initNav() {
	const brand = document.getElementById('brand');
	const brandText1 = document.getElementById('brandText1');
	const brandText2 = document.getElementById('brandText2');
	const nav = document.getElementById('nav');
	const navBg = document.getElementById('navBg');
	const hamburger = document.getElementById('hamburger');
	const bg = document.getElementById('bg');
	const scrollBtn = document.getElementById('scroll');
	const videoBg = document.getElementById('videoBg');
	const mainHeader = document.getElementById('main-header');

	const isHomePage = document.body.classList.contains('is-homepage');

	// Always ensure instant interactive readiness (no frozen legacy intro)
	sessionStorage.setItem('heroIntroPlayed', '1');

	// Subpage vs Homepage setup
	if (!isHomePage) {
		if (brand) brand.classList.add('moving');
		if (nav) nav.classList.add('nav-scrolled', 'visible');
		if (bg) bg.classList.add('shrink');
		if (navBg) navBg.classList.add('visible');
		if (hamburger) hamburger.classList.add('visible');
		if (mainHeader) mainHeader.style.pointerEvents = 'auto';
	} else {
		// Static navbar & moving brand from start
		if (brand) brand.classList.add('moving');
		if (scrollBtn) scrollBtn.classList.add('hidden');
		if (brandText1) {
			brandText1.classList.remove('visible');
			brandText1.classList.add('hidden');
		}
		if (brandText2) {
			brandText2.classList.remove('visible');
			brandText2.classList.add('hidden');
		}
		if (nav) nav.classList.add('visible');
		if (navBg) navBg.classList.add('visible');
		if (hamburger) hamburger.classList.add('visible');
		if (bg) bg.classList.add('shrink');

		// Autoplay hero video background if present
		if (videoBg) {
			videoBg.classList.add('visible');
			const vid = videoBg.querySelector('video');
			if (vid) {
				vid.muted = true;
				const playPromise = vid.play();
				if (playPromise !== undefined) {
					playPromise.catch(() => {});
				}
			}
		}
	}

	// Sticky Navbar Scroll Handler (Passive for performance)
	let ticking = false;
	const onScroll = () => {
		const scrollY = window.pageYOffset || document.documentElement.scrollTop;
		if (scrollY > 30) {
			if (nav) nav.classList.add('nav-scrolled');
			if (navBg) navBg.classList.add('nav-scrolled');
		} else {
			if (nav) nav.classList.remove('nav-scrolled');
			if (navBg) navBg.classList.remove('nav-scrolled');
		}
		ticking = false;
	};

	window.addEventListener(
		'scroll',
		() => {
			if (!ticking) {
				requestAnimationFrame(onScroll);
				ticking = true;
			}
		},
		{ passive: true }
	);

	// Run once immediately
	onScroll();

	// Mobile Menu (Hamburger)
	if (hamburger && nav) {
		const closeMobileMenu = () => {
			hamburger.classList.remove('active');
			nav.classList.remove('mobile-active');
			document.body.classList.remove('nav-open');
			document.body.style.overflow = '';
		};

		hamburger.addEventListener('click', e => {
			e.preventDefault();
			e.stopPropagation();

			const isActive = hamburger.classList.contains('active');
			if (!isActive) {
				hamburger.classList.add('active');
				nav.classList.add('mobile-active');
				document.body.classList.add('nav-open');
				document.body.style.overflow = 'hidden';
			} else {
				closeMobileMenu();
			}
		});

		// Close on navigation link click
		nav.querySelectorAll('a').forEach(link => {
			link.addEventListener('click', () => {
				if (hamburger.classList.contains('active')) {
					closeMobileMenu();
				}
			});
		});

		// Close on clicking outside menu in mobile active state
		document.addEventListener('click', e => {
			if (
				nav.classList.contains('mobile-active') &&
				!nav.contains(e.target) &&
				!hamburger.contains(e.target)
			) {
				closeMobileMenu();
			}
		});
	}

	// Brand Click -> Smooth Scroll to Top
	if (brand) {
		brand.addEventListener('click', e => {
			if (isHomePage) {
				e.preventDefault();
				window.scrollTo({ top: 0, behavior: 'smooth' });
			}
		});
	}

	// Scroll button click -> smooth scroll to #onas
	if (scrollBtn) {
		scrollBtn.addEventListener('click', () => {
			const onas = document.getElementById('onas');
			if (onas) {
				onas.scrollIntoView({ behavior: 'smooth' });
			}
		});
	}
}
