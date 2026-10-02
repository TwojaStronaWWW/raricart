/**
 * modules/i18n.js - Moduł wielojęzyczności i podmiany treści w locie (Lejek 2026)
 */

let currentLang = 'pl';
let translationsData = {};
const langChangeCallbacks = [];

export function getCurrentLang() {
	return currentLang;
}

export function getTranslation(path, fallback = '') {
	if (!path) return fallback;
	const keys = path.split('.');
	let text = translationsData[currentLang];
	if (text) {
		for (const k of keys) {
			if (text && typeof text === 'object') {
				text = text[k];
			} else {
				return fallback;
			}
		}
	}
	return typeof text === 'string' ? text : fallback;
}

export function onLanguageChange(callback) {
	if (typeof callback === 'function') {
		langChangeCallbacks.push(callback);
	}
}

export function updateLanguage(lang) {
	if (!translationsData[lang]) return;
	currentLang = lang;

	const langBtns = document.querySelectorAll('.lang-btn');
	langBtns.forEach(btn => btn.classList.toggle('active', btn.dataset.lang === lang));

	document.querySelectorAll('[data-i18n]').forEach(el => {
		const key = el.getAttribute('data-i18n');
		const text = getTranslation(key);
		if (text) {
			el.innerHTML = text;
		}
	});

	document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
		const key = el.getAttribute('data-i18n-placeholder');
		const text = getTranslation(key);
		if (text) {
			el.placeholder = text;
		}
	});

	langChangeCallbacks.forEach(cb => {
		try {
			cb(lang);
		} catch (e) {
			console.error('Error in langChangeCallback', e);
		}
	});
}

export function initI18n(translations) {
	translationsData = translations || {};
	const langBtns = document.querySelectorAll('.lang-btn');

	langBtns.forEach(btn => {
		btn.addEventListener('click', () => {
			if (btn.dataset.lang) {
				updateLanguage(btn.dataset.lang);
			}
		});
	});
}
