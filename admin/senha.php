<?php
require __DIR__ . '/_layout.php'; require_login();
if (is_post()) {
    csrf_check(); $r = q1('SELECT * FROM users WHERE id=?', [user()['id']]);
    if (!$r || !password_verify($_POST['atual'] ?? '', $r['senha_hash'])) flash('Senha atual incorreta.', 'danger');
    elseif (strlen($_POST['nova'] ?? '') < 8 || ($_POST['nova'] ?? '') !== ($_POST['conf'] ?? '')) flash('A nova senha deve ter ao menos 8 caracteres e ser igual à confirmação.', 'danger');
    else { qx('UPDATE users SET senha_hash=? WHERE id=?', [password_hash($_POST['nova'], PASSWORD_DEFAULT), $r['id']]); session_regenerate_id(true); flash('Senha alterada.'); }
    redirect('senha.php');
}
admin_header('Alterar minha senha', 'senha'); ?>
<div class="adm-card" style="max-width:460px"><form method="post"><?= csrf_field() ?>
<label class="form-label">Senha atual</label><input class="form-control mb-3" type="password" name="atual" required autocomplete="current-password">
<label class="form-label">Nova senha (mín. 8)</label><input class="form-control mb-3" type="password" name="nova" required autocomplete="new-password">
<label class="form-label">Confirmar nova senha</label><input class="form-control mb-3" type="password" name="conf" required autocomplete="new-password"><button class="btn btn-y">Alterar senha</button></form></div>
<?php admin_footer();
