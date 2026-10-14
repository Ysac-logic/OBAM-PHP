<?php require __DIR__ . '/_crud.php';
crud(['table' => 'orgaos', 'title' => 'Órgãos por esfera', 'active' => 'orgaos', 'order' => 'FIELD(esfera,"Municipal","Estadual","Federal"), ordem, id', 'group' => 'esfera', 'fields' => [
 ['name' => 'esfera', 'label' => 'Esfera', 'type' => 'select', 'opts' => ['Municipal' => 'Municipal', 'Estadual' => 'Estadual', 'Federal' => 'Federal'], 'col' => 4],
 ['name' => 'nome', 'label' => 'Nome do órgão', 'req' => 1, 'col' => 8, 'max' => 255], ['name' => 'sede', 'label' => 'Local da sede', 'max' => 255], ['name' => 'link', 'label' => 'Link do órgão', 'max' => 255]],
 'cols' => [['Esfera', 'esfera'], ['Órgão', 'nome'], ['Sede', 'sede']]]);
