<main class="receipt-sheet">
    <div class="receipt-toolbar">
        <a class="button button-secondary" href="<?= e(route('movements')) ?>">← <?= e(t('back')) ?></a>
        <button class="button button-primary" type="button" data-print><?= icon('print', 18) ?> <?= e(t('print')) ?></button>
    </div>

    <header class="receipt-header">
        <div class="brand receipt-brand">
            <span class="brand-mark" aria-hidden="true">I</span>
            <span><strong>IMS</strong><small>Inventory Management</small></span>
        </div>
        <div>
            <p class="eyebrow"><?= e(t('receipt_title')) ?></p>
            <h1><?= e($movement['reference']) ?></h1>
            <span class="badge badge-<?= e($movement['type']) ?>"><?= e(t('movement_type_' . $movement['type'])) ?></span>
        </div>
    </header>

    <section class="receipt-meta">
        <div><span><?= e(t('movement_date')) ?></span><strong><?= e(local_date($movement['occurred_at'], true)) ?></strong></div>
        <div><span><?= e(t('status')) ?></span><strong><?= e(t($movement['status'])) ?></strong></div>
        <div><span><?= e(t('created_by')) ?></span><strong><?= e($movement['user_name'] ?: '—') ?></strong></div>
        <div><span><?= e(t('stock_after')) ?></span><strong><?= e(quantity($movement['balance_after'])) ?> <?= e(t($movement['unit'] === 'box' ? 'box_unit' : $movement['unit'])) ?></strong></div>
    </section>

    <?php if ($movement['partner_name']): ?>
        <section class="receipt-partner">
            <span><?= e(t('partner')) ?></span>
            <h2><?= e($movement['partner_name']) ?></h2>
            <p>
                <?= e($movement['partner_code'] ?: '') ?>
                <?= $movement['partner_phone'] ? ' · ' . e($movement['partner_phone']) : '' ?>
                <?= $movement['partner_email'] ? ' · ' . e($movement['partner_email']) : '' ?>
            </p>
            <?php if ($movement['partner_address']): ?><p><?= e($movement['partner_address']) ?></p><?php endif; ?>
        </section>
    <?php endif; ?>

    <div class="table-wrap receipt-table">
        <table>
            <thead><tr><th><?= e(t('product')) ?></th><th><?= e(t('quantity')) ?></th><th><?= e(t('unit_price')) ?></th><th><?= e(t('total')) ?></th></tr></thead>
            <tbody>
            <tr>
                <td><strong><?= e($movement['product_name']) ?></strong><small><?= e($movement['sku']) ?></small></td>
                <td><?= e(quantity($movement['quantity'])) ?> <?= e(t($movement['unit'] === 'box' ? 'box_unit' : $movement['unit'])) ?></td>
                <td><?= money($movement['unit_price']) ?></td>
                <td><strong><?= money($movement['total_amount']) ?></strong></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="receipt-total">
        <span><?= e(t('total')) ?></span>
        <strong><?= money($movement['total_amount']) ?></strong>
    </div>

    <?php if ($movement['notes']): ?>
        <section class="receipt-notes"><span><?= e(t('notes')) ?></span><p><?= nl2br(e($movement['notes'])) ?></p></section>
    <?php endif; ?>

    <footer class="receipt-footer">
        <p><?= e(t('document_generated')) ?></p>
        <p><?= e(date('Y')) ?> · <?= e(config('name')) ?></p>
    </footer>
</main>
