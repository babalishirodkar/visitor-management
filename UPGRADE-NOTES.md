# Interface and performance upgrade — 26 October 2026

The existing PHP application and routes are retained. The shared layout now gives all 38 module pages and sign-in the same navy/teal palette, local IBM Plex typography, readable tables, consistent controls and responsive spacing. Dashboard and sign-in use a native SVG site illustration; generic record pages have a separate contextual information panel on desktop.

Motion is finite and uses opacity/transforms. Reduced-motion preferences disable CSS and chart animation. Navigation, forms, tabs, dropdowns, dialogs, print layouts and access-restricted states use the shared system.

## Interaction fixes

- Text, column and site filters compose rather than replacing each other's results. A result count, clear action and empty state appear for searchable tables.
- Visitor category filtering uses the category column. Returnable filtering no longer matches non-returnable items.
- The header opens a keyboard-accessible workspace switcher restricted to the current role's modules. It is navigation search, not a new database-wide record API.
- Required-field feedback focuses the first invalid control. Existing explicit form POSTs keep their original actions and submitted values. Inline prototype forms now show their intended feedback instead of silently reloading.
- Repeating material rows load correctly, reset cloned values, avoid duplicate IDs and focus the new input. Legacy pass links point to the existing pass module.
- Quoted visitor names work in QR generation and preview deletion.
- Appointment authentication and role checks run before session writes; anonymous and auditor submissions are rejected.

## Performance work

- Removed competing style/animation override layers and per-control click-ripple handlers.
- Replaced remote font requests with existing local WOFF2 assets.
- Cached table text, batched filter updates, consolidated event delegation and initialized tooltips on demand.
- Removed unnecessary chart/QR loads from form and list pages. Appointment summary counts use one pass without allocating temporary filtered arrays.
- Precompressed eight CSS/JavaScript assets: **728,326 → 165,943 bytes**, a **77% reduction** across those artifacts. Source files and safe fallbacks remain available.
- Application URLs now work under both WAMP's subfolder and PHP's standalone document root.

Local mobile lab measurements, using the same cold-cache conditions before and after adding precompressed delivery:

| Page | LCP before compression | LCP after compression | Static resource transfer, before → after | Measured layout shift after |
| --- | ---: | ---: | ---: | ---: |
| Appointments | 3.95 s | 2.17 s | 689,493 → 313,037 bytes | 0.000094 |
| Dashboard | 4.55 s | 1.87 s | 899,871 → 384,440 bytes | 0.000012 |
| Visitors | 3.69 s | 1.65 s | 689,493 → 313,037 bytes | 0.000221 |

Conditions: Chromium, 390×844 viewport, cache disabled, 150 ms latency, 1.6 Mbps download, 4× CPU slowdown, one run per page. These compare the upgraded interface with and without compressed delivery, not the original project's complete performance. Transfer totals cover resource entries rather than the main HTML document. These are lab samples, not Lighthouse scores or production percentile metrics.

## Verification

- **51 PHP files** passed syntax checks; both application scripts passed Node syntax checks.
- **456 module route/viewport combinations** passed at 320, 360, 375, 390, 430, 480, 600, 768, 1024, 1280, 1440 and 1920 pixels, with no page overflow, PHP errors, runtime errors, failed asset requests or external asset requests. Short landscape checks also passed.
- Automated axe WCAG A/AA checks passed on sign-in and all module routes; additional checks covered workspace search, material dialog, appointment detail and mobile navigation.
- **48 workflow checks** passed, including appointment persistence, quoted names, authentication and role guards, validation, combined filters, material rows, file selection, mobile controls, reduced motion, printing, unavailable sessionStorage and main-content visibility without JavaScript.
- The icon font loaded from one preloaded 134,012-byte WOFF2 request; repeat use was served from memory cache, with no browser warnings.
- Apache responses for precompressed CSS/JS were verified for correct content types and `Content-Encoding: gzip`.

Browser screenshots and machine-readable reports are in `.tools/audit/`. The repeatable checks are in `tests/`.

## Hosted icon-loading finding

Browser inspection of `https://visitor-mangement.free.nf` found that its Bootstrap Icons WOFF2 request returned HTTP 200 with a **0-byte body** and `Content-Type: text/plain`. Chrome reported a font decode error and fetched the fallback WOFF file (180,288 bytes). Font responses had `Cache-Control: max-age=0`. Sampled HTML TTFB was 212–226 ms, so the icon delay came from the broken font response and fallback, not sampled PHP response latency. This project's valid local WOFF2 is 134,012 bytes.

The source now uses `font-display:swap`, preloads the exact query-free WOFF2 URL, and includes optional Apache MIME and font-cache rules. `.tools/free-infinity-fix.zip` contains the matching CSS, correct icon WOFF2, page head, `.htaccess`, and IBM Plex fonts. **Upload the archive's files, purge host/CDN cache, and check the icon-font response before considering the hosted repair deployed.** The site's response stayed empty during our inspection. The existing server URL was inaccessible to the browsing integration; direct host inspection was performed with Chromium.

## Checks outside this environment

No production Lighthouse score, real-device Safari/Firefox test, screen-reader session, production database/API profile or live email/upload integration test was performed. Axe checks supplement manual review; they do not certify complete accessibility. The existing mock-data/session-backed prototype behavior remains as described in the README. Optional Apache expiry/header/deflate modules are disabled on this WAMP host; precompressed delivery works without them.
