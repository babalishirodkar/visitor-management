<?php
session_start();
require_once __DIR__ . '/includes/helpers.php';

if (is_authenticated()) {
    header('Location: pages/dashboard/dashboard-list.php');
} else {
    header('Location: login.php');
}
exit;
