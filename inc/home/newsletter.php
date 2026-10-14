<section class="newsletter-strip"><div class="container"><div class="row align-items-center g-4" data-aos="fade-up">
  <div class="col-lg-6"><h3><?= e(cfg('newsletter_titulo')) ?></h3><p><?= e(cfg('newsletter_texto')) ?></p></div>
  <div class="col-lg-6">
    <form class="nl-form" method="post" action="<?= u('newsletter.php') ?>"><?= csrf_field() ?>
      <input type="email" name="email" placeholder="Seu melhor e-mail..." required maxlength="160">
      <input type="text" name="website" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
      <button type="submit"><i class="bi bi-send me-1"></i> Inscrever</button>
    </form>
    <p style="font-size:.72rem;color:rgba(255,255,255,.4);margin-top:8px;">Sem spam. Seus dados seguem a <a href="<?= u('post.php?slug=politica-privacidade') ?>" style="color:inherit;text-decoration:underline">Política de Privacidade</a>.</p>
  </div></div></div></section>
