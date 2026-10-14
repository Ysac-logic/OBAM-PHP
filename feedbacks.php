<?php
require __DIR__ . '/inc/layout.php';
$tipos = ['Elogio', 'Sugestão', 'Crítica']; $perfis = ['Apicultor(a)', 'Pesquisador(a)', 'Estudante', 'Cidadão(ã)', 'Outro'];
if (is_post()) {
    csrf_check(); $nome = post('nome'); $msg = post('mensagem'); $tipo = post('tipo'); $nota = max(1, min(5, (int)post('nota', '5')));
    if (post('website') !== '' || throttle('fb', 5, 3600)) flash('Não foi possível enviar agora.', 'warning');
    elseif ($nome === '' || $msg === '' || !in_array($tipo, $tipos, true)) flash('Preencha nome, tipo e mensagem.', 'danger');
    else {
        qx('INSERT INTO feedbacks (nome,perfil,tipo,nota,mensagem) VALUES (?,?,?,?,?)', [mb_substr($nome, 0, 120), in_array(post('perfil'), $perfis, true) ? post('perfil') : '', $tipo, $nota, mb_substr($msg, 0, 3000)]);
        flash('Obrigado! Sua mensagem foi enviada e aparecerá no site após a aprovação da equipe.'); redirect(u('feedbacks.php'));
    }
}
$fb = qa("SELECT * FROM feedbacks WHERE status='aprovado' ORDER BY criado_em DESC, id DESC LIMIT 30");
site_header('Elogios, Sugestões e Críticas'); page_title('Elogios, Sugestões e Críticas');
?>
<section class="section"><div class="container">
  <div class="text-center mb-5"><span class="section-label"><i class="bi bi-star-fill"></i> Depoimentos</span><h2 class="section-title mt-2">O que dizem sobre o OBAM</h2></div>
  <div class="row g-4"><?php foreach ($fb as $i => $f): ?>
    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 50 ?>"><div class="feedback-card">
      <div class="stars"><?= str_repeat('★', (int)$f['nota']) . str_repeat('☆', 5 - (int)$f['nota']) ?></div><div class="quote-icon">"</div>
      <p><?= nl2br(e($f['mensagem'])) ?></p>
      <div class="author"><div class="author-avatar"><?= e(mb_strtoupper(mb_substr($f['nome'], 0, 1))) ?></div><div><div class="author-name"><?= e($f['nome']) ?></div><div class="author-role"><?= e(trim($f['perfil'] . ' · ' . $f['tipo'], ' ·')) ?></div></div></div>
    </div></div><?php endforeach; ?></div>
  <div class="row justify-content-center mt-5"><div class="col-lg-7"><div class="form-card">
    <h3 style="font-weight:800">Ajude-nos a melhorar</h3><p class="text-muted">Envie um elogio, sugestão ou crítica. As mensagens são publicadas após moderação.</p>
    <form method="post"><?= csrf_field() ?><input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
      <div class="row g-3">
        <div class="col-sm-6"><label>Nome *</label><input class="form-control" name="nome" required maxlength="120"></div>
        <div class="col-sm-6"><label>Perfil</label><select class="form-select" name="perfil"><option value="">Selecione...</option><?php foreach ($perfis as $p): ?><option><?= e($p) ?></option><?php endforeach; ?></select></div>
        <div class="col-sm-6"><label>Tipo *</label><select class="form-select" name="tipo" required><?php foreach ($tipos as $t): ?><option><?= e($t) ?></option><?php endforeach; ?></select></div>
        <div class="col-sm-6"><label>Avaliação</label><select class="form-select" name="nota"><?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= str_repeat('★', $i) ?></option><?php endfor; ?></select></div>
        <div class="col-12"><label>Mensagem *</label><textarea class="form-control" name="mensagem" rows="4" required maxlength="3000"></textarea></div>
        <div class="col-12"><button class="btn-submit"><i class="bi bi-send me-2"></i>Enviar</button></div>
      </div></form></div></div></div>
</div></section>
<?php site_footer();
