/**
 * modules/gallery.js - Moduł galerii realizacji, lightboxa i efektu parallax (Lejek 2026)
 */

export function initGallery() {
	const galleryModal = document.getElementById('galleryModal');
	const galleryModalImg = document.getElementById('galleryModalImg');
	const gallerySection = document.getElementById('realizacje-parallax');
	const galleryGrid = document.getElementById('dynamicGalleryGrid');
	const openFullBtn = document.getElementById('openFullGalleryBtn') || document.querySelector('.realizations-more-btn');

	const galleryImages = [];
	let currentGalleryIndex = 0;

	function toggleBodyScroll(lock) {
		if (lock) {
			document.body.classList.add('lightbox-open');
		} else {
			document.body.style.overflow = 'auto';
			document.body.classList.remove('lightbox-open');
		}
	}

	function openGalleryModal(index) {
		if (!galleryModal || !galleryModalImg || galleryImages.length === 0) return;
		currentGalleryIndex = index >= 0 && index < galleryImages.length ? index : 0;

		galleryModalImg.src = galleryImages[currentGalleryIndex];
		galleryModalImg.style.opacity = '1';
		galleryModalImg.style.transform = 'scale(1)';

		galleryModal.classList.add('active');
		toggleBodyScroll(true);

		history.pushState({ modal: 'gallery' }, '');
	}

	function closeGalleryModal() {
		if (galleryModal) galleryModal.classList.remove('active');
		toggleBodyScroll(false);
	}

	function changeGalleryImage(direction) {
		if (galleryImages.length === 0) return;
		currentGalleryIndex += direction;
		if (currentGalleryIndex < 0) currentGalleryIndex = galleryImages.length - 1;
		if (currentGalleryIndex >= galleryImages.length) currentGalleryIndex = 0;

		if (galleryModalImg) {
			galleryModalImg.style.opacity = '0';
			galleryModalImg.style.transform = 'scale(0.8)';
			setTimeout(() => {
				galleryModalImg.src = galleryImages[currentGalleryIndex];
				galleryModalImg.style.opacity = '1';
				galleryModalImg.style.transform = 'scale(1)';
			}, 200);
		}
	}

	// Initial Preload from SSR element
	const galleryDataEl = document.getElementById('galleryInitialData');
	if (galleryDataEl) {
		try {
			const preloaded = JSON.parse(galleryDataEl.textContent);
			if (Array.isArray(preloaded) && preloaded.length > 0) {
				preloaded.forEach((src, idx) => {
					galleryImages[idx] = src;
				});
			}
		} catch (e) {
			console.warn('Initial gallery data parse error', e);
		}
	}

	// Attach click handlers to SSR items
	document.querySelectorAll('.gallery-item').forEach(item => {
		const img = item.querySelector('img');
		const index = parseInt(item.getAttribute('data-index'), 10);
		if (img && !isNaN(index) && !galleryImages[index]) {
			galleryImages[index] = img.src;
		}
		item.addEventListener('click', () => openGalleryModal(index));
		item.addEventListener('keydown', e => {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				openGalleryModal(index);
			}
		});
	});

	if (openFullBtn) {
		openFullBtn.addEventListener('click', () => openGalleryModal(0));
	}

	// Lightbox navigation buttons
	const prevBtn = document.querySelector('.gallery-prev');
	const nextBtn = document.querySelector('.gallery-next');
	if (prevBtn) {
		prevBtn.addEventListener('click', e => {
			e.stopPropagation();
			changeGalleryImage(-1);
		});
	}
	if (nextBtn) {
		nextBtn.addEventListener('click', e => {
			e.stopPropagation();
			changeGalleryImage(1);
		});
	}

	// Close on backdrop or close button click
	if (galleryModal) {
		galleryModal.addEventListener('click', e => {
			if (
				e.target === galleryModal ||
				e.target.classList.contains('gallery-modal-close') ||
				e.target.closest('.gallery-modal-close')
			) {
				if (history.state && history.state.modal === 'gallery') {
					history.back();
				} else {
					closeGalleryModal();
				}
			}
		});
	}

	// Keyboard ESC and arrow keys listener
	document.addEventListener('keydown', e => {
		if (galleryModal && galleryModal.classList.contains('active')) {
			if (e.key === 'Escape') {
				if (history.state && history.state.modal === 'gallery') {
					history.back();
				} else {
					closeGalleryModal();
				}
			} else if (e.key === 'ArrowLeft') {
				changeGalleryImage(-1);
			} else if (e.key === 'ArrowRight') {
				changeGalleryImage(1);
			}
		}
	});

	// Popstate handler for modal back button
	window.addEventListener('popstate', e => {
		if (!e.state || e.state.modal !== 'gallery') {
			closeGalleryModal();
		}
	});

	// Parallax scroll calculations
	function updateGalleryParallax() {
		const parallaxColumns = document.querySelectorAll('.gallery-column.parallax');
		if (gallerySection && parallaxColumns.length > 0) {
			const rect = gallerySection.getBoundingClientRect();
			const windowHeight = window.innerHeight;

			if (rect.top < windowHeight && rect.bottom > 0) {
				const sectionCenter = rect.top + rect.height / 2;
				const viewportCenter = windowHeight / 2;
				const dist = sectionCenter - viewportCenter;
				const factor = 0.15;

				const isMobile = window.innerWidth <= 768;
				parallaxColumns.forEach((col, index) => {
					const direction = index % 2 === 0 ? -1 : 1;
					const movement = dist * factor * direction;

					if (isMobile) {
						const items = col.querySelectorAll('.gallery-item');
						items.forEach(item => {
							item.style.transition = 'transform 0.05s linear';
							item.style.transform = `translateY(${movement * 0.4}px)`;
						});
					} else {
						col.style.transform = `translateY(${movement}px)`;
					}
				});
			}
		}
	}

	let galleryTick = false;
	window.addEventListener(
		'scroll',
		() => {
			if (!galleryTick) {
				requestAnimationFrame(() => {
					updateGalleryParallax();
					galleryTick = false;
				});
				galleryTick = true;
			}
		},
		{ passive: true }
	);

	// Dynamic content & gallery API loading (deferred for maximum page speed)
	const loadDynamicContent = () => {
		// 1. Availability Notice
		fetch('api/get_status.php?v=' + Date.now())
			.then(r => r.json())
			.then(data => {
				const notice = document.getElementById('availability-notice');
				if (data && data.enabled && data.text && notice) {
					notice.innerHTML = `
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" class="notice-icon" style="stroke: var(--color-accent); min-width: 20px;">
							<rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="1.5"></rect>
							<line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="1.5"></line>
							<line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="1.5"></line>
							<line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="1.5"></line>
						</svg>
						<span>${data.text}</span>
					`;
					notice.style.display = 'flex';
				}
			})
			.catch(() => {});

		// 2. Fetch Gallery API
		if (galleryGrid) {
			fetch('api/get_gallery.php?v=' + Date.now())
				.then(r => r.json())
				.then(images => {
					if (!Array.isArray(images) || images.length === 0) return;

					const columns = galleryGrid.querySelectorAll('.gallery-column');
					if (columns.length === 0) return;

					columns.forEach(col => (col.innerHTML = ''));

					if (images.length >= 5) {
						columns.forEach((col, cIdx) => {
							if (cIdx % 2 !== 0) col.classList.add('parallax');
							else col.classList.remove('parallax');
						});
					} else {
						columns.forEach(col => col.classList.remove('parallax'));
					}

					galleryImages.length = 0;
					images.forEach((src, idx) => {
						galleryImages[idx] = src;
					});

					const countBadge = document.querySelector('.realizations-count');
					if (countBadge) {
						countBadge.textContent = `(${images.length})`;
					}

					const maxDisplay = 8;
					const displayImages = images.slice(0, maxDisplay);

					displayImages.forEach((src, i) => {
						const colIndex = i % columns.length;
						const col = columns[colIndex];

						const div = document.createElement('div');
						div.className = 'gallery-item in-view';
						div.setAttribute('data-index', i);

						const img = document.createElement('img');
						img.src = src;
						img.loading = 'lazy';
						img.alt = 'Realizacja Raricart ' + (i + 1);
						img.style.width = '100%';
						img.style.display = 'block';

						div.appendChild(img);
						col.appendChild(div);

						div.addEventListener('click', () => openGalleryModal(i));
					});
				})
				.catch(() => {});
		}
	};

	if ('requestIdleCallback' in window) {
		requestIdleCallback(loadDynamicContent, { timeout: 2000 });
	} else {
		setTimeout(loadDynamicContent, 100);
	}
}
