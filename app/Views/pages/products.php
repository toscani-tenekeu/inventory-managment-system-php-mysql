<?php
$showForm = $editing !== null || isset($_GET['new']);
$product = $editing ?? [
    'id' => '',
    'sku' => '',
    'barcode' => '',
    'name' => '',
    'description' => '',
    'location' => '',
    'category_id' => '',
    'unit' => 'piece',
    'stock_quantity' => 0,
    'cost_price' => 0,
    'sale_price' => 0,
    'reorder_level' => 5,
    'target_stock' => 10,
    'manufactured_at' => '',
    'expires_at' => '',
];
?>
<header class="page-header">
    <div>
        <p class="eyebrow"><?= e((string) $products['total']) ?> <?= e(t('results')) ?></p>
        <h1><?= e(t('products_title')) ?></h1>
        <p><?= e(t('products_subtitle')) ?></p>
    </div>
    <div class="header-actions">
        <a class="button button-secondary" href="<?= e(route('export', ['resource' => 'products'])) ?>">
            <?= icon('download', 18) ?> <?= e(t('export_csv')) ?>
        </a>
        <a class="button button-primary" href="<?= e(route('products', ['new' => 1])) ?>">
            <?= icon('plus', 18) ?> <?= e(t('new_product')) ?>
        </a>
    </div>
</header>

