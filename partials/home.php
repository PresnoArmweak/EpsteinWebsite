<?php // partials/home.php ?>
<section class="historica-hero">
    <span class="historica-hero__eyebrow">Volume I &middot; <?= date('Y') ?></span>
    <h1 class="historica-hero__title">
        Names in the
        <em>public record.</em>
    </h1>
    <p class="historica-hero__lede">
        The Epstein Archive is a curated, read-only reference of people and
        institutions named in publicly available materials &mdash; court
        filings, sworn depositions, unsealed flight logs, congressional
        releases, and major investigative reporting &mdash; surrounding the
        Jeffrey Epstein case. Entries are organized by role, not chronology.
    </p>

    <div class="historica-hero__meta">
        <div>
            <span class="historica-hero__stat"><?= count($categories) ?></span>
            <span class="historica-hero__stat-label">categories</span>
        </div>
        <div>
            <span class="historica-hero__stat">
                <?= array_sum(array_map(fn($c) => (int)$c['article_count'], $categories)) ?>
            </span>
            <span class="historica-hero__stat-label">entries available to you</span>
        </div>
        <div>
            <span class="historica-hero__stat">read-only</span>
            <span class="historica-hero__stat-label">no public editing</span>
        </div>
    </div>
</section>

<section class="historica-featured">
    <header class="historica-featured__header">
        <h2 class="historica-featured__title">// featured entries</h2>
        <span class="historica-featured__hint">rotating selection</span>
    </header>

    <div class="historica-featured__grid">
        <?php foreach ($featured as $f): ?>
            <a class="historica-card" href="?view=article&slug=<?= e($f['slug']) ?>">
                <span class="historica-card__category"><?= e($f['category_name']) ?></span>
                <h3 class="historica-card__name"><?= e($f['name']) ?></h3>
                <span class="historica-card__life"><?= e($f['lifespan'] ?? '') ?></span>
                <p class="historica-card__known"><?= e($f['known_for']) ?></p>
                <span class="historica-card__more">Open file &rarr;</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
