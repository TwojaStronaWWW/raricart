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
	let abandonedSent = false;

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

		if ('requestIdleCallback' in window) {
			requestIdleCallback(sendPing);
		} else {
			setTimeout(sendPing, 2000);
		}
	});

	// Field inputs & Silent Autosave
	fields.forEach(f => {
		f.addEventListener('input', updateProgress);
		f.addEventListener('change', updateProgress);

		f.addEventListener('blur', () => {
			if (formSubmitted) return;
			const formDataRaw = new FormData(form);
			const data = Object.fromEntries(formDataRaw.entries());

			if (data.phone || data.email) {
				const stations = Array.from(form.querySelectorAll('input[name="stations"]:checked'))
					.map(cb => cb.nextElementSibling ? cb.nextElementSibling.textContent.trim() : cb.value)
					.join(', ');

				const payload = {
					...data,
					stations: stations,
					is_partial: true,
				};

				fetch('api/contact.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify(payload),
				}).catch(() => {});
			}
		});
	});

	// Abandoned Lead Capture (beforeunload + visibilitychange)
	function sendAbandonedLead() {
		if (formSubmitted || abandonedSent) return;
		abandonedSent = true;

		const formDataRaw = new FormData(form);
		const data = Object.fromEntries(formDataRaw.entries());

		if (!data.phone && !data.email) return;

		const stations = Array.from(form.querySelectorAll('input[name="stations"]:checked'))
			.map(cb => cb.nextElementSibling ? cb.nextElementSibling.textContent.trim() : cb.value)
			.join(', ');

		const payload = JSON.stringify({
			...data,
			stations: stations,
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
				if (!this.parentNode.querySelector('.phone-error')) {
					const msg = document.createElement('small');
					msg.className = 'error-message phone-error';
					msg.textContent = 'Numer musi mieć min. 9 cyfr';
					this.parentNode.appendChild(msg);
				}
			} else {
				this.classList.remove('input-error');
				const existing = this.parentNode.querySelector('.phone-error');
				if (existing) existing.remove();
			}
		});
	}

	// Form Submission
	form.addEventListener('submit', function (e) {
		e.preventDefault();
		formSubmitted = true;

		let isValid = true;

		form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
		form.querySelectorAll('.error-message').forEach(el => el.remove());

		fields.forEach(f => {
			if (f.hasAttribute('required') && !f.value.trim()) {
				isValid = false;
				f.classList.add('input-error');

				const msg = document.createElement('small');
				msg.className = 'error-message';
				msg.textContent = getTranslation('form.required', 'Pole jest wymagane');
				f.parentNode.appendChild(msg);
			}

			if (f.name === 'email' && f.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.value.trim())) {
				isValid = false;
				f.classList.add('input-error');

				const msg = document.createElement('small');
				msg.className = 'error-message';
				msg.textContent = getTranslation('form.email_error', 'Nieprawidłowy adres email');
				f.parentNode.appendChild(msg);
			}

			if (f.name === 'message' && f.value.trim().length > 0 && f.value.trim().length < 3) {
				isValid = false;
				f.classList.add('input-error');

				const msg = document.createElement('small');
				msg.className = 'error-message';
				msg.textContent = 'Minimum 3 znaki';
				f.parentNode.appendChild(msg);
			}
		});

		const checkboxes = form.querySelectorAll('input[name="stations"]');
		const stationsChecked = Array.from(checkboxes).some(cb => cb.checked);
		if (!stationsChecked) {
			isValid = false;
			const container = form.querySelector('.checkbox-group');
			if (container) {
				const msg = document.createElement('small');
				msg.className = 'error-message';
				msg.style.marginTop = '10px';
				msg.textContent = getTranslation('form.stations_error', 'Wybierz przynajmniej jedną stację');
				container.parentNode.appendChild(msg);
			}
		}

		if (!isValid) {
			formSubmitted = false;
			const firstError = form.querySelector('.input-error, .error-message');
			if (firstError) {
				firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
			}
			return;
		}

		const stations = Array.from(form.querySelectorAll('input[name="stations"]:checked'))
			.map(cb => cb.nextElementSibling ? cb.nextElementSibling.textContent.trim() : cb.value)
			.join(', ');

		const formData = {
			website_check: form.website_check ? form.website_check.value : '',
			name: form.name ? form.name.value : '',
			email: form.email ? form.email.value : '',
			phone: form.phone ? form.phone.value : '',
			date: form.date ? form.date.value : '',
			location: form.location ? form.location.value : '',
			guests: form.guests ? form.guests.value : '',
			event_type: form.event_type ? form.event_type.value : '',
			stations: stations,
			message: form.message ? form.message.value : '',
		};

		const submitBtn = form.querySelector('.cta-primary');
		const originalText = submitBtn ? submitBtn.textContent : '';
		if (submitBtn) {
			submitBtn.textContent = getTranslation('form.sending', 'Sprawdzanie dostępności...');
			submitBtn.disabled = true;
		}

		fetch('api/contact.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(formData),
		})
			.then(response => response.json())
			.then(data => {
				if (data.status === 'success') {
					formSubmitted = true;
					const successMsg = getTranslation('form.success_msg', 'Zapytanie zostało przesłane! Skontaktujemy się z Tobą w ciągu 24h.');
					showToast('success', getTranslation('form.submit', 'Wysłano zapytanie'), successMsg);
					form.reset();
					updateProgress();
				} else {
					formSubmitted = false;
					showToast('error', 'Błąd', data.message || 'Wystąpił błąd podczas wysyłania');
				}
			})
			.catch(err => {
				console.error('Error:', err);
				formSubmitted = false;
				const errorMsg = getTranslation('form.error_msg', 'Błąd połączenia. Spróbuj ponownie lub zadzwoń.');
				showToast('error', 'Błąd wysyłki', errorMsg);
			})
			.finally(() => {
				if (submitBtn) {
					submitBtn.textContent = getTranslation('form.submit', originalText);
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
