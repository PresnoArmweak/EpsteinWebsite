<?php // partials/article.php ?>
<?php if (!isset($figure) || !$figure): ?>
    <div class="historica-empty">
        <h1>Entry unavailable</h1>
        <?php if (!is_logged_in()): ?>
            <p>
                Either this entry doesn&rsquo;t exist, or it&rsquo;s reserved for
                signed-in readers. <a href="?view=login">Sign in</a> and try again.
            </p>
        <?php else: ?>
            <p>That entry doesn&rsquo;t exist or isn&rsquo;t available to your account.</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <article class="historica-article">
        <header class="historica-article__header">
            <span class="historica-breadcrumb">
                <a href="?view=home">Home</a>
                <span>&rsaquo;</span>
                <a href="?view=category&slug=<?= e($figure['category_slug']) ?>">
                    <?= e($figure['category_name']) ?>
                </a>
            </span>

            <h1 class="historica-article__title"><?= e($figure['name']) ?></h1>

            <p class="historica-article__summary"><?= e($figure['summary']) ?></p>

            <div class="historica-article__chips">
                <?php if ((int)$figure['is_admin_only'] === 1): ?>
                    <span class="historica-pill historica-pill--admin">admin only</span>
                <?php elseif ((int)$figure['is_restricted'] === 1): ?>
                    <span class="historica-pill historica-pill--locked">members only</span>
                <?php endif; ?>
                <span class="historica-pill historica-pill--readonly">read-only</span>
            </div>
        </header>

        <div class="historica-article__layout">
            <div class="historica-article__body">
                <?php
                $body = trim($figure['biography'] ?? '');
                if ($body === '') {
                    echo '<p class="historica-empty" style="padding:1.5rem;text-align:left;margin:0;">'
                       . '<em>This entry is a stub. The full text has not been written yet.</em></p>';
                } else {
                    $paragraphs = preg_split('/\n\s*\n/', $body);
                    foreach ($paragraphs as $p) {
                        if (trim($p) === '') continue;
                        echo '<p>' . e($p) . '</p>';
                    }
                }
                ?>

                <div class="historica-article__notice">
                    <strong>Read-only entry.</strong>
                    The Epstein Archive does not accept public edits or new
                    submissions. Entries are maintained by the editorial team
                    and cite the public record.
                </div>
            </div>

            <aside class="historica-infobox" aria-label="Quick facts">
                <h2 class="historica-infobox__title"><?= e($figure['name']) ?></h2>
                <dl class="historica-infobox__list">
                    <?php if (!empty($figure['lifespan'])): ?>
                        <dt>Dates</dt>
                        <dd><?= e($figure['lifespan']) ?></dd>
                    <?php endif; ?>
                    <?php if (!empty($figure['nationality'])): ?>
                        <dt>Affiliation</dt>
                        <dd><?= e($figure['nationality']) ?></dd>
                    <?php endif; ?>
                    <?php if (!empty($figure['known_for'])): ?>
                        <dt>Connection</dt>
                        <dd><?= e($figure['known_for']) ?></dd>
                    <?php endif; ?>
                    <dt>Category</dt>
                    <dd>
                        <a href="?view=category&slug=<?= e($figure['category_slug']) ?>">
                            <?= e($figure['category_name']) ?>
                        </a>
                    </dd>
                </dl>
            </aside>
        </div>
    </article>
<?php endif; ?>
