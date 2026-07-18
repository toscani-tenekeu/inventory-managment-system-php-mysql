<div class="auth-heading">
    <span class="mobile-auth-logo">I</span>
    <p class="eyebrow"><?= e(config('name')) ?></p>
    <h2><?= e(t('login_title')) ?></h2>
    <p><?= e(t('login_subtitle')) ?></p>
</div>

<form method="post" action="<?= e(route('login')) ?>" class="stack-form">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="login">

    <label class="field">
        <span><?= e(t('email')) ?></span>
        <input type="email" name="email" autocomplete="username" maxlength="190" required autofocus
               placeholder="admin@example.com">
    </label>

    <label class="field">
        <span><?= e(t('password')) ?></span>
        <span class="password-field">
            <input type="password" name="password" autocomplete="current-password" maxlength="500" required>
            <button type="button" data-password-toggle aria-label="<?= e(t('password')) ?>">○</button>
        </span>
    </label>

    <button class="button button-primary button-large button-block" type="submit"><?= e(t('sign_in')) ?></button>
</form>

<p class="security-note">
    <span aria-hidden="true">✓</span>
    <?= e(t('remember_security')) ?>
</p>
