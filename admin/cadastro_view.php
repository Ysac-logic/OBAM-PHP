<?php
require __DIR__ . '/_layout.php'; require_login();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0); $new = ($_GET['a'] ?? '') === 'new';
$y1 = (int)cfg('ano_inicio_controle', '2024'); $y2 = max($y1, (int)cfg('ano_fim_controle', '2034'));
$meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
if (is_post()) {
    require_admin(); csrf_check(); $act = $_POST['act'] ?? '';
    try {
        if ($act === 'delete' && $id) { qx('DELETE FROM apicultores WHERE id=?', [$id]); flash('Cadastro excluído.'); redirect('cadastros.php'); }
        if ($act === 'save') {
            $cpf = only_digits(post('cpf')); $dt = post('data_filiacao'); $dt = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dt) ? $dt : null;
            if (mb_strlen(post('nome')) < 3) throw new RuntimeException('Informe o nome.');
            if (!cpf_valid($cpf)) throw new RuntimeException('CPF inválido.');
            if (qv('SELECT COUNT(*) FROM apicultores WHERE cpf=? AND id<>?', [$cpf, $id])) throw new RuntimeException('Já existe cadastro com este CPF.');
            $st = in_array(post('status'), ['pendente', 'ativo', 'inativo'], true) ? post('status') : 'pendente';
            $v = [post('nome'), $cpf, post('endereco'), post('localidade'), post('telefone'), post('nucleo'), $dt, post('assinatura'), $st, post('obs')];
            if ($id) qx('UPDATE apicultores SET nome=?,cpf=?,endereco=?,localidade=?,telefone=?,nucleo=?,data_filiacao=?,assinatura=?,status=?,obs=? WHERE id=?', [...$v, $id]);
            else { qx('INSERT INTO apicultores (nome,cpf,endereco,localidade,telefone,nucleo,data_filiacao,assinatura,status,obs,consentimento,consentimento_em,ip) VALUES (?,?,?,?,?,?,?,?,?,?,1,NOW(),?)', [...$v, client_ip()]); $id = (int)db()->lastInsertId(); }
            // controle mensal
            qx('DELETE FROM controle_mensal WHERE apicultor_id=?', [$id]);
            foreach (($_POST['cm'] ?? []) as $ano => $ms) foreach ((array)$ms as $m => $val) { $val = mb_substr(trim((string)$val), 0, 60); $ano = (int)$ano; $m = (int)$m; if ($val !== '' && $ano >= 2000 && $ano <= 2100 && $m >= 1 && $m <= 12) qx('INSERT INTO controle_mensal (apicultor_id,ano,mes,valor) VALUES (?,?,?,?)', [$id, $ano, $m, $val]); }
            flash('Cadastro salvo.'); redirect('cadastro_view.php?id=' . $id);
        }
    } catch (Throwable $e) { flash($e->getMessage(), 'danger'); redirect($id ? "cadastro_view.php?id=$id" : 'cadastro_view.php?a=new'); }
}
$r = $id ? q1('SELECT * FROM apicultores WHERE id=?', [$id]) : null;
if ($id && !$r) { flash('Cadastro não encontrado.', 'warning'); redirect('cadastros.php'); }
if ($new && !is_admin()) redirect('cadastros.php');
$r = $r ?: ['nome' => '', 'cpf' => '', 'endereco' => '', 'localidade' => '', 'telefone' => '', 'nucleo' => '', 'data_filiacao' => '', 'assinatura' => '', 'status' => 'ativo', 'obs' => '', 'consentimento' => 1, 'consentimento_em' => null, 'criado_em' => null, 'ip' => ''];
$cm = []; if ($id) foreach (qa('SELECT ano,mes,valor FROM controle_mensal WHERE apicultor_id=?', [$id]) as $c) $cm[$c['ano']][$c['mes']] = $c['valor'];
admin_header($id ? 'Ficha do apicultor' : 'Novo cadastro', 'cadastros'); ro_notice(); $ro = !is_admin(); ?>
<form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= $id ?>"><fieldset <?= $ro ? 'disabled' : '' ?>>
<div class="adm-card"><h5 class="mb-3 text-center">FICHA DE CADASTRO DO APICULTOR</h5><div class="row g-3">
 <div class="col-md-8"><label class="form-label fw-semibold">NOME</label><input class="form-control" name="nome" required value="<?= e($r['nome']) ?>"></div>
 <div class="col-md-4"><label class="form-label fw-semibold">DATA DE FILIAÇÃO</label><input class="form-control" type="date" name="data_filiacao" value="<?= e($r['data_filiacao']) ?>"></div>
 <div class="col-md-4"><label class="form-label fw-semibold">CPF</label><input class="form-control" name="cpf" required value="<?= e(cpf_fmt($r['cpf'])) ?>"></div>
 <div class="col-md-4"><label class="form-label fw-semibold">TEL.</label><input class="form-control" name="telefone" value="<?= e($r['telefone']) ?>"></div>
 <div class="col-md-4"><label class="form-label fw-semibold">NÚCLEO</label><input class="form-control" name="nucleo" value="<?= e($r['nucleo']) ?>"></div>
 <div class="col-md-8"><label class="form-label fw-semibold">ENDEREÇO</label><input class="form-control" name="endereco" value="<?= e($r['endereco']) ?>"></div>
 <div class="col-md-4"><label class="form-label fw-semibold">LOCALIDADE</label><input class="form-control" name="localidade" value="<?= e($r['localidade']) ?>"></div>
 <div class="col-md-8"><label class="form-label fw-semibold">ASSINATURA</label><input class="form-control" name="assinatura" value="<?= e($r['assinatura']) ?>"></div>
 <div class="col-md-4"><label class="form-label fw-semibold">Status</label><select class="form-select" name="status"><?php foreach (['pendente' => 'Pendente', 'ativo' => 'Ativo', 'inativo' => 'Inativo'] as $k => $l): ?><option value="<?= $k ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select></div>
 <div class="col-12"><label class="form-label fw-semibold">Observações internas</label><textarea class="form-control" name="obs" rows="2"><?= e($r['obs']) ?></textarea></div>
 <?php if ($id): ?><div class="col-12 small text-muted">Cadastrado em <?= e(fdate($r['criado_em'], 'd/m/Y H:i')) ?> · Consentimento LGPD em <?= e(fdate($r['consentimento_em'], 'd/m/Y H:i')) ?> · IP <?= e($r['ip']) ?></div><?php endif; ?>
