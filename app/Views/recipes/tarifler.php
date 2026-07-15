<?php

?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChefMate.AI — Tarifler</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Cinzel:wght@700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index.css" />
    <link rel="stylesheet" href="assets/css/tarifler.css" />
</head>
<body>

    <!-- ══════════════════════════════════════════════════════════
         NAVBAR — Sayfanın üstüne sabitlenmiş navigasyon çubuğu.
         ──────────────────────────────────────────────────────────
         • ChefMate logosu → index.php'ye bağlantılı
         • Arama formu (GET ?ara=): 3 karakter girilince 650ms gecikmeyle otomatik arama
         • Tema toggle butonu: dark/light modu localStorage'a kaydeder
         • Oturum açıksa: kullanıcı adı + "← Panel'e Dön" + "Çıkış" butonları
         • Oturum yoksa: "Giriş Yap" + "Kayıt Ol" butonları
    ═══════════════════════════════════════════════════════════ -->
    <!-- NAVBAR (index.php stili) -->
    <nav class="navbar navbar-expand-lg fixed-top chef-navbar bg-transparent" style="z-index:1000;">
        <div class="container position-relative">
            <a class="navbar-brand chef-brand" href=".">
                <span class="chef-badge"><i class="fas fa-utensils"></i></span>
                <span class="chef-logo-text">ChefMate.AI</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navTar">
                <span class="navbar-toggler-icon"><span></span></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navTar">
                <ul class="navbar-nav chef-nav-center align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link" href=".">Ana Sayfa</a></li>
                    <li class="nav-item"><a class="nav-link active" href="tarifler">Tarifler</a></li>
                    <li class="nav-item"><a class="nav-link" href="about">Hakkımızda</a></li>
                </ul>
            </div>
            <div class="chef-right d-none d-lg-flex align-items-center gap-3">
                <!-- Arama -->
                <form method="GET" action="tarifler" class="d-flex align-items-center" style="margin:0;gap:6px;">
                    <div class="nav-search" style="position:relative;max-width:220px;">
                        <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#555;font-size:.8rem;z-index:1;pointer-events:none;"></i>
                        <input type="text" name="ara" id="searchInputNav" placeholder="Tarif ara…" value="<?= htmlspecialchars($aramaMetni) ?>" autocomplete="off"
                            style="padding:8px 16px 8px 34px;border-radius:50px;border:1.5px solid rgba(0,0,0,0.18);background:#fff;color:#111;caret-color:#111;font-size:.82rem;outline:none;cursor:text;">
                    </div>
                    <button type="submit" style="padding:8px 16px;background:var(--gold);color:#000;border:none;border-radius:50px;font-weight:700;font-size:.78rem;cursor:pointer;white-space:nowrap;">🔍 Ara</button>
                </form>
                <button id="themeToggle" class="theme-pill" type="button">
                    <span class="theme-pill-label">DARK</span>
                    <span class="theme-pill-icon"><i class="fas fa-moon"></i></span>
                </button>
                <?php if ($isLoggedIn): ?>
                    <span style="color:gold;font-weight:600;"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <a class="nav-link auth-gold-btn" href="dashboard">← Panel'e Dön</a>
                    <a class="nav-link auth-gold-btn" href="logout">Çıkış</a>
                <?php else: ?>
                    <a class="nav-link auth-gold-btn" href="login">Giriş Yap</a>
                    <a class="nav-link auth-gold-btn" href="register">Kayıt Ol</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- Navbar boşluk -->
    <div style="height:70px;"></div>

    <!-- ══════════════════════════════════════════════════════════
         HERO BÖLÜMÜ — Sayfanın üst tanıtım alanı.
         Unsplash'ten arka plan görseli + koyu gradient örtüsü.
         hero-stats: PHP'den gelen tarif sayısı, kategori sayısı ve cache süresi.
    ═══════════════════════════════════════════════════════════ -->
    <!-- HERO -->
    <section class="hero">
        <div class="hero-inner">
            <div class="hero-left">
                <p class="hero-eyebrow">Türk Mutfağı &mdash; Güncel Tarifler &mdash; RSS Canlı</p>
                <h1 class="hero-title">Leziz <span>Tarifler</span><br>Sizi Bekliyor</h1>
                <p class="hero-desc">lezizyemeklerim.com'dan anlık çekilen, kategorilere ayrılmış nefis Türk mutfağı tarifleri.</p>
            </div>
            <div class="hero-right">
                <div class="hero-stats">
                    <div class="stat"><strong><?= count($tarifler) ?></strong><span>Tarif</span></div>
                    <div class="stat"><strong><?= count($kategoriler) ?></strong><span>Kategori</span></div>
                    <div class="stat"><strong>1sa</strong><span>Cache</span></div>
                </div>
            </div>
        </div>
    </section>
    <div class="gold-line"></div>

    <!-- ANA İÇERİK -->
    <main class="main-wrap">

        <!-- ══════════════════════════════════════════════════════════
             KATEGORİ ÇUBUĞU — Yatay kaydırılabilir filtre butonları.
             PHP foreach ile $kategoriler dizisinden üretilir.
             Aktif kategori 'aktif' CSS class'ı alır (altın arka plan).
             Sol/sağ ok butonları (kat-scroll-*): JS ile yatay kaydırma yapar.
             'Kullanıcılardan Tarifler' butonu ayrıca eklenir (RSS değil, DB kaynağı).
        ═══════════════════════════════════════════════════════════ -->
        <!-- KATEGORİ BAR -->
        <div class="kat-bar-wrap">
            <button class="kat-scroll-btn kat-scroll-left" id="katScrollLeft">&#8249;</button>
            <nav class="kat-bar" id="katBar">
                <?php foreach ($kategoriler as $slug => $kat): ?>
                    <a href="tarifler?kategori=<?= $slug ?>"
                        class="kat-btn <?= $slug === $aktifKat ? 'aktif' : '' ?>">
                        <?= $kat['ikon'] ?> <?= htmlspecialchars($kat['isim']) ?>
                    </a>
                <?php endforeach; ?>
                <a href="tarifler?kategori=kullanici-tarifleri"
                    class="kat-btn kat-btn-community <?php echo $aktifKat === 'kullanici-tarifleri' ? 'aktif' : ''; ?>">
                    👨‍🍳 Kullanıcılardan Tarifler
                </a>
            </nav>
            <button class="kat-scroll-btn kat-scroll-right" id="katScrollRight">&#8250;</button>
        </div>

        <!-- Kategori çubuğu kaydırma — IIFE (Immediately Invoked Function Expression).
             update(): Sol ok scrollLeft>0'da, sağ ok içerik genişliği aşılmadıkça görünür.
             btnL/btnR tıklanınca bar.scrollBy() ile 220px yumuşak kaydırma yapılır.
             scroll ve resize eventleri ile ok görünürlüğü sürekli güncellenir. -->

        <!-- ══════════════════════════════════════════════════════════
             TOOLBAR — Başlık + sayaç + aksiyon butonları satırı.
             Arama sonucu, kullanıcı tarifleri veya normal kategori olmasına
             göre başlık PHP tarafında koşullu render edilir.
             Kullanıcı tariflerinde ve oturum açıksa "Tarif Paylaş" butonu çıkar.
             Oturum yoksa "Giriş Yaparak Paylaş" bağlantısı gösterilir.
        ═══════════════════════════════════════════════════════════ -->
        <!-- TOOLBAR -->
        <div class="toolbar">
            <div class="toolbar-left">
                <h2>
                    <?php if (!empty($aramaMetni)): ?>
                        "<?= htmlspecialchars($aramaMetni) ?>"
                    <?php elseif ($aktifKat === 'kullanici-tarifleri'): ?>
                        👨‍🍳 Kullanıcılardan Tarifler
                    <?php else: ?>
                        <?= $kategoriler[$aktifKat]['ikon'] . ' ' . htmlspecialchars($kategoriler[$aktifKat]['isim']) ?>
                    <?php endif; ?>
                </h2>
                <?php if (!empty($tarifler)): ?>
                    <span class="count-badge"><?= count($tarifler) ?> tarif</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($aramaMetni)): ?>
                <a href="tarifler?kategori=<?= $aktifKat ?>" class="btn-nav">✕ Aramayı Temizle</a>
            <?php elseif ($aktifKat === 'kullanici-tarifleri' && $isLoggedIn): ?>
                <button onclick="openUserRecipeAdd()" style="padding:9px 20px;background:linear-gradient(135deg,#1a4a1a,#1e6b2e);border:1.5px solid #27ae60;color:#a8ffcb;border-radius:8px;font-weight:700;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    ➕ Tarif Paylaş
                </button>
            <?php elseif ($aktifKat === 'kullanici-tarifleri' && !$isLoggedIn): ?>
                <a href="login" style="padding:9px 20px;background:rgba(255,215,0,.1);border:1.5px solid #cfae55;color:#cfae55;border-radius:8px;font-weight:700;font-size:.82rem;text-decoration:none;">
                    🔒 Giriş Yaparak Paylaş
                </a>
            <?php endif; ?>
        </div>

        <!-- HATA KUTUSU — RSS yüklenemezse ($hata dolu ise) gösterilir.
             "Cache temizle" bağlantısı ?cache_temizle=1 ile mevcut XML cache'i siler. -->
        <?php if ($hata): ?>
            <div class="hata-box">
                <i class="fas fa-circle-exclamation" style="margin-top:0.1rem;flex-shrink:0;"></i>
                <div>
                    <strong>Yükleme Hatası:</strong> <?= htmlspecialchars($hata) ?><br>
                    <small><a href="?cache_temizle=1">Cache temizle ve tekrar dene →</a></small>
                </div>
            </div>
        <?php endif; ?>

        <!-- ══════════════════════════════════════════════════════════
             TARİF IZGARASI (#recipesGrid) — PHP tarafında ilk 12 kart.
             Her kart: görsel, başlık, özet, tarih, kaynak linki.
             Kullanıcı tarifleri için tıklama → openUserRecipeDetail() (JS modal).
             RSS tarifleri için tıklama → kaynağa yeni sekmede git.
             Görsel yoksa Unsplash fallback görseli + emoji placeholder kullanılır.
             Beğen/Kaydet/Yorum butonları: oturum açıksa gösterilir, yoksa login linki.
        ═══════════════════════════════════════════════════════════ -->
        <!-- GRID -->
        <section class="recipes-grid" id="recipesGrid">
            <?php if (!empty($tarifler)):
                $katIkon = $aktifKat === 'kullanici-tarifleri' ? '👨‍🍳' : ($kategoriler[$aktifKat]['ikon'] ?? '🍽️');
                $katIsim = $aktifKat === 'kullanici-tarifleri' ? 'Kullanıcı Tarifi' : ($kategoriler[$aktifKat]['isim'] ?? 'Tarif');
                foreach ($tarifler as $tarif):
                $isUserRec = !empty($tarif['is_user_recipe']);
                $cardOnClick = $isUserRec
                    ? "openUserRecipeDetail(" . htmlspecialchars(json_encode([
                        'id'    => $tarif['recipe_id'] ?? 0,
                        'title' => $tarif['baslik'],
                        'resim' => $tarif['resim'] ?? '',
                        'ozet'  => $tarif['ozet'] ?? '',
                        'tarih' => $tarif['tarih'] ?? '',
                        'user_name' => $tarif['user_name'] ?? '',
                        'ingredients' => $tarif['ingredients'] ?? '',
                        'instructions' => $tarif['instructions'] ?? '',
                        'is_mine' => isset($tarif['recipe_id']) && ($currentUserId > 0),
                    ])) . ")"
                    : "window.open('" . htmlspecialchars(addslashes($tarif['url'])) . "','_blank')";
            ?>
                    <article class="recipe-card" onclick="<?= $cardOnClick ?>">

                        <div class="card-img-wrap">
                            <?php if (!empty($tarif['resim'])): ?>
                                <img
                                    src="<?= htmlspecialchars($tarif['resim']) ?>"
                                    alt="<?= htmlspecialchars($tarif['baslik']) ?>"
                                    loading="lazy"
                                    data-page-url="<?= htmlspecialchars($tarif['url']) ?>"
                                    onerror="fixCardImageFromDetail(this)">
                            <?php endif; ?>
                            <div class="img-ph" <?= !empty($tarif['resim']) ? 'style="display:none"' : '' ?>>
                                <img src="https://images.unsplash.com/photo-1547592180-85f173990554?w=400&q=60"
                                    alt="Yemek" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0.6;">
                                <span class="e" style="position:relative;z-index:1;"><?= $katIkon ?></span>
                            </div>
                            <?php if ($isUserRec && !empty($tarif['user_name'])): ?>
                                <span class="rozet">👤 <?= htmlspecialchars($tarif['user_name']) ?></span>
                            <?php else: ?>
                                <span class="rozet"><?= htmlspecialchars($katIsim) ?></span>
                            <?php endif; ?>

                            <?php if (!$isUserRec): ?>
                            <!-- FAVORİ -->
                            <button class="fav-btn"
                                data-url="<?= htmlspecialchars($tarif['url']) ?>"
                                onclick="event.stopPropagation();favToggle(this)"
                                title="Favorilere Ekle">♡</button>
                            <?php endif; ?>

                            <!-- FOTOĞRAF ÇEK -->
                            <?php if (!empty($tarif['resim'])): ?>
                                <button class="capture-btn"
                                    data-img="<?= htmlspecialchars($tarif['resim']) ?>"
                                    data-title="<?= htmlspecialchars($tarif['baslik']) ?>"
                                    onclick="event.stopPropagation();openLightbox(this)"
                                    title="Fotoğrafı Görüntüle &amp; İndir">
                                    <i class="fas fa-camera"></i>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($tarif['baslik']) ?></h3>
                            <?php if (!empty($tarif['ozet'])): ?>
                                <p class="card-desc"><?= htmlspecialchars($tarif['ozet']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="card-footer-custom">
                            <?php if (!empty($tarif['tarih'])): ?>
                                <span class="card-date">
                                    <i class="fas fa-calendar-days" style="font-size:0.65rem;"></i>
                                    <?= htmlspecialchars($tarif['tarih']) ?>
                                </span>
                            <?php else: ?>
                                <span></span>
                            <?php endif; ?>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <?php if ($isUserRec): ?>
                                    <button class="card-link" onclick="event.stopPropagation();openUserRecipeDetail(<?= htmlspecialchars(json_encode([
                                        'id'    => $tarif['recipe_id'] ?? 0,
                                        'title' => $tarif['baslik'],
                                        'resim' => $tarif['resim'] ?? '',
                                        'ozet'  => $tarif['ozet'] ?? '',
                                        'tarih' => $tarif['tarih'] ?? '',
                                        'user_name' => $tarif['user_name'] ?? '',
                                        'ingredients' => $tarif['ingredients'] ?? '',
                                        'instructions' => $tarif['instructions'] ?? '',
                                        'is_mine' => ($currentUserId > 0),
                                    ])) ?>)" style="background:transparent;border:none;cursor:pointer;padding:0;">
                                        Detay <i class="fas fa-info-circle" style="font-size:0.6rem;"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="card-link" onclick="event.stopPropagation();openRecipeDetail(<?= htmlspecialchars(json_encode([
                                        'title' => $tarif['baslik'],
                                        'resim' => $tarif['resim'] ?? '',
                                        'url'   => $tarif['url'],
                                        'ozet'  => $tarif['ozet'] ?? '',
                                        'tarih' => $tarif['tarih'] ?? '',
                                        'kat'   => $katIsim,
                                    ])) ?>)" style="background:transparent;border:none;cursor:pointer;padding:0;">
                                        Detay <i class="fas fa-info-circle" style="font-size:0.6rem;"></i>
                                    </button>
                                    <span style="color:var(--border);">|</span>
                                    <a class="card-link"
                                        href="<?= htmlspecialchars($tarif['url']) ?>"
                                        target="_blank" rel="noopener"
                                        onclick="event.stopPropagation()">
                                        Kaynak <i class="fas fa-arrow-up-right-from-square" style="font-size:0.6rem;"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php
                        $rKey = md5($tarif['url']);
                        $isLiked = isset($myLikes[$rKey]);
                        $isSaved = isset($mySaves[$rKey]);
                        ?>
                        <!-- BEĞENİ / KAYDET / YORUM -->
                        <?php if ($isLoggedIn): ?>
                            <div class="card-interact" onclick="event.stopPropagation()">
                                <button class="int-btn int-like <?= $isLiked ? 'active' : '' ?>"
                                    data-key="<?= htmlspecialchars($rKey) ?>"
                                    data-title="<?= htmlspecialchars($tarif['baslik']) ?>"
                                    data-img="<?= htmlspecialchars($tarif['resim'] ?? '') ?>"
                                    data-url="<?= htmlspecialchars($tarif['url']) ?>"
                                    onclick="toggleLike(this)">
                                    <?= $isLiked ? '❤️' : '🤍' ?> Beğen
                                </button>
                                <button class="int-btn int-save <?= $isSaved ? 'active' : '' ?>"
                                    data-key="<?= htmlspecialchars($rKey) ?>"
                                    data-title="<?= htmlspecialchars($tarif['baslik']) ?>"
                                    data-img="<?= htmlspecialchars($tarif['resim'] ?? '') ?>"
                                    data-url="<?= htmlspecialchars($tarif['url']) ?>"
                                    onclick="toggleSave(this)">
                                    <?= $isSaved ? '📖 Kaydedildi' : '🔖 Kaydet' ?>
                                </button>
                                <button class="int-btn int-comment"
                                    data-key="<?= htmlspecialchars($rKey) ?>"
                                    data-title="<?= htmlspecialchars($tarif['baslik']) ?>"
                                    data-img="<?= htmlspecialchars($tarif['resim'] ?? '') ?>"
                                    data-url="<?= htmlspecialchars($tarif['url']) ?>"
                                    onclick="openComment(this)">
                                    💬 Yorum
                                </button>
                                <button class="int-btn int-menu-add"
                                    data-key="<?= htmlspecialchars($rKey) ?>"
                                    data-title="<?= htmlspecialchars($tarif['baslik']) ?>"
                                    data-img="<?= htmlspecialchars($tarif['resim'] ?? '') ?>"
                                    data-url="<?= htmlspecialchars($tarif['url']) ?>"
                                    onclick="openMenuAdd(this)">
                                    🍽️ Menüye Ekle
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="card-interact" style="text-align:center;padding:8px 12px;">
                                <a href="login" style="color:#cfae55;font-size:.8rem;text-decoration:none;">🔒 Giriş yaparak beğen & kaydet</a>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach;
            else: ?>
                <div class="empty-state">
                    <span class="e">🔍</span>
                    <h3><?= !empty($aramaMetni) ? '"' . htmlspecialchars($aramaMetni) . '" için sonuç yok' : 'Tarif Bulunamadı' ?></h3>
                    <p>Farklı bir kategori veya arama terimi deneyin.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- SONSUZ SCROLL SENTİNEL ────────────────────────────────────────────
             IntersectionObserver bu div'i izler.
             Viewport'a girince loadMore() tetiklenir ve AJAX'tan sonraki 12 kart gelir.
             scrollLoader: Yüklenirken dönen ikon gösterilir.
             scrollEnd   : Tüm tarifler yüklendiğinde "✅ Tüm tarifler yüklendi" mesajı. -->
        <!-- SONSUZ SCROLL SENTINEL -->
        <div id="scrollSentinel" style="height:60px;display:flex;align-items:center;justify-content:center;margin:16px 0;">
            <div id="scrollLoader" style="display:none;gap:8px;align-items:center;color:var(--gold);">
                <i class="fas fa-circle-notch fa-spin"></i> Tarifler yükleniyor...
            </div>
            <div id="scrollEnd" style="display:none;color:var(--muted);font-size:.85rem;">✅ Tüm tarifler yüklendi.</div>
        </div>

    </main>

    <!-- LİGHTBOX — Tarif görselini tam ekran gösterir.
         openLightbox(btn): Kameralı küçük butondan tetiklenir.
         İndir butonu: <a> elemanıyla download attribute trick'i kullanır.
         "Kaynağa Git": görsel URL'sini yeni sekmede açar.
         ESC tuşu ile de kapatılabilir (keydown listener). -->
    <div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
        <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
        <img id="lb-img" src="" alt="">
        <p class="lightbox-title" id="lb-title"></p>
        <div class="lightbox-actions">
            <button class="lb-btn lb-gold" onclick="downloadImg()">
                <i class="fas fa-download"></i> İndir
            </button>
            <a id="lb-open" href="#" target="_blank" rel="noopener" class="lb-btn lb-outline">
                <i class="fas fa-external-link"></i> Kaynağa Git
            </a>
            <button class="lb-btn lb-outline" onclick="closeLightbox()">Kapat</button>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="site-footer">
        Tarifler: <a href="https://www.lezizyemeklerim.com" target="_blank" rel="noopener">lezizyemeklerim.com</a>
        (Açık RSS Feed) — ChefMate.AI Arayüzü &copy; 2025
    </footer>

    <!-- TARİF DETAY MODAL (#recipeDetailOverlay) ────────────────────────────
         openRecipeDetail(data): RSS tariflerinde önce mevcut RSS verisiyle anında dolar,
           ardından api.php → recipe_detail action'ı ile malzeme + adım listesi çekilir.
         openUserRecipeDetail(data): Kullanıcı tariflerinde tüm veri zaten mevcuttur,
           ek API çağrısına gerek yoktur; düzenleme paneli de buradan açılır.
         "Geri Dön" butonu ve overlay dışı tıklama closeRecipeDetail() ile kapatır.
         rdIngSection / rdStepSection: İçerik yoksa display:none ile gizlenir. -->
    <!-- TARİF DETAY MODAL -->
    <div class="recipe-detail-overlay" id="recipeDetailOverlay">
        <div class="recipe-detail-box" id="recipeDetailBox">
            <button class="rd-close" onclick="closeRecipeDetail()">×</button>
            <!-- GERİ DÖN BUTONU (Madde 4) -->
            <div style="padding:12px 18px 0;">
              <button onclick="closeRecipeDetail()" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);color:var(--text,#fff);padding:6px 16px;border-radius:99px;cursor:pointer;font-size:13px;font-weight:700;">
                ← Tariflere Geri Dön
              </button>
            </div>
            <div id="rdImgWrap"></div>
            <div class="rd-body">
                <div class="rd-title" id="rdTitle"></div>
                <div class="rd-meta" id="rdMeta"></div>
                <div class="rd-section" id="rdIngSection" style="display:none;">
                    <h4>🧺 Malzemeler</h4>
                    <div class="rd-ing-list" id="rdIngList"></div>
                </div>
                <div class="rd-section" id="rdStepSection" style="display:none;">
                    <h4>👩‍🍳 Yapılışı</h4>
                    <div class="rd-steps-list" id="rdStepsList"></div>
                </div>
                <div class="rd-section" id="rdDescSection" style="display:none;">
                    <h4>📝 Açıklama</h4>
                    <p id="rdDesc" style="font-size:.9rem;color:var(--text);line-height:1.7;"></p>
                </div>
                <div class="rd-actions">
                    <a id="rdSourceLink" href="#" target="_blank" rel="noopener" class="btn-gold-outline" style="font-size:.75rem;padding:.5rem 1.2rem;">Kaynağa Git <i class="fas fa-external-link-alt"></i></a>
                    <button class="btn-gold-outline" style="font-size:.75rem;padding:.5rem 1.2rem;" onclick="closeRecipeDetail()">← Geri Dön</button>
                </div>
            </div>
        </div>
    </div>

    <!-- YORUM MODALİ (#cmOverlay) ──────────────────────────────────────────
         openComment(btn): Tarif kartındaki 💬 butonuyla açılır.
         Mevcut yorumlar api.php → recipe_get_interactions ile yüklenir.
         cmSend → cmInput içeriğini api.php → recipe_comment ile kaydeder.
         Overlay dışı tıklama veya × butonu ile kapatılır. -->
    <!-- YORUM MODALİ -->
    <div class="cm-overlay" id="cmOverlay">
        <div class="cm-box">
            <button class="cm-close" onclick="document.getElementById('cmOverlay').classList.remove('open')">×</button>
            <h3 id="cmTitle">💬 Yorumlar</h3>
            <textarea class="cm-input" id="cmInput" placeholder="Yorumunuzu yazın..."></textarea>
            <button class="cm-send" id="cmSend">Gönder</button>
            <div class="cm-list" id="cmList"></div>
        </div>
    </div>
    <div id="tarifToast"></div>

    <!-- MENÜYE EKLE MODALİ -->
    <div class="menu-add-overlay" id="menuAddOverlay">
        <div class="menu-add-box">
            <button class="menu-add-close" onclick="closeMenuAdd()">×</button>

            <div id="maFormView">
                <div class="menu-add-recipe-name" id="maRecipeName">🍽️ Tarif</div>
                <div class="menu-add-subtitle">Tarifi günlük menünüze ekleyin.</div>

                <div class="ma-row">
                    <input type="date" id="maDate">
                    <select id="maMealType">
                        <option value="kahvaltı">☀️ Kahvaltı</option>
                        <option value="öğle" selected>🌤️ Öğle</option>
                        <option value="akşam">🌙 Akşam</option>
                        <option value="atıştırmalık">🍎 Atıştırmalık</option>
                    </select>
                </div>

                <div class="ma-row">
                    <input type="number" id="maCal" placeholder="🔥 Kalori (kcal) — opsiyonel" min="0" max="9999">
                </div>
                <div class="ma-cal-hint">💡 Kalori bilgisi bilmiyorsanız boş bırakabilirsiniz. Tarife tıklayıp ölçeğinize göre tahmin edebilirsiniz.</div>

                <button class="ma-submit-btn" id="maSubmitBtn" onclick="submitMenuAdd()">
                    ➕ Menüye Ekle
                </button>
            </div>

            <div class="ma-success" id="maSuccess">
                <span class="mas-icon">✅</span>
                <p>Tarif menünüze başarıyla eklendi!</p>
                <a href="dashboard" class="ma-goto-btn">📅 Dashboard'a Git →</a>
            </div>
        </div>
    </div>

    <!-- KULLANICI TARİF EKLE MODALİ -->
    <div class="ura-overlay" id="uraOverlay">
        <div class="ura-box">
            <button class="ura-close" onclick="closeUserRecipeAdd()">×</button>
            <div id="uraFormView">
                <div class="ura-title">👨‍🍳 Tarifinizi Paylaşın</div>

                <label class="ura-label">Tarif Adı *</label>
                <input type="text" id="uraTitle" class="ura-input" placeholder="Örn: Annemin Tarhana Çorbası">

                <label class="ura-label">Kategori</label>
                <select id="uraCategory" class="ura-input">
                    <option value="">-- Seçiniz --</option>
                    <option>Et Yemekleri</option><option>Tatlılar</option><option>Çorbalar</option>
                    <option>Hamur İşleri</option><option>Kahvaltılık</option><option>Vejetaryen</option>
                    <option>Balık</option><option>Kekler</option><option>Kurabiye</option>
                    <option>Diyet</option><option>Köfte</option><option>Diğer</option>
                </select>

                <label class="ura-label">Malzemeler * (her satıra bir malzeme)</label>
                <textarea id="uraIngredients" class="ura-input ura-textarea" placeholder="2 su bardağı un&#10;3 yumurta&#10;1 çay bardağı zeytinyağı"></textarea>

                <label class="ura-label">Yapılış Adımları * (her satıra bir adım)</label>
                <textarea id="uraInstructions" class="ura-input ura-textarea" style="min-height:120px;" placeholder="Malzemeleri bir kaba koyun.&#10;Iyice yoğurun.&#10;Önceden ısıtılmış fırında pişirin."></textarea>

                <label class="ura-label">Fotoğraf (opsiyonel)</label>
                <div style="background:rgba(255,255,255,.04);border:1.5px solid #2a2a2a;border-radius:10px;padding:10px 12px;margin-bottom:14px;">
                  <div style="display:flex;gap:14px;margin-bottom:8px;font-size:.83rem;color:#bbb;">
                    <label style="display:flex;align-items:center;gap:5px;cursor:pointer;">
                      <input type="radio" name="uraImgMode" value="file" id="uraImgModeFile" checked style="accent-color:#27ae60;"> Dosya Yükle
                    </label>
                    <label style="display:flex;align-items:center;gap:5px;cursor:pointer;">
                      <input type="radio" name="uraImgMode" value="url" id="uraImgModeUrl" style="accent-color:#27ae60;"> URL ile
                    </label>
                  </div>
                  <div id="uraImgFileWrap">
                    <input type="file" id="uraImageFile" accept="image/jpeg,image/png,image/webp" style="font-size:.82rem;color:#ccc;width:100%;padding:4px 0;">
                    <div style="font-size:.7rem;color:#666;margin-top:2px;">JPEG/PNG/WebP — maks 5 MB</div>
                  </div>
                  <div id="uraImgUrlWrap" style="display:none;">
                    <input type="url" id="uraImage" class="ura-input" style="margin-bottom:0;" placeholder="https://...">
                  </div>
                  <div id="uraImgPrev" style="margin-top:6px;display:none;"><img id="uraImgPrevImg" style="max-height:90px;border-radius:8px;border:1px solid #333;" src="" alt="Önizleme"></div>
                </div>

                <button class="ura-submit" id="uraSubmitBtn" onclick="submitUserRecipe()">
                    📤 Tarifi Paylaş
                </button>
            </div>
            <div class="ura-success" id="uraSuccess">
                <span class="ura-ok-icon">✅</span>
                <p>Tarifiniz başarıyla paylaşıldı!</p>
                <button onclick="closeUserRecipeAdd();window.location.reload();" style="margin-top:12px;padding:8px 24px;background:linear-gradient(135deg,#1a6b2e,#27ae60);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;">Sayfayı Yenile</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ once: true, duration: 800 });</script>
    <script src="assets/js/tarifler.js"></script>
</body>
</html>
