<?php
require __DIR__ . '/_layout.php'; require_admin();
$me = user()['id'];
if (is_post()) {
    csrf_check(); $act = $_POST['act'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    try {
        if ($act === 'create') {
            $un = strtolower(post('username')); $pw = $_POST['senha'] ?? '';
            if (!preg_match('/^[a-z0-9._-]{3,50}$/', $un)) throw new RuntimeException('Usuário: 3 a 50 caracteres (letras, números, ponto, hífen, sublinhado).');
            if (strlen($pw) < 8) throw new RuntimeException('A senha deve ter ao menos 8 caracteres.');
            if (post('nome') === '') throw new RuntimeException('Informe o nome.');
            if (qv('SELECT COUNT(*) FROM users WHERE username=?', [$un])) throw new RuntimeException('Este usuário já existe.');
            qx('INSERT INTO users (username,nome,senha_hash,papel) VALUES (?,?,?,?)', [$un, post('nome'), password_hash($pw, PASSWORD_DEFAULT), post('papel') === 'admin' ? 'admin' : 'responsavel']); flash('Usuário criado.');
        } elseif ($act === 'update' && $id) {
            $papel = post('papel') === 'admin' ? 'admin' : 'responsavel'; $ativo = isset($_POST['ativo']) ? 1 : 0;
            if ($id === $me && ($papel !== 'admin' || !$ativo)) throw new RuntimeException('Você não pode remover seu próprio acesso de administrador.');
            qx('UPDATE users SET nome=?,papel=?,ativo=? WHERE id=?', [post('nome'), $papel, $ativo, $id]);
            if (($_POST['senha'] ?? '') !== '') { if (strlen($_POST['senha']) < 8) throw new RuntimeException('A nova senha deve ter ao menos 8 caracteres.'); qx('UPDATE users SET senha_hash=? WHERE id=?', [password_hash($_POST['senha'], PASSWORD_DEFAULT), $id]); }
            flash('Usuário atualizado.');
        } elseif ($act === 'delete' && $id) {
            if ($id === $me) throw new RuntimeException('Você não pode excluir a si mesmo.');
            qx('DELETE FROM users WHERE id=?', [$id]); flash('Usuário excluído.');
        }
    } catch (Throwable $e) { flash($e->getMessage(), 'danger'); }
    redirect('users.php');
}
admin_header('Usuários', 'users'); $rows = qa('SELECT * FROM users ORDER BY id'); ?>
<div class="adm-card"><h5>Novo usuário</h5><form method="post" class="row g-2"><?= csrf_field() ?><input type="hidden" name="act" value="create">
<div class="col-md-3"><input class="form-control" name="nome" placeholder="Nome" required></div><div class="col-md-2"><input class="form-control" name="username" placeholder="Usuário" required autocomplete="off"></div>
<div class="col-md-3"><input class="form-control" type="password" name="senha" placeholder="Senha (mín. 8)" required autocomplete="new-password"></div>
<div class="col-md-2"><select class="form-select" name="papel"><option value="responsavel">Responsável</option><option value="admin">ADMIN</option></select></div><div class="col-md-2"><button class="btn btn-y w-100">Criar</button></div></form>
<div class="form-text mt-2"><b>ADMIN</b>: acesso total (cria, edita, exclui). <b>Responsável</b>: visualiza o site e os cadastros e exporta .CSV, sem editar nem excluir.</div></div>
<div class="adm-card"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Nome</th><th>Usuário</th><th>Perfil</th><th>Ativo</th><th>Nova senha</th><th>Último acesso</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="update"><input type="hidden" name="id" value="<?= $r['id'] ?>">
<td><input class="form-control form-control-sm" name="nome" value="<?= e($r['nome']) ?>"></td><td><code><?= e($r['username']) ?></code></td>
<td><select class="form-select form-select-sm" name="papel"><option value="responsavel" <?= $r['papel'] === 'responsavel' ? 'selected' : '' ?>>Responsável</option><option value="admin" <?= $r['papel'] === 'admin' ? 'selected' : '' ?>>ADMIN</option></select></td>
<td><input type="checkbox" class="form-check-input" name="ativo" value="1" <?= $r['ativo'] ? 'checked' : '' ?>></td><td><input class="form-control form-control-sm" type="password" name="senha" placeholder="(manter)" autocomplete="new-password"></td>
<td class="small"><?= e(fdate($r['ultimo_login'], 'd/m/Y H:i')) ?></td><td class="text-end text-nowrap"><button class="btn btn-sm btn-dark">Salvar</button></form>
<?php if ((int)$r['id'] !== (int)$me) echo confirm_form('delete', ['id' => $r['id']], 'Excluir', 'Excluir este usuário?'); ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php admin_footer();
