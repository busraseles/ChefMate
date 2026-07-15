<?php

?>
<!doctype html>
<html lang="tr" data-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ChefMate.AI — Evdeki Malzemelerle Ne Pişirsem?</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&display=swap"
        rel="stylesheet" />

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

    <link rel="stylesheet" href="assets/css/index.css" />
    <link rel="stylesheet" href="assets/css/ai-detect.css" />
</head>
<body>
<body>
    <!-- PRELOADER
         Sayfa tamamen yüklenene kadar (window load) ekranı kaplar.
         JavaScript'te 1100ms sonra .loader-hidden class'ı eklenir:
           opacity: 0 + visibility: hidden → kaybolur, altındaki sayfa görünür.
         .bar-progress: Soldan sağa gidip gelen animasyonlu altın yükleme çubuğu.
    -->
    <div id="preloader">
        <div class="loader-content">
            <div class="loader-logo">
                <i class="fas fa-utensils"></i>
            </div>
            <div class="loader-text">CHEFMATE.AI</div>
            <div class="loader-bar">
                <div class="bar-progress"></div>
            </div>
        </div>
    </div>

    <!-- NAVBAR (Sabit Üst Menü)
         fixed-top → Her zaman ekranın üstünde sabit kalır.
         Başlangıçta bg-transparent (hero arka planıyla kaynaşır).
         scroll > 40px olunca JS ile .bg-solid eklenir → glassmorphism efekti.
         .chef-logo-text → "ChefMate.AI" metni altın renk kayan parıltı animasyonu (shine).
         Sağ kısım PHP ile kontrol edilir:
           - Giriş yapılmamışsa → "Giriş Yap" + "Kayıt Ol" altın butonları
           - Giriş yapılmışsa   → Kullanıcı adı (altın renk) + "Panel" + "Çıkış"
         .theme-pill → Light/Dark mod geçiş butonu (güneş/ay ikonu).
    -->
    <nav class="navbar navbar-expand-lg fixed-top chef-navbar bg-transparent">
        <div class="container position-relative">
            <a class="navbar-brand chef-brand" href=".">
                <span class="chef-badge"><i class="fas fa-utensils"></i></span>
                <span class="chef-logo-text">ChefMate.AI</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"><span></span></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="nav">
                <ul class="navbar-nav chef-nav-center align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link active" href=".">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tarifler">Tarifler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about">Hakkımızda</a>
                    </li>
                </ul>

                <!-- Mobil menü alt bölümü: tema + auth (sadece mobile görünür) -->
                <div class="d-flex d-lg-none flex-column gap-2 px-1 pb-3 mt-2 mobile-nav-footer">
                    <!-- Tema değiştirici -->
                    <button id="themeToggleMobile" class="mobile-theme-btn" type="button">
                        <span class="mobile-theme-icon"><i class="fas fa-sun"></i></span>
                        <span class="mobile-theme-label">LIGHT MOD</span>
                    </button>

                    <!-- Auth butonları -->
                    <?php if (empty($_SESSION["user_id"])): ?>
                        <a class="auth-gold-btn" href="login">🔑 Giriş Yap</a>
                        <a class="auth-gold-btn auth-register-btn" href="register">✨ Kayıt Ol</a>
                    <?php else: ?>
                        <div class="mobile-username">👋 <?= htmlspecialchars($_SESSION["user_name"]) ?></div>
                        <a class="auth-gold-btn" href="dashboard">📊 Panel</a>
                        <a class="auth-gold-btn auth-logout-btn" href="logout">🚪 Çıkış</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="chef-right d-none d-lg-flex align-items-center gap-3">

                <button id="themeToggle" class="theme-pill" type="button">
                    <span class="theme-pill-label">LIGHT</span>
                    <span class="theme-pill-icon"><i class="fas fa-sun"></i></span>
                </button>

                <?php if (empty($_SESSION["user_id"])): ?>

                    <a class="nav-link auth-gold-btn" href="login">Giriş Yap</a>
                    <a class="nav-link auth-gold-btn" href="register">Kayıt Ol</a>

                <?php else: ?>

                    <span style="color: gold; font-weight:600;">
                        <?= htmlspecialchars($_SESSION["user_name"]) ?>
                    </span>

                    <a class="nav-link auth-gold-btn" href="dashboard">Panel</a>
                    <a class="nav-link auth-gold-btn" href="logout">Çıkış</a>

                <?php endif; ?>

            </div>
        </div>
    </nav>

    <main>
        <!-- HERO (Tam Ekran Giriş Bölümü)
             #heroBg → hero.jpg yerel görseli; yüklenemezse JS (fixHeroBg) Unsplash URL'sine geçer.
             .overlay → Görselin üstünde yarı saydam gradient; yazıların okunmasını sağlar.
             .hero-title → "ChefMate.AI" — Playfair Display, altın renk parlama animasyonu.

             Malzeme Arama Çubuğu:
               #ingredient-input → Malzeme yazılır, Enter/+ ile chip'e dönüşür.
               #add-ingredient  → + butonu; addChip() fonksiyonunu tetikler.
               #camera-btn      → Kamera modalını açar (Bootstrap modal).
               #ingredient-chips → Eklenen malzemelerin renkli chip'leri burada görünür.

             Butonlar:
               #suggest-btn → "Yapay Zeka ile Tarif Bul" — chip'lerdeki malzemeleri toplayıp
                              setupAISearch() ile tarif araması yapar.
               "Tarif Ekle" → recipe-add.html sayfasına yönlendirir.
        -->
               
        <!-- HERO -->
        <header class="hero text-white">
            <!-- 1) Yerel hero.jpg 2) Bozulursa otomatik fallback -->
            <img id="heroBg" src="assets/images/hero.jpg" alt="Mutfak arka planı" class="hero-bg" loading="eager" decoding="async" />
            <div class="overlay"></div>

            <div class="hero-content">
                <h1 class="hero-title">ChefMate.AI</h1>
                <p class="hero-sub">Evdekilerle Harikalar Yarat</p>

                <div class="search-wrap mb-4">
                    <div class="input-group input-group-lg hero-input">
                        <input id="ingredient-input" type="text" class="form-control border-0"
                            placeholder="Örn: Domates, Yumurta, Peynir..." />
                        <button id="add-ingredient" class="btn btn-emerald px-4" type="button" aria-label="Malzeme Ekle">
                            <i class="fas fa-plus"></i>
                        </button>
                        <label id="hero-upload-btn" class="btn px-3 mb-0" type="button"
                            aria-label="Fotoğraf yükle ve malzeme tara"
                            title="Fotoğraf yükle — AI malzemeyi tanısın"
                            style="background:transparent; color:var(--muted); cursor:pointer; display:flex; align-items:center;"
                            for="heroFileInput">
                            <i class="fas fa-image"></i>
                        </label>
                        <input type="file" id="heroFileInput" accept="image/*" style="display:none;">
                        <button id="camera-btn" class="btn px-3" type="button"
                            aria-label="Kamera ile malzeme tanı" style="background:transparent; color:var(--muted);"
                            onclick="
                                var s=document.getElementById('ai-ingredient-detection');
                                var d=document.getElementById('aiSectionDivider');
                                if(s){s.style.display='';}
                                if(d){d.style.display='';}
                                s && s.scrollIntoView({behavior:'smooth'});
                                setTimeout(()=>{ document.getElementById('aiTabCamera')?.click(); }, 400);
                            ">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <div id="ingredient-chips" class="mt-3 d-flex justify-content-center gap-2 flex-wrap"></div>
                </div>
                <div class="buttons">
                    <button id="suggest-btn" class="btn" type="button">
                        <i class="fas fa-snowflake me-2"></i>Buzdolabına Ekle
                    </button>
                    <a href="tarifler" class="btn btn-light rounded-pill px-4 fw-bold">Tarif Ekle</a>
                </div>

            </div>
        </header>

        <!-- ALTIN BÖLÜCÜ ÇİZGİ (.gold-divider)
             Her iki bölüm arasına yerleştirilen ince, parlayan altın çizgi.
             CSS @keyframes goldShimmer ile soldan sağa ışık geçişi animasyonu.
             .short → daha kısa genişlik; .thin → 1px yükseklik. -->
        <div class="gold-divider"></div>

        <!-- MARKA FELSEFESİ ALINTISI (.why-quote)
             Uygulamanın "sürdürülebilir mutfak" yaklaşımını özetleyen tek alıntı.
             Playfair Display italik, büyük serif font.
             .hl spanları → "atıksız döngü", "akıllı seçimler", "yaratıcı dokunuşlar"
             kelimelerini altın rengiyle vurgular.
             Arka planda altın + beyaz radyal gradient glow efekti. -->
        <section class="why-quote">
            <div class="container">
                <div class="quote-frame">
                    <blockquote>
                        "Sürdürülebilir mutfak; <span class="hl">atıksız döngü</span>, <span class="hl">akıllı seçimler</span> ve
                        <span class="hl">yaratıcı dokunuşların</span> dengesidir."
                    </blockquote>
                    <div class="quote-by">ChefMate yaklaşımı</div>
                    <div class="gold-divider short thin"></div>

                </div>
            </div>
        </section>

        <!-- FEATURES — GSAP YATAY KAYDIRMA (5 Panel)
             .gsap-section → GSAP ScrollTrigger bu elementi "pin" eder (sayfada kilitler).
             Kullanıcı aşağı kaydırdıkça paneller sağdan sola kayar (yatay scroll hissi).
             Her panel .horizontal-wrapper içinde yan yana sıralanır (width: max-content).
             Panel görselleri ScrollTrigger ile hafif zoom (scale 1→1.08) efekti alır.

             Panel İçerikleri:
               01 — Yapay Zeka: Malzeme analizi + "50K+ Tarif" istatistik kartı
               02 — Akıllı Kamera: Buzdolabı fotoğrafıyla malzeme tanıma
               03 — Gurme Topluluğu: Tarif paylaşımı ve puanlama
               04 — Kişiye Özel Panel: Su takibi, kalori, rozet kazanma özellikleri
               05 — CTA paneli: "Hazır mısınız?" + "Ücretsiz Başla" butonu
                    #startConfettiBtn → tıklanınca altın konfeti patlar (bindGoldConfetti)
                    IntersectionObserver → Panel görünür olunca otomatik konfeti (autoGoldConfettiOnPanel5)
        -->
        <section class="features-premium gsap-section" id="features">
            <div class="features-header-sticky with-divider">
                <div class="features-badge">
                    <i class="fas fa-gem"></i>
                    <span>Premium Özellikler</span>
                </div>
                <h2 class="features-title-main">
                    Neden <span>ChefMate.AI</span>?
                </h2>
                <div class="gold-divider short thin"></div>
            </div>

            <div class="horizontal-wrapper">
                <!-- PANEL 1 -->
                <div class="panel panel-dark">
                    <span class="panel-num">01</span>
                    <div class="panel-content">
                        <div class="panel-text">
                            <span class="feature-tag"><i class="fas fa-brain"></i> Yapay Zeka</span>
                            <h3>Gör, Tanı, <br>Pişir.</h3>
                            <p>Malzemelerinizi yapay zeka ile anında analiz edin. En uygun tarifleri saniyeler içinde bulun.</p>
                            <div class="feature-stats">
                                <div class="stat-item">
                                    <div class="stat-value" style="color: var(--gold);">50K+</div>
                                    <div class="stat-label">Tarif</div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-visual">
                            <!-- Daha “ChefMate” vibe: şık tabak + modern mutfak -->
                            <img src="assets/images/gor_tani.jpg"
                                alt="AI ile tarif önerisi">
                        </div>
                    </div>
                </div>

                <!-- PANEL 2 -->
                <div class="panel panel-dark">
                    <span class="panel-num">02</span>
                    <div class="panel-content">
                        <div class="panel-visual">
                            <!-- Buzdolabı / market / taze malzeme -->
                            <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1600&q=80"
                                alt="Buzdolabı ve malzemeler">
                        </div>
                        <div class="panel-text" style="text-align: right;">
                            <span class="feature-tag" style="border-left: none; border-right: 4px solid var(--gold);">
                                <i class="fas fa-camera"></i> Kamera
                            </span>
                            <h3>Akıllı <br>Kamera</h3>
                            <p>Buzdolabınızı fotoğraflayın, yapay zeka malzemeleri tanısın. Görsel tanıma teknolojisi ile alışveriş
                                listenizi yönetin.</p>
                        </div>
                    </div>
                </div>

                <!-- PANEL 3 -->
                <div class="panel panel-dark">
                    <span class="panel-num">03</span>
                    <div class="panel-content">
                        <div class="panel-text">
                            <span class="feature-tag"><i class="fas fa-users"></i> Topluluk</span>
                            <h3>Gurme <br>Topluluğu</h3>
                            <p>Kendi tariflerinizi paylaşın, diğer kullanıcıların tariflerini deneyin ve puanlayın.</p>
                        </div>
                        <div class="panel-visual">
                            <!-- birlikte pişirme / paylaşım -->
                            <img src="https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?auto=format&fit=crop&w=1600&q=80"
                                alt="Topluluk ve paylaşım">
                        </div>
                    </div>
                </div>

                <!-- PANEL 4 -->
                <div class="panel panel-dark">
                    <span class="panel-num">04</span>
                    <div class="panel-content">
                        <div class="panel-visual">
                            <!-- dashboard hissi: laptop + analitik -->
                            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1600&q=80"
                                alt="User Dashboard">
                        </div>
                        <div class="panel-text" style="text-align: right;">
                            <span class="feature-tag" style="border-left: none; border-right: 4px solid var(--gold);">
                                <i class="fas fa-user-circle"></i> Panel
                            </span>
                            <h3>Kişiye Özel <br>Panel</h3>
                            <p>Kendi mutfağınızı yönetin ve kişiselleştirilmiş deneyimin keyfini çıkarın.</p>
                            <ul class="feature-list">
                                <li><i class="fas fa-tint"></i> Su ve Kalori Takibi</li>
                                <li><i class="fas fa-medal"></i> Rozet Kazanma</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- PANEL 5 -->
                <div class="panel panel-dark">
                    <span class="panel-num">05</span>
                    <div class="panel-content" style="justify-content: center; text-align: center;">
                        <div class="panel-text" style="max-width: 800px;">
                            <h3 style="font-size: 4rem; color: var(--gold);">Hazır mısınız?</h3>
                            <p style="font-size: 1.2rem;">Yapay zeka destekli mutfak deneyimine hoş geldiniz.</p>

                            <!-- ✅ Konfeti butonu: id eklendi -->
                            <a id="startConfettiBtn" href="register" class="btn mt-4"
                                style="background: var(--gold); color: #000; padding: 14px 40px; border-radius: 999px; font-weight: 800; font-size: 1.1rem; text-decoration: none;">
                                Ücretsiz Başla
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES → TESTİMONİALS BÖLÜCÜ -->
        <div class="gold-divider"></div>

        <!-- KULLANICI YORUMLARI (Testimonials)
             3 kart yan yana (Bootstrap col-lg-4).
             Her kart: avatar fotoğrafı (randomuser.me), isim, şehir, yıldız puanı, yorum metni.
             Sağ alt köşedeki büyük tırnak işareti (.testimonial-quote) dekoratif CSS elemanı.
             Hover'da kart yukarı kalkar ve altın kenarlık alır.
        -->
        <section class="testimonials-section">
            <div class="container">
                <div class="text-center mb-5 with-divider">
                    <span class="section-badge">Geri Bildirimler</span>
                    <h2 class="section-big-title">Kullanıcılar Ne Diyor?</h2>
                    <div class="gold-divider short thin"></div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="testimonial-card position-relative">
                            <div class="testimonial-header">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Ayşe Yılmaz"
                                    class="testimonial-avatar">
                                <div class="testimonial-info">
                                    <h5>Ayşe Yılmaz</h5>
                                    <div class="testimonial-location">Ankara, Türkiye</div>
                                </div>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                        class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </div>
                            <p class="testimonial-text">"AI önerileri sayesinde buzdolabımdaki malzemeleri değerlendirmeyi öğrendim.
                                Harika bir platform!"</p>
                            <i class="fas fa-quote-right testimonial-quote"></i>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="testimonial-card position-relative">
                            <div class="testimonial-header">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Mehmet Kaya" class="testimonial-avatar">
                                <div class="testimonial-info">
                                    <h5>Mehmet Kaya</h5>
                                    <div class="testimonial-location">İstanbul, Türkiye</div>
                                </div>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                        class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                            </div>
                            <p class="testimonial-text">"Kamera özelliği inanılmaz! Buzdolabımı fotoğrafladım ve hemen tarifler
                                çıktı."</p>
                            <i class="fas fa-quote-right testimonial-quote"></i>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="testimonial-card position-relative">
                            <div class="testimonial-header">
                                <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Zeynep Demir"
                                    class="testimonial-avatar">
                                <div class="testimonial-info">
                                    <h5>Zeynep Demir</h5>
                                    <div class="testimonial-location">İzmir, Türkiye</div>
                                </div>
                                <div class="testimonial-rating">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                                        class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <p class="testimonial-text">"Sağlık takibi ve beslenme önerileri gerçekten işe yarıyor."</p>
                            <i class="fas fa-quote-right testimonial-quote"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA — KAYIT ÇAĞRISI (Call to Action)
             Koyu siyah arka plan (#0a0a0a) üzerinde altın parlama efekti (.cta-bg-glow).
             "Mutfak Devrimine Katılın" başlığı — "Devrimine" kelimesi altın gradyan.
             İki buton:
               .btn-cta-primary → "Ücretsiz Kayıt Ol" (register.php) — altın, gölgeli
               .btn-cta-outline → "Giriş Yap" (login.php) — saydam çerçeveli
             .cta-pill etiketleri → "%100 Ücretsiz" ve "2DK Kayıt Süresi" bilgi rozeti.
             Light modda arka plan krem rengine döner (dark tema zorunu yok).
        -->
        <section class="cta-section-ultimate">
            <div class="cta-bg-glow"></div>
            <div class="container">
                <div class="cta-content text-center">
                    <span class="cta-mini-badge">BAŞLA</span>

                    <h2 class="cta-title-ultimate">
                        Mutfak <span>Devrimine</span> Katılın
                    </h2>

                    <p class="cta-subtitle-ultimate">
                        Yapay zeka destekli pişirme deneyimini yaşamak için
                        hemen ücretsiz kayıt olun.
                    </p>

                    <div class="cta-buttons-ultimate">
                        <a href="register" class="btn-cta-primary">
                            <i class="fas fa-bolt"></i> Ücretsiz Kayıt Ol
                        </a>

                        <a href="login" class="btn-cta-outline">
                            Giriş Yap
                        </a>
                    </div>

                    <div class="cta-feature-row">
                        <div class="cta-pill">
                            <i class="fas fa-gift"></i>
                            <span>%100 Ücretsiz</span>
                        </div>

                        <div class="cta-pill">
                            <i class="fas fa-clock"></i>
                            <span>2DK Kayıt Süresi</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- CTA → GALERİ BÖLÜCÜ -->
        <div class="gold-divider"></div>

        <!-- LEZZET GALERİSİ (Sonsuz Kayan Fotoğraflar)
             .scroller-wrap overflow:hidden ile görünür alanı kırpar.
             .scroller-content CSS @keyframes marquee ile soldan sağa 22 saniyede döner.
             4 fotoğraf, sonsuz döngü için iki kez tekrar edilmiştir (Duplicate).
             Fotoğraflar Unsplash'tan yüklenir: pizza, pankek, salata, makarna.
             prefers-reduced-motion medya sorgusu → animasyon devre dışı bırakılır.
        -->
        <section class="scroller-section py-5">
            <div class="container">
                <div class="text-center mb-5 with-divider">
                    <span class="section-badge">Galeri</span>
                    <h2 class="section-big-title">Lezzet Galerisi</h2>
                    <div class="gold-divider short thin"></div>
                </div>
            </div>

            <div class="scroller-wrap">
                <div class="scroller-content">
                    <div class="scroller-item"><img
                            src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?q=80&w=400&auto=format&fit=crop"
                            alt="Pizza"></div>
                    <div class="scroller-item"><img
                            src="https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?q=80&w=400&auto=format&fit=crop"
                            alt="Pankek"></div>
                    <div class="scroller-item"><img
                            src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?q=80&w=400&auto=format&fit=crop"
                            alt="Salata"></div>
                    <div class="scroller-item"><img
                            src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=400&auto=format&fit=crop"
                            alt="Makarna"></div>

                    <!-- Duplicate -->
                    <div class="scroller-item"><img
                            src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?q=80&w=400&auto=format&fit=crop"
                            alt="Pizza"></div>
                    <div class="scroller-item"><img
                            src="https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?q=80&w=400&auto=format&fit=crop"
                            alt="Pankek"></div>
                    <div class="scroller-item"><img
                            src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?q=80&w=400&auto=format&fit=crop"
                            alt="Salata"></div>
                    <div class="scroller-item"><img
                            src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=400&auto=format&fit=crop"
                            alt="Makarna"></div>
                </div>
            </div>
        </section>

        <!-- BÜLTEN ABONELİĞİ
             E-posta adresi girilen basit form.
             #newsletter-btn → Şu an backend bağlantısı yok; ileride api.php'ye eklenebilir.
             Giriş kutusu ve buton Bootstrap input-group ile tek satırda birleştirilmiştir.
        -->
        <section class="newsletter py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="section-big-title">Bültenimize Abone Olun</h2>
                        <p class="section-desc mb-4" style="color: var(--muted);">En yeni tarifler ve mutfak ipuçları için e-posta
                            adresinizi bırakın.</p>
                        <div class="input-group input-group-lg mb-3" style="max-width: 600px; margin: 0 auto;">
                            <input type="email" class="form-control" placeholder="E-posta adresiniz" id="newsletter-email"
                                style="border-radius: 50px 0 0 50px; border:1px solid var(--border);" />
                            <button class="btn btn-emerald px-4" type="button" id="newsletter-btn"
                                style="border-radius: 0 50px 50px 0;">Abone Ol</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================
             AI MALZEMETANIMAVEBULMASEKSİYONU
             Kullanıcı fotoğraf yükler → predict_upload.php → Python YOLO
             → malzeme listesine eklenir → "Tarif Bul" → api.php → tarifler gösterilir.
        ============================================================ -->
        <div id="aiSectionDivider" class="gold-divider" style="display:none;"></div>

        <section id="ai-ingredient-detection" class="ai-detect-section py-5" style="scroll-margin-top:80px; display:none;">
        <span id="heroAiDetectSection"></span>
            <div class="container">
                <div class="text-center mb-5 with-divider">
                    <span class="section-badge"><i class="fas fa-camera me-1"></i> AI Tanıma</span>
                    <h2 class="section-big-title">Malzemeyi Tara, Tarifi Bul</h2>
                    <p class="section-desc" style="color:var(--muted);">Fotoğraf yükle, yapay zeka malzemeyi tanısın. Listeyi oluştur ve tarifleri keşfet.</p>
                    <div class="gold-divider short thin"></div>
                </div>

                <div class="row g-4 justify-content-center">

                    <!-- SOL KART: Görsel Yükleme + Tanıma -->
                    <div class="col-lg-5">
                        <div class="ai-detect-card">
                            <div class="ai-detect-card-header">
                                <i class="fas fa-camera me-2"></i>Görsel Yükle veya Kamerayla Çek
                            </div>
                            <div class="ai-detect-card-body">

                                <!-- Kaynak seçim sekmeleri -->
                                <div class="ai-source-tabs">
                                    <button class="ai-source-tab active" id="aiTabFile" type="button">
                                        <i class="fas fa-folder-open me-1"></i>Galeriden Seç
                                    </button>
                                    <button class="ai-source-tab" id="aiTabCamera" type="button">
                                        <i class="fas fa-camera me-1"></i>Kamerayı Aç
                                    </button>
                                </div>

                                <!-- GALERİ ALANI -->
                                <div id="aiFileArea">
                                    <label class="ai-upload-label" id="aiUploadLabel" for="aiImageInput">
                                        <i class="fas fa-image ai-upload-icon"></i>
                                        <span id="aiUploadText">Resim seçmek için tıkla veya sürükle</span>
                                    </label>
                                    <input type="file" id="aiImageInput" accept="image/*" style="display:none;">
                                </div>

                                <!-- CANLI KAMERA ALANI -->
                                <div id="aiCameraArea" style="display:none;">
                                    <div class="ai-camera-wrap">
                                        <video id="aiCameraStream" autoplay playsinline class="ai-camera-video"></video>
                                        <canvas id="aiCameraCanvas" style="display:none;"></canvas>
                                        <!-- Kamera durumu -->
                                        <div id="aiCameraStatus" class="ai-camera-status">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Kamera başlatılıyor...
                                        </div>
                                    </div>
                                    <!-- Kamerayı durdur -->
                                    <button id="aiStopCameraBtn" class="btn-ai-ghost w-100 mt-2" type="button" style="display:none;">
                                        <i class="fas fa-stop-circle me-1"></i>Kamerayı Durdur
                                    </button>
                                </div>

                                <!-- Resim önizleme (her iki kaynak için ortak) -->
                                <div id="aiPreviewWrap" class="ai-preview-wrap d-none">
                                    <img id="aiPreviewImg" src="" alt="Önizleme" class="ai-preview-img">
                                    <button id="aiClearImg" class="ai-clear-btn" type="button" title="Resmi temizle">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Tara / Fotoğrafı Çek butonu -->
                                <button id="aiScanBtn" class="btn-ai-primary w-100 mt-3" type="button" disabled>
                                    <i class="fas fa-search me-2"></i>Malzemeyi Tara
                                </button>

                                <!-- Durum / yükleniyor -->
                                <div id="aiStatusBox" class="ai-status-box d-none"></div>

                                <!-- Algılanan malzeme sonucu (otomatik listeye eklenir) -->
                                <div id="aiResultBox" class="ai-result-box d-none">
                                    <div class="ai-result-label">Algılanan Malzeme</div>
                                    <div class="ai-result-value" id="aiResultValue">—</div>
                                    <div class="ai-result-confidence" id="aiResultConf"></div>
                                    <div class="ai-result-auto-added" style="font-size:0.8rem;color:#22a55a;font-weight:700;margin-top:6px;">
                                        <i class="fas fa-check-circle me-1"></i>Listeye otomatik eklendi
                                    </div>
                                    <!-- Otomatik ekleme aktif; manuel buton gizlendi -->
                                    <button id="aiAddToListBtn" class="btn-ai-secondary mt-2" type="button" style="display:none;" aria-hidden="true">
                                        <i class="fas fa-plus me-1"></i>Listeye Ekle
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- SAĞ KART: Malzeme Listesi (Hero ile ortak) -->
                    <div class="col-lg-5">
                        <div class="ai-detect-card">
                            <div class="ai-detect-card-header">
                                <i class="fas fa-list-ul me-2"></i>Malzeme Listesi
                            </div>
                            <div class="ai-detect-card-body">

                                <!-- Bilgi notu -->
                                <div style="font-size:0.8rem;color:var(--muted);margin-bottom:12px;display:flex;align-items:center;gap:7px;">
                                    <i class="fas fa-info-circle" style="color:#cfae55;"></i>
                                    Taradığınız malzemeler aşağıya ve yukarıdaki arama çubuğuna otomatik eklenir.
                                </div>

                                <!-- Chip listesi — hero chips ile senkronize -->
                                <div id="aiIngredientList" class="ai-chip-container">
                                    <p class="ai-empty-msg" id="aiEmptyMsg">Henüz malzeme eklenmedi.<br>
                                        <span style="font-size:0.78rem;">Soldaki karttan tarama yapın veya yukarıdaki arama çubuğunu kullanın.</span>
                                    </p>
                                </div>

                                <!-- Listeyi temizle -->
                                <button id="aiClearListBtn" class="btn-ai-ghost mt-2 d-none" type="button">
                                    <i class="fas fa-trash me-1"></i>Listeyi Temizle
                                </button>

                                <!-- Buzdolabına Ekle butonu -->
                                <button id="aiFindRecipesBtn" class="btn-ai-primary w-100 mt-3" type="button" disabled>
                                    <i class="fas fa-snowflake me-2"></i>Buzdolabına Ekle
                                </button>

                                <!-- Tarif arama durumu -->
                                <div id="aiRecipeStatus" class="ai-status-box d-none"></div>

                            </div>
                        </div>
                    </div>

                </div><!-- /row -->

                <!-- TARİF SONUÇLARI -->
                <div id="aiRecipeResults" class="ai-recipe-results mt-5 d-none">
                    <div class="text-center mb-4">
                        <h4 class="ai-results-title">
                            <i class="fas fa-utensils me-2" style="color:var(--gold)"></i>Tarif Sonuçları
                        </h4>
                    </div>
                    <div id="aiRecipeGrid" class="row g-4"></div>
                </div>

            </div>
        </section>

        <div class="gold-divider"></div>

        <!-- FOOTER
             Koyu (#0f1115) arka planlı dört sütunlu düzen (Bootstrap col-lg).
             Sütunlar: Marka açıklaması + sosyal medya ikonları, Hızlı Linkler,
             Yardım linkleri (SSS/İletişim/Gizlilik), İletişim bilgileri.
             .social-links → Facebook, Twitter, Instagram, YouTube ikonları;
             hover'da ikon kıremi altın arka plan alır.
             .footer-bottom → Telif hakkı satırı, üstte ince beyaz çizgiyle ayrılmış.
        -->
        <footer>
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <h5>ChefMate.AI</h5>
                        <p style="color: rgba(255,255,255,0.6);">Evdeki malzemelerle lezzetli yemekler yapmanızı sağlayan yapay zeka
                            destekli tarif platformu.</p>
                        <div class="social-links mt-3">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <h5>Hızlı Linkler</h5>
                        <ul>
                            <li><a href=".">Ana Sayfa</a></li>
                            <li><a href="world.php">Dünya Yemekleri</a></li>
                            <li><a href="categories.php">Kategoriler</a></li>
                            <li><a href="about">Hakkımızda</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <h5>Yardım</h5>
                        <ul>
                            <li><a href="#">SSS</a></li>
                            <li><a href="#">İletişim</a></li>
                            <li><a href="#">Gizlilik Politikası</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h5>Bize Ulaşın</h5>
                        <p><i class="fas fa-envelope me-2"></i> info@chefmate.ai</p>
                        <p><i class="fas fa-phone me-2"></i> +90 (555) 123 45 67</p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2025 ChefMate.AI. Tüm hakları saklıdır.</p>
                </div>
            </div>
        </footer>
    </main>

    <!-- KAMERA MODALI (Bootstrap Modal)
         #camera-btn'e tıklanınca data-bs-target ile açılır.
         shown.bs.modal → navigator.mediaDevices.getUserMedia({ video: environment })
           ile arka kamerayı açar; canlı görüntüyü <video> elementine bağlar.
         hidden.bs.modal → Tüm kamera track'leri durdurulur (bellek/pil tasarrufu).
         #capture-btn → Fotoğrafı çeker; 450ms sonra demo olarak rastgele bir
           gıda ismi (Domates, Biber vb.) chip olarak hero arama çubuğuna ekler.
         NOT: Gerçek AI görsel tanıma için bu demo kısmı api.php'ye bağlanabilir.
    -->

    <!-- Harici JavaScript Kütüphaneleri -->
    <!-- Bootstrap 5.3: Modal, collapse, dropdown bileşenleri için (bundle = Popper dahil) -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"
      onerror="window._confettiFallback=true;"></script>

    <script src="assets/js/home.js"></script>

</body>

</html>
