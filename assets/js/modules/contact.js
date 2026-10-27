/**
 * modules/contact.js - Moduł formularza kontaktowego, walidacji, lead capture i toastów (Lejek 2026)
 */

import { getTranslation } from './i18n.js';

export function initContactForm() {
	const form = document.getElementById('form');
	if (!form) return;

	const progressBar = document.getElementById('form-progress');
	const progressText = document.getElementById('progress-text');
	const fields = form.querySelectorAll('input, select, textarea');
	const preCtaBtn = document.getElementById('preCtaCheckBtn');

	let formSubmitted = false;
	let isSubmitting = false;
	let abandonedSent = false;
	let draftTimer = null;
	let lastDraftSignature = '';

	function cancelPendingDraft() {
		if (draftTimer !== null) {
			clearTimeout(draftTimer);
			draftTimer = null;
		}
	}

	function collectFormData() {
		const data = {
			website_check: '',
			name: '',
			email: '',
			phone: '',
			date: '',
			location: '',
			guests: '',
			event_type: '',
			stations: '',
			message: '',
		};

		fields.forEach(f => {
			if (!f.name || f.type === 'checkbox' || f.type === 'submit') return;
			data[f.name] = typeof f.value === 'string' ? f.value.trim() : '';
		});

		const stations = Array.from(form.querySelectorAll('input[name="stations"]:checked'))
			.map(cb => (cb.nextElementSibling && cb.nextElementSibling.textContent ? cb.nextElementSibling.textContent.trim() : cb.value))
			.filter(Boolean)
			.join(', ');

		data.stations = stations;
		return data;
	}

	function sendDraftNow() {
		draftTimer = null;
		if (formSubmitted || isSubmitting) return;

		const data = collectFormData();
		if (!data.phone && !data.email) return;

		const sig = JSON.stringify(data);
		if (sig === lastDraftSignature) return;
		lastDraftSignature = sig;

		const payload = {
			...data,
			is_partial: true,
		};

		fetch('api/contact.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(payload),
		}).catch(() => {});
	}

	function scheduleDraftSave() {
		if (formSubmitted || isSubmitting) return;
		cancelPendingDraft();
		draftTimer = setTimeout(sendDraftNow, 300);
	}

	// Toast Notification Utility
	function showToast(type, title, message) {
		const existing = document.querySelector('.toast-notification');
		if (existing) existing.remove();

		const toast = document.createElement('div');
		toast.className = `toast-notification toast-${type}`;
		toast.innerHTML = `
			<span class="toast-icon">${type === 'success' ? '✅' : '❌'}</span>
			<div class="toast-body">
				<div class="toast-title">${title}</div>
				<div class="toast-message">${message}</div>
			</div>
			<button class="toast-close" aria-label="Zamknij">&times;</button>
		`;

		document.body.appendChild(toast);

		const dismissToast = (t) => {
			if (!t || !t.parentNode) return;
			t.classList.add('toast-out');
			setTimeout(() => t.remove(), 400);
		};

		toast.querySelector('.toast-close').addEventListener('click', () => dismissToast(toast));
		setTimeout(() => dismissToast(toast), 6000);
	}

	// Progress Bar Calculation
	function updateProgress() {
		if (!progressBar || !progressText) return;

		let filled = 0;
		let total = 0;

		fields.forEach(f => {
			if (f.type !== 'hidden' && f.type !== 'submit' && f.type !== 'checkbox') {
				if (f.hasAttribute('required')) {
					total++;
					if (f.value.trim() !== '') filled++;
				}
			}
		});

		const checkboxes = form.querySelectorAll('input[name="stations"]');
		if (checkboxes.length > 0) {
			total++;
			if (Array.from(checkboxes).some(cb => cb.checked)) filled++;
		}

		const percent = total > 0 ? Math.round((filled / total) * 100) : 0;
		progressBar.style.width = percent + '%';
		const progressTemplate = getTranslation('form.progress_text', 'Uzupełnij dane (0%)');
		progressText.textContent = progressTemplate.replace('0%', `${percent}%`);
	}

	// Trigger "Poor Man's Cron" cleanly after page load
	window.addEventListener('load', () => {
		const sendPing = () => {
			fetch('api/contact.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ action: 'ping' }),
			}).catch(() => {});
		};

		if ('requestIdleCallback' in window && typeof window.requestIdleCallback === 'function') {
			window.requestIdleCallback(sendPing);
		} else {
			setTimeout(sendPing, 2000);
		}
	});

	// Prevent blur-triggered draft race when clicking Submit button
	const submitBtnEl = form.querySelector('.cta-primary');
	if (submitBtnEl) {
		['pointerdown', 'mousedown', 'touchstart'].forEach(evtName => {
			submitBtnEl.addEventListener(evtName, cancelPendingDraft);
		});
	}

	// Field inputs & Silent Autosave
	fields.forEach(f => {
		const clearFieldError = () => {
			f.classList.remove('input-error');
			if (f.parentNode) {
				const err = f.parentNode.querySelector('.error-message');
				if (err) err.remove();
			}
			if (f.name === 'stations') {
				const group = form.querySelector('.checkbox-group');
				if (group) {
					group.classList.remove('input-error');
					if (group.parentNode) {
						const groupErr = group.parentNode.querySelector('.error-message');
						if (groupErr) groupErr.remove();
					}
				}
			}
		};

		f.addEventListener('input', () => {
			clearFieldError();
			updateProgress();
		});

		f.addEventListener('change', () => {
			clearFieldError();
			updateProgress();
			if (f.tagName === 'SELECT' || f.type === 'checkbox' || f.type === 'date') {
				scheduleDraftSave();
			}
		});

		f.addEventListener('blur', () => {
			if (formSubmitted || isSubmitting) return;
			scheduleDraftSave();
		});
	});

	// Abandoned Lead Capture (beforeunload + visibilitychange)
	function sendAbandonedLead() {
		if (formSubmitted || isSubmitting) return;
		cancelPendingDraft();

		const data = collectFormData();
		if (!data.phone && !data.email) return;

		const sig = JSON.stringify(data);
		if (abandonedSent && sig === lastDraftSignature) return;
		abandonedSent = true;
		lastDraftSignature = sig;

		const payload = JSON.stringify({
			...data,
			is_partial: true,
			is_abandoned: true,
		});

		fetch('api/contact.php', {
			method: 'POST',
			keepalive: true,
			headers: { 'Content-Type': 'application/json' },
			body: payload,
		}).catch(() => {});
	}

	window.addEventListener('beforeunload', sendAbandonedLead);
	document.addEventListener('visibilitychange', () => {
		if (document.visibilityState === 'hidden') {
			sendAbandonedLead();
		}
	});

	// Phone Input Formatting & Validation
	const phoneInput = form.querySelector('input[name="phone"]');
	if (phoneInput) {
		phoneInput.addEventListener('input', function (e) {
			let val = e.target.value.replace(/[^\d\s+]/g, '');
			if (val.length > 20) val = val.substring(0, 20);
			e.target.value = val;
		});

		phoneInput.addEventListener('blur', function () {
			const digits = this.value.replace(/\D/g, '');
			if (digits.length > 0 && digits.length < 9) {
				this.classList.add('input-error');
				if (this.parentNode && !this.parentNode.querySelector('.phone-error')) {
					const msg = document.createElement('small');
					msg.className = 'error-message phone-error';
					msg.textContent = 'Numer musi mieć min. 9 cyfr';
					this.parentNode.appendChild(msg);
				}
			} else {
				this.classList.remove('input-error');
				if (this.parentNode) {
					const existing = this.parentNode.querySelector('.phone-error');
					if (existing) existing.remove();
				}
			}
		});
	}

	// Form Submission
	form.addEventListener('submit', function (e) {
		e.preventDefault();
		cancelPendingDraft();
		if (isSubmitting) return;

		let isValid = true;

		form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
		form.querySelectorAll('.error-message').forEach(el => el.remove());

		fields.forEach(f => {
			const val = typeof f.value === 'string' ? f.value.trim() : '';

			if (f.hasAttribute('required') && !val) {
				isValid = false;
				f.classList.add('input-error');

				if (f.parentNode) {
					const msg = document.createElement('small');
					msg.className = 'error-message';
					msg.textContent = getTranslation('form.required', 'Pole jest wymagane');
					f.parentNode.appendChild(msg);
				}
			}

			if (f.name === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
				isValid = false;
				f.classList.add('input-error');

				if (f.parentNode) {
					const msg = document.createElement('small');
					msg.className = 'error-message';
					msg.textContent = getTranslation('form.email_error', 'Nieprawidłowy adres email');
					f.parentNode.appendChild(msg);
				}
			}

			if (f.name === 'phone' && val) {
				const digits = val.replace(/\D/g, '');
				if (digits.length > 0 && digits.length < 9) {
					isValid = false;
					f.classList.add('input-error');

					if (f.parentNode) {
						const msg = document.createElement('small');
						msg.className = 'error-message phone-error';
						msg.textContent = 'Numer musi mieć min. 9 cyfr';
						f.parentNode.appendChild(msg);
					}
				}
			}
		});

		const checkboxes = form.querySelectorAll('input[name="stations"]');
		const stationsChecked = Array.from(checkboxes).some(cb => cb.checked);
		if (!stationsChecked) {
			isValid = false;
			const container = form.querySelector('.checkbox-group');
			if (container) {
				container.classList.add('input-error');
				if (container.parentNode) {
					const msg = document.createElement('small');
					msg.className = 'error-message';
					msg.style.marginTop = '10px';
					msg.textContent = getTranslation('form.stations_error', 'Wybierz przynajmniej jedną stację');
					container.parentNode.appendChild(msg);
				}
			}
		}

		if (!isValid) {
			formSubmitted = false;
			const firstError = form.querySelector('.input-error, .error-message');
			if (firstError && typeof firstError.scrollIntoView === 'function') {
				firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
			}
			return;
		}

		isSubmitting = true;
		formSubmitted = true;

		const formData = collectFormData();

		const submitBtn = form.querySelector('.cta-primary');
		const submitLabel = submitBtn ? (submitBtn.querySelector('span') || submitBtn) : null;
		const originalText = submitLabel ? submitLabel.textContent : '';
		if (submitBtn && submitLabel) {
			submitLabel.textContent = getTranslation('form.sending', 'Sprawdzanie dostępności...');
			submitBtn.disabled = true;
		}

		fetch('api/contact.php', {
			method: 'POST',
			keepalive: true,
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(formData),
		})
			.then(response => response.json())
			.then(data => {
				if (data && data.status === 'success') {
					formSubmitted = true;
					lastDraftSignature = '';
					const successMsg = getTranslation('form.success_msg', 'Zapytanie zostało przesłane! Skontaktujemy się z Tobą w ciągu 24h.');
					showToast('success', getTranslation('form.submit', 'Wysłano zapytanie'), successMsg);
					if (typeof form.reset === 'function') {
						form.reset();
					}
					checkboxes.forEach(cb => {
						cb.checked = false;
					});
					updateProgress();
				} else {
					formSubmitted = false;
					showToast('error', 'Błąd', (data && data.message) || 'Wystąpił błąd podczas wysyłania');
				}
			})
			.catch(err => {
				console.error('Error:', err);
				formSubmitted = false;
				const errorMsg = getTranslation('form.error_msg', 'Błąd połączenia. Spróbuj ponownie lub zadzwoń.');
				showToast('error', 'Błąd wysyłki', errorMsg);
			})
			.finally(() => {
				isSubmitting = false;
				if (submitBtn && submitLabel) {
					submitLabel.textContent = getTranslation('form.submit', originalText);
					submitBtn.disabled = false;
				}
			});
	});

	// Pre-CTA Check Availability Button Click
	if (preCtaBtn) {
		preCtaBtn.addEventListener('click', e => {
			const contactSec = document.getElementById('kontakt');
			if (contactSec) {
				e.preventDefault();
				contactSec.scrollIntoView({ behavior: 'smooth' });
				setTimeout(() => {
					const nameInput = document.getElementById('name');
					if (nameInput) nameInput.focus();
				}, 600);
			}
		});
	}
}
