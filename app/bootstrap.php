<?php

declare(strict_types=1);

use IMS\Core\Auth;
use IMS\Core\Csrf;
use IMS\Core\Database;
use IMS\Core\Translator;
use IMS\Domain\InventoryRepository;
use IMS\Domain\InventoryService;

defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/app/helpers.php';
load_env(BASE_PATH . '/.env');

spl_autoload_register(static function (string $class): void {
    $prefix = 'IMS\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = BASE_PATH . '/app/' . $relative . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

$debug = env_bool('APP_DEBUG', false);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/app.log');

$timezone = env_string('APP_TIMEZONE', 'Africa/Douala');
try {
    new DateTimeZone($timezone);
} catch (Throwable) {
    $timezone = 'UTC';
}
date_default_timezone_set($timezone);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(env_string('SESSION_NAME', 'ims_session'));
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => env_bool('SESSION_SECURE', false),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

$config = [
    'name' => env_string('APP_NAME', 'Inventory Management System'),
    'environment' => env_string('APP_ENV', 'production'),
    'debug' => $debug,
    'timezone' => $timezone,
    'locale' => env_string('APP_LOCALE', 'fr'),
    'currency' => env_string('APP_CURRENCY', 'XAF'),
    'currency_decimals' => env_int('APP_CURRENCY_DECIMALS', 0),
    'db' => [
        'host' => env_string('DB_HOST', '127.0.0.1'),
        'port' => env_int('DB_PORT', 3306),
        'name' => env_string('DB_NAME', 'ims'),
        'user' => env_string('DB_USER', 'ims'),
        'pass' => env_string('DB_PASS', ''),
    ],
];

$pdo = Database::connect($config['db']);
$auth = new Auth($pdo);
$csrf = new Csrf();
$repository = new InventoryRepository($pdo);
$service = new InventoryService($pdo, $repository);

$requestedLocale = isset($_GET['locale']) ? strtolower((string) $_GET['locale']) : null;
if (in_array($requestedLocale, ['fr', 'en'], true)) {
    $_SESSION['locale'] = $requestedLocale;
}

$currentUser = $auth->user();
$locale = $_SESSION['locale'] ?? ($currentUser['locale'] ?? $config['locale']);
$translator = new Translator(BASE_PATH . '/lang', in_array($locale, ['fr', 'en'], true) ? $locale : 'fr');

$container = compact('config', 'pdo', 'auth', 'csrf', 'repository', 'service', 'translator');
set_app($container);

return $container;
