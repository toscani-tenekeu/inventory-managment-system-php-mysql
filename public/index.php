<?php

declare(strict_types=1);

use IMS\Core\AuthorizationException;
use IMS\Core\ValidationException;
use IMS\Domain\MovementType;

defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));

try {
    /** @var array{auth: IMS\Core\Auth, csrf: IMS\Core\Csrf, repository: IMS\Domain\InventoryRepository, service: IMS\Domain\InventoryService} $container */
    $container = require BASE_PATH . '/app/bootstrap.php';
} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);
    $debug = filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOL);
    $detail = $debug ? '<pre>' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>' : '';
    echo '<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Inventory Management System — Configuration</title><style>body{margin:0;background:#f5f7fa;color:#242424;font:16px system-ui;display:grid;place-items:center;min-height:100vh}'
        . 'main{max-width:620px;margin:24px;padding:32px;background:#fff;border:1px solid #ddd;border-radius:16px;box-shadow:0 12px 40px #0001}'
        . 'h1{font-size:24px}code,pre{background:#f1f1f1;padding:3px 6px;border-radius:5px}pre{padding:12px;overflow:auto}</style>'
        . '<main><h1>Configuration requise</h1><p>Vérifiez le fichier <code>.env</code>, puis importez <code>db/schema.sql</code> dans MySQL.</p>'
        . $detail . '</main></html>';
    exit;
}

$auth = $container['auth'];
$csrf = $container['csrf'];
$repository = $container['repository'];
$service = $container['service'];

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; font-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$page = preg_replace('/[^a-z-]/', '', strtolower((string) ($_GET['page'] ?? 'dashboard'))) ?: 'dashboard';
$action = preg_replace('/[^a-z-]/', '', strtolower((string) ($_POST['action'] ?? ''))) ?: '';

