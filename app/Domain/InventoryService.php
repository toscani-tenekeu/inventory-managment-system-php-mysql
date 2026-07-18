<?php

declare(strict_types=1);

namespace IMS\Domain;

use PDO;
use IMS\Core\ValidationException;
use Throwable;

final class InventoryService
{
    private const MAX_QUANTITY = 9999999999.9999;
    private const MAX_AMOUNT = 99999999999999.99;

    public function __construct(
        private readonly PDO $pdo,
        private readonly InventoryRepository $repository
    ) {
    }

    public function saveProduct(array $data, ?int $id, float $initialStock, int $userId): int
    {
        $this->pdo->beginTransaction();
        try {
            if ($id !== null) {
                $product = $this->repository->productForUpdate($id);
                if (!$product) {
                    throw new ValidationException('product_not_found');
                }
                if ($product['unit'] !== $data['unit'] && $this->repository->productHasMovements($id)) {
                    throw new ValidationException('product_unit_locked');
                }
            }

            $productId = $this->repository->saveProduct($data, $id);
            if ($id === null && $initialStock > 0) {
                $this->createMovementWithinTransaction([
                    'type' => 'adjustment_in',
                    'product_id' => $productId,
                    'partner_id' => null,
                    'quantity' => $initialStock,
                    'unit_price' => (float) $data['cost_price'],
                    'reference' => 'INIT-' . $productId . '-' . strtoupper((string) $data['sku']),
                    'notes' => 'Initial stock',
                    'occurred_at' => date('Y-m-d\\TH:i'),
                ], $userId);
            }

            $this->repository->audit(
                $id === null ? 'created' : 'updated',
                'product',
                $productId,
                ['sku' => $data['sku'], 'name' => $data['name']],
                $userId
            );
            $this->pdo->commit();
            return $productId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function toggleProduct(int $productId, int $userId): void
    {
        $this->pdo->beginTransaction();
        try {
            $product = $this->repository->productForUpdate($productId);
            if (!$product) {
                throw new ValidationException('product_not_found');
            }
            if ((int) $product['active'] === 1 && abs((float) $product['stock_quantity']) > 0.00005) {
                throw new ValidationException('product_stock_not_zero');
            }
            if (!$this->repository->toggleProduct($productId)) {
                throw new ValidationException('operation_failed');
            }

            $this->repository->audit(
                'status_changed',
                'product',
                $productId,
                ['active' => (int) $product['active'] === 1 ? 0 : 1],
                $userId
            );
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function createMovement(array $data, int $userId): int
    {
        $this->pdo->beginTransaction();
        try {
            $id = $this->createMovementWithinTransaction($data, $userId);
            $this->pdo->commit();
            return $id;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function reverseMovement(int $movementId, int $userId): int
    {
        $this->pdo->beginTransaction();
        try {
            $movement = $this->repository->movementForUpdate($movementId);
            if (!$movement) {
                throw new ValidationException('movement_not_found');
            }
            if ($movement['status'] !== 'posted' || $movement['reversal_of_id'] !== null) {
                throw new ValidationException('movement_cannot_be_reversed');
            }

            $product = $this->repository->productForUpdate((int) $movement['product_id']);
            if (!$product) {
                throw new ValidationException('product_not_found');
            }
            if ((int) $product['active'] !== 1) {
                throw new ValidationException('product_inactive_for_reversal');
            }

            $reverseDelta = -MovementType::delta((string) $movement['type'], (float) $movement['quantity']);
            $newStock = round((float) $product['stock_quantity'] + $reverseDelta, 4);
            if ($newStock < 0) {
                throw new ValidationException('reversal_would_make_stock_negative');
            }
            if (!is_finite($newStock) || $newStock > self::MAX_QUANTITY) {
                throw new ValidationException('invalid_movement_values');
            }

            $this->repository->adjustStock((int) $product['id'], $reverseDelta);
            $this->repository->markMovementReversed($movementId);

            $reverseType = MovementType::reversalType((string) $movement['type']);

            $reversalId = $this->repository->insertMovement([
                'reference' => 'REV-' . $movementId . '-' . date('YmdHis'),
                'type' => $reverseType,
                'product_id' => (int) $movement['product_id'],
                'partner_id' => $movement['partner_id'] ? (int) $movement['partner_id'] : null,
                'quantity' => (float) $movement['quantity'],
                'unit_price' => (float) $movement['unit_price'],
                'total_amount' => (float) $movement['total_amount'],
                'notes' => 'Reversal of ' . $movement['reference'],
                'status' => 'posted',
                'reversal_of_id' => $movementId,
                'created_by' => $userId,
                'occurred_at' => date('Y-m-d H:i:s'),
            ]);

            $this->repository->audit(
                'reversed',
                'stock_movement',
                $movementId,
                ['reversal_id' => $reversalId, 'reference' => $movement['reference']],
                $userId
            );
            $this->pdo->commit();
            return $reversalId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    private function createMovementWithinTransaction(array $data, int $userId): int
    {
        $type = (string) ($data['type'] ?? '');
        $productId = (int) ($data['product_id'] ?? 0);
        $partnerId = !empty($data['partner_id']) ? (int) $data['partner_id'] : null;
        $quantity = round((float) ($data['quantity'] ?? 0), 4);
        $unitPrice = round((float) ($data['unit_price'] ?? 0), 2);

        if (!MovementType::isValid($type)) {
            throw new ValidationException('invalid_movement_type');
        }
        if (
            $productId < 1
            || !is_finite($quantity) || $quantity <= 0 || $quantity > self::MAX_QUANTITY
            || !is_finite($unitPrice) || $unitPrice < 0 || $unitPrice > self::MAX_AMOUNT
        ) {
            throw new ValidationException('invalid_movement_values');
        }

        $product = $this->repository->productForUpdate($productId);
        if (!$product || (int) $product['active'] !== 1) {
            throw new ValidationException('product_not_found');
        }

        $expectedPartnerType = MovementType::partnerType($type);
        if ($expectedPartnerType !== null) {
            if ($partnerId === null) {
                throw new ValidationException('partner_required');
            }
            $partner = $this->repository->partnerForUpdate($partnerId);
            if (!$partner || $partner['type'] !== $expectedPartnerType || (int) $partner['active'] !== 1) {
                throw new ValidationException('invalid_partner');
            }
        } else {
            $partnerId = null;
        }

        $delta = MovementType::delta($type, $quantity);
        $newStock = round((float) $product['stock_quantity'] + $delta, 4);
        if ($newStock < 0) {
            throw new ValidationException('insufficient_stock');
        }
        if (!is_finite($newStock) || $newStock > self::MAX_QUANTITY) {
            throw new ValidationException('invalid_movement_values');
        }

        $reference = $this->normalizeReference((string) ($data['reference'] ?? ''));
        $occurredAt = $this->normalizeOccurredAt((string) ($data['occurred_at'] ?? ''));
        $total = round($quantity * $unitPrice, 2);
        if (!is_finite($total) || $total > self::MAX_AMOUNT) {
            throw new ValidationException('invalid_movement_values');
        }

        $movementId = $this->repository->insertMovement([
            'reference' => $reference,
            'type' => $type,
            'product_id' => $productId,
            'partner_id' => $partnerId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $total,
            'notes' => trim((string) ($data['notes'] ?? '')),
            'status' => 'posted',
            'reversal_of_id' => null,
            'created_by' => $userId,
            'occurred_at' => $occurredAt,
        ]);
        $this->repository->adjustStock($productId, $delta);
        if ($this->repository->minimumLedgerBalance($productId) < -0.00005) {
            throw new ValidationException('historical_stock_negative');
        }
        $this->repository->audit(
            'created',
            'stock_movement',
            $movementId,
            ['type' => $type, 'reference' => $reference, 'quantity' => $quantity, 'occurred_at' => $occurredAt],
            $userId
        );

        return $movementId;
    }

    private function normalizeReference(string $reference): string
    {
        $reference = strtoupper(trim($reference));
        if ($reference === '') {
            return 'MVT-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(2)));
        }

        $reference = preg_replace('/[^A-Z0-9._\/-]+/', '-', $reference) ?: '';
        $reference = trim($reference, '-');
        if ($reference === '') {
            throw new ValidationException('invalid_reference');
        }
        return substr($reference, 0, 64);
    }

    private function normalizeOccurredAt(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return date('Y-m-d H:i:s');
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i', $value);
        $errors = \DateTimeImmutable::getLastErrors();
        if (!$date || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new ValidationException('invalid_movement_date');
        }
        if ($date > new \DateTimeImmutable('+5 minutes')) {
            throw new ValidationException('movement_date_future');
        }

        return $date->format('Y-m-d H:i:s');
    }
}
