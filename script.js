// Mobile menu
const menuBtn = document.querySelector('.menu-btn');
const nav = document.querySelector('.nav');
if (menuBtn && nav) {
  menuBtn.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    menuBtn.setAttribute('aria-expanded', String(open));
  });
}

// Nav dropdown: open on mouse hover (pointer enter) and close on mouse leave
document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
  const toggleBtn = dropdown.querySelector('.nav-dropdown-toggle');

  dropdown.addEventListener('mouseenter', () => {
    if (window.innerWidth > 1100) {
      dropdown.classList.add('open');
      if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
    }
  });

  dropdown.addEventListener('mouseleave', () => {
    if (window.innerWidth > 1100) {
      dropdown.classList.remove('open');
      if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
    }
  });

  if (toggleBtn) {
    toggleBtn.addEventListener('click', (e) => {
      if (window.innerWidth <= 1100) {
        e.preventDefault();
        const open = dropdown.classList.toggle('open');
        toggleBtn.setAttribute('aria-expanded', String(open));
      }
    });
  }
});

// Close open dropdowns if clicked outside
document.addEventListener('click', (e) => {
  if (!e.target.closest('.nav-dropdown')) {
    document.querySelectorAll('.nav-dropdown.open').forEach(dropdown => {
      dropdown.classList.remove('open');
      const toggleBtn = dropdown.querySelector('.nav-dropdown-toggle');
      if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
    });
  }
});

// Accordion
document.querySelectorAll('.acc-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const item = btn.parentElement;
    const body = item.querySelector('.acc-body');
    const open = item.classList.toggle('open');
    body.style.maxHeight = open ? body.scrollHeight + 'px' : 0;
  });
});

// Scroll reveal
const io = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
}, { threshold: .12 });
document.querySelectorAll('.reveal').forEach(el => io.observe(el));

// Broken image fallback (logo / photos)
document.querySelectorAll('img').forEach(img => {
  img.addEventListener('error', () => { img.style.visibility = 'hidden'; });
});

// Forms (front-end demo: connect to your backend / Google Form / email service)
document.querySelectorAll('form[data-form]').forEach(form => {
  form.addEventListener('submit', e => {
    e.preventDefault();
    const ok = form.querySelector('.form-success');
    if (ok) ok.style.display = 'block';
    form.reset();
  });
});

// Count-up for hero stats
document.querySelectorAll('[data-count]').forEach(el => {
  const end = +el.dataset.count, suffix = el.dataset.suffix || '';
  let n = 0; const step = Math.max(1, Math.round(end / 40));
  const t = setInterval(() => {
    n += step; if (n >= end) { n = end; clearInterval(t); }
    el.textContent = n + suffix;
  }, 30);
});
