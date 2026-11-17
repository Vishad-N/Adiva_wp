// @ts-check
/**
 * Playwright config for ADiva layout validation.
 *
 * By default it starts the local preview (PHP built-in server rendering the
 * real theme templates with demo products) on port 8089. To test a real
 * WordPress + WooCommerce site instead:
 *
 *   ADIVA_BASE_URL=https://staging.example.com \
 *   ADIVA_PRODUCT_PATH=/product/your-product/ \
 *   npx playwright test -c playwright
 */
const path = require('path');
const { defineConfig, devices } = require('@playwright/test');

const external = !!process.env.ADIVA_BASE_URL;
const baseURL = process.env.ADIVA_BASE_URL || 'http://127.0.0.1:8089';
const themeDir = path.resolve(__dirname, '..');

module.exports = defineConfig({
	testDir: __dirname,
	testMatch: /.*\.spec\.js/,
	outputDir: path.join(__dirname, 'test-results'),
	timeout: 60_000,
	fullyParallel: true,
	retries: 0,
	reporter: [['list'], ['html', { open: 'never', outputFolder: path.join(__dirname, 'report') }]],
	use: {
		baseURL,
		...devices['Desktop Chrome'],
		trace: 'retain-on-failure',
		// Optional: reuse an already-installed Chromium instead of `npx playwright install`.
		launchOptions: process.env.PW_CHROMIUM_PATH ? { executablePath: process.env.PW_CHROMIUM_PATH } : {},
	},
	webServer: external
		? undefined
		: {
				command: 'php -S 127.0.0.1:8089 -t . dev-preview/router.php',
				cwd: themeDir,
				url: 'http://127.0.0.1:8089/',
				reuseExistingServer: true,
				timeout: 30_000,
			},
});