<?php if ($showForm): ?>
<section class="panel form-panel">
    <div class="panel-header">
        <div>
            <h2><?= e(t($editing ? 'edit_product' : 'new_product')) ?></h2>
            <p><?= e(t('products_subtitle')) ?></p>
        </div>
        <a class="icon-button" href="<?= e(route('products')) ?>" aria-label="<?= e(t('cancel')) ?>">×</a>
    </div>
    <form method="post" action="<?= e(route('products')) ?>" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="product-save">
        <input type="hidden" name="id" value="<?= e($product['id']) ?>">

        <label class="field">
            <span><?= e(t('sku')) ?> *</span>
            <input name="sku" maxlength="64" value="<?= e($product['sku']) ?>" required placeholder="SKU-001">
        </label>
        <label class="field">
            <span><?= e(t('barcode')) ?></span>
            <input name="barcode" maxlength="80" value="<?= e($product['barcode'] ?? '') ?>" placeholder="1234567890123">
        </label>
        <label class="field field-span-2">
            <span><?= e(t('name')) ?> *</span>
            <input name="name" maxlength="160" value="<?= e($product['name']) ?>" required>
        </label>
        <label class="field">
            <span><?= e(t('category')) ?></span>
            <select name="category_id">
                <option value=""><?= e(t('choose_category')) ?></option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e($category['id']) ?>"<?= selected($product['category_id'], $category['id']) ?><?= (int) $category['active'] !== 1 && (string) $product['category_id'] !== (string) $category['id'] ? ' disabled' : '' ?>>
                        <?= e($category['name']) ?><?= (int) $category['active'] !== 1 ? ' — ' . e(t('archived')) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field">
            <span><?= e(t('unit')) ?> *</span>
            <select name="unit" required>
                <?php foreach (['piece', 'kilogram', 'liter', 'meter', 'box'] as $unit): ?>
                    <option value="<?= e($unit) ?>"<?= selected($product['unit'], $unit) ?>><?= e(t($unit === 'box' ? 'box_unit' : $unit)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field">
            <span><?= e(t('location')) ?></span>
            <input name="location" maxlength="120" value="<?= e($product['location'] ?? '') ?>" placeholder="A-01-03">
        </label>
        <label class="field">
            <span><?= e(t('cost_price')) ?></span>
            <input name="cost_price" type="number" min="0" step="0.01" value="<?= e($product['cost_price']) ?>">
        </label>
        <label class="field">
            <span><?= e(t('sale_price')) ?></span>
            <input name="sale_price" type="number" min="0" step="0.01" value="<?= e($product['sale_price']) ?>">
        </label>
        <label class="field">
            <span><?= e(t('reorder_level')) ?></span>
            <input name="reorder_level" type="number" min="0" step="0.0001" value="<?= e($product['reorder_level']) ?>">
        </label>
        <label class="field">
            <span><?= e(t('target_stock')) ?></span>
            <input name="target_stock" type="number" min="0" step="0.0001" value="<?= e($product['target_stock'] ?? 0) ?>">
            <small><?= e(t('target_stock_help')) ?></small>
        </label>
        <?php if (!$editing): ?>
            <label class="field">
                <span><?= e(t('initial_stock')) ?></span>
                <input name="initial_stock" type="number" min="0" step="0.0001" value="0">
            </label>
        <?php else: ?>
            <label class="field">
                <span><?= e(t('current_stock')) ?></span>
                <input value="<?= e(quantity($product['stock_quantity'])) ?>" disabled>
                <input name="initial_stock" type="hidden" value="0">
            </label>
        <?php endif; ?>
        <label class="field">
            <span><?= e(t('manufactured_at')) ?></span>
            <input name="manufactured_at" type="date" value="<?= e($product['manufactured_at']) ?>">
        </label>
        <label class="field">
            <span><?= e(t('expires_at')) ?></span>
            <input name="expires_at" type="date" value="<?= e($product['expires_at']) ?>">
        </label>
        <label class="field field-span-full">
            <span><?= e(t('description')) ?></span>
            <textarea name="description" rows="3" maxlength="5000"><?= e($product['description']) ?></textarea>
        </label>
        <div class="form-actions field-span-full">
            <a class="button button-secondary" href="<?= e(route('products')) ?>"><?= e(t('cancel')) ?></a>
            <button class="button button-primary" type="submit"><?= e(t('save')) ?></button>
        </div>
    </form>
</section>
<?php endif; ?>

<section class="panel">
    <form class="filters" method="get" action="index.php">
        <input type="hidden" name="page" value="products">
        <label class="search-control">
            <?= icon('search', 18) ?>
            <input name="q" type="search" maxlength="100" value="<?= e($search) ?>" placeholder="<?= e(t('search')) ?>">
        </label>
        <select name="status" aria-label="<?= e(t('status')) ?>">
            <option value="active"<?= selected($status, 'active') ?>><?= e(t('active')) ?></option>
            <option value="archived"<?= selected($status, 'archived') ?>><?= e(t('archived')) ?></option>
            <option value="all"<?= selected($status, 'all') ?>><?= e(t('all_statuses')) ?></option>
        </select>
        <select name="category" aria-label="<?= e(t('category')) ?>">
            <option value=""><?= e(t('all_categories')) ?></option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= e($category['id']) ?>"<?= selected($categoryId, $category['id']) ?>><?= e($category['name']) ?><?= (int) $category['active'] !== 1 ? ' — ' . e(t('archived')) : '' ?></option>
            <?php endforeach; ?>
        </select>
        <select name="stock" aria-label="<?= e(t('stock_status')) ?>">
            <option value=""><?= e(t('all_stock_levels')) ?></option>
            <option value="out"<?= selected($stockStatus, 'out') ?>><?= e(t('out_of_stock')) ?></option>
            <option value="low"<?= selected($stockStatus, 'low') ?>><?= e(t('low_stock_only')) ?></option>
            <option value="available"<?= selected($stockStatus, 'available') ?>><?= e(t('available_stock')) ?></option>
            <option value="healthy"<?= selected($stockStatus, 'healthy') ?>><?= e(t('healthy_stock')) ?></option>
        </select>
        <button class="button button-secondary" type="submit"><?= e(t('filter')) ?></button>
        <?php if ($search !== '' || $status !== 'active' || $stockStatus !== '' || $categoryId !== null): ?>
            <a class="text-link" href="<?= e(route('products')) ?>"><?= e(t('clear')) ?></a>
        <?php endif; ?>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th><?= e(t('product')) ?></th>
                <th><?= e(t('category')) ?></th>
                <th><?= e(t('current_stock')) ?></th>
                <th><?= e(t('cost_price')) ?></th>
                <th><?= e(t('sale_price')) ?></th>
                <th><?= e(t('status')) ?></th>
                <th class="actions-column"><?= e(t('actions')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ($products['items'] === []): ?>
                <tr><td colspan="7" class="empty-cell"><?= e(t('no_data')) ?></td></tr>
            <?php endif; ?>
            <?php foreach ($products['items'] as $row): ?>
                <?php $isLow = (float) $row['stock_quantity'] <= (float) $row['reorder_level']; ?>
                <tr>
                    <td>
                        <strong><?= e($row['name']) ?></strong>
                        <small class="reference"><?= e($row['sku']) ?><?= $row['barcode'] ? ' · ' . e($row['barcode']) : '' ?></small>
                    </td>
                    <td><?= e($row['category_name'] ?? t('choose_category')) ?><?php if ($row['location']): ?><small><?= e($row['location']) ?></small><?php endif; ?></td>
                    <td>
                        <span class="stock-pill<?= $isLow ? ' is-low' : '' ?>">
                            <?= e(quantity($row['stock_quantity'])) ?> <?= e(t($row['unit'] === 'box' ? 'box_unit' : $row['unit'])) ?>
                        </span>
                    </td>
                    <td><?= money($row['cost_price']) ?></td>
                    <td><?= money($row['sale_price']) ?></td>
                    <td><span class="status-dot <?= (int) $row['active'] === 1 ? 'is-active' : 'is-muted' ?>"><?= e(t((int) $row['active'] === 1 ? 'active' : 'archived')) ?></span></td>
                    <td>
                        <div class="table-actions">
                            <a class="button button-ghost button-small" href="<?= e(route('movements', ['product' => $row['id']])) ?>"><?= e(t('ledger')) ?></a>
                            <a class="button button-ghost button-small" href="<?= e(route('products', ['edit' => $row['id']])) ?>"><?= e(t('edit')) ?></a>
                            <?php if (app('auth')->isAdmin()): ?>
                                <form method="post" action="<?= e(route('products')) ?>" data-confirm="<?= e(t('product_archive_confirm')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="product-toggle">
                                    <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                    <button class="button button-ghost button-small" type="submit">
                                        <?= e(t((int) $row['active'] === 1 ? 'archive' : 'restore')) ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $pagination = $products; require BASE_PATH . '/app/Views/partials/pagination.php'; ?>
</section>
