/* Run with node tests/browser-audit.cjs. See README for the isolated test dependencies. */
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require('../.tools/node_modules/playwright-core');
const { default: AxeBuilder } = require('../.tools/node_modules/@axe-core/playwright');
const root = path.resolve(__dirname, '..');
const base = process.env.TEST_URL || 'http://localhost/visitor-management/';
const browserPath = process.env.CHROME_PATH || path.join(process.env.LOCALAPPDATA, 'ms-playwright/chromium-1223/chrome-win64/chrome.exe');
const out = path.join(root, '.tools', 'audit');
fs.mkdirSync(out, { recursive: true });
const routes = fs.readdirSync(path.join(root, 'pages')).flatMap(module => fs.readdirSync(path.join(root, 'pages', module)).filter(f => f.endsWith('.php')).map(f => `pages/${module}/${f}`));
const failures = [], screens = [], errors = [], requests = new Set();
(async () => {
  const browser = await chromium.launch({ headless: true, executablePath: browserPath });
  try {
    const context = await browser.newContext({ viewport: { width: 1440, height: 1000 }, reducedMotion: 'reduce' });
    const page = await context.newPage();
    page.on('pageerror', e => errors.push({ url: page.url(), message: e.message }));
    page.on('response', response => { if (response.status() >= 400) errors.push({ url: response.url(), status: response.status() }); });
    page.on('request', request => { if (/^https?:/.test(request.url()) && !request.url().startsWith(base)) requests.add(request.url()); });
    await page.goto(base + 'login.php');
    await page.evaluate(() => document.fonts.ready);
    await page.screenshot({ path: path.join(out, 'login-desktop.png'), fullPage: true });
    const loginAxe = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21aa']).analyze();
    if (loginAxe.violations.length) failures.push({ route: 'login', violations: loginAxe.violations });
    await page.getByRole('button', { name: 'Sign in to Portal' }).click();
    await page.waitForURL('**/appointments-list.php');
    const widths = [320, 360, 375, 390, 430, 480, 600, 768, 1024, 1280, 1440, 1920];
    for (const width of widths) {
      await page.setViewportSize({ width, height: 1000 });
      let completed = 0;
      for (const route of routes) {
        const response = await page.goto(base + route);
        await page.evaluate(() => document.fonts.ready);
        const data = await page.evaluate(() => ({
          title: document.title,
          heading: document.querySelector('h1')?.textContent,
          overflow: document.documentElement.scrollWidth > innerWidth + 1,
          overflowing: Array.from(document.querySelectorAll('main *')).filter(el => {
            const rect = el.getBoundingClientRect();
            return rect.width && rect.right > innerWidth + 2 && !el.closest('.table-responsive, .nav-tabs, .command-hero-visual, .modal, .dropdown-menu');
          }).slice(0, 5).map(el => ({ tag: el.tagName, cls: el.className })),
          phpError: /(?:Warning|Fatal error|Parse error|Deprecated):/.test(document.body.innerText),
          unnamed: Array.from(document.querySelectorAll('main input:not([type=hidden]),main select,main textarea')).filter(el => !el.labels?.length && !el.getAttribute('aria-label') && !el.getAttribute('aria-labelledby')).length,
          fonts: document.fonts.check('14px "IBM Plex Sans"'),
          assets: performance.getEntriesByType('resource').filter(r => r.initiatorType === 'script').map(r => r.name.split('/').pop()),
        }));
        if (response.status() >= 400 || data.overflow || data.phpError || !data.heading || data.unnamed) failures.push({ route, width, status: response.status(), ...data });
        if (width === 1440) {
          const axe = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21aa']).analyze();
          if (axe.violations.length) failures.push({ route, width, violations: axe.violations });
        }
        screens.push({ route, width, ...data });
        if ([1440, 390].includes(width) && ['dashboard', 'appointments', 'visitors', 'gate', 'reports', 'passes', 'organization', 'material-outward'].some(module => route === `pages/${module}/${module}-list.php`)) {
          await page.screenshot({ path: path.join(out, `${route.split('/')[1]}-${width}.png`), fullPage: true });
        }
        if (++completed % 10 === 0) console.log(`${width}px: ${completed}/${routes.length} routes checked`);
      }
      if ([390, 768, 1024].includes(width)) {
        await page.setViewportSize({ width, height: 390 });
        const landscape = await page.evaluate(() => ({ overflow: document.documentElement.scrollWidth > innerWidth + 1, shortViewport: innerHeight <= 520 }));
        if (landscape.overflow) failures.push({ route: 'pages/dashboard/dashboard-list.php', width, orientation: 'landscape', ...landscape });
      }
      console.log(`${width}px: all ${routes.length} routes checked`);
    }
    const result = { screens: screens.length, errors, externalRequests: [...requests], failures, details: screens };
    fs.writeFileSync(path.join(out, 'results.json'), JSON.stringify(result, null, 2));
    console.log(JSON.stringify({ screens: screens.length, errors, externalRequests: [...requests], failures: failures.map(f => ({ route: f.route, width: f.width, overflow: f.overflow, violations: f.violations?.map(v => ({ id: v.id, count: v.nodes.length, targets: v.nodes.slice(0, 4).map(n => n.target) })) })) }, null, 2));
    process.exitCode = failures.length || errors.length ? 1 : 0;
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
