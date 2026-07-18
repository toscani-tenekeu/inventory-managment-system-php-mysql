<?php

declare(strict_types=1);

defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$failures = 0;
$check = static function (bool $condition, string $label, string $detail = '') use (&$failures): void {
    $prefix = $condition ? '[OK]  ' : '[FAIL]';
    fwrite($condition ? STDOUT : STDERR, $prefix . ' ' . $label . ($detail ? ' — ' . $detail : '') . PHP_EOL);
    if (!$condition) {
        $failures++;
    }
};

$check(version_compare(PHP_VERSION, '8.2.0', '>='), 'PHP 8.2+', PHP_VERSION);
$check(extension_loaded('pdo_mysql'), 'Extension pdo_mysql');
$check(extension_loaded('mbstring'), 'Extension mbstring');
$check(is_file(BASE_PATH . '/.env'), 'Fichier .env');
$check(is_writable(BASE_PATH . '/storage/logs'), 'Répertoire storage/logs accessible en écriture');

if ($failures === 0) {
    try {
        $container = require BASE_PATH . '/app/bootstrap.php';
        $requiredTables = [
            'users', 'categories', 'products', 'partners', 'stock_movements',
            'audit_logs', 'schema_migrations',
        ];
        $statement = $container['pdo']->prepare(
            'SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = :database_name AND table_name = :table_name'
        );
        foreach ($requiredTables as $table) {
            $statement->execute(['database_name' => config('db.name'), 'table_name' => $table]);
            $check((int) $statement->fetchColumn() === 1, 'Table ' . $table);
        }

        $columns = $container['pdo']->prepare(
            'SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = :database_name AND table_name = :table_name AND column_name = :column_name'
        );
        foreach ([
            ['products', 'barcode'],
            ['products', 'location'],
            ['products', 'target_stock'],
            ['stock_movements', 'occurred_at'],
        ] as [$table, $column]) {
            $columns->execute([
                'database_name' => config('db.name'),
                'table_name' => $table,
                'column_name' => $column,
            ]);
            $check((int) $columns->fetchColumn() === 1, 'Colonne ' . $table . '.' . $column);
        }

        $migration = $container['pdo']->prepare(
            'SELECT COUNT(*) FROM schema_migrations WHERE version = :version'
        );
        $migration->execute(['version' => '2026-07-classic-inventory']);
        $check((int) $migration->fetchColumn() === 1, 'Migration 2026-07-classic-inventory');

        $negativeStock = (int) $container['pdo']->query(
            'SELECT COUNT(*) FROM products WHERE stock_quantity < 0'
        )->fetchColumn();
        $check($negativeStock === 0, 'Aucun stock négatif', (string) $negativeStock);

        $integrityIssues = $container['repository']->stockIntegrityIssues();
        $check(
            $integrityIssues === [],
            'Cohérence stock / registre',
            $integrityIssues === [] ? '' : count($integrityIssues) . ' écart(s)'
        );
    } catch (Throwable $exception) {
        $check(false, 'Connexion et schéma MySQL', $exception->getMessage());
    }
}

exit($failures === 0 ? 0 : 1);
