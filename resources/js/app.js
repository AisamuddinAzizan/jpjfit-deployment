import './bootstrap';

import Alpine from 'alpinejs';
import AOS from 'aos';
import confetti from 'canvas-confetti';
import Chart from 'chart.js/auto';
import { CountUp } from 'countup.js';
import 'particles.js';
import { DataTable } from 'simple-datatables';
import 'simple-datatables/dist/style.css';
import Swiper from 'swiper';
import { Autoplay, EffectFade, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import Typed from 'typed.js';
import 'aos/dist/aos.css';

window.Alpine = Alpine;

Alpine.start();

window.Chart = Chart;

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const debounce = (func, wait = 100) => {
	let timeout;

	return (...args) => {
		window.clearTimeout(timeout);
		timeout = window.setTimeout(() => func(...args), wait);
	};
};

const animateLegacyCounters = () => {
	const counters = document.querySelectorAll('[data-counter-target]');

	counters.forEach((counter) => {
		const target = Number(counter.dataset.counterTarget || 0);
		const duration = 1200;
		const start = performance.now();

		const update = (time) => {
			const progress = Math.min((time - start) / duration, 1);
			const eased = 1 - Math.pow(1 - progress, 3);
			const value = Math.round(target * eased);
			counter.textContent = value.toLocaleString();

			if (progress < 1) {
				requestAnimationFrame(update);
			}
		};

		requestAnimationFrame(update);
	});
};

const renderDashboardCharts = () => {
	const sessionsChartEl = document.getElementById('sessionsChart');
	const classificationChartEl = document.getElementById('classificationChart');

	if (sessionsChartEl) {
		const labels = JSON.parse(sessionsChartEl.dataset.labels || '[]');
		const passValues = JSON.parse(sessionsChartEl.dataset.passValues || '[]');
		const failValues = JSON.parse(sessionsChartEl.dataset.failValues || '[]');
		const passLabel = sessionsChartEl.dataset.passLabel || 'Pass';
		const failLabel = sessionsChartEl.dataset.failLabel || 'Fail';

		new Chart(sessionsChartEl, {
			type: 'bar',
			data: {
				labels,
				datasets: [
					{
						label: passLabel,
						data: passValues,
						backgroundColor: '#059669',
						borderRadius: 8,
					},
					{
						label: failLabel,
						data: failValues,
						backgroundColor: '#e11d48',
						borderRadius: 8,
					},
				],
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: true, position: 'top' },
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							precision: 0,
						},
					},
				},
			},
		});
	}

	if (classificationChartEl) {
		const labels = JSON.parse(classificationChartEl.dataset.labels || '[]');
		const values = JSON.parse(classificationChartEl.dataset.values || '[]');

		new Chart(classificationChartEl, {
			type: 'doughnut',
			data: {
				labels,
				datasets: [
					{
						data: values,
						backgroundColor: ['#b91c1c', '#d97706', '#0369a1', '#166534'],
						borderWidth: 2,
						borderColor: '#ffffff',
					},
				],
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
			},
		});
	}
};

const applyPageTransition = () => {
	document.body.classList.add('page-enter');
	requestAnimationFrame(() => {
		document.body.classList.add('page-enter-active');
	});

	// Remove transition classes so body does not keep a transform context,
	// which can interfere with fixed-position effects in some browsers.
	window.setTimeout(() => {
		document.body.classList.remove('page-enter');
		document.body.classList.remove('page-enter-active');
	}, 380);
};

const removePageLoader = () => {
	const loader = document.getElementById('pageLoader');
	if (!loader) {
		return;
	}

	loader.style.opacity = '0';
	setTimeout(() => loader.remove(), 260);
};

const applyTheme = (theme) => {
	const isDark = theme === 'dark';
	document.body.classList.toggle('theme-dark', isDark);

	const moonIcon = document.getElementById('themeIconMoon');
	const sunIcon = document.getElementById('themeIconSun');

	if (moonIcon && sunIcon) {
		moonIcon.classList.toggle('hidden', isDark);
		sunIcon.classList.toggle('hidden', !isDark);
	}
};

