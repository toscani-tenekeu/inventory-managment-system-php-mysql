<?php
$isSupplier = $partnerType === 'supplier';
$partner = $editing ?? [
    'id' => '',
    'code' => '',
    'name' => '',
    'contact_name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
];
$showForm = $editing !== null || isset($_GET['new']);
?>
<header class="page-header">
    <div>
        <p class="eyebrow"><?= e((string) $partners['total']) ?> <?= e(t('results')) ?></p>
        <h1><?= e(t($currentPage . '_title')) ?></h1>
        <p><?= e(t($currentPage . '_subtitle')) ?></p>
    </div>
    <a class="button button-primary" href="<?= e(route($currentPage, ['new' => 1])) ?>">
        <?= icon('plus', 18) ?> <?= e(t($isSupplier ? 'new_supplier' : 'new_customer')) ?>
    </a>
</header>

<?php if ($showForm): ?>
<section class="panel form-panel">
    <div class="panel-header">
        <div>
            <h2><?= e(t($editing ? ($isSupplier ? 'edit_supplier' : 'edit_customer') : ($isSupplier ? 'new_supplier' : 'new_customer'))) ?></h2>
            <p><?= e(t($currentPage . '_subtitle')) ?></p>
        </div>
        <a class="icon-button" href="<?= e(route($currentPage)) ?>" aria-label="<?= e(t('cancel')) ?>">×</a>
    </div>
    <form method="post" action="<?= e(route($currentPage)) ?>" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="partner-save">
        <input type="hidden" name="partner_type" value="<?= e($partnerType) ?>">
        <input type="hidden" name="id" value="<?= e($partner['id']) ?>">
        <label class="field">
            <span><?= e(t('code')) ?> *</span>
            <input name="code" maxlength="64" required value="<?= e($partner['code']) ?>" placeholder="<?= $isSupplier ? 'SUP-001' : 'CLI-001' ?>">
        </label>
        <label class="field field-span-2">
            <span><?= e(t('name')) ?> *</span>
            <input name="name" maxlength="160" required value="<?= e($partner['name']) ?>">
        </label>
        <label class="field">
            <span><?= e(t('contact_name')) ?></span>
            <input name="contact_name" maxlength="160" value="<?= e($partner['contact_name']) ?>">
        </label>
        <label class="field">
            <span><?= e(t('email')) ?></span>
            <input name="email" type="email" maxlength="190" value="<?= e($partner['email']) ?>">
        </label>
        <label class="field">
            <span><?= e(t('phone')) ?></span>
            <input name="phone" type="tel" maxlength="50" value="<?= e($partner['phone']) ?>">
        </label>
        <label class="field field-span-full">
            <span><?= e(t('address')) ?></span>
            <textarea name="address" rows="3" maxlength="500"><?= e($partner['address']) ?></textarea>
        </label>
        <div class="form-actions field-span-full">
            <a class="button button-secondary" href="<?= e(route($currentPage)) ?>"><?= e(t('cancel')) ?></a>
            <button class="button button-primary" type="submit"><?= e(t('save')) ?></button>
        </div>
    </form>
</section>
<?php endif; ?>

<section class="panel">
    <form class="filters" method="get" action="index.php">
        <input type="hidden" name="page" value="<?= e($currentPage) ?>">
        <label class="search-control">
            <?= icon('search', 18) ?>
            <input name="q" type="search" maxlength="100" value="<?= e($search) ?>" placeholder="<?= e(t('search')) ?>">
        </label>
        <select name="status" aria-label="<?= e(t('status')) ?>">
            <option value="active"<?= selected($status, 'active') ?>><?= e(t('active')) ?></option>
            <option value="archived"<?= selected($status, 'archived') ?>><?= e(t('archived')) ?></option>
            <option value="all"<?= selected($status, 'all') ?>><?= e(t('all_statuses')) ?></option>
        </select>
        <button class="button button-secondary" type="submit"><?= e(t('filter')) ?></button>
    </form>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th><?= e(t('code')) ?></th>
                <th><?= e(t('name')) ?></th>
                <th><?= e(t('contact_name')) ?></th>
                <th><?= e(t('email')) ?></th>
                <th><?= e(t('phone')) ?></th>
                <th><?= e(t('status')) ?></th>
                <th class="actions-column"><?= e(t('actions')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ($partners['items'] === []): ?>
                <tr><td colspan="7" class="empty-cell"><?= e(t('no_data')) ?></td></tr>
            <?php endif; ?>
            <?php foreach ($partners['items'] as $row): ?>
                <tr>
                    <td><span class="reference"><?= e($row['code']) ?></span></td>
                    <td><strong><?= e($row['name']) ?></strong><small><?= e($row['address'] ?: '') ?></small></td>
                    <td><?= e($row['contact_name'] ?: '—') ?></td>
                    <td><?= e($row['email'] ?: '—') ?></td>
                    <td><?= e($row['phone'] ?: '—') ?></td>
                    <td><span class="status-dot <?= (int) $row['active'] === 1 ? 'is-active' : 'is-muted' ?>"><?= e(t((int) $row['active'] === 1 ? 'active' : 'archived')) ?></span></td>
                    <td>
                        <div class="table-actions">
                            <a class="button button-ghost button-small" href="<?= e(route($currentPage, ['edit' => $row['id']])) ?>"><?= e(t('edit')) ?></a>
                            <?php if (app('auth')->isAdmin()): ?>
                                <form method="post" action="<?= e(route($currentPage)) ?>" data-confirm="<?= e(t('product_archive_confirm')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="partner-toggle">
                                    <input type="hidden" name="partner_type" value="<?= e($partnerType) ?>">
                                    <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                    <button class="button button-ghost button-small" type="submit"><?= e(t((int) $row['active'] === 1 ? 'archive' : 'restore')) ?></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $pagination = $partners; require BASE_PATH . '/app/Views/partials/pagination.php'; ?>
</section>
