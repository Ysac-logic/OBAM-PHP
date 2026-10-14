<?php
require __DIR__ . '/../inc/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
if (!is_admin()) { http_response_code(403); exit(json_encode(['error' => 'Sem permissão.'])); }
try { csrf_check(); $p = upload_file('file', 'img', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif']); echo json_encode(['url' => $p ? u($p) : null, 'error' => $p ? null : 'Nenhum arquivo.']); }
catch (Throwable $e) { echo json_encode(['error' => $e->getMessage()]); }