const initThemeToggle = () => {
	const storageKey = 'jpjfit-theme';
	const toggleButton = document.getElementById('themeToggle');
	if (!toggleButton) {
		return;
	}

	const preferredTheme = localStorage.getItem(storageKey)
		|| (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
	applyTheme(preferredTheme);

	toggleButton.addEventListener('click', () => {
		const nextTheme = document.body.classList.contains('theme-dark') ? 'light' : 'dark';
		localStorage.setItem(storageKey, nextTheme);
		applyTheme(nextTheme);
	});
};

const initDataTables = () => {
	const tables = document.querySelectorAll('table.data-table');
	const isMalay = document.documentElement.lang?.toLowerCase().startsWith('ms');
	const labels = isMalay
		? {
				placeholder: 'Cari...',
				perPage: 'Baris per halaman',
				noRows: 'Tiada rekod padanan ditemui',
				info: 'Memaparkan {start} hingga {end} daripada {rows} rekod',
			}
		: {
				placeholder: 'Search...',
				perPage: 'Rows per page',
				noRows: 'No matching records found',
				info: 'Showing {start} to {end} of {rows} entries',
			};

	tables.forEach((table) => {
		if (table.dataset.datatableInitialized === '1') {
			return;
		}

		table.dataset.datatableInitialized = '1';

		new DataTable(table, {
			searchable: true,
			fixedHeight: false,
			perPage: 10,
			perPageSelect: [10, 25, 50, 100],
			labels,
		});

		// Defensive cleanup for any literal token text from plugin rendering.
		const wrapper = table.closest('.datatable-wrapper');
		const perPageLabel = wrapper?.querySelector('.datatable-dropdown label');
		if (perPageLabel) {
			perPageLabel.innerHTML = perPageLabel.innerHTML.replaceAll('{select}', '').replace(/\s{2,}/g, ' ').trim();
		}
	});
};

const initLandingTheme = () => {
	const body = document.body;
	if (body.dataset.page !== 'landing') {
		return;
	}

	const key = 'jpjfit-landing-theme';
	const toggle = document.getElementById('themeToggleLanding');
	const label = toggle?.querySelector('[data-theme-label]');

	const apply = (theme) => {
		body.classList.toggle('theme-light', theme === 'light');
		if (label) {
			label.textContent = theme === 'light' ? 'Light' : 'Dark';
		}
	};

	const stored = localStorage.getItem(key) || 'dark';
	apply(stored);

	toggle?.addEventListener('click', () => {
		const next = body.classList.contains('theme-light') ? 'dark' : 'light';
		localStorage.setItem(key, next);
		apply(next);
	});
};

const initLandingHeader = () => {
	const body = document.body;
	if (body.dataset.page !== 'landing') {
		return;
	}

	const nav = document.getElementById('landingNav');
	const progressBar = document.getElementById('scrollProgress');
	const goTopButton = document.getElementById('goTopBtn');

	const updateNav = () => {
		const scrollY = window.scrollY;
		nav?.classList.toggle('is-solid', scrollY > 150);
		goTopButton?.classList.toggle('go-top-visible', scrollY > 300);

		if (progressBar) {
			const total = document.documentElement.scrollHeight - window.innerHeight;
			const percent = total > 0 ? (scrollY / total) * 100 : 0;
			progressBar.style.width = `${Math.min(percent, 100)}%`;
		}
	};

	updateNav();
	window.addEventListener('scroll', debounce(updateNav, 10), { passive: true });

	goTopButton?.addEventListener('click', () => {
		window.scrollTo({ top: 0, behavior: 'smooth' });
	});

	const menuToggle = document.getElementById('mobileMenuToggle');
	const mobileMenu = document.getElementById('mobileMenu');

	menuToggle?.addEventListener('click', () => {
		const isOpen = mobileMenu?.classList.toggle('hidden') === false;
		menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
	});

	document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
		anchor.addEventListener('click', (event) => {
			const href = anchor.getAttribute('href');
			if (!href || href === '#') {
				return;
			}

			const target = document.querySelector(href);
			if (!target) {
				return;
			}

			event.preventDefault();
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
			mobileMenu?.classList.add('hidden');
		});
	});
};

const initHeroSlider = () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	document.querySelectorAll('.hero-slide').forEach((slide) => {
		if (slide instanceof HTMLElement && slide.dataset.bg) {
			slide.style.backgroundImage = `url(${slide.dataset.bg})`;
		}
	});

	const container = document.querySelector('.hero-swiper');
	if (!container) {
		return;
	}

	new Swiper(container, {
		modules: [Autoplay, EffectFade, Navigation, Pagination],
		effect: 'fade',
		fadeEffect: { crossFade: true },
		speed: prefersReducedMotion ? 0 : 900,
		loop: true,
		autoplay: prefersReducedMotion
			? false
			: {
				delay: 4200,
				disableOnInteraction: false,
			},
		navigation: {
			nextEl: '.hero-next',
			prevEl: '.hero-prev',
		},
		pagination: {
			el: '.hero-pagination',
			clickable: true,
		},
	});

	const typedTarget = document.getElementById('heroTypedText');
	if (typedTarget && !prefersReducedMotion) {
		new Typed(typedTarget, {
			strings: [
				'Fit Teams. Faster Decisions.',
				'Measure Progress. Drive Readiness.',
				'Operational Fitness, Digitally Managed.',
			],
			typeSpeed: 48,
			backSpeed: 24,
			backDelay: 1400,
			loop: true,
			showCursor: false,
		});
	}
};

const initAOS = () => {
	AOS.init({
		duration: prefersReducedMotion ? 0 : 700,
		easing: 'ease-out-cubic',
		once: true,
		offset: 70,
	});
};

