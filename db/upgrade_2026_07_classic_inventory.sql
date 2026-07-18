-- Upgrade an existing Inventory Management System database.
-- Back up the database before running this file once.

SET NAMES utf8mb4;
START TRANSACTION;

ALTER TABLE products
    ADD COLUMN barcode VARCHAR(80) NULL AFTER sku,
    ADD COLUMN location VARCHAR(120) NULL AFTER description,
    ADD COLUMN target_stock DECIMAL(14,4) NOT NULL DEFAULT 0 AFTER reorder_level,
    ADD UNIQUE KEY uq_products_barcode (barcode),
    ADD CONSTRAINT chk_products_stock CHECK (stock_quantity >= 0),
    ADD CONSTRAINT chk_products_levels CHECK (reorder_level >= 0 AND target_stock >= 0),
    ADD CONSTRAINT chk_products_prices CHECK (cost_price >= 0 AND sale_price >= 0);

UPDATE products
SET target_stock = CASE
    WHEN reorder_level > 0 THEN reorder_level * 2
    ELSE 0
END;

ALTER TABLE stock_movements
    MODIFY COLUMN type ENUM(
        'purchase', 'sale', 'customer_return', 'supplier_return',
        'adjustment_in', 'adjustment_out'
    ) NOT NULL,
    ADD COLUMN occurred_at DATETIME NULL AFTER created_by;

UPDATE stock_movements SET occurred_at = created_at WHERE occurred_at IS NULL;

ALTER TABLE stock_movements
    MODIFY COLUMN occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    DROP KEY idx_movements_product_date,
    DROP KEY idx_movements_type_status,
    ADD KEY idx_movements_product_date (product_id, occurred_at, id),
    ADD KEY idx_movements_type_status_date (type, status, occurred_at),
    ADD CONSTRAINT chk_movements_quantity CHECK (quantity > 0),
    ADD CONSTRAINT chk_movements_amounts CHECK (unit_price >= 0 AND total_amount >= 0);

CREATE TABLE IF NOT EXISTS schema_migrations (
    version VARCHAR(80) NOT NULL,
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO schema_migrations (version) VALUES ('2026-07-classic-inventory');

COMMIT;