</div></div>
<div class="adm-card"><h5 class="mb-2">MÊS/ANO — controle mensal</h5><p class="text-muted small">Digite o registro de cada mês (ex.: “Pago”, valor ou observação). Campos vazios ficam em branco.</p>
<div class="cm-grid"><table class="table table-bordered table-sm"><thead><tr><th>MÊS/ANO</th><?php for ($y = $y1; $y <= $y2; $y++): ?><th><?= $y ?></th><?php endfor; ?></tr></thead><tbody>
<?php foreach ($meses as $i => $mn): $m = $i + 1; ?><tr><td><?= $mn ?></td><?php for ($y = $y1; $y <= $y2; $y++): ?><td><input class="form-control form-control-sm" name="cm[<?= $y ?>][<?= $m ?>]" maxlength="60" value="<?= e($cm[$y][$m] ?? '') ?>"></td><?php endfor; ?></tr><?php endforeach; ?></tbody></table></div></div>
</fieldset>
<div class="d-flex gap-2 flex-wrap"><?php if (!$ro): ?><button class="btn btn-y">Salvar</button><?php endif; ?><a class="btn btn-outline-secondary" href="cadastros.php">Voltar à LISTA</a></div></form>
<?php if ($id && !$ro): ?><form method="post" class="mt-3" onsubmit="return confirm('Excluir este cadastro definitivamente?')"><?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= $id ?>"><button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Excluir cadastro</button></form><?php endif; ?>
<?php admin_footer();
