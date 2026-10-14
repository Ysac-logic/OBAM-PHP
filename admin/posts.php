<?php
require __DIR__ . '/_layout.php';
require_login();
$tipo = ($_GET['tipo'] ?? 'post') === 'page' ? 'page' : 'post';
if (is_post()) { $tipo = ($_POST['tipo'] ?? 'post') === 'page' ? 'page' : 'post'; }
$protected = ['quem-somos', 'politica-privacidade', 'termos-de-uso', 'cookies'];
$self = 'posts.php' . ($tipo === 'page' ? '?tipo=page' : '');
if (is_post()) {
    require_admin(); csrf_check(); $act = $_POST['act'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    try {
        if ($act === 'delete' && $id) {
            $p = q1('SELECT slug FROM posts WHERE id=?', [$id]);
            if ($p && in_array($p['slug'], $protected, true)) throw new RuntimeException('Esta página é usada pelo site e não pode ser excluída (você pode editá-la).');
            qx('DELETE FROM posts WHERE id=?', [$id]); flash('Publicação excluída.');
        } elseif ($act === 'save') {
            $titulo = post('titulo'); if ($titulo === '') throw new RuntimeException('Informe o título.');
            $slug = slugify(post('slug') !== '' ? post('slug') : $titulo);
            if (qv('SELECT COUNT(*) FROM posts WHERE slug=? AND id<>?', [$slug, $id])) $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
            $cur = $id ? q1('SELECT imagem FROM posts WHERE id=?', [$id]) : null;
            $img = upload_image('f_imagem') ?: (post('imagem') !== '' ? post('imagem') : ($cur['imagem'] ?? null)); if (!empty($_POST['rm_imagem'])) $img = null;
            $d = post('publicado_em'); $d = preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? $d : date('Y-m-d');
            $vals = [$tipo, $titulo, $slug, post('categoria'), mb_substr(post('resumo'), 0, 400), $_POST['conteudo'] ?? '', $img, post('autor', 'Equipe OBAM'), $d,
                     post('status') === 'rascunho' ? 'rascunho' : 'publicado', isset($_POST['destaque']) ? 1 : 0, isset($_POST['lista_home']) ? 1 : 0, (int)post('ordem', '0')];
            if ($id) { qx('UPDATE posts SET tipo=?,titulo=?,slug=?,categoria=?,resumo=?,conteudo=?,imagem=?,autor=?,publicado_em=?,status=?,destaque=?,lista_home=?,ordem=? WHERE id=?', [...$vals, $id]); }
            else { qx('INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)', $vals); }
            flash('Salvo com sucesso.');
        }
    } catch (Throwable $e) { flash($e->getMessage(), 'danger'); if (($act ?? '') === 'save') redirect($self . ($id ? (str_contains($self, '?') ? '&' : '?') . "a=edit&id=$id" : (str_contains($self, '?') ? '&' : '?') . 'a=new')); }
    redirect($self);
}
$a = $_GET['a'] ?? 'list'; $ro = !is_admin();
admin_header($tipo === 'page' ? 'Páginas fixas' : 'Publicações', $tipo === 'page' ? 'pages' : 'posts'); ro_notice();
if ($a === 'new' || $a === 'edit') {
    $id = (int)($_GET['id'] ?? 0); $p = $id ? q1('SELECT * FROM posts WHERE id=?', [$id]) : ['titulo' => '', 'slug' => '', 'categoria' => '', 'resumo' => '', 'conteudo' => '', 'imagem' => '', 'autor' => 'Equipe OBAM', 'publicado_em' => date('Y-m-d'), 'status' => 'publicado', 'destaque' => 0, 'lista_home' => 0, 'ordem' => 0];
    if (!$p) { echo '<div class="alert alert-warning">Não encontrado.</div>'; admin_footer(); exit; }
    if ($ro && !$id) redirect($self);
    $cats = array_column(qa("SELECT DISTINCT categoria FROM posts WHERE categoria<>'' ORDER BY categoria"), 'categoria');
    ?>
<div class="adm-card"><form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="tipo" value="<?= $tipo ?>">
<fieldset <?= $ro ? 'disabled' : '' ?>><div class="row g-3">
 <div class="col-md-8"><label class="form-label fw-semibold">Título *</label><input class="form-control" name="titulo" required maxlength="255" value="<?= e($p['titulo']) ?>"></div>
 <div class="col-md-4"><label class="form-label fw-semibold">Endereço (slug)</label><input class="form-control" name="slug" maxlength="190" value="<?= e($p['slug']) ?>" placeholder="gerado automaticamente"></div>
 <div class="col-md-4"><label class="form-label fw-semibold">Categoria</label><input class="form-control" name="categoria" list="cats" maxlength="80" value="<?= e($p['categoria']) ?>"><datalist id="cats"><?php foreach ($cats as $c): ?><option value="<?= e($c) ?>"><?php endforeach; ?></datalist></div>
 <div class="col-md-3"><label class="form-label fw-semibold">Data</label><input class="form-control" type="date" name="publicado_em" value="<?= e($p['publicado_em']) ?>"></div>
 <div class="col-md-3"><label class="form-label fw-semibold">Autor</label><input class="form-control" name="autor" maxlength="120" value="<?= e($p['autor']) ?>"></div>
 <div class="col-md-2"><label class="form-label fw-semibold">Status</label><select class="form-select" name="status"><option value="publicado" <?= $p['status'] === 'publicado' ? 'selected' : '' ?>>Publicado</option><option value="rascunho" <?= $p['status'] === 'rascunho' ? 'selected' : '' ?>>Rascunho</option></select></div>
 <div class="col-12"><label class="form-label fw-semibold">Resumo (opcional)</label><textarea class="form-control" name="resumo" rows="2" maxlength="400"><?= e($p['resumo']) ?></textarea></div>
 <div class="col-12"><label class="form-label fw-semibold">Imagem de capa</label>
   <?php if ($p['imagem']): ?><div class="mb-2"><img src="<?= e(img_url($p['imagem'])) ?>" style="max-height:80px;border-radius:8px"> <label class="ms-2 small"><input type="checkbox" name="rm_imagem" value="1"> remover</label></div><?php endif; ?>
   <input type="hidden" name="imagem" id="imagem_h" value="<?= e($p['imagem']) ?>"><input class="form-control" type="file" name="f_imagem" accept="image/*">
   <input class="form-control form-control-sm mt-1" placeholder="ou caminho/URL (ex.: assets/image/foto1.jpeg)" onchange="document.getElementById('imagem_h').value=this.value"></div>
 <div class="col-12"><label class="form-label fw-semibold">Conteúdo (HTML)</label>
   <div class="btn-group btn-group-sm mb-1 flex-wrap" id="tb"><?php foreach ([['<strong>', '</strong>', 'B'], ['<em>', '</em>', 'I'], ['<h3>', '</h3>', 'H3'], ['<p>', '</p>', 'P'], ['<ul><li>', '</li></ul>', 'Lista'], ['<blockquote>', '</blockquote>', 'Citação'], ['<a href="https://">', '</a>', 'Link']] as $b): ?><button type="button" class="btn btn-outline-secondary" data-a="<?= e($b[0]) ?>" data-b="<?= e($b[1]) ?>"><?= $b[2] ?></button><?php endforeach; ?>
   <?php if (!$ro): ?><button type="button" class="btn btn-outline-secondary" id="imgbtn"><i class="bi bi-image"></i> Imagem</button><input type="file" id="imgfile" accept="image/*" hidden><?php endif; ?></div>
   <textarea class="form-control font-monospace" id="conteudo" name="conteudo" rows="18"><?= e($p['conteudo']) ?></textarea></div>
 <?php if ($tipo === 'post'): ?>
 <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="destaque" value="1" id="d1" <?= $p['destaque'] ? 'checked' : '' ?>><label for="d1" class="form-check-label">Destaque principal da home (card grande)</label></div></div>
 <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="lista_home" value="1" id="d2" <?= $p['lista_home'] ? 'checked' : '' ?>><label for="d2" class="form-check-label">Mostrar na lista de destaques da home</label></div></div>
 <div class="col-md-4"><label class="form-label">Ordem na lista de destaques (menor = primeiro)</label><input class="form-control" type="number" name="ordem" value="<?= (int)$p['ordem'] ?>"></div>
 <?php else: ?><input type="hidden" name="ordem" value="0"><?php endif; ?>
</div></fieldset>
<div class="mt-3 d-flex gap-2 flex-wrap"><?php if (!$ro): ?><button class="btn btn-y">Salvar</button><?php endif; ?><a class="btn btn-outline-secondary" href="<?= e($self) ?>">Voltar</a><?php if ($id): ?><a class="btn btn-outline-dark" target="_blank" href="<?= u('post.php?slug=' . urlencode($p['slug'])) ?>">Ver no site</a><?php endif; ?></div></form></div>
<script>
(function(){var t=document.getElementById('conteudo');document.getElementById('tb').addEventListener('click',function(e){var b=e.target.closest('button[data-a]');if(!b)return;var s=t.selectionStart,f=t.selectionEnd,v=t.value;t.value=v.slice(0,s)+b.dataset.a+v.slice(s,f)+b.dataset.b+v.slice(f);t.focus();});
var ib=document.getElementById('imgbtn'),ifl=document.getElementById('imgfile');if(ib){ib.onclick=function(){ifl.click()};ifl.onchange=function(){var fd=new FormData();fd.append('file',ifl.files[0]);fd.append('_csrf','<?= e(csrf_token()) ?>');fetch('upload_inline.php',{method:'POST',body:fd}).then(r=>r.json()).then(function(j){if(j.url){var s=t.selectionStart;t.value=t.value.slice(0,s)+'<img src="'+j.url+'" alt="" class="img-fluid">'+t.value.slice(s);}else alert(j.error||'Erro no upload');});};}})();
</script>
<?php } else {
    $s = trim($_GET['q'] ?? ''); $w = 'tipo=?'; $par = [$tipo]; if ($s !== '') { $w .= ' AND (titulo LIKE ? OR categoria LIKE ?)'; $par[] = "%$s%"; $par[] = "%$s%"; }
    $rows = qa("SELECT * FROM posts WHERE $w ORDER BY publicado_em DESC, id DESC", $par); ?>
<div class="adm-card"><div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
 <form class="d-flex gap-2" method="get"><?php if ($tipo === 'page'): ?><input type="hidden" name="tipo" value="page"><?php endif; ?><input class="form-control" name="q" value="<?= e($s) ?>" placeholder="Buscar..."><button class="btn btn-outline-dark"><i class="bi bi-search"></i></button></form>
 <?php if (!$ro): ?><a class="btn btn-y" href="<?= e($self . (str_contains($self, '?') ? '&' : '?') . 'a=new') ?>"><i class="bi bi-plus-lg"></i> <?= $tipo === 'page' ? 'Nova página' : 'Nova publicação' ?></a><?php endif; ?></div>
<div class="table-responsive"><table class="table table-hover"><thead><tr><th></th><th>Título</th><th>Categoria</th><th>Data</th><th>Status</th><th>Home</th><th>Views</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php if ($r['imagem']): ?><img class="thumb-s" src="<?= e(img_url($r['imagem'])) ?>"><?php endif; ?></td>
<td><strong><?= e($r['titulo']) ?></strong><div class="small text-muted"><?= e($r['slug']) ?></div></td><td><?= e($r['categoria']) ?></td><td><?= e(fdate($r['publicado_em'])) ?></td>
<td><span class="badge <?= $r['status'] === 'publicado' ? 'bg-success' : 'bg-secondary' ?>"><?= e($r['status']) ?></span></td>
<td><?= $r['destaque'] ? '<span class="badge bg-warning text-dark">Destaque</span> ' : '' ?><?= $r['lista_home'] ? '<span class="badge bg-info text-dark">Lista</span>' : '' ?></td><td><?= (int)$r['visualizacoes'] ?></td>
<td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-secondary" target="_blank" href="<?= u('post.php?slug=' . urlencode($r['slug'])) ?>" title="Ver"><i class="bi bi-box-arrow-up-right"></i></a>
<a class="btn btn-sm btn-outline-dark" href="<?= e($self . (str_contains($self, '?') ? '&' : '?') . 'a=edit&id=' . $r['id']) ?>"><i class="bi <?= $ro ? 'bi-eye' : 'bi-pencil' ?>"></i></a>
<?php if (!$ro) echo confirm_form('delete', ['id' => $r['id'], 'tipo' => $tipo], 'Excluir', 'Excluir esta publicação? Não dá para desfazer.'); ?></td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="8" class="text-muted">Nada encontrado.</td></tr><?php endif; ?></tbody></table></div></div>
<?php }
admin_footer();
