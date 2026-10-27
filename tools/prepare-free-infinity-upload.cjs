/* Build .tools/free-infinity-fix.zip. Run: node tools/prepare-free-infinity-upload.cjs */
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const root = path.resolve(__dirname, '..');
const staging = path.join(root, '.tools', 'free-infinity-fix');
fs.rmSync(staging, { recursive: true, force: true });
for (const relative of [
  'includes/headerscript.php',
  'assets/.htaccess',
  'assets/css/app.css',
  'assets/css/app.css.gz',
  'assets/js/app.js',
  'assets/js/app.js.gz',
  'assets/vendor/icons/bootstrap-icons.min.css',
  'assets/vendor/icons/fonts/bootstrap-icons.woff2',
  'assets/vendor/fonts/ibm-plex-sans-latin-400-normal.woff2',
  'assets/vendor/fonts/ibm-plex-sans-latin-500-normal.woff2',
  'assets/vendor/fonts/ibm-plex-sans-latin-600-normal.woff2',
  'assets/vendor/fonts/ibm-plex-sans-latin-700-normal.woff2',
]) {
  const destination = path.join(staging, relative);
  fs.mkdirSync(path.dirname(destination), { recursive: true });
  fs.copyFileSync(path.join(root, relative), destination);
}
const notes = `GateDesk icon-font and mobile delivery repair
Generated ${new Date().toISOString().slice(0, 10)}

After deploying code changes, upload the listed files, preserving their relative paths and replacing existing files. Use InfinityFree File Manager or FTP. Keep the assets directory structure intact.

Critical fix: assets/vendor/icons/fonts/bootstrap-icons.woff2
Expected size: ${fs.statSync(path.join(staging, 'assets/vendor/icons/fonts/bootstrap-icons.woff2')).size} bytes; valid WOFF2 signature; previously hosted response was empty and Chromium fell back to a large 180 KB WOFF font.

The matching includes/headerscript.php preloads the same query-free WOFF2 URL used by icon CSS. This avoids duplicate requests if the host strips query strings. CSS uses font-display:swap, so text/icons appear immediately. The replacement assets/.htaccess sets font MIME types and one-year browser caching when mod_headers/mod_expires are enabled. If your free host ignores .htaccess, verify compression/caching in its panel; WOFF2 still works.

Upload each completed file individually if ZIP extraction is unavailable. Purge the host/CDN cache, clear browser cache, then confirm the font responds with 134012 bytes, WOFF2 signature wOF2, and no decode errors. Responsive source updates to assets/css/app.css and assets/js/app.js should be uploaded from the project too if they are not yet deployed.
`;
fs.writeFileSync(path.join(staging, 'UPLOAD-README.txt'), notes);
const archive = path.join(root, '.tools', 'free-infinity-fix.zip');
fs.rmSync(archive, { force: true });
execFileSync('tar', ['-a', '-c', '-f', archive, '-C', staging, '.']);
console.log(JSON.stringify({ archive, files: 13, fontBytes: fs.statSync(path.join(staging, 'assets/vendor/icons/fonts/bootstrap-icons.woff2')).size }, null, 2));
