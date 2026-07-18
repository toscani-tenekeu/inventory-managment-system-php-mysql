<?php $locale = app('translator')->locale(); ?>
<!doctype html>
<html lang="<?= e($locale) ?>" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? t('receipt_title')) ?> — <?= e(config('name')) ?></title>
    <link rel="stylesheet" href="<?= e(asset('app.css')) ?>">
    <script src="<?= e(asset('app.js')) ?>" defer></script>
</head>
<body class="receipt-body">
<?= $content ?>
</body>
</html>
