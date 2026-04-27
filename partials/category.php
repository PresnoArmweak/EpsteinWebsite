<?php // partials/category.php ?>
<?php if (!isset($category) || !$category): ?>
    <div class="historica-empty">
        <h1>Category not found</h1>
        <p>That category doesn&rsquo;t exist. Pick another from the sidebar.</p>
    </div>
<?php else: ?>
    <header class="historica-page-header">
        <span class="historica-breadcrumb">
            <a href="?view=home">Home</a>
            <span>&rsaquo;</span>
            <span>Category</span>
        </span>
        <h1 class="historica-page-header__title"><?= e($category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?>
            <p class="historica-page-header__lede"><?= e($category['description']) ?></p>
        <?php endif; ?>
        <span class="historica-page-header__count">
            <?= count($figures) ?> entr<?= count($figures) === 1 ? 'y' : 'ies' ?>
            <?= is_logged_in() ? '' : 'visible to guests' ?>
        </span>
    </header>

    <?php if (empty($figures)): ?>
        <div class="historica-empty">
            <p>No entries in this category are visible to your account.</p>
            <?php if (!is_logged_in()): ?>
                <p><a href="?view=login">Sign in</a> &mdash; some entries are reserved for members.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <ul class="historica-list">
            <?php foreach ($figures as $f): ?>
                <li class="historica-list__item">
                    <a class="historica-list__link" href="?view=article&slug=<?= e($f['slug']) ?>">
                        <div class="historica-list__main">
                            <h3 class="historica-list__name"><?= e($f['name']) ?></h3>
                            <p class="historica-list__known"><?= e($f['known_for']) ?></p>
                        </div>
                        <div class="historica-list__meta">
                            <span class="historica-list__life"><?= e($f['lifespan'] ?? '') ?></span>
                            <?php if ((int)$f['is_admin_only'] === 1): ?>
                                <span class="historica-pill historica-pill--admin">admin</span>
                            <?php elseif ((int)$f['is_restricted'] === 1): ?>
                                <span class="historica-pill historica-pill--locked">members</span>
                            <?php endif; ?>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>
