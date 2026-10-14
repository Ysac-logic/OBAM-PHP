<?php
require __DIR__ . '/inc/layout.php';
$old = []; $erros = [];
$campos = ['nome', 'cpf', 'endereco', 'localidade', 'telefone', 'nucleo', 'data_filiacao', 'assinatura'];
if (is_post()) {
    csrf_check(); foreach ($campos as $c) $old[$c] = post($c);
    $cpf = only_digits($old['cpf']); $tel = $old['telefone'];
    if (post('website') !== '') $erros[] = 'Envio bloqueado.';
    if (throttle('cad', 5, 3600)) $erros[] = 'Muitas tentativas. Tente novamente mais tarde.';
    if (mb_strlen($old['nome']) < 5) $erros[] = 'Informe o nome completo.';
    if (!cpf_valid($cpf)) $erros[] = 'CPF inválido.';
    if ($old['endereco'] === '') $erros[] = 'Informe o endereço.';
    if ($old['localidade'] === '') $erros[] = 'Informe a localidade.';
    if (strlen(only_digits($tel)) < 10) $erros[] = 'Informe um telefone com DDD.';
    $dt = null; if ($old['data_filiacao'] !== '') { if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $old['data_filiacao']) && strtotime($old['data_filiacao']) <= time()) $dt = $old['data_filiacao']; else $erros[] = 'Data de filiação inválida.'; }
    if (mb_strlen($old['assinatura']) < 5) $erros[] = 'Digite seu nome completo como assinatura.';
    if (empty($_POST['consentimento'])) $erros[] = 'É necessário aceitar o tratamento de dados (LGPD).';
    if (!$erros && qv('SELECT COUNT(*) FROM apicultores WHERE cpf=?', [$cpf])) $erros[] = 'Já existe um cadastro com este CPF.';
    if (!$erros) {
        qx('INSERT INTO apicultores (nome,cpf,endereco,localidade,telefone,nucleo,data_filiacao,assinatura,consentimento,consentimento_em,ip,status) VALUES (?,?,?,?,?,?,?,?,1,NOW(),?,\'pendente\')',
            [mb_substr($old['nome'], 0, 160), $cpf, mb_substr($old['endereco'], 0, 255), mb_substr($old['localidade'], 0, 160), mb_substr($tel, 0, 30), mb_substr($old['nucleo'], 0, 120), $dt, mb_substr($old['assinatura'], 0, 160), client_ip()]);
        flash('Cadastro enviado com sucesso! Ele será analisado pela equipe do OBAM.'); redirect(u('cadastro.php'));
    }
}
site_header('Cadastro do Apicultor'); page_title('Cadastro do Apicultor');
?>
<section class="section"><div class="container"><div class="row g-5 align-items-start">
  <div class="col-lg-8" data-aos="fade-right">
    <span class="section-label"><i class="bi bi-person-vcard"></i> Ficha de Cadastro</span><h2 class="section-title mb-3">Ficha de Cadastro do Apicultor</h2>
    <p class="text-muted mb-4"><?= e(cfg('cadastro_intro')) ?></p>
    <?php if ($erros): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($erros as $x): ?><li><?= e($x) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <div class="form-card"><form method="post" novalidate><?= csrf_field() ?>
      <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
      <div class="row g-3">
        <div class="col-12"><label>Nome *</label><input class="form-control" name="nome" required maxlength="160" value="<?= e($old['nome'] ?? '') ?>"></div>
        <div class="col-md-6"><label>CPF *</label><input class="form-control" name="cpf" id="cpf" required inputmode="numeric" maxlength="14" placeholder="000.000.000-00" value="<?= e($old['cpf'] ?? '') ?>"></div>
        <div class="col-md-6"><label>Tel. *</label><input class="form-control" name="telefone" id="tel" required inputmode="tel" maxlength="16" placeholder="(75) 90000-0000" value="<?= e($old['telefone'] ?? '') ?>"></div>
        <div class="col-12"><label>Endereço *</label><input class="form-control" name="endereco" required maxlength="255" value="<?= e($old['endereco'] ?? '') ?>"></div>
        <div class="col-md-6"><label>Localidade *</label><input class="form-control" name="localidade" required maxlength="160" value="<?= e($old['localidade'] ?? '') ?>"></div>
        <div class="col-md-6"><label>Núcleo</label><input class="form-control" name="nucleo" maxlength="120" value="<?= e($old['nucleo'] ?? '') ?>"></div>
        <div class="col-md-6"><label>Data de filiação</label><input class="form-control" type="date" name="data_filiacao" max="<?= date('Y-m-d') ?>" value="<?= e($old['data_filiacao'] ?? '') ?>"></div>
        <div class="col-md-6"><label>Assinatura (digite seu nome completo) *</label><input class="form-control" name="assinatura" required maxlength="160" value="<?= e($old['assinatura'] ?? '') ?>"></div>
        <div class="col-12"><div class="lgpd-box"><div class="form-check"><input class="form-check-input" type="checkbox" name="consentimento" id="consent" value="1" required><label class="form-check-label" for="consent" style="font-weight:400"><?= e(cfg('lgpd_texto')) ?> <a href="<?= u('post.php?slug=politica-privacidade') ?>" target="_blank">Política de Privacidade</a>.</label></div></div></div>
        <div class="col-12"><button class="btn-submit"><i class="bi bi-send me-2"></i>Enviar Cadastro</button></div>
      </div></form></div>
  </div>
  <div class="col-lg-4" data-aos="fade-left">
    <div class="sidebar-widget mb-4"><h4>Como funciona</h4><ul style="font-size:.87rem;padding-left:16px;line-height:1.9"><li>Preencha a ficha com seus dados.</li><li>A equipe do OBAM analisa e ativa o cadastro.</li><li>O controle mensal de 2024 a 2034 é registrado pela coordenação.</li></ul></div>
    <div class="sidebar-widget"><h4>Já é registrado na ADAB?</h4><p style="font-size:.87rem;color:var(--gray-500)">Veja como regularizar seu apiário.</p><a href="<?= u('post.php?slug=cadastro-adab') ?>" class="btn-outline-y d-block text-center">Cadastro ADAB</a></div>
  </div></div></div></section>
<script>
(function(){var c=document.getElementById('cpf'),t=document.getElementById('tel');
c&&c.addEventListener('input',function(){var v=c.value.replace(/\D/g,'').slice(0,11);v=v.replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2');c.value=v;});
t&&t.addEventListener('input',function(){var v=t.value.replace(/\D/g,'').slice(0,11);v=v.length>10?v.replace(/(\d{2})(\d{5})(\d{4})/,'($1) $2-$3'):v.replace(/(\d{2})(\d{4})(\d{0,4})/,'($1) $2-$3');t.value=v.replace(/[-\s]$/,'');});})();
</script>
<?php site_footer();
