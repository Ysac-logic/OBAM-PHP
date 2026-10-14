<?php
require __DIR__ . '/inc/layout.php';
$esf = $_GET['esfera'] ?? 'Municipal'; if (!in_array($esf, ['Municipal', 'Estadual', 'Federal'], true)) $esf = 'Municipal';
$rows = qa('SELECT * FROM orgaos WHERE esfera=? ORDER BY ordem,id', [$esf]);
site_header("Órgãos — $esf"); page_title("Órgãos — $esf");
?>
<section class="section"><div class="container">
  <div class="text-center mb-4"><span class="section-label"><i class="bi bi-building"></i> Informações Importantes</span><h2 class="section-title mt-2">Órgãos por esfera</h2>
    <div class="mt-3"><?php foreach (['Municipal', 'Estadual', 'Federal'] as $x): ?><a href="?esfera=<?= $x ?>" class="topic-pill" <?= $x === $esf ? 'style="background:var(--yellow)"' : '' ?>><?= $x ?></a><?php endforeach; ?></div></div>
  <div class="table-responsive"><table class="table table-hover align-middle bg-white">
    <thead><tr><th>Órgão</th><th>Sede</th><th>Link</th></tr></thead><tbody>
    <?php foreach ($rows as $r): ?><tr><td><strong><?= e($r['nome']) ?></strong></td><td><?= e($r['sede']) ?></td><td><?php if ($r['link']): ?><a href="<?= e($r['link']) ?>" target="_blank" rel="noopener">Acessar <i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?></td></tr><?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="3" class="text-muted">Nenhum órgão cadastrado.</td></tr><?php endif; ?></tbody></table></div>
</div></section>
<?php site_footer();
