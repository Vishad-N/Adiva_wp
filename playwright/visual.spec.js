// @ts-check
/**
 * ADiva visual + layout validation.
 *
 * For the homepage, a collection page and a product page, at 375 / 768 /
 * 1280 / 1440 / 1920px it asserts:
 *   - no horizontal page overflow (scrollWidth <= innerWidth, and <= clientWidth)
 *   - header, footer, carousels, product cards render and are visible
 *   - carousels and grids stay inside the viewport
 *   - buttons/badges don't overflow their own boxes
 * and saves a full-page screenshot per page × viewport for review.
 *
 * Interaction tests cover the mega menu, mobile menu, quick view, carousel
 * arrows, load more, wishlist and the filter drawer.
 */
const fs = require('fs');
const path = require('path');
const { test, expect } = require('@playwright/test');

const SHOTS = path.join(__dirname, 'screenshots');
fs.mkdirSync(SHOTS, { recursive: true });

const VIEWPORTS = [
	{ name: 'mobile-375', width: 375, height: 812 },
	{ name: 'tablet-768', width: 768, height: 1024 },
	{ name: 'laptop-1280', width: 1280, height: 800 },
	{ name: 'desktop-1440', width: 1440, height: 900 },
	{ name: 'wide-1920', width: 1920, height: 1080 },
];

const PAGES = [
	{ name: 'home', path: '/', key: ['.hero', '.carousel__track .card', '.marquee__track'] },
	{ name: 'shop', path: process.env.ADIVA_SHOP_PATH || '/shop/', key: ['.collection-title', '.toolbar', '.product-grid .card'] },
	{ name: 'product', path: process.env.ADIVA_PRODUCT_PATH || '/product/marigold-zardozi-tote/', key: ['.gallery__main', '.product-title', '.single_add_to_cart_button'] },
];

/** Settle the page: fonts loaded, lazy images triggered, back to top. */
async function settle(page) {
	await page.waitForLoadState('load');
	await page.evaluate(async () => {
		await document.fonts.ready;
		// The theme uses smooth scrolling; jump instantly so screenshots start at the top.
		const step = window.innerHeight * 0.8;
		for (let y = 0; y < document.body.scrollHeight; y += step) {
			window.scrollTo({ top: y, behavior: 'instant' });
			await new Promise((r) => setTimeout(r, 30));
		}
		window.scrollTo({ top: 0, behavior: 'instant' });
		await new Promise((r) => setTimeout(r, 150));
	});
}

/** Overflow measurements plus the worst offending elements, for debugging. */
async function measureOverflow(page) {
	return page.evaluate(() => {
		const de = document.documentElement;
		const vw = de.clientWidth;
		const clipped = (el) => {
			for (let p = el.parentElement; p && p !== document.body; p = p.parentElement) {
				const s = getComputedStyle(p);
				if (s.overflowX !== 'visible' || s.position === 'fixed') return true;
			}
			return false;
		};
		const label = (el, r) => `${el.tagName.toLowerCase()}.${String(el.className).trim().split(/\s+/).slice(0, 2).join('.')} [${Math.round(r.left)}→${Math.round(r.right)}]`;
		const offenders = [];
		const outside = [];
		for (const el of document.querySelectorAll('body *')) {
			const r = el.getBoundingClientRect();
			if (!r.width || getComputedStyle(el).position === 'fixed') continue;
			if (r.right > vw + 1 || r.left < -1) {
				outside.push(label(el, r));
				if (!clipped(el)) offenders.push(label(el, r));
			}
		}
		// Absolutely positioned children can escape a scroller; fall back to the raw list.
		return { scrollWidth: de.scrollWidth, innerWidth: window.innerWidth, clientWidth: vw, offenders: (offenders.length ? offenders : outside).slice(0, 8) };
	});
}

async function expectNoOverflow(page, label) {
	const m = await measureOverflow(page);
	expect(m.scrollWidth, `${label}: page wider than viewport. Offenders: ${m.offenders.join(', ')}`).toBeLessThanOrEqual(m.innerWidth);
	expect(m.scrollWidth, `${label}: horizontal scroll present. Offenders: ${m.offenders.join(', ')}`).toBeLessThanOrEqual(m.clientWidth);
}

