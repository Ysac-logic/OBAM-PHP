<?php
require __DIR__ . '/inc/layout.php';
site_header('Denúncias'); page_title('Denúncias');
?>
<section class="section"><div class="container"><div class="row justify-content-center"><div class="col-lg-6" data-aos="fade-up">
    <div class="sidebar-widget mb-4"><h4>Por que denunciar?</h4><ul style="font-size:.87rem;padding-left:16px;line-height:2;"><li>Mapear regiões de risco para as abelhas</li><li>Acionar autoridades ambientais</li><li>Gerar dados para pesquisas científicas</li><li>Pressionar por políticas públicas</li></ul></div>
    <?php if (cfg('denuncia_externa_url')): ?><div class="sidebar-widget mb-4"><h4>Fórum Baiano de Combate aos Agrotóxicos</h4><p style="font-size:.87rem;color:var(--gray-500)">Formulário de denúncia de mortandade de abelhas do FBCA.</p><a href="<?= e(cfg('denuncia_externa_url')) ?>" target="_blank" rel="noopener" class="btn-outline-y d-block text-center">Abrir formulário do FBCA</a></div><?php endif; ?>
  </div></div></div></section>
<?php site_footer();
