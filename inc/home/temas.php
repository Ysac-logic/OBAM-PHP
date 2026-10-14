<?php $cats = qa("SELECT categoria, COUNT(*) n FROM posts WHERE tipo='post' AND status='publicado' AND categoria<>'' GROUP BY categoria ORDER BY categoria"); ?>
<section class="section" style="background:var(--gray-100);padding:48px 0;"><div class="container" data-aos="fade-up">
  <div class="text-center mb-4"><span class="section-label"><i class="bi bi-grid-fill"></i> Explorar por Tema</span></div>
  <div class="topic-pills">
    <?php foreach ($cats as $c): ?><a href="<?= u('publicacoes.php?cat=' . urlencode($c['categoria'])) ?>" class="topic-pill"><i class="bi bi-tag"></i> <?= e($c['categoria']) ?></a><?php endforeach; ?>
    <a href="<?= u('mapa.php') ?>" class="topic-pill"><i class="bi bi-map"></i> Mapa de Apiários</a>
    <a href="<?= u('galeria.php') ?>" class="topic-pill"><i class="bi bi-images"></i> Galeria</a>
    <a href="<?= u('videos.php') ?>" class="topic-pill"><i class="bi bi-play-circle"></i> Vídeos</a>
    <a href="<?= u('cadastro.php') ?>" class="topic-pill"><i class="bi bi-person-vcard"></i> Cadastro do Apicultor</a>
    <a href="<?= u('denuncias.php') ?>" class="topic-pill"><i class="bi bi-megaphone"></i> Denúncias</a>
  </div></div></section>
