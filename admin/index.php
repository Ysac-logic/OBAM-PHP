<?php
require __DIR__ . '/_layout.php';
admin_header('Painel', 'index'); ro_notice();
$c = fn($sql) => (int)qv($sql);
$cards = [
 ['Cadastros de apicultores', $c("SELECT COUNT(*) FROM apicultores"), 'cadastros.php', 'bi-person-lines-fill'],
 ['Cadastros pendentes', $c("SELECT COUNT(*) FROM apicultores WHERE status='pendente'"), 'cadastros.php?status=pendente', 'bi-hourglass-split'],
 ['Denúncias novas', $c("SELECT COUNT(*) FROM denuncias WHERE status='nova'"), 'caixa.php?t=denuncias', 'bi-megaphone'],
 ['Mensagens não lidas', $c("SELECT COUNT(*) FROM mensagens WHERE lida=0"), 'caixa.php?t=mensagens', 'bi-envelope'],
 ['Depoimentos a moderar', $c("SELECT COUNT(*) FROM feedbacks WHERE status='pendente'"), 'caixa.php?t=feedbacks', 'bi-star'],
 ['Publicações', $c("SELECT COUNT(*) FROM posts WHERE tipo='post'"), 'posts.php', 'bi-journal-text'],
];
?>
<div class="row g-3 mb-4"><?php foreach ($cards as $x): ?><div class="col-6 col-lg-4 col-xl-2"><a href="<?= e($x[2]) ?>" class="text-decoration-none text-dark"><div class="stat"><i class="bi <?= $x[3] ?> text-warning fs-4"></i><b><?= $x[1] ?></b><span class="small text-muted"><?= e($x[0]) ?></span></div></a></div><?php endforeach; ?></div>
<div class="row g-3"><div class="col-lg-7"><div class="adm-card"><h5>Últimos cadastros</h5><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Nome</th><th>Localidade</th><th>Status</th><th>Data</th></tr></thead><tbody>
<?php foreach (qa('SELECT id,nome,localidade,status,criado_em FROM apicultores ORDER BY id DESC LIMIT 8') as $r): ?><tr><td><a href="cadastro_view.php?id=<?= $r['id'] ?>"><?= e($r['nome']) ?></a></td><td><?= e($r['localidade']) ?></td><td><?= e($r['status']) ?></td><td><?= e(fdate($r['criado_em'])) ?></td></tr><?php endforeach; ?></tbody></table></div><a class="btn btn-y btn-sm" href="cadastros.php">Abrir LISTA</a></div></div>
<div class="col-lg-5"><div class="adm-card"><h5>Atalhos</h5><div class="d-grid gap-2"><a class="btn btn-outline-dark" href="posts.php"><i class="bi bi-journal-text me-1"></i> Publicações</a><a class="btn btn-outline-dark" href="home.php"><i class="bi bi-layout-text-window-reverse me-1"></i> Ordem da página inicial</a><a class="btn btn-outline-dark" href="menu.php"><i class="bi bi-list-nested me-1"></i> Menu</a><a class="btn btn-outline-dark" href="settings.php"><i class="bi bi-gear me-1"></i> Configurações</a></div></div></div></div>
<?php admin_footer();
