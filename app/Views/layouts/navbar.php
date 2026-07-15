<?php

$activeNav = $activeNav ?? '';
$navbarStyle = $navbarStyle ?? 'solid';
$navbarBgClass = $navbarStyle === 'transparent' ? 'bg-transparent' : 'bg-solid';
?>
<nav class="navbar navbar-expand-lg fixed-top chef-navbar <?= htmlspecialchars($navbarBgClass) ?>" data-navbar-style="<?= htmlspecialchars($navbarStyle) ?>">
  <div class="container position-relative">

    <!-- Logo: Çatal-bıçak ikonu + site adı, ana sayfaya bağlantılı -->
    <!-- index.php HENÜZ migrate edilmedi, bu yüzden ../index.php (legacy) -->
    <a class="navbar-brand chef-brand" href="." aria-label="ChefMate.AI">
      <span class="chef-badge"><i class="fas fa-utensils"></i></span>
      <span class="chef-logo-text">ChefMate.AI</span>
    </a>

    <!-- Mobilde menüyü açıp kapatan hamburger butonu -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"
      aria-controls="nav" aria-expanded="false" aria-label="Menü">
      <span class="navbar-toggler-icon"><span></span></span>
    </button>

    <!-- Navigasyon linkleri: Masaüstünde yatay, mobilde açılır menü -->
    <div class="collapse navbar-collapse justify-content-center" id="nav">
      <ul class="navbar-nav chef-nav-center align-items-lg-center gap-lg-2">
        <li class="nav-item">
          <a class="nav-link <?= $activeNav === 'home' ? 'active' : '' ?>" href=".">Ana Sayfa</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $activeNav === 'about' ? 'active' : '' ?>" href="about" <?= $activeNav === 'about' ? 'aria-current="page"' : '' ?>>Hakkımızda</a>
        </li>

        <!-- Sadece mobilde görünür: Tema değiştirme butonu -->
        <li class="nav-item d-lg-none mt-2">
          <button id="themeToggleMobile" class="theme-pill w-100 justify-content-between" type="button" aria-label="Tema değiştir">
            <span class="theme-pill-label">LIGHT</span>
            <span class="theme-pill-icon" aria-hidden="true"><i class="fas fa-sun"></i></span>
          </button>
        </li>

        <?php if (empty($_SESSION['user_id'])): ?>
          <li class="nav-item d-lg-none mt-2">
            <a class="nav-link auth-gold-btn w-100" href="register">Başla</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>

    <!-- Sağ taraf: Tema butonu + oturum durumuna göre koşullu buton grubu -->
    <div class="chef-right d-none d-lg-flex align-items-center gap-3">
      <button id="themeToggle" class="theme-pill" type="button" aria-label="Tema değiştir">
        <span class="theme-pill-label">LIGHT</span>
        <span class="theme-pill-icon" aria-hidden="true"><i class="fas fa-sun"></i></span>
      </button>

      <?php if (empty($_SESSION['user_id'])): ?>
        <a class="nav-link auth-gold-btn" href="login">Giriş Yap</a>
        <a class="nav-link auth-gold-btn" href="register">Kayıt Ol</a>
      <?php else: ?>
        <span style="color:gold;font-weight:600;"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <!-- dashboard.php HENÜZ migrate edilmedi, bu yüzden ../dashboard.php (legacy) -->
        <a class="nav-link auth-gold-btn" href="dashboard">Panel</a>
        <a class="nav-link auth-gold-btn" href="logout">Çıkış</a>
      <?php endif; ?>
    </div>

  </div>
</nav>
