<?php
require_once __DIR__ . '/../inc/bootstrap.php';
function admin_header(string $title, string $active = ''): void {
    require_login();
    $u = user(); $adm = is_admin();
    $nav = [
        ['Geral'], ['index.php', 'bi-speedometer2', 'Painel', 'index'],
        ['Conteúdo do site'],
        ['posts.php', 'bi-journal-text', 'Publicações', 'posts'], ['posts.php?tipo=page', 'bi-file-earmark-text', 'Páginas fixas', 'pages'],
        ['home.php', 'bi-layout-text-window-reverse', 'Página inicial (ordem)', 'home'], ['slides.php', 'bi-images', 'Carrossel', 'slides'],
        ['menu.php', 'bi-list-nested', 'Menu do site', 'menu'], ['parceiros.php', 'bi-people', 'Parceiros', 'parceiros'],
        ['equipe.php', 'bi-person-badge', 'Equipe', 'equipe'], ['galeria.php', 'bi-card-image', 'Galeria', 'galeria'],
        ['videos.php', 'bi-play-btn', 'Vídeos', 'videos'], ['literatura.php', 'bi-book', 'Literatura', 'literatura'], ['orgaos.php', 'bi-building', 'Órgãos por esfera', 'orgaos'],
        ['settings.php', 'bi-gear', 'Configurações do site', 'settings'],
        ['Recebidos'],
        ['cadastros.php', 'bi-person-lines-fill', 'LISTA — Cadastros', 'cadastros'],
        ['caixa.php?t=denuncias', 'bi-megaphone', 'Denúncias', 'denuncias'], ['caixa.php?t=mensagens', 'bi-envelope', 'Mensagens', 'mensagens'],
        ['caixa.php?t=feedbacks', 'bi-star', 'Elogios / Sugestões', 'feedbacks'], ['caixa.php?t=newsletter', 'bi-send', 'Newsletter', 'newsletter'],
    ];
    if ($adm) { $nav[] = ['Administração']; $nav[] = ['users.php', 'bi-shield-lock', 'Usuários', 'users']; }
    $nav[] = ['Conta']; $nav[] = ['senha.php', 'bi-key', 'Alterar minha senha', 'senha']; $nav[] = ['logout.php', 'bi-box-arrow-right', 'Sair', 'logout'];
    ?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title><?= e($title) ?> — Painel OBAM</title>
<link href="<?= u('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
<link href="<?= u('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link href="<?= u('admin/admin.css') ?>" rel="stylesheet"></head><body>
<aside class="adm-side" id="side"><div class="adm-brand"><span>●</span> OBAM · Painel</div>
<div style="padding:10px 18px;font-size:.78rem;color:#aaa"><?= e($u['nome']) ?><br><span class="badge <?= $adm ? 'bg-warning text-dark' : 'bg-secondary' ?>"><?= $adm ? 'ADMIN' : 'RESPONSÁVEL (somente leitura)' ?></span></div>
<?php foreach ($nav as $n): if (count($n) === 1): ?><div class="grp"><?= e($n[0]) ?></div><?php else: ?>
<a href="<?= u('admin/' . $n[0]) ?>" class="<?= $active === $n[3] ? 'act' : '' ?>"><i class="bi <?= $n[1] ?> me-2"></i><?= e($n[2]) ?></a><?php endif; endforeach; ?>
<a href="<?= u('index.php') ?>" target="_blank" style="margin-top:14px"><i class="bi bi-box-arrow-up-right me-2"></i>Ver o site</a></aside>
<div class="adm-main">
<div class="adm-top"><div class="d-flex align-items-center gap-2"><button class="btn btn-dark adm-toggle" onclick="document.getElementById('side').classList.toggle('open')"><i class="bi bi-list"></i></button><h1><?= e($title) ?></h1></div></div>
<?= flash_html() ?>
<?php }
function admin_footer(): void { ?>
</div><script src="<?= u('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script>document.addEventListener('click',function(e){var s=document.getElementById('side');if(s&&s.classList.contains('open')&&!s.contains(e.target)&&!e.target.closest('.adm-toggle'))s.classList.remove('open');});</script>
</body></html>
<?php }
function ro_notice(): void { if (!is_admin()) echo '<div class="alert alert-info py-2"><i class="bi bi-eye me-1"></i> Perfil <strong>responsável</strong>: você pode visualizar e exportar, mas não editar nem excluir.</div>'; }
function confirm_form(string $action, array $hidden, string $label = 'Excluir', string $msg = 'Excluir este item?'): string {
    $h = csrf_field() . '<input type="hidden" name="act" value="' . e($action) . '">';
    foreach ($hidden as $k => $v) $h .= '<input type="hidden" name="' . e($k) . '" value="' . e($v) . '">';
    return '<form method="post" class="d-inline" onsubmit="return confirm(\'' . e($msg) . '\')">' . $h . '<button class="btn btn-sm btn-outline-danger" title="' . e($label) . '"><i class="bi bi-trash"></i></button></form>';
}
