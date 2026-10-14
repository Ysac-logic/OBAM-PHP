<?php require __DIR__ . '/_crud.php';
crud(['table' => 'videos', 'title' => 'Vídeos (YouTube)', 'active' => 'videos', 'fields' => [
 ['name' => 'titulo', 'label' => 'Título', 'req' => 1, 'max' => 200], ['name' => 'youtube_url', 'label' => 'Link do YouTube', 'req' => 1, 'max' => 255], ['name' => 'ativo', 'label' => 'Ativo', 'type' => 'check', 'default' => 1]],
 'cols' => [['Título', 'titulo'], ['Link', fn($r) => '<a href="' . e($r['youtube_url']) . '" target="_blank">abrir</a>'], ['Ativo', fn($r) => $r['ativo'] ? 'Sim' : 'Não']]]);
