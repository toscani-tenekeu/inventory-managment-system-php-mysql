<?php

declare(strict_types=1);

namespace IMS\Domain;

use PDO;
use IMS\Core\ValidationException;

final class InventoryRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function dashboardStats(): array
    {
        $sql = <<<'SQL'
            SELECT
                (SELECT COUNT(*) FROM products WHERE active = 1) AS products_count,
                (SELECT COUNT(*) FROM products WHERE active = 1 AND stock_quantity = 0) AS out_of_stock_count,
                (SELECT COUNT(*) FROM products WHERE active = 1 AND stock_quantity <= reorder_level) AS low_stock_count,
                (SELECT COALESCE(SUM(stock_quantity * cost_price), 0) FROM products WHERE active = 1) AS inventory_value,
                (SELECT COALESCE(SUM(CASE
                    WHEN type = 'sale' THEN total_amount
                    WHEN type = 'customer_return' THEN -total_amount
                    ELSE 0 END), 0)
                 FROM stock_movements
                 WHERE status = 'posted' AND occurred_at >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')) AS monthly_sales,
                (SELECT COALESCE(SUM(CASE
                    WHEN type = 'purchase' THEN total_amount
                    WHEN type = 'supplier_return' THEN -total_amount
                    ELSE 0 END), 0)
                 FROM stock_movements
                 WHERE status = 'posted' AND occurred_at >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')) AS monthly_purchases
            SQL;
        return $this->pdo->query($sql)->fetch() ?: [];
    }

    public function recentMovements(int $limit = 8): array
    {
        $statement = $this->pdo->prepare(
            'SELECT m.*, p.name AS product_name, p.sku, pr.name AS partner_name, u.name AS user_name
             FROM stock_movements m
             JOIN products p ON p.id = m.product_id
             LEFT JOIN partners pr ON pr.id = m.partner_id
             LEFT JOIN users u ON u.id = m.created_by
             ORDER BY m.occurred_at DESC, m.id DESC
             LIMIT :limit'
        );
        $statement->bindValue(':limit', max(1, min(50, $limit)), PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function lowStockProducts(int $limit = 8): array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.active = 1 AND p.stock_quantity <= p.reorder_level
             ORDER BY (p.stock_quantity = 0) DESC, p.stock_quantity ASC, p.name ASC
             LIMIT :limit'
        );
        $statement->bindValue(':limit', max(1, min(50, $limit)), PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function topSellingProducts(int $limit = 6): array
    {
        $statement = $this->pdo->prepare(
            "SELECT p.id, p.name, p.sku,
                    SUM(CASE WHEN m.type = 'sale' THEN m.quantity ELSE -m.quantity END) AS quantity_sold,
                    SUM(CASE WHEN m.type = 'sale' THEN m.total_amount ELSE -m.total_amount END) AS sales_total
             FROM stock_movements m
             JOIN products p ON p.id = m.product_id
             WHERE m.type IN ('sale', 'customer_return') AND m.status = 'posted'
             GROUP BY p.id, p.name, p.sku
             HAVING quantity_sold > 0
             ORDER BY quantity_sold DESC, sales_total DESC
             LIMIT :limit"
        );
        $statement->bindValue(':limit', max(1, min(50, $limit)), PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function products(
        string $search = '',
        string $status = 'active',
        int $page = 1,
        int $perPage = 20,
        ?int $categoryId = null,
        string $stockStatus = ''
    ): array {
        $where = [];
        $params = [];

        if ($status === 'active') {
            $where[] = 'p.active = 1';
        } elseif ($status === 'archived') {
            $where[] = 'p.active = 0';
        }

        if ($search !== '') {
            $where[] = '(p.name LIKE :q_name OR p.sku LIKE :q_sku OR p.barcode LIKE :q_barcode
                OR p.location LIKE :q_location OR c.name LIKE :q_category)';
            $like = '%' . $search . '%';
            $params += [
                'q_name' => $like,
                'q_sku' => $like,
                'q_barcode' => $like,
                'q_location' => $like,
                'q_category' => $like,
            ];
        }

        if ($categoryId !== null) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        if ($stockStatus === 'out') {
            $where[] = 'p.stock_quantity = 0';
        } elseif ($stockStatus === 'low') {
            $where[] = 'p.stock_quantity > 0 AND p.stock_quantity <= p.reorder_level';
        } elseif ($stockStatus === 'healthy') {
            $where[] = 'p.stock_quantity > p.reorder_level';
        } elseif ($stockStatus === 'available') {
            $where[] = 'p.stock_quantity > 0';
        }

        $clause = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        return $this->paginate(
            'SELECT p.*, c.name AS category_name
             FROM products p LEFT JOIN categories c ON c.id = p.category_id' . $clause . '
             ORDER BY p.active DESC, p.name ASC',
            'SELECT COUNT(*) FROM products p LEFT JOIN categories c ON c.id = p.category_id' . $clause,
            $params,
            $page,
            $perPage
        );
    }

    public function product(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.*, c.name AS category_name FROM products p
             LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function productForUpdate(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1 FOR UPDATE');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function saveProduct(array $data, ?int $id = null): int
    {
        $bindings = [
            'sku' => $data['sku'],
            'barcode' => $data['barcode'] ?: null,
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'location' => $data['location'] ?: null,
            'category_id' => $data['category_id'] ?: null,
            'unit' => $data['unit'],
            'cost_price' => $data['cost_price'],
            'sale_price' => $data['sale_price'],
            'reorder_level' => $data['reorder_level'],
            'target_stock' => $data['target_stock'],
            'manufactured_at' => $data['manufactured_at'] ?: null,
            'expires_at' => $data['expires_at'] ?: null,
        ];

        if ($id !== null) {
            $bindings['id'] = $id;
            $statement = $this->pdo->prepare(
                'UPDATE products SET
                    sku = :sku, barcode = :barcode, name = :name, description = :description,
                    location = :location, category_id = :category_id,
                    unit = :unit, cost_price = :cost_price, sale_price = :sale_price,
                    reorder_level = :reorder_level, target_stock = :target_stock,
                    manufactured_at = :manufactured_at, expires_at = :expires_at
                 WHERE id = :id'
            );
            $statement->execute($bindings);
            return $id;
        }

        $statement = $this->pdo->prepare(
            'INSERT INTO products
                (sku, barcode, name, description, location, category_id, unit, stock_quantity,
                 cost_price, sale_price, reorder_level, target_stock, manufactured_at, expires_at)
             VALUES
                (:sku, :barcode, :name, :description, :location, :category_id, :unit, 0,
                 :cost_price, :sale_price, :reorder_level, :target_stock, :manufactured_at, :expires_at)'
        );
        $statement->execute($bindings);
        return (int) $this->pdo->lastInsertId();
    }

    public function toggleProduct(int $id): bool
    {
        $statement = $this->pdo->prepare('UPDATE products SET active = 1 - active WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->rowCount() === 1;
    }

    public function productHasMovements(int $id): bool
    {
        $statement = $this->pdo->prepare('SELECT EXISTS(SELECT 1 FROM stock_movements WHERE product_id = :id)');
        $statement->execute(['id' => $id]);
        return (bool) $statement->fetchColumn();
    }

    public function minimumLedgerBalance(int $productId): float
    {
        $statement = $this->pdo->prepare(
            "SELECT COALESCE(MIN(balance_after), 0)
             FROM (
                SELECT SUM(CASE
                    WHEN type IN ('purchase', 'customer_return', 'adjustment_in') THEN quantity
                    ELSE -quantity END
                ) OVER (ORDER BY occurred_at, id ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS balance_after
                FROM stock_movements
                WHERE product_id = :product_id
             ) AS ledger"
        );
        $statement->execute(['product_id' => $productId]);
        return (float) $statement->fetchColumn();
    }

    public function activeProducts(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, sku, barcode, name, unit, stock_quantity, cost_price, sale_price
             FROM products WHERE active = 1 ORDER BY name, sku'
        );
        return $statement->fetchAll();
    }

    public function categories(bool $activeOnly = false): array
    {
        $where = $activeOnly ? 'WHERE c.active = 1' : '';
        $statement = $this->pdo->query(
            "SELECT c.*, COUNT(p.id) AS products_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             {$where}
             GROUP BY c.id, c.name, c.description, c.active, c.created_at, c.updated_at
             ORDER BY c.active DESC, c.name ASC"
        );
        return $statement->fetchAll();
    }

    public function category(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function saveCategory(array $data, ?int $id = null): int
    {
        if ($id !== null) {
            $statement = $this->pdo->prepare(
                'UPDATE categories SET name = :name, description = :description WHERE id = :id'
            );
            $statement->execute([
                'name' => $data['name'],
                'description' => $data['description'] ?: null,
                'id' => $id,
            ]);
            return $id;
        }

        $statement = $this->pdo->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');
        $statement->execute(['name' => $data['name'], 'description' => $data['description'] ?: null]);
        return (int) $this->pdo->lastInsertId();
    }

    public function toggleCategory(int $id): bool
    {
        $statement = $this->pdo->prepare('UPDATE categories SET active = 1 - active WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->rowCount() === 1;
    }

    public function partners(
        string $type,
        string $search = '',
        string $status = 'active',
        int $page = 1,
        int $perPage = 20
    ): array {
        $where = ['pr.type = :partner_type'];
        $params = ['partner_type' => $type];

        if ($status === 'active') {
            $where[] = 'pr.active = 1';
        } elseif ($status === 'archived') {
            $where[] = 'pr.active = 0';
        }

        if ($search !== '') {
            $where[] = '(pr.name LIKE :q_name OR pr.code LIKE :q_code OR pr.email LIKE :q_email OR pr.phone LIKE :q_phone)';
            $like = '%' . $search . '%';
            $params += ['q_name' => $like, 'q_code' => $like, 'q_email' => $like, 'q_phone' => $like];
        }

        $clause = ' WHERE ' . implode(' AND ', $where);
        return $this->paginate(
            'SELECT pr.* FROM partners pr' . $clause . ' ORDER BY pr.active DESC, pr.name ASC',
            'SELECT COUNT(*) FROM partners pr' . $clause,
            $params,
            $page,
            $perPage
        );
    }

    public function partner(int $id, ?string $type = null): ?array
    {
        $sql = 'SELECT * FROM partners WHERE id = :id';
        $params = ['id' => $id];
        if ($type !== null) {
            $sql .= ' AND type = :type';
            $params['type'] = $type;
        }
        $statement = $this->pdo->prepare($sql . ' LIMIT 1');
        $statement->execute($params);
        return $statement->fetch() ?: null;
    }

    public function partnerForUpdate(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM partners WHERE id = :id LIMIT 1 FOR UPDATE');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function savePartner(array $data, ?int $id = null): int
    {
        $bindings = [
            'type' => $data['type'],
            'code' => $data['code'],
            'name' => $data['name'],
            'contact_name' => $data['contact_name'] ?: null,
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null,
            'address' => $data['address'] ?: null,
        ];

        if ($id !== null) {
            $bindings['id'] = $id;
            $statement = $this->pdo->prepare(
                'UPDATE partners SET type = :type, code = :code, name = :name, contact_name = :contact_name,
                    email = :email, phone = :phone, address = :address WHERE id = :id'
            );
            $statement->execute($bindings);
            return $id;
        }

        $statement = $this->pdo->prepare(
            'INSERT INTO partners (type, code, name, contact_name, email, phone, address)
             VALUES (:type, :code, :name, :contact_name, :email, :phone, :address)'
        );
        $statement->execute($bindings);
        return (int) $this->pdo->lastInsertId();
    }

    public function togglePartner(int $id): bool
    {
        $statement = $this->pdo->prepare('UPDATE partners SET active = 1 - active WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->rowCount() === 1;
    }

    public function activePartners(?string $type = null): array
    {
        $sql = 'SELECT id, type, code, name FROM partners WHERE active = 1';
        $params = [];
        if ($type !== null) {
            $sql .= ' AND type = :type';
            $params['type'] = $type;
        }
        $sql .= ' ORDER BY name';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function movements(
        string $type = '',
        string $search = '',
        string $status = '',
        int $page = 1,
        int $perPage = 25,
        ?int $productId = null,
        string $dateFrom = '',
        string $dateTo = ''
    ): array {
        $where = [];
        $params = [];

        if (in_array($type, MovementType::all(), true)) {
            $where[] = 'm.type = :movement_type';
            $params['movement_type'] = $type;
        }
        if (in_array($status, ['posted', 'reversed'], true)) {
            $where[] = 'm.status = :movement_status';
            $params['movement_status'] = $status;
        }
        if ($search !== '') {
            $where[] = '(m.reference LIKE :q_reference OR p.name LIKE :q_product OR p.sku LIKE :q_sku
                OR p.barcode LIKE :q_barcode OR pr.name LIKE :q_partner)';
            $like = '%' . $search . '%';
            $params += [
                'q_reference' => $like,
                'q_product' => $like,
                'q_sku' => $like,
                'q_barcode' => $like,
                'q_partner' => $like,
            ];
        }
        if ($productId !== null) {
            $where[] = 'm.product_id = :product_id';
            $params['product_id'] = $productId;
        }
        if ($dateFrom !== '') {
            $where[] = 'm.occurred_at >= :date_from';
            $params['date_from'] = $dateFrom . ' 00:00:00';
        }
        if ($dateTo !== '') {
            $where[] = 'm.occurred_at < DATE_ADD(:date_to, INTERVAL 1 DAY)';
            $params['date_to'] = $dateTo . ' 00:00:00';
        }

        $clause = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $join = ' FROM ' . $this->movementLedgerSql() . '
                  JOIN products p ON p.id = m.product_id
                  LEFT JOIN partners pr ON pr.id = m.partner_id
                  LEFT JOIN users u ON u.id = m.created_by';
        $countJoin = ' FROM stock_movements m
                       JOIN products p ON p.id = m.product_id
                       LEFT JOIN partners pr ON pr.id = m.partner_id';

        return $this->paginate(
            'SELECT m.*, p.name AS product_name, p.sku, p.unit, pr.name AS partner_name, u.name AS user_name'
                . $join . $clause . ' ORDER BY m.occurred_at DESC, m.id DESC',
            'SELECT COUNT(*)' . $countJoin . $clause,
            $params,
            $page,
            $perPage
        );
    }

    public function movement(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT m.*, p.name AS product_name, p.sku, p.unit, pr.name AS partner_name,
                    pr.code AS partner_code, pr.email AS partner_email, pr.phone AS partner_phone,
                    pr.address AS partner_address, u.name AS user_name
             FROM ' . $this->movementLedgerSql() . '
             JOIN products p ON p.id = m.product_id
             LEFT JOIN partners pr ON pr.id = m.partner_id
             LEFT JOIN users u ON u.id = m.created_by
             WHERE m.id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function movementForUpdate(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM stock_movements WHERE id = :id LIMIT 1 FOR UPDATE');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function insertMovement(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO stock_movements
                (reference, type, product_id, partner_id, quantity, unit_price, total_amount,
                 notes, status, reversal_of_id, created_by, occurred_at)
             VALUES
                (:reference, :type, :product_id, :partner_id, :quantity, :unit_price, :total_amount,
                 :notes, :status, :reversal_of_id, :created_by, :occurred_at)'
        );
        $statement->execute([
            'reference' => $data['reference'],
            'type' => $data['type'],
            'product_id' => $data['product_id'],
            'partner_id' => $data['partner_id'] ?: null,
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'total_amount' => $data['total_amount'],
            'notes' => $data['notes'] ?: null,
            'status' => $data['status'] ?? 'posted',
            'reversal_of_id' => $data['reversal_of_id'] ?? null,
            'created_by' => $data['created_by'] ?: null,
            'occurred_at' => $data['occurred_at'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function adjustStock(int $productId, float $delta): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE products SET stock_quantity = stock_quantity + :delta
             WHERE id = :id AND stock_quantity + :delta >= 0'
        );
        $statement->execute(['delta' => $delta, 'id' => $productId]);
        if ($statement->rowCount() !== 1) {
            throw new ValidationException('insufficient_stock');
        }
    }

    public function markMovementReversed(int $id): void
    {
        $this->pdo->prepare('UPDATE stock_movements SET status = :status WHERE id = :id')
            ->execute(['status' => 'reversed', 'id' => $id]);
    }

    public function users(): array
    {
        return $this->pdo->query(
            'SELECT id, name, email, role, locale, theme, active, last_login_at, created_at
             FROM users ORDER BY active DESC, name ASC'
        )->fetchAll();
    }

    public function user(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, role, locale, theme, active, last_login_at FROM users WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function saveUser(array $data, ?int $id = null): int
    {
        if ($id !== null) {
            $bindings = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'locale' => $data['locale'],
                'id' => $id,
            ];
            $passwordSql = '';
            if ($data['password'] !== '') {
                $passwordSql = ', password_hash = :password_hash';
                $bindings['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            $statement = $this->pdo->prepare(
                'UPDATE users SET name = :name, email = :email, role = :role, locale = :locale'
                . $passwordSql . ' WHERE id = :id'
            );
            $statement->execute($bindings);
            return $id;
        }

        $statement = $this->pdo->prepare(
            'INSERT INTO users (name, email, password_hash, role, locale)
             VALUES (:name, :email, :password_hash, :role, :locale)'
        );
        $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'locale' => $data['locale'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function toggleUser(int $id): bool
    {
        $statement = $this->pdo->prepare('UPDATE users SET active = 1 - active WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->rowCount() === 1;
    }

    public function activeAdminCount(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND active = 1")->fetchColumn();
    }

    public function updateProfile(int $id, array $data): void
    {
        $bindings = [
            'name' => $data['name'],
            'email' => $data['email'],
            'locale' => $data['locale'],
            'theme' => $data['theme'],
            'id' => $id,
        ];
        $passwordSql = '';
        if ($data['password'] !== '') {
            $passwordSql = ', password_hash = :password_hash';
            $bindings['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $statement = $this->pdo->prepare(
            'UPDATE users SET name = :name, email = :email, locale = :locale, theme = :theme'
            . $passwordSql . ' WHERE id = :id'
        );
        $statement->execute($bindings);
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';

        $products = $this->pdo->prepare(
            'SELECT id, sku AS code, barcode, name, stock_quantity, active FROM products
             WHERE name LIKE :q_name OR sku LIKE :q_sku OR barcode LIKE :q_barcode OR location LIKE :q_location
             ORDER BY active DESC, name LIMIT 8'
        );
        $products->execute([
            'q_name' => $like,
            'q_sku' => $like,
            'q_barcode' => $like,
            'q_location' => $like,
        ]);

        $partners = $this->pdo->prepare(
            'SELECT id, type, code, name, phone, active FROM partners
             WHERE name LIKE :q_name OR code LIKE :q_code OR phone LIKE :q_phone
             ORDER BY active DESC, name LIMIT 8'
        );
        $partners->execute(['q_name' => $like, 'q_code' => $like, 'q_phone' => $like]);

        $movements = $this->pdo->prepare(
            'SELECT m.id, m.reference, m.type, m.total_amount, m.occurred_at, p.name AS product_name
             FROM stock_movements m JOIN products p ON p.id = m.product_id
             WHERE m.reference LIKE :q_reference OR p.name LIKE :q_product
             ORDER BY m.occurred_at DESC, m.id DESC LIMIT 8'
        );
        $movements->execute(['q_reference' => $like, 'q_product' => $like]);

        return [
            'products' => $products->fetchAll(),
            'partners' => $partners->fetchAll(),
            'movements' => $movements->fetchAll(),
        ];
    }

    public function exportProducts(): array
    {
        return $this->pdo->query(
            'SELECT p.sku, p.barcode, p.name, c.name AS category, p.location, p.unit,
                    p.stock_quantity, p.reorder_level, p.target_stock,
                    p.cost_price, p.sale_price, (p.stock_quantity * p.cost_price) AS stock_value,
                    p.active, p.expires_at
             FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.name'
        )->fetchAll();
    }

    public function exportMovements(): array
    {
        return $this->pdo->query(
            'SELECT m.reference, m.type, p.sku, p.name AS product, pr.name AS partner,
                    m.quantity, m.unit_price, m.total_amount, m.status, u.name AS created_by,
                    m.occurred_at, m.created_at
             FROM stock_movements m
             JOIN products p ON p.id = m.product_id
             LEFT JOIN partners pr ON pr.id = m.partner_id
             LEFT JOIN users u ON u.id = m.created_by
             ORDER BY m.occurred_at DESC, m.id DESC'
        )->fetchAll();
    }

    public function reorderSuggestions(): array
    {
        return $this->pdo->query(
            "SELECT p.id, p.sku, p.barcode, p.name, p.location, p.unit, p.stock_quantity,
                    p.reorder_level, GREATEST(p.target_stock, p.reorder_level) AS target_stock,
                    p.cost_price, c.name AS category_name,
                    GREATEST(GREATEST(p.target_stock, p.reorder_level) - p.stock_quantity, 0) AS suggested_quantity,
                    GREATEST(GREATEST(p.target_stock, p.reorder_level) - p.stock_quantity, 0) * p.cost_price AS suggested_value
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.active = 1 AND p.stock_quantity <= p.reorder_level
             ORDER BY (p.stock_quantity = 0) DESC, p.stock_quantity ASC, p.name ASC"
        )->fetchAll();
    }

    public function exportReorderSuggestions(): array
    {
        return $this->pdo->query(
            "SELECT p.sku, p.barcode, p.name, c.name AS category, p.location, p.unit,
                    p.stock_quantity, p.reorder_level,
                    GREATEST(p.target_stock, p.reorder_level) AS target_stock,
                    GREATEST(GREATEST(p.target_stock, p.reorder_level) - p.stock_quantity, 0) AS suggested_quantity,
                    GREATEST(GREATEST(p.target_stock, p.reorder_level) - p.stock_quantity, 0) * p.cost_price AS suggested_value
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.active = 1 AND p.stock_quantity <= p.reorder_level
             ORDER BY (p.stock_quantity = 0) DESC, p.stock_quantity ASC, p.name ASC"
        )->fetchAll();
    }

    public function categoryInventorySummary(): array
    {
        return $this->pdo->query(
            "SELECT COALESCE(c.name, '—') AS category_name, COUNT(p.id) AS products_count,
                    SUM(CASE WHEN p.stock_quantity = 0 THEN 1 ELSE 0 END) AS out_of_stock_count,
                    COALESCE(SUM(p.stock_quantity * p.cost_price), 0) AS stock_value
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.active = 1
             GROUP BY c.id, c.name
             ORDER BY stock_value DESC, category_name ASC"
        )->fetchAll();
    }

    public function movementSummary(string $dateFrom, string $dateTo): array
    {
        $statement = $this->pdo->prepare(
            'SELECT m.type, p.unit, COUNT(*) AS movements_count, SUM(m.quantity) AS total_quantity,
                    SUM(m.total_amount) AS total_amount
             FROM stock_movements m
             JOIN products p ON p.id = m.product_id
             WHERE m.status = :status AND m.occurred_at >= :date_from
               AND m.occurred_at < DATE_ADD(:date_to, INTERVAL 1 DAY)
             GROUP BY m.type, p.unit ORDER BY m.type, p.unit'
        );
        $statement->execute([
            'status' => 'posted',
            'date_from' => $dateFrom . ' 00:00:00',
            'date_to' => $dateTo . ' 00:00:00',
        ]);
        return $statement->fetchAll();
    }

    public function stockIntegrityIssues(): array
    {
        return $this->pdo->query(
            "SELECT p.id, p.sku, p.name, p.stock_quantity,
                    COALESCE(SUM(CASE
                        WHEN m.type IN ('purchase', 'customer_return', 'adjustment_in') THEN m.quantity
                        ELSE -m.quantity END), 0) AS ledger_quantity
             FROM products p
             LEFT JOIN stock_movements m ON m.product_id = p.id
             GROUP BY p.id, p.sku, p.name, p.stock_quantity
             HAVING ABS(stock_quantity - ledger_quantity) > 0.00005
             ORDER BY p.name"
        )->fetchAll();
    }

    public function audit(string $action, string $entityType, ?int $entityId, array $metadata, ?int $userId): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO audit_logs (user_id, action, entity_type, entity_id, metadata, ip_address)
             VALUES (:user_id, :action, :entity_type, :entity_id, :metadata, :ip_address)'
        );
        $statement->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata === [] ? null : json_encode($metadata, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'ip_address' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? 'cli'), 0, 45),
        ]);
    }

    private function movementLedgerSql(): string
    {
        return "(SELECT sm.*,
                    SUM(CASE
                        WHEN sm.type IN ('purchase', 'customer_return', 'adjustment_in') THEN sm.quantity
                        ELSE -sm.quantity END
                    ) OVER (
                        PARTITION BY sm.product_id
                        ORDER BY sm.occurred_at, sm.id
                        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
                    ) AS balance_after
                 FROM stock_movements sm) AS m";
    }

    private function paginate(
        string $dataSql,
        string $countSql,
        array $params,
        int $page,
        int $perPage
    ): array {
        $page = max(1, $page);
        $perPage = max(5, min(100, $perPage));

        $count = $this->pdo->prepare($countSql);
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);

        $statement = $this->pdo->prepare($dataSql . ' LIMIT :limit OFFSET :offset');
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . ltrim((string) $key, ':'), $value);
        }
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $statement->execute();

        return [
            'items' => $statement->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => $pages,
        ];
    }
}
