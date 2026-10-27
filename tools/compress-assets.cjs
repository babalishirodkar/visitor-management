/* Optional build step: node tools/compress-assets.cjs (Node built-ins only).
 * Source files remain authoritative. PHP falls back when a .gz is stale/missing.
 */
const fs = require('node:fs');
const path = require('node:path');
const { gzipSync } = require('node:zlib');
const root = path.resolve(__dirname, '..');
const files = [
  'assets/css/app.css',
  'assets/js/app.js',
  'assets/js/items.js',
  'assets/vendor/bootstrap/bootstrap.min.css',
  'assets/vendor/bootstrap/bootstrap.bundle.min.js',
  'assets/vendor/icons/bootstrap-icons.min.css',
  'assets/vendor/chartjs/chart.umd.js',
  'assets/vendor/qr/qrcode.js',
];
let original = 0, compressed = 0;
for (const file of files) {
  const input = fs.readFileSync(path.join(root, file));
  const output = gzipSync(input, { level: 9 });
  fs.writeFileSync(path.join(root, file + '.gz'), output);
  original += input.length;
  compressed += output.length;
}
console.log(`${files.length} assets: ${original} → ${compressed} bytes (${Math.round((1 - compressed / original) * 100)}% smaller over the wire)`);
