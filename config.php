<?php
// Prevent multiple inclusions
if (!defined('CONFIG_LOADED')) {
    define('CONFIG_LOADED', true);

    // Dynamic Protocol & Host Detection
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $protocol = $is_https ? 'https://' : 'http://';
    $http_host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Resolve the mount path for WAMP subfolders and PHP's built-in server.
    $app_root = str_replace('\\', '/', __DIR__);
    $script_file = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base_url_path = '';
    if (str_starts_with($script_file, $app_root . '/')) {
        $relative_script = substr($script_file, strlen($app_root));
        if (str_ends_with($script_name, $relative_script)) {
            $base_url_path = substr($script_name, 0, -strlen($relative_script));
        }
    }
    $site_url = $protocol . $http_host . rtrim($base_url_path, '/') . '/';

    define('SITE_URL', $site_url);
    define('BASE_PATH', str_replace('\\', '/', __DIR__));

    // Database Configuration (Ready for MySQL integration)
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "visitor_management";

    // Global site constants
    define('APP_NAME', 'GateDesk');
    define('APP_TAGLINE', 'Enterprise Visitor & Gate Pass Management');
}
