<?php
/* Shared document head. All assets are served locally. */
require_once __DIR__ . '/helpers.php';

// Check auth unless $isPublicPage is defined
if (empty($isPublicPage)) {
    require_auth();
}

$pageTitle = $pageTitle ?? 'GateDesk';
$currentUser = get_current_user_info();
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> · GateDesk Visitor Management</title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%232563eb'/%3E%3Cpath d='M8 23V11l8-4 8 4v12' fill='none' stroke='%23ffffff' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='16' cy='16' r='2' fill='%23ffffff'/%3E%3C/svg%3E">
  
  <meta name="theme-color" content="#132d39">
  <!-- Match the icon stylesheet's URL exactly so this request is reused. -->
  <link rel="preload" href="<?= asset_url('assets/vendor/icons/fonts/bootstrap-icons.woff2') ?>" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?= asset_url('assets/vendor/fonts/ibm-plex-sans-latin-400-normal.woff2') ?>" as="font" type="font/woff2" crossorigin>

  <link rel="stylesheet" href="<?= versioned_asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= versioned_asset('assets/vendor/icons/bootstrap-icons.min.css') ?>">
  <link rel="stylesheet" href="<?= versioned_asset('assets/css/app.css') ?>">
  <?= $extraHead ?? '' ?>
</head>
