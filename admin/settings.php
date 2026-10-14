<?php
require __DIR__ . '/_layout.php'; require_login();
$S = [
 'Identidade' => [['site_nome', 'Nome curto (OBAM)'], ['site_subtitulo', 'Nome completo'], ['site_org', 'Instituição'], ['logo', 'Logo', 'image'], ['favicon', 'Favicon', 'image']],
 'Contato e redes' => [['email', 'E-mail'], ['telefone', 'Telefone'], ['endereco', 'Endereço'], ['instagram', 'Link do Instagram'], ['instagram_nome', 'Nome do Instagram (@...)'], ['youtube', 'Link do YouTube'], ['facebook', 'Link do Facebook']],
 'Números da página inicial' => [['stat1_num', 'Número 1'], ['stat1_label', 'Rótulo 1'], ['stat2_num', 'Número 2'], ['stat2_label', 'Rótulo 2'], ['stat3_num', 'Número 3'], ['stat3_label', 'Rótulo 3'], ['stat4_num', 'Número 4'], ['stat4_label', 'Rótulo 4']],
 'Textos' => [['sobre_lead', 'Frase de abertura (Quem Somos)', 'textarea'], ['sobre_imagem', 'Imagem de Quem Somos', 'image'], ['newsletter_titulo', 'Título da newsletter'], ['newsletter_texto', 'Texto da newsletter'], ['cadastro_intro', 'Texto de abertura do Cadastro do Apicultor', 'textarea'], ['lgpd_texto', 'Texto de consentimento LGPD (cadastro)', 'textarea']],
 'Mapas e links' => [['mapa_apiarios_url', 'URL do mapa de apiários (Google Maps > Compartilhar > Incorporar)', 'textarea'], ['mapa_contato_url', 'URL do mapa de contato', 'textarea'], ['denuncia_externa_url', 'Link do formulário externo de denúncia (FBCA)']],
 'Controle mensal do cadastro' => [['ano_inicio_controle', 'Ano inicial da tabela'], ['ano_fim_controle', 'Ano final da tabela']],
];
if (is_post()) {
    require_admin(); csrf_check();
    try {
        foreach ($S as $g) foreach ($g as $f) {
            $k = $f[0]; $type = $f[2] ?? 'text';
            if ($type === 'image') { $v = upload_image("f_$k") ?: post($k); } else $v = post($k);
            if (in_array($k, ['ano_inicio_controle', 'ano_fim_controle'], true)) $v = (string)max(2000, min(2100, (int)$v ?: ($k === 'ano_inicio_controle' ? 2024 : 2034)));
            qx('INSERT INTO settings (k,v) VALUES (?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)', [$k, $v]);
        }
        flash('Configurações salvas.');
    } catch (Throwable $e) { flash($e->getMessage(), 'danger'); }
    redirect('settings.php');
}
admin_header('Configurações do site', 'settings'); ro_notice(); $ro = !is_admin(); ?>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><fieldset <?= $ro ? 'disabled' : '' ?>>
<?php foreach ($S as $g => $fs): ?><div class="adm-card"><h5 class="mb-3"><?= e($g) ?></h5><div class="row g-3">
<?php foreach ($fs as $f): $k = $f[0]; $type = $f[2] ?? 'text'; $v = cfg($k); ?>
 <div class="col-md-<?= $type === 'text' ? 6 : 12 ?>"><label class="form-label fw-semibold"><?= e($f[1]) ?></label>
 <?php if ($type === 'textarea'): ?><textarea class="form-control" name="<?= e($k) ?>" rows="3"><?= e($v) ?></textarea>
 <?php elseif ($type === 'image'): ?><?php if ($v): ?><div class="mb-2"><img src="<?= e(img_url($v)) ?>" style="max-height:60px"></div><?php endif; ?><input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>"><input class="form-control" type="file" name="f_<?= e($k) ?>" accept="image/*">
 <?php else: ?><input class="form-control" name="<?= e($k) ?>" value="<?= e($v) ?>"><?php endif; ?></div>
<?php endforeach; ?></div></div><?php endforeach; ?>
</fieldset><?php if (!$ro): ?><button class="btn btn-y btn-lg">Salvar configurações</button><?php endif; ?></form>
<?php admin_footer();
