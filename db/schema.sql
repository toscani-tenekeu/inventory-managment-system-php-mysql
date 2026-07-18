SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    locale ENUM('fr', 'en') NOT NULL DEFAULT 'fr',
    theme ENUM('system', 'light', 'dark') NOT NULL DEFAULT 'system',
    active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role_active (role, active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    description VARCHAR(500) NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_name (name),
    KEY idx_categories_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id BIGINT UNSIGNED NULL,
    sku VARCHAR(64) NOT NULL,
    barcode VARCHAR(80) NULL,
    name VARCHAR(160) NOT NULL,
    description TEXT NULL,
    location VARCHAR(120) NULL,
    unit VARCHAR(30) NOT NULL DEFAULT 'piece',
    stock_quantity DECIMAL(14,4) NOT NULL DEFAULT 0,
    reorder_level DECIMAL(14,4) NOT NULL DEFAULT 0,
    target_stock DECIMAL(14,4) NOT NULL DEFAULT 0,
    cost_price DECIMAL(16,2) NOT NULL DEFAULT 0,
    sale_price DECIMAL(16,2) NOT NULL DEFAULT 0,
    manufactured_at DATE NULL,
    expires_at DATE NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_products_sku (sku),
    UNIQUE KEY uq_products_barcode (barcode),
    KEY idx_products_name (name),
    KEY idx_products_category (category_id),
    KEY idx_products_stock_alert (active, stock_quantity, reorder_level),
    CONSTRAINT chk_products_stock CHECK (stock_quantity >= 0),
    CONSTRAINT chk_products_levels CHECK (reorder_level >= 0 AND target_stock >= 0),
    CONSTRAINT chk_products_prices CHECK (cost_price >= 0 AND sale_price >= 0),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories (id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS partners (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    type ENUM('customer', 'supplier') NOT NULL,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(160) NOT NULL,
    contact_name VARCHAR(160) NULL,
    email VARCHAR(190) NULL,
    phone VARCHAR(50) NULL,
    address VARCHAR(500) NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_partners_code (code),
    KEY idx_partners_type_active (type, active),
    KEY idx_partners_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stock_movements (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reference VARCHAR(64) NOT NULL,
    type ENUM(
        'purchase', 'sale', 'customer_return', 'supplier_return',
        'adjustment_in', 'adjustment_out'
    ) NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    partner_id BIGINT UNSIGNED NULL,
    quantity DECIMAL(14,4) NOT NULL,
    unit_price DECIMAL(16,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(16,2) NOT NULL DEFAULT 0,
    notes VARCHAR(1000) NULL,
    status ENUM('posted', 'reversed') NOT NULL DEFAULT 'posted',
    reversal_of_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NULL,
    occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_movements_reference (reference),
    UNIQUE KEY uq_movements_reversal (reversal_of_id),
    KEY idx_movements_product_date (product_id, occurred_at, id),
    KEY idx_movements_type_status_date (type, status, occurred_at),
    KEY idx_movements_partner (partner_id),
    KEY idx_movements_created_by (created_by),
    CONSTRAINT chk_movements_quantity CHECK (quantity > 0),
    CONSTRAINT chk_movements_amounts CHECK (unit_price >= 0 AND total_amount >= 0),
    CONSTRAINT fk_movements_product
        FOREIGN KEY (product_id) REFERENCES products (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_movements_partner
        FOREIGN KEY (partner_id) REFERENCES partners (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_movements_user
        FOREIGN KEY (created_by) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_movements_reversal
        FOREIGN KEY (reversal_of_id) REFERENCES stock_movements (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS schema_migrations (
    version VARCHAR(80) NOT NULL,
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO schema_migrations (version) VALUES ('2026-07-classic-inventory');

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    entity_type VARCHAR(80) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    metadata JSON NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_entity (entity_type, entity_id),
    KEY idx_audit_user_date (user_id, created_at),
    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
