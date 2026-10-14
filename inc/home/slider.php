<?php $slides = qa('SELECT * FROM slides WHERE ativo=1 ORDER BY ordem,id'); if (!$slides) return; ?>
<section id="slider" class="slider section dark-background"><div class="container" data-aos="fade-up">
  <div class="swiper init-swiper">
    <script type="application/json" class="swiper-config">{"loop":true,"speed":700,"autoplay":{"delay":5000},"slidesPerView":"auto","centeredSlides":true,"pagination":{"el":".swiper-pagination","clickable":true},"navigation":{"nextEl":".swiper-button-next","prevEl":".swiper-button-prev"}}</script>
    <div class="swiper-wrapper">
    <?php foreach ($slides as $s): ?>
      <div class="swiper-slide" style="background-image:url('<?= e(img_url($s['imagem'])) ?>');"><div class="content">
        <?php if ($s['tag']): ?><span class="slide-tag"><i class="bi bi-bookmark-star"></i> <?= e($s['tag']) ?></span><?php endif; ?>
        <h2><a href="<?= e(link_url($s['url'])) ?>"><?= e($s['titulo']) ?></a></h2>
        <p><?= e($s['texto']) ?></p>
        <?php if ($s['botao']): ?><a href="<?= e(link_url($s['url'])) ?>" class="slide-cta"><?= e($s['botao']) ?> <i class="bi bi-arrow-right"></i></a><?php endif; ?>
      </div></div>
    <?php endforeach; ?>
    </div>
    <div class="swiper-button-next"></div><div class="swiper-button-prev"></div><div class="swiper-pagination"></div>
  </div></div></section>