const initCountUpCounters = () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	const counters = document.querySelectorAll('[data-counter], [data-live-workout-counter]');
	if (!counters.length) {
		return;
	}

	const runCounter = (element, target) => {
		const countUp = new CountUp(element, Number(target || 0), {
			duration: prefersReducedMotion ? 0 : 1.4,
			separator: ',',
		});

		if (!countUp.error) {
			countUp.start();
		}
	};

	const observer = new IntersectionObserver((entries, obs) => {
		entries.forEach((entry) => {
			if (!entry.isIntersecting) {
				return;
			}

			const el = entry.target;
			const target = el.getAttribute('data-counter') || el.getAttribute('data-live-workout-counter') || '0';
			runCounter(el, target);
			obs.unobserve(el);
		});
	}, { threshold: 0.4 });

	counters.forEach((counter) => observer.observe(counter));

	const liveWorkout = document.querySelector('[data-live-workout-counter]');
	if (liveWorkout && !prefersReducedMotion) {
		const base = Number(liveWorkout.getAttribute('data-live-workout-counter') || 0);
		setInterval(() => {
			const delta = Math.round(Math.random() * 4);
			liveWorkout.textContent = (base + delta).toLocaleString();
		}, 3000);
	}
};

const updateRingProgress = (ring, value) => {
	const progressCircle = ring.querySelector('circle.progress');
	if (!progressCircle) {
		return;
	}

	const radius = 52;
	const circumference = 2 * Math.PI * radius;
	const ratio = Math.max(0, Math.min(Number(value), 100)) / 100;
	const offset = circumference - ratio * circumference;

	progressCircle.style.strokeDasharray = `${circumference}`;
	progressCircle.style.strokeDashoffset = `${offset}`;
};

const initProgressRings = () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	const rings = document.querySelectorAll('[data-progress-ring]');
	rings.forEach((ring) => updateRingProgress(ring, ring.getAttribute('data-progress-ring') || 0));
};

const fetchStats = async () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	const cards = document.querySelectorAll('[data-stat-card]');

	try {
		const response = await fetch('/api/stats', { headers: { Accept: 'application/json' } });
		if (!response.ok) {
			return;
		}

		const payload = await response.json();
		const stats = payload.data || {};

		const statMap = {
			total_participants: Number(stats.total_participants || 0),
			total_test_sessions: Number(stats.total_test_sessions || 0),
			pass_rate: Number(stats.pass_rate || 0),
			live_workout_counter: Number(stats.live_workout_counter || 0),
		};

		document.querySelectorAll('[data-counter], [data-live-workout-counter], [data-ring-value]').forEach((node) => {
			const key = node.getAttribute('data-stat-key');
			if (!key || !(key in statMap)) {
				return;
			}

			const value = key === 'pass_rate' ? Math.round(statMap[key]) : statMap[key];
			const isPercent = node.getAttribute('data-stat-format') === 'percent';

			if (node.hasAttribute('data-counter')) {
				node.setAttribute('data-counter', String(value));
			}

			if (node.hasAttribute('data-live-workout-counter')) {
				node.setAttribute('data-live-workout-counter', String(value));
			}

			node.textContent = isPercent
				? `${value}%`
				: Number(value).toLocaleString();
		});

		document.querySelectorAll('[data-progress-ring]').forEach((ring) => {
			const key = ring.getAttribute('data-stat-key');
			if (!key || !(key in statMap)) {
				updateRingProgress(ring, ring.getAttribute('data-progress-ring') || 0);
				return;
			}

			const rawValue = statMap[key];
			let ringValue = 0;

			if (key === 'pass_rate') {
				ringValue = Math.round(rawValue);
			} else if (key === 'total_test_sessions') {
				ringValue = Math.min(100, Math.max(8, Math.round(rawValue)));
			} else if (key === 'total_participants') {
				ringValue = Math.min(100, Math.max(10, Math.round(rawValue / 2)));
			} else if (key === 'live_workout_counter') {
				ringValue = Math.min(100, Math.max(15, Math.round(rawValue / 2)));
			}

			ring.setAttribute('data-progress-ring', String(ringValue));
			updateRingProgress(ring, ringValue);
		});

		cards.forEach((card) => card.classList.remove('skeleton-card'));
		initCountUpCounters();
	} catch {
		cards.forEach((card) => card.classList.remove('skeleton-card'));
	}
};

const renderStars = (rating = 5) => {
	const value = Math.max(1, Math.min(Number(rating), 5));
	return new Array(5).fill('★').map((star, index) => (index < value ? star : '☆')).join('');
};

const escapeHTML = (value = '') => value
	.replaceAll('&', '&amp;')
	.replaceAll('<', '&lt;')
	.replaceAll('>', '&gt;')
	.replaceAll('"', '&quot;')
	.replaceAll("'", '&#039;');

