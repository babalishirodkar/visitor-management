# GateDesk – Visitor Management & Gate Pass

An existing PHP 8+ / Bootstrap 5.3.8 prototype, with a shared responsive interface across 19 modules and sign-in. All runtime fonts, icons, styles and scripts are bundled locally. No Node process or internet connection is needed to run the app.

## Run

- WAMP: open `http://localhost/visitor-management/`.
- Standalone: run `php -S localhost:8000` from this directory, then open `http://localhost:8000/`.
- Demo sign-in is prefilled. Use the profile menu to preview the 10 existing roles.

URLs are derived from the application mount path, supporting a document root or a subfolder. Keep the existing PHP session configuration enabled.

## Shared interface

- `assets/css/app.css`: design tokens, shell, tables, forms, module components, responsive layouts, print rules and reduced-motion support.
- `assets/js/app.js`: composed filters, accessible validation, workspace search (`Ctrl/Cmd+K`), on-demand tooltips and finite entrance motion.
- `includes/header.php`, `footer.php`, `headerscript.php`, `footerscript.php`: common navigation, head, dialogs and scripts.
- `includes/helpers.php`: reusable page headings, statistics, modal and table helpers, asset versioning and existing session operations.
- `includes/site-illustration.php`: local SVG artwork used by the dashboard and sign-in.
- `assets/js/items.js`: repeating material item controls, loaded only by the two material lists.

Chart.js loads only on the dashboard and reports list. QR support loads only on badge previews. Keep `$needsCharts`, `$needsQR` and `$needsItems` set on pages that need those tools.

## Static asset optimization

Source CSS and JavaScript remain editable. After changing them, optionally refresh the precompressed copies:

```sh
node tools/compress-assets.cjs
```

This uses only Node's built-in modules. `versioned_asset()` selects a current `.gz` copy only for an Apache client accepting gzip. A newer source file, missing compressed copy, declined gzip encoding, or a different server automatically uses the original asset. URLs include the source modification time for cache invalidation.

`assets/.htaccess` supplies the compressed MIME types/encoding without requiring `mod_deflate`. Cache lifetimes and dynamic compression are also configured when the corresponding optional Apache modules are enabled. PHP session pages retain their existing cache behavior. On other web servers, the source assets work directly; compression can be configured by the host.

## Browser checks

Test dependencies are isolated from the application in `.tools/`:

```sh
npm.cmd install --prefix .tools --no-save --package-lock=false --ignore-scripts playwright-core @axe-core/playwright
node tests/browser-audit.cjs
node tests/workflows.cjs
node tests/performance.cjs
```

Set `CHROME_PATH` to a local Chromium executable if it differs from the script default. Set `TEST_URL` to test another mount (default: `http://localhost/visitor-management/`). A running PHP/Apache server is required. `npm.cmd` avoids PowerShell execution-policy restrictions on `npm.ps1`.

- `browser-audit.cjs` checks all 38 module routes at 1440, 768, 390 and 320 pixels, plus sign-in. It checks overflow, PHP/runtime/network errors, accessible form names and axe WCAG A/AA rules, and writes screenshots/results to `.tools/audit/`.
- `workflows.cjs` exercises authentication, appointment create/edit/check-in, role guards, search, composed filters, material items, uploads, validation, tabs, mobile navigation, reduced motion, printing and fallback behavior. Test records exist only in its isolated PHP session.
- `performance.cjs` records local mobile lab samples with cold cache, 150 ms latency, 1.6 Mbps download and 4× CPU throttling. It does not produce a Lighthouse score or field Core Web Vitals.

## Existing prototype boundaries

Mock data lives in `includes/data.php`; appointments persist in the PHP session. The database settings are placeholders and there is no connected database or external API to optimize. Most other save/import/export/notification operations remain prototype feedback, and preview deletion only removes a displayed row. File-selection controls remain functional, but they are not a new upload backend. QR badges retain the existing sample verification payloads.

This upgrade preserves those boundaries and existing routes. It does not implement production authentication, persistent CRUD for every module, email delivery, document storage, or live gate integrations. See [UPGRADE-NOTES.md](UPGRADE-NOTES.md) for validation results.
