<header class="page-header">
    <div>
        <p class="eyebrow"><?= e(t('inventory_control')) ?></p>
        <h1><?= e(t('reports_title')) ?></h1>
        <p><?= e(t('reports_subtitle')) ?></p>
    </div>
    <div class="header-actions">
        <a class="button button-secondary" href="<?= e(route('export', ['resource' => 'reorder'])) ?>">
            <?= icon('download', 18) ?> <?= e(t('export_reorder')) ?>
        </a>
        <a class="button button-primary" href="<?= e(route('movements', ['new' => 1])) ?>">
            <?= icon('plus', 18) ?> <?= e(t('new_movement')) ?>
        </a>
    </div>
</header>

<section class="stats-grid" aria-label="<?= e(t('reports_title')) ?>">
    <article class="stat-card stat-blue">
        <span class="stat-icon"><?= icon('box', 22) ?></span>
        <div><p><?= e(t('products_count')) ?></p><strong><?= e((string) ($stats['products_count'] ?? 0)) ?></strong></div>
    </article>
    <article class="stat-card stat-orange">
        <span class="stat-icon">!</span>
        <div><p><?= e(t('low_stock')) ?></p><strong><?= e((string) ($stats['low_stock_count'] ?? 0)) ?></strong></div>
    </article>
    <article class="stat-card stat-orange">
        <span class="stat-icon">0</span>
        <div><p><?= e(t('out_of_stock')) ?></p><strong><?= e((string) ($stats['out_of_stock_count'] ?? 0)) ?></strong></div>
    </article>
    <article class="stat-card stat-purple">
        <span class="stat-icon">∑</span>
        <div><p><?= e(t('inventory_value')) ?></p><strong><?= money($stats['inventory_value'] ?? 0) ?></strong></div>
    </article>
    <article class="stat-card stat-green">
        <span class="stat-icon">↗</span>
        <div><p><?= e(t('monthly_sales')) ?></p><strong><?= money($stats['monthly_sales'] ?? 0) ?></strong></div>
    </article>
    <article class="stat-card stat-teal">
        <span class="stat-icon">↙</span>
        <div><p><?= e(t('monthly_purchases')) ?></p><strong><?= money($stats['monthly_purchases'] ?? 0) ?></strong></div>
    </article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2><?= e(t('movement_summary')) ?></h2>
            <p><?= e(t('movement_summary_help')) ?></p>
        </div>
    </div>
    <form class="filters" method="get" action="index.php">
        <input type="hidden" name="page" value="reports">
        <label class="field compact-field"><span><?= e(t('date_from')) ?></span><input type="date" name="from" value="<?= e($dateFrom) ?>"></label>
        <label class="field compact-field"><span><?= e(t('date_to')) ?></span><input type="date" name="to" value="<?= e($dateTo) ?>"></label>
        <button class="button button-secondary" type="submit"><?= e(t('filter')) ?></button>
    </form>
    <div class="table-wrap">
        <table>
            <thead><tr><th><?= e(t('movement_type')) ?></th><th><?= e(t('movements_count')) ?></th><th><?= e(t('quantity')) ?></th><th><?= e(t('total')) ?></th></tr></thead>
            <tbody>
            <?php if ($movementSummary === []): ?><tr><td colspan="4" class="empty-cell"><?= e(t('no_data')) ?></td></tr><?php endif; ?>
            <?php foreach ($movementSummary as $row): ?>
                <tr>
                    <td><span class="badge badge-<?= e($row['type']) ?>"><?= e(t('movement_type_' . $row['type'])) ?></span></td>
                    <td><?= e((string) $row['movements_count']) ?></td>
                    <td><?= e(quantity($row['total_quantity'])) ?> <?= e(t($row['unit'] === 'box' ? 'box_unit' : $row['unit'])) ?></td>
                    <td><?= money($row['total_amount']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="report-grid">
    <section class="panel">
        <div class="panel-header">
            <div><h2><?= e(t('reorder_suggestions')) ?></h2><p><?= e(t('reorder_help')) ?></p></div>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th><?= e(t('product')) ?></th><th><?= e(t('current_stock')) ?></th><th><?= e(t('target_stock')) ?></th><th><?= e(t('suggested_order')) ?></th></tr></thead>
                <tbody>
                <?php if ($reorderSuggestions === []): ?><tr><td colspan="4" class="empty-cell"><?= e(t('no_low_stock')) ?></td></tr><?php endif; ?>
                <?php foreach ($reorderSuggestions as $product): ?>
                    <tr>
                        <td><a href="<?= e(route('movements', ['product' => $product['id']])) ?>"><strong><?= e($product['name']) ?></strong><small><?= e($product['sku']) ?><?= $product['location'] ? ' · ' . e($product['location']) : '' ?></small></a></td>
                        <td><?= e(quantity($product['stock_quantity'])) ?> <?= e(t($product['unit'] === 'box' ? 'box_unit' : $product['unit'])) ?></td>
                        <td><?= e(quantity($product['target_stock'])) ?></td>
                        <td><strong><?= e(quantity($product['suggested_quantity'])) ?></strong><small><?= money($product['suggested_value']) ?></small></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div><h2><?= e(t('value_by_category')) ?></h2><p><?= e(t('inventory_value')) ?></p></div>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th><?= e(t('category')) ?></th><th><?= e(t('products_count')) ?></th><th><?= e(t('out_of_stock')) ?></th><th><?= e(t('inventory_value')) ?></th></tr></thead>
                <tbody>
                <?php if ($categorySummary === []): ?><tr><td colspan="4" class="empty-cell"><?= e(t('no_data')) ?></td></tr><?php endif; ?>
                <?php foreach ($categorySummary as $row): ?>
                    <tr><td><strong><?= e($row['category_name']) ?></strong></td><td><?= e((string) $row['products_count']) ?></td><td><?= e((string) $row['out_of_stock_count']) ?></td><td><?= money($row['stock_value']) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
