<?php
$locale = app('translator')->locale();
$flashes = pull_flashes();
?>
<!doctype html>
<html lang="<?= e($locale) ?>" data-theme="system">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <title><?= e($title ?? t('sign_in')) ?> — <?= e(config('name')) ?></title>
    <link rel="stylesheet" href="<?= e(asset('app.css')) ?>">
    <script src="<?= e(asset('app.js')) ?>" defer></script>
</head>
<body class="auth-body">
<main class="auth-shell">
    <section class="auth-brand-panel">
        <div class="auth-brand-content">
            <span class="auth-logo">I</span>
            <p class="eyebrow">IMS</p>
            <h1><?= e(config('name')) ?></h1>
            <p><?= e(t('app_tagline')) ?></p>
            <div class="auth-metrics" aria-hidden="true">
                <span><strong>100%</strong><small>PHP + MySQL</small></span>
                <span><strong>2</strong><small>FR / EN</small></span>
                <span><strong>24/7</strong><small>Traçabilité</small></span>
            </div>
        </div>
    </section>
    <section class="auth-form-panel">
        <div class="auth-toolbar">
            <a href="<?= e(route('login', ['locale' => 'fr'])) ?>" class="<?= $locale === 'fr' ? 'is-active' : '' ?>">FR</a>
            <a href="<?= e(route('login', ['locale' => 'en'])) ?>" class="<?= $locale === 'en' ? 'is-active' : '' ?>">EN</a>
            <button class="icon-button" type="button" data-theme-toggle aria-label="<?= e(t('toggle_theme')) ?>">◐</button>
        </div>
        <div class="auth-card">
            <?php foreach ($flashes as $flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>" role="status">
                    <span><?= e($flash['message']) ?></span>
                </div>
            <?php endforeach; ?>
            <?= $content ?>
        </div>
    </section>
</main>
</body>
</html>
