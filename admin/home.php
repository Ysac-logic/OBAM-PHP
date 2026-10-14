<?php
require __DIR__ . '/_layout.php'; require_login();
if (is_post()) {
    require_admin(); csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
    if ($act === 'toggle') qx('UPDATE home_sections SET ativo=1-ativo WHERE id=?', [$id]);
    elseif ($act === 'move') {
        $ids = array_map('intval', array_column(qa('SELECT id FROM home_sections ORDER BY ordem,id'), 'id')); $i = array_search($id, $ids, true);
        $j = ($_POST['dir'] ?? '') === 'up' ? $i - 1 : $i + 1;
        if ($i !== false && $j >= 0 && $j < count($ids)) { [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]]; foreach ($ids as $k => $x) qx('UPDATE home_sections SET ordem=? WHERE id=?', [$k + 1, $x]); }
    } elseif ($act === 'rename') qx('UPDATE home_sections SET titulo=? WHERE id=?', [mb_substr(post('titulo'), 0, 120), $id]);
    redirect('home.php');
}
admin_header('Página inicial — ordem das seções', 'home'); ro_notice();
$rows = qa('SELECT * FROM home_sections ORDER BY ordem,id'); $ro = !is_admin(); ?>
<p class="text-muted">Defina a ordem em que cada bloco aparece na página inicial e quais ficam visíveis. O conteúdo dos blocos é editado em <a href="slides.php">Carrossel</a>, <a href="settings.php">Configurações</a> (números e newsletter), <a href="posts.php">Publicações</a> (destaques) e <a href="parceiros.php">Parceiros</a>.</p>
<div class="adm-card"><div class="list-group"><?php foreach ($rows as $i => $r): ?>
<div class="list-group-item d-flex align-items-center gap-2 flex-wrap"><span class="badge bg-dark"><?= $i + 1 ?></span>
<form method="post" class="d-flex gap-2 flex-grow-1"><?= csrf_field() ?><input type="hidden" name="act" value="rename"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input class="form-control form-control-sm" name="titulo" value="<?= e($r['titulo']) ?>" <?= $ro ? 'disabled' : '' ?>><code class="align-self-center"><?= e($r['chave']) ?></code><?php if (!$ro): ?><button class="btn btn-sm btn-outline-dark">Renomear</button><?php endif; ?></form>
<?php if (!$ro): foreach (['up' => 'bi-arrow-up', 'down' => 'bi-arrow-down'] as $d => $ic): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="move"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="dir" value="<?= $d ?>"><button class="btn btn-sm btn-outline-secondary"><i class="bi <?= $ic ?>"></i></button></form><?php endforeach; ?>
<form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm <?= $r['ativo'] ? 'btn-success' : 'btn-outline-secondary' ?>"><?= $r['ativo'] ? 'Visível' : 'Oculto' ?></button></form>
<?php else: ?><span class="badge <?= $r['ativo'] ? 'bg-success' : 'bg-secondary' ?>"><?= $r['ativo'] ? 'Visível' : 'Oculto' ?></span><?php endif; ?></div><?php endforeach; ?></div></div>
<?php admin_footer();
