/**
 * ADiva theme interactions. No dependencies; every feature is progressive
 * enhancement over working HTML (links, forms, native scrolling).
 *
 *  - sticky header state + measured offset for sticky toolbars
 *  - announcement rotation
 *  - mega menu (hover via CSS, click/keyboard via buttons)
 *  - drawers / search overlay / quick-view modal with focus trap
 *  - mobile accordion menu, product accordions
 *  - product carousels (arrows + dots over scroll-snap)
 *  - quick view, wishlist, recently viewed (localStorage)
 *  - load more, sort auto-submit, gallery zoom, quantity steppers, swatches
 */
(function () {
	'use strict';

	var CFG = window.ADIVA || { i18n: {} };
	var T = CFG.i18n || {};
	var doc = document;
	var root = doc.documentElement;
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	var $ = function (sel, ctx) { return (ctx || doc).querySelector(sel); };
	var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || doc).querySelectorAll(sel)); };
	var esc = function (s) {
		return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	};
	var store = {
		get: function (k, d) { try { var v = JSON.parse(localStorage.getItem(k)); return v == null ? d : v; } catch (e) { return d; } },
		set: function (k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) { /* private mode */ } }
	};

	/* ---------- Toast ---------- */
	var toastTimer;
	function toast(msg) {
		var el = $('.toast');
		if (!el) { return; }
		el.textContent = msg;
		el.hidden = false;
		clearTimeout(toastTimer);
		toastTimer = setTimeout(function () { el.hidden = true; }, 3200);
	}

	/* ---------- Sticky header ---------- */
	function initHeader() {
		var header = $('#site-header');
		if (!header) { return; }
		var setOffset = function () { root.style.setProperty('--sticky-top', header.offsetHeight + 'px'); };
		var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 40); };
		setOffset();
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', setOffset);
		if ('ResizeObserver' in window) { new ResizeObserver(setOffset).observe(header); }
	}

	/* ---------- Announcement ---------- */
	function initAnnouncement() {
		var bar = $('[data-announcement]');
		if (!bar) { return; }
		var msgs = $$('.announcement__msg', bar);
		if (msgs.length < 2) { return; }
		var i = 0, timer, paused = false;
		var show = function (n) {
			msgs[i].hidden = true; msgs[i].classList.remove('is-active');
			i = (n + msgs.length) % msgs.length;
			msgs[i].hidden = false; msgs[i].classList.add('is-active');
		};
		var start = function () { if (!reduceMotion) { clearInterval(timer); timer = setInterval(function () { if (!paused) { show(i + 1); } }, 5000); } };
		$$('.announcement__nav', bar).forEach(function (b) {
			b.addEventListener('click', function () { show(i + parseInt(b.getAttribute('data-dir'), 10)); start(); });
		});
		bar.addEventListener('mouseenter', function () { paused = true; });
		bar.addEventListener('mouseleave', function () { paused = false; });
		bar.addEventListener('focusin', function () { paused = true; });
		bar.addEventListener('focusout', function () { paused = false; });
		start();
	}

	/* ---------- Mega menu ---------- */
	function initMega() {
		var items = $$('.primary-nav__item.has-mega');
		var closeAll = function (except) {
			items.forEach(function (it) {
				if (it !== except) {
					it.classList.remove('is-open');
					var b = $('.mega-toggle', it);
					if (b) { b.setAttribute('aria-expanded', 'false'); }
				}
			});
		};
		items.forEach(function (it) {
			var btn = $('.mega-toggle', it);
			btn.addEventListener('click', function () {
				var open = !it.classList.contains('is-open');
				closeAll(it);
				it.classList.toggle('is-open', open);
				btn.setAttribute('aria-expanded', String(open));
				if (open) { var first = $('.mega a', it); if (first) { first.focus(); } }
			});
			it.addEventListener('mouseleave', function () {
				if (it.contains(doc.activeElement)) { return; }
				it.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false');
			});
			it.addEventListener('focusout', function (e) {
				if (!it.contains(e.relatedTarget)) { it.classList.remove('is-open'); btn.setAttribute('aria-expanded', 'false'); }
			});
		});
		doc.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape') { return; }
			var open = items.filter(function (it) { return it.classList.contains('is-open'); })[0];
			if (open) { closeAll(); $('.mega-toggle', open).focus(); }
		});
		doc.addEventListener('click', function (e) { if (!e.target.closest('.primary-nav')) { closeAll(); } });
	}

	/* ---------- Overlays: drawers, search, modal ---------- */
	var active = null; // { el, trigger }
	var scrim = $('[data-scrim]');
	var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])';

	function openOverlay(el, trigger) {
		if (!el) { return; }
		if (active) { closeOverlay(true); }
		active = { el: el, trigger: trigger || doc.activeElement };
		el.hidden = false;
		if (scrim && !el.classList.contains('modal')) { scrim.hidden = false; }
		doc.body.classList.add('is-locked');
		$$('[aria-controls="' + el.id + '"]').forEach(function (b) { b.setAttribute('aria-expanded', 'true'); });
		// Next frame so the transition runs from the closed state.
		requestAnimationFrame(function () {
			requestAnimationFrame(function () {
				el.classList.add('is-open');
				if (scrim) { scrim.classList.add('is-visible'); }
				var target = $('input[type="search"]', el) || $(FOCUSABLE, el);
				if (target) { target.focus({ preventScroll: true }); }
			});
		});
	}

	function closeOverlay(immediate) {
		if (!active) { return; }
		var el = active.el, trigger = active.trigger;
		active = null;
		el.classList.remove('is-open');
		if (scrim) { scrim.classList.remove('is-visible'); }
		$$('[aria-controls="' + el.id + '"]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
		var done = function () {
			el.hidden = true;
			if (scrim && !active) { scrim.hidden = true; }
			if (!active) { doc.body.classList.remove('is-locked'); }
		};
		if (immediate || reduceMotion) { done(); } else { setTimeout(done, 320); }
		if (trigger && trigger.focus && !immediate) { trigger.focus({ preventScroll: true }); }
	}

	function initOverlays() {
		doc.addEventListener('click', function (e) {
			var opener = e.target.closest('[data-open]');
			if (opener) {
				var target = doc.getElementById(opener.getAttribute('data-open'));
				if (target) { e.preventDefault(); openOverlay(target, opener); }
				return;
			}
			if (e.target.closest('[data-close]') || e.target === scrim) { closeOverlay(); return; }
			if (active && active.el.classList.contains('modal') && e.target === active.el) { closeOverlay(); }
		});
		doc.addEventListener('keydown', function (e) {
			if (!active) { return; }
			if (e.key === 'Escape') { closeOverlay(); return; }
			if (e.key !== 'Tab') { return; }
			var nodes = $$(FOCUSABLE, active.el).filter(function (n) { return n.offsetParent !== null || n === doc.activeElement; });
			if (!nodes.length) { return; }
			var first = nodes[0], last = nodes[nodes.length - 1];
			if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
			else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
		});
		// Leaving desktop width with the mobile menu open.
		window.addEventListener('resize', function () {
			if (active && active.el.id === 'mobile-drawer' && window.innerWidth >= 1100) { closeOverlay(true); }
		});
	}

	/* ---------- Accordions (mobile menu + product) ---------- */
	function initAccordions() {
		doc.addEventListener('click', function (e) {
			var btn = e.target.closest('.mobile-nav__toggle, .accordion__heading button');
			if (!btn) { return; }
			var panel = doc.getElementById(btn.getAttribute('aria-controls'));
			var open = btn.getAttribute('aria-expanded') !== 'true';
			btn.setAttribute('aria-expanded', String(open));
			if (panel) { panel.hidden = !open; }
		});
	}

	/* ---------- Carousels ---------- */
	function initCarousel(car) {
		if (car.__adiva) { car.__adiva(); return; }
		var track = $('.carousel__track', car);
		var prev = $('[data-prev]', car);
		var next = $('[data-next]', car);
		var dotsWrap = $('[data-dots]', car);
		var pages = 0;

		var step = function () { return track.clientWidth; };
		var update = function () {
			var max = track.scrollWidth - track.clientWidth;
			if (prev) { prev.disabled = track.scrollLeft <= 4; }
			if (next) { next.disabled = track.scrollLeft >= max - 4; }
			var count = Math.max(1, Math.ceil((track.scrollWidth - 4) / Math.max(1, track.clientWidth)));
			if (count !== pages) { buildDots(count); }
			var idx = max <= 0 ? 0 : Math.round((track.scrollLeft / max) * (pages - 1));
			$$('.carousel__dot', dotsWrap).forEach(function (d, n) { d.setAttribute('aria-current', String(n === idx)); });
		};
		var buildDots = function (count) {
			pages = count;
			if (!dotsWrap) { return; }
			dotsWrap.innerHTML = '';
			dotsWrap.hidden = count < 2;
			for (var n = 0; n < count; n++) {
				var b = doc.createElement('button');
				b.type = 'button';
				b.className = 'carousel__dot';
				b.setAttribute('aria-label', (T.slide || 'Go to slide %d').replace('%d', n + 1));
				(function (n) {
					b.addEventListener('click', function () {
						var max = track.scrollWidth - track.clientWidth;
						track.scrollTo({ left: count < 2 ? 0 : (max * n) / (count - 1), behavior: reduceMotion ? 'auto' : 'smooth' });
					});
				})(n);
				dotsWrap.appendChild(b);
			}
		};
		var go = function (dir) { track.scrollBy({ left: dir * step(), behavior: reduceMotion ? 'auto' : 'smooth' }); };
		if (prev) { prev.addEventListener('click', function () { go(-1); }); }
		if (next) { next.addEventListener('click', function () { go(1); }); }
		track.addEventListener('keydown', function (e) {
			if (e.target !== track) { return; }
			if (e.key === 'ArrowRight') { e.preventDefault(); go(1); }
			if (e.key === 'ArrowLeft') { e.preventDefault(); go(-1); }
		});
		var ticking = false;
		track.addEventListener('scroll', function () {
			if (ticking) { return; }
			ticking = true;
			requestAnimationFrame(function () { ticking = false; update(); });
		}, { passive: true });
		window.addEventListener('resize', update);
		car.__adiva = update;
		update();
	}

	/* ---------- Product data from cards ---------- */
	function cardData(el) {
		var card = el.closest('[data-product]');
		if (!card) { return null; }
		try { return JSON.parse(card.getAttribute('data-product')); } catch (e) { return null; }
	}

	function cardHTML(d) {
		var tint = d.bg ? ' style="--tint:' + esc(d.bg) + '"' : '';
		return '<li class="carousel__slide product-grid__item"><article class="card" data-product="' + esc(JSON.stringify(d)) + '" data-id="' + esc(d.id) + '">' +
			'<div class="card__media"' + tint + '><a class="card__img-link" href="' + esc(d.url) + '" tabindex="-1" aria-hidden="true">' +
			'<img class="card__img" src="' + esc(d.image) + '" alt="" width="600" height="750" loading="lazy" decoding="async"></a>' +
			'<button class="card__wish wish-toggle" type="button" aria-pressed="false" data-id="' + esc(d.id) + '"><svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 20s-7.5-4.6-9.2-9.3C1.7 7.6 3.6 4.5 6.9 4.5c2 0 3.5 1.1 4.3 2.6.8-1.5 2.3-2.6 4.3-2.6 3.3 0 5.2 3.1 4.1 6.2C19.5 15.4 12 20 12 20Z"/></svg><span class="screen-reader-text">' + esc(T.wishAdd || 'Add to wishlist') + '</span></button>' +
			'<button class="card__quick" type="button" data-quick-view><span>Quick view</span><span class="screen-reader-text">' + esc(d.title) + '</span></button>' +
			'</div><div class="card__body"><h3 class="card__title"><a href="' + esc(d.url) + '">' + esc(d.title) + '</a></h3>' +
			(d.price || '') + (d.rating || '') + '</div></article></li>';
	}

	/* ---------- Wishlist ---------- */
	var WKEY = 'adiva_wishlist';
	function wishlist() { return store.get(WKEY, {}); }
	function syncWish() {
		var list = wishlist();
		var count = Object.keys(list).length;
		$$('.wishlist-count').forEach(function (c) { c.textContent = count; c.setAttribute('data-count', count); });
		$$('.wish-toggle').forEach(function (b) {
			var id = b.getAttribute('data-id');
			var on = !!(id && list[id]);
			b.setAttribute('aria-pressed', String(on));
			var sr = $('.screen-reader-text', b);
			if (sr && b.classList.contains('card__wish') === false) { sr.textContent = on ? (T.wishRemove || 'Remove from wishlist') : (T.wishAdd || 'Add to wishlist'); }
		});
		renderWishlistPage(list);
	}
	function toggleWish(btn) {
		var id = btn.getAttribute('data-id');
		var d = cardData(btn) || (btn.closest('[data-track-recent]') ? JSON.parse(btn.closest('[data-track-recent]').getAttribute('data-track-recent')) : null);
		if (!id || !d) { return; }
		var list = wishlist();
		if (list[id]) { delete list[id]; toast(T.wishRemove || 'Removed from wishlist'); }
		else { list[id] = d; toast('♥ ' + (d.title || '')); }
		store.set(WKEY, list);
		syncWish();
	}
	function renderWishlistPage(list) {
		var grid = $('[data-wishlist-grid]');
		if (!grid) { return; }
		var items = Object.keys(list).map(function (k) { return list[k]; });
		grid.innerHTML = items.map(cardHTML).join('');
		var empty = $('[data-wishlist-empty]');
		if (empty) { empty.hidden = items.length > 0; }
	}

	/* ---------- Recently viewed ---------- */
	var RKEY = 'adiva_recent';
	function initRecent() {
		var current = $('[data-track-recent]');
		var list = store.get(RKEY, []);
		if (current) {
			try {
				var d = JSON.parse(current.getAttribute('data-track-recent'));
				list = [d].concat(list.filter(function (x) { return x.id !== d.id; })).slice(0, 12);
				store.set(RKEY, list);
			} catch (e) { /* ignore */ }
		}
		$$('[data-recently-viewed]').forEach(function (sec) {
			var exclude = parseInt(sec.getAttribute('data-exclude'), 10) || 0;
			var items = list.filter(function (x) { return x.id !== exclude; });
			if (!items.length) { return; }
			$('.carousel__track', sec).innerHTML = items.map(cardHTML).join('');
			sec.hidden = false;
			initCarousel($('[data-carousel]', sec));
		});
	}

	/* ---------- Cart (WooCommerce AJAX, demo fallback) ---------- */
	function bumpCount(n) {
		$$('.cart-count').forEach(function (c) {
			var v = (parseInt(c.textContent, 10) || 0) + n;
			c.textContent = v; c.setAttribute('data-count', v);
		});
	}
	function addToCart(productId, qty) {
		if (!CFG.wcAjax || !productId) {
			bumpCount(qty || 1);
			toast((T.added || 'Added to your bag') + (CFG.demo ? ' (demo)' : ''));
			return Promise.resolve(true);
		}
		var body = new URLSearchParams({ product_id: productId, quantity: qty || 1 });
		return fetch(CFG.wcAjax.replace('%%endpoint%%', 'add_to_cart'), { method: 'POST', body: body, credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (!res || res.error) { if (res && res.product_url) { window.location = res.product_url; } return false; }
				if (res.fragments) {
					Object.keys(res.fragments).forEach(function (sel) {
						$$(sel).forEach(function (node) { node.outerHTML = res.fragments[sel]; });
					});
				}
				if (window.jQuery) { window.jQuery(doc.body).trigger('added_to_cart', [res.fragments, res.cart_hash]); }
				toast(T.added || 'Added to your bag');
				return true;
			})
			.catch(function () { toast(T.error || 'Something went wrong.'); return false; });
	}

	/* ---------- Quick view ---------- */
	function openQuickView(d, trigger) {
		var m = $('#quick-view');
		if (!m || !d) { return; }
		var img = $('.qv__img', m);
		img.src = d.image; img.alt = d.alt || d.title;
		$('.qv__media', m).style.setProperty('--tint', d.bg || '');
		$('.qv__title', m).textContent = d.title;
		$('.qv__rating', m).innerHTML = d.rating || '';
		$('.qv__price', m).innerHTML = d.price || '';
		$('.qv__short', m).textContent = d.short || '';
		$('.qv__link', m).href = d.url;
		$('.qv__status', m).textContent = '';
		var wish = $('.qv__wish', m);
		wish.setAttribute('data-id', d.id);
		m.setAttribute('data-product', JSON.stringify(d));

		var sizesBox = $('.qv__sizes', m), sw = $('.swatches', sizesBox);
		sw.innerHTML = '';
		var hasSizes = d.sizes && d.sizes.length && CFG.demo;
		sizesBox.hidden = !hasSizes;
		if (hasSizes) {
			d.sizes.forEach(function (s, n) {
				sw.insertAdjacentHTML('beforeend', '<button type="button" class="swatch-btn" role="radio" aria-checked="' + (n === 0) + '">' + esc(s) + '</button>');
			});
		}

		var add = $('.qv__add', m);
		var buyable = d.inStock !== false && (d.cart || CFG.demo);
		add.textContent = buyable ? (T.addToBag || 'Add to bag') : (d.inStock === false ? (T.outOfStock || 'Sold out') : (T.chooseOption || 'Choose options'));
		add.disabled = d.inStock === false;
		add.onclick = function () {
			if (!buyable) { window.location = d.url; return; }
			add.classList.add('is-loading');
			addToCart(d.cart, 1).then(function (ok) {
				add.classList.remove('is-loading');
				if (ok) { $('.qv__status', m).textContent = T.added || 'Added to your bag'; }
			});
		};
		syncWish();
		openOverlay(m, trigger);
	}

	/* ---------- Load more ---------- */
	function initLoadMore() {
		var btn = $('[data-load-more]');
		var grid = $('[data-grid]');
		if (!btn || !grid || !window.fetch || !window.DOMParser) { return; }
		btn.closest('.load-more').classList.add('is-enhanced');
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			var url = btn.getAttribute('href');
			btn.classList.add('is-loading');
			btn.textContent = T.loading || 'Loading…';
			fetch(url, { credentials: 'same-origin' })
				.then(function (r) { return r.text(); })
				.then(function (html) {
					var page = new DOMParser().parseFromString(html, 'text/html');
					var items = $$('[data-grid] > li', page);
					var firstNew = null;
					items.forEach(function (li) {
						var node = doc.importNode(li, true);
						grid.appendChild(node);
						if (!firstNew) { firstNew = node; }
					});
					var nextBtn = $('[data-load-more]', page);
					var count = $('.toolbar__count', page);
					var countEl = $('.toolbar__count');
					if (countEl && (countEl.textContent.match(/\d+/g) || []).length > 1) {
						// "Showing 12 of 24" → "Showing 24 of 24"
						countEl.textContent = countEl.textContent.replace(/\d+/, String($$('[data-grid] > li').length));
					}
					if (nextBtn) {
						btn.setAttribute('href', nextBtn.getAttribute('href'));
						btn.classList.remove('is-loading');
						btn.textContent = T.loadMore || 'Load more';
					} else {
						btn.remove();
					}
					syncWish();
					if (firstNew) { var link = $('.card__title a', firstNew); if (link) { link.focus({ preventScroll: true }); } }
				})
				.catch(function () { window.location = url; });
		});
	}

	/* ---------- Sort select ---------- */
	function initSort() {
		$$('[data-autosubmit]').forEach(function (sel) {
			sel.addEventListener('change', function () { sel.form.submit(); });
		});
	}

	/* ---------- Gallery: thumbs + zoom ---------- */
	function initGallery() {
		var g = $('[data-gallery]');
		if (!g) { return; }
		var main = $('[data-zoom]', g);
		var img = $('.gallery__img', g);
		$$('.gallery__thumb', g).forEach(function (t) {
			t.addEventListener('click', function () {
				$$('.gallery__thumb', g).forEach(function (o) { o.classList.remove('is-active'); o.setAttribute('aria-pressed', 'false'); });
				t.classList.add('is-active'); t.setAttribute('aria-pressed', 'true');
				main.classList.remove('is-zoomed');
				img.classList.add('is-swapping');
				setTimeout(function () {
					img.src = t.getAttribute('data-src');
					var tint = t.getAttribute('data-tint');
					if (tint) { main.style.setProperty('--tint', tint); }
					img.classList.remove('is-swapping');
				}, 150);
			});
		});
		var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
		var setOrigin = function (e) {
			var r = main.getBoundingClientRect();
			main.style.setProperty('--zx', ((e.clientX - r.left) / r.width * 100) + '%');
			main.style.setProperty('--zy', ((e.clientY - r.top) / r.height * 100) + '%');
		};
		if (finePointer) {
			main.addEventListener('mouseenter', function (e) { setOrigin(e); main.classList.add('is-zoomed'); });
			main.addEventListener('mousemove', setOrigin);
			main.addEventListener('mouseleave', function () { main.classList.remove('is-zoomed'); });
		} else {
			main.addEventListener('click', function (e) { setOrigin(e); main.classList.toggle('is-zoomed'); });
		}
	}

	/* ---------- Quantity steppers ---------- */
	function initQuantity(ctx) {
		$$('.quantity', ctx).forEach(function (q) {
			var input = $('input.qty', q);
			if (!input || q.__stepper || input.type === 'hidden') { return; }
			q.__stepper = true;
			var mk = function (dir, label) {
				var b = doc.createElement('button');
				b.type = 'button'; b.className = 'qty-btn'; b.setAttribute('aria-label', label);
				b.innerHTML = dir < 0 ? '&minus;' : '+';
				b.addEventListener('click', function () {
					var min = parseFloat(input.min) || 1, max = parseFloat(input.max) || Infinity;
					var v = Math.min(max, Math.max(min, (parseFloat(input.value) || min) + dir));
					input.value = v;
					input.dispatchEvent(new Event('change', { bubbles: true }));
				});
				return b;
			};
			q.insertBefore(mk(-1, 'Decrease quantity'), input);
			q.appendChild(mk(1, 'Increase quantity'));
		});
	}

	/* ---------- Variation selects → swatch buttons ---------- */
	function initSwatches() {
		$$('.variations_form').forEach(function (form) {
			var selects = $$('.variations select', form);
			if (!selects.length) { return; }
			form.classList.add('has-swatches');
			selects.forEach(function (sel) {
				var wrap = doc.createElement('div');
				wrap.className = 'swatches';
				wrap.setAttribute('role', 'radiogroup');
				var label = form.querySelector('label[for="' + sel.id + '"]');
				if (label) { wrap.setAttribute('aria-label', label.textContent.trim()); }
				sel.parentNode.insertBefore(wrap, sel);
				var build = function () {
					wrap.innerHTML = '';
					$$('option', sel).forEach(function (o) {
						if (!o.value) { return; }
						var b = doc.createElement('button');
						b.type = 'button'; b.className = 'swatch-btn';
						b.setAttribute('role', 'radio');
						b.setAttribute('aria-checked', String(sel.value === o.value));
						b.disabled = o.disabled;
						b.textContent = o.textContent;
						b.addEventListener('click', function () {
							sel.value = sel.value === o.value ? '' : o.value;
							sel.dispatchEvent(new Event('change', { bubbles: true }));
							if (window.jQuery) { window.jQuery(sel).trigger('change'); }
							build();
						});
						wrap.appendChild(b);
					});
				};
				build();
				// WooCommerce rewrites options as attributes are chosen; mirror that.
				new MutationObserver(build).observe(sel, { childList: true, subtree: true, attributes: true });
				sel.addEventListener('change', build);
			});
		});
	}

	/* ---------- Demo product form ---------- */
	function initDemoCart() {
		$$('[data-demo-cart]').forEach(function (form) {
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				var qty = parseInt((form.querySelector('.qty') || {}).value, 10) || 1;
				addToCart(null, qty);
				if (e.submitter && e.submitter.hasAttribute('data-buy-now')) { toast('Demo store — checkout opens once WooCommerce is active.'); }
			});
		});
	}

	/* ---------- Global click delegation ---------- */
	function initDelegation() {
		doc.addEventListener('click', function (e) {
			var wish = e.target.closest('.wish-toggle');
			if (wish) {
				e.preventDefault();
				if (!wish.getAttribute('data-id')) {
					var holder = wish.closest('[data-product]');
					if (holder) { try { wish.setAttribute('data-id', JSON.parse(holder.getAttribute('data-product')).id); } catch (err) { /* noop */ } }
				}
				toggleWish(wish);
				return;
			}
			var qv = e.target.closest('[data-quick-view]');
			if (qv) { e.preventDefault(); openQuickView(cardData(qv), qv); return; }
			var sw = e.target.closest('.qv__sizes .swatch-btn');
			if (sw) {
				$$('.swatch-btn', sw.parentNode).forEach(function (b) { b.setAttribute('aria-checked', String(b === sw)); });
			}
		});
	}

	/* ---------- Boot ---------- */
	function boot() {
		initHeader();
		initAnnouncement();
		initMega();
		initOverlays();
		initAccordions();
		$$('[data-carousel]').forEach(function (c) { if ($('.carousel__track > *', c)) { initCarousel(c); } });
		initDelegation();
		initRecent();
		syncWish();
		initLoadMore();
		initSort();
		initGallery();
		initQuantity(doc);
		initSwatches();
		initDemoCart();
		// WooCommerce re-renders the mini cart; re-apply steppers there.
		if (window.jQuery) { window.jQuery(doc.body).on('wc_fragments_refreshed wc_fragments_loaded', function () { initQuantity(doc); }); }
		window.addEventListener('storage', function (e) { if (e.key === WKEY) { syncWish(); } });
	}

	if (doc.readyState === 'loading') { doc.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
