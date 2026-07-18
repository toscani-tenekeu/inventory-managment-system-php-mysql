<?php
$auth = app('auth');
$user = $auth->user();
$locale = app('translator')->locale();
$flashes = pull_flashes();
$currentPage = $currentPage ?? '';
$navItems = [
    ['dashboard', 'nav_dashboard', 'dashboard'],
    ['products', 'nav_products', 'box'],
    ['categories', 'nav_categories', 'category'],
    ['customers', 'nav_customers', 'users'],
    ['suppliers', 'nav_suppliers', 'truck'],
    ['movements', 'nav_movements', 'swap'],
    ['reports', 'nav_reports', 'chart'],
];
?>
<!doctype html>
<html lang="<?= e($locale) ?>" data-theme="<?= e((string) ($user['theme'] ?? 'system')) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title><?= e($title ?? config('name')) ?> — <?= e(config('name')) ?></title>
    <link rel="stylesheet" href="<?= e(asset('app.css')) ?>">
    <script src="<?= e(asset('app.js')) ?>" defer></script>
</head>
<body class="app-body">
<div class="app-frame">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="<?= e(route('dashboard')) ?>">
            <span class="brand-mark" aria-hidden="true">I</span>
            <span>
                <strong>IMS</strong>
                <small>Inventory Management</small>
            </span>
        </a>

        <nav class="sidebar-nav" aria-label="Navigation principale">
            <?php foreach ($navItems as [$navPage, $label, $navIcon]): ?>
                <a class="nav-item<?= $currentPage === $navPage ? ' is-active' : '' ?>"
                   href="<?= e(route($navPage)) ?>"<?= $currentPage === $navPage ? ' aria-current="page"' : '' ?>>
                    <?= icon($navIcon) ?>
                    <span><?= e(t($label)) ?></span>
                </a>
            <?php endforeach; ?>

            <?php if ($auth->isAdmin()): ?>
                <div class="nav-separator"></div>
                <a class="nav-item<?= $currentPage === 'users' ? ' is-active' : '' ?>"
                   href="<?= e(route('users')) ?>"<?= $currentPage === 'users' ? ' aria-current="page"' : '' ?>>
                    <?= icon('settings') ?>
                    <span><?= e(t('nav_users')) ?></span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <a class="profile-summary" href="<?= e(route('profile')) ?>">
                <span class="avatar"><?= e(mb_strtoupper(mb_substr((string) $user['name'], 0, 1))) ?></span>
                <span class="profile-copy">
                    <strong><?= e($user['name']) ?></strong>
                    <small><?= e(t($user['role'] === 'admin' ? 'administrator' : 'operator')) ?></small>
                </span>
            </a>
            <form method="post" action="<?= e(route($currentPage ?: 'dashboard')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="logout">
                <button class="nav-item nav-button" type="submit">
                    <?= icon('logout') ?>
                    <span><?= e(t('logout')) ?></span>
                </button>
            </form>
        </div>
    </aside>

    <button class="sidebar-overlay" type="button" data-sidebar-close aria-label="<?= e(t('open_menu')) ?>"></button>

    <div class="app-main">
        <header class="topbar">
            <button class="icon-button mobile-menu" type="button" data-sidebar-toggle aria-label="<?= e(t('open_menu')) ?>">
                <?= icon('menu', 22) ?>
            </button>
            <form class="global-search" method="get" action="index.php" role="search">
                <input type="hidden" name="page" value="search">
                <?= icon('search', 18) ?>
                <input name="q" type="search" minlength="2" maxlength="100"
                       placeholder="<?= e(t('global_search')) ?>" aria-label="<?= e(t('search')) ?>">
            </form>
            <div class="topbar-actions">
                <div class="language-switch" aria-label="<?= e(t('language')) ?>">
                    <a href="<?= e(route($currentPage ?: 'dashboard', ['locale' => 'fr'])) ?>"
                       class="<?= $locale === 'fr' ? 'is-active' : '' ?>">FR</a>
                    <a href="<?= e(route($currentPage ?: 'dashboard', ['locale' => 'en'])) ?>"
                       class="<?= $locale === 'en' ? 'is-active' : '' ?>">EN</a>
                </div>
                <button class="icon-button" type="button" data-theme-toggle aria-label="<?= e(t('toggle_theme')) ?>">
                    <span class="theme-icon" aria-hidden="true">◐</span>
                </button>
                <a class="topbar-avatar" href="<?= e(route('profile')) ?>" aria-label="<?= e(t('nav_profile')) ?>">
                    <?= e(mb_strtoupper(mb_substr((string) $user['name'], 0, 1))) ?>
                </a>
            </div>
        </header>

        <main class="content">
            <?php foreach ($flashes as $flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>" role="status">
                    <span><?= e($flash['message']) ?></span>
                    <button type="button" data-alert-close aria-label="Fermer">×</button>
                </div>
            <?php endforeach; ?>
            <?= $content ?>
        </main>
    </div>
</div>
</body>
</html>
