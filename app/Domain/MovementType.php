<?php

declare(strict_types=1);

namespace IMS\Domain;

use IMS\Core\ValidationException;

final class MovementType
{
    private const CONFIG = [
        'purchase' => ['direction' => 1, 'partner' => 'supplier', 'price' => 'cost_price'],
        'sale' => ['direction' => -1, 'partner' => 'customer', 'price' => 'sale_price'],
        'customer_return' => ['direction' => 1, 'partner' => 'customer', 'price' => 'sale_price'],
        'supplier_return' => ['direction' => -1, 'partner' => 'supplier', 'price' => 'cost_price'],
        'adjustment_in' => ['direction' => 1, 'partner' => null, 'price' => 'cost_price'],
        'adjustment_out' => ['direction' => -1, 'partner' => null, 'price' => 'cost_price'],
    ];

    public static function all(): array
    {
        return array_keys(self::CONFIG);
    }

    public static function isValid(string $type): bool
    {
        return isset(self::CONFIG[$type]);
    }

    public static function isInbound(string $type): bool
    {
        return self::config($type)['direction'] === 1;
    }

    public static function delta(string $type, float $quantity): float
    {
        return round(self::config($type)['direction'] * $quantity, 4);
    }

    public static function partnerType(string $type): ?string
    {
        return self::config($type)['partner'];
    }

    public static function priceField(string $type): string
    {
        return self::config($type)['price'];
    }

    public static function reversalType(string $type): string
    {
        return self::isInbound($type) ? 'adjustment_out' : 'adjustment_in';
    }

    private static function config(string $type): array
    {
        if (!isset(self::CONFIG[$type])) {
            throw new ValidationException('invalid_movement_type');
        }

        return self::CONFIG[$type];
    }
}
