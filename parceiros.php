<?php
require __DIR__ . '/inc/layout.php';
$ps = qa('SELECT * FROM parceiros WHERE ativo=1 ORDER BY ordem,id');
site_header('Parceiros e Apoiadores'); page_title('Parceiros e Apoiadores');
?>
<section class="section"><div class="container">
  <div class="text-center mb-5" data-aos="fade-up"><span class="section-label"><i class="bi bi-people-fill"></i> Rede de Apoio</span><h2 class="section-title mt-2">Parceiros do OBAM</h2>
    <p class="text-muted mt-2">Instituições e organizações que acreditam na missão do Observatório.</p></div>
  <div class="row g-4"><?php foreach ($ps as $i => $p): ?>
    <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 60 ?>"><div class="partner-card">
      <div class="partner-logo"><i class="bi <?= e($p['icone'] ?: 'bi-people-fill') ?>"></i></div>
      <h4><?= e($p['sigla'] ?: $p['nome']) ?></h4>
      <?php if ($p['sigla']): ?><p style="font-size:.78rem;font-weight:700;color:var(--yellow-dark);margin-bottom:8px;"><?= e($p['nome']) ?></p><?php endif; ?>
      <p><?= e($p['descricao']) ?></p>
      <?php if ($p['funcao']): ?><span class="partner-tag"><?= e($p['funcao']) ?></span><?php endif; ?>
      <?php if ($p['url']): ?><div class="mt-2"><a href="<?= e($p['url']) ?>" target="_blank" rel="noopener">Visitar site <i class="bi bi-box-arrow-up-right"></i></a></div><?php endif; ?>
    </div></div><?php endforeach; ?></div>
  <div class="text-center mt-5"><h3>Seja um parceiro</h3><p class="text-muted">Sua organização pode contribuir com pesquisa, financiamento, divulgação ou ação no campo.</p><a href="<?= u('contato.php') ?>" class="btn-primary-y">Entre em Contato</a></div>
</div></section>
<?php site_footer();
