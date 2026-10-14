<?php
require __DIR__ . '/inc/layout.php';
if (is_post()) {
    csrf_check(); $n = post('nome'); $m = filter_var(post('email'), FILTER_VALIDATE_EMAIL); $msg = post('mensagem');
    if (post('website') !== '' || throttle('ct', 5, 3600)) flash('Não foi possível enviar agora.', 'warning');
    elseif ($n === '' || !$m || $msg === '') flash('Preencha nome, e-mail válido e mensagem.', 'danger');
    else {
        qx('INSERT INTO mensagens (nome,email,assunto,mensagem) VALUES (?,?,?,?)', [mb_substr($n, 0, 120), $m, mb_substr(post('assunto'), 0, 200), mb_substr($msg, 0, 5000)]);
        flash('Mensagem enviada! Entraremos em contato em breve.'); redirect(u('contato.php'));
    }
}
site_header('Contatos'); page_title('Contatos');
?>
<section class="contact section"><div class="container">
  <div class="row g-4 mb-5">
    <div class="col-md-4"><div class="contact-info-card"><i class="bi bi-geo-alt-fill"></i><div><h4>Endereço</h4><p><?= e(cfg('endereco')) ?></p></div></div></div>
    <div class="col-md-4"><div class="contact-info-card"><i class="bi bi-envelope-fill"></i><div><h4>E-mail</h4><p><?= e(cfg('email')) ?></p></div></div></div>
    <div class="col-md-4"><div class="contact-info-card"><i class="bi bi-instagram"></i><div><h4>Instagram</h4><p><?= e(cfg('instagram_nome')) ?></p></div></div></div>
  </div>
  <div class="row g-4">
    <div class="col-lg-7"><div class="form-card"><h3 style="font-size:1.5rem;font-weight:800">Envie uma mensagem</h3>
      <p class="text-muted mb-4">Preencha o formulário e entraremos em contato em breve.</p>
      <form method="post"><?= csrf_field() ?><input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
        <div class="row g-3">
          <div class="col-sm-6"><label>Nome completo *</label><input class="form-control" name="nome" required maxlength="120"></div>
          <div class="col-sm-6"><label>E-mail *</label><input class="form-control" type="email" name="email" required maxlength="160"></div>
          <div class="col-12"><label>Assunto</label><input class="form-control" name="assunto" maxlength="200"></div>
          <div class="col-12"><label>Mensagem *</label><textarea class="form-control" name="mensagem" rows="6" required maxlength="5000"></textarea></div>
          <div class="col-12"><button class="btn-submit"><i class="bi bi-send me-2"></i>Enviar Mensagem</button></div>
        </div></form></div></div>
    <div class="col-lg-5"><div class="map-wrapper"><iframe src="<?= e(cfg('mapa_contato_url')) ?>" width="100%" height="100%" style="border:0;min-height:380px" allowfullscreen loading="lazy"></iframe></div></div>
  </div></div></section>
<?php site_footer();