if ($method === 'POST') {
    $returnPage = match ($action) {
        'login' => 'login',
        'product-save', 'product-toggle' => 'products',
        'category-save', 'category-toggle' => 'categories',
        'partner-save', 'partner-toggle' => post_value('partner_type') === 'supplier' ? 'suppliers' : 'customers',
        'movement-create', 'movement-reverse' => 'movements',
        'user-save', 'user-toggle' => 'users',
        'profile-save' => 'profile',
        default => 'dashboard',
    };

    try {
        if (!$csrf->validate(isset($_POST['_token']) ? (string) $_POST['_token'] : null)) {
            throw new ValidationException('invalid_session');
        }

        switch ($action) {
            case 'login':
                $email = required_string('email', 190);
                $password = required_string('password', 500, false);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationException('invalid_email');
                }
                if (!$auth->attempt($email, $password)) {
                    throw new ValidationException('invalid_credentials');
                }
                $csrf->regenerate();
                redirect('dashboard');

            case 'logout':
                $auth->requireLogin();
                $auth->logout();
                redirect('login');

            case 'product-save':
                $user = $auth->requireLogin();
                $id = optional_id('id');
                $data = validate_product();
                $service->saveProduct($data, $id, decimal_value('initial_stock', 0), (int) $user['id']);
                flash('success', t('product_saved'));
                redirect('products');

            case 'product-toggle':
                $user = $auth->requireLogin();
                $auth->authorize('catalog.archive');
                $id = required_id('id');
                $service->toggleProduct($id, (int) $user['id']);
                flash('success', t('product_toggled'));
                redirect('products', ['status' => 'all']);

            case 'category-save':
                $user = $auth->requireLogin();
                $id = optional_id('id');
                $categoryId = $repository->saveCategory([
                    'name' => required_string('name', 120),
                    'description' => optional_string('description', 500),
                ], $id);
                $repository->audit($id ? 'updated' : 'created', 'category', $categoryId, [], (int) $user['id']);
                flash('success', t('category_saved'));
                redirect('categories');

            case 'category-toggle':
                $user = $auth->requireLogin();
                $auth->authorize('catalog.archive');
                $id = required_id('id');
                if (!$repository->toggleCategory($id)) {
                    throw new ValidationException('operation_failed');
                }
                $repository->audit('status_changed', 'category', $id, [], (int) $user['id']);
                flash('success', t('category_toggled'));
                redirect('categories');

            case 'partner-save':
                $user = $auth->requireLogin();
                $id = optional_id('id');
                $partnerType = post_value('partner_type');
                if (!in_array($partnerType, ['customer', 'supplier'], true)) {
                    throw new ValidationException('required_fields');
                }
                if ($id !== null) {
                    $existingPartner = $repository->partner($id);
                    if (!$existingPartner || $existingPartner['type'] !== $partnerType) {
                        throw new ValidationException('invalid_partner');
                    }
                }
                $email = optional_string('email', 190);
                if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationException('invalid_email');
                }
                $partnerId = $repository->savePartner([
                    'type' => $partnerType,
                    'code' => strtoupper(required_string('code', 64)),
                    'name' => required_string('name', 160),
                    'contact_name' => optional_string('contact_name', 160),
                    'email' => mb_strtolower($email),
                    'phone' => optional_string('phone', 50),
                    'address' => optional_string('address', 500),
                ], $id);
                $repository->audit($id ? 'updated' : 'created', 'partner', $partnerId, ['type' => $partnerType], (int) $user['id']);
                flash('success', t('partner_saved'));
                redirect($partnerType === 'supplier' ? 'suppliers' : 'customers');

            case 'partner-toggle':
                $user = $auth->requireLogin();
                $auth->authorize('catalog.archive');
                $id = required_id('id');
                $partnerType = post_value('partner_type') === 'supplier' ? 'supplier' : 'customer';
                if (!$repository->partner($id, $partnerType)) {
                    throw new ValidationException('invalid_partner');
                }
                if (!$repository->togglePartner($id)) {
                    throw new ValidationException('operation_failed');
                }
                $repository->audit('status_changed', 'partner', $id, ['type' => $partnerType], (int) $user['id']);
                flash('success', t('partner_toggled'));
                redirect($partnerType === 'supplier' ? 'suppliers' : 'customers', ['status' => 'all']);

            case 'movement-create':
                $user = $auth->requireLogin();
                $service->createMovement([
                    'type' => post_value('type'),
                    'product_id' => required_id('product_id'),
                    'partner_id' => optional_id('partner_id'),
                    'quantity' => decimal_value('quantity'),
                    'unit_price' => decimal_value('unit_price', 0),
                    'reference' => optional_string('reference', 64),
                    'notes' => optional_string('notes', 1000),
                    'occurred_at' => optional_string('occurred_at', 16),
                ], (int) $user['id']);
                flash('success', t('movement_saved'));
                redirect('movements');

            case 'movement-reverse':
                $user = $auth->requireLogin();
                $auth->authorize('movements.reverse');
                $service->reverseMovement(required_id('id'), (int) $user['id']);
                flash('success', t('movement_reversed'));
                redirect('movements');

            case 'user-save':
                $currentUser = $auth->requireLogin();
                $auth->authorize('users.manage');
                $id = optional_id('id');
                $role = post_value('role');
                $locale = post_value('locale');
                $password = optional_string('password', 500, false);
                if (!in_array($role, ['admin', 'user'], true) || !in_array($locale, ['fr', 'en'], true)) {
                    throw new ValidationException('required_fields');
                }
                $email = mb_strtolower(required_string('email', 190));
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationException('invalid_email');
                }
                if ($id === null && $password === '') {
                    throw new ValidationException('password_required');
                }
                if ($password !== '' && !valid_password($password)) {
                    throw new ValidationException('invalid_password');
                }
                if ($id !== null) {
                    $target = $repository->user($id);
                    if (!$target) {
                        throw new ValidationException('operation_failed');
                    }
                    if ($target['role'] === 'admin' && $role !== 'admin' && $repository->activeAdminCount() <= 1) {
                        throw new ValidationException('last_admin_required');
                    }
                }
                $savedId = $repository->saveUser([
                    'name' => required_string('name', 120),
                    'email' => $email,
                    'role' => $role,
                    'locale' => $locale,
                    'password' => $password,
                ], $id);
                $repository->audit($id ? 'updated' : 'created', 'user', $savedId, ['role' => $role], (int) $currentUser['id']);
                flash('success', t('user_saved'));
                redirect('users');

            case 'user-toggle':
                $currentUser = $auth->requireLogin();
                $auth->authorize('users.manage');
                $id = required_id('id');
                if ($id === (int) $currentUser['id']) {
                    throw new ValidationException('cannot_disable_self');
                }
                $target = $repository->user($id);
                if (!$target) {
                    throw new ValidationException('operation_failed');
                }
                if ($target['role'] === 'admin' && (int) $target['active'] === 1 && $repository->activeAdminCount() <= 1) {
                    throw new ValidationException('last_admin_required');
                }
                $repository->toggleUser($id);
                $repository->audit('status_changed', 'user', $id, [], (int) $currentUser['id']);
                flash('success', t('user_toggled'));
                redirect('users');

            case 'profile-save':
                $user = $auth->requireLogin();
                $email = mb_strtolower(required_string('email', 190));
                $locale = post_value('locale');
                $theme = post_value('theme');
                $password = optional_string('password', 500, false);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationException('invalid_email');
                }
                if (!in_array($locale, ['fr', 'en'], true) || !in_array($theme, ['system', 'light', 'dark'], true)) {
                    throw new ValidationException('required_fields');
                }
                if ($password !== '' && !valid_password($password)) {
                    throw new ValidationException('invalid_password');
                }
                $repository->updateProfile((int) $user['id'], [
                    'name' => required_string('name', 120),
                    'email' => $email,
                    'locale' => $locale,
                    'theme' => $theme,
                    'password' => $password,
                ]);
                $repository->audit('updated', 'profile', (int) $user['id'], [], (int) $user['id']);
                $_SESSION['locale'] = $locale;
                flash('success', t('profile_saved'));
                redirect('profile');

            default:
                throw new ValidationException('operation_failed');
        }
    } catch (AuthorizationException $exception) {
        if (!$auth->user()) {
            flash('error', t('authentication_required'));
            redirect('login');
        }
        flash('error', t('forbidden'));
        redirect('dashboard');
    } catch (ValidationException $exception) {
        flash('error', t($exception->getMessage()));
        redirect($returnPage);
    } catch (PDOException $exception) {
        error_log((string) $exception);
        flash('error', $exception->getCode() === '23000' ? t('duplicate_value') : t('operation_failed'));
        redirect($returnPage);
    } catch (Throwable $exception) {
        error_log((string) $exception);
        flash('error', t('operation_failed'));
        redirect($returnPage);
    }
}

