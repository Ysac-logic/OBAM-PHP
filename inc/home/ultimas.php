<?php $posts = qa("SELECT * FROM posts WHERE tipo='post' AND status='publicado' ORDER BY publicado_em DESC, id DESC LIMIT 4"); if (!$posts) return; ?>
<section class="section"><div class="container">
  <div class="d-flex align-items-center gap-3 mb-4" data-aos="fade-up"><span class="section-label"><i class="bi bi-clock-history"></i> Últimas Publicações</span></div>
  <div class="row g-4"><?php foreach ($posts as $i => $p) post_card($p, $i * 50); ?></div>
  <div class="text-center mt-4"><a href="<?= u('publicacoes.php') ?>" class="btn-outline-y">Ver todas as publicações <i class="bi bi-arrow-right"></i></a></div>
</div></section>
