<?php require __DIR__ . '/_crud.php';
crud(['table' => 'menu_items', 'title' => 'Menu do site', 'active' => 'menu', 'group' => 'parent_id',
 'order' => 'COALESCE(parent_id,id), parent_id IS NOT NULL, ordem, id',
 'intro' => 'Use as setas para mudar a ordem dentro do mesmo nível. Itens com “Pertence a” aparecem dentro de um menu suspenso. Use <code>#</code> como link para criar um grupo.',
 'fields' => [['name' => 'label', 'label' => 'Texto do menu', 'req' => 1, 'max' => 120],
 ['name' => 'url', 'label' => 'Link', 'req' => 1, 'max' => 255, 'help' => 'Páginas: post.php?slug=nome-da-pagina · Categoria: publicacoes.php?cat=Eventos · Externo: https://...'],
 ['name' => 'parent_id', 'label' => 'Pertence a (grupo)', 'type' => 'select', 'nullable' => 1, 'opts' => fn() => array_column(qa('SELECT id,label FROM menu_items WHERE parent_id IS NULL ORDER BY ordem,id'), 'label', 'id')],
 ['name' => 'visivel', 'label' => 'Visível', 'type' => 'check', 'default' => 1]],
 'cols' => [['Item', fn($r) => ($r['parent_id'] ? '<span class="text-muted ms-4">└ </span>' : '<strong>') . e($r['label']) . ($r['parent_id'] ? '' : '</strong>')], ['Link', 'url'], ['Visível', fn($r) => $r['visivel'] ? 'Sim' : 'Não']]]);
