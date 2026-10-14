<?php
require __DIR__ . '/inc/layout.php';
$g = qa('SELECT * FROM galeria ORDER BY ordem,id');
site_header('Galeria de Fotos'); page_title('Galeria de Fotos');
?>
<section class="section"><div class="container">
  <div class="text-center mb-5"><span class="section-label"><i class="bi bi-images"></i> Fotografia</span><h2 class="section-title mt-2">Momentos do OBAM</h2><p class="text-muted mt-2">Registros de campo, eventos e a beleza das nossas abelhas.</p></div>
  <div class="gallery-grid"><?php foreach ($g as $i => $f): ?><a class="gallery-item" href="<?= e(img_url($f['imagem'])) ?>" data-caption="<?= e($f['legenda']) ?>" data-aos="zoom-in" data-aos-delay="<?= ($i % 4) * 50 ?>"><img src="<?= e(img_url($f['imagem'])) ?>" alt="<?= e($f['legenda']) ?>" loading="lazy"><div class="gallery-overlay"><i class="bi bi-zoom-in"></i></div></a><?php endforeach; ?></div>
  <?php if (!$g): ?><p class="text-center text-muted">Nenhuma foto cadastrada.</p><?php endif; ?>
</div></section>
<?php site_footer();
