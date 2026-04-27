<?php // partials/sources.php ?>
<header class="historica-page-header">
    <span class="historica-breadcrumb">
        <a href="?view=home">Home</a>
        <span>&rsaquo;</span>
        <span>Sources</span>
    </span>
    <h1 class="historica-page-header__title">// sources</h1>
    <p class="historica-page-header__lede">
        The full bibliography for the archive, formatted in MLA 9. Citations
        in entry text appear as subscript numbers like
        <sub class="cite">[<a class="cite__link" href="#cite-1">1</a>]</sub>;
        each links here. Numbers are stable across the site &mdash; source #47 is
        the same source on every entry that cites it.
    </p>
    <span class="historica-page-header__count">
        <?= count($sources) ?> source<?= count($sources) === 1 ? '' : 's' ?>
    </span>
</header>

<?php if (empty($sources)): ?>
    <div class="historica-empty">
        <p>No sources have been registered yet.</p>
    </div>
<?php else: ?>
    <ol class="historica-sources">
        <?php foreach ($sources as $s): ?>
            <li id="cite-<?= (int)$s['id'] ?>" class="historica-sources__item">
                <span class="historica-sources__num">[<?= (int)$s['id'] ?>]</span>
                <span class="historica-sources__entry">
                    <?= source_format_mla($s) ?>
                </span>
                <?php if (!empty($s['type'])): ?>
                    <span class="historica-sources__type"><?= e($s['type']) ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>