try {
    $currentUser = $auth->user();
    if (!$currentUser) {
        if ($page !== 'login') {
            redirect('login');
        }
        render('login', ['title' => t('sign_in')], 'auth');
        exit;
    }

    if ($page === 'login') {
        redirect('dashboard');
    }

    switch ($page) {
        case 'dashboard':
            render('dashboard', [
                'title' => t('dashboard_title'),
                'currentPage' => 'dashboard',
                'stats' => $repository->dashboardStats(),
                'recentMovements' => $repository->recentMovements(),
                'lowStockProducts' => $repository->lowStockProducts(),
                'topProducts' => $repository->topSellingProducts(),
            ]);
            break;

        case 'products':
            $search = clean_query('q', 100);
            $status = clean_query('status', 20) ?: 'active';
            $stockStatus = clean_query('stock', 20);
            $categoryId = query_id('category');
            $editId = query_id('edit');
            render('products', [
                'title' => t('products_title'),
                'currentPage' => 'products',
                'products' => $repository->products(
                    $search,
                    $status,
                    query_page(),
                    20,
                    $categoryId,
                    $stockStatus
                ),
                'categories' => $repository->categories(),
                'editing' => $editId ? $repository->product($editId) : null,
                'search' => $search,
                'status' => $status,
                'stockStatus' => $stockStatus,
                'categoryId' => $categoryId,
            ]);
            break;

        case 'categories':
            $editId = query_id('edit');
            render('categories', [
                'title' => t('categories_title'),
                'currentPage' => 'categories',
                'categories' => $repository->categories(),
                'editing' => $editId ? $repository->category($editId) : null,
            ]);
            break;

        case 'customers':
        case 'suppliers':
            $type = $page === 'suppliers' ? 'supplier' : 'customer';
            $search = clean_query('q', 100);
            $status = clean_query('status', 20) ?: 'active';
            $editId = query_id('edit');
            render('partners', [
                'title' => t($page . '_title'),
                'currentPage' => $page,
                'partnerType' => $type,
                'partners' => $repository->partners($type, $search, $status, query_page(), 20),
                'editing' => $editId ? $repository->partner($editId, $type) : null,
                'search' => $search,
                'status' => $status,
            ]);
            break;

        case 'movements':
            $type = clean_query('type', 30);
            $search = clean_query('q', 100);
            $status = clean_query('status', 20);
            $productId = query_id('product');
            $dateFrom = clean_date_query('from');
            $dateTo = clean_date_query('to');
            if ($dateFrom !== '' && $dateTo !== '' && $dateFrom > $dateTo) {
                [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
            }
            render('movements', [
                'title' => t('movements_title'),
                'currentPage' => 'movements',
                'movements' => $repository->movements(
                    $type,
                    $search,
                    $status,
                    query_page(),
                    25,
                    $productId,
                    $dateFrom,
                    $dateTo
                ),
                'products' => $repository->activeProducts(),
                'customers' => $repository->activePartners('customer'),
                'suppliers' => $repository->activePartners('supplier'),
                'movementTypes' => MovementType::all(),
                'type' => $type,
                'search' => $search,
                'status' => $status,
                'productId' => $productId,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);
            break;

        case 'reports':
            $dateFrom = clean_date_query('from') ?: date('Y-m-01');
            $dateTo = clean_date_query('to') ?: date('Y-m-d');
            if ($dateFrom > $dateTo) {
                [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
            }
            render('reports', [
                'title' => t('reports_title'),
                'currentPage' => 'reports',
                'stats' => $repository->dashboardStats(),
                'reorderSuggestions' => $repository->reorderSuggestions(),
                'categorySummary' => $repository->categoryInventorySummary(),
                'movementSummary' => $repository->movementSummary($dateFrom, $dateTo),
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]);
            break;

        case 'receipt':
            $movement = $repository->movement(query_id('id') ?? 0);
            if (!$movement) {
                throw new ValidationException('movement_not_found');
            }
            render('receipt', [
                'title' => t('receipt_title'),
                'currentPage' => 'movements',
                'movement' => $movement,
            ], 'receipt');
            break;

        case 'users':
            $auth->authorize('users.manage');
            $editId = query_id('edit');
            render('users', [
                'title' => t('users_title'),
                'currentPage' => 'users',
                'users' => $repository->users(),
                'editing' => $editId ? $repository->user($editId) : null,
            ]);
            break;

        case 'profile':
            render('profile', [
                'title' => t('profile_title'),
                'currentPage' => 'profile',
                'profile' => $currentUser,
            ]);
            break;

        case 'search':
            $term = clean_query('q', 100);
            render('search', [
                'title' => t('search_title'),
                'currentPage' => 'search',
                'term' => $term,
                'results' => mb_strlen($term) >= 2 ? $repository->search($term) : null,
            ]);
            break;

        case 'export':
            export_csv(clean_query('resource', 20), $repository);
            break;

        default:
            http_response_code(404);
            render('not-found', ['title' => t('not_found'), 'currentPage' => '']);
    }
} catch (AuthorizationException) {
    http_response_code(403);
    flash('error', t('forbidden'));
    redirect('dashboard');
} catch (ValidationException $exception) {
    flash('error', t($exception->getMessage()));
    redirect('movements');
} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);
    flash('error', t('operation_failed'));
    redirect('dashboard');
}

function post_value(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

function required_string(string $key, int $max, bool $trim = true): string
{
    $value = (string) ($_POST[$key] ?? '');
    $value = $trim ? trim($value) : $value;
    if ($value === '' || mb_strlen($value) > $max) {
        throw new ValidationException('required_fields');
    }
    return $value;
}

function optional_string(string $key, int $max, bool $trim = true): string
{
    $value = (string) ($_POST[$key] ?? '');
    $value = $trim ? trim($value) : $value;
    if (mb_strlen($value) > $max) {
        throw new ValidationException('required_fields');
    }
    return $value;
}

function required_id(string $key): int
{
    $id = filter_var($_POST[$key] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        throw new ValidationException('required_fields');
    }
    return (int) $id;
}

function optional_id(string $key): ?int
{
    $value = $_POST[$key] ?? null;
    if ($value === null || $value === '') {
        return null;
    }
    $id = filter_var($value, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        throw new ValidationException('required_fields');
    }
    return (int) $id;
}

function decimal_value(string $key, ?float $default = null): float
{
    $raw = str_replace(',', '.', trim((string) ($_POST[$key] ?? '')));
    if ($raw === '' && $default !== null) {
        return $default;
    }
    if ($raw === '' || !is_numeric($raw)) {
        throw new ValidationException('required_fields');
    }
    return (float) $raw;
}

function valid_password(string $password): bool
{
    return strlen($password) >= 10
        && preg_match('/[a-z]/', $password) === 1
        && preg_match('/[A-Z]/', $password) === 1
        && preg_match('/[0-9]/', $password) === 1;
}

function validate_product(): array
{
    $sku = strtoupper(required_string('sku', 64));
    if (preg_match('/^[A-Z0-9._\/-]+$/', $sku) !== 1) {
        throw new ValidationException('invalid_reference');
    }

    $barcode = optional_string('barcode', 80);
    if ($barcode !== '' && preg_match('/^[A-Za-z0-9._\/-]+$/', $barcode) !== 1) {
        throw new ValidationException('invalid_barcode');
    }

    $unit = post_value('unit');
    if (!in_array($unit, ['piece', 'kilogram', 'liter', 'meter', 'box'], true)) {
        throw new ValidationException('required_fields');
    }

    $manufactured = post_value('manufactured_at');
    $expires = post_value('expires_at');
    foreach ([$manufactured, $expires] as $date) {
        if ($date === '') {
            continue;
        }
        $parsedDate = DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date) {
            throw new ValidationException('invalid_date_range');
        }
    }
    if ($manufactured !== '' && $expires !== '' && $expires <= $manufactured) {
        throw new ValidationException('invalid_date_range');
    }

    $cost = decimal_value('cost_price', 0);
    $sale = decimal_value('sale_price', 0);
    $reorder = decimal_value('reorder_level', 0);
    $targetStock = decimal_value('target_stock', 0);
    $initialStock = decimal_value('initial_stock', 0);
    if (
        $cost < 0 || $cost > 999999999999
        || $sale < 0 || $sale > 999999999999
        || $reorder < 0 || $reorder > 9999999999
        || $targetStock < 0 || $targetStock > 9999999999
        || $initialStock < 0 || $initialStock > 9999999999
    ) {
        throw new ValidationException('invalid_movement_values');
    }
    if ($targetStock > 0 && $targetStock < $reorder) {
        throw new ValidationException('invalid_stock_levels');
    }

    return [
        'sku' => $sku,
        'barcode' => $barcode,
        'name' => required_string('name', 160),
        'description' => optional_string('description', 5000),
        'location' => optional_string('location', 120),
        'category_id' => optional_id('category_id'),
        'unit' => $unit,
        'cost_price' => $cost,
        'sale_price' => $sale,
        'reorder_level' => $reorder,
        'target_stock' => $targetStock,
        'manufactured_at' => $manufactured,
        'expires_at' => $expires,
    ];
}

function clean_query(string $key, int $max): string
{
    return mb_substr(trim((string) ($_GET[$key] ?? '')), 0, $max);
}

function query_id(string $key): ?int
{
    $id = filter_var($_GET[$key] ?? null, FILTER_VALIDATE_INT);
    return $id && $id > 0 ? (int) $id : null;
}

function query_page(): int
{
    $page = filter_var($_GET['p'] ?? 1, FILTER_VALIDATE_INT);
    return $page && $page > 0 ? (int) $page : 1;
}

function clean_date_query(string $key): string
{
    $value = trim((string) ($_GET[$key] ?? ''));
    if ($value === '') {
        return '';
    }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    $errors = DateTimeImmutable::getLastErrors();
    if (!$date || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
        return '';
    }
    return $date->format('Y-m-d');
}

function export_csv(string $resource, IMS\Domain\InventoryRepository $repository): never
{
    $rows = match ($resource) {
        'products' => $repository->exportProducts(),
        'movements' => $repository->exportMovements(),
        'reorder' => $repository->exportReorderSuggestions(),
        default => throw new ValidationException('not_found'),
    };

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="ims-' . $resource . '-' . date('Y-m-d') . '.csv"');
    header('Cache-Control: no-store');
    $stream = fopen('php://output', 'wb');
    fwrite($stream, "\xEF\xBB\xBF");
    if ($rows !== []) {
        fputcsv($stream, array_keys($rows[0]), ';');
        foreach ($rows as $row) {
            fputcsv($stream, array_map('csv_safe', array_values($row)), ';');
        }
    }
    fclose($stream);
    exit;
}

function csv_safe(mixed $value): string
{
    $value = (string) ($value ?? '');
    return preg_match('/^[=+\-@]/', $value) === 1 ? "'" . $value : $value;
}
