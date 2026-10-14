<?php
$feat = q1("SELECT * FROM posts WHERE tipo='post' AND status='publicado' AND destaque=1 ORDER BY publicado_em DESC, id DESC LIMIT 1");
$list = qa("SELECT * FROM posts WHERE tipo='post' AND status='publicado' AND lista_home=1 ORDER BY ordem, publicado_em DESC LIMIT 4");
$hot  = qa("SELECT * FROM posts WHERE tipo='post' AND status='publicado' ORDER BY visualizacoes DESC, publicado_em DESC LIMIT 5");
if (!$feat && !$list && !$hot) return; ?>
<section class="section"><div class="container">
  <div class="d-flex align-items-center gap-3 mb-4" data-aos="fade-up"><span class="section-label"><i class="bi bi-fire"></i> Destaques</span></div>
  <div class="row g-4">
    <div class="col-lg-5" data-aos="fade-right"><?php if ($feat): ?>
      <a href="<?= u('post.php?slug=' . urlencode($feat['slug'])) ?>" class="featured-img-card">
        <img src="<?= e(img_url($feat['imagem'])) ?>" alt="<?= e($feat['titulo']) ?>"><div class="overlay"></div>
        <div class="card-info"><div class="card-tag"><?= e($feat['categoria']) ?> · <?= e(month_pt($feat['publicado_em'])) ?></div><h2><?= e($feat['titulo']) ?></h2><p><?= e($feat['resumo'] ?: mb_strimwidth(strip_tags($feat['conteudo']), 0, 120, '…')) ?></p></div>
      </a><?php endif; ?></div>
    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
      <?php foreach ($list as $p): ?>
      <div class="article-row"><div class="thumb"><img src="<?= e(img_url($p['imagem'])) ?>" alt="" loading="lazy"></div>
        <div><div class="cat"><?= e($p['categoria']) ?></div><h4><a href="<?= u('post.php?slug=' . urlencode($p['slug'])) ?>"><?= e($p['titulo']) ?></a></h4></div></div>
      <?php endforeach; ?></div>
    <div class="col-lg-3" data-aos="fade-left"><div class="trending-box h-100"><div class="box-title">Em Alta</div><ul class="trending-list">
      <?php foreach ($hot as $i => $p): ?><li><span class="num"><?= $i + 1 ?></span><div><h5><a href="<?= u('post.php?slug=' . urlencode($p['slug'])) ?>"><?= e($p['titulo']) ?></a></h5><span class="tdate"><?= e(fdate($p['publicado_em'])) ?></span></div></li><?php endforeach; ?>
    </ul></div></div>
  </div></div></section>
