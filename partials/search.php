<?php // partials/search.php ?>
<header class="historica-page-header">
    <span class="historica-breadcrumb">
        <a href="?view=home">Home</a>
        <span>&rsaquo;</span>
        <span>Search</span>
    </span>
    <h1 class="historica-page-header__title">
        <?php if (!empty($q)): ?>
            Results for &ldquo;<?= e($q) ?>&rdquo;
        <?php else: ?>
            Search Historica
        <?php endif; ?>
    </h1>
    <?php if (!empty($q)): ?>
        <span class="historica-page-header__count">
            <?= count($results) ?> match<?= count($results) === 1 ? '' : 'es' ?>
        </span>
    <?php endif; ?>
</header>

<?php if (empty($q)): ?>
    <p class="historica-empty">Type a query in the search bar above to find an entry.</p>
<?php elseif (empty($results)): ?>
    <div class="historica-empty">
        <p>No matching entries are visible to your account.</p>
        <?php if (!is_logged_in()): ?>
            <p><a href="?view=login">Sign in</a> &mdash; some entries are members-only.</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <ul class="historica-list">
        <?php foreach ($results as $r): ?>
            <li class="historica-list__item">
                <a class="historica-list__link" href="?view=article&slug=<?= e($r['slug']) ?>">
                    <div class="historica-list__main">
                        <h3 class="historica-list__name"><?= e($r['name']) ?></h3>
                        <p class="historica-list__known"><?= e($r['known_for']) ?></p>
                    </div>
                    <div class="historica-list__meta">
                        <span class="historica-list__category"><?= e($r['category_name']) ?></span>
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
