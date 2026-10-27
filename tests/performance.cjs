/* Repeatable local lab sample, not a Lighthouse score or field Core Web Vitals. */
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require('../.tools/node_modules/playwright-core');
const base = process.env.TEST_URL || 'http://localhost/visitor-management/';
(async () => {
  const browser = await chromium.launch({ headless: true, executablePath: process.env.CHROME_PATH || path.join(process.env.LOCALAPPDATA, 'ms-playwright/chromium-1223/chrome-win64/chrome.exe') });
  const results = [];
  try {
    const context = await browser.newContext({ viewport: { width: 390, height: 844 }, isMobile: true, deviceScaleFactor: 1 });
    const page = await context.newPage();
    await page.goto(base + 'login.php');
    await page.getByRole('button', { name: 'Sign in to Portal' }).click();
    await page.waitForURL('**/appointments-list.php');
    await page.addInitScript(() => {
      window.labMetrics = { lcp: 0, cls: 0, longTasks: 0 };
      new PerformanceObserver(list => list.getEntries().forEach(e => { window.labMetrics.lcp = e.startTime; })).observe({ type: 'largest-contentful-paint', buffered: true });
      new PerformanceObserver(list => list.getEntries().forEach(e => { if (!e.hadRecentInput) window.labMetrics.cls += e.value; })).observe({ type: 'layout-shift', buffered: true });
      new PerformanceObserver(list => { window.labMetrics.longTasks += list.getEntries().length; }).observe({ type: 'longtask', buffered: true });
    });
    const cdp = await context.newCDPSession(page);
    await cdp.send('Network.enable');
    await cdp.send('Network.setCacheDisabled', { cacheDisabled: true });
    await cdp.send('Network.emulateNetworkConditions', { offline: false, latency: 150, downloadThroughput: 200000, uploadThroughput: 93750 });
    await cdp.send('Emulation.setCPUThrottlingRate', { rate: 4 });
    for (const module of ['appointments', 'dashboard', 'visitors']) {
      await page.goto(`${base}pages/${module}/${module}-list.php`);
      await page.evaluate(() => document.fonts.ready);
      await page.waitForTimeout(1500);
      results.push(await page.evaluate(module => ({
        module,
        ...window.labMetrics,
        requestCount: performance.getEntriesByType('resource').length,
        transferBytes: performance.getEntriesByType('resource').reduce((total, resource) => total + resource.transferSize, 0),
        externalRequests: performance.getEntriesByType('resource').filter(r => !r.name.startsWith(location.origin)).map(r => r.name),
        scripts: performance.getEntriesByType('resource').filter(r => r.initiatorType === 'script').map(r => r.name.split('/').pop()),
      }), module));
    }
    fs.mkdirSync('.tools/audit', { recursive: true });
    fs.writeFileSync('.tools/audit/performance.json', JSON.stringify({ conditions: 'Chromium; 390x844; cold cache; 150ms latency; 1.6Mbps download; 4x CPU slowdown; one run per page', results }, null, 2));
    console.log(JSON.stringify(results, null, 2));
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
