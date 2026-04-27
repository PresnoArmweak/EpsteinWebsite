<?php
// data/functions.php
// Procedural data-access layer, in the same style as the source repo.

require_once __DIR__ . '/db.php';

// ---------------------------------------------------------------------------
// Users / auth
// ---------------------------------------------------------------------------

function user_find_by_username(string $username): ?array
{
    $stmt = db()->prepare(
        'SELECT id, username, full_name, password_hash, role
         FROM users WHERE username = :u LIMIT 1'
    );
    $stmt->execute([':u' => $username]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    return [
        'id'        => (int)$_SESSION['user_id'],
        'full_name' => $_SESSION['full_name'] ?? '',
        'role'      => $_SESSION['role'] ?? 'member',
    ];
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

// ---------------------------------------------------------------------------
// Visibility helper
//   Guests:  only public articles                (is_restricted = 0)
//   Members: public + restricted                  (is_admin_only = 0)
//   Admins:  everything
// Returns a SQL fragment (without the WHERE keyword) plus its bound params.
// ---------------------------------------------------------------------------

function visibility_clause(string $alias = 'f'): array
{
    if (is_admin()) {
        return ['1 = 1', []];
    }
    if (is_logged_in()) {
        return ["{$alias}.is_admin_only = 0", []];
    }
    return ["{$alias}.is_restricted = 0 AND {$alias}.is_admin_only = 0", []];
}

// ---------------------------------------------------------------------------
// Categories
// ---------------------------------------------------------------------------

function categories_all(): array
{
    [$vis, $params] = visibility_clause('f');
    $sql =
        "SELECT c.id, c.name, c.slug, c.description,
                COUNT(f.id) AS article_count
         FROM categories c
         LEFT JOIN figures f
                ON f.category_id = c.id
               AND {$vis}
         GROUP BY c.id, c.name, c.slug, c.description
         ORDER BY c.name ASC";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function category_find_by_slug(string $slug): ?array
{
    $stmt = db()->prepare(
        'SELECT id, name, slug, description FROM categories WHERE slug = :s LIMIT 1'
    );
    $stmt->execute([':s' => $slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

// ---------------------------------------------------------------------------
// Figures (articles)
// ---------------------------------------------------------------------------

function figures_by_category(int $category_id): array
{
    [$vis, $params] = visibility_clause('f');
    $params[':cid'] = $category_id;
    $sql =
        "SELECT f.id, f.name, f.slug, f.lifespan, f.known_for,
                f.is_restricted, f.is_admin_only
         FROM figures f
         WHERE f.category_id = :cid AND {$vis}
         ORDER BY f.name ASC";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function figure_find_by_slug(string $slug): ?array
{
    [$vis, $params] = visibility_clause('f');
    $params[':slug'] = $slug;
    $sql =
        "SELECT f.*, c.name AS category_name, c.slug AS category_slug
         FROM figures f
         JOIN categories c ON c.id = f.category_id
         WHERE f.slug = :slug AND {$vis}
         LIMIT 1";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}

function figures_search(string $q): array
{
    [$vis, $params] = visibility_clause('f');
    // PDO with emulated prepares OFF requires each named placeholder
    // to appear exactly once in the SQL, so we bind three names that
    // all carry the same wildcarded value.
    $like = '%' . $q . '%';
    $params[':q_name']    = $like;
    $params[':q_known']   = $like;
    $params[':q_summary'] = $like;
    $sql =
        "SELECT f.id, f.name, f.slug, f.known_for, c.name AS category_name
         FROM figures f
         JOIN categories c ON c.id = f.category_id
         WHERE (f.name LIKE :q_name
                OR f.known_for LIKE :q_known
                OR f.summary  LIKE :q_summary)
           AND {$vis}
         ORDER BY f.name ASC
         LIMIT 50";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function figures_featured(int $limit = 4): array
{
    [$vis, $params] = visibility_clause('f');
    $sql =
        "SELECT f.id, f.name, f.slug, f.known_for, f.lifespan,
                c.name AS category_name, c.slug AS category_slug
         FROM figures f
         JOIN categories c ON c.id = f.category_id
         WHERE {$vis}
         ORDER BY RAND()
         LIMIT " . (int)$limit;
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// ---------------------------------------------------------------------------
// Tiny escape helper for templates
// ---------------------------------------------------------------------------

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
