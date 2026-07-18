<?php

declare(strict_types=1);

function load_env(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if ($key === '' || getenv($key) !== false) {
            continue;
        }

        if (
            strlen($value) >= 2
            && (($value[0] === '"' && str_ends_with($value, '"'))
                || ($value[0] === "'" && str_ends_with($value, "'")))
        ) {
            $value = substr($value, 1, -1);
        }

        $_ENV[$key] = $value;
        putenv($key . '=' . $value);
    }
}

function env_string(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : (string) $value;
}

function env_bool(string $key, bool $default = false): bool
{
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }

    return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
}

function env_int(string $key, int $default = 0): int
{
    $value = getenv($key);
    return $value !== false && filter_var($value, FILTER_VALIDATE_INT) !== false ? (int) $value : $default;
}

function set_app(array $container): void
{
    $GLOBALS['ims'] = $container;
}

function app(?string $key = null): mixed
{
    $container = $GLOBALS['ims'] ?? [];
    return $key === null ? $container : ($container[$key] ?? null);
}

function config(string $key, mixed $default = null): mixed
{
    $value = app('config');
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function t(string $key, array $replace = []): string
{
    $translator = app('translator');
    return $translator instanceof IMS\Core\Translator ? $translator->get($key, $replace) : $key;
}

function route(string $page = 'dashboard', array $params = []): string
{
    $query = $page === '' ? $params : array_merge(['page' => $page], $params);
    return 'index.php' . ($query === [] ? '' : '?' . http_build_query($query));
}

function asset(string $path): string
{
    $prefix = defined('IMS_PUBLIC_PREFIX') ? IMS_PUBLIC_PREFIX : '';
    return $prefix . 'assets/' . ltrim($path, '/');
}

function redirect(string $page = 'dashboard', array $params = []): never
{
    header('Location: ' . route($page, $params), true, 303);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['_flashes'][] = ['type' => $type, 'message' => $message];
}

function pull_flashes(): array
{
    $flashes = $_SESSION['_flashes'] ?? [];
    unset($_SESSION['_flashes']);
    return is_array($flashes) ? $flashes : [];
}

function csrf_field(): string
{
    $csrf = app('csrf');
    return $csrf instanceof IMS\Core\Csrf
        ? '<input type="hidden" name="_token" value="' . e($csrf->token()) . '">'
        : '';
}

function money(float|int|string|null $amount): string
{
    $decimals = (int) config('currency_decimals', 0);
    $translator = app('translator');
    $locale = $translator instanceof IMS\Core\Translator ? $translator->locale() : 'fr';
    $formatted = number_format(
        (float) ($amount ?? 0),
        $decimals,
        $locale === 'fr' ? ',' : '.',
        $locale === 'fr' ? ' ' : ','
    );
    return $formatted . ' ' . e((string) config('currency', 'XAF'));
}

function quantity(float|int|string|null $amount): string
{
    $number = number_format((float) ($amount ?? 0), 2, '.', '');
    return rtrim(rtrim($number, '0'), '.');
}

function local_date(?string $date, bool $withTime = false): string
{
    if (!$date) {
        return '—';
    }
    $timestamp = strtotime($date);
    return $timestamp === false ? '—' : date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $timestamp);
}

function selected(mixed $value, mixed $expected): string
{
    return (string) $value === (string) $expected ? ' selected' : '';
}

function checked(bool $condition): string
{
    return $condition ? ' checked' : '';
}

function render(string $view, array $data = [], string $layout = 'app'): void
{
    $viewFile = BASE_PATH . '/app/Views/pages/' . $view . '.php';
    $layoutFile = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
    if (!is_file($viewFile) || !is_file($layoutFile)) {
        throw new RuntimeException('View not found: ' . $view);
    }

    extract($data, EXTR_SKIP);
    ob_start();
    require $viewFile;
    $content = (string) ob_get_clean();
    require $layoutFile;
}

