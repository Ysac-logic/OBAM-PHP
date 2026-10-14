<?php
require_once __DIR__ . '/_layout.php';
/* Motor CRUD genérico.  cfg: table,title,active,fields[],cols[],order,group,sortable,intro */
function crud(array $c): void {
    $t = $c['table']; $F = $c['fields']; $order = $c['order'] ?? 'ordem, id'; $group = $c['group'] ?? null;
    $sortable = $c['sortable'] ?? true; $self = basename($_SERVER['SCRIPT_NAME']);
    require_login();
    if (is_post()) {
        require_admin(); csrf_check(); $act = $_POST['act'] ?? ''; $id = (int)($_POST['id'] ?? 0);
        try {
            if ($act === 'delete' && $id) { qx("DELETE FROM `$t` WHERE id=?", [$id]); flash('Item excluído.'); }
            elseif ($act === 'move' && $id) crud_move($t, $id, $_POST['dir'] ?? 'up', $group);
            elseif ($act === 'save') {
                $vals = []; $cur = $id ? q1("SELECT * FROM `$t` WHERE id=?", [$id]) : null;
                foreach ($F as $f) {
                    $n = $f['name']; $type = $f['type'] ?? 'text';
                    if ($type === 'check') { $vals[$n] = isset($_POST[$n]) ? 1 : 0; continue; }
                    if ($type === 'image' || $type === 'file') {
                        $up = $type === 'image' ? upload_image("f_$n") : upload_file("f_$n", 'files', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'png'], 30);
                        $v = $up ?: (post($n) !== '' ? post($n) : ($cur[$n] ?? null));
                        if (!empty($_POST["rm_$n"])) $v = null;
                        $vals[$n] = $v; if (!empty($f['req']) && !$v) throw new RuntimeException("Envie: {$f['label']}.");
                        continue;
                    }
                    $v = post($n);
                    if (!empty($f['req']) && $v === '') throw new RuntimeException("Preencha: {$f['label']}.");
                    if ($type === 'number') $v = (int)$v;
                    elseif ($type === 'date') $v = $v === '' ? null : $v;
                    elseif ($type === 'select') { $o = $f['opts'] instanceof Closure ? ($f['opts'])() : $f['opts']; if ($v !== '' && !array_key_exists($v, $o)) throw new RuntimeException("Valor inválido: {$f['label']}."); if ($v === '' && ($f['nullable'] ?? false)) $v = null; }
                    elseif (!empty($f['max'])) $v = mb_substr($v, 0, $f['max']);
                    $vals[$n] = $v;
                }
                if ($id) { $set = implode(',', array_map(fn($k) => "`$k`=?", array_keys($vals))); qx("UPDATE `$t` SET $set WHERE id=?", [...array_values($vals), $id]); }
                else {
                    if (!isset($vals['ordem']) && $sortable) { $vals['ordem'] = 1 + (int)qv("SELECT COALESCE(MAX(ordem),0) FROM `$t`" . ($group && isset($vals[$group]) ? " WHERE `$group`<=>?" : ''), $group && isset($vals[$group]) ? [$vals[$group]] : []); }
                    qx("INSERT INTO `$t` (" . implode(',', array_map(fn($k) => "`$k`", array_keys($vals))) . ") VALUES (" . implode(',', array_fill(0, count($vals), '?')) . ")", array_values($vals));
                }
                flash('Salvo com sucesso.');
            }
        } catch (Throwable $e) { flash($e->getMessage(), 'danger'); if ($act === 'save') redirect($self . ($id ? "?a=edit&id=$id" : '?a=new')); }
        redirect($self);
    }
    $a = $_GET['a'] ?? 'list'; $id = (int)($_GET['id'] ?? 0); $ro = !is_admin();
    admin_header($c['title'], $c['active']); ro_notice();
    if (!empty($c['intro'])) echo '<p class="text-muted">' . $c['intro'] . '</p>';
    if ($a === 'new' || $a === 'edit') {
        $row = $a === 'edit' ? q1("SELECT * FROM `$t` WHERE id=?", [$id]) : []; if ($a === 'edit' && !$row) { echo '<div class="alert alert-warning">Item não encontrado.</div>'; admin_footer(); return; }
        if ($a === 'new' && $ro) { redirect($self); }
        echo '<div class="adm-card"><form method="post" enctype="multipart/form-data">' . csrf_field() . '<input type="hidden" name="act" value="save"><input type="hidden" name="id" value="' . $id . '"><fieldset ' . ($ro ? 'disabled' : '') . '><div class="row g-3">';
        foreach ($F as $f) {
            $n = $f['name']; $type = $f['type'] ?? 'text'; $v = $row[$n] ?? ($f['default'] ?? ''); $w = $f['col'] ?? 12;
            echo "<div class=\"col-md-$w\">";
            if ($type === 'check') { echo '<div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="' . e($n) . '" value="1" ' . ($v ? 'checked' : '') . ' id="f_' . e($n) . '"><label class="form-check-label" for="f_' . e($n) . '">' . e($f['label']) . '</label></div></div>'; continue; }
            echo '<label class="form-label fw-semibold">' . e($f['label']) . (!empty($f['req']) ? ' *' : '') . '</label>';
            if ($type === 'textarea') echo '<textarea class="form-control" name="' . e($n) . '" rows="' . ($f['rows'] ?? 4) . '">' . e($v) . '</textarea>';
            elseif ($type === 'select') { $o = $f['opts'] instanceof Closure ? ($f['opts'])() : $f['opts']; echo '<select class="form-select" name="' . e($n) . '">'; if (!empty($f['nullable'])) echo '<option value="">— nenhum —</option>'; foreach ($o as $k => $l) echo '<option value="' . e($k) . '" ' . ((string)$k === (string)$v ? 'selected' : '') . '>' . e($l) . '</option>'; echo '</select>'; }
            elseif ($type === 'image' || $type === 'file') {
                if ($v) echo '<div class="mb-2">' . ($type === 'image' ? '<img src="' . e(img_url($v)) . '" style="max-height:80px;border-radius:8px">' : '<a href="' . e(img_url($v)) . '" target="_blank">' . e($v) . '</a>') . ' <label class="ms-2 small"><input type="checkbox" name="rm_' . e($n) . '" value="1"> remover</label></div>';
                echo '<input type="hidden" name="' . e($n) . '" value="' . e($v) . '"><input class="form-control" type="file" name="f_' . e($n) . '"' . ($type === 'image' ? ' accept="image/*"' : '') . '>';
                if ($type === 'image') echo '<div class="form-text">Ou informe um caminho/URL no campo abaixo (opcional).</div><input class="form-control form-control-sm mt-1" type="text" name="' . e($n) . '_path" placeholder="assets/image/foto1.jpeg ou https://..." value="" onchange="this.form[\'' . e($n) . '\'].value=this.value">';
            }
            else echo '<input class="form-control" type="' . ($type === 'number' ? 'number' : ($type === 'date' ? 'date' : ($type === 'url' ? 'text' : 'text'))) . '" name="' . e($n) . '" value="' . e($v) . '"' . (!empty($f['max']) ? ' maxlength="' . $f['max'] . '"' : '') . '>';
            if (!empty($f['help'])) echo '<div class="form-text">' . e($f['help']) . '</div>';
            echo '</div>';
        }
        echo '</div></fieldset><div class="mt-3 d-flex gap-2">' . ($ro ? '' : '<button class="btn btn-y">Salvar</button>') . '<a class="btn btn-outline-secondary" href="' . e($self) . '">Voltar</a></div></form></div>';
    } else {
        $rows = qa("SELECT * FROM `$t` ORDER BY $order");
        echo '<div class="adm-card"><div class="d-flex justify-content-between mb-3 flex-wrap gap-2"><span class="text-muted">' . count($rows) . ' item(ns)</span>' . ($ro ? '' : '<a class="btn btn-y" href="' . e($self) . '?a=new"><i class="bi bi-plus-lg"></i> Novo</a>') . '</div><div class="table-responsive"><table class="table table-hover"><thead><tr>';
        foreach ($c['cols'] as $col) echo '<th>' . e($col[0]) . '</th>'; echo '<th style="width:170px"></th></tr></thead><tbody>';
        foreach ($rows as $r) {
            echo '<tr>'; foreach ($c['cols'] as $col) { $v = $col[1] instanceof Closure ? ($col[1])($r) : e($r[$col[1]] ?? ''); echo '<td>' . $v . '</td>'; }
            echo '<td class="text-end text-nowrap">';
            if (!$ro && $sortable) foreach (['up' => 'bi-arrow-up', 'down' => 'bi-arrow-down'] as $d => $ic) echo '<form method="post" class="d-inline">' . csrf_field() . '<input type="hidden" name="act" value="move"><input type="hidden" name="id" value="' . $r['id'] . '"><input type="hidden" name="dir" value="' . $d . '"><button class="btn btn-sm btn-outline-secondary" title="Mover"><i class="bi ' . $ic . '"></i></button></form> ';
            echo '<a class="btn btn-sm btn-outline-dark" href="' . e($self) . '?a=edit&id=' . $r['id'] . '" title="' . ($ro ? 'Ver' : 'Editar') . '"><i class="bi ' . ($ro ? 'bi-eye' : 'bi-pencil') . '"></i></a> ';
            if (!$ro) echo confirm_form('delete', ['id' => $r['id']]);
            echo '</td></tr>';
        }
        if (!$rows) echo '<tr><td colspan="' . (count($c['cols']) + 1) . '" class="text-muted">Nada cadastrado.</td></tr>';
        echo '</tbody></table></div></div>';
    }
    admin_footer();
}
function crud_move(string $t, int $id, string $dir, ?string $group): void {
    $r = q1("SELECT * FROM `$t` WHERE id=?", [$id]); if (!$r) return;
    $ids = array_column($group ? qa("SELECT id FROM `$t` WHERE `$group`<=>? ORDER BY ordem, id", [$r[$group]]) : qa("SELECT id FROM `$t` ORDER BY ordem, id"), 'id');
    $i = array_search($id, array_map('intval', $ids)); if ($i === false) return;
    $j = $dir === 'up' ? $i - 1 : $i + 1; if ($j < 0 || $j >= count($ids)) return;
    [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
    foreach ($ids as $k => $x) qx("UPDATE `$t` SET ordem=? WHERE id=?", [$k + 1, $x]);
}
