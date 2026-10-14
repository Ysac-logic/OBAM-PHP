
(function() {
  'use strict';

  
  const preloader = document.getElementById('preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.style.opacity = '0';
      setTimeout(() => preloader.remove(), 500);
    });
  }

  
  const header = document.getElementById('header');
  if (header) {
    const onScroll = () => {
      header.classList.toggle('scrolled', window.scrollY > 10);
    };
    window.addEventListener('scroll', onScroll);
  }

  
  const mobileToggle = document.querySelector('.mobile-nav-toggle');
  const navmenu = document.querySelector('#navmenu');
  if (mobileToggle && navmenu) {
    const icon = mobileToggle.querySelector('i') || mobileToggle;
    const setOpen = (open) => {
      navmenu.classList.toggle('mobile-nav-active', open);
      document.body.classList.toggle('nav-open', open);
      icon.classList.toggle('bi-list', !open);
      icon.classList.toggle('bi-x', open);
      mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      mobileToggle.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
    };
    mobileToggle.addEventListener('click', () => setOpen(!navmenu.classList.contains('mobile-nav-active')));

    document.addEventListener('click', (e) => {
      if (!navmenu.contains(e.target) && !mobileToggle.contains(e.target)) setOpen(false);
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && navmenu.classList.contains('mobile-nav-active')) { setOpen(false); mobileToggle.focus(); }
    });
    // fecha ao escolher um link real (não os de submenu '#')
    navmenu.addEventListener('click', (e) => {
      const a = e.target.closest('a');
      if (a && a.getAttribute('href') !== '#') setOpen(false);
    });
    // volta ao estado desktop ao redimensionar
    window.matchMedia('(min-width: 992px)').addEventListener('change', (m) => { if (m.matches) setOpen(false); });
  }

  document.querySelectorAll('.navmenu .dropdown > a').forEach(link => {
    link.setAttribute('aria-haspopup', 'true');
    link.setAttribute('aria-expanded', 'false');
    link.addEventListener('click', (e) => {
      if (window.innerWidth < 992) {
        e.preventDefault();
        const open = link.parentElement.classList.toggle('active');
        link.setAttribute('aria-expanded', open ? 'true' : 'false');
      }
    });
  });

  
  const scrollTopBtn = document.getElementById('scroll-top');
  if (scrollTopBtn) {
    const toggleScrollTop = () => {
      scrollTopBtn.classList.toggle('active', window.scrollY > 200);
    };
    window.addEventListener('scroll', toggleScrollTop);
    scrollTopBtn.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  
  if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 600, easing: 'ease-in-out', once: true, offset: 60 });
  }

  
  document.querySelectorAll('.swiper.init-swiper').forEach(el => {
    const configEl = el.querySelector('.swiper-config');
    if (configEl && typeof Swiper !== 'undefined') {
      const config = JSON.parse(configEl.textContent);
      new Swiper(el, config);
    }
  });

  
  // Lightbox da galeria: abre na própria página, com arraste/setas/teclado
  const galleryItems = Array.from(document.querySelectorAll('.gallery-item'));
  if (galleryItems.length) {
    let box = null, track = null, cap = null, counter = null;
    let idx = 0, dragX = 0, startX = 0, startY = 0, dragging = false, axis = null, startT = 0;

    const src = (i) => galleryItems[i].querySelector('img').currentSrc || galleryItems[i].href;
    const full = (i) => galleryItems[i].getAttribute('href') || src(i);
    const width = () => box.clientWidth;

    const render = (animate = true) => {
      track.style.transition = animate ? 'transform .35s cubic-bezier(.22,.8,.3,1)' : 'none';
      track.style.transform = 'translate3d(' + (-idx * width() + dragX) + 'px,0,0)';
      const slides = track.children;
      for (let k = 0; k < slides.length; k++) {
        const img = slides[k].firstChild;
        if (Math.abs(k - idx) <= 1 && !img.src) img.src = full(k);
      }
      const it = galleryItems[idx];
      cap.textContent = it.dataset.caption || '';
      counter.textContent = (idx + 1) + ' / ' + galleryItems.length;
    };
    const go = (n) => {
      idx = Math.max(0, Math.min(galleryItems.length - 1, n));
      dragX = 0; render(true);
    };
    const close = () => {
      if (!box) return;
      document.removeEventListener('keydown', onKey);
      window.removeEventListener('resize', onResize);
      box.remove(); box = null;
      document.body.style.overflow = '';
    };
    const onKey = (e) => {
      if (e.key === 'Escape') close();
      else if (e.key === 'ArrowRight') go(idx + 1);
      else if (e.key === 'ArrowLeft') go(idx - 1);
    };
    const onResize = () => render(false);

    const open = (i) => {
      idx = i; dragX = 0;
      box = document.createElement('div');
      box.className = 'lb';
      box.setAttribute('role', 'dialog');
      box.setAttribute('aria-modal', 'true');
      box.setAttribute('aria-label', 'Galeria de fotos');
      box.innerHTML =
        '<button type="button" class="lb-close" aria-label="Fechar"><i class="bi bi-x-lg"></i></button>' +
        '<button type="button" class="lb-nav lb-prev" aria-label="Foto anterior"><i class="bi bi-chevron-left"></i></button>' +
        '<button type="button" class="lb-nav lb-next" aria-label="Próxima foto"><i class="bi bi-chevron-right"></i></button>' +
        '<div class="lb-track"></div><div class="lb-info"><div class="lb-cap"></div><div class="lb-count"></div></div>';
      track = box.querySelector('.lb-track');
      cap = box.querySelector('.lb-cap');
      counter = box.querySelector('.lb-count');
      galleryItems.forEach((it) => {
        const s = document.createElement('div'); s.className = 'lb-slide';
        const im = document.createElement('img'); im.alt = it.dataset.caption || ''; im.draggable = false;
        s.appendChild(im); track.appendChild(s);
      });
      document.body.appendChild(box);
      document.body.style.overflow = 'hidden';
      render(false);

      box.querySelector('.lb-close').addEventListener('click', close);
      box.querySelector('.lb-prev').addEventListener('click', () => go(idx - 1));
      box.querySelector('.lb-next').addEventListener('click', () => go(idx + 1));
      box.addEventListener('click', (e) => { if (e.target.classList.contains('lb-slide')) close(); });

      box.addEventListener('pointerdown', (e) => {
        if (e.target.closest('button')) return;
        dragging = true; axis = null; startX = e.clientX; startY = e.clientY; startT = Date.now(); dragX = 0;
      });
      box.addEventListener('pointermove', (e) => {
        if (!dragging) return;
        const dx = e.clientX - startX, dy = e.clientY - startY;
        if (!axis && (Math.abs(dx) > 6 || Math.abs(dy) > 6)) axis = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y';
        if (axis !== 'x') return;
        const atEdge = (idx === 0 && dx > 0) || (idx === galleryItems.length - 1 && dx < 0);
        dragX = atEdge ? dx / 3 : dx;
        render(false);
      });
      const end = (e) => {
        if (!dragging) return;
        dragging = false;
        const fast = Date.now() - startT < 300 && Math.abs(dragX) > 30;
        if (axis === 'x' && (Math.abs(dragX) > width() * 0.2 || fast)) go(idx + (dragX < 0 ? 1 : -1));
        else { dragX = 0; render(true); }
      };
      box.addEventListener('pointerup', end);
      box.addEventListener('pointercancel', end);
      document.addEventListener('keydown', onKey);
      window.addEventListener('resize', onResize);
      box.querySelector('.lb-close').focus();
    };

    galleryItems.forEach((item, i) => {
      item.addEventListener('click', (e) => { e.preventDefault(); open(i); });
    });
  }

})();
