<?php
require __DIR__ . '/inc/layout.php';
$tipos = ['Mortandade de abelhas', 'Uso inadequado de agrotóxico', 'Destruição de ninho / colmeia', 'Queimada próxima a apiário', 'Outro'];
if (is_post()) {
    csrf_check();
    $d = post('descricao'); $data = post('data_ocorrencia'); $tipo = post('tipo');
    if (post('website') !== '' || throttle('den', 5, 3600)) flash('Não foi possível enviar agora. Tente mais tarde.', 'warning');
    elseif ($d === '' || !in_array($tipo, $tipos, true)) flash('Informe o tipo e a descrição da ocorrência.', 'danger');
    else {
        $dt = $data && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) ? $data : null;
        qx('INSERT INTO denuncias (nome,municipio,tipo,data_ocorrencia,descricao,contato) VALUES (?,?,?,?,?,?)', [mb_substr(post('nome'), 0, 120), mb_substr(post('municipio'), 0, 120), $tipo, $dt, mb_substr($d, 0, 5000), mb_substr(post('contato'), 0, 160)]);
        flash('Denúncia registrada. Obrigado por ajudar a proteger as abelhas!'); redirect(u('denuncias.php'));
    }
}
site_header('Denúncias'); page_title('Denúncias');
?>
<section class="section"><div class="container"><div class="row g-5 align-items-start">
  <div class="col-lg-7" data-aos="fade-right">
    <span class="section-label"><i class="bi bi-megaphone"></i> Participe</span><h2 class="section-title mb-3">Faça uma Denúncia</h2>
    <p class="text-muted mb-4">Registre casos de mortandade de abelhas, uso indevido de agrotóxicos ou destruição de ninhos.</p>
    <div class="form-card"><form method="post"><?= csrf_field() ?>
      <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
      <div class="row g-3">
        <div class="col-sm-6"><label>Nome (opcional)</label><input class="form-control" name="nome" maxlength="120"></div>
        <div class="col-sm-6"><label>Município</label><input class="form-control" name="municipio" maxlength="120"></div>
        <div class="col-md-7"><label>Tipo de ocorrência *</label><select class="form-select" name="tipo" required><option value="">Selecione...</option><?php foreach ($tipos as $t): ?><option><?= e($t) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-5"><label>Data aproximada</label><input class="form-control" type="date" name="data_ocorrencia" max="<?= date('Y-m-d') ?>"></div>
        <div class="col-12"><label>Descrição da ocorrência *</label><textarea class="form-control" name="descricao" rows="5" required maxlength="5000"></textarea></div>
        <div class="col-12"><label>Contato para retorno (opcional)</label><input class="form-control" name="contato" maxlength="160" placeholder="E-mail ou telefone"></div>
        <div class="col-12"><button class="btn-submit"><i class="bi bi-send me-2"></i>Enviar Denúncia</button></div>
      </div></form></div>
  </div>
  <div class="col-lg-5" data-aos="fade-left">
    <div class="sidebar-widget mb-4"><h4>Por que denunciar?</h4><ul style="font-size:.87rem;padding-left:16px;line-height:2;"><li>Mapear regiões de risco para as abelhas</li><li>Acionar autoridades ambientais</li><li>Gerar dados para pesquisas científicas</li><li>Pressionar por políticas públicas</li></ul></div>
    <?php if (cfg('denuncia_externa_url')): ?><div class="sidebar-widget mb-4"><h4>Fórum Baiano de Combate aos Agrotóxicos</h4><p style="font-size:.87rem;color:var(--gray-500)">Formulário de denúncia de mortandade de abelhas do FBCA.</p><a href="<?= e(cfg('denuncia_externa_url')) ?>" target="_blank" rel="noopener" class="btn-outline-y d-block text-center">Abrir formulário do FBCA</a></div><?php endif; ?>
    <div class="sidebar-widget"><h4>Contato Direto</h4><p style="font-size:.87rem;color:var(--gray-500)">Situações urgentes:</p><a href="<?= u('contato.php') ?>" class="btn-primary-y d-block text-center"><i class="bi bi-envelope"></i> Falar com a Equipe</a></div>
  </div></div></div></section>
<?php site_footer();
