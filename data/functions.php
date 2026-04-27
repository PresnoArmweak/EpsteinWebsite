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

// ===========================================================================
// CITATION SYSTEM
// ===========================================================================
//
// Site-wide MLA 9 citations. Each source lives in the `sources` table and
// is referenced by its primary key. In an entry's body, write a citation
// inline as one of:
//
//   [47]                       single source
//   [47, 12]                   multiple sources
//   [47][12]                   multiple sources, alternative syntax
//   [47, p. 23]                single source with page locator
//   [47, p. 23; 12, p. 5]      multiple sources, each with locator
//
// On render, every match becomes a comma-separated group of subscript
// links pointing at /index.php?view=sources#cite-{id}.
// ---------------------------------------------------------------------------

/**
 * Fetch every source in id order.
 */
function sources_all(): array
{
    $stmt = db()->query('SELECT * FROM sources ORDER BY id ASC');
    return $stmt->fetchAll();
}

/**
 * Fetch a single source by id, or null if it does not exist.
 */
function source_find(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM sources WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Render an MLA 9 citation string for one source row.
 * Returns plain HTML — already-escaped where appropriate, with
 * <em> tags around container titles per MLA convention.
 */
function source_format_mla(array $s): string
{
    $authors    = trim((string)($s['authors']        ?? ''));
    $title      = trim((string)($s['title']          ?? ''));
    $container  = trim((string)($s['container']      ?? ''));
    $publisher  = trim((string)($s['publisher']      ?? ''));
    $date       = trim((string)($s['date_published'] ?? ''));
    $url        = trim((string)($s['url']            ?? ''));
    $accessed   = trim((string)($s['date_accessed']  ?? ''));
    $location   = trim((string)($s['location']       ?? ''));
    $type       = (string)($s['type'] ?? 'web');

    // Court filings have their own MLA convention: title is italicized,
    // followed by case number, court, and date.
    if ($type === 'court_filing') {
        $parts = [];
        if ($title !== '')     $parts[] = '<em>' . e($title) . '</em>';
        if ($location !== '')  $parts[] = e($location);
        if ($container !== '') $parts[] = e($container);
        if ($date !== '')      $parts[] = e($date);
        $line = implode(', ', $parts);
        if ($url !== '') {
            $line .= ', <a href="' . e($url) . '" target="_blank" rel="noopener">'
                   . e($url) . '</a>';
        }
        return $line . '.';
    }

    // Books: Author. Title. Publisher, Year.
    if ($type === 'book') {
        $parts = [];
        if ($authors !== '')   $parts[] = e(rtrim($authors, '.')) . '.';
        if ($title !== '')     $parts[] = '<em>' . e($title) . '</em>.';
        $tail = [];
        if ($publisher !== '') $tail[] = e($publisher);
        if ($date !== '')      $tail[] = e($date);
        if ($tail)             $parts[] = implode(', ', $tail) . '.';
        if ($location !== '')  $parts[] = e($location) . '.';
        return implode(' ', $parts);
    }

    // Default (web, periodical, gov_doc, film, interview, other):
    //   Author. "Title." Container, Publisher, Date, URL. Accessed Date.
    $parts = [];
    if ($authors !== '')   $parts[] = e(rtrim($authors, '.')) . '.';
    if ($title !== '')     $parts[] = '&ldquo;' . e($title) . '.&rdquo;';
    $middle = [];
    if ($container !== '') $middle[] = '<em>' . e($container) . '</em>';
    if ($publisher !== '' && $publisher !== $container) $middle[] = e($publisher);
    if ($date !== '')      $middle[] = e($date);
    if ($middle)           $parts[] = implode(', ', $middle) . ',';
    if ($url !== '') {
        $parts[] = '<a href="' . e($url) . '" target="_blank" rel="noopener">'
                 . e($url) . '</a>.';
    }
    if ($accessed !== '')  $parts[] = 'Accessed ' . e($accessed) . '.';
    if ($location !== '')  $parts[] = e($location) . '.';
    return implode(' ', $parts);
}

/**
 * Convert all citation markers in an entry body into subscript links.
 *
 * Handles:
 *   [47]                       -> [47]
 *   [47, 12]                   -> [47, 12]   (single bracket, both linked)
 *   [47][12]                   -> [47][12]   (two adjacent brackets)
 *   [47, p. 23]                -> [47] with hover-title showing the locator
 *   [47, p. 23; 12, p. 5]      -> two linked numbers, each with a locator
 *
 * Unknown source IDs are kept as plain text so missing sources are obvious.
 *
 * Input: trusted-but-untransformed entry body (already escaped by caller).
 * Output: HTML with <sub><a>…</a></sub> in place of the markers.
 */
function citations_render(string $body_escaped, array $valid_ids): string
{
    return preg_replace_callback(
        '/\[([^\[\]]+)\]/',
        function ($match) use ($valid_ids) {
            $inner = $match[1];

            // The bracket holds one or more cites separated by ';'.
            // Each cite is "id" or "id, locator".
            //
            // We also allow a comma-only list of bare ids ("1, 3") inside
            // one cite group as shorthand for "1; 3". We disambiguate by
            // checking whether the part after the comma is purely numeric:
            //
            //   "5, 12"     -> two ids: 5 and 12
            //   "5, p. 12"  -> one id (5) with locator "p. 12"

            $cites = [];
            foreach (preg_split('/\s*;\s*/', $inner) as $segment) {
                $segment = trim($segment);
                if ($segment === '') continue;

                if (!preg_match('/^(\d+)(?:\s*,\s*(.+))?$/', $segment, $m)) {
                    // Bracket isn't a citation at all; bail to plain text.
                    return $match[0];
                }
                $first = (int)$m[1];
                $rest  = isset($m[2]) ? trim($m[2]) : '';

                if ($rest === '') {
                    $cites[] = ['id' => $first, 'loc' => ''];
                    continue;
                }

                // Is the rest a comma-separated list of bare ids?
                if (preg_match('/^\d+(\s*,\s*\d+)*$/', $rest)) {
                    $cites[] = ['id' => $first, 'loc' => ''];
                    foreach (preg_split('/\s*,\s*/', $rest) as $extra) {
                        $cites[] = ['id' => (int)$extra, 'loc' => ''];
                    }
                    continue;
                }

                // Otherwise it's a locator like "p. 12" or "§ 4(b)".
                $cites[] = ['id' => $first, 'loc' => $rest];
            }

            $parts = [];
            foreach ($cites as $c) {
                $id = $c['id'];
                $loc = $c['loc'];
                if (!in_array($id, $valid_ids, true)) {
                    $parts[] = '<span class="cite cite--missing"'
                             . ' title="Source #' . $id . ' not in registry">'
                             . $id . '</span>';
                    continue;
                }
                $title_attr = $loc !== ''
                    ? ' title="' . e($loc) . '"'
                    : '';
                $loc_html = $loc !== ''
                    ? ' <span class="cite__loc">(' . e($loc) . ')</span>'
                    : '';
                $parts[] = '<a href="?view=sources#cite-' . $id . '"'
                         . ' class="cite__link"' . $title_attr . '>'
                         . $id . '</a>' . $loc_html;
            }

            return '<sub class="cite">[' . implode(', ', $parts) . ']</sub>';
        },
        $body_escaped
    );
}

/**
 * Render two adjacent bracket groups [47][12] as one combined sub.
 * Run this AFTER citations_render so we collapse already-rendered subs.
 */
function citations_collapse_adjacent(string $html): string
{
    // Two <sub class="cite">[...]</sub> right next to each other (with
    // optional whitespace between them) -> a single comma-separated group.
    // Repeat until no more matches, so [1][2][3] also collapses.
    $prev = null;
    while ($prev !== $html) {
        $prev = $html;
        $html = preg_replace(
            '#\]</sub>(\s*)<sub class="cite">\[#',
            ', $1',
            $html
        );
    }
    return $html;
}

/**
 * Convenience: take a raw entry body, escape it, and apply both
 * citation passes. Returns ready-to-output HTML.
 */
function render_entry_body(string $raw_body): string
{
    // Pull just the ids of valid sources for cheap lookups.
    $stmt = db()->query('SELECT id FROM sources');
    $valid_ids = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

    $escaped = e($raw_body);
    // Re-introduce paragraph breaks the same way the article partial did.
    $paragraphs = preg_split('/\n\s*\n/', trim($escaped));
    $html = '';
    foreach ($paragraphs as $p) {
        $p = trim($p);
        if ($p === '') continue;
        // Convert single newlines inside a paragraph to <br>
        $p_with_breaks = nl2br($p, false);
        $rendered = citations_render($p_with_breaks, $valid_ids);
        $rendered = citations_collapse_adjacent($rendered);
        $html .= '<p>' . $rendered . "</p>\n";
    }
    return $html;
}

