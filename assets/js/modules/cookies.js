/**
 * modules/cookies.js - Moduł zgody na pliki cookies (RODO/GDPR) i ładowania Google Analytics (Lejek 2026)
 */

export function initCookies() {
	const banner = document.getElementById('cookie-banner');
	const acceptBtn = document.getElementById('accept-cookies');
	const rejectBtn = document.getElementById('reject-cookies');

	function loadAnalytics() {
		if (document.getElementById('ga-script')) return;

		const script = document.createElement('script');
		script.id = 'ga-script';
		script.async = true;
		script.src = 'https://www.googletagmanager.com/gtag/js?id=G-K4ZBMDXLW7';
		document.head.appendChild(script);

		window.dataLayer = window.dataLayer || [];
		function gtag() {
			window.dataLayer.push(arguments);
		}
		gtag('js', new Date());
		gtag('config', 'G-K4ZBMDXLW7');
	}

	function closeBanner() {
		if (banner) {
			banner.style.opacity = '0';
			setTimeout(() => {
				banner.style.display = 'none';
			}, 500);
		}
	}

	const consent = localStorage.getItem('raricart_cookies_consent');

	if (consent === 'granted') {
		loadAnalytics();
	} else if (consent === 'denied') {
		// Analytics blocked per user choice
	} else {
		// Migration check for legacy key
		if (localStorage.getItem('raricart_cookies_accepted')) {
			localStorage.setItem('raricart_cookies_consent', 'granted');
			localStorage.removeItem('raricart_cookies_accepted');
			loadAnalytics();
		} else if (banner) {
			banner.style.display = 'block';
			setTimeout(() => {
				banner.style.opacity = '1';
			}, 10);
		}
	}

	if (acceptBtn) {
		acceptBtn.addEventListener('click', () => {
			localStorage.setItem('raricart_cookies_consent', 'granted');
			loadAnalytics();
			closeBanner();
		});
	}

	if (rejectBtn) {
		rejectBtn.addEventListener('click', () => {
			localStorage.setItem('raricart_cookies_consent', 'denied');
			closeBanner();
		});
	}
}
