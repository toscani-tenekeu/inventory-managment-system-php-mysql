<header class="page-header">
    <div>
        <p class="eyebrow"><?= e(t('search')) ?></p>
        <h1><?= e(t('search_title')) ?></h1>
        <p><?= $term !== '' ? e(t('search_for', ['term' => $term])) : e(t('search_prompt')) ?></p>
    </div>
</header>

<form class="search-page-form" method="get" action="index.php">
    <input type="hidden" name="page" value="search">
    <?= icon('search', 22) ?>
    <input name="q" type="search" minlength="2" maxlength="100" value="<?= e($term) ?>"
           placeholder="<?= e(t('global_search')) ?>" autofocus>
    <button class="button button-primary" type="submit"><?= e(t('search')) ?></button>
</form>

<?php if ($results === null): ?>
    <section class="panel empty-page-state">
        <?= icon('search', 38) ?>
        <h2><?= e(t('search_prompt')) ?></h2>
    </section>
<?php else: ?>
    <?php $totalResults = count($results['products']) + count($results['partners']) + count($results['movements']); ?>
    <?php if ($totalResults === 0): ?>
        <section class="panel empty-page-state">
            <?= icon('search', 38) ?>
            <h2><?= e(t('no_results')) ?></h2>
            <p><?= e(t('search_for', ['term' => $term])) ?></p>
        </section>
    <?php else: ?>
        <div class="search-results-grid">
            <section class="panel">
                <div class="panel-header"><h2><?= e(t('nav_products')) ?></h2><span class="count-badge"><?= e((string) count($results['products'])) ?></span></div>
                <div class="result-list">
                    <?php foreach ($results['products'] as $item): ?>
                        <a href="<?= e(route('products', ['edit' => $item['id']])) ?>" class="result-item">
                            <span class="result-icon"><?= icon('box', 18) ?></span>
                            <span><strong><?= e($item['name']) ?></strong><small><?= e($item['code']) ?><?= $item['barcode'] ? ' · ' . e($item['barcode']) : '' ?> · <?= e(quantity($item['stock_quantity'])) ?></small></span>
                            <span>→</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
            <section class="panel">
                <div class="panel-header"><h2><?= e(t('partners')) ?></h2><span class="count-badge"><?= e((string) count($results['partners'])) ?></span></div>
                <div class="result-list">
                    <?php foreach ($results['partners'] as $item): ?>
                        <a href="<?= e(route($item['type'] === 'supplier' ? 'suppliers' : 'customers', ['edit' => $item['id']])) ?>" class="result-item">
                            <span class="result-icon"><?= icon($item['type'] === 'supplier' ? 'truck' : 'users', 18) ?></span>
                            <span><strong><?= e($item['name']) ?></strong><small><?= e($item['code']) ?><?= $item['phone'] ? ' · ' . e($item['phone']) : '' ?></small></span>
                            <span>→</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
            <section class="panel">
                <div class="panel-header"><h2><?= e(t('nav_movements')) ?></h2><span class="count-badge"><?= e((string) count($results['movements'])) ?></span></div>
                <div class="result-list">
                    <?php foreach ($results['movements'] as $item): ?>
                        <a href="<?= e(route('receipt', ['id' => $item['id']])) ?>" class="result-item">
                            <span class="result-icon"><?= icon('swap', 18) ?></span>
                            <span><strong><?= e($item['reference']) ?></strong><small><?= e($item['product_name']) ?> · <?= money($item['total_amount']) ?></small></span>
                            <span>→</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    <?php endif; ?>
<?php endif; ?>
