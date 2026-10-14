<?php
require __DIR__ . '/inc/bootstrap.php';
if (is_post()) {
    csrf_check();
    $mail = filter_var(post('email'), FILTER_VALIDATE_EMAIL);
    if (post('website') !== '' || throttle('nl', 5, 600)) { flash('Não foi possível processar agora.', 'warning'); }
    elseif (!$mail) { flash('E-mail inválido.', 'danger'); }
    else { qx('INSERT IGNORE INTO newsletter (email) VALUES (?)', [$mail]); flash('Inscrição realizada. Obrigado!'); }
}
redirect($_SERVER['HTTP_REFERER'] ?? u('index.php'));
