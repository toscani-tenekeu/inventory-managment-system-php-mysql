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

session_save_path(sys_get_temp_dir());
session_start();
$_SERVER['SCRIPT_NAME'] = 'index.php';

$fakeUser = [
    'id' => 1,
    'name' => 'Test Admin',
    'email' => 'admin@example.com',
    'role' => 'admin',
    'locale' => 'fr',
    'theme' => 'system',
    'active' => 1,
    'last_login_at' => '2026-07-18 12:00:00',
];
$fakeAuth = new class($fakeUser) {
    public function __construct(private readonly array $user)
    {
    }
    public function user(): array
    {
        return $this->user;
    }
    public function isAdmin(): bool
    {
        return true;
    }
};

set_app([
    'config' => [
        'name' => 'Inventory Management System',
        'currency' => 'XAF',
        'currency_decimals' => 0,
    ],
    'translator' => new Translator(BASE_PATH . '/lang', 'fr'),
    'csrf' => new Csrf(),
    'auth' => $fakeAuth,
]);

$product = [
    'id' => 1,
    'sku' => 'SKU-001',
    'barcode' => '1234567890123',
    'name' => 'Produit test',
    'description' => 'Description',
    'location' => 'A-01',
    'category_id' => 1,
    'category_name' => 'Catégorie test',
    'unit' => 'piece',
    'stock_quantity' => '8.0000',
    'reorder_level' => '10.0000',
    'target_stock' => '20.0000',
    'cost_price' => '1000.00',
    'sale_price' => '1500.00',
    'manufactured_at' => '2026-01-01',
    'expires_at' => '2027-01-01',
    'active' => 1,
];
$category = [
    'id' => 1,
    'name' => 'Catégorie test',
    'description' => 'Description',
    'active' => 1,
    'products_count' => 1,
];
$partner = [
    'id' => 1,
    'type' => 'customer',
    'code' => 'CLI-001',
    'name' => 'Client test',
    'contact_name' => 'Contact test',
    'email' => 'client@example.com',
    'phone' => '+237600000000',
    'address' => 'Yaoundé',
    'active' => 1,
];
$movement = [
    'id' => 1,
    'reference' => 'MVT-TEST-001',
    'type' => 'sale',
    'product_id' => 1,
    'product_name' => 'Produit test',
    'sku' => 'SKU-001',
    'unit' => 'piece',
    'partner_id' => 1,
    'partner_name' => 'Client test',
    'partner_code' => 'CLI-001',
    'partner_email' => 'client@example.com',
    'partner_phone' => '+237600000000',
    'partner_address' => 'Yaoundé',
    'quantity' => '2.0000',
    'unit_price' => '1500.00',
    'total_amount' => '3000.00',
    'notes' => 'Test',
    'status' => 'posted',
    'reversal_of_id' => null,
    'user_name' => 'Test Admin',
    'occurred_at' => '2026-07-18 12:00:00',
    'balance_after' => '8.0000',
    'created_at' => '2026-07-18 12:00:00',
];
$pagination = [
    'items' => [$product],
    'total' => 1,
    'page' => 1,
    'per_page' => 20,
    'pages' => 1,
];

