<?php
// data/db.php
// PDO connection helper.
//
// Auto-detects whether the site is running locally (XAMPP) or on the
// live Stablepoint host, and uses the matching credentials. This way
// the same db.php file works in both environments.
//
// IMPORTANT: keep your real live password in this file ONLY on the
// live server. When syncing files local->live, either edit it after
// upload, or keep db.php out of the synced set.

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    // ------------------------------------------------------------------
    // Decide which environment we're in.
    //
    // XAMPP serves the site over localhost / 127.0.0.1 / ::1, anything
    // else is treated as live (epsteinarchive.info or your test domain).
    // ------------------------------------------------------------------
    $host_header = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    $is_local = (
        $host_header === ''                               // CLI
        || str_starts_with($host_header, 'localhost')
        || str_starts_with($host_header, '127.0.0.1')
        || str_starts_with($host_header, '[::1]')
    );

    if ($is_local) {
        // ---- LOCAL: XAMPP defaults --------------------------------
        $host = '127.0.0.1';
        $port = '3306';
        $name = 'epstein_archive_local';
        $user = 'root';
        $pass = '';
    } else {
        // ---- LIVE: Stablepoint cPanel ----------------------------
        $host = '127.0.0.1';
        $port = '3306';
        $name = 'epsteina_archive';
        $user = 'epsteina_archive';
        $pass = '@+?mcwXX.DtU;ZaI';
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    return $pdo;
}
