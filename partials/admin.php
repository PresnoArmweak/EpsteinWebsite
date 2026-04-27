<?php // partials/admin.php ?>
<?php
// Admin dashboard. Read-only by design: it lists everything the database
// has, including admin-only entries, but provides no edit/add/delete UI.
$stmt = db()->query(
    'SELECT f.id, f.name, f.slug, f.is_restricted, f.is_admin_only,
            c.name AS category_name
     FROM figures f
     JOIN categories c ON c.id = f.category_id
     ORDER BY c.name ASC, f.name ASC'
);
$all_figures = $stmt->fetchAll();

$user_count = (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
?>

<header class="historica-page-header">
    <span class="historica-breadcrumb">
        <a href="?view=home">Home</a>
        <span>&rsaquo;</span>
        <span>Admin</span>
    </span>
    <h1 class="historica-page-header__title">Editorial dashboard</h1>
    <p class="historica-page-header__lede">
        A read-only overview. The Epstein Archive does not allow editing or
        adding entries through the web UI &mdash; content is curated and
        seeded through the database.
    </p>
</header>

<div class="historica-admin-stats">
    <div class="historica-admin-stats__cell">
        <span class="historica-admin-stats__value"><?= count($all_figures) ?></span>
        <span class="historica-admin-stats__label">total entries</span>
    </div>
    <div class="historica-admin-stats__cell">
        <span class="historica-admin-stats__value"><?= count($categories) ?></span>
        <span class="historica-admin-stats__label">categories</span>
    </div>
    <div class="historica-admin-stats__cell">
        <span class="historica-admin-stats__value"><?= $user_count ?></span>
        <span class="historica-admin-stats__label">accounts</span>
    </div>
</div>

<h2 class="historica-admin-subhead">All entries (including restricted)</h2>

<table class="historica-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Visibility</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($all_figures as $f): ?>
            <tr>
                <td>
                    <a href="?view=article&slug=<?= e($f['slug']) ?>">
                        <?= e($f['name']) ?>
                    </a>
                </td>
                <td><?= e($f['category_name']) ?></td>
                <td>
                    <?php if ((int)$f['is_admin_only'] === 1): ?>
                        <span class="historica-pill historica-pill--admin">admin only</span>
                    <?php elseif ((int)$f['is_restricted'] === 1): ?>
                        <span class="historica-pill historica-pill--locked">members only</span>
                    <?php else: ?>
                        <span class="historica-pill historica-pill--readonly">public</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
