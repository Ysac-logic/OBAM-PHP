<?php
require __DIR__ . '/inc/layout.php';
site_header('Início', 'index-page', cfg('site_subtitulo'));
foreach (qa('SELECT chave FROM home_sections WHERE ativo=1 ORDER BY ordem,id') as $s) {
    $f = __DIR__ . '/inc/home/' . basename($s['chave']) . '.php';
    if (is_file($f)) include $f;
}
site_footer();
