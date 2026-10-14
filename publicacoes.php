<?php
require __DIR__ . '/inc/layout.php';
$cat = trim($_GET['cat'] ?? ''); $s = trim($_GET['q'] ?? ''); $pg = max(1, (int)($_GET['p'] ?? 1)); $per = 12;
$w = "tipo='post' AND status='publicado'"; $par = [];
if ($cat !== '') { $w .= ' AND categoria=?'; $par[] = $cat; }
if ($s !== '') { $w .= ' AND (titulo LIKE ? OR conteudo LIKE ?)'; $par[] = "%$s%"; $par[] = "%$s%"; }
$total = (int)qv("SELECT COUNT(*) FROM posts WHERE $w", $par);
$posts = qa("SELECT * FROM posts WHERE $w ORDER BY publicado_em DESC, id DESC LIMIT $per OFFSET " . (($pg - 1) * $per), $par);
$cats = qa("SELECT categoria, COUNT(*) n FROM posts WHERE tipo='post' AND status='publicado' AND categoria<>'' GROUP BY categoria ORDER BY categoria");
site_header($cat ?: 'Publicações');
page_title($cat ?: 'Todas as Publicações', $cat ? ['Publicações' => 'publicacoes.php'] : []);
?>
<section class="section"><div class="container"><div class="row g-4">
  <div class="col-lg-9">
    <form class="d-flex gap-2 mb-4" method="get"><?php if ($cat): ?><input type="hidden" name="cat" value="<?= e($cat) ?>"><?php endif; ?>
      <input class="form-control" name="q" value="<?= e($s) ?>" placeholder="Buscar publicações..."><button class="btn btn-warning"><i class="bi bi-search"></i></button></form>
    <?php if (!$posts): ?><p class="text-muted">Nenhuma publicação encontrada<?= $cat ? ' em “' . e($cat) . '”' : '' ?> até o momento.</p><?php endif; ?>
    <div class="row g-4"><?php foreach ($posts as $i => $p) post_card($p, 0, 'col-md-6 col-xl-4'); ?></div>
    <?php $pages = (int)ceil($total / $per); if ($pages > 1): ?><nav class="mt-4"><ul class="pagination justify-content-center flex-wrap">
      <?php for ($i = 1; $i <= $pages; $i++): ?><li class="page-item <?= $i === $pg ? 'active' : '' ?>"><a class="page-link" href="?<?= e(http_build_query(['cat' => $cat, 'q' => $s, 'p' => $i])) ?>"><?= $i ?></a></li><?php endfor; ?></ul></nav><?php endif; ?>
  </div>
  <div class="col-lg-3"><div class="sidebar-widget"><h4>Categorias</h4>
    <a href="<?= u('publicacoes.php') ?>" class="cat-link"><span>Todas</span></a>
    <?php foreach ($cats as $c): ?><a href="<?= u('publicacoes.php?cat=' . urlencode($c['categoria'])) ?>" class="cat-link"><span><?= e($c['categoria']) ?></span><span class="cat-count"><?= (int)$c['n'] ?></span></a><?php endforeach; ?>
  </div></div></div></div></section>
<?php site_footer();
