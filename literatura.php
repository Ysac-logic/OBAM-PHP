<?php
require __DIR__ . '/inc/layout.php';
$l = qa('SELECT * FROM literatura ORDER BY ordem,id');
site_header('Literatura Apícola / Melípona'); page_title('Literatura Apícola / Melípona');
?>
<section class="section"><div class="container">
  <div class="text-center mb-5"><span class="section-label"><i class="bi bi-book"></i> Biblioteca</span><h2 class="section-title mt-2">Literatura Apícola e Melípona</h2></div>
  <div class="row g-4"><?php foreach ($l as $x): $href = $x['arquivo'] ? img_url($x['arquivo']) : $x['url']; ?>
    <div class="col-md-6 col-lg-4"><div class="lit-card"><i class="bi bi-file-earmark-pdf"></i><h4 class="mt-2"><?= e($x['titulo']) ?></h4><p class="text-muted"><?= e($x['descricao']) ?></p>
      <?php if ($href): ?><a href="<?= e($href) ?>" target="_blank" rel="noopener" class="btn-outline-y">Abrir / baixar <i class="bi bi-download"></i></a><?php endif; ?></div></div><?php endforeach; ?></div>
</div></section>
<?php site_footer();
