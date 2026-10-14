<?php
require __DIR__ . '/inc/layout.php';
$locs = qa("SELECT localidade, COUNT(*) n FROM apicultores WHERE status='ativo' GROUP BY localidade ORDER BY n DESC, localidade LIMIT 40");
site_header('Mapa de Apiários / Meliponários'); page_title('Mapa de Apiários / Meliponários');
?>
<section class="section"><div class="container"><div class="row g-5 align-items-start">
  <div class="col-lg-8" data-aos="fade-right">
    <span class="section-label"><i class="bi bi-geo-alt-fill"></i> Localização</span><h2 class="section-title mb-3">Apiários e Meliponários Cadastrados</h2>
    <p class="text-muted mb-4">Mapa do território de atuação do OBAM. O georreferenciamento dos apiários é feito em parceria com a ASSAPEC e demais instituições.</p>
    <div class="map-wrapper"><div id="apiary-map" class="apiary-map" role="region" aria-label="Mapa de apiários e meliponários de Euclides da Cunha"></div></div>
    <p class="small text-muted mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Os pontos indicam <strong>localidades</strong> (posição aproximada), nunca endereços de apicultores. Fonte da base: © OpenStreetMap.</p>
  </div>
  <div class="col-lg-4" data-aos="fade-left">
    <div class="sidebar-widget mb-4"><h4>Estatísticas</h4><div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
      <?php for ($i = 1; $i <= 4; $i++): ?><div style="background:var(--yellow-pale);border-radius:var(--radius-md);padding:16px;text-align:center;"><div style="font-size:1.6rem;font-weight:800;"><?= e(cfg("stat{$i}_num")) ?></div><div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--gray-500);"><?= e(cfg("stat{$i}_label")) ?></div></div><?php endfor; ?></div></div>
    <?php if ($locs): ?><div class="sidebar-widget mb-4"><h4>Localidades com apicultores</h4><?php foreach ($locs as $l): ?><div class="cat-link"><span><i class="bi bi-geo-alt me-2"></i><?= e($l['localidade']) ?></span><span class="cat-count"><?= (int)$l['n'] ?></span></div><?php endforeach; ?></div><?php endif; ?>
    <div class="sidebar-widget"><h4>Cadastre-se</h4><p style="font-size:.87rem;color:var(--gray-500)">Apicultor? Faça sua ficha de cadastro no OBAM e entre na rede de monitoramento.</p><a href="<?= u('cadastro.php') ?>" class="btn-primary-y d-block text-center">Cadastro do Apicultor</a></div>
  </div></div></div></section>
<?php
// Comunidades de Euclides da Cunha citadas publicamente (prefeitura / edital CAR-BA) com unidades de mel ou associações.
$publicas = [
    ['nome' => 'Serra Branca', 'nota' => 'Unidade de beneficiamento de mel'],
    ['nome' => 'Varzinha', 'nota' => 'Unidade de beneficiamento de mel'],
    ['nome' => 'Muriti', 'nota' => 'Unidade de beneficiamento de mel'],
    ['nome' => 'Curralinho II', 'nota' => 'Unidade de beneficiamento de mel'],
    ['nome' => 'Campo Seco', 'nota' => 'Associação comunitária'],
    ['nome' => 'Prata', 'nota' => 'Associação de agricultores familiares'],
];
$pontos = [];
foreach ($publicas as $p) $pontos[] = ['nome' => $p['nome'], 'nota' => $p['nota'], 'n' => 0];
foreach ($locs as $l) {
    $nm = trim($l['localidade']); if ($nm === '') continue;
    $dup = false; foreach ($pontos as &$q) if (mb_strtolower($q['nome']) === mb_strtolower($nm)) { $q['n'] = (int)$l['n']; $dup = true; } unset($q);
    if (!$dup) $pontos[] = ['nome' => $nm, 'nota' => '', 'n' => (int)$l['n']];
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<style>
.apiary-map{height:480px;width:100%;z-index:0}
@media (max-width:575px){.apiary-map{height:380px}}
.apiary-pin{background:var(--yellow);border:3px solid #111;border-radius:50% 50% 50% 0;transform:rotate(-45deg);width:26px!important;height:26px!important;margin:-26px 0 0 -13px!important;box-shadow:0 2px 6px rgba(0,0,0,.35)}
.apiary-pin::after{content:"";position:absolute;inset:6px;background:#111;border-radius:50%}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
(function () {
  var pontos = <?= json_encode($pontos, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
  var el = document.getElementById('apiary-map');
  if (!el || typeof L === 'undefined') { if (el) el.innerHTML = '<p class="p-4 text-center text-muted">Não foi possível carregar o mapa.</p>'; return; }
  var centro = [-10.507, -39.016]; // Euclides da Cunha, BA
  var map = L.map(el, { scrollWheelZoom: false, tap: true }).setView(centro, 10);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18, attribution: '&copy; OpenStreetMap' }).addTo(map);
  map.on('focus', function () { map.scrollWheelZoom.enable(); });
  map.on('blur', function () { map.scrollWheelZoom.disable(); });
  L.marker(centro, { title: 'Euclides da Cunha' }).addTo(map).bindPopup('<strong>Euclides da Cunha</strong><br>Bahia');

  var icon = L.divIcon({ className: 'apiary-pin', iconSize: [26, 26] });
  var KEY = 'obam_geo_v1', cache = {};
  try { cache = JSON.parse(localStorage.getItem(KEY) || '{}'); } catch (e) {}
  var save = function () { try { localStorage.setItem(KEY, JSON.stringify(cache)); } catch (e) {} };
  var inRegion = function (la, lo) { return Math.abs(la - centro[0]) < 1.5 && Math.abs(lo - centro[1]) < 1.5; };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; };

  var place = function (p, la, lo) {
    var html = '<strong>' + esc(p.nome) + '</strong>';
    if (p.nota) html += '<br>' + esc(p.nota);
    if (p.n) html += '<br>' + p.n + ' apicultor(es) cadastrado(s)';
    L.marker([la, lo], { icon: icon, title: p.nome }).addTo(map).bindPopup(html);
  };
  var fetchGeo = function (p) {
    var q = encodeURIComponent(p.nome + ', Euclides da Cunha, Bahia, Brasil');
    return fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=br&q=' + q, { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        var ok = j && j[0] && inRegion(+j[0].lat, +j[0].lon);
        cache[p.nome] = ok ? [+j[0].lat, +j[0].lon] : null; save();
        return cache[p.nome];
      }).catch(function () { return undefined; });
  };
  var i = 0, bounds = [];
  (function next() {
    if (i >= pontos.length) { if (bounds.length > 1) map.fitBounds(bounds, { padding: [30, 30], maxZoom: 12 }); return; }
    var p = pontos[i++], c = cache[p.nome];
    if (c !== undefined) { if (c) { place(p, c[0], c[1]); bounds.push(c); } return next(); }
    fetchGeo(p).then(function (r) { if (r) { place(p, r[0], r[1]); bounds.push(r); } setTimeout(next, 1100); });
  })();
})();
</script>
<?php site_footer();
