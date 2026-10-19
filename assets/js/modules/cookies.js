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

	// Bezciasteczkowa Analityka Ruchu WWW & Konwersji (100% Cookieless RODO / Zero-PII)
	const win = typeof window !== 'undefined' ? window : null;
	if (win && !win.__TSW_ANALYTICS_ACTIVE__ && typeof win.navigator?.sendBeacon === 'function') {
		win.__TSW_ANALYTICS_ACTIVE__ = true;
		const analyticsEndpoint = 'https://ai.twojastronawww.pl/analytics';
		const sendBeacon = (extra = {}) => {
			try {
				const loc = win.location || { pathname: '/', hash: '', search: '' };
				const params = typeof URLSearchParams !== 'undefined' ? new URLSearchParams(loc.search || '') : null;
				const utmTerm = params ? (params.get('q') || params.get('utm_term') || params.get('utm_campaign') || '') : '';
				const payload = JSON.stringify({
					site_id: 'raricart',
					type: extra.type || 'pageview',
					path: (loc.pathname || '/') + (loc.hash || ''),
					referrer: document.referrer || '',
					screen_w: win.innerWidth || 1280,
					search_term: extra.search_term || utmTerm || undefined,
					event_name: extra.event_name || undefined,
					lcp_ms: extra.lcp_ms || undefined
				});
				win.navigator.sendBeacon(analyticsEndpoint, payload);
			} catch (_) { /* ciche ignorowanie */ }
		};
		let lcpVal = 0;
		if (typeof PerformanceObserver === 'function') {
			try {
				const po = new PerformanceObserver((list) => {
					const entries = list.getEntries();
					if (entries.length > 0) lcpVal = Math.round(entries[entries.length - 1].startTime);
				});
				po.observe({ type: 'largest-contentful-paint', buffered: true });
			} catch (_) { /* brak wsparcia LCP */ }
		}
		setTimeout(() => sendBeacon(lcpVal > 0 ? { lcp_ms: lcpVal } : {}), 900);
		setTimeout(() => {
			if (document.visibilityState !== 'hidden') sendBeacon({ type: 'engaged' });
		}, 15000);
		if (typeof win.addEventListener === 'function') {
			win.addEventListener('hashchange', () => sendBeacon({ type: 'pageview' }));
		}
		if (typeof document.addEventListener === 'function') {
			document.addEventListener('click', (e) => {
				const link = e.target && typeof e.target.closest === 'function' ? e.target.closest('a[href]') : null;
				if (!link) return;
				const href = (link.getAttribute('href') || '').trim();
				if (href.startsWith('tel:')) sendBeacon({ type: 'event', event_name: 'cta_phone_click' });
				else if (href.startsWith('mailto:')) sendBeacon({ type: 'event', event_name: 'cta_email_click' });
			}, { passive: true });
			document.addEventListener('submit', () => {
				sendBeacon({ type: 'event', event_name: 'contact_form_submit' });
			}, { passive: true });
		}
	}
}

