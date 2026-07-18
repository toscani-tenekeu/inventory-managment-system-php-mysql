<?php
$category = $editing ?? ['id' => '', 'name' => '', 'description' => ''];
?>
<header class="page-header">
    <div>
        <p class="eyebrow"><?= e((string) count($categories)) ?> <?= e(t('results')) ?></p>
        <h1><?= e(t('categories_title')) ?></h1>
        <p><?= e(t('categories_subtitle')) ?></p>
    </div>
</header>

<div class="split-layout">
    <section class="panel sticky-panel">
        <div class="panel-header">
            <div>
                <h2><?= e(t($editing ? 'edit_category' : 'new_category')) ?></h2>
                <p><?= e(t('categories_subtitle')) ?></p>
            </div>
        </div>
        <form method="post" action="<?= e(route('categories')) ?>" class="stack-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="category-save">
            <input type="hidden" name="id" value="<?= e($category['id']) ?>">
            <label class="field">
                <span><?= e(t('name')) ?> *</span>
                <input name="name" maxlength="120" value="<?= e($category['name']) ?>" required>
            </label>
            <label class="field">
                <span><?= e(t('description')) ?></span>
                <textarea name="description" rows="5" maxlength="500"><?= e($category['description']) ?></textarea>
            </label>
            <div class="form-actions">
                <?php if ($editing): ?>
                    <a class="button button-secondary" href="<?= e(route('categories')) ?>"><?= e(t('cancel')) ?></a>
                <?php endif; ?>
                <button class="button button-primary" type="submit"><?= e(t('save')) ?></button>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th><?= e(t('name')) ?></th>
                    <th><?= e(t('description')) ?></th>
                    <th><?= e(t('products')) ?></th>
                    <th><?= e(t('status')) ?></th>
                    <th class="actions-column"><?= e(t('actions')) ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ($categories === []): ?>
                    <tr><td colspan="5" class="empty-cell"><?= e(t('no_data')) ?></td></tr>
                <?php endif; ?>
                <?php foreach ($categories as $row): ?>
                    <tr>
                        <td><strong><?= e($row['name']) ?></strong></td>
                        <td class="truncate-cell"><?= e($row['description'] ?: '—') ?></td>
                        <td><?= e((string) $row['products_count']) ?></td>
                        <td><span class="status-dot <?= (int) $row['active'] === 1 ? 'is-active' : 'is-muted' ?>"><?= e(t((int) $row['active'] === 1 ? 'active' : 'archived')) ?></span></td>
                        <td>
                            <div class="table-actions">
                                <a class="button button-ghost button-small" href="<?= e(route('categories', ['edit' => $row['id']])) ?>"><?= e(t('edit')) ?></a>
                                <?php if (app('auth')->isAdmin()): ?>
                                    <form method="post" action="<?= e(route('categories')) ?>" data-confirm="<?= e(t('product_archive_confirm')) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="category-toggle">
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
    </section>
</div>
