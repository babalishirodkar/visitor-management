<?php
/**
 * Database & API Gateway Helper
 * GateDesk - Visitor Management & Gate Pass System
 */

if (!defined('DB_LOADED')) {
    define('DB_LOADED', true);

    require_once __DIR__ . '/../config.php';

    /**
     * Get the PDO Database Instance (Singleton)
     */
    function get_db(): PDO {
        static $pdo = null;
        if ($pdo === null) {
            global $db_host, $db_user, $db_pass, $db_name;
            $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                $pdo = new PDO($dsn, $db_user, $db_pass, $options);
            } catch (PDOException $e) {
                // If this is an API call, return JSON error
                if (str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/api/')) {
                    api_error("Database connection failed: " . $e->getMessage(), 500);
                }
                throw $e;
            }
        }
        return $pdo;
    }

    /**
     * Send standard JSON API success response
     */
    function api_response($data = null, string $message = 'Success', int $statusCode = 200): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send standard JSON API error response
     */
    function api_error(string $message = 'Error', int $statusCode = 400, array $errors = []): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Parse incoming request input (handles JSON body and standard POST/GET)
     */
    function api_input(): array {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        $data = [];
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true) ?? [];
        } else {
            $data = ($method === 'POST') ? $_POST : $_GET;
        }

        // Merge query parameters for GET or optional params
        return array_merge($_GET, $data);
    }
}
