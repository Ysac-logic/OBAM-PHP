<?php
require_once __DIR__ . '/../config.php';
date_default_timezone_set(TIMEZONE);
error_reporting(E_ALL);
ini_set('display_errors', DEBUG ? '1' : '0');
mb_internal_encoding('UTF-8');

$_script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
define('BASE', rtrim(preg_replace('#/admin$#', '', $_script), '/'));
define('ROOT', dirname(__DIR__));

if (session_status() === PHP_SESSION_NONE) {
    session_name('obam_sess');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')]);
    session_start();
}

/* ---------- Banco ---------- */
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec("SET time_zone = '" . date('P') . "'");
    } catch (Throwable $e) {
        http_response_code(500);
        echo '<!doctype html><meta charset="utf-8"><body style="font-family:sans-serif;max-width:640px;margin:60px auto;padding:0 16px">'
           . '<h2>Não foi possível conectar ao banco de dados</h2><p>Edite o arquivo <code>config.php</code> com os dados do seu MySQL e, se for a primeira vez, execute <a href="' . BASE . '/install.php">install.php</a> ou importe <code>schema.sql</code> no MySQL Workbench.</p>'
           . (DEBUG ? '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>' : '') . '</body>';
        exit;
    }
    return $pdo;
}
function qa(string $sql, array $p = []): array { $s = db()->prepare($sql); $s->execute($p); return $s->fetchAll(); }
function q1(string $sql, array $p = []) { $s = db()->prepare($sql); $s->execute($p); $r = $s->fetch(); return $r === false ? null : $r; }
function qx(string $sql, array $p = []): int { $s = db()->prepare($sql); $s->execute($p); return $s->rowCount(); }
function qv(string $sql, array $p = []) { $s = db()->prepare($sql); $s->execute($p); return $s->fetchColumn(); }

/* ---------- Helpers ---------- */
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function u(string $path = ''): string { return BASE . '/' . ltrim($path, '/'); }
function img_url(?string $p): string { if (!$p) return ''; return preg_match('#^(https?:)?//#', $p) ? $p : u($p); }
function link_url(?string $p): string { if (!$p || $p === '#') return '#'; return preg_match('#^(https?:|mailto:|tel:)#', $p) ? $p : u($p); }
function redirect(string $to): never { header('Location: ' . $to); exit; }
function slugify(string $s): string {
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
    $s = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $s), '-'));
    return $s !== '' ? $s : 'item';
}
function fdate(?string $d, string $f = 'd/m/Y'): string { return $d && $d !== '0000-00-00' ? date($f, strtotime($d)) : ''; }
function month_pt(string $d): string { $m = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']; $t = strtotime($d); return $m[(int)date('n', $t) - 1] . ' ' . date('Y', $t); }

function settings_all(): array {
    static $c = null;
    if ($c === null) { $c = []; try { foreach (qa('SELECT k,v FROM settings') as $r) $c[$r['k']] = $r['v']; } catch (Throwable $e) {} }
    return $c;
}
function cfg(string $k, string $d = ''): string { $s = settings_all(); return isset($s[$k]) && $s[$k] !== '' ? $s[$k] : $d; }

function flash(?string $msg = null, string $type = 'success') {
    if ($msg !== null) { $_SESSION['flash'][] = [$type, $msg]; return; }
    $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f;
}
function flash_html(): string {
    $o = ''; foreach (flash() as [$t, $m]) $o .= '<div class="alert alert-' . e($t) . ' alert-dismissible fade show" role="alert">' . e($m) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    return $o;
}

/* ---------- CSRF ---------- */
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check(): void {
    $t = $_POST['_csrf'] ?? '';
    if (!is_string($t) || !hash_equals($_SESSION['csrf'] ?? '', $t)) { http_response_code(419); exit('Sessão expirada ou requisição inválida. Volte e tente novamente.'); }
}
function is_post(): bool { return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'; }
function post(string $k, string $d = ''): string { $v = $_POST[$k] ?? $d; return is_string($v) ? trim($v) : $d; }
function client_ip(): string { return substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45); }
function throttle(string $key, int $max, int $window): bool { // true = bloqueado
    $now = time(); $a = array_filter($_SESSION['thr'][$key] ?? [], fn($t) => $t > $now - $window);
    if (count($a) >= $max) { $_SESSION['thr'][$key] = $a; return true; }
    $a[] = $now; $_SESSION['thr'][$key] = $a; return false;
}

/* ---------- Auth ---------- */
function user(): ?array { return $_SESSION['user'] ?? null; }
function is_logged(): bool { return !empty($_SESSION['user']); }
function is_admin(): bool { return (($_SESSION['user']['papel'] ?? '') === 'admin'); }
function require_login(): void { if (!is_logged()) redirect(u('admin/login.php')); }
function require_admin(): void {
    require_login();
    if (!is_admin()) { flash('Seu perfil (responsável) permite apenas visualizar e exportar.', 'warning'); redirect($_SERVER['HTTP_REFERER'] ?? u('admin/')); }
}

/* ---------- Upload ---------- */
function upload_file(string $field, string $subdir, array $exts, int $maxMb = 5): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Falha no upload (código ' . $f['error'] . ').');
    if ($f['size'] > $maxMb * 1048576) throw new RuntimeException("Arquivo maior que {$maxMb} MB.");
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $exts, true)) throw new RuntimeException('Tipo de arquivo não permitido (' . implode(', ', $exts) . ').');
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) && !@getimagesize($f['tmp_name'])) throw new RuntimeException('Imagem inválida.');
    $dir = ROOT . '/uploads/' . $subdir; if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], "$dir/$name")) throw new RuntimeException('Não foi possível salvar o arquivo (verifique permissão da pasta uploads).');
    return 'uploads/' . $subdir . '/' . $name;
}
function upload_image(string $field): ?string { return upload_file($field, 'img', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif']); }

/* ---------- CPF / CSV ---------- */
function only_digits(string $s): string { return preg_replace('/\D+/', '', $s); }
function cpf_valid(string $cpf): bool {
    $cpf = only_digits($cpf);
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) { $s = 0; for ($c = 0; $c < $t; $c++) $s += $cpf[$c] * (($t + 1) - $c); $d = ((10 * $s) % 11) % 10; if ($cpf[$t] != $d) return false; }
    return true;
}
function cpf_fmt(string $c): string { $c = only_digits($c); return strlen($c) === 11 ? substr($c, 0, 3) . '.' . substr($c, 3, 3) . '.' . substr($c, 6, 3) . '-' . substr($c, 9) : $c; }
function csv_cell($v): string { $v = (string)$v; if ($v !== '' && strpbrk($v[0], "=+-@\t\r")) $v = "'" . $v; return '"' . str_replace('"', '""', $v) . '"'; }
function csv_send(string $filename, array $header, iterable $rows): never {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF" . 'sep=;' . "\r\n";
    echo implode(';', array_map('csv_cell', $header)) . "\r\n";
    foreach ($rows as $r) echo implode(';', array_map('csv_cell', $r)) . "\r\n";
    exit;
}
function youtube_id(string $url): string {
    if (preg_match('#(?:v=|youtu\.be/|embed/)([A-Za-z0-9_-]{11})#', $url, $m)) return $m[1]; return '';
}
