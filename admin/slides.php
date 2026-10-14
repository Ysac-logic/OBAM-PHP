<?php require __DIR__ . '/_crud.php';
crud(['table' => 'slides', 'title' => 'Carrossel da página inicial', 'active' => 'slides', 'fields' => [
 ['name' => 'tag', 'label' => 'Etiqueta', 'col' => 4, 'max' => 60], ['name' => 'titulo', 'label' => 'Título', 'req' => 1, 'col' => 8, 'max' => 200],
 ['name' => 'texto', 'label' => 'Texto', 'type' => 'textarea', 'rows' => 2, 'max' => 400], ['name' => 'botao', 'label' => 'Texto do botão', 'col' => 4, 'max' => 60],
 ['name' => 'url', 'label' => 'Link do botão', 'col' => 8, 'max' => 255, 'help' => 'Ex.: post.php?slug=abelhas ou https://...'], ['name' => 'imagem', 'label' => 'Imagem de fundo', 'type' => 'image', 'req' => 1],
 ['name' => 'ativo', 'label' => 'Ativo', 'type' => 'check', 'default' => 1]],
 'cols' => [['Img', fn($r) => '<img class="thumb-s" src="' . e(img_url($r['imagem'])) . '">'], ['Título', 'titulo'], ['Etiqueta', 'tag'], ['Ativo', fn($r) => $r['ativo'] ? 'Sim' : 'Não']]]);
