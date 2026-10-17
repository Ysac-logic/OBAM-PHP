<?php
require_once __DIR__ . '/bootstrap.php';

function menu_tree(): array {
    $rows = qa('SELECT * FROM menu_items WHERE visivel=1 ORDER BY ordem, id');
    $t = []; foreach ($rows as $r) if (!$r['parent_id']) { $r['kids'] = []; $t[$r['id']] = $r; }
    foreach ($rows as $r) if ($r['parent_id'] && isset($t[$r['parent_id']])) $t[$r['parent_id']]['kids'][] = $r;
    return $t;
}
function current_rel(): string { return basename($_SERVER['SCRIPT_NAME']) . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''); }
function item_active(array $i): bool {
    $c = current_rel(); $x = $i['url'];
    if ($x === '#' || $x === '') return false;
    $xs = rawurldecode($x); $cs = rawurldecode($c);
    return $xs === $cs || (!str_contains($xs, '?') && $xs === basename($_SERVER['SCRIPT_NAME']) && $xs !== 'post.php' && $xs !== 'publicacoes.php');
}
function render_menu(): string {
    $o = '<ul>';
    foreach (menu_tree() as $m) {
        if ($m['kids']) {
            $act = false; foreach ($m['kids'] as $k) if (item_active($k)) $act = true;
            $o .= '<li class="dropdown"><a href="#"' . ($act ? ' class="active"' : '') . '><span>' . e($m['label']) . '</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a><ul>';
            foreach ($m['kids'] as $k) $o .= '<li><a href="' . e(link_url($k['url'])) . '"' . (item_active($k) ? ' class="active"' : '') . '>' . e($k['label']) . '</a></li>';
            $o .= '</ul></li>';
        } else {
            $o .= '<li><a href="' . e(link_url($m['url'])) . '"' . (item_active($m) ? ' class="active"' : '') . '>' . e($m['label']) . '</a></li>';
        }
    }
    return $o . '</ul>';
}
function site_header(string $title, string $bodyClass = '', string $desc = ''): void {
    $name = cfg('site_nome', 'OBAM'); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> — <?= e($name) ?></title>
  <?php if ($desc): ?><meta name="description" content="<?= e($desc) ?>"><?php endif; ?>
  <link href="<?= e(img_url(cfg('favicon', 'assets/img/favicon.png'))) ?>" rel="icon">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="<?= u('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= u('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
  <link href="<?= u('assets/vendor/aos/aos.css') ?>" rel="stylesheet">
  <link href="<?= u('assets/vendor/swiper/swiper-bundle.min.css') ?>" rel="stylesheet">
  <link href="<?= u('assets/css/main.css') ?>" rel="stylesheet">
  <link href="<?= u('assets/css/extra.css') ?>" rel="stylesheet">
</head>
<body class="<?= e($bodyClass) ?>">
<header id="header" class="header d-flex align-items-center sticky-top">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="<?= u('index.php') ?>" class="logo d-flex align-items-center gap-2"><img src="<?= e(img_url(cfg('logo', 'assets/image/logo-completa.png'))) ?>" alt="<?= e($name) ?>"></a>
    <button type="button" class="mobile-nav-toggle d-lg-none" aria-label="Abrir menu" aria-controls="navmenu" aria-expanded="false"><i class="bi bi-list"></i></button>
    <nav id="navmenu" class="navmenu" aria-label="Menu principal"><?= render_menu() ?></nav>
  </div>
</header>
<main class="main">
<?php $f = flash_html(); if ($f): ?><div class="container pt-3"><?= $f ?></div><?php endif;
}
function page_title(string $h1, array $crumbs = []): void { ?>
<div class="page-title"><div class="container d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
  <h1><?= e($h1) ?></h1>
  <nav class="breadcrumbs"><ol><li><a href="<?= u('index.php') ?>">Início</a></li>
  <?php foreach ($crumbs as $l => $href): ?><li><a href="<?= e(link_url($href)) ?>"><?= e($l) ?></a></li><?php endforeach; ?>
  <li class="current"><?= e($h1) ?></li></ol></nav>
</div></div>
<?php }
function site_footer(): void {
    $name = cfg('site_nome', 'OBAM'); ?>
</main>
<footer id="footer" class="footer dark-background">
  <div class="container footer-top"><div class="row gy-4">
    <div class="col-lg-4 col-md-6 footer-about">
      <a href="<?= u('index.php') ?>" class="logo mb-2"><span class="footer-brand"><img src="<?= e(img_url(cfg('logo', 'assets/image/logo-completa.png'))) ?>" alt="<?= e($name) ?>"></span></a>
      <div class="footer-contact">
        <p><?= e(cfg('site_subtitulo')) ?></p><p><?= e(cfg('site_org')) ?></p>
        <?php if (cfg('email')): ?><p class="mt-2"><strong>Email:</strong> <?= e(cfg('email')) ?></p><?php endif; ?>
        <?php if (cfg('telefone')): ?><p><strong>Tel.:</strong> <?= e(cfg('telefone')) ?></p><?php endif; ?>
      </div>
      <div class="social-links">
        <?php foreach (['youtube' => 'YouTube', 'facebook' => 'Facebook', 'instagram' => 'Instagram'] as $k => $l): if (cfg($k)): ?>
          <a href="<?= e(cfg($k)) ?>" title="<?= $l ?>" target="_blank" rel="noopener"><i class="bi bi-<?= $k ?>"></i></a>
        <?php endif; endforeach; ?>
      </div>
    </div>
    <div class="col-lg-2 col-md-3 col-6 footer-links"><h4>Legal</h4><ul>
      <li><a href="<?= u('post.php?slug=termos-de-uso') ?>">Termos de Uso</a></li>
      <li><a href="<?= u('post.php?slug=politica-privacidade') ?>">Política de Privacidade</a></li>
      <li><a href="<?= u('post.php?slug=cookies') ?>">Cookies</a></li></ul></div>
    <div class="col-lg-2 col-md-3 col-6 footer-links"><h4>Institucional</h4><ul>
      <li><a href="<?= u('sobre.php') ?>">Quem Somos</a></li>
      <li><a href="<?= u('parceiros.php') ?>">Parceiros</a></li>
      <li><a href="<?= u('feedbacks.php') ?>">Elogios</a></li></ul></div>
    <div class="col-lg-2 col-md-3 col-6 footer-links"><h4>Conteúdo</h4><ul>
      <li><a href="<?= u('publicacoes.php') ?>">Publicações</a></li>
      <li><a href="<?= u('galeria.php') ?>">Galeria</a></li>
      <li><a href="<?= u('literatura.php') ?>">Literatura</a></li></ul></div>
    <div class="col-lg-2 col-md-3 col-6 footer-links"><h4>Participar</h4><ul>
      <li><a href="<?= u('cadastro.php') ?>">Cadastro do Apicultor</a></li>
      <li><a href="<?= u('denuncias.php') ?>">Denúncias</a></li>
      <li><a href="<?= u('contato.php') ?>">Contato</a></li></ul></div>
  </div></div>
  <div class="container copyright text-center">
    <p>© <span>Copyright</span> <strong class="px-1 sitename"><?= e($name) ?></strong> <span>Todos os direitos reservados</span></p>
    <div class="credits"><?= e(cfg('site_org')) ?> · Bahia · Brasil</div>
  </div>
</footer>
<a href="#" id="scroll-top" class="scroll-top"><i class="bi bi-arrow-up-short"></i></a>
<div id="preloader"><img src="<?= e(img_url(cfg('logo', 'assets/image/logo-completa.png'))) ?>" alt=""></div>
<script src="<?= u('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= u('assets/vendor/aos/aos.js') ?>"></script>
<script src="<?= u('assets/vendor/swiper/swiper-bundle.min.js') ?>"></script>
<script src="<?= u('assets/js/main.js') ?>"></script>
</body></html>
<?php }
function post_card(array $p, int $delay = 0, string $col = 'col-sm-6 col-lg-3'): void { ?>
<div class="<?= e($col) ?>" data-aos="fade-up" data-aos-delay="<?= $delay ?>"><div class="post-card">
  <div class="card-thumb"><img src="<?= e(img_url($p['imagem'] ?: 'assets/image/honeycomb.jpg')) ?>" alt="<?= e($p['titulo']) ?>" loading="lazy"></div>
  <div class="card-body"><div class="card-cat"><?= e($p['categoria']) ?></div><h3><a href="<?= u('post.php?slug=' . urlencode($p['slug'])) ?>"><?= e($p['titulo']) ?></a></h3>
  <div class="card-footer-inner"><span><i class="bi bi-calendar3 me-1"></i><?= e($p['publicado_em'] ? month_pt($p['publicado_em']) : '') ?></span><a href="<?= u('post.php?slug=' . urlencode($p['slug'])) ?>" class="read-link">Ler <i class="bi bi-arrow-right"></i></a></div></div>
</div></div>
<?php }
