<section class="stats-strip"><div class="container"><div class="row g-0">
<?php for ($i = 1; $i <= 4; $i++): if (cfg("stat{$i}_num") === '' && cfg("stat{$i}_label") === '') continue; ?>
  <div class="col-6 col-md-3 stat-item"><div class="stat-number"><?= e(cfg("stat{$i}_num")) ?></div><div class="stat-label"><?= e(cfg("stat{$i}_label")) ?></div></div>
<?php endfor; ?>
</div></div></section>