$cases = [
    ['login', ['title' => 'Connexion'], 'auth', []],
    ['dashboard', [
        'title' => 'Tableau de bord',
        'currentPage' => 'dashboard',
        'stats' => [
            'products_count' => 1,
            'out_of_stock_count' => 0,
            'low_stock_count' => 1,
            'inventory_value' => 8000,
            'monthly_sales' => 3000,
            'monthly_purchases' => 0,
        ],
        'recentMovements' => [$movement],
        'lowStockProducts' => [$product],
        'topProducts' => [[
            'id' => 1,
            'name' => 'Produit test',
            'sku' => 'SKU-001',
            'quantity_sold' => 2,
            'sales_total' => 3000,
        ]],
    ], 'app', []],
    ['products', [
        'title' => 'Articles',
        'currentPage' => 'products',
        'products' => $pagination,
        'categories' => [$category],
        'editing' => null,
        'search' => '',
        'status' => 'active',
        'stockStatus' => '',
        'categoryId' => null,
    ], 'app', ['page' => 'products', 'new' => 1]],
    ['categories', [
        'title' => 'Catégories',
        'currentPage' => 'categories',
        'categories' => [$category],
        'editing' => null,
    ], 'app', ['page' => 'categories']],
    ['partners', [
        'title' => 'Clients',
        'currentPage' => 'customers',
        'partnerType' => 'customer',
        'partners' => array_replace($pagination, ['items' => [$partner]]),
        'editing' => null,
        'search' => '',
        'status' => 'active',
    ], 'app', ['page' => 'customers', 'new' => 1]],
    ['movements', [
        'title' => 'Mouvements',
        'currentPage' => 'movements',
        'movements' => array_replace($pagination, ['items' => [$movement], 'per_page' => 25]),
        'products' => [$product],
        'customers' => [$partner],
        'suppliers' => [array_replace($partner, ['id' => 2, 'type' => 'supplier', 'code' => 'SUP-001'])],
        'movementTypes' => ['purchase', 'sale', 'customer_return', 'supplier_return', 'adjustment_in', 'adjustment_out'],
        'type' => '',
        'search' => '',
        'status' => '',
        'productId' => null,
        'dateFrom' => '',
        'dateTo' => '',
    ], 'app', ['page' => 'movements', 'new' => 1]],
    ['reports', [
        'title' => 'Rapports',
        'currentPage' => 'reports',
        'stats' => [
            'products_count' => 1,
            'out_of_stock_count' => 0,
            'low_stock_count' => 1,
            'inventory_value' => 8000,
            'monthly_sales' => 3000,
            'monthly_purchases' => 0,
        ],
        'reorderSuggestions' => [[
            'id' => 1,
            'sku' => 'SKU-001',
            'barcode' => '1234567890123',
            'name' => 'Produit test',
            'location' => 'A-01',
            'unit' => 'piece',
            'stock_quantity' => 8,
            'reorder_level' => 10,
            'target_stock' => 20,
            'cost_price' => 1000,
            'category_name' => 'Catégorie test',
            'suggested_quantity' => 12,
            'suggested_value' => 12000,
        ]],
        'categorySummary' => [[
            'category_name' => 'Catégorie test',
            'products_count' => 1,
            'out_of_stock_count' => 0,
            'stock_value' => 8000,
        ]],
        'movementSummary' => [[
            'type' => 'sale',
            'unit' => 'piece',
            'movements_count' => 1,
            'total_quantity' => 2,
            'total_amount' => 3000,
        ]],
        'dateFrom' => '2026-07-01',
        'dateTo' => '2026-07-18',
    ], 'app', ['page' => 'reports']],
    ['users', [
        'title' => 'Utilisateurs',
        'currentPage' => 'users',
        'users' => [$fakeUser + ['created_at' => '2026-07-18 12:00:00']],
        'editing' => null,
    ], 'app', ['page' => 'users', 'new' => 1]],
    ['profile', [
        'title' => 'Profil',
        'currentPage' => 'profile',
        'profile' => $fakeUser,
    ], 'app', ['page' => 'profile']],
    ['search', [
        'title' => 'Recherche',
        'currentPage' => 'search',
        'term' => 'test',
        'results' => [
            'products' => [[
                'id' => 1,
                'code' => 'SKU-001',
                'barcode' => '1234567890123',
                'name' => 'Produit test',
                'stock_quantity' => 8,
                'active' => 1,
            ]],
            'partners' => [$partner],
            'movements' => [$movement],
        ],
    ], 'app', ['page' => 'search', 'q' => 'test']],
    ['receipt', [
        'title' => 'Reçu',
        'currentPage' => 'movements',
        'movement' => $movement,
    ], 'receipt', ['page' => 'receipt', 'id' => 1]],
    ['not-found', [
        'title' => 'Introuvable',
        'currentPage' => '',
    ], 'app', ['page' => 'unknown']],
];

set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    foreach ($cases as [$view, $data, $layout, $query]) {
        $_GET = $query;
        ob_start();
        render($view, $data, $layout);
        $html = (string) ob_get_clean();
        if (!str_contains($html, '<!doctype html>') || !str_contains($html, '</html>')) {
            throw new RuntimeException("Invalid rendered document for {$view}.");
        }
    }
    fwrite(STDOUT, 'View smoke tests passed: ' . count($cases) . PHP_EOL);
} catch (Throwable $exception) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    fwrite(STDERR, 'View smoke test failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
} finally {
    restore_error_handler();
}
