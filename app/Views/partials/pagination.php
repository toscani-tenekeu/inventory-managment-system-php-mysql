<?php if (($pagination['pages'] ?? 1) > 1): ?>
    <nav class="pagination" aria-label="Pagination">
        <a class="button button-secondary button-small<?= $pagination['page'] <= 1 ? ' is-disabled' : '' ?>"
           href="<?= $pagination['page'] > 1 ? e(page_link($pagination, $pagination['page'] - 1)) : '#' ?>">
            <?= e(t('previous')) ?>
        </a>
        <span><?= e(t('page_of', ['current' => $pagination['page'], 'total' => $pagination['pages']])) ?></span>
        <a class="button button-secondary button-small<?= $pagination['page'] >= $pagination['pages'] ? ' is-disabled' : '' ?>"
           href="<?= $pagination['page'] < $pagination['pages'] ? e(page_link($pagination, $pagination['page'] + 1)) : '#' ?>">
            <?= e(t('next')) ?>
        </a>
    </nav>
<?php endif; ?>
