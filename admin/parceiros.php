<?php require __DIR__ . '/_crud.php';
crud(['table' => 'parceiros', 'title' => 'Parceiros', 'active' => 'parceiros', 'fields' => [
 ['name' => 'nome', 'label' => 'Nome completo', 'req' => 1, 'max' => 200], ['name' => 'sigla', 'label' => 'Sigla', 'col' => 4, 'max' => 40],
 ['name' => 'funcao', 'label' => 'Papel (ex.: Parceiro Institucional)', 'col' => 4, 'max' => 80], ['name' => 'icone', 'label' => 'Ícone (Bootstrap Icons)', 'col' => 4, 'default' => 'bi-people-fill', 'help' => 'Ex.: bi-bank, bi-mortarboard-fill'],
 ['name' => 'descricao', 'label' => 'Descrição', 'type' => 'textarea', 'max' => 500], ['name' => 'url', 'label' => 'Site (opcional)', 'max' => 255],
 ['name' => 'ativo', 'label' => 'Ativo (aparece no site)', 'type' => 'check', 'default' => 1]],
 'cols' => [['Sigla', 'sigla'], ['Nome', 'nome'], ['Papel', 'funcao'], ['Ativo', fn($r) => $r['ativo'] ? 'Sim' : 'Não']]]);