for (const vp of VIEWPORTS) {
	test.describe(`@${vp.name}`, () => {
		test.use({ viewport: { width: vp.width, height: vp.height } });

		for (const pg of PAGES) {
			test(`${pg.name} renders without overflow`, async ({ page }) => {
				const errors = [];
				page.on('pageerror', (e) => errors.push(e.message));

				const res = await page.goto(pg.path);
				expect(res?.status(), 'HTTP status').toBeLessThan(400);
				await settle(page);

				await expectNoOverflow(page, `${pg.name}@${vp.width}`);

				// Global chrome.
				await expect(page.locator('#site-header')).toBeVisible();
				await expect(page.locator('.site-footer')).toBeVisible();
				await expect(page.locator('.wordmark, .custom-logo-link').first()).toBeVisible();
				if (vp.width >= 1100) {
					await expect(page.locator('.primary-nav')).toBeVisible();
					await expect(page.locator('.menu-btn')).toBeHidden();
				} else {
					await expect(page.locator('.menu-btn')).toBeVisible();
				}

				// Page-specific key elements.
				for (const sel of pg.key) {
					await expect(page.locator(sel).first(), `${sel} visible`).toBeVisible();
				}

				// Carousels and grids stay inside the viewport.
				const boxes = await page.evaluate(() => {
					const vw = document.documentElement.clientWidth;
					return [...document.querySelectorAll('.carousel__track, .product-grid, .tiles, .trust__list, .site-footer__main')]
						.filter((el) => el.offsetParent !== null)
						.map((el) => { const r = el.getBoundingClientRect(); return { cls: el.className, left: r.left, right: r.right, vw }; });
				});
				for (const b of boxes) {
					expect(b.left, `${b.cls} left edge`).toBeGreaterThanOrEqual(-1);
					expect(b.right, `${b.cls} right edge`).toBeLessThanOrEqual(b.vw + 1);
				}

				// Product cards: image box has a real size and the title is present.
				const cards = page.locator('.card');
				expect(await cards.count()).toBeGreaterThan(0);
				const firstCard = cards.first();
				await firstCard.scrollIntoViewIfNeeded();
				const media = await firstCard.locator('.card__media').boundingBox();
				expect(media?.width ?? 0).toBeGreaterThan(100);
				expect(media?.height ?? 0).toBeGreaterThan((media?.width ?? 0) * 1.1); // 4:5 portrait
				await expect(firstCard.locator('.card__title')).not.toBeEmpty();

				// Buttons, badges and nav links never overflow their own boxes.
				const textOverflow = await page.evaluate(() =>
					[...document.querySelectorAll('.button, .tag, .card__quick, .primary-nav__link, .link-arrow, .payments li')]
						.filter((el) => el.offsetParent !== null && el.scrollWidth > el.clientWidth + 1)
						.map((el) => `${el.className}: "${el.textContent.trim().slice(0, 30)}"`)
				);
				expect(textOverflow, 'text overflowing its container').toEqual([]);

				await page.screenshot({ path: path.join(SHOTS, `${pg.name}-${vp.name}.png`), fullPage: true });
				expect(errors, 'JavaScript errors').toEqual([]);
			});
		}
	});
}

