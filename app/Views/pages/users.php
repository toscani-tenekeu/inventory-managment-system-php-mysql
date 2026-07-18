<?php
$userForm = $editing ?? ['id' => '', 'name' => '', 'email' => '', 'role' => 'user', 'locale' => 'fr'];
$showForm = $editing !== null || isset($_GET['new']);
?>
<header class="page-header">
    <div>
        <p class="eyebrow"><?= e((string) count($users)) ?> <?= e(t('results')) ?></p>
        <h1><?= e(t('users_title')) ?></h1>
        <p><?= e(t('users_subtitle')) ?></p>
    </div>
    <a class="button button-primary" href="<?= e(route('users', ['new' => 1])) ?>">
        <?= icon('plus', 18) ?> <?= e(t('new_user')) ?>
    </a>
</header>

<?php if ($showForm): ?>
<section class="panel form-panel">
    <div class="panel-header">
        <div>
            <h2><?= e(t($editing ? 'edit_user' : 'new_user')) ?></h2>
            <p><?= e(t('users_subtitle')) ?></p>
        </div>
        <a class="icon-button" href="<?= e(route('users')) ?>" aria-label="<?= e(t('cancel')) ?>">×</a>
    </div>
    <form method="post" action="<?= e(route('users')) ?>" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="user-save">
        <input type="hidden" name="id" value="<?= e($userForm['id']) ?>">
        <label class="field field-span-2">
            <span><?= e(t('name')) ?> *</span>
            <input name="name" maxlength="120" required value="<?= e($userForm['name']) ?>">
        </label>
        <label class="field field-span-2">
            <span><?= e(t('email')) ?> *</span>
            <input name="email" type="email" maxlength="190" required value="<?= e($userForm['email']) ?>">
        </label>
        <label class="field">
            <span><?= e(t('role')) ?> *</span>
            <select name="role" required>
                <option value="user"<?= selected($userForm['role'], 'user') ?>><?= e(t('operator')) ?></option>
                <option value="admin"<?= selected($userForm['role'], 'admin') ?>><?= e(t('administrator')) ?></option>
            </select>
        </label>
        <label class="field">
            <span><?= e(t('language')) ?> *</span>
            <select name="locale" required>
                <option value="fr"<?= selected($userForm['locale'], 'fr') ?>><?= e(t('french')) ?></option>
                <option value="en"<?= selected($userForm['locale'], 'en') ?>><?= e(t('english')) ?></option>
            </select>
        </label>
        <label class="field field-span-2">
            <span><?= e($editing ? t('new_password_optional') : t('password')) ?><?= $editing ? '' : ' *' ?></span>
            <input name="password" type="password" minlength="10" maxlength="500"<?= $editing ? '' : ' required' ?> autocomplete="new-password">
            <small><?= e(t('password_hint')) ?></small>
        </label>
        <div class="form-actions field-span-full">
            <a class="button button-secondary" href="<?= e(route('users')) ?>"><?= e(t('cancel')) ?></a>
            <button class="button button-primary" type="submit"><?= e(t('save')) ?></button>
        </div>
    </form>
</section>
<?php endif; ?>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th><?= e(t('name')) ?></th>
                <th><?= e(t('email')) ?></th>
                <th><?= e(t('role')) ?></th>
                <th><?= e(t('language')) ?></th>
                <th><?= e(t('last_login')) ?></th>
                <th><?= e(t('status')) ?></th>
                <th class="actions-column"><?= e(t('actions')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $row): ?>
                <tr>
                    <td><span class="user-cell"><span class="avatar avatar-small"><?= e(mb_strtoupper(mb_substr($row['name'], 0, 1))) ?></span><strong><?= e($row['name']) ?></strong></span></td>
                    <td><?= e($row['email']) ?></td>
                    <td><span class="badge badge-neutral"><?= e(t($row['role'] === 'admin' ? 'administrator' : 'operator')) ?></span></td>
                    <td><?= e(mb_strtoupper($row['locale'])) ?></td>
                    <td><?= $row['last_login_at'] ? e(local_date($row['last_login_at'], true)) : e(t('never')) ?></td>
                    <td><span class="status-dot <?= (int) $row['active'] === 1 ? 'is-active' : 'is-muted' ?>"><?= e(t((int) $row['active'] === 1 ? 'active' : 'archived')) ?></span></td>
                    <td>
                        <div class="table-actions">
                            <a class="button button-ghost button-small" href="<?= e(route('users', ['edit' => $row['id']])) ?>"><?= e(t('edit')) ?></a>
                            <?php if ((int) $row['id'] !== (int) app('auth')->user()['id']): ?>
                                <form method="post" action="<?= e(route('users')) ?>" data-confirm="<?= e(t('product_archive_confirm')) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="user-toggle">
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
