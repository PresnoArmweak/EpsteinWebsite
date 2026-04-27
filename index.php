<?php
require_once __DIR__ . '/data/functions.php';

session_start();

$view   = filter_input(INPUT_GET, 'view') ?: 'home';
$action = filter_input(INPUT_POST, 'action');

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ?view=login');
        exit;
    }
}

function require_admin(): void
{
    require_login();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ?view=denied');
        exit;
    }
}

// Anyone may view home, browse public categories, search public articles,
// or read a public article. Login is only enforced when the requested view
// or article turns out to be restricted.
$public_actions = ['login', 'logout'];

switch ($action) {
    case 'login':
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        if ($username && $password) {
            $user = user_find_by_username($username);
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id']   = (int)$user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];
                header('Location: ?view=home');
                exit;
            } else {
                $login_error = 'Invalid username or password.';
                $view = 'login';
            }
        } else {
            $login_error = 'Enter both fields.';
            $view = 'login';
        }
        break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        session_start();
        header('Location: ?view=home');
        exit;
}

// ---------------------------------------------------------------------------
// View pre-loading
// ---------------------------------------------------------------------------

// Categories sidebar is rendered on every page, so always load it.
$categories = categories_all();

if ($view === 'category') {
    $slug = (string)(filter_input(INPUT_GET, 'slug') ?? '');
    $category = $slug ? category_find_by_slug($slug) : null;
    $figures  = $category ? figures_by_category((int)$category['id']) : [];
}

if ($view === 'article') {
    $slug   = (string)(filter_input(INPUT_GET, 'slug') ?? '');
    $figure = $slug ? figure_find_by_slug($slug) : null;
    // If the article exists but is hidden for the current visibility level,
    // the query returns null. We give a hint to the user that they may need
    // to log in -- but we never confirm whether the article exists.
}

if ($view === 'search') {
    $q = trim((string)(filter_input(INPUT_GET, 'q') ?? ''));
    $results = $q !== '' ? figures_search($q) : [];
}

if ($view === 'home') {
    $featured = figures_featured(4);
}

if ($view === 'admin') {
    require_admin();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Epstein Archive &mdash; Public-Record Reference</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600;8..60,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>

<?php include __DIR__ . '/components/nav.php'; ?>

<div class="container historica-shell mt-4">
    <div class="row g-4">
        <aside class="col-lg-3">
            <?php include __DIR__ . '/components/sidebar.php'; ?>
        </aside>

        <main class="col-lg-9">
            <?php
            if ($view === 'login') {
                include __DIR__ . '/partials/login_form.php';
            } elseif ($view === 'denied') {
                include __DIR__ . '/partials/denied.php';
            } elseif ($view === 'category') {
                include __DIR__ . '/partials/category.php';
            } elseif ($view === 'article') {
                include __DIR__ . '/partials/article.php';
            } elseif ($view === 'search') {
                include __DIR__ . '/partials/search.php';
            } elseif ($view === 'admin') {
                include __DIR__ . '/partials/admin.php';
            } elseif ($view === 'about') {
                include __DIR__ . '/partials/about.php';
            } else {
                include __DIR__ . '/partials/home.php';
            }
            ?>
        </main>
    </div>

    <?php include __DIR__ . '/components/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