test.describe('interactions', () => {
	test('desktop mega menu opens inside the viewport @1280', async ({ page }) => {
		await page.setViewportSize({ width: 1280, height: 800 });
		await page.goto('/');
		const item = page.locator('.primary-nav__item.has-mega', { hasText: 'Handbags' });
		await item.hover();
		const mega = item.locator('.mega');
		await expect(mega).toBeVisible();
		await page.waitForTimeout(350);
		const box = await mega.boundingBox();
		expect(box.x).toBeGreaterThanOrEqual(-1);
		expect(box.x + box.width).toBeLessThanOrEqual(1281);
		await expect(mega.locator('a', { hasText: 'Beach Bags' })).toBeVisible();
		await expectNoOverflow(page, 'mega menu open');
		await page.screenshot({ path: path.join(SHOTS, 'interaction-mega-1280.png') });

		// Keyboard: toggle button opens and Escape closes.
		await page.mouse.move(5, 700);
		const toggle = page.locator('.primary-nav__item.has-mega', { hasText: 'Clothing' }).locator('.mega-toggle');
		await toggle.focus();
		await page.keyboard.press('Enter');
		await expect(toggle).toHaveAttribute('aria-expanded', 'true');
		await page.keyboard.press('Escape');
		await expect(toggle).toHaveAttribute('aria-expanded', 'false');
	});

	test('mega menu fits at a small desktop width @1100', async ({ page }) => {
		await page.setViewportSize({ width: 1100, height: 760 });
		await page.goto('/');
		const nav = await page.locator('.primary-nav__list').boundingBox();
		expect(nav.x + nav.width).toBeLessThanOrEqual(1100);
		// All top-level items on one row.
		const tops = await page.locator('.primary-nav__link').evaluateAll((els) => new Set(els.map((e) => Math.round(e.getBoundingClientRect().top))).size);
		expect(tops).toBe(1);
		await expectNoOverflow(page, 'nav@1100');
	});

	test('mobile menu drawer with accordion @375', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto('/');
		await page.locator('.menu-btn').click();
		const drawer = page.locator('#mobile-drawer');
		await expect(drawer).toBeVisible();
		await page.waitForTimeout(400);
		const toggle = drawer.locator('.mobile-nav__toggle', { hasText: 'Handbags' });
		await toggle.click();
		await expect(drawer.locator('.mobile-nav__sub a', { hasText: 'Totes' })).toBeVisible();
		const box = await drawer.boundingBox();
		expect(box.x).toBeGreaterThanOrEqual(-1);
		expect(box.x + box.width).toBeLessThanOrEqual(376);
		await expectNoOverflow(page, 'mobile drawer');
		await page.screenshot({ path: path.join(SHOTS, 'interaction-mobile-menu-375.png') });
		await page.keyboard.press('Escape');
		await expect(drawer).toBeHidden();
	});

	for (const width of [375, 1280]) {
		test(`quick view modal @${width}`, async ({ page }) => {
			await page.setViewportSize({ width, height: width < 768 ? 812 : 800 });
			await page.goto('/shop/');
			const card = page.locator('.product-grid .card').first();
			await card.hover();
			await card.locator('[data-quick-view]').click();
			const modal = page.locator('#quick-view');
			await expect(modal).toBeVisible();
			await expect(modal.locator('.qv__title')).not.toBeEmpty();
			await page.waitForTimeout(350);
			const panel = await modal.locator('.modal__panel').boundingBox();
			expect(panel.x).toBeGreaterThanOrEqual(0);
			expect(panel.x + panel.width).toBeLessThanOrEqual(width);
			await expectNoOverflow(page, `quick view@${width}`);
			await page.screenshot({ path: path.join(SHOTS, `interaction-quickview-${width}.png`) });
			await modal.locator('.qv__add').click();
			await expect(page.locator('.cart-count').first()).toHaveText('1');
			await page.keyboard.press('Escape');
			await expect(modal).toBeHidden();
		});
	}

	test('carousel arrows scroll the track @1440', async ({ page }) => {
		await page.setViewportSize({ width: 1440, height: 900 });
		await page.goto('/');
		const carousel = page.locator('[data-carousel]').first();
		const track = carousel.locator('.carousel__track');
		await carousel.scrollIntoViewIfNeeded();
		await expect(carousel.locator('[data-prev]')).toBeDisabled();
		await carousel.locator('[data-next]').click();
		await expect.poll(() => track.evaluate((t) => t.scrollLeft)).toBeGreaterThan(100);
		await expect(carousel.locator('[data-prev]')).toBeEnabled();
		expect(await carousel.locator('.carousel__dot').count()).toBeGreaterThan(1);
	});

	test('load more appends the next page @1280', async ({ page }) => {
		await page.setViewportSize({ width: 1280, height: 800 });
		await page.goto('/shop/');
		const items = page.locator('[data-grid] > li');
		const before = await items.count();
		const more = page.locator('[data-load-more]');
		await expect(more).toBeVisible();
		await more.click();
		await expect.poll(() => items.count()).toBeGreaterThan(before);
		await expectNoOverflow(page, 'after load more');
	});

	test('wishlist toggle updates the header count', async ({ page }) => {
		await page.setViewportSize({ width: 1280, height: 800 });
		await page.goto('/shop/');
		const heart = page.locator('.product-grid .card__wish').first();
		await heart.click();
		await expect(heart).toHaveAttribute('aria-pressed', 'true');
		await expect(page.locator('.wishlist-count')).toHaveText('1');
		await page.goto('/wishlist/');
		await expect(page.locator('[data-wishlist-grid] .card')).toHaveCount(1);
	});

	test('filter drawer and sort @375', async ({ page }) => {
		await page.setViewportSize({ width: 375, height: 812 });
		await page.goto('/shop/');
		await page.locator('.toolbar__filter').click();
		const drawer = page.locator('#filter-drawer');
		await expect(drawer).toBeVisible();
		await page.waitForTimeout(400);
		await expectNoOverflow(page, 'filter drawer');
		await page.screenshot({ path: path.join(SHOTS, 'interaction-filters-375.png') });
		await page.keyboard.press('Escape');
		await page.selectOption('#orderby', 'price');
		await page.waitForURL(/orderby=price/);
		const prices = await page.locator('.product-grid .card').evaluateAll((cards) => cards.map((c) => JSON.parse(c.dataset.product).price.replace(/<[^>]+>/g, '')));
		expect(prices.length).toBeGreaterThan(0);
	});
});
