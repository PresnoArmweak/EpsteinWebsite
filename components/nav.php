<?php // components/nav.php ?>
<nav class="historica-nav">
    <div class="container historica-nav__inner">
        <a class="historica-nav__brand" href="?view=home">
            <span class="historica-nav__brand-mark"></span>
            <span class="historica-nav__brand-name">Epstein Archive</span>
            <span class="historica-nav__brand-tag">public-record reference</span>
        </a>

        <form class="historica-nav__search" method="get" action="">
            <input type="hidden" name="view" value="search">
            <input
                class="form-control"
                type="search"
                name="q"
                placeholder="Search names, roles, institutions&hellip;"
                value="<?= e(filter_input(INPUT_GET, 'q') ?? '') ?>">
            <button type="submit">Search</button>
        </form>

        <div class="historica-nav__account">
            <?php if (is_logged_in()): ?>
                <span class="historica-nav__greeting">
                    <?= e($_SESSION['full_name'] ?? 'Reader') ?>
                    <?php if (is_admin()): ?>
                        <span class="historica-pill historica-pill--admin">admin</span>
                    <?php else: ?>
                        <span class="historica-pill">member</span>
                    <?php endif; ?>
                </span>
                <?php if (is_admin()): ?>
                    <a class="historica-nav__link" href="?view=admin">Admin</a>
                <?php endif; ?>
                <form method="post" class="d-inline">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="historica-nav__link historica-nav__link--button">Sign out</button>
                </form>
            <?php else: ?>
                <a class="historica-nav__link" href="?view=login">Sign in</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
