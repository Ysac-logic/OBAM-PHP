<?php
require __DIR__ . '/inc/layout.php';
$p = q1("SELECT * FROM posts WHERE slug='quem-somos'");
$team = qa('SELECT * FROM equipe ORDER BY ordem,id');
site_header('Quem Somos', 'about-page');
page_title('Quem Somos');
?>
<section class="about section"><div class="container">
  <div class="row g-5 align-items-center mb-5">
    <div class="col-lg-6" data-aos="fade-right">
      <span class="section-label"><i class="bi bi-info-circle"></i> Quem Somos</span>
      <h2 class="section-title mb-4"><?= e(cfg('site_subtitulo')) ?></h2>
      <p class="fst-italic text-muted mb-3"><?= e(cfg('sobre_lead')) ?></p>
      <div class="d-flex gap-3 mt-4 flex-wrap"><a href="<?= u('contato.php') ?>" class="btn-primary-y"><i class="bi bi-envelope"></i> Fale Conosco</a><a href="<?= u('parceiros.php') ?>" class="btn-outline-y"><i class="bi bi-people"></i> Nossos Parceiros</a></div>
    </div>
    <div class="col-lg-6" data-aos="fade-left"><img src="<?= e(img_url(cfg('sobre_imagem', 'assets/img/about-company-1.jpg'))) ?>" alt="OBAM" class="img-fluid" style="border-radius:var(--radius-lg);border:3px solid var(--yellow);"></div>
  </div>
  <?php if ($p): ?><div class="article mb-5"><div class="content-body content" style="padding:32px"><?= $p['conteudo'] ?></div></div><?php endif; ?>
  <?php if ($team): ?><div class="text-center mb-4"><span class="section-label">Nossa Equipe</span><h2 class="section-title mt-2">Quem faz o OBAM</h2></div>
  <div class="row g-4 justify-content-center"><?php foreach ($team as $t): ?>
    <div class="col-sm-6 col-md-4 col-lg-3 text-center"><div class="partner-card">
      <?php if ($t['foto']): ?><img src="<?= e(img_url($t['foto'])) ?>" alt="<?= e($t['nome']) ?>" class="rounded-circle mb-3" style="width:110px;height:110px;object-fit:cover"><?php endif; ?>
      <h4><?= e($t['nome']) ?></h4><p class="mb-0"><?= e($t['cargo']) ?></p></div></div><?php endforeach; ?></div><?php endif; ?>
</div></section>
<?php site_footer();
