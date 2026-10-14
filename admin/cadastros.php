<?php
require __DIR__ . '/_layout.php'; require_login();
$S = ['pendente' => 'Pendente', 'ativo' => 'Ativo', 'inativo' => 'Inativo'];
$q = trim($_GET['q'] ?? ''); $st = $_GET['status'] ?? ''; $nuc = trim($_GET['nucleo'] ?? ''); $pg = max(1, (int)($_GET['p'] ?? 1)); $per = 25;
$w = '1=1'; $par = [];
if ($q !== '') { $w .= ' AND (nome LIKE ? OR cpf LIKE ? OR localidade LIKE ? OR telefone LIKE ?)'; $l = "%$q%"; array_push($par, $l, '%' . only_digits($q) . '%', $l, $l); if (only_digits($q) === '') { $par[1] = '%__none__%'; } }
if (isset($S[$st])) { $w .= ' AND status=?'; $par[] = $st; }
if ($nuc !== '') { $w .= ' AND nucleo=?'; $par[] = $nuc; }
if (is_post() && ($_POST['act'] ?? '') === 'status') { require_admin(); csrf_check(); if (isset($S[$_POST['status'] ?? ''])) qx('UPDATE apicultores SET status=? WHERE id=?', [$_POST['status'], (int)$_POST['id']]); redirect($_SERVER['REQUEST_URI']); }
if (isset($_GET['export'])) { // CSV (admin e responsável)
    $full = $_GET['export'] === 'full'; $y1 = (int)cfg('ano_inicio_controle', '2024'); $y2 = (int)cfg('ano_fim_controle', '2034');
    $h = ['ID', 'Nome', 'CPF', 'Endereço', 'Localidade', 'Telefone', 'Núcleo', 'Data de filiação', 'Assinatura', 'Status', 'Consentimento LGPD em', 'Cadastrado em'];
    if ($full) for ($y = $y1; $y <= $y2; $y++) for ($m = 1; $m <= 12; $m++) $h[] = sprintf('%02d/%d', $m, $y);
    $gen = (function () use ($w, $par, $full, $y1, $y2) {
        foreach (qa("SELECT * FROM apicultores WHERE $w ORDER BY nome", $par) as $r) {
            $row = [$r['id'], $r['nome'], cpf_fmt($r['cpf']), $r['endereco'], $r['localidade'], $r['telefone'], $r['nucleo'], fdate($r['data_filiacao']), $r['assinatura'], $r['status'], fdate($r['consentimento_em'], 'd/m/Y H:i'), fdate($r['criado_em'], 'd/m/Y H:i')];
            if ($full) { $cm = []; foreach (qa('SELECT ano,mes,valor FROM controle_mensal WHERE apicultor_id=?', [$r['id']]) as $c) $cm[$c['ano'] . '-' . $c['mes']] = $c['valor']; for ($y = $y1; $y <= $y2; $y++) for ($m = 1; $m <= 12; $m++) $row[] = $cm["$y-$m"] ?? ''; }
            yield $row;
        }
    })();
    csv_send('cadastros-apicultores-' . date('Ymd-His') . '.csv', $h, $gen);
}
$total = (int)qv("SELECT COUNT(*) FROM apicultores WHERE $w", $par); $pages = max(1, (int)ceil($total / $per)); $pg = min($pg, $pages);
$rows = qa("SELECT * FROM apicultores WHERE $w ORDER BY id DESC LIMIT $per OFFSET " . (($pg - 1) * $per), $par);
$nucs = array_column(qa("SELECT DISTINCT nucleo FROM apicultores WHERE nucleo<>'' ORDER BY nucleo"), 'nucleo');
$qs = fn($extra = []) => http_build_query(array_filter(array_merge(['q' => $q, 'status' => $st, 'nucleo' => $nuc], $extra), fn($v) => $v !== '' && $v !== null));
admin_header('LISTA — Cadastros de apicultores', 'cadastros'); ro_notice(); $ro = !is_admin(); ?>
<div class="adm-card">
<form class="row g-2 mb-3" method="get"><div class="col-md-4"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Buscar nome, CPF, localidade, telefone"></div>
<div class="col-6 col-md-2"><select class="form-select" name="status"><option value="">Todos os status</option><?php foreach ($S as $k => $l): ?><option value="<?= $k ?>" <?= $st === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select></div>
<div class="col-6 col-md-2"><select class="form-select" name="nucleo"><option value="">Todos os núcleos</option><?php foreach ($nucs as $n): ?><option <?= $nuc === $n ? 'selected' : '' ?>><?= e($n) ?></option><?php endforeach; ?></select></div>
<div class="col-12 col-md-4 d-flex gap-2 flex-wrap"><button class="btn btn-dark">Filtrar</button><a class="btn btn-outline-secondary" href="cadastros.php">Limpar</a>
<div class="btn-group"><a class="btn btn-y" href="?<?= e($qs(['export' => '1'])) ?>"><i class="bi bi-filetype-csv"></i> Exportar .CSV</a><button class="btn btn-y dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button><ul class="dropdown-menu"><li><a class="dropdown-item" href="?<?= e($qs(['export' => 'full'])) ?>">CSV com controle mensal</a></li></ul></div></div></form>
<div class="d-flex justify-content-between mb-2 flex-wrap gap-2"><span class="text-muted"><?= $total ?> cadastro(s)</span><?php if (!$ro): ?><a class="btn btn-outline-dark btn-sm" href="cadastro_view.php?a=new"><i class="bi bi-plus-lg"></i> Novo cadastro</a><?php endif; ?></div>
<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Nome</th><th>CPF</th><th>Localidade</th><th>Telefone</th><th>Núcleo</th><th>Filiação</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><a href="cadastro_view.php?id=<?= $r['id'] ?>"><strong><?= e($r['nome']) ?></strong></a><div class="small text-muted"><?= e(fdate($r['criado_em'], 'd/m/Y H:i')) ?></div></td><td class="text-nowrap"><?= e(cpf_fmt($r['cpf'])) ?></td><td><?= e($r['localidade']) ?></td><td class="text-nowrap"><?= e($r['telefone']) ?></td><td><?= e($r['nucleo']) ?></td><td><?= e(fdate($r['data_filiacao'])) ?></td>
<td><span class="badge bg-<?= ['pendente' => 'warning text-dark', 'ativo' => 'success', 'inativo' => 'secondary'][$r['status']] ?>"><?= e($S[$r['status']]) ?></span></td>
<td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-dark" href="cadastro_view.php?id=<?= $r['id'] ?>"><i class="bi <?= $ro ? 'bi-eye' : 'bi-pencil' ?>"></i></a>
<?php if (!$ro): ?><form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="act" value="status"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="status" value="<?= $r['status'] === 'ativo' ? 'inativo' : 'ativo' ?>"><button class="btn btn-sm btn-outline-success" title="<?= $r['status'] === 'ativo' ? 'Inativar' : 'Ativar' ?>"><i class="bi bi-<?= $r['status'] === 'ativo' ? 'pause-circle' : 'check-circle' ?>"></i></button></form><?php endif; ?></td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="8" class="text-muted">Nenhum cadastro encontrado.</td></tr><?php endif; ?></tbody></table></div>
<?php if ($pages > 1): ?><nav><ul class="pagination flex-wrap"><?php for ($i = 1; $i <= $pages; $i++): ?><li class="page-item <?= $i === $pg ? 'active' : '' ?>"><a class="page-link" href="?<?= e($qs(['p' => $i])) ?>"><?= $i ?></a></li><?php endfor; ?></ul></nav><?php endif; ?></div>
<?php admin_footer();
