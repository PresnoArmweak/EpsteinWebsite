<?php // components/sidebar.php ?>
<div class="historica-sidebar">
    <div class="historica-sidebar__heading">
        <span class="historica-sidebar__eyebrow">Index</span>
        <h2 class="historica-sidebar__title">Categories</h2>
    </div>

    <nav class="historica-sidebar__nav">
        <?php
        $current_slug = (string)(filter_input(INPUT_GET, 'slug') ?? '');
        $current_view = (string)(filter_input(INPUT_GET, 'view') ?? '');
        ?>
        <?php foreach ($categories as $cat): ?>
            <?php
            $is_active = ($current_view === 'category' && $current_slug === $cat['slug']);
            ?>
            <a
                class="historica-sidebar__link<?= $is_active ? ' is-active' : '' ?>"
                href="?view=category&slug=<?= e($cat['slug']) ?>"
                title="<?= e($cat['description'] ?? '') ?>">
                <span class="historica-sidebar__link-name"><?= e($cat['name']) ?></span>
                <span class="historica-sidebar__link-count"><?= (int)$cat['article_count'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="historica-sidebar__footer">
        <a href="?view=about" class="historica-sidebar__about">About this archive &rarr;</a>
        <a href="?view=sources" class="historica-sidebar__about">Sources &amp; bibliography &rarr;</a>
        <?php if (!is_logged_in()): ?>
            <p class="historica-sidebar__hint">
                Some entries are reserved for signed-in readers.
                <a href="?view=login">Sign in</a>
                to unlock them.
            </p>
        <?php endif; ?>
    </div>
</div>