const initTestimonials = async () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	const skeletons = document.getElementById('testimonialSkeletons');
	const slider = document.getElementById('testimonialSwiper');
	const slides = document.getElementById('testimonialSlides');
	if (!slider || !slides) {
		return;
	}

	let initial = true;

	try {
		const response = await fetch('/api/testimonials', { headers: { Accept: 'application/json' } });
		if (response.ok) {
			const payload = await response.json();
			const records = Array.isArray(payload.data) ? payload.data : [];
			if (records.length > 0) {
				const html = records.map((item) => {
					const name = escapeHTML(item.name || 'JPJFit User');
					const content = escapeHTML(item.content || 'Great experience with JPJFit.');
					const rating = Number(item.rating || 5);
					return `
						<article class="swiper-slide testimonial-card">
							<div class="flex items-center gap-3">
								<div class="avatar-wrap">${name.charAt(0)}</div>
								<div>
									<p class="font-semibold text-white">${name}</p>
									<div class="stars" aria-label="${rating} star rating">${renderStars(rating)}</div>
								</div>
							</div>
							<p class="mt-4 text-sm text-slate-300">${content}</p>
							<span class="quote-icon" aria-hidden="true">"</span>
						</article>`;
				}).join('');

				slides.innerHTML = html;
				initial = false;
			}
		}
	} catch {
		// Keep server-rendered fallback cards when API request fails.
	}

	if (initial) {
		slides.querySelectorAll('.stars').forEach((starEl) => {
			const rating = Number(starEl.getAttribute('data-rating') || 5);
			starEl.textContent = renderStars(rating);
		});
	}

	skeletons?.classList.add('hidden');
	slider.classList.remove('hidden');

	const testimonialSwiper = new Swiper(slider, {
		modules: [Autoplay, Pagination],
		loop: true,
		autoplay: prefersReducedMotion
			? false
			: {
				delay: 3500,
				disableOnInteraction: false,
			},
		spaceBetween: 16,
		pagination: {
			el: '.testimonial-pagination',
			clickable: true,
		},
		breakpoints: {
			768: { slidesPerView: 2 },
			1024: { slidesPerView: 3 },
		},
		slidesPerView: 1,
	});

	slider.addEventListener('mouseenter', () => testimonialSwiper.autoplay?.stop());
	slider.addEventListener('mouseleave', () => testimonialSwiper.autoplay?.start());
};

const initFAQ = () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	document.querySelectorAll('#faqAccordion .faq-item').forEach((item) => {
		const trigger = item.querySelector('.faq-trigger');
		const panel = item.querySelector('.faq-panel');
		if (!trigger || !panel) {
			return;
		}

		trigger.addEventListener('click', () => {
			const expanded = trigger.getAttribute('aria-expanded') === 'true';
			trigger.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			item.classList.toggle('active', !expanded);
			panel.hidden = expanded;
		});
	});

	const search = document.getElementById('faqSearch');
	search?.addEventListener('input', () => {
		const keyword = search.value.trim().toLowerCase();
		document.querySelectorAll('[data-faq-item]').forEach((item) => {
			const text = (item.textContent || '').toLowerCase();
			item.classList.toggle('hidden', keyword.length > 0 && !text.includes(keyword));
		});
	});
};

const initBeforeAfterSlider = () => {
	document.querySelectorAll('[data-before-after]').forEach((wrap) => {
		const after = wrap.querySelector('[data-after]');
		const divider = wrap.querySelector('[data-divider]');
		const handle = wrap.querySelector('[data-handle]');
		const range = wrap.querySelector('[data-range]');

		if (!(after instanceof HTMLElement)
			|| !(divider instanceof HTMLElement)
			|| !(handle instanceof HTMLElement)
			|| !(range instanceof HTMLInputElement)) {
			return;
		}

		const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
		let isDragging = false;

		const setPosition = (value) => {
			const percent = clamp(value, 0, 100);
			after.style.width = '100%';
			wrap.style.setProperty('--comparison-position', `${percent}%`);
			divider.style.left = `${percent}%`;
			range.value = `${percent}`;
			handle.setAttribute('aria-valuenow', `${Math.round(percent)}`);
		};

		const getPercentFromEvent = (event) => {
			const rect = wrap.getBoundingClientRect();
			const relative = ((event.clientX - rect.left) / rect.width) * 100;
			return clamp(relative, 0, 100);
		};

		const beginDrag = (event) => {
			isDragging = true;
			wrap.classList.add('is-dragging');
			event.preventDefault();
			setPosition(getPercentFromEvent(event));
		};

		const moveDrag = (event) => {
			if (!isDragging) {
				return;
			}

			event.preventDefault();
			setPosition(getPercentFromEvent(event));
		};

		const endDrag = () => {
			if (!isDragging) {
				return;
			}

			isDragging = false;
			wrap.classList.remove('is-dragging');
		};

		const initial = Number(wrap.dataset.position || range.value || 50);
		setPosition(initial);

		range.addEventListener('input', () => {
			setPosition(Number(range.value));
		});

		handle.addEventListener('keydown', (event) => {
			const keyStep = event.key === 'ArrowLeft' ? -1 : event.key === 'ArrowRight' ? 1 : 0;
			if (keyStep === 0) {
				return;
			}

			event.preventDefault();
			setPosition(Number(range.value) + keyStep);
		});

		handle.addEventListener('pointerdown', beginDrag);
		wrap.addEventListener('pointerdown', (event) => {
			if (event.target === range) {
				return;
			}

			beginDrag(event);
		});

		window.addEventListener('pointermove', moveDrag, { passive: false });
		window.addEventListener('pointerup', endDrag);
		window.addEventListener('pointercancel', endDrag);
	});
};

