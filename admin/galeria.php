<?php require __DIR__ . '/_crud.php';
crud(['table' => 'galeria', 'title' => 'Galeria de fotos', 'active' => 'galeria', 'fields' => [
 ['name' => 'imagem', 'label' => 'Imagem', 'type' => 'image', 'req' => 1], ['name' => 'legenda', 'label' => 'Legenda', 'max' => 200]],
 'cols' => [['Foto', fn($r) => '<img class="thumb-s" src="' . e(img_url($r['imagem'])) . '">'], ['Legenda', 'legenda']]]);
