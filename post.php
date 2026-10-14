<?php
require __DIR__ . '/inc/layout.php';
$slug = $_GET['slug'] ?? '';
$p = q1('SELECT * FROM posts WHERE slug=?' . (is_logged() ? '' : " AND status='publicado'"), [$slug]);
if (!$p) { http_response_code(404); site_header('Página não encontrada'); page_title('Página não encontrada'); echo '<section class="section"><div class="container text-center"><p>O conteúdo solicitado não existe.</p><a class="btn-primary-y" href="' . u('index.php') . '">Voltar ao início</a></div></section>'; site_footer(); exit; }
if (empty($_SESSION['seen'][$p['id']])) { qx('UPDATE posts SET visualizacoes=visualizacoes+1 WHERE id=?', [$p['id']]); $_SESSION['seen'][$p['id']] = 1; }
site_header($p['titulo'], '', $p['resumo'] ?: mb_strimwidth(strip_tags($p['conteudo']), 0, 150, '…'));
page_title($p['titulo'], $p['tipo'] === 'post' ? ['Publicações' => 'publicacoes.php'] : []);
$cats = qa("SELECT categoria, COUNT(*) n FROM posts WHERE tipo='post' AND status='publicado' AND categoria<>'' GROUP BY categoria ORDER BY categoria");
$rel = qa("SELECT * FROM posts WHERE tipo='post' AND status='publicado' AND id<>? ORDER BY (categoria=?) DESC, publicado_em DESC LIMIT 3", [$p['id'], $p['categoria']]);
?>
<section class="blog-details section"><div class="container"><div class="row g-4">
  <div class="col-lg-8"><div class="article" data-aos="fade-up">
    <?php if ($p['status'] !== 'publicado'): ?><div class="alert alert-warning m-3">Rascunho — visível apenas para usuários logados.</div><?php endif; ?>
    <?php if ($p['imagem']): ?><div class="post-img"><img src="<?= e(img_url($p['imagem'])) ?>" alt="<?= e($p['titulo']) ?>" class="img-fluid" style="width:100%;max-height:380px;object-fit:cover;"></div><?php endif; ?>
    <div class="content-body" style="padding:36px 36px 28px;">
      <h2 class="title"><?= e($p['titulo']) ?></h2>
      <?php if ($p['tipo'] === 'post'): ?><div class="meta-top"><ul>
        <li><i class="bi bi-person"></i><?= e($p['autor']) ?></li><li><i class="bi bi-calendar3"></i><?= e(fdate($p['publicado_em'])) ?></li><li><i class="bi bi-tag"></i><?= e($p['categoria']) ?></li></ul></div><?php endif; ?>
      <div class="content"><?= $p['conteudo'] ?></div>
    </div></div></div>
  <div class="col-lg-4">
    <?php if ($cats): ?><div class="sidebar-widget mb-4"><h4>Categorias</h4>
      <?php foreach ($cats as $c): ?><a href="<?= u('publicacoes.php?cat=' . urlencode($c['categoria'])) ?>" class="cat-link"><span><i class="bi bi-tag me-2"></i><?= e($c['categoria']) ?></span><span class="cat-count"><?= (int)$c['n'] ?></span></a><?php endforeach; ?></div><?php endif; ?>
    <?php if ($rel): ?><div class="sidebar-widget mb-4"><h4>Leia Também</h4>
      <?php foreach ($rel as $r): ?><div class="article-row"><div class="thumb"><img src="<?= e(img_url($r['imagem'])) ?>" alt="" loading="lazy"></div><div><div class="cat"><?= e($r['categoria']) ?></div><h4><a href="<?= u('post.php?slug=' . urlencode($r['slug'])) ?>"><?= e($r['titulo']) ?></a></h4></div></div><?php endforeach; ?></div><?php endif; ?>
    <div class="sidebar-widget"><h4>Participe</h4><p style="font-size:.87rem;color:var(--gray-500)">Contribua com o OBAM: cadastre-se, faça uma denúncia ou envie um feedback.</p>
      <a href="<?= u('cadastro.php') ?>" class="btn-primary-y d-block text-center mb-2">Cadastro do Apicultor</a>
      <a href="<?= u('denuncias.php') ?>" class="btn-outline-y d-block text-center">Fazer Denúncia</a></div>
  </div></div></div></section>
<?php site_footer();
