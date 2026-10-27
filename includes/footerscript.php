<?php /* footerscript.php – all scripts with correct relative paths. */ ?>
<script src="<?= versioned_asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<?php if (!empty($needsCharts)): ?>
<script src="<?= versioned_asset('assets/vendor/chartjs/chart.umd.js') ?>"></script>
<?php endif; ?>
<?php if (!empty($needsQR)): ?>
<script src="<?= versioned_asset('assets/vendor/qr/qrcode.js') ?>"></script>
<?php endif; ?>
<script src="<?= versioned_asset('assets/js/app.js') ?>"></script>
<?php if (!empty($needsItems)): ?>
<script src="<?= versioned_asset('assets/js/items.js') ?>"></script>
<?php endif; ?>
<?= $extraScripts ?? '' ?>
</body>
</html>
