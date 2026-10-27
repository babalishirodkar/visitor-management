/* Verify local font bytes and exact asset paths on WAMP or a supplied hosted URL. */
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require('../.tools/node_modules/playwright-core');
const root = path.resolve(__dirname, '..');
const base = process.env.TEST_URL || 'http://localhost/visitor-management/';
const fontPath = 'assets/vendor/icons/fonts/bootstrap-icons.woff2';
const localFont = fs.readFileSync(path.join(root, fontPath));
const iconCss = fs.readFileSync(path.join(root, 'assets/vendor/icons/bootstrap-icons.min.css'), 'utf8');
const woff2Path = /url\(["']?([^"')]*bootstrap-icons\.woff2[^"')]*)/.exec(iconCss)?.[1];
assert.equal(localFont.subarray(0, 4).toString(), 'wOF2', 'Local WOFF2 signature is valid');
assert.ok(woff2Path, 'Icon stylesheet has a WOFF2 URL');

(async () => {
  const browser = await chromium.launch({ headless: true, executablePath: process.env.CHROME_PATH || path.join(process.env.LOCALAPPDATA, 'ms-playwright/chromium-1223/chrome-win64/chrome.exe') });
  try {
    const context = await browser.newContext();
    const page = await context.newPage();
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    page.on('console', message => {
      if (message.type() === 'error' || /preloaded.*not used/i.test(message.text())) errors.push(message.text());
    });
    await page.goto(base + 'login.php');
    await page.evaluate(() => document.fonts.ready);
    const result = await page.evaluate(() => {
      const entries = performance.getEntriesByType('resource');
      return {
        loaded: document.fonts.check('16px bootstrap-icons'),
        fonts: entries.filter(e => /bootstrap-icons\.woff2?$/.test(new URL(e.name).pathname)).map(e => ({ name: e.name, start: e.startTime, transfer: e.transferSize })),
        preload: document.querySelector('link[rel="preload"][href*="bootstrap-icons.woff2"]')?.href,
        stylesheet: document.querySelector('link[href*="bootstrap-icons.min.css"]')?.href,
      };
    });
    assert.ok(result.loaded, 'Icon font is ready');
    assert.equal(result.fonts.length, 1, 'Font preload is reused; no duplicate or fallback font request');
    assert.ok(new URL(result.fonts[0].name).pathname.endsWith('.woff2'), 'Browser uses WOFF2');
    assert.ok(new URL(result.preload).pathname.endsWith(woff2Path), 'Preload path matches icon CSS');
    const delivered = await context.request.get(base + fontPath);
    assert.equal(delivered.status(), 200, 'Font request succeeds');
    const deliveredBytes = await delivered.body();
    assert.ok(deliveredBytes.length > 100000, 'Font response is nonempty');
    assert.equal(deliveredBytes.subarray(0, 4).toString(), 'wOF2', 'Response has a valid WOFF2 signature');
    assert.deepEqual(deliveredBytes, localFont, 'Served font bytes match the project font');
    await page.reload();
    await page.evaluate(() => document.fonts.ready);
    const warm = await page.evaluate(() => performance.getEntriesByType('resource').filter(e => /bootstrap-icons\.woff2?$/.test(new URL(e.name).pathname)).map(e => ({ name: e.name, transfer: e.transferSize })));
    assert.equal(warm.length, 1);
    assert.equal(warm[0].transfer, 0, 'Browser memory cache serves icons on repeat navigation');
    assert.deepEqual(errors, [], 'No font decode, unused preload or runtime errors');
    console.log(JSON.stringify({ fontBytes: deliveredBytes.length, iconLoaded: result.loaded, preload: result.preload, fonts: result.fonts, repeat: warm, warnings: errors }, null, 2));
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
