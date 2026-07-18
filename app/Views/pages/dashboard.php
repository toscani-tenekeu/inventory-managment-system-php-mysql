<header class="page-header">
    <div>
        <p class="eyebrow"><?= e(date('d/m/Y')) ?></p>
        <h1><?= e(t('dashboard_title')) ?></h1>
        <p><?= e(t('dashboard_subtitle')) ?></p>
    </div>
    <a class="button button-primary" href="<?= e(route('movements', ['new' => 1])) ?>">
        <?= icon('plus', 18) ?> <?= e(t('new_movement')) ?>
    </a>
</header>

<section class="stats-grid" aria-label="<?= e(t('dashboard_title')) ?>">
    <article class="stat-card stat-blue">
        <span class="stat-icon"><?= icon('box', 22) ?></span>
        <div>
            <p><?= e(t('products_count')) ?></p>
            <strong><?= e(number_format((float) ($stats['products_count'] ?? 0), 0, ',', ' ')) ?></strong>
        </div>
    </article>
    <article class="stat-card stat-orange">
        <span class="stat-icon">0</span>
        <div>
            <p><?= e(t('out_of_stock')) ?></p>
            <strong><?= e((string) ($stats['out_of_stock_count'] ?? 0)) ?></strong>
        </div>
    </article>
    <article class="stat-card stat-orange">
        <span class="stat-icon">!</span>
        <div>
            <p><?= e(t('low_stock')) ?></p>
            <strong><?= e((string) ($stats['low_stock_count'] ?? 0)) ?></strong>
        </div>
    </article>
    <article class="stat-card stat-purple">
        <span class="stat-icon">∑</span>
        <div>
            <p><?= e(t('inventory_value')) ?></p>
            <strong><?= money($stats['inventory_value'] ?? 0) ?></strong>
        </div>
    </article>
    <article class="stat-card stat-green">
        <span class="stat-icon">↗</span>
        <div>
            <p><?= e(t('monthly_sales')) ?></p>
            <strong><?= money($stats['monthly_sales'] ?? 0) ?></strong>
        </div>
    </article>
    <article class="stat-card stat-teal">
        <span class="stat-icon">↙</span>
        <div>
            <p><?= e(t('monthly_purchases')) ?></p>
            <strong><?= money($stats['monthly_purchases'] ?? 0) ?></strong>
        </div>
    </article>
</section>

<div class="dashboard-grid">
    <section class="panel panel-wide">
        <div class="panel-header">
            <div>
                <h2><?= e(t('recent_movements')) ?></h2>
                <p><?= e(t('movements_subtitle')) ?></p>
            </div>
            <a class="text-link" href="<?= e(route('movements')) ?>"><?= e(t('view_all')) ?> →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th><?= e(t('reference')) ?></th>
                    <th><?= e(t('product')) ?></th>
                    <th><?= e(t('movement_type')) ?></th>
                    <th><?= e(t('quantity')) ?></th>
                    <th><?= e(t('total')) ?></th>
                    <th><?= e(t('date')) ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($recentMovements === []): ?>
                    <tr><td colspan="6" class="empty-cell"><?= e(t('no_recent_movement')) ?></td></tr>
                <?php endif; ?>
                <?php foreach ($recentMovements as $movement): ?>
                    <tr>
                        <td><a class="reference" href="<?= e(route('receipt', ['id' => $movement['id']])) ?>"><?= e($movement['reference']) ?></a></td>
                        <td>
                            <strong><?= e($movement['product_name']) ?></strong>
                            <small><?= e($movement['sku']) ?></small>
                        </td>
                        <td><span class="badge badge-<?= e($movement['type']) ?>"><?= e(t('movement_type_' . $movement['type'])) ?></span></td>
                        <td><?= e(quantity($movement['quantity'])) ?></td>
                        <td><?= money($movement['total_amount']) ?></td>
                        <td><?= e(local_date($movement['occurred_at'], true)) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h2><?= e(t('top_products')) ?></h2>
                <p><?= e(t('sales_revenue')) ?></p>
            </div>
        </div>
        <div class="rank-list">
            <?php if ($topProducts === []): ?>
                <p class="empty-state"><?= e(t('no_data')) ?></p>
            <?php endif; ?>
            <?php foreach ($topProducts as $index => $product): ?>
                <div class="rank-item">
                    <span class="rank-number"><?= e((string) ($index + 1)) ?></span>
                    <span class="rank-copy">
                        <strong><?= e($product['name']) ?></strong>
                        <small><?= e(quantity($product['quantity_sold'])) ?> <?= e(t('units_sold')) ?></small>
                    </span>
                    <strong><?= money($product['sales_total']) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2><?= e(t('stock_attention')) ?></h2>
            <p><?= e(t('low_stock')) ?></p>
        </div>
        <a class="text-link" href="<?= e(route('reports')) ?>"><?= e(t('view_all')) ?> →</a>
    </div>
    <?php if ($lowStockProducts === []): ?>
        <div class="positive-empty"><span>✓</span><?= e(t('no_low_stock')) ?></div>
    <?php else: ?>
        <div class="stock-alert-grid">
            <?php foreach ($lowStockProducts as $product): ?>
                <?php
                $stock = (float) $product['stock_quantity'];
                $threshold = max(1.0, (float) $product['reorder_level']);
                $percentage = max(0, min(100, ($stock / $threshold) * 100));
                ?>
                <article class="stock-alert-item">
                    <div>
                        <strong><?= e($product['name']) ?></strong>
                        <small><?= e($product['sku']) ?> · <?= e($product['category_name'] ?? t('choose_category')) ?></small>
                    </div>
                    <div class="stock-level">
                        <span><strong><?= e(quantity($stock)) ?></strong> / <?= e(quantity($threshold)) ?></span>
                        <progress value="<?= e((string) $percentage) ?>" max="100"><?= e((string) $percentage) ?>%</progress>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
