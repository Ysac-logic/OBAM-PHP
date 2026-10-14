<?php
require __DIR__ . '/inc/layout.php';
$v = qa('SELECT * FROM videos WHERE ativo=1 ORDER BY ordem,id');
site_header('Recomendações de vídeos'); page_title('Recomendações de vídeos');
?>
<section class="section"><div class="container">
  <div class="text-center mb-5"><h2 class="section-title">Recomendações de vídeos</h2></div>
  <div class="video-grid"><?php foreach ($v as $x): $id = youtube_id($x['youtube_url']); if (!$id) continue; ?>
    <div class="video-frame"><iframe src="https://www.youtube-nocookie.com/embed/<?= e($id) ?>" title="<?= e($x['titulo']) ?>" loading="lazy" allowfullscreen></iframe></div><?php endforeach; ?></div>
  <?php if (!$v): ?><p class="text-center text-muted">Nenhum vídeo cadastrado.</p><?php endif; ?>
</div></section>
<?php site_footer();