const formatCountdown = (totalSeconds) => {
	const days = Math.floor(totalSeconds / 86400);
	const hours = Math.floor((totalSeconds % 86400) / 3600);
	const minutes = Math.floor((totalSeconds % 3600) / 60);
	const seconds = Math.floor(totalSeconds % 60);

	return { days, hours, minutes, seconds };
};

const initCountdowns = () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	const countdown = document.querySelector('[data-countdown]');
	let countdownSeconds = Number(countdown?.getAttribute('data-countdown') || 0);

	const offerCountdown = document.querySelector('[data-offer-countdown]');
	let offerSeconds = Number(offerCountdown?.getAttribute('data-offer-countdown') || 0);

	const updateMain = () => {
		if (!countdown) {
			return;
		}

		const values = formatCountdown(countdownSeconds);
		const pad = (value) => String(value).padStart(2, '0');

		const days = countdown.querySelector('[data-countdown-days]');
		const hours = countdown.querySelector('[data-countdown-hours]');
		const minutes = countdown.querySelector('[data-countdown-minutes]');
		const seconds = countdown.querySelector('[data-countdown-seconds]');

		if (days) days.textContent = pad(values.days);
		if (hours) hours.textContent = pad(values.hours);
		if (minutes) minutes.textContent = pad(values.minutes);
		if (seconds) seconds.textContent = pad(values.seconds);
	};

	const updateOffer = () => {
		if (!offerCountdown) {
			return;
		}

		const values = formatCountdown(offerSeconds);
		const hours = offerCountdown.querySelector('[data-offer-hours]');
		const minutes = offerCountdown.querySelector('[data-offer-minutes]');
		const seconds = offerCountdown.querySelector('[data-offer-seconds]');

		if (hours) hours.textContent = String(values.hours + values.days * 24).padStart(2, '0');
		if (minutes) minutes.textContent = String(values.minutes).padStart(2, '0');
		if (seconds) seconds.textContent = String(values.seconds).padStart(2, '0');
	};

	updateMain();
	updateOffer();

	window.setInterval(() => {
		countdownSeconds = Math.max(0, countdownSeconds - 1);
		offerSeconds = Math.max(0, offerSeconds - 1);
		updateMain();
		updateOffer();
	}, 1000);
};

const initFitnessPreviewChart = () => {
	const canvas = document.getElementById('fitnessPreviewChart');
	if (!canvas) {
		return;
	}

	const isMalay = document.documentElement.lang?.toLowerCase().startsWith('ms');
	const weekLabels = isMalay
		? ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5']
		: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
	const performanceTrendLabel = isMalay ? 'Trend Prestasi' : 'Performance Trend';

	new Chart(canvas, {
		type: 'line',
		data: {
			labels: weekLabels,
			datasets: [
				{
					label: performanceTrendLabel,
					data: [52, 58, 66, 73, 81],
					borderColor: '#38bdf8',
					backgroundColor: 'rgba(56, 189, 248, 0.2)',
					tension: 0.35,
					fill: true,
				},
			],
		},
		options: {
			plugins: { legend: { display: false } },
			scales: {
				x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148, 163, 184, 0.15)' } },
				y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148, 163, 184, 0.15)' } },
			},
		},
	});
};

const initBMICalculator = () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	const modal = document.getElementById('bmiModal');
	const openButton = document.getElementById('openBmiModal');
	const calculateButton = document.getElementById('calcBmiBtn');
	const heightInput = document.getElementById('bmiHeight');
	const weightInput = document.getElementById('bmiWeight');
	const output = document.getElementById('bmiResult');

	openButton?.addEventListener('click', () => {
		if (typeof modal?.showModal === 'function') {
			modal.showModal();
		}
	});

	calculateButton?.addEventListener('click', () => {
		const height = Number(heightInput?.value || 0);
		const weight = Number(weightInput?.value || 0);

		if (!height || !weight || !output) {
			return;
		}

		const bmi = weight / ((height / 100) ** 2);
		let label = 'Normal';
		if (bmi < 18.5) {
			label = 'Underweight';
		} else if (bmi >= 25 && bmi < 30) {
			label = 'Overweight';
		} else if (bmi >= 30) {
			label = 'Obese';
		}

		output.textContent = `BMI: ${bmi.toFixed(2)} (${label})`;
	});
};

