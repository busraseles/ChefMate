
function applyTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('theme', theme);
  syncThemeButton(document.getElementById('themeToggle'), theme);
  syncThemeButton(document.getElementById('themeToggleMobile'), theme);
}

function syncThemeButton(btn, theme) {
  if (!btn) return;
  const label = btn.querySelector('.theme-pill-label');
  const icon = btn.querySelector('.theme-pill-icon i');
  const isDark = theme === 'dark';
  if (label) label.textContent = isDark ? 'DARK' : 'LIGHT';
  if (icon) icon.className = isDark ? 'fas fa-moon' : 'fas fa-sun';
}

function initTheme() {
  const saved = localStorage.getItem('theme');
  if (saved === 'light' || saved === 'dark') {
    applyTheme(saved);
    return;
  }
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  applyTheme(prefersDark ? 'dark' : 'light');
}

function bindThemeButtons() {
  const desktop = document.getElementById('themeToggle');
  const mobile = document.getElementById('themeToggleMobile');
  const toggle = () => {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    applyTheme(current === 'dark' ? 'light' : 'dark');
  };
  if (desktop) desktop.addEventListener('click', toggle);
  if (mobile) mobile.addEventListener('click', toggle);
}

document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  bindThemeButtons();

    const navbar = document.querySelector('.chef-navbar');
  if (navbar && navbar.dataset.navbarStyle === 'transparent') {
    const onScroll = () => {
      if (window.scrollY > 40) {
        navbar.classList.add('bg-solid');
        navbar.classList.remove('bg-transparent');
      } else {
        navbar.classList.remove('bg-solid');
        navbar.classList.add('bg-transparent');
      }
    };
    window.addEventListener('scroll', onScroll);
    onScroll();
  }
});
