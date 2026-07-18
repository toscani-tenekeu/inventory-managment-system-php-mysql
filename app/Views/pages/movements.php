<?php
$showForm = isset($_GET['new']);
$inboundTypes = ['purchase', 'customer_return', 'adjustment_in'];
?>
<header class="page-header">
    <div>
        <p class="eyebrow"><?= e((string) $movements['total']) ?> <?= e(t('results')) ?></p>
        <h1><?= e(t('movements_title')) ?></h1>
        <p><?= e(t('movements_subtitle')) ?></p>
    </div>
    <div class="header-actions">
        <a class="button button-secondary" href="<?= e(route('export', ['resource' => 'movements'])) ?>">
            <?= icon('download', 18) ?> <?= e(t('export_csv')) ?>
        </a>
        <a class="button button-primary" href="<?= e(route('movements', ['new' => 1])) ?>">
            <?= icon('plus', 18) ?> <?= e(t('new_movement')) ?>
        </a>
    </div>
</header>

<?php if ($showForm): ?>
<section class="panel form-panel">
    <div class="panel-header">
        <div>
            <h2><?= e(t('new_movement')) ?></h2>
            <p><?= e(t('movements_subtitle')) ?></p>
        </div>
        <a class="icon-button" href="<?= e(route('movements')) ?>" aria-label="<?= e(t('cancel')) ?>">×</a>
    </div>
    <form method="post" action="<?= e(route('movements')) ?>" class="form-grid" data-movement-form
          data-currency="<?= e(config('currency', 'XAF')) ?>"
          data-currency-decimals="<?= e((string) config('currency_decimals', 0)) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="movement-create">
        <label class="field">
            <span><?= e(t('movement_type')) ?> *</span>
            <select name="type" required data-movement-type>
                <?php foreach ($movementTypes as $movementType): ?>
                    <option value="<?= e($movementType) ?>"><?= e(t('movement_type_' . $movementType)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field field-span-2">
            <span><?= e(t('product')) ?> *</span>
            <select name="product_id" required data-movement-product>
                <option value=""><?= e(t('select_product')) ?></option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= e($product['id']) ?>"<?= selected($productId, $product['id']) ?>
                            data-cost="<?= e($product['cost_price']) ?>"
                            data-sale="<?= e($product['sale_price']) ?>"
                            data-stock="<?= e($product['stock_quantity']) ?>">
                        <?= e($product['name']) ?> — <?= e($product['sku']) ?> (<?= e(quantity($product['stock_quantity'])) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field">
            <span><?= e(t('partner')) ?></span>
            <select name="partner_id" data-movement-partner>
                <option value=""><?= e(t('select_partner')) ?></option>
                <optgroup label="<?= e(t('nav_suppliers')) ?>" data-partner-group="supplier">
                    <?php foreach ($suppliers as $partner): ?>
                        <option value="<?= e($partner['id']) ?>" data-partner-type="supplier"><?= e($partner['name']) ?> — <?= e($partner['code']) ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="<?= e(t('nav_customers')) ?>" data-partner-group="customer">
                    <?php foreach ($customers as $partner): ?>
                        <option value="<?= e($partner['id']) ?>" data-partner-type="customer"><?= e($partner['name']) ?> — <?= e($partner['code']) ?></option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </label>
        <label class="field">
            <span><?= e(t('quantity')) ?> *</span>
            <input name="quantity" type="number" min="0.0001" step="0.0001" required data-movement-quantity>
        </label>
        <label class="field">
            <span><?= e(t('unit_price')) ?> *</span>
            <input name="unit_price" type="number" min="0" step="0.01" value="0" required data-movement-price>
        </label>
        <label class="field">
            <span><?= e(t('reference')) ?></span>
            <input name="reference" maxlength="64" placeholder="<?= e(t('automatic_reference')) ?>">
        </label>
        <label class="field">
            <span><?= e(t('movement_date')) ?> *</span>
            <input name="occurred_at" type="datetime-local" value="<?= e(date('Y-m-d\\TH:i')) ?>" max="<?= e(date('Y-m-d\\TH:i')) ?>" required>
        </label>
        <label class="field field-span-full">
            <span><?= e(t('notes')) ?></span>
            <textarea name="notes" rows="3" maxlength="1000"></textarea>
        </label>
        <div class="movement-total field-span-full">
            <span><?= e(t('total')) ?></span>
            <output data-movement-total><?= money(0) ?></output>
        </div>
        <div class="form-actions field-span-full">
            <a class="button button-secondary" href="<?= e(route('movements')) ?>"><?= e(t('cancel')) ?></a>
            <button class="button button-primary" type="submit"><?= e(t('save')) ?></button>
        </div>
    </form>
</section>
<?php endif; ?>

<section class="panel">
    <form class="filters" method="get" action="index.php">
        <input type="hidden" name="page" value="movements">
        <label class="search-control">
            <?= icon('search', 18) ?>
            <input name="q" type="search" maxlength="100" value="<?= e($search) ?>" placeholder="<?= e(t('search')) ?>">
        </label>
        <select name="type" aria-label="<?= e(t('movement_type')) ?>">
            <option value=""><?= e(t('movement_type')) ?> — <?= e(t('all_statuses')) ?></option>
            <?php foreach ($movementTypes as $movementType): ?>
                <option value="<?= e($movementType) ?>"<?= selected($type, $movementType) ?>><?= e(t('movement_type_' . $movementType)) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="product" aria-label="<?= e(t('product')) ?>">
            <option value=""><?= e(t('all_products')) ?></option>
            <?php foreach ($products as $product): ?>
                <option value="<?= e($product['id']) ?>"<?= selected($productId, $product['id']) ?>><?= e($product['name']) ?> — <?= e($product['sku']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" aria-label="<?= e(t('status')) ?>">
            <option value=""><?= e(t('all_statuses')) ?></option>
            <option value="posted"<?= selected($status, 'posted') ?>><?= e(t('posted')) ?></option>
            <option value="reversed"<?= selected($status, 'reversed') ?>><?= e(t('reversed')) ?></option>
        </select>
        <label class="filter-date"><span><?= e(t('date_from')) ?></span><input type="date" name="from" value="<?= e($dateFrom) ?>"></label>
        <label class="filter-date"><span><?= e(t('date_to')) ?></span><input type="date" name="to" value="<?= e($dateTo) ?>"></label>
        <button class="button button-secondary" type="submit"><?= e(t('filter')) ?></button>
        <?php if ($search !== '' || $type !== '' || $status !== '' || $productId !== null || $dateFrom !== '' || $dateTo !== ''): ?>
            <a class="text-link" href="<?= e(route('movements')) ?>"><?= e(t('clear')) ?></a>
        <?php endif; ?>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th><?= e(t('reference')) ?></th>
                <th><?= e(t('product')) ?></th>
                <th><?= e(t('movement_type')) ?></th>
                <th><?= e(t('partner')) ?></th>
                <th><?= e(t('quantity')) ?></th>
                <th><?= e(t('stock_after')) ?></th>
                <th><?= e(t('total')) ?></th>
                <th><?= e(t('date')) ?></th>
                <th><?= e(t('status')) ?></th>
                <th class="actions-column"><?= e(t('actions')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ($movements['items'] === []): ?>
                <tr><td colspan="10" class="empty-cell"><?= e(t('no_data')) ?></td></tr>
            <?php endif; ?>
            <?php foreach ($movements['items'] as $movement): ?>
                <tr>
                    <td><span class="reference"><?= e($movement['reference']) ?></span></td>
                    <td><strong><?= e($movement['product_name']) ?></strong><small><?= e($movement['sku']) ?></small></td>
                    <td><span class="badge badge-<?= e($movement['type']) ?>"><?= e(t('movement_type_' . $movement['type'])) ?></span></td>
                    <td><?= e($movement['partner_name'] ?: '—') ?></td>
                    <td><span class="movement-direction <?= in_array($movement['type'], $inboundTypes, true) ? 'is-in' : 'is-out' ?>"><?= in_array($movement['type'], $inboundTypes, true) ? '+' : '−' ?><?= e(quantity($movement['quantity'])) ?></span> <?= e(t($movement['unit'] === 'box' ? 'box_unit' : $movement['unit'])) ?></td>
                    <td><strong><?= e(quantity($movement['balance_after'])) ?></strong></td>
                    <td><?= money($movement['total_amount']) ?></td>
                    <td><?= e(local_date($movement['occurred_at'], true)) ?><small><?= e($movement['user_name'] ?: '—') ?></small></td>
                    <td><span class="status-dot <?= $movement['status'] === 'posted' ? 'is-active' : 'is-muted' ?>"><?= e(t($movement['status'])) ?></span></td>
                    <td>
                        <div class="table-actions">
                            <a class="button button-ghost button-small" href="<?= e(route('receipt', ['id' => $movement['id']])) ?>">
                                <?= e(t('receipt')) ?>
                            </a>
                            <?php if (
                                app('auth')->isAdmin()
                                && $movement['status'] === 'posted'
                                && $movement['reversal_of_id'] === null
                            ): ?>
                                <form method="post" action="<?= e(route('movements')) ?>" data-confirm="<?= e(t('reverse_confirm')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="movement-reverse">
                                    <input type="hidden" name="id" value="<?= e($movement['id']) ?>">
                                    <button class="button button-danger-ghost button-small" type="submit"><?= e(t('reverse')) ?></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $pagination = $movements; require BASE_PATH . '/app/Views/partials/pagination.php'; ?>
</section>
