<?php

declare(strict_types=1);

use IMS\Core\ValidationException;
use IMS\Domain\InventoryService;
use IMS\Domain\MovementType;

defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));

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

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    $checks++;
    if (!$condition) {
        throw new RuntimeException($message);
    }
};

try {
    $assert(count(MovementType::all()) === 6, 'Movement type catalog is incomplete.');
    $assert(MovementType::delta('purchase', 2.5) === 2.5, 'Purchase must increase stock.');
    $assert(MovementType::delta('customer_return', 2.5) === 2.5, 'Customer return must increase stock.');
    $assert(MovementType::delta('sale', 2.5) === -2.5, 'Sale must decrease stock.');
    $assert(MovementType::delta('supplier_return', 2.5) === -2.5, 'Supplier return must decrease stock.');
    $assert(MovementType::partnerType('purchase') === 'supplier', 'Purchase supplier rule failed.');
    $assert(MovementType::partnerType('customer_return') === 'customer', 'Customer return rule failed.');
    $assert(MovementType::partnerType('adjustment_in') === null, 'Adjustment must not require a partner.');
    $assert(MovementType::reversalType('sale') === 'adjustment_in', 'Sale reversal rule failed.');

    try {
        MovementType::delta('invalid', 1);
        throw new RuntimeException('Invalid movement type was accepted.');
    } catch (ValidationException) {
        $checks++;
    }

    $service = (new ReflectionClass(InventoryService::class))->newInstanceWithoutConstructor();
    $normalizeDate = new ReflectionMethod(InventoryService::class, 'normalizeOccurredAt');
    $normalizeDate->setAccessible(true);
    $normalized = $normalizeDate->invoke($service, date('Y-m-d\\TH:i'));
    $assert(
        is_string($normalized) && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:00$/', $normalized) === 1,
        'Movement date normalization failed.'
    );

    try {
        $normalizeDate->invoke($service, date('Y-m-d\\TH:i', strtotime('+1 day')));
        throw new RuntimeException('Future movement date was accepted.');
    } catch (ReflectionException) {
        throw new RuntimeException('Unable to test movement date validation.');
    } catch (ValidationException) {
        $checks++;
    }

    fwrite(STDOUT, "Movement type tests passed: {$checks}" . PHP_EOL);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Movement type test failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
