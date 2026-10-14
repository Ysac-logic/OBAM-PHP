<?php
require __DIR__ . '/../inc/bootstrap.php';
if (is_logged()) redirect(u('admin/'));
$err = '';
if (is_post()) {
    csrf_check();
    if (throttle('login', 8, 600)) { $err = 'Muitas tentativas. Aguarde alguns minutos.'; }
    else {
        $r = q1('SELECT * FROM users WHERE username=? AND ativo=1', [post('usuario')]);
        if ($r && password_verify($_POST['senha'] ?? '', $r['senha_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => (int)$r['id'], 'username' => $r['username'], 'nome' => $r['nome'], 'papel' => $r['papel']];
            qx('UPDATE users SET ultimo_login=NOW() WHERE id=?', [$r['id']]);
            if (password_needs_rehash($r['senha_hash'], PASSWORD_DEFAULT)) qx('UPDATE users SET senha_hash=? WHERE id=?', [password_hash($_POST['senha'], PASSWORD_DEFAULT), $r['id']]);
            redirect(u('admin/'));
        }
        usleep(400000); $err = 'Usuário ou senha inválidos.';
    }
}
?><!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex"><title>Entrar — Painel OBAM</title>
<link href="<?= u('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet"><link href="<?= u('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet"><link href="<?= u('admin/admin.css') ?>" rel="stylesheet">
<style>body{display:flex;align-items:center;justify-content:center;min-height:100vh;background:#1c1c1c}.box{background:#fff;border-radius:18px;padding:32px;width:100%;max-width:380px;margin:16px}</style></head><body>
<div class="box"><div class="text-center mb-3"><img src="<?= e(img_url(cfg('logo', 'assets/image/logo-completa.png'))) ?>" alt="OBAM" style="max-height:60px"><h5 class="mt-3 fw-bold">Painel Administrativo</h5></div>
<?php if ($err): ?><div class="alert alert-danger py-2"><?= e($err) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
<label class="form-label">Usuário</label><input class="form-control mb-3" name="usuario" required autofocus autocomplete="username">
<label class="form-label">Senha</label><input class="form-control mb-3" type="password" name="senha" required autocomplete="current-password">
<button class="btn btn-y w-100 py-2">Entrar</button></form>
<div class="text-center mt-3"><a href="<?= u('index.php') ?>" class="small text-muted">← Voltar ao site</a></div></div></body></html>
