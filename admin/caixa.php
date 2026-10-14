<?php
require __DIR__ . '/_layout.php'; require_login();
$T = [
 'denuncias' => ['Denúncias', 'denuncias', ['nova' => 'Nova', 'em_analise' => 'Em análise', 'resolvida' => 'Resolvida'], 'status',
   ['Data' => 'criado_em', 'Nome' => 'nome', 'Município' => 'municipio', 'Tipo' => 'tipo', 'Ocorrência em' => 'data_ocorrencia', 'Descrição' => 'descricao', 'Contato' => 'contato', 'Status' => 'status']],
 'mensagens' => ['Mensagens de contato', 'mensagens', [0 => 'Não lida', 1 => 'Lida'], 'lida',
   ['Data' => 'criado_em', 'Nome' => 'nome', 'E-mail' => 'email', 'Assunto' => 'assunto', 'Mensagem' => 'mensagem', 'Lida' => 'lida']],
 'feedbacks' => ['Elogios, sugestões e críticas', 'feedbacks', ['pendente' => 'Pendente', 'aprovado' => 'Aprovado (público)'], 'status',
   ['Data' => 'criado_em', 'Nome' => 'nome', 'Perfil' => 'perfil', 'Tipo' => 'tipo', 'Nota' => 'nota', 'Mensagem' => 'mensagem', 'Status' => 'status']],
 'newsletter' => ['Inscritos na newsletter', 'newsletter', null, null, ['Data' => 'criado_em', 'E-mail' => 'email']],
];
$t = $_GET['t'] ?? $_POST['t'] ?? 'denuncias'; if (!isset($T[$t])) $t = 'denuncias';
[$title, $table, $states, $scol, $cols] = $T[$t];
if (is_post()) {
    require_admin(); csrf_check(); $id = (int)($_POST['id'] ?? 0); $act = $_POST['act'] ?? '';
    if ($act === 'delete') { qx("DELETE FROM `$table` WHERE id=?", [$id]); flash('Excluído.'); }
    elseif ($act === 'state' && $states && array_key_exists($_POST['v'] ?? '', $states)) { qx("UPDATE `$table` SET `$scol`=? WHERE id=?", [$_POST['v'], $id]); flash('Atualizado.'); }
    redirect("caixa.php?t=$t");
}
if (isset($_GET['export'])) { // CSV: admin e responsável
    $rows = (function () use ($table, $cols) { foreach (qa("SELECT * FROM `$table` ORDER BY id DESC") as $r) { $o = []; foreach ($cols as $c) $o[] = $r[$c] ?? ''; yield $o; } })();
    csv_send($t . '-' . date('Ymd-His') . '.csv', array_keys($cols), $rows);
}
if ($t === 'mensagens' && is_admin() && isset($_GET['id'])) qx('UPDATE mensagens SET lida=1 WHERE id=?', [(int)$_GET['id']]);
$rows = qa("SELECT * FROM `$table` ORDER BY id DESC LIMIT 300"); $ro = !is_admin();
admin_header($title, $t); ro_notice(); ?>
<div class="adm-card"><div class="d-flex justify-content-between mb-3 flex-wrap gap-2"><span class="text-muted"><?= count($rows) ?> registro(s) <?= count($rows) === 300 ? '(últimos 300; o CSV exporta todos)' : '' ?></span><a class="btn btn-y" href="?t=<?= e($t) ?>&export=1"><i class="bi bi-filetype-csv"></i> Exportar .CSV</a></div>
<div class="table-responsive"><table class="table table-hover align-top"><thead><tr><?php foreach ($cols as $l => $c): ?><th><?= e($l) ?></th><?php endforeach; ?><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><?php foreach ($cols as $l => $c): $v = $r[$c] ?? ''; if ($c === 'criado_em') $v = fdate($v, 'd/m/Y H:i'); elseif ($c === 'data_ocorrencia') $v = fdate($v); elseif ($c === 'lida') $v = $v ? 'Sim' : 'Não'; ?>
<td style="<?= in_array($c, ['descricao', 'mensagem'], true) ? 'min-width:260px;white-space:pre-wrap' : '' ?>"><?= e($v) ?></td><?php endforeach; ?>
<td class="text-end text-nowrap"><?php if (!$ro): ?>
<?php if ($states): ?><form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="t" value="<?= e($t) ?>"><input type="hidden" name="act" value="state"><input type="hidden" name="id" value="<?= $r['id'] ?>">
<select name="v" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()"><?php foreach ($states as $k => $l): ?><option value="<?= e($k) ?>" <?= (string)$r[$scol] === (string)$k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></form><?php endif; ?>
<form method="post" class="d-inline" onsubmit="return confirm('Excluir?')"><?= csrf_field() ?><input type="hidden" name="t" value="<?= e($t) ?>"><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form><?php endif; ?></td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="<?= count($cols) + 1 ?>" class="text-muted">Nada recebido ainda.</td></tr><?php endif; ?></tbody></table></div></div>
<?php admin_footer();