const initWorkflowProgress = () => {
	const steps = document.querySelectorAll('[data-step-progress]');
	if (!steps.length) {
		return;
	}

	const observer = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				entry.target.classList.add('is-visible');
			}
		});
	}, { threshold: 0.4 });

	steps.forEach((step) => observer.observe(step));
};

const initTiltCards = () => {
	if (prefersReducedMotion) {
		return;
	}

	document.querySelectorAll('.tilt-card').forEach((card) => {
		card.addEventListener('mousemove', (event) => {
			const rect = card.getBoundingClientRect();
			const x = event.clientX - rect.left;
			const y = event.clientY - rect.top;
			const rotateX = ((y / rect.height) - 0.5) * -10;
			const rotateY = ((x / rect.width) - 0.5) * 10;
			card.style.transform = `translateY(-8px) scale(1.02) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
		});

		card.addEventListener('mouseleave', () => {
			card.style.transform = '';
		});
	});
};

const initMagneticButtons = () => {
	if (prefersReducedMotion) {
		return;
	}

	document.querySelectorAll('.cta-magnetic').forEach((button) => {
		button.addEventListener('mousemove', (event) => {
			const rect = button.getBoundingClientRect();
			const x = event.clientX - rect.left - rect.width / 2;
			const y = event.clientY - rect.top - rect.height / 2;
			button.style.transform = `translate(${x * 0.12}px, ${y * 0.12}px)`;
		});

		button.addEventListener('mouseleave', () => {
			button.style.transform = '';
		});
	});
};

const initRippleEffect = () => {
	document.addEventListener('click', (event) => {
		const target = event.target.closest('a, button, .fab-btn');
		if (!(target instanceof HTMLElement)) {
			return;
		}

		const rect = target.getBoundingClientRect();
		const ripple = document.createElement('span');
		ripple.className = 'ripple';
		ripple.style.left = `${event.clientX - rect.left}px`;
		ripple.style.top = `${event.clientY - rect.top}px`;
		ripple.style.width = `${Math.max(rect.width, rect.height)}px`;
		ripple.style.height = `${Math.max(rect.width, rect.height)}px`;
		ripple.style.position = 'absolute';

		if (getComputedStyle(target).position === 'static') {
			target.style.position = 'relative';
		}

		target.appendChild(ripple);
		setTimeout(() => ripple.remove(), 650);
	});
};

const initMouseTrail = () => {
	if (document.body.dataset.page !== 'landing' || prefersReducedMotion) {
		return;
	}

	const canvas = document.getElementById('heroMouseTrail');
	if (!(canvas instanceof HTMLCanvasElement)) {
		return;
	}

	const context = canvas.getContext('2d');
	if (!context) {
		return;
	}

	const points = [];

	const resize = () => {
		canvas.width = window.innerWidth;
		canvas.height = window.innerHeight;
	};

	resize();
	window.addEventListener('resize', debounce(resize, 120));

	window.addEventListener('mousemove', (event) => {
		points.push({ x: event.clientX, y: event.clientY, alpha: 1 });
		if (points.length > 40) {
			points.shift();
		}
	});

	const draw = () => {
		context.clearRect(0, 0, canvas.width, canvas.height);
		points.forEach((point) => {
			context.beginPath();
			context.arc(point.x, point.y, 3, 0, Math.PI * 2);
			context.fillStyle = `rgba(56, 189, 248, ${point.alpha})`;
			context.fill();
			point.alpha *= 0.93;
		});

		for (let i = points.length - 1; i >= 0; i -= 1) {
			if (points[i].alpha < 0.05) {
				points.splice(i, 1);
			}
		}

		requestAnimationFrame(draw);
	};

	draw();
};

const initParallaxSections = () => {
	if (prefersReducedMotion) {
		return;
	}

	const sections = document.querySelectorAll('.parallax-section');
	if (!sections.length) {
		return;
	}

	const update = () => {
		const scrollY = window.scrollY;
		sections.forEach((section) => {
			const speed = Number(section.getAttribute('data-parallax-speed') || 0.04);
			section.style.transform = `translate3d(0, ${scrollY * speed}px, 0)`;
		});
	};

	window.addEventListener('scroll', debounce(update, 12), { passive: true });
	update();
};

const initFAQBackgroundParallax = () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	const faqSection = document.getElementById('faq');
	if (!faqSection) {
		return;
	}

	const fixedLayer = faqSection.querySelector('.faq-bg-fixed-layer');

	let ticking = false;

	const update = () => {
		ticking = false;

		const rect = faqSection.getBoundingClientRect();
		const viewportHeight = window.innerHeight || 1;
		const isVisible = rect.bottom > 0 && rect.top < viewportHeight;

		faqSection.classList.toggle('faq-fixed-active', isVisible);

		if (!isVisible) {
			if (fixedLayer) {
				fixedLayer.style.transform = 'translate3d(0, 0, 0) scale(1.03)';
			}
			return;
		}

		if (prefersReducedMotion) {
			return;
		}

		// Shift the image subtly while it stays viewport-fixed.
		const progress = (viewportHeight - rect.top) / (viewportHeight + rect.height);
		const clamped = Math.max(0, Math.min(1, progress));
		const offset = (clamped - 0.5) * 80;

		if (fixedLayer instanceof HTMLElement) {
			fixedLayer.style.transform = `translate3d(0, ${(offset * 0.35).toFixed(2)}px, 0) scale(1.03)`;
		}

		faqSection.style.backgroundPosition = `center center, center calc(50% + ${offset.toFixed(2)}px)`;
	};

	const onScroll = () => {
		if (ticking) {
			return;
		}

		ticking = true;
		requestAnimationFrame(update);
	};

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll, { passive: true });
	onScroll();
};

const initParticles = () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	if (typeof window.particlesJS !== 'function') {
		return;
	}

	window.particlesJS('heroParticles', {
		particles: {
			number: { value: 36, density: { enable: true, value_area: 800 } },
			color: { value: ['#38bdf8', '#818cf8', '#fbbf24'] },
			shape: { type: 'circle' },
			opacity: { value: 0.45 },
			size: { value: 3, random: true },
			line_linked: { enable: true, distance: 130, color: '#93c5fd', opacity: 0.22, width: 1 },
			move: { enable: true, speed: 1.4, out_mode: 'out' },
		},
		interactivity: {
			detect_on: 'canvas',
			events: {
				onhover: { enable: true, mode: 'repulse' },
				onclick: { enable: true, mode: 'push' },
			},
			modes: {
				repulse: { distance: 90 },
				push: { particles_nb: 3 },
			},
		},
		retina_detect: true,
	});
};

const initTextScramble = () => {
	const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	document.querySelectorAll('[data-text-scramble]').forEach((element) => {
		const finalText = element.getAttribute('data-text-scramble') || element.textContent || '';
		element.addEventListener('mouseenter', () => {
			let frame = 0;
			const interval = window.setInterval(() => {
				const nextText = finalText.split('').map((letter, index) => {
					if (index < frame) {
						return letter;
					}
					return chars[Math.floor(Math.random() * chars.length)];
				}).join('');

				element.textContent = nextText;
				frame += 1 / 2;

				if (frame >= finalText.length) {
					window.clearInterval(interval);
					element.textContent = finalText;
				}
			}, 28);
		});
	});
};

const initAchievementConfetti = () => {
	if (prefersReducedMotion) {
		return;
	}

	document.querySelectorAll('.fitness-level').forEach((badge) => {
		badge.addEventListener('mouseenter', () => {
			const rect = badge.getBoundingClientRect();
			const x = (rect.left + rect.width / 2) / window.innerWidth;
			const y = (rect.top + rect.height / 2) / window.innerHeight;

			confetti({
				particleCount: 40,
				spread: 42,
				origin: { x, y },
				zIndex: 80,
			});
		});
	});
};

const initOverviewFeaturesBackgroundRotator = () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	const sections = ['overview', 'features']
		.map((id) => document.getElementById(id))
		.filter((section) => section instanceof HTMLElement);

	if (!sections.length) {
		return;
	}

	const images = [
		'/images/section_2_bg_1.jpg',
		'/images/section_2_bg_2.jpg',
		'/images/section_2_bg_3.jpg',
	];

	const setLayerImage = (section, layer, src) => {
		section.style.setProperty(`--section-rotator-${layer}`, `url('${src}')`);
	};

	images.forEach((src) => {
		const image = new Image();
		image.src = src;
	});

	sections.forEach((section) => {
		section.dataset.activeLayer = 'a';
		setLayerImage(section, 'a', images[0]);
		setLayerImage(section, 'b', images[1] || images[0]);
	});

	if (prefersReducedMotion || images.length < 2) {
		return;
	}

	   let activeLayer = 'a';
	   let roundIndex = 0;
	   // For each image, show it twice: once at top, once at bottom, then move to next image
	   window.setInterval(() => {
		   const incomingLayer = activeLayer === 'a' ? 'b' : 'a';
		   // Each image is shown twice per round: top then bottom
		   const imageIdx = Math.floor(roundIndex / 2) % images.length;
		   const isTop = roundIndex % 2 === 0;

		   sections.forEach((section) => {
			   setLayerImage(section, incomingLayer, images[imageIdx]);
			   section.dataset.activeLayer = incomingLayer;
			   section.dataset.bgPosition = isTop ? 'top' : 'bottom';
		   });

		   activeLayer = incomingLayer;
		   roundIndex = (roundIndex + 1) % (images.length * 2);
	   }, 4000);
};

const initHighContrastToggle = () => {
	const button = document.getElementById('highContrastToggle');
	if (!button) {
		return;
	}

	button.addEventListener('click', () => {
		document.body.classList.toggle('high-contrast');
	});
};

const initLandingPage = () => {
	if (document.body.dataset.page !== 'landing') {
		return;
	}

	initLandingTheme();
	initLandingHeader();
	initHeroSlider();
	initOverviewFeaturesBackgroundRotator();
	initAOS();
	initCountUpCounters();
	initProgressRings();
	fetchStats();
	initTestimonials();
	initFAQ();
	initBeforeAfterSlider();
	initCountdowns();
	initFitnessPreviewChart();
	initBMICalculator();
	initWorkflowProgress();
	initTiltCards();
	initMagneticButtons();
	initRippleEffect();
	initTextScramble();
	initParticles();
	initMouseTrail();
	initParallaxSections();
	initFAQBackgroundParallax();
	initAchievementConfetti();
	initHighContrastToggle();
};

const initCertificateGenerateModal = () => {
	const form = document.getElementById('certificateGenerateForm');
	if (!form) {
		return;
	}

	const sessionSelect = form.querySelector('select[name="test_session_id"]');
	const submitButton = form.querySelector('[data-generate-submit]');
	const modal = document.getElementById('certificateGenerateModal');
	const modalMessage = document.getElementById('certificateGenerateModalMessage');

	if (!sessionSelect || !submitButton || !modal || !modalMessage) {
		return;
	}

	form.addEventListener('submit', (event) => {
		if (!sessionSelect.value) {
			return;
		}

		event.preventDefault();

		const selectedOption = sessionSelect.options[sessionSelect.selectedIndex];
		const sessionCode = selectedOption?.dataset.sessionCode || '';
		const pendingCount = selectedOption?.dataset.pendingCount || '0';
		const processingPrefix = modal.dataset.processingPrefix || 'Generating certificates for session';
		const eligibleLabel = modal.dataset.eligibleLabel || 'eligible participants';

		if (sessionCode !== '') {
			modalMessage.textContent = `${processingPrefix} ${sessionCode} (${pendingCount} ${eligibleLabel}).`;
		}

		submitButton.disabled = true;
		submitButton.classList.add('opacity-60', 'cursor-not-allowed');
		modal.classList.remove('hidden');
		modal.classList.add('flex');

		window.requestAnimationFrame(() => {
			window.setTimeout(() => {
				form.submit();
			}, 60);
		});
	});
};

const initCholesterolTrendChart = () => {
	const section = document.getElementById('cholesterolTrendSection');
	if (!section) {
		return;
	}

	const canvas = document.getElementById('cholesterolTrendChart');
	const participantSelect = document.getElementById('cholesterolParticipantSelect');
	const emptyState = document.getElementById('cholesterolTrendEmptyState');

	if (!canvas || !participantSelect) {
		return;
	}

	let historyByParticipant = {};
	try {
		historyByParticipant = JSON.parse(section.dataset.history || '{}');
	} catch {
		historyByParticipant = {};
	}

	const yAxisLabel = section.dataset.yLabel || 'Cholesterol (mmol/L)';
	const noDataMessage = section.dataset.noData || 'No cholesterol trend data available for this participant.';
	let chartInstance;

	const render = (participantId) => {
		const points = historyByParticipant[String(participantId)] || [];

		if (chartInstance) {
			chartInstance.destroy();
		}

		if (!points.length) {
			emptyState.textContent = noDataMessage;
			return;
		}

		emptyState.textContent = '';

		chartInstance = new Chart(canvas, {
			type: 'line',
			data: {
				labels: points.map((point) => point.date),
				datasets: [
					{
						label: yAxisLabel,
						data: points.map((point) => point.value),
						borderColor: '#0f766e',
						backgroundColor: 'rgba(15, 118, 110, 0.15)',
						tension: 0.35,
						fill: true,
						pointRadius: 4,
						pointHoverRadius: 6,
						pointBackgroundColor: points.map((point) => point.color),
					},
				],
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					tooltip: {
						callbacks: {
							label: (context) => {
								const value = context.parsed.y;
								const point = points[context.dataIndex];
								return `${value} mmol/L (${point?.level ?? ''})`;
							},
						},
					},
				},
				scales: {
					y: {
						beginAtZero: false,
						title: {
							display: true,
							text: yAxisLabel,
						},
					},
				},
			},
		});
	};

	const defaultId = section.dataset.defaultParticipantId || participantSelect.value;
	if (defaultId) {
		participantSelect.value = String(defaultId);
		render(defaultId);
	}

	participantSelect.addEventListener('change', () => {
		render(participantSelect.value);
	});
};

document.addEventListener('DOMContentLoaded', () => {
	animateLegacyCounters();
	renderDashboardCharts();
	applyPageTransition();
	removePageLoader();
	initThemeToggle();
	initDataTables();
	initLandingPage();
	initCertificateGenerateModal();
	initCholesterolTrendChart();
});
