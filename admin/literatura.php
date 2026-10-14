<?php require __DIR__ . '/_crud.php';
crud(['table' => 'literatura', 'title' => 'Literatura Apícola / Melípona', 'active' => 'literatura', 'fields' => [
 ['name' => 'titulo', 'label' => 'Título', 'req' => 1, 'max' => 200], ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'textarea', 'max' => 500],
 ['name' => 'arquivo', 'label' => 'Arquivo (PDF, DOC...)', 'type' => 'file', 'help' => 'Até 30 MB. Ou use o link externo abaixo.'], ['name' => 'url', 'label' => 'Link externo (opcional)', 'max' => 255]],
 'cols' => [['Título', 'titulo'], ['Arquivo', fn($r) => e($r['arquivo'] ?: $r['url'])]]]);
