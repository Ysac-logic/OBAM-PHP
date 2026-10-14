<?php require __DIR__ . '/_crud.php';
crud(['table' => 'equipe', 'title' => 'Equipe', 'active' => 'equipe', 'fields' => [
 ['name' => 'nome', 'label' => 'Nome', 'req' => 1, 'max' => 120], ['name' => 'cargo', 'label' => 'Cargo / função', 'max' => 120], ['name' => 'foto', 'label' => 'Foto', 'type' => 'image']],
 'cols' => [['Foto', fn($r) => $r['foto'] ? '<img class="thumb-s" src="' . e(img_url($r['foto'])) . '">' : ''], ['Nome', 'nome'], ['Cargo', 'cargo']]]);
