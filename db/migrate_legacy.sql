-- À exécuter uniquement après db/schema.sql, dans la base qui contient encore
-- les tables historiques : utilisateur, categorie, article, client,
-- fournisseur, commande et vente.

SET NAMES utf8mb4;
START TRANSACTION;

INSERT INTO categories (name, description, active)
SELECT TRIM(nom_categorie), NULLIF(TRIM(description_categorie), ''), 1
FROM categorie
WHERE TRIM(nom_categorie) <> ''
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO users (name, email, password_hash, role, locale, theme, active)
SELECT
    TRIM(prenom),
    LOWER(TRIM(email)),
    mot_de_passe,
    CASE WHEN role = 'admin' THEN 'admin' ELSE 'user' END,
    'fr',
    'system',
    1
FROM utilisateur
WHERE TRIM(email) <> ''
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    password_hash = VALUES(password_hash),
    role = VALUES(role);

INSERT INTO partners (type, code, name, contact_name, phone, address, active)
SELECT
    'customer',
    CONCAT('LEG-CUST-', id),
    TRIM(CONCAT(nom, ' ', prenom)),
    TRIM(CONCAT(nom, ' ', prenom)),
    NULLIF(TRIM(telephone), ''),
    NULLIF(TRIM(adresse), ''),
    1
FROM client
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    phone = VALUES(phone),
    address = VALUES(address);

INSERT INTO partners (type, code, name, contact_name, phone, address, active)
SELECT
    'supplier',
    CONCAT('LEG-SUP-', id),
    TRIM(CONCAT(nom, ' ', prenom)),
    TRIM(CONCAT(nom, ' ', prenom)),
    NULLIF(TRIM(telephone), ''),
    NULLIF(TRIM(adresse), ''),
    1
FROM fournisseur
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    phone = VALUES(phone),
    address = VALUES(address);

INSERT INTO products (
    category_id, sku, name, unit, stock_quantity, reorder_level,
    cost_price, sale_price, manufactured_at, expires_at, active
)
SELECT
    c.id,
    CONCAT('LEGACY-', a.id),
    TRIM(a.nom_article),
    'piece',
    0,
    5,
    GREATEST(a.prix_unitaire, 0),
    GREATEST(a.prix_unitaire, 0),
    DATE(a.date_fabrication),
    DATE(a.date_expiration),
    1
FROM article a
LEFT JOIN categories c ON LOWER(c.name) = LOWER(TRIM(a.categorie))
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    category_id = VALUES(category_id),
    stock_quantity = 0,
    cost_price = VALUES(cost_price),
    sale_price = VALUES(sale_price);

INSERT IGNORE INTO stock_movements (
    reference, type, product_id, partner_id, quantity, unit_price,
    total_amount, notes, status, created_by, occurred_at, created_at
)
SELECT
    CONCAT('LEG-PUR-', co.id),
    'purchase',
    p.id,
    pr.id,
    co.quantite,
    COALESCE(ROUND(co.prix / NULLIF(co.quantite, 0), 2), 0),
    GREATEST(co.prix, 0),
    'Import historique',
    'posted',
    (SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1),
    COALESCE(co.date_commande, NOW()),
    COALESCE(co.date_commande, NOW())
FROM commande co
JOIN products p ON p.sku = CONCAT('LEGACY-', co.id_article)
LEFT JOIN partners pr ON pr.code = CONCAT('LEG-SUP-', co.id_fournisseur)
WHERE co.quantite > 0;

INSERT IGNORE INTO stock_movements (
    reference, type, product_id, partner_id, quantity, unit_price,
    total_amount, notes, status, created_by, occurred_at, created_at
)
SELECT
    CONCAT('LEG-SAL-', v.id),
    'sale',
    p.id,
    pr.id,
    v.quantite,
    COALESCE(ROUND(v.prix / NULLIF(v.quantite, 0), 2), 0),
    GREATEST(v.prix, 0),
    'Import historique',
    'posted',
    (SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1),
    COALESCE(v.date_vente, NOW()),
    COALESCE(v.date_vente, NOW())
FROM vente v
JOIN products p ON p.sku = CONCAT('LEGACY-', v.id_article)
LEFT JOIN partners pr ON pr.code = CONCAT('LEG-CUST-', v.id_client)
WHERE v.quantite > 0;

-- Reconcile the imported ledger with the current quantity from the legacy
-- product table. This preserves traceability without duplicating current stock.
INSERT INTO stock_movements (
    reference, type, product_id, partner_id, quantity, unit_price,
    total_amount, notes, status, created_by, occurred_at
)
SELECT
    CONCAT('LEG-BAL-', a.id),
    CASE WHEN GREATEST(a.quantite, 0) - COALESCE(m.ledger_quantity, 0) >= 0
        THEN 'adjustment_in' ELSE 'adjustment_out' END,
    p.id,
    NULL,
    ABS(GREATEST(a.quantite, 0) - COALESCE(m.ledger_quantity, 0)),
    p.cost_price,
    ABS(GREATEST(a.quantite, 0) - COALESCE(m.ledger_quantity, 0)) * p.cost_price,
    'Rapprochement du solde historique',
    'posted',
    (SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1),
    NOW()
FROM article a
JOIN products p ON p.sku = CONCAT('LEGACY-', a.id)
LEFT JOIN (
    SELECT product_id,
        SUM(CASE WHEN type IN ('purchase', 'customer_return', 'adjustment_in')
            THEN quantity ELSE -quantity END) AS ledger_quantity
    FROM stock_movements
    GROUP BY product_id
) m ON m.product_id = p.id
WHERE ABS(GREATEST(a.quantite, 0) - COALESCE(m.ledger_quantity, 0)) > 0.00005
ON DUPLICATE KEY UPDATE reference = VALUES(reference);

UPDATE products p
JOIN article a ON p.sku = CONCAT('LEGACY-', a.id)
SET p.stock_quantity = GREATEST(a.quantite, 0),
    p.target_stock = GREATEST(p.reorder_level * 2, p.reorder_level);

COMMIT;