function page_link(array $pagination, int $page): string
{
    $params = $_GET;
    unset($params['page']);
    $view = (string) ($_GET['page'] ?? 'dashboard');
    $params['p'] = $page;
    return route($view, $params);
}

function icon(string $name, int $size = 20): string
{
    $paths = [
        'dashboard' => '<path d="M3 3h7v7H3V3Zm11 0h7v7h-7V3ZM3 14h7v7H3v-7Zm11 0h7v7h-7v-7Z"/>',
        'box' => '<path d="m12 2 9 4.5v11L12 22l-9-4.5v-11L12 2Zm0 2.2L6.1 7.1 12 10l5.9-2.9L12 4.2ZM5 8.7v7.6l6 3v-7.6l-6-3Zm14 0-6 3v7.6l6-3V8.7Z"/>',
        'category' => '<path d="M4 3h6a1 1 0 0 1 1 1v6H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Zm10 0h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1h-7V4a1 1 0 0 1 1-1ZM4 13h7v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-6a1 1 0 0 1 1-1Zm9 0h7a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-6a1 1 0 0 1-1-1v-7Z"/>',
        'users' => '<path d="M16 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 1c-2.7 0-8 1.35-8 4v3h16v-3c0-2.65-5.3-4-8-4ZM8 14c-3.1 0-8 1.55-8 4.5V20h6v-3c0-1.1.45-2.08 1.2-2.9L8 14Z"/>',
        'truck' => '<path d="M3 4h12v11H3V4Zm14 4h3l4 4v3h-2a3 3 0 0 1-6 0h-1V8h2Zm-11 9a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm11.8-7v2H21l-2-2h-1.2Z"/>',
        'swap' => '<path d="M7 7h11l-3-3 1.4-1.4L21.8 8l-5.4 5.4L15 12l3-3H7V7Zm10 10H6l3 3-1.4 1.4L2.2 16l5.4-5.4L9 12l-3 3h11v2Z"/>',
        'chart' => '<path d="M4 20V10h4v10H4Zm6 0V4h4v16h-4Zm6 0v-7h4v7h-4ZM2 22h20v-2H2v2Z"/>',
        'search' => '<path d="M10.5 3a7.5 7.5 0 1 0 4.7 13.35L20.85 22 22 20.85l-5.65-5.65A7.5 7.5 0 0 0 10.5 3Zm0 2a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Z"/>',
        'settings' => '<path d="M19.4 13a7.8 7.8 0 0 0 .05-1l2.1-1.65-2-3.46-2.48 1a8.2 8.2 0 0 0-1.72-1L15 4.25h-4l-.4 2.64a8.2 8.2 0 0 0-1.72 1l-2.48-1-2 3.46L6.5 12a7.8 7.8 0 0 0 .05 1L4.4 14.65l2 3.46 2.48-1a8.2 8.2 0 0 0 1.72 1l.4 2.64h4l.4-2.64a8.2 8.2 0 0 0 1.72-1l2.48 1 2-3.46L19.4 13ZM13 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z"/>',
        'logout' => '<path d="M10 3H3v18h7v-2H5V5h5V3Zm7.6 4.6L16.2 9l2 2H9v2h9.2l-2 2 1.4 1.4L22 12l-4.4-4.4Z"/>',
        'menu' => '<path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z"/>',
        'plus' => '<path d="M11 3h2v8h8v2h-8v8h-2v-8H3v-2h8V3Z"/>',
        'download' => '<path d="M11 3h2v10.2l3.6-3.6L18 11l-6 6-6-6 1.4-1.4 3.6 3.6V3ZM4 19h16v2H4v-2Z"/>',
        'print' => '<path d="M6 3h12v5H6V3Zm-2 7h16a2 2 0 0 1 2 2v6h-4v3H6v-3H2v-6a2 2 0 0 1 2-2Zm4 6v3h8v-5H8v2Z"/>',
    ];
    $path = $paths[$name] ?? $paths['box'];
    return '<svg class="icon" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" aria-hidden="true">' . $path . '</svg>';
}
