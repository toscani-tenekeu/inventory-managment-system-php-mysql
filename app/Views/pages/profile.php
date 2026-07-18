<header class="page-header">
    <div>
        <p class="eyebrow"><?= e(t($profile['role'] === 'admin' ? 'administrator' : 'operator')) ?></p>
        <h1><?= e(t('profile_title')) ?></h1>
        <p><?= e(t('profile_subtitle')) ?></p>
    </div>
</header>

<section class="panel profile-panel">
    <div class="profile-hero">
        <span class="avatar avatar-large"><?= e(mb_strtoupper(mb_substr($profile['name'], 0, 1))) ?></span>
        <div>
            <h2><?= e($profile['name']) ?></h2>
            <p><?= e($profile['email']) ?></p>
        </div>
    </div>
    <form method="post" action="<?= e(route('profile')) ?>" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="profile-save">
        <label class="field field-span-2">
            <span><?= e(t('name')) ?> *</span>
            <input name="name" maxlength="120" value="<?= e($profile['name']) ?>" required>
        </label>
        <label class="field field-span-2">
            <span><?= e(t('email')) ?> *</span>
            <input name="email" type="email" maxlength="190" value="<?= e($profile['email']) ?>" required>
        </label>
        <label class="field">
            <span><?= e(t('language')) ?></span>
            <select name="locale">
                <option value="fr"<?= selected($profile['locale'], 'fr') ?>><?= e(t('french')) ?></option>
                <option value="en"<?= selected($profile['locale'], 'en') ?>><?= e(t('english')) ?></option>
            </select>
        </label>
        <label class="field">
            <span><?= e(t('theme')) ?></span>
            <select name="theme">
                <option value="system"<?= selected($profile['theme'], 'system') ?>><?= e(t('system_theme')) ?></option>
                <option value="light"<?= selected($profile['theme'], 'light') ?>><?= e(t('light_theme')) ?></option>
                <option value="dark"<?= selected($profile['theme'], 'dark') ?>><?= e(t('dark_theme')) ?></option>
            </select>
        </label>
        <label class="field field-span-2">
            <span><?= e(t('new_password_optional')) ?></span>
            <input name="password" type="password" minlength="10" maxlength="500" autocomplete="new-password">
            <small><?= e(t('password_hint')) ?></small>
        </label>
        <div class="form-actions field-span-full">
            <button class="button button-primary" type="submit"><?= e(t('save')) ?></button>
        </div>
    </form>
</section>
