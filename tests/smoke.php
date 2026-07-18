<?php

declare(strict_types=1);

use IMS\Core\Csrf;
use IMS\Core\Translator;

defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/helpers.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'IMS\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_save_path(sys_get_temp_dir());
    session_start();
}

$translator = new Translator(BASE_PATH . '/lang', 'fr');
$csrf = new Csrf();
set_app([
    'config' => [
        'name' => 'Inventory Management System',
        'currency' => 'XAF',
        'currency_decimals' => 0,
    ],
    'translator' => $translator,
    'csrf' => $csrf,
]);

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    $checks++;
    if (!$condition) {
        throw new RuntimeException($message);
    }
};

try {
    $assert(e('<script>') === '&lt;script&gt;', 'HTML escaping failed.');
    $assert(quantity('12.5000') === '12.5', 'Quantity formatting failed.');
    $assert(str_contains(money(12500), '12 500') && str_contains(money(12500), 'XAF'), 'Money formatting failed.');
    $assert(t('save') === 'Enregistrer', 'French translation failed.');
    $assert(t('movement_type_customer_return') === 'Retour client', 'Inventory translation failed.');
    $assert(t('reports_title') === 'Rapports de stock', 'Reports translation failed.');
    $assert(route('products', ['p' => 2]) === 'index.php?page=products&p=2', 'Route generation failed.');
    $assert(str_contains(icon('box'), '<svg'), 'Icon generation failed.');

    $token = $csrf->token();
    $assert(strlen($token) === 64 && $csrf->validate($token), 'CSRF validation failed.');
    $assert(!$csrf->validate('invalid-token'), 'Invalid CSRF token accepted.');

    $english = new Translator(BASE_PATH . '/lang', 'en');
    $assert($english->get('save') === 'Save', 'English translation failed.');

    fwrite(STDOUT, "Smoke tests passed: {$checks}" . PHP_EOL);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Smoke test failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
