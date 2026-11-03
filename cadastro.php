<?php
require __DIR__ . '/inc/layout.php';
$old = []; $erros = [];
$campos = ['nome', 'cpf', 'nascimento', 'telefone', 'email', 'data_filiacao', 'endereco', 'numero', 'cep', 'localidade', 'municipio', 'uf', 'nucleo', 'colmeias', 'apiarios', 'especies', 'local_apiario', 'producao_mel', 'observacoes'];
$UFS = ['BA', 'SE', 'PE', 'AL', 'PI', 'CE', 'MA', 'PB', 'RN'];
$ATIV = ['Apicultura', 'Meliponicultura', 'Ambas'];
$PROD = ['Própolis', 'Cera', 'Pólen', 'Geleia real', 'Meliprodutos', 'Outros'];
if (is_post()) {
    csrf_check(); foreach ($campos as $c) $old[$c] = post($c);
    $old['uf'] = $old['uf'] ?: 'BA';
    $old['atividade'] = array_values(array_intersect($ATIV, (array)($_POST['atividade'] ?? [])));
    $old['produtos'] = array_values(array_intersect($PROD, (array)($_POST['produtos'] ?? [])));
    $cpf = only_digits($old['cpf']); $tel = $old['telefone']; $cep = only_digits($old['cep']);
    $date = fn($v) => preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) && strtotime($v) <= time();
    if (post('website') !== '') $erros[] = 'Envio bloqueado.';
    if (throttle('cad', 5, 3600)) $erros[] = 'Muitas tentativas. Tente novamente mais tarde.';
    if (mb_strlen($old['nome']) < 5) $erros[] = 'Informe o nome completo.';
    if (!cpf_valid($cpf)) $erros[] = 'CPF inválido.';
    if (strlen(only_digits($tel)) < 10) $erros[] = 'Informe um telefone com DDD.';
    if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
    if ($old['endereco'] === '') $erros[] = 'Informe o endereço.';
    if ($old['localidade'] === '') $erros[] = 'Informe a localidade.';
    if ($old['municipio'] === '') $erros[] = 'Informe o município.';
    if (!in_array($old['uf'], $UFS, true)) $erros[] = 'UF inválida.';
    if ($cep !== '' && strlen($cep) !== 8) $erros[] = 'CEP inválido.';
    if (!$old['atividade']) $erros[] = 'Selecione pelo menos um tipo de atividade.';
    $dt = null; if ($old['data_filiacao'] !== '') { if ($date($old['data_filiacao'])) $dt = $old['data_filiacao']; else $erros[] = 'Data de filiação inválida.'; }
    $nasc = null; if ($old['nascimento'] !== '') { if ($date($old['nascimento'])) $nasc = $old['nascimento']; else $erros[] = 'Data de nascimento inválida.'; }
    if (empty($_POST['consentimento'])) $erros[] = 'É necessário aceitar a declaração e o tratamento de dados (LGPD).';
    if (!$erros && qv('SELECT COUNT(*) FROM apicultores WHERE cpf=?', [$cpf])) $erros[] = 'Já existe um cadastro com este CPF.';
    $fotos = [null, null];
    if (!$erros) {
        try { $fotos = [upload_file('foto', 'cadastros', ['jpg', 'jpeg', 'png', 'webp']), upload_file('apiarioFoto', 'cadastros', ['jpg', 'jpeg', 'png', 'webp'])]; }
        catch (RuntimeException $ex) { $erros[] = $ex->getMessage(); }
    }
    if (!$erros) {
        $int = fn($v) => $v === '' ? null : max(0, (int)$v);
        $prod = $old['producao_mel'] === '' ? null : max(0, (float)str_replace(',', '.', $old['producao_mel']));
        qx('INSERT INTO apicultores (nome,cpf,nascimento,telefone,email,data_filiacao,endereco,numero,cep,localidade,municipio,uf,nucleo,atividade,colmeias,apiarios,especies,local_apiario,producao_mel,produtos,observacoes,foto_apicultor,foto_apiario,assinatura,consentimento,consentimento_em,ip,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,NOW(),?,\'pendente\')',
            [mb_substr($old['nome'], 0, 160), $cpf, $nasc, mb_substr($tel, 0, 30), mb_substr($old['email'], 0, 160) ?: null, $dt, mb_substr($old['endereco'], 0, 255), mb_substr($old['numero'], 0, 20), $cep ?: null, mb_substr($old['localidade'], 0, 160), mb_substr($old['municipio'], 0, 120), $old['uf'], mb_substr($old['nucleo'], 0, 120), implode(', ', $old['atividade']), $int($old['colmeias']), $int($old['apiarios']), mb_substr($old['especies'], 0, 255), mb_substr($old['local_apiario'], 0, 255), $prod, implode(', ', $old['produtos']), mb_substr($old['observacoes'], 0, 2000), $fotos[0], $fotos[1], mb_substr($old['nome'], 0, 160), client_ip()]);
        flash('Cadastro enviado com sucesso! Ele será analisado pela equipe do OBAM.'); redirect(u('cadastro.php'));
    }
}
site_header('Cadastro de Apicultor(a) / Meliponicultor(a)'); page_title('Cadastro de Apicultor(a) / Meliponicultor(a)');
$v = fn($k) => e($old[$k] ?? '');
$ck = fn($k, $val) => in_array($val, $old[$k] ?? [], true) ? 'checked' : '';
?>
<section class="section"><div class="container"><div class="row g-5 align-items-start">
  <div class="col-lg-8" data-aos="fade-right">
    <span class="section-label"><i class="bi bi-person-vcard"></i> Ficha de Cadastro</span><h2 class="section-title mb-3">Ficha de Cadastro de Apicultor(a) / Meliponicultor(a)</h2>
    <p class="text-muted mb-4"><?= e(cfg('cadastro_intro')) ?> Campos marcados com * são obrigatórios.</p>
    <?php if ($erros): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($erros as $x): ?><li><?= e($x) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <div class="form-card"><form method="post" enctype="multipart/form-data" novalidate><?= csrf_field() ?>
      <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
      <h5 class="mb-3"><span class="badge bg-warning text-dark me-2">1</span>Dados pessoais</h5>
      <div class="row g-3 mb-4">
        <div class="col-md-6"><label for="nome">Nome completo *</label><input class="form-control" id="nome" name="nome" required maxlength="160" autocomplete="name" value="<?= $v('nome') ?>"></div>
        <div class="col-md-3"><label for="cpf">CPF *</label><input class="form-control" name="cpf" id="cpf" required inputmode="numeric" maxlength="14" placeholder="000.000.000-00" value="<?= $v('cpf') ?>"></div>
        <div class="col-md-3"><label for="nascimento">Data de nascimento</label><input class="form-control" type="date" id="nascimento" name="nascimento" max="<?= date('Y-m-d') ?>" value="<?= $v('nascimento') ?>"></div>
        <div class="col-md-4"><label for="tel">Telefone / WhatsApp *</label><input class="form-control" name="telefone" id="tel" required inputmode="tel" maxlength="16" placeholder="(75) 90000-0000" value="<?= $v('telefone') ?>"></div>
        <div class="col-md-4"><label for="email">E-mail</label><input class="form-control" type="email" id="email" name="email" maxlength="160" autocomplete="email" value="<?= $v('email') ?>"></div>
        <div class="col-md-4"><label for="data_filiacao">Data de filiação</label><input class="form-control" type="date" id="data_filiacao" name="data_filiacao" max="<?= date('Y-m-d') ?>" value="<?= $v('data_filiacao') ?>"></div>
      </div>
      <h5 class="mb-3"><span class="badge bg-warning text-dark me-2">2</span>Endereço e localização</h5>
      <div class="row g-3 mb-4">
        <div class="col-md-6"><label for="endereco">Endereço *</label><input class="form-control" id="endereco" name="endereco" required maxlength="255" value="<?= $v('endereco') ?>"></div>
        <div class="col-md-3"><label for="numero">Número</label><input class="form-control" id="numero" name="numero" maxlength="20" value="<?= $v('numero') ?>"></div>
        <div class="col-md-3"><label for="cep">CEP</label><input class="form-control" id="cep" name="cep" inputmode="numeric" maxlength="9" placeholder="00000-000" value="<?= $v('cep') ?>"></div>
        <div class="col-md-4"><label for="localidade">Localidade / Comunidade *</label><input class="form-control" id="localidade" name="localidade" required maxlength="160" value="<?= $v('localidade') ?>"></div>
        <div class="col-md-4"><label for="municipio">Município *</label><input class="form-control" id="municipio" name="municipio" required maxlength="120" value="<?= $v('municipio') ?>"></div>
        <div class="col-md-2"><label for="uf">UF *</label><select class="form-select" id="uf" name="uf" required><?php foreach ($UFS as $s): ?><option <?= ($old['uf'] ?? 'BA') === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label for="nucleo">Núcleo</label><input class="form-control" id="nucleo" name="nucleo" maxlength="120" value="<?= $v('nucleo') ?>"></div>
      </div>
      <h5 class="mb-3"><span class="badge bg-warning text-dark me-2">3</span>Atividade apícola</h5>
      <div class="row g-3 mb-4">
        <div class="col-12"><label>Tipo de atividade *</label><div class="d-flex flex-wrap gap-3"><?php foreach ($ATIV as $a): ?><div class="form-check"><input class="form-check-input" type="checkbox" name="atividade[]" id="at-<?= e($a) ?>" value="<?= e($a) ?>" <?= $ck('atividade', $a) ?>><label class="form-check-label" for="at-<?= e($a) ?>" style="font-weight:400"><?= e($a) ?></label></div><?php endforeach; ?></div></div>
        <div class="col-md-6"><label for="colmeias">Número de colmeias</label><input class="form-control" type="number" min="0" id="colmeias" name="colmeias" value="<?= $v('colmeias') ?>"></div>
        <div class="col-md-6"><label for="apiarios">Nº de apiários / meliponários</label><input class="form-control" type="number" min="0" id="apiarios" name="apiarios" value="<?= $v('apiarios') ?>"></div>
        <div class="col-md-6"><label for="especies">Espécies / abelhas criadas</label><input class="form-control" id="especies" name="especies" maxlength="255" placeholder="Ex.: Apis mellifera, Jataí, Uruçu..." value="<?= $v('especies') ?>"></div>
        <div class="col-md-6"><label for="local_apiario">Localização do apiário / meliponário</label><input class="form-control" id="local_apiario" name="local_apiario" maxlength="255" value="<?= $v('local_apiario') ?>"></div>
        <div class="col-md-4"><label for="producao_mel">Produção estimada de mel (kg/ano)</label><input class="form-control" type="number" min="0" step="0.1" id="producao_mel" name="producao_mel" value="<?= $v('producao_mel') ?>"></div>
        <div class="col-md-8"><label>Outros produtos</label><div class="d-flex flex-wrap gap-3"><?php foreach ($PROD as $i => $p): ?><div class="form-check"><input class="form-check-input" type="checkbox" name="produtos[]" id="pr-<?= $i ?>" value="<?= e($p) ?>" <?= $ck('produtos', $p) ?>><label class="form-check-label" for="pr-<?= $i ?>" style="font-weight:400"><?= e($p) ?></label></div><?php endforeach; ?></div></div>
        <div class="col-12"><label for="observacoes">Observações</label><textarea class="form-control" id="observacoes" name="observacoes" rows="3" maxlength="2000" placeholder="Informações adicionais sobre a atividade..."><?= $v('observacoes') ?></textarea></div>
      </div>
      <h5 class="mb-3"><span class="badge bg-warning text-dark me-2">4</span>Documentos e imagens <small class="text-muted fw-normal">(opcional)</small></h5>
      <div class="row g-3 mb-4">
        <div class="col-md-6"><label for="foto">Foto do(a) apicultor(a) / meliponicultor(a)</label><input class="form-control" type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"></div>
        <div class="col-md-6"><label for="apiarioFoto">Foto do apiário / meliponário</label><input class="form-control" type="file" id="apiarioFoto" name="apiarioFoto" accept="image/jpeg,image/png,image/webp"></div>
      </div>
      <h5 class="mb-3"><span class="badge bg-warning text-dark me-2">5</span>Declaração</h5>
      <div class="row g-3">
        <div class="col-12"><div class="lgpd-box"><div class="form-check"><input class="form-check-input" type="checkbox" name="consentimento" id="consent" value="1" required><label class="form-check-label" for="consent" style="font-weight:400">Declaro que as informações fornecidas nesta ficha cadastral são verdadeiras. <?= e(cfg('lgpd_texto')) ?> <a href="<?= u('post.php?slug=politica-privacidade') ?>" target="_blank">Política de Privacidade</a>.</label></div></div></div>
        <div class="col-12"><button class="btn-submit"><i class="bi bi-send me-2"></i>Enviar Cadastro</button></div>
      </div></form></div>
  </div>
  <div class="col-lg-4" data-aos="fade-left">
    <div class="sidebar-widget mb-4"><h4>Como funciona</h4><ul style="font-size:.87rem;padding-left:16px;line-height:1.9"><li>Preencha a ficha com seus dados.</li><li>A equipe do OBAM analisa e ativa o cadastro.</li><li>O controle mensal de 2024 a 2034 é registrado pela coordenação.</li></ul></div>
    <div class="sidebar-widget"><h4>Já é registrado na ADAB?</h4><p style="font-size:.87rem;color:var(--gray-500)">Veja como regularizar seu apiário.</p><a href="<?= u('post.php?slug=cadastro-adab') ?>" class="btn-outline-y d-block text-center">Cadastro ADAB</a></div>
  </div></div></div></section>
<script>
(function(){var c=document.getElementById('cpf'),t=document.getElementById('tel'),z=document.getElementById('cep');
z&&z.addEventListener('input',function(){var v=z.value.replace(/\D/g,'').slice(0,8);z.value=v.replace(/(\d{5})(\d)/,'$1-$2');});
c&&c.addEventListener('input',function(){var v=c.value.replace(/\D/g,'').slice(0,11);v=v.replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2');c.value=v;});
t&&t.addEventListener('input',function(){var v=t.value.replace(/\D/g,'').slice(0,11);v=v.length>10?v.replace(/(\d{2})(\d{5})(\d{4})/,'($1) $2-$3'):v.replace(/(\d{2})(\d{4})(\d{0,4})/,'($1) $2-$3');t.value=v.replace(/[-\s]$/,'');});})();
</script>
<?php site_footer();
