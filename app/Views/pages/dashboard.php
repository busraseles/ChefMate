<?php

?>
<!doctype html>
<html lang="tr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ChefMate AI | Dashboard</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Playfair+Display:wght@600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <link rel="stylesheet" href="assets/css/dashboard_inline.css" />
</head>
<body>
<body>

  <!-- ═══════════════════════════════════════════════════════════
       SIDEBAR — Sol kenar çubuğu; tüm sayfalara navigasyon sağlar.
       ─────────────────────────────────────────────────────────────
       Nasıl çalışır:
         • Her .nav-item[data-page="..."] bir "sayfaya" karşılık gelir.
         • Tıklanınca setActivePage(pageId) tetiklenir → JS ilgili
           <section id="pageId"> elemanına .active ekler, diğerlerinden kaldırır.
         • Aktif nav-item altın renk alır (dashboard.css .nav-item.active).
       Alt bölümler:
         • sidebar-brand  → Logo ve site adı
         • nav-links      → Tüm sayfa bağlantıları (JS ile yönetilir)
         • sidebar-footer → Kullanıcı adı, avatar, tema toggle butonu
  ═══════════════════════════════════════════════════════════ -->
  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo">🥗</div>
      <div class="sidebar-brand-text"><b>ChefMate AI</b></div>
    </div>
    <nav class="nav-links" id="navLinks">
      <!-- Her nav-item'in data-page değeri, HTML'deki <section id="..."> ile eşleşir. -->
      <!-- "active" class'ı ilk açılışta Genel Bakış'ta başlar; JS ile değişir. -->
      <div class="nav-item active" data-page="page-overview">🏠 Genel Bakış</div>
      <div class="nav-item" data-page="page-tracker">💧 Su + Vücut</div>          <!-- Su takibi + BMR/VKİ analizi -->
      <div class="nav-item" data-page="page-health">🩺 Sağlık Önerileri</div>    <!-- HSGM tabanlı rehber -->
      <div class="nav-item" data-page="page-recipes">🌿 Sağlıklı Tarifler ve İpuçları</div> <!-- Blog ızgarası -->
      <div class="nav-item" data-page="page-badges">🏆 Rozetlerim</div>           <!-- Rozet koleksiyonu -->
      <div class="nav-item" data-page="page-digital-cabinet">❄️ Dijital Buzdolabım</div> <!-- fridge.html iframe -->
      <div class="nav-item" data-page="page-daily-menu">📅 Günün Menüsü</div>    <!-- AI tarif önerisi + öğün takibi -->
      <div class="nav-item" data-page="page-recipe-book">📖 Tarif Defterim</div>  <!-- Kullanıcının kaydettiği tarifler -->
      <div class="nav-item" data-page="page-my-likes">❤️ Beğenilerim</div>        <!-- Beğenilen tarifler -->
      <div class="nav-item" data-page="page-my-comments">💬 Yorumlarım</div>      <!-- Kullanıcı yorumları -->
      <div class="nav-item" data-page="page-shopping-list">🛒 Alışveriş Listesi</div> <!-- Checkbox'lı alışveriş -->
      <div class="nav-item" data-page="page-waste">♻️ Atık Yönetimi</div>         <!-- Gıda israfı takibi -->
      <div class="nav-item" data-page="page-timer">⏱️ Zamanlayıcı</div>           <!-- Pişirme geri sayımı -->
      <div class="nav-item" data-page="page-food-info">🥑 Gıda Bilgisi</div>      <!-- Besin değeri arama -->
      <div class="nav-item" data-page="page-insights">📊 İçgörüler</div>          <!-- Chart.js grafikleri -->
      <div class="nav-item" data-page="page-settings">⚙️ Ayarlar & Profil</div>   <!-- Vücut profili + şifre -->
      <div class="nav-item logout-btn" onclick="window.location='logout'">🚪 Çıkış Yap</div> <!-- public/logout'a yönlendirir -->
    </nav>
    <div class="sidebar-footer">
      <div class="user-mini">
        <img id="sidebarAvatarImg"
          src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' fill='%23334155' rx='16'/><text x='50%' y='54%' font-size='16' text-anchor='middle' dominant-baseline='middle' fill='%23cfae55'>👤</text></svg>"
          style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid rgba(207,174,85,.4);flex-shrink:0;cursor:pointer;"
          onclick="document.querySelector('.nav-item[data-page=page-settings]')?.click()"
          title="Profil Ayarları"
          onerror="this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\'><rect width=\'32\' height=\'32\' fill=\'%23334155\' rx=\'16\'/><text x=\'50%25\' y=\'54%25\' font-size=\'16\' text-anchor=\'middle\' dominant-baseline=\'middle\' fill=\'%23cfae55\'>👤</text></svg>'">
        <div class="user-mini-info">
          <div id="sidebarUserName"><?= $name ?></div>
          <div>Üye</div>
        </div>
      </div>
      <button class="theme-toggle-mini" id="themeToggleSidebar">🌙 Tema Değiştir</button>
    </div>
  </aside>

  <!-- ═══════════════════════════════════════════════════════════
       ANA WRAPPER — Sidebar dışındaki tüm içeriği sarar.
       ─────────────────────────────────────────────────────────────
       • header     → Üst bar: "Merhaba [ad]" pill, Ana Sayfa + Tarifler butonları,
                      bildirim zili (#notifBtn). 60px scroll sonrası glassmorphism alır.
       • main.wrap  → Tüm .page <section>'larını barındırır; aktif olan .active alır.
       • Mobilde hamburger (☰) sidebar'ı açar/kapatır.
  ═══════════════════════════════════════════════════════════ -->
  <!-- ANA WRAPPER -->
  <div class="main-wrapper">
    <header>
      <div class="header-left">
        <button class="mobile-menu-btn" id="menuToggle">☰</button>
        <span class="pill" id="helloPill">✨ Merhaba, <?= $name ?>!</span>
      </div>
      <div class="header-actions">
        <a href="." class="btn" style="text-decoration:none;">🏠 Ana Sayfaya Dön</a>
        <button class="btn gold" id="btnGoRecipes" onclick="window.location.href='tarifler'">🍽️ Tariflere Git</button>
        <button class="notif-btn" id="notifBtn" title="Bildirimler">🔔<span class="notif-badge" id="notifBadge">0</span></button>
      </div>
    </header>
    <!-- BİLDİRİM PANELİ
         Zil butonuna tıklanınca açılır/kapanır (toggle).
         api.php → notifications_sync_fridge ile SKT yaklaşan malzemeler otomatik bildirim oluşturur.
         Okunmamış bildirim sayısı kırmızı rozette gösterilir (#notifBadge).
    -->
    <div class="notif-panel" id="notifPanel">
      <div class="notif-panel-hd">
        <h4>🔔 Bildirimler</h4>
        <button class="btn-sm btn-gray" id="btnReadAll" style="font-size:.75rem;padding:4px 10px;">Tümünü Okundu İşaretle</button>
      </div>
      <div class="notif-panel-body" id="notifList">
        <div class="notif-empty">Bildirim yok.</div>
      </div>
    </div>

    <div class="confetti" id="confetti"></div>

    <main class="wrap">

      <!-- ═══════════════════════════════════════ GENEL BAKIŞ
           İlk açılan sayfadır (.active class'ı ile başlar).
           5 istatistik kartı: Su, Dolap, Tarif, Rozet, Alışveriş — PHP'den beslenir.
           Hero banner + motivasyon metni ve hızlı erişim butonları içerir.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page active" id="page-overview">
        <section class="hero-aurum">
          <div class="hero-aurum-inner">
            <h1 class="hero-title">AURA OF HEALTH</h1>
            <p class="hero-subtitle">HSGM BİLİMSEL REHBERİ</p>
            <div class="luxury-line"></div>
            <div class="hero-cta-row">
              <button class="btn gold" data-goto="page-tracker">✨ Deneyimi Başlat</button>
              <button class="btn" data-goto="page-health">🩺 Rehbere Git</button>
              <button class="btn" onclick="window.location.href='tarifler'">🍽️ Tariflere Bak</button>
            </div>
          </div>
        </section>
        <!-- istatistik kartları -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin:24px 0;">
          <div class="panel" style="text-align:center;padding:16px;">
            <div style="font-size:2rem;">💧</div>
            <div style="font-size:1.4rem;font-weight:800;" id="statWater"><?= number_format($waterToday / 1000, 1) ?> L</div>
            <div style="font-size:.78rem;color:var(--muted);">Bugünkü Su</div>
          </div>
          <div class="panel" style="text-align:center;padding:16px;">
            <div style="font-size:2rem;">🧊</div>
            <div style="font-size:1.4rem;font-weight:800;" id="statCabinet"><?= $cabinetCount ?></div>
            <div style="font-size:.78rem;color:var(--muted);">Dolap Malzeme</div>
          </div>
          <div class="panel" style="text-align:center;padding:16px;">
            <div style="font-size:2rem;">📖</div>
            <div style="font-size:1.4rem;font-weight:800;" id="statRecipes"><?= $recipeCount ?></div>
            <div style="font-size:.78rem;color:var(--muted);">Kayıtlı Tarif</div>
          </div>
          <div class="panel" style="text-align:center;padding:16px;">
            <div style="font-size:2rem;">🏆</div>
            <div style="font-size:1.4rem;font-weight:800;" id="statBadges"><?= $badgeCount ?></div>
            <div style="font-size:.78rem;color:var(--muted);">Rozet</div>
          </div>
          <div class="panel" style="text-align:center;padding:16px;">
            <div style="font-size:2rem;">🛒</div>
            <div style="font-size:1.4rem;font-weight:800;" id="statShop"><?= $shopCount ?></div>
            <div style="font-size:.78rem;color:var(--muted);">Alışveriş Kalemi</div>
          </div>
        </div>

        <section class="hero">
          <div class="heroLeft">
            <span class="pill" style="width:fit-content;margin-bottom:12px;">Sağlıklı beslenme 💛</span>
            <h1>Küçük adımlar büyük fark eder</h1>
            <p>Su, tabak dengesi, sebze-meyve ve pratik tarifler. Hedeflerine ulaşarak rozetler kazanabilirsin! 🎉</p>
          </div>
          <div class="heroRight">
            <div class="heroBadge">🌿 Bugün küçük bir adım at!</div>
          </div>
        </section>
      </section>

      <!-- ═══════════════════════════════════════ SU + VÜCUT
           Günlük su tüketimini ml olarak takip eder.
           Her "+250ml" butonu api.php → water_add action'ını çağırır, veritabanına kaydeder.
           Vücut Analizi paneli: kilo/boy/yaş'a göre BMR, TDEE, VKİ hesaplar.
           Günlük görevler (checkbox): kahvaltı, sebze, tuz — tamamlanınca rozet kazandırır.
           Rozet Aktiviteleri: Uyku, Yürüyüş, Meyve butonları günlük rozet tetikler.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-tracker">
        <div class="sectionTitle">
          <h2>Kişisel Panelim (Su + Vücut) ✨</h2>
        </div>
        <section class="trackerRow">
          <div class="panel">
            <div class="panelHd">
              <b>💧 Hidrasyon Asistanı</b>
              <span class="pill" id="waterTargetPill">Hedef: 2000ml</span>
            </div>
            <div class="panelBd">
              <div class="water-glass">
                <div class="water-fill" id="waterFill"></div>
              </div>
              <div class="water-stat"><b id="currentWaterDisplay">0</b> <small>ml</small></div>
              <div class="water-controls">
                <button class="round" id="waterMinus" title="Azalt">−</button>
                <button class="round" id="waterPlus" title="Arttır">+</button>
                <button class="round" id="waterReset" title="Sıfırla" style="font-size:.8rem">↺</button>
              </div>
              <div class="hint" style="text-align:center;margin-top:10px;">Her + düğmesi <b>250ml</b>. DB'ye kaydedilir.</div>
            </div>
          </div>
          <div class="panel">
            <div class="panelHd">
              <b>⚡ Vücut Analizi</b>
              <button class="pill" id="btnOpenProfile" style="cursor:pointer;border:none;">⚙️ Profili Düzenle</button>
            </div>
            <div class="panelBd" id="bodyAnalysisContent">
              <p style="color:var(--muted);text-align:center;padding:20px;">Profilini kur...</p>
            </div>
            <div style="border-top:1px solid var(--line);padding:14px;">
              <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:.9rem;font-weight:900;">
                <span>🏅 Rozet Durumu</span><span id="badgeCount">0/9</span>
              </div>
              <div style="text-align:center;margin-top:12px;">
                <button class="btn" data-goto="page-badges" style="width:100%;justify-content:center;">Tüm Rozetleri Gör 🏆</button>
              </div>
            </div>
          </div>
        </section>
        <section class="panel" style="margin-top:14px">
          <div class="panelHd"><b>✅ Günlük görevler</b><span class="pill" id="taskProgress">0/3</span></div>
          <div class="panelBd">
            <div class="tasks">
              <div class="task"><input type="checkbox" id="t1"><label for="t1"><b>Kahvaltı yaptım</b><small>Basit bir başlangıç bile yeter</small></label></div>
              <div class="task"><input type="checkbox" id="t2"><label for="t2"><b>Bugün sebze/meyve ekledim</b><small>En az 2 kez hedefle</small></label></div>
              <div class="task"><input type="checkbox" id="t3"><label for="t3"><b>Tuzluğu sofraya koymadım</b><small>Baharat/limon daha güzel</small></label></div>
            </div>
          </div>
        </section>
        <section class="panel" style="margin-top:14px">
          <div class="panelHd"><b>🔥 Rozet Aktiviteleri</b><span class="pill">Bugün Ekstra</span></div>
          <div class="panelBd">
            <div class="activity-row">
              <button class="btn" id="btnLogSleep">😴 7+ Saat Uydum</button>
              <button class="btn" id="btnLogMove">🚶 20dk Yürüdüm</button>
              <button class="btn" id="btnLogSnack">🍎 Meyve Seçtim</button>
            </div>
            <div id="activityFeedback"></div>
          </div>
        </section>
      </section>

      <!-- ═══════════════════════════════════════ SAĞLIK REHBERİ
           HSGM (Türkiye Halk Sağlığı Genel Müdürlüğü) kaynaklı bilimsel kartlar.
           Her kart detay butonu ile genişler, HSGM resmi sitesine bağlantı içerir.
           Kart açıldığında "tipsRead" sayacı artar; kart okuyunca 🧠 rozeti kazanılır.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-health">
        <div class="sectionTitle">
          <h2>Resmi Sağlık Rehberi 🩺</h2>
          <p>Türkiye Halk Sağlığı Genel Müdürlüğü (HSGM) kaynaklı bilimsel içerik.</p>
        </div>
        <div style="margin-bottom:24px;display:flex;justify-content:flex-end;">
          <a href="https://hsgm.saglik.gov.tr/tr/saglikli-beslenme-db.html" target="_blank" class="btn primary" style="text-decoration:none;">🌐 HSGM Resmi Veritabanına Git</a>
        </div>
        <section class="tipsGrid" id="tipsGrid"></section>
      </section>

      <!-- ═══════════════════════════════════════ SAĞLIKLI TARİFLER BLOG
           Kategori filtreli blog kartları: Tarif, Beslenme, Gıda Faydaları,
           Bağışıklık, Kilo, Kanser Koruma.
           BLOG_DATA dizisi (dashboard.js içinde) statik içeriği barındırır.
           Her kart genişleyince detay + malzeme + yapılış gösterilir.
           Beğen / Kaydet / Yorum butonları api.php üzerinden veritabanına yazar.
           Yorum Modalı (#commentModal) tüm tarifler için ortaklaşa kullanılır.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-recipes">

        <div class="blog-hero">
          <div class="blog-hero-icon">🌿</div>
          <div class="blog-hero-text">
            <h2>Sağlıklı Tarifler ve İpuçları</h2>
            <p>Tarifler, beslenme önerileri, gıdaların faydaları — aşağı kaydırarak keşfet.</p>
          </div>
        </div>

        <div class="blog-filter-bar" id="blogFilterBar">
          <button class="blog-chip active" data-bcat="all">Tümü</button>
          <button class="blog-chip" data-bcat="tarif">🍽️ Tarifler</button>
          <button class="blog-chip" data-bcat="beslenme">🥗 Beslenme</button>
          <button class="blog-chip" data-bcat="gida">🥑 Gıda Faydaları</button>
          <button class="blog-chip" data-bcat="immun">🛡️ Bağışıklık</button>
          <button class="blog-chip" data-bcat="kilo">⚖️ Kilo</button>
          <button class="blog-chip" data-bcat="kanser">💊 Kanser Koruma</button>
        </div>

        <div class="blog-grid" id="blogGrid"></div>

        <!-- Eski tarif yapıları — gizli, uyumluluk için -->
        <div id="recipeTabAll" style="display:none;"><section id="shotsGrid"></section><div id="recipeGrid"></div></div>
        <div id="recipeTabCommunity" style="display:none;"><div id="communityRecipeGrid"></div><div id="communityLoadMore"></div></div>
        <div style="display:none;"><input id="searchInput"><div id="chips"></div></div>
      </section>

      <!-- Yorum Modalı -->
      <div class="comment-modal" id="commentModal">
        <div class="comment-modal-box">
          <button onclick="document.getElementById('commentModal').classList.remove('open')" style="position:absolute;top:12px;right:16px;font-size:1.3rem;background:none;border:none;cursor:pointer;">×</button>
          <h3 id="commentModalTitle" style="margin-bottom:12px;padding-right:24px;"></h3>
          <div class="db-form-row">
            <textarea id="commentInput" placeholder="Yorumunuzu yazın..." style="min-height:80px;"></textarea>
            <button class="btn-sm btn-gold" id="btnSubmitComment">Gönder</button>
          </div>
          <div class="comment-list" id="commentList"></div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════ ROZETLER
           BADGES_CONFIG dizisindeki 11 rozetin tamamını grid'de gösterir.
           Kilitli rozetler gri/soluk, kazanılmış olanlar altın kenarlıklıdır.
           Günlük rozetler her gün sıfırlanır (badge_reset_daily API çağrısı).
           Rozet kazanılınca konfeti animasyonu ve toast bildirimi tetiklenir.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-badges">
        <div class="sectionTitle">
          <h2>Rozet Koleksiyonum 🏆</h2>
        </div>
        <div class="badge-grid-full" id="badgesFullGrid"></div>
      </section>

      <!-- ═══════════════════════════════════════ DİJİTAL BUZDOLABIM
           fridge.html dosyasını tam ekran iframe ile gömülü gösterir.
           fridge.html: 3D animasyonlu buzdolabı arayüzü (raf bazlı, SKT uyarısı, sıcaklık).
           Veriler api.php → fridge_list / fridge_add / fridge_delete ile yönetilir.
           Buradaki malzeme listesi aynı zamanda AI tarif önerileri için kullanılır.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-digital-cabinet">
        <div class="sectionTitle">
          <h2>Dijital Buzdolabım ❄️</h2>
          <p>3D buzdolabı ile malzemelerinizi yönetin. Veriler veritabanına kaydedilir.</p>
        </div>
        <div style="position:relative;width:100%;height:calc(100vh - 160px);border-radius:16px;overflow:hidden;border:1px solid var(--line,#ddd);">
          <iframe src="fridge" style="width:100%;height:100%;border:none;" id="fridgeIframe" title="Dijital Buzdolabı"></iframe>
        </div>
      </section>

      <!-- ═══════════════════════════════════════ GÜNÜN MENÜSÜ
           Üç bölümden oluşur:
           1) AI Tarif Önerileri: Buzdolabındaki malzemelere göre eşleşen tarifler
              (api.php → ai_recipe_recommendations, uyum yüzdesi hesaplanır).
           2) Öğün Ekle formu: Gıda veritabanından arama + kalori otomatik doldurma.
              Seçilen gıdanın makro değerleri (protein/karbonhidrat/yağ) de kaydedilir.
           3) Bugünkü Menüm: O gün eklenen tüm öğünlerin özeti, toplam kalori çubuğu.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-daily-menu">
        <div class="sectionTitle">
          <h2>Günün Menüsü 📅</h2>
        </div>

        <!-- ═══ AI TARİF ÖNERİLERİ — TAM SÜRÜM ═══ -->

        <div class="panel" id="aiRecPanel" style="margin-bottom:16px;border:2px solid rgba(207,174,85,.45);">
          <div class="panelHd" style="background:linear-gradient(135deg,rgba(207,174,85,.1),transparent);">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex:1;">
              <b>Tarif Önerileri</b>
              <span style="background:linear-gradient(135deg,#cfae55,#b8963e);color:#1a1a1a;
                           font-size:.68rem;font-weight:800;padding:2px 9px;border-radius:20px;"></span>
              <span id="aiRecMeta" style="font-size:.74rem;color:var(--muted);"></span>
            </div>
            <button id="btnAiRefresh"
              onclick="loadAiRecipes(true)"
              style="padding:6px 14px;border-radius:8px;border:1px solid rgba(207,174,85,.35);
                     background:transparent;color:#cfae55;font-size:.8rem;font-weight:600;
                     cursor:pointer;display:flex;align-items:center;gap:5px;">
              <span id="aiRefreshIco">🔄</span> Yenile
            </button>
          </div>
          <div class="panelBd">
            <div class="ai-rec-grid" id="aiRecGrid">
              <!-- skeleton -->
              <?php for($i=0;$i<3;$i++): ?>
              <div class="ai-card">
                <div class="ai-card-thumb ai-skel"></div>
                <div class="ai-card-body" style="gap:9px;">
                  <div class="ai-skel" style="height:16px;width:70%"></div>
                  <div class="ai-skel" style="height:5px"></div>
                  <div class="ai-skel" style="height:12px;width:88%"></div>
                  <div class="ai-skel" style="height:12px;width:55%"></div>
                </div>
              </div>
              <?php endfor; ?>
            </div>
          </div>
        </div>

        <div class="panel" style="margin-bottom:16px;border:1.5px solid rgba(207,174,85,.25);border-radius:20px;overflow:hidden;">
          <div class="panelHd" style="background:linear-gradient(135deg,rgba(207,174,85,.15),rgba(249,115,22,.08));border-bottom:1px solid rgba(207,174,85,.2);padding:14px 18px;">
            <b style="font-size:1rem;">➕ Öğün Ekle</b>
            <span style="font-size:.75rem;color:var(--muted);margin-left:8px;">Gıdayı seçin, kalori otomatik gelir</span>
          </div>
          <div class="panelBd" style="padding:18px;">

            <!-- Satır 1: Tarih + Öğün Tipi -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
              <div>
                <label style="font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:5px;">📅 Tarih</label>
                <input type="date" id="menuDate" value="<?= date('Y-m-d') ?>" style="width:100%;padding:10px 12px;border-radius:10px;border:1.5px solid var(--line,#ddd);background:var(--surface,#fff);color:var(--ink,#222);font-size:.9rem;box-sizing:border-box;">
              </div>
              <div>
                <label style="font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:5px;">🍽️ Öğün</label>
                <select id="menuType" style="width:100%;padding:10px 12px;border-radius:10px;border:1.5px solid var(--line,#ddd);background:var(--surface,#fff);color:var(--ink,#222);font-size:.9rem;box-sizing:border-box;">
                  <option value="kahvaltı">☀️ Kahvaltı</option>
                  <option value="öğle">🌤️ Öğle</option>
                  <option value="akşam">🌙 Akşam</option>
                  <option value="atıştırmalık">🍎 Atıştırmalık</option>
                </select>
              </div>
            </div>

            <!-- Gıda Arama Kutusu -->
            <div style="margin-bottom:10px;">
              <label style="font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:5px;">🔍 Gıda Ara</label>
              <div style="position:relative;">
                <input type="text" id="foodSearchInput" placeholder="Örn: tavuk, yoğurt, elma..." autocomplete="off"
                  oninput="filterFoodList(this.value)"
                  style="width:100%;padding:11px 14px;border-radius:12px;border:1.5px solid rgba(207,174,85,.4);background:var(--surface,#fff);color:var(--ink,#222);font-size:.92rem;box-sizing:border-box;outline:none;transition:border-color .2s;"
                  onfocus="this.style.borderColor='rgba(207,174,85,.8)';_positionFoodDropdown();showFoodDropdown()"
                  onblur="setTimeout(hideFoodDropdown,200)">
                <span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:1.1rem;pointer-events:none;">🥘</span>
                <!-- Dropdown: body'de fixed konumlanır, overflow:hidden'dan etkilenmez -->
              </div>
            </div>

            <!-- Seçili Gıda Kartı -->
            <div id="foodSelCard" style="display:none;margin-bottom:14px;padding:14px 16px;
              background:linear-gradient(135deg,rgba(207,174,85,.12),rgba(249,115,22,.06));
              border:1.5px solid rgba(207,174,85,.4);border-radius:14px;
              align-items:center;gap:14px;flex-wrap:wrap;">
              <div id="foodSelEmoji" style="font-size:2.6rem;line-height:1;flex-shrink:0;"></div>
              <div style="flex:1;min-width:0;">
                <div id="foodSelName" style="font-size:1.05rem;font-weight:700;color:var(--ink);"></div>
                <div id="foodSelDetail" style="font-size:.8rem;color:var(--muted);margin-top:3px;line-height:1.5;"></div>
              </div>
              <div style="text-align:center;background:rgba(249,115,22,.14);border-radius:12px;padding:10px 16px;flex-shrink:0;">
                <div id="foodSelKcal" style="font-size:1.5rem;font-weight:800;color:#f97316;line-height:1;"></div>
                <div style="font-size:.68rem;color:var(--muted);margin-top:3px;font-weight:600;">kcal</div>
              </div>
              <button onclick="clearFoodSel()" style="background:rgba(0,0,0,.06);border:none;color:var(--muted);cursor:pointer;font-size:1rem;padding:6px 8px;border-radius:8px;flex-shrink:0;" title="Temizle">✕</button>
            </div>

            <!-- Öğün Açıklaması + Kalori + Butonlar -->
            <div style="display:grid;grid-template-columns:1fr 130px auto auto;gap:8px;align-items:flex-end;">
              <div>
                <label style="font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:5px;">📝 Açıklama</label>
                <input type="text" id="menuDesc" placeholder="Öğün adı (otomatik veya manuel)" style="width:100%;padding:10px 12px;border-radius:10px;border:1.5px solid var(--line,#ddd);background:var(--surface,#fff);color:var(--ink,#222);font-size:.9rem;box-sizing:border-box;">
              </div>
              <div>
                <label style="font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:5px;">🔥 kcal</label>
                <input type="number" id="menuCal" placeholder="0" min="0"
                  style="width:100%;padding:10px 12px;border-radius:10px;border:1.5px solid rgba(249,115,22,.35);
                  background:rgba(249,115,22,.06);color:#f97316;font-weight:700;font-size:.95rem;box-sizing:border-box;">
              </div>
              <button onclick="clearFoodSel()" class="btn-sm btn-gray" title="Temizle" style="align-self:flex-end;padding:10px 12px;">✕</button>
              <button class="btn-sm btn-gold" id="btnMenuAdd" style="align-self:flex-end;padding:10px 18px;font-size:.92rem;">➕ Ekle</button>
            </div>

          </div>
        </div>
        <div class="panel">
          <div class="panelHd"><b>📅 Bugünkü Menüm</b></div>
          <div class="panelBd">
            <div class="menu-day-grid" id="menuDayGrid"></div>
          </div>
        </div>
      </section>

      <!-- ═══════════════════════════════════════ TARİF DEFTERİM
           Kullanıcının kendi eklediği tarifleri listeler.
           Tarif eklerken: resim dosyası yükleme VEYA URL ile görsel ekleme desteklenir.
           Detay modalı: malzemeler + yapılış adımları + yazdır butonu.
           "Kitap Görünümünde Aç" → tarif_defteri.php iframe ile açılır.
           Tarifler sayfasından "Kaydet" butonu da buraya yazar (recipe_save_interaction).
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-recipe-book">
        <div class="sectionTitle">
          <h2>Tarif Defterim 📖</h2>
          <p>Kendi tariflerini kaydet, düzenle, yazdır. Tarif sayfasından kaydettiğin tarifler burada görünür.</p>
        </div>
        <!-- KİTAP GÖRÜNÜMÜ BUTONU -->
        <div style="margin-bottom:16px;">
          <button class="btn-sm btn-gold" onclick="openBookView()" style="padding:10px 20px;font-size:.9rem;">📖 Kitap Görünümünde Aç</button>
          <a href="tarifler" class="btn-sm btn-gray" style="padding:10px 20px;font-size:.9rem;text-decoration:none;display:inline-block;margin-left:8px;">🍽️ Tariflere Git & Kaydet</a>
        </div>
        <!-- TARİF LİSTESİ -->
        <div class="panel">
          <div class="panelHd">
            <b>📚 Tarif Koleksiyonum</b>
            <span class="pill" id="rbCountPill">0 tarif</span>
          </div>
          <div class="panelBd">
            <div class="recipe-book-list" id="recipeBookList"></div>
          </div>
        </div>
        <!-- TARİF DETAY MODAL -->
        <div id="rbModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:9000;align-items:center;justify-content:center;">
          <div style="background:var(--surface,#fff);border-radius:20px;padding:28px;max-width:600px;width:90%;max-height:85vh;overflow-y:auto;position:relative;">
            <button onclick="document.getElementById('rbModal').style.display='none'" style="position:absolute;top:12px;right:16px;font-size:1.4rem;background:none;border:none;cursor:pointer;">×</button>
            <h2 id="rbModalTitle" style="margin-bottom:14px;"></h2>
            <div id="rbModalImgWrap"></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:14px;">
              <div>
                <h4>🧺 Malzemeler</h4>
                <div id="rbModalIng" style="font-size:.9rem;line-height:1.8;"></div>
              </div>
              <div>
                <h4>👩‍🍳 Yapılışı</h4>
                <div id="rbModalSteps" style="font-size:.9rem;line-height:1.8;"></div>
              </div>
            </div>
            <div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap;">
              <button class="btn-sm btn-gold" onclick="window.print()">🖨️ Yazdır</button>
              <button class="btn-sm btn-red" id="rbModalDelete">🗑️ Sil</button>
              <a id="rbModalSource" href="#" target="_blank" rel="noopener" class="btn-sm btn-gray" style="display:none;text-decoration:none;">🔗 Kaynağa Git</a>
            </div>
          </div>
        </div>
        <!-- KİTAP MODAL -->
        <div id="bookModal" style="display:none;position:fixed;inset:0;background:#0f0f0f;z-index:9999;flex-direction:column;align-items:center;justify-content:center;">
          <div id="bookContainer" style="width:100%;height:100%;"></div>
        </div>
      </section>

      <!-- ═══════════════════════════════════════ BEĞENİLERİM
           Kullanıcının ❤️ ile beğendiği tarifleri listeler.
           api.php → my_likes action'ı ile çekilir.
           Kaynak URL varsa "Kaynağa Git" butonu gösterilir.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-my-likes">
        <div class="sectionTitle">
          <h2>Beğenilerim ❤️</h2>
          <p>Beğendiğin tarifler burada listelenir.</p>
        </div>
        <div class="recipe-book-list" id="myLikesList">
          <p style="color:var(--muted);text-align:center;padding:30px;">Yükleniyor...</p>
        </div>
      </section>

      <!-- ═══════════════════════════════════════ YORUMLARIM
           Kullanıcının tüm tarif yorumlarını listeler.
           Her yorum; düzenle (inline textarea) ve sil butonlarına sahiptir.
           api.php → comment_edit / comment_delete action'ları kullanılır.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-my-comments">
        <div class="sectionTitle">
          <h2>Yorumlarım 💬</h2>
          <p>Tariflere yaptığın tüm yorumlar.</p>
        </div>
        <div id="myCommentsList" style="display:flex;flex-direction:column;gap:12px;margin-top:8px;">
          <p style="color:var(--muted);text-align:center;padding:30px;">Yükleniyor...</p>
        </div>
      </section>

      <!-- ═══════════════════════════════════════ ALIŞVERİŞ LİSTESİ
           api.php → shop_list / shop_add / shop_toggle / shop_delete ile çalışır.
           Checkbox ile kalem tamamlandı işaretlenir (is_done=1).
           "Tamamlananları Temizle" yalnızca işaretlenenleri siler.
           "Listeyi Sıfırla" tüm kalemleri temizler (onay gerektirir).
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-shopping-list">
        <div class="sectionTitle">
          <h2>Alışveriş Listesi 🛒</h2>
        </div>
        <div class="panel" style="margin-bottom:16px;">
          <div class="panelHd"><b>➕ Ürün Ekle</b></div>
          <div class="panelBd">
            <div class="db-form-row">
              <input type="text" id="shopNewName" placeholder="Ürün adı">
              <input type="text" id="shopNewQty" placeholder="Miktar (opsiyonel)" style="max-width:160px;">
              <button class="btn-sm btn-gold" id="btnShopAdd">Listeye Ekle</button>
            </div>
            <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;">
              <button class="btn-sm btn-gray" id="btnShopClearDone">🧹 Tamamlananları Temizle</button>
              <button class="btn-sm btn-red" id="btnShopClearAll">🗑️ Listeyi Sıfırla</button>
              <button class="btn-sm btn-gold" id="btnShopPrint">🖨️ Yazdır</button>
            </div>
          </div>
        </div>
        <div class="panel">
          <div class="panelHd"><b>📋 Listen</b><span class="pill" id="shopProgressPill">0/0</span></div>
          <div class="panelBd" id="shopListWrap"></div>
        </div>
      </section>

      <!-- ═══════════════════════════════════════ ATIK YÖNETİMİ
           Leaflet.js ile OpenStreetMap haritası üzerinde atık merkezleri gösterilir.
           GPS konumu alınarak en yakın merkez hesaplanır (Haversine mesafesi).
           Atık kayıtları waste_logs tablosuna yazılır; ilk kayıtta 🏆 rozet kazanılır.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-waste">
        <div class="sectionTitle">
          <h2>Atık Yönetimi ♻️</h2>
          <p>Haritadan en yakın atık merkezini bul ve atıklarını kaydet.</p>
        </div>
        <div class="waste-actions">
          <button class="btn primary" id="btnUseGPS">📍 Şu anki konumumu kullan</button>
          <button class="btn" id="btnGuideClick">🗺️ Haritadan seç</button>
        </div>
        <div id="shiningAddressContainer" class="shining-text"></div>
        <div id="wasteMap" aria-label="Atık merkezleri haritası"></div>
        <!-- Atık kayıt formu -->
        <div class="panel" style="margin-top:16px;">
          <div class="panelHd"><b>🗑️ Atık Kaydı</b></div>
          <div class="panelBd">
            <div class="db-form-row">
              <input type="text" id="wasteItem" placeholder="Ürün adı">
              <input type="text" id="wasteAmount" placeholder="Miktar (örn: 500g)">
              <input type="text" id="wasteReason" placeholder="Sebep (opsiyonel)">
              <button class="btn-sm btn-gold" id="btnWasteAdd">Kaydet</button>
            </div>
          </div>
        </div>
        <div class="waste-log-list" id="wasteLogList"></div>
      </section>

      <!-- ═══════════════════════════════════════ MUTFAK ZAMANLAYICISI
           SVG dairesel progress ring ile görsel geri sayım.
           Preset butonları: 1/5/10/30 dakika hızlı seçim.
           Süre bitince Web Audio API ile sesli alarm ve modal uyarı çıkar.
           TimerModule IIFE (Immediately Invoked Function Expression) olarak yapılandırılmıştır.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-timer">
        <div class="sectionTitle">
          <h2>Mutfak Zamanlayıcısı ⏱️</h2>
        </div>
        <div class="timer-shell">
          <main class="timer-card" id="timerCard">
            <h1>Mutfak Zamanlayıcısı</h1>
            <div class="presets">
              <button class="chip" data-preset="1">1 Dk</button>
              <button class="chip" data-preset="5">5 Dk</button>
              <button class="chip" data-preset="10">10 Dk</button>
              <button class="chip" data-preset="30">30 Dk</button>
            </div>
            <div class="progress-container">
              <svg class="progress-ring" width="260" height="260">
                <circle class="bg-circle" stroke-width="12" fill="transparent" r="110" cx="130" cy="130"></circle>
                <circle id="progress-circle" class="progress-ring__circle" stroke-width="12" fill="transparent" r="110" cx="130" cy="130"></circle>
              </svg>
              <div class="boiling-pot-wrapper" id="potWrapper">
                <div class="pot-emoji">🍲</div>
                <span class="bubble">💨</span><span class="bubble">🫧</span><span class="bubble">💨</span>
              </div>
              <div class="input-group active" id="input-group">
                <input type="number" id="in-min" class="time-input" placeholder="00" min="0" max="99" value="05">
                <span class="separator">:</span>
                <input type="number" id="in-sec" class="time-input" placeholder="00" min="0" max="59" value="00">
              </div>
            </div>
            <div class="timer-info">
              <div class="time-big" id="time-display">00:00</div>
              <div class="status-label" id="status-label">Süreyi ayarla ve başlat</div>
            </div>
            <div class="controls-container">
              <button id="btn-big-start" class="btn btn-primary-start">BAŞLAT</button>
              <div class="icon-controls" id="icon-controls">
                <button id="btn-pause" class="btn btn-icon btn-pause">⏸</button>
                <button id="btn-reset" class="btn btn-icon btn-reset">⏹</button>
              </div>
            </div>
          </main>
        </div>
        <div class="modal-overlay" id="timer-modal">
          <div class="modal-card">
            <span class="modal-icon">🥘</span>
            <h2 style="color:var(--ink);margin-bottom:10px;font-size:1.5rem;">Zaman Doldu!</h2>
            <p style="color:var(--muted);">Yemeğin afiyet olsun!</p>
            <button class="btn-modal-ok" id="timer-modal-ok">Tamamdır</button>
          </div>
        </div>
      </section>

      <!-- ═══════════════════════════════════════ GIDA GÜVENLİĞİ REHBERİ
           Statik veri (arts2 dizisi) ile dört kategoride bilgi kartları:
           ⏰ Bozulma Süreleri / ⚠️ Dikkat Noktaları / ☠️ Zehirleyici Etkiler / 🤧 Alerjiler
           Kart filtresi fi2FilterBar ile çalışır; "Detayları Gör" ile her kart genişler.
           İçerik tamamen istemci taraflı render edilir — API çağrısı yapılmaz.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-food-info">

        <div class="fi2-hero">
          <div style="font-size:3.5rem;line-height:1;">⚠️</div>
          <div>
            <h2>Gıda Güvenliği & Uyarı Rehberi</h2>
            <p>Bozulma süreleri, dikkat edilmesi gerekenler, zehirleyici etkiler ve alerji riskleri.</p>
          </div>
        </div>

        <div class="fi2-filter" id="fi2FilterBar">
          <button class="fi2-chip active" data-c2="all">Tümü</button>
          <button class="fi2-chip" data-c2="bozulma">⏰ Bozulma Süreleri</button>
          <button class="fi2-chip" data-c2="dikkat">⚠️ Dikkat Edilecekler</button>
          <button class="fi2-chip" data-c2="zehir">☠️ Zehirleyici Etkiler</button>
          <button class="fi2-chip" data-c2="alerji">🤧 Alerjiler</button>
        </div>

        <div class="fi2-grid" id="fi2Grid"></div>

      </section>

      <!-- ═══════════════════════════════════════ İÇGÖRÜLER & ANALİTİK
           Chart.js ile dört grafik: Su tüketimi (bar), Genel durum (donut),
           Kalori trendi (line), Rozet ilerlemesi (donut).
           Tüm veriler API'den paralel olarak Promise.all ile çekilir.
           Kalori özet kartı: bugün alınan kalori + haftalık ortalama.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-insights">
        <div class="sectionTitle">
          <h2>İçgörüler 📊</h2>
        </div>

        <!-- ── Kalori & Su Özet Kartı ── -->
        <div class="panel" style="margin-bottom:16px;border:1.5px solid rgba(249,115,22,.3);">
          <div class="panelHd" style="background:linear-gradient(135deg,rgba(249,115,22,.08),transparent);">
            <b>🔥 Bugünkü Kalori</b>
            <span id="insCalBadge" style="background:#f97316;color:#fff;padding:3px 12px;border-radius:20px;font-size:.85rem;font-weight:700;">0 kcal</span>
          </div>
          <div class="panelBd" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px;">
            <div style="text-align:center;background:var(--surface);border-radius:10px;padding:12px 6px;">
              <div style="font-size:1.5rem;font-weight:800;color:#f97316;" id="insCalToday">0</div>
              <div style="font-size:.72rem;color:var(--muted);">Bugün (kcal)</div>
            </div>
            <div style="text-align:center;background:var(--surface);border-radius:10px;padding:12px 6px;">
              <div style="font-size:1.5rem;font-weight:800;color:#3b82f6;" id="insWaterToday">0 L</div>
              <div style="font-size:.72rem;color:var(--muted);">Bugün Su</div>
            </div>
            <div style="text-align:center;background:var(--surface);border-radius:10px;padding:12px 6px;">
              <div style="font-size:1.5rem;font-weight:800;color:#22c55e;" id="insCalWeekAvg">—</div>
              <div style="font-size:.72rem;color:var(--muted);">Haftalık Ort. (kcal)</div>
            </div>
          </div>
        </div>

        <!-- ── İstatistik ızgarası ── -->
        <div class="insights-grid" id="insightsGrid"></div>

        <!-- ── Grafikler ── -->
        <div class="charts-grid" id="chartsRow">
          <div class="panel">
            <div class="panelHd"><b>💧 Son 7 Gün Su Tüketimi</b></div>
            <div class="panelBd" style="height:220px;position:relative;">
              <canvas id="waterChart"></canvas>
            </div>
          </div>
          <div class="panel">
            <div class="panelHd"><b>📊 Genel Durum</b></div>
            <div class="panelBd" style="height:220px;position:relative;">
              <canvas id="statsChart"></canvas>
            </div>
          </div>
          <div class="panel">
            <div class="panelHd"><b>🔥 Son 7 Gün Kalori</b></div>
            <div class="panelBd" style="height:220px;position:relative;">
              <canvas id="calChart"></canvas>
            </div>
          </div>
          <div class="panel">
            <div class="panelHd"><b>🏆 Rozet İlerlemesi</b></div>
            <div class="panelBd" style="height:220px;position:relative;">
              <canvas id="badgeChart"></canvas>
            </div>
          </div>
        </div>
      </section>

      <!-- ═══════════════════════════════════════ AYARLAR & PROFİL
           İki form içerir:
           1) Vücut Profili: kilo/boy/yaş/cinsiyet → user_meta tablosuna JSON olarak kaydedilir.
              Bu veriler Su+Vücut sayfasındaki BMR/TDEE/VKİ hesaplarını besler.
           2) Şifre Değiştir: Mevcut şifre doğrulandıktan sonra bcrypt ile yeni şifre kaydedilir.
           Profil resmi: JPEG/PNG/WEBP yükleme desteği, max 2MB, api.php → avatar_upload.
           Sidebar'daki avatar resmi de anlık güncellenir.
      ═══════════════════════════════════════════════════════════ -->
      <section class="page" id="page-settings">
        <div class="sectionTitle">
          <h2>Ayarlar & Profil ⚙️</h2>
        </div>

        <!-- Profil Resmi Kartı -->
        <div class="panel" style="margin-bottom:18px;max-width:480px;">
          <div class="panelHd"><b>🖼️ Profil Resmi</b></div>
          <div class="panelBd" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div style="position:relative;flex-shrink:0;">
              <img id="avatarPreview" src=""
                style="width:80px;height:80px;border-radius:50%;object-fit:cover;
                  border:3px solid rgba(207,174,85,.5);background:rgba(0,0,0,.1);"
                onerror="this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 80 80\'><rect width=\'80\' height=\'80\' fill=\'%23334155\' rx=\'40\'/><text x=\'50%\' y=\'54%\' font-size=\'32\' text-anchor=\'middle\' dominant-baseline=\'middle\' fill=\'%23cfae55\'>👤</text></svg>'">
              <label for="avatarInput"
                style="position:absolute;bottom:0;right:0;width:24px;height:24px;border-radius:50%;
                  background:#cfae55;display:flex;align-items:center;justify-content:center;
                  cursor:pointer;font-size:.8rem;box-shadow:0 2px 8px rgba(0,0,0,.3);" title="Resim Değiştir">✏️</label>
            </div>
            <div style="flex:1;min-width:180px;">
              <div style="font-weight:700;font-size:.95rem;margin-bottom:4px;" id="avatarUserName"><?= htmlspecialchars($name) ?></div>
              <div style="font-size:.78rem;color:var(--muted);margin-bottom:12px;">Profil fotoğrafınızı değiştirmek için tıklayın</div>
              <input type="file" id="avatarInput" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none">
              <button onclick="document.getElementById('avatarInput').click()"
                class="btn-sm btn-gold" style="margin-right:8px;">📁 Resim Seç</button>
              <button id="btnUploadAvatar" onclick="uploadAvatar()" class="btn-sm"
                style="display:none;background:rgba(34,197,94,.15);color:#15803d;
                  border:1px solid rgba(34,197,94,.3);">✅ Yükle</button>
            </div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;flex-wrap:wrap;">
          <!-- Vücut profili -->
          <div class="panel">
            <div class="panelHd"><b>👤 Vücut Profili</b></div>
            <div class="panelBd">
              <form class="profile-settings-form" id="bodyProfileForm">
                <div class="psf-group"><label>Kilo (kg)</label><input type="number" name="weight" id="psWeight" placeholder="75" min="30" max="250"></div>
                <div class="psf-group"><label>Boy (cm)</label><input type="number" name="height" id="psHeight" placeholder="165" min="120" max="220"></div>
                <div class="psf-group"><label>Yaş</label><input type="number" name="age" id="psAge" placeholder="25" min="10" max="90"></div>
                <div class="psf-group"><label>Cinsiyet</label>
                  <select name="gender" id="psGender">
                    <option value="female">Kadın</option>
                    <option value="male">Erkek</option>
                  </select>
                </div>
                <button type="submit" class="btn-sm btn-gold" style="width:100%;">💾 Profili Kaydet</button>
              </form>
            </div>
          </div>
          <!-- Şifre değiştir -->
          <div class="panel">
            <div class="panelHd"><b>🔒 Şifre Değiştir</b></div>
            <div class="panelBd">
              <form class="profile-settings-form" id="passwordChangeForm">
                <div class="psf-group"><label>Mevcut Şifre</label><input type="password" name="current_password" placeholder="••••••" required></div>
                <div class="psf-group"><label>Yeni Şifre</label><input type="password" name="new_password" placeholder="••••••" required></div>
                <div class="psf-group"><label>Yeni Şifre (Tekrar)</label><input type="password" name="confirm_password" placeholder="••••••" required></div>
                <button type="submit" class="btn-sm btn-gold" style="width:100%;">🔒 Şifreyi Değiştir</button>
              </form>
            </div>
          </div>
        </div>
      </section>

    </main><!-- /wrap -->
  </div><!-- /main-wrapper -->

  <!-- Tarif detay modal -->
  <div class="modalOverlay" id="modalOverlay">
    <div class="modal" role="dialog" aria-modal="true">
      <button class="modalClose" id="modalClose">×</button>
      <div class="modalHero" id="modalHero">
        <div class="modalTitleBox">
          <h2 id="modalTitle">Tarif</h2>
          <div class="subline">
            <span class="chip2" id="modalTime">⏱️ -</span>
            <span class="chip2" id="modalLevel">🔥 -</span>
            <span class="chip2" id="modalCal">🍽️ -</span>
          </div>
        </div>
      </div>
      <div class="modalBody">
        <div class="modalCols">
          <div class="box">
            <h4>🧺 Malzemeler</h4>
            <ul id="modalIng"></ul>
          </div>
          <div class="box">
            <h4>👩‍🍳 Yapılışı</h4>
            <ol id="modalSteps"></ol>
          </div>
        </div>
        <div class="modalActions">
          <button class="btn primary" id="btnCook">🍳 Bunu Yaptım (Rozet)</button>
          <button class="btn gold" id="btnPrintRecipe">🖨️ Tarifi Yazdır</button>
          <button class="btn" id="btnClose2">Kapat</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Profil modal -->
  <div class="modalOverlay" id="profileModal">
    <div class="modal" style="max-width:480px;">
      <button class="modalClose" id="closeProfile">×</button>
      <div style="padding:20px;">
        <h2 style="text-align:center;margin-bottom:14px;">Vücut Profilim</h2>
        <form id="profileForm" class="profile-settings-form">
          <div class="psf-group"><label>Kilo (kg)</label><input type="number" id="inputWeight" placeholder="75" required min="30" max="250"></div>
          <div class="psf-group"><label>Boy (cm)</label><input type="number" id="inputHeight" placeholder="165" required min="120" max="220"></div>
          <div class="psf-group"><label>Yaş</label><input type="number" id="inputAge" placeholder="25" required min="10" max="90"></div>
          <div class="psf-group"><label>Cinsiyet</label>
            <select id="inputGender">
              <option value="female">Kadın</option>
              <option value="male">Erkek</option>
            </select>
          </div>
          <button type="submit" class="btn gold" style="width:100%;justify-content:center;">Analizi Güncelle</button>
        </form>
      </div>
    </div>
  </div>

  <div class="toast-container" id="toastContainer"></div>

  <!-- ═══════════════════════════════════════════════════════════ JS -->

  <script>

        (function(){
          const arts2 = [

            // ── BOZULMA SÜRELERİ ──
            {c:'bozulma', e:'🥛', t:'Süt & Süt Ürünleri Bozulma Süresi', lead:'Açılmış süt ve süt ürünleri için kritik saklama süreleri. Bu süreleri aşmak ciddi gıda zehirlenmesi riskine yol açar.',
             body:`<div class="fi2-shelf-row">
               <div class="fi2-shelf"><b>2–3 gün</b><span>🥛 Açılmış süt (buzdolabı)</span></div>
               <div class="fi2-shelf"><b>7–10 gün</b><span>🧈 Yoğurt (kapalı kap)</span></div>
               <div class="fi2-shelf"><b>1–2 hafta</b><span>🧀 Beyaz peynir (tuzlu)</span></div>
               <div class="fi2-shelf"><b>3–5 hafta</b><span>🥚 Yumurta (yıkanmamış)</span></div>
             </div>
             <ul><li>Açılmamış UHT süt oda sıcaklığında 6 aya kadar dayanır; açıldıktan sonra max 3 gün.</li><li>Pastörize süt (kısa ömürlü) buzdolabında 5–7 gün.</li><li>Greek yoğurt açıldıktan sonra max 5–7 gün.</li><li>Tereyağı buzdolabında 1 ay, derin dondurucuda 6–9 ay.</li></ul>
             <div class="fi2-warn">⚠️ Sarımtırak renk, ekşi koku veya kabarcık oluşumu bozulma işaretidir. Şüpheli süt ürünlerini asla tüketmeyin.</div>`},

            {c:'bozulma', e:'🥩', t:'Et & Tavuk Bozulma Süresi', lead:'Et ürünleri en hızlı bozulan gıdalar arasındadır. Doğru saklama ve sürelere uymak hayati önem taşır.',
             body:`<div class="fi2-shelf-row">
               <div class="fi2-shelf"><b>1–2 gün</b><span>🍗 Taze tavuk/kıyma (buzdolabı)</span></div>
               <div class="fi2-shelf"><b>3–5 gün</b><span>🥩 Parça et (buzdolabı)</span></div>
               <div class="fi2-shelf"><b>5–7 gün</b><span>🌭 Açılmış sosis/salam</span></div>
               <div class="fi2-shelf"><b>6–12 ay</b><span>❄️ Derin dondurucuda et</span></div>
             </div>
             <ul><li>Dondurulmuş et çözüldükten sonra yeniden dondurulmamalı.</li><li>Et her zaman buzdolabının en alt rafında saklanmalı (diğer gıdalara damlamasın).</li><li>Pişmiş et buzdolabında max 3–4 gün.</li></ul>
             <div class="fi2-warn">⚠️ Gri-yeşil renk değişimi, yapışkanlık veya ekşi/çürük koku bozulma işareti. Şüpheli eti kesinlikle tüketmeyin.</div>
             <div class="fi2-info">🌡️ Güvenli iç pişirme sıcaklığı: Tavuk 74°C, Kıyma 71°C, Parça et 63°C.</div>`},

            {c:'bozulma', e:'🥦', t:'Sebze & Meyve Bozulma Süresi', lead:'Her sebze ve meyvenin kendine özgü bozulma hızı vardır. Yanlış saklama besin değeri kaybını ve erken bozulmayı hızlandırır.',
             body:`<div class="fi2-shelf-row">
               <div class="fi2-shelf"><b>1–3 gün</b><span>🥬 Yeşil yapraklılar</span></div>
               <div class="fi2-shelf"><b>5–7 gün</b><span>🍅 Olgunlaşmış domates</span></div>
               <div class="fi2-shelf"><b>1–2 hafta</b><span>🥕 Havuç, brokoli</span></div>
               <div class="fi2-shelf"><b>1–2 ay</b><span>🥔 Patates, soğan (serin/karanlık)</span></div>
             </div>
             <ul><li>🍌 Olgunlaşmış muz buzdolabında 3–5 gün (kabuk kararır ama içi taze kalır).</li><li>🍓 Çilek yıkanmadan saklanmalı; yıkandıktan sonra max 1 gün.</li><li>🥑 Avokado, kesilmişse limon sürüp hava geçirmez kapta sakla.</li><li>Patates buzdolabına koyulmamalı — nişasta şekere dönüşür, tat bozulur.</li></ul>
             <div class="fi2-alert">⚠️ Meyveler etilen gazı salgılar — sebzelerden ayrı saklayın, yoksa sebzeler erken olgunlaşıp çabuk bozulur.</div>`},

            {c:'bozulma', e:'🍞', t:'Ekmek, Tahıl & Kuru Bakliyat', lead:'Tahıl ürünleri ve kuru bakliyatın bozulma süreleri diğer gıdalara göre daha uzundur; ancak nem ve sıcaklık kritik faktörlerdir.',
             body:`<div class="fi2-shelf-row">
               <div class="fi2-shelf"><b>2–3 gün</b><span>🍞 Ekmek (oda sıcaklığı)</span></div>
               <div class="fi2-shelf"><b>2–3 ay</b><span>🌾 Açılmamış kuru makarna</span></div>
               <div class="fi2-shelf"><b>1–2 yıl</b><span>🫘 Kuru baklagiller</span></div>
               <div class="fi2-shelf"><b>6–12 ay</b><span>🌾 Pirinç, bulgur (ağzı kapalı)</span></div>
             </div>
             <ul><li>Ekmek buzdolabında kuru olur; dondurucuda 3 aya kadar tazeliğini korur.</li><li>Kuru bakliyat yaşlandıkça pişirme süresi uzar, besin değeri azalır.</li><li>Un, yulaf açıldıktan sonra hava geçirmez kapta 3–6 ay.</li></ul>
             <div class="fi2-warn">⚠️ Tahıllar üzerindeki mavi-yeşil küf (Aspergillus) aflatoksin üretir — kesinlikle tüketmeyin, küflü kısmı kesip geri kalanı yemek yeterli değildir.</div>`},

            // ── DİKKAT EDİLECEKLER ──
            {c:'dikkat', e:'🧊', t:'Derin Dondurucu Dikkat Noktaları', lead:'Derin dondurucu gıdaları sonsuza kadar korumaz; yanlış kullanım ciddi gıda güvenliği sorunlarına yol açar.',
             body:`<ul>
               <li><b>-18°C altında tutun</b> — elektrik kesilmelerinde max 48 saat gıdalar güvenlidir (kapağı açmayın).</li>
               <li><b>Yeniden dondurma riski:</b> Çözülmüş et/balık/kümes hayvanları kesinlikle yeniden dondurulmamalı.</li>
               <li><b>Donmuş gıdayı çözme:</b> Buzdolabında (en güvenli), soğuk su altında veya mikrodalgada — oda sıcaklığında değil.</li>
               <li>Dondurucuda çok uzun kalan gıdalarda "freezer burn" (donma yanığı) oluşur — kahverengi/gri lekeler. Tüketimi zararlı değil ama tat kötüleşir.</li>
             </ul>
             <div class="fi2-warn">⚠️ Koku ve görünüm donmuş gıdanın güvenli olduğunu garanti etmez — bakteri sporları donmaya dirençlidir.</div>
             <div class="fi2-info">💡 Dondurma tarihini etiketleyin: Et 6–12 ay, Balık 3–6 ay, Ekmek 3 ay, Pişmiş yemek 2–3 ay.</div>`},

            {c:'dikkat', e:'🫙', t:'Konserve & Hazır Gıda Dikkat Noktaları', lead:'Konserveler uzun raf ömrüne sahip olmakla birlikte, yanlış saklama veya hasarlı ambalaj ciddi risk oluşturur.',
             body:`<ul>
               <li><b>Şişmiş/bombeli konserve:</b> İçinde Clostridium botulinum olabilir — kesinlikle açmayın veya tüketmeyin.</li>
               <li>Pas tutmuş, ezilmiş veya sızdıran konserveleri atmak en güvenli seçenektir.</li>
               <li>Açılmış konserveyi orijinal kutusunda buzdolabında max 3–4 gün bırakabilirsiniz (cam/plastik kaba aktarın).</li>
               <li><b>SKT (Son Kullanma Tarihi) vs TKT (Tüketim Tarihi):</b> SKT geçen bazı ürünler hala tüketilebilir; TKT geçen ürünler kesinlikle tüketilmemeli.</li>
             </ul>
             <div class="fi2-warn">⚠️ Botulinum toksini renksiz, kokusuzdur — görünüm ve koku güvenli görünse bile risk taşır. Şüpheli konserveyi ısıtmak da toksini tamamen yok etmez.</div>`},

            {c:'dikkat', e:'🥚', t:'Yumurta Saklama Dikkat Noktaları', lead:'Yumurta görünürde taze görünse bile bakteri içerebilir. Doğru saklama ve pişirme kritik önem taşır.',
             body:`<ul>
               <li>Türkiye'de yumurtalar yıkanmadan satılır (doğal koruyucu balmumu katmanı korunur) — mümkünse yıkamadan saklayın.</li>
               <li>Yumurtaları buzdolabının kapı bölmesine değil, en az sallanan raf bölümüne koyun.</li>
               <li>Çatlamış yumurtayı hemen kullanın veya atın.</li>
               <li>Su testi: Taze yumurta suya batar; bayat yumurta yüzer — bu güvenilir bir testtir.</li>
               <li>Pişirilmiş yumurta buzdolabında max 1 hafta.</li>
             </ul>
             <div class="fi2-warn">⚠️ Çiğ yumurta veya az pişmiş yumurta Salmonella riski taşır — özellikle hamile kadınlar, yaşlılar ve bağışıklığı zayıf kişiler için tehlikelidir.</div>`},

            {c:'dikkat', e:'🌡️', t:'Sıcaklık Tehlike Bölgesi (Danger Zone)', lead:'Gıda güvenliğinin en temel kuralı: Bakterilerin en hızlı çoğaldığı sıcaklık aralığını bilmek.',
             body:`<div class="fi2-shelf-row">
               <div class="fi2-shelf"><b>4°C altı</b><span>✅ Güvenli (buzdolabı)</span></div>
               <div class="fi2-shelf"><b>4–60°C</b><span>☠️ TEHLİKE BÖLGESİ</span></div>
               <div class="fi2-shelf"><b>60°C üzeri</b><span>✅ Bakteriler ölür</span></div>
               <div class="fi2-shelf"><b>2 saat</b><span>⚠️ Max oda sıcaklığı süresi</span></div>
             </div>
             <ul><li>Tehlike bölgesinde 20 dakikada bir bakteri sayısı ikiye katlanabilir.</li><li>Pişirilmiş yemek oda sıcaklığında max 2 saat bekletilmeli; sıcak havalarda bu süre 1 saate düşer.</li><li>Büyük yemekler buzdolabında saklanmadan önce küçük kaplara bölünmeli (hızlı soğuması için).</li></ul>
             <div class="fi2-warn">⚠️ "Görünce anlarım" yanlış! Bakteri üremesi görünür değişikliğe yol açmayabilir — koku ve görünüm her zaman güvenlik garantisi değildir.</div>`},

            // ── ZEHİRLEYİCİ ETKİLER ──
            {c:'zehir', e:'☠️', t:'Gıda Zehirlenmesi: Belirtiler & Ne Yapmalı?', lead:'Gıda zehirlenmesi her yıl milyonlarca insanı etkiler. Belirtileri tanımak ve doğru davranmak kritiktir.',
             body:`<ul>
               <li><b>Belirtiler:</b> Bulantı, kusma, ishal, karın ağrısı, ateş, baş ağrısı — genellikle 2–48 saat içinde başlar.</li>
               <li><b>Staphylococcus aureus (Staph):</b> Eller aracılığıyla bulaşır; 1–6 saat içinde ani başlangıç; toxini ısıya dayanıklı.</li>
               <li><b>Salmonella:</b> Çiğ et, yumurta, kümes hayvanları; 12–72 saat belirtisi; yüksek ateş eşlik eder.</li>
               <li><b>E. coli O157:</b> Az pişmiş kıyma, kirli su; 1–10 gün; kanlı ishal, böbrek yetmezliği riski.</li>
               <li><b>Listeria:</b> Hazır etler, yumuşak peynir; hamile kadınlar ve bağışıklığı zayıf kişiler için tehlikeli.</li>
             </ul>
             <div class="fi2-warn">🚨 Şu durumlarda hemen hastaneye gidin: Kanlı ishal, yüksek ateş (38.5°C+), aşırı kusma/dehidrasyon, nörolojik belirtiler (çift görme, yutma güçlüğü).</div>
             <div class="fi2-info">💡 Tedavi: Bol su içmek, elektrolit (tuz-şeker-su karışımı). Antibiyotik kural değil; hafif vakalarda gerekli değil.</div>`},

            {c:'zehir', e:'🥬', t:'Doğal Bitki Toksinleri: Gözden Kaçan Tehlikeler', lead:'Bazı gıdalar doğal olarak toksik bileşikler içerir. Doğru hazırlama bu riskleri minimize eder.',
             body:`<ul>
               <li><b>🥔 Yeşillenmiş/filizlenmiş patates:</b> Solanin içerir (acı, yeşil kısımlar). Mide bulantısı, kusma, baş dönmesi yapabilir. Yeşil kısımları kalınca soyun — çok fazlaysa atın.</li>
               <li><b>🫘 Çiğ kırmızı fasulye:</b> Phasin/lektin içerir; 4–5 çiğ fasulye ile ciddi kusma-ishal başlar. Mutlaka 10+ dakika kaynatın, ıslatma suyunu dökün.</li>
               <li><b>🍒 Meyve çekirdekleri (kayısı, şeftali, kiraz):</b> Amigdalin içerir — vücutta siyanür açığa çıkarır. Yutmayın.</li>
               <li><b>🍠 Çiğ ıspanak/pancar:</b> Oksalik asit böbrek taşı riskini artırır; yüksek miktarda ham tüketim önerilmez.</li>
               <li><b>🥦 Çiğ brokoli/lahana:</b> Thiocyanate içerir; çok fazla tüketim tiroid fonksiyonunu etkileyebilir (normal miktarlarda sorun yok).</li>
             </ul>
             <div class="fi2-alert">⚠️ Mantar toplamayı bilmiyorsanız asla doğadan mantar toplamayın — zehirli mantarların büyük çoğunluğu yenilir mantarlara çok benzer.</div>`},

            {c:'zehir', e:'🐟', t:'Deniz Ürünleri Zehirlenmesi & Histamin', lead:'Deniz ürünleri zehirlenmeleri özellikle tehlikeli olabilir. Bazı türlerde "pişirmek" korumaz.',
             body:`<ul>
               <li><b>Skombroid (Histamin) zehirlenmesi:</b> Ton balığı, uskumru, sardalye yetersiz soğutulduğunda histamin biriktirir. Belirtiler: Yüz kızarması, baş ağrısı, ürtiker — alerjiye benzer. Pişirmek önlemez.</li>
               <li><b>Ciguatera zehirlenmesi:</b> Tropik balıklarda (mercan balığı vb.) ciguatoksin birikimi. Pişirmek etkisiz. Türkiye'de nadir.</li>
               <li><b>Tetrodotoksin (Balon balığı):</b> Japonya'da fugu olarak bilinen balon balığı — Türk sularında da bulunur. Eğitimsiz kişilerce kesinlikle hazırlanmamalı.</li>
               <li><b>Çiğ midye/istiridye:</b> Kirli sularda Vibrio ve Norovirus riski. Kırmızı gel döneminde (kıyı alarmı varsa) tüketilmemeli.</li>
             </ul>
             <div class="fi2-warn">⚠️ Deniz ürünleri zehirlenmesinde solunumsal belirtiler (nefes darlığı, felç) varsa derhal 112'yi arayın.</div>`},

            {c:'zehir', e:'🍄', t:'Küf & Mikotoksinler: Görünmeyen Tehdit', lead:'Bazı küf türleri gıdalarda yüzeyden derine işleyen tehlikeli toksinler üretir.',
             body:`<ul>
               <li><b>Aflatoksin (Aspergillus):</b> Tahıllar, fındık, fıstık, baharatlar üzerinde oluşur. Karaciğer kanseri riskiyle ilişkilendirilmiştir. Küflü kısmı kesip geri kalanını yemek yeterli değildir.</li>
               <li><b>Okratoksin:</b> Kahve çekirdekleri, tahıllar, kırmızı şarap — böbrek toksisitesi riski.</li>
               <li><b>Patulin:</b> Küflü elma ve elma sularında — çocuklar için özellikle riskli.</li>
             </ul>
             <div class="fi2-warn">⚠️ Sert gıdalarda (sert peynir, havuç) küf görüldüğünde 2–3cm çevresini keserek geri kalanı tüketebilirsiniz. Yumuşak gıdalarda (ekmek, meyve, yoğurt) görünen küf tüm gıdanın kontamine olduğunu gösterir — atın.</div>
             <div class="fi2-info">💡 Mikotoksinler ısıya dayanıklıdır — pişirmek, küf ürettiği toksinleri yok etmez.</div>`},

            // ── ALERJİLER ──
            {c:'alerji', e:'🤧', t:'14 Büyük Alerjen: Zorunlu Bildirim Listesi', lead:'AB ve Türk gıda mevzuatına göre bu 14 alerjen gıda etiketlerinde zorunlu olarak belirtilmelidir.',
             body:`<ul>
               <li>🌾 <b>Gluten içeren tahıllar</b> (buğday, çavdar, arpa, yulaf)</li>
               <li>🦐 <b>Kabuklu deniz ürünleri</b> (karides, yengeç, ıstakoz)</li>
               <li>🥚 <b>Yumurta</b></li>
               <li>🐟 <b>Balık</b></li>
               <li>🥜 <b>Yer fıstığı</b></li>
               <li>🫘 <b>Soya</b></li>
               <li>🥛 <b>Süt</b> (laktoz dahil)</li>
               <li>🌰 <b>Kabuklu yemişler</b> (fındık, ceviz, badem, antep fıstığı, kaju, makademya, brezilya fıstığı)</li>
               <li>🌿 <b>Kereviz</b></li>
               <li>🌻 <b>Hardal</b></li>
               <li>🌾 <b>Susam</b></li>
               <li>🧂 <b>Kükürt dioksit / sülfitler</b> (≥10mg/kg — kuru meyve, şarap)</li>
               <li>🦑 <b>Yumuşakçalar</b> (midye, istiridye, ahtapot)</li>
               <li>🌱 <b>Acı bakla (lupine)</b></li>
             </ul>
             <div class="fi2-info">💡 Bu alerjenleri içeren gıdaların etiketi okunmadan tüketilmesi ciddi alerjik reaksiyona yol açabilir.</div>`},

            {c:'alerji', e:'🌾', t:'Gluten Duyarlılığı & Çölyak', lead:'Gluten alerjisi ve çölyak hastalığı farklı mekanizmalarla işler; her ikisinde de buğday, çavdar ve arpa içeren gıdalar risk oluşturur.',
             body:`<ul>
               <li><b>Çölyak Hastalığı:</b> Otoimmün hastalık — glutene karşı bağışıklık sistemi ince bağırsağa saldırır. Ömür boyu gluten kaçınması gerekir.</li>
               <li><b>Gluten Hassasiyeti (Non-çölyak):</b> Bağırsak zedelenmesi yok ama şişkinlik, baş ağrısı, yorgunluk semptomları.</li>
               <li><b>Buğday Alerjisi:</b> IgE aracılı alerji — anafilaksi riski taşır (çölyaktan farklı mekanizma).</li>
               <li>Çapraz kontaminasyon riski: Aynı mutfak aletinin glutenli gıda ile temas etmesi bile çölyaklılar için tehlikeli.</li>
             </ul>
             <div class="fi2-alert">⚠️ "Az miktarda" gluten çölyaklılar için bile zararlıdır — ince bağırsak zedelenmesi semptom olmaksızın da devam edebilir.</div>
             <div class="fi2-info">🌾 Güvenli alternatifler: Pirinç, mısır, patates, karabuğday, kinoa, manioka unu.</div>`},

            {c:'alerji', e:'🥜', t:'Fındık & Kuruyemiş Alerjisi', lead:'Kuruyemiş alerjileri en ciddi ve potansiyel olarak yaşamı tehdit eden alerjiler arasındadır.',
             body:`<ul>
               <li>Yer fıstığı alerji bakımından en tehlikelisi — baklagil ailesinden, kuruyemiş değil.</li>
               <li>Anafilaksi riski yüksek: Nefes darlığı, boğaz şişmesi, kan basıncı düşmesi, bilinç kaybı.</li>
               <li>Çapraz reaktivite: Bir kuruyemişe alerjisi olan kişiler diğerlerine de reaksiyon gösterebilir.</li>
               <li>Gizli kaynaklar: Ekmek, bisküvi, çikolata, sos, bazı kozmetikler (badem yağı).</li>
               <li>Semptomlar miktar ne kadar az olursa olsun tetiklenebilir.</li>
             </ul>
             <div class="fi2-warn">🚨 Ağır anafilaksi belirtilerinde (solunumsal sıkıntı, yüz/boğaz şişmesi) EpiPen varsa kullanın ve hemen 112'yi arayın.</div>
             <div class="fi2-info">💡 Kuruyemiş alerjisi genellikle ömür boyu devam eder; çocuklarda yer fıstığı alerjisi %20 erişkin dönemde geçebilir.</div>`},

            {c:'alerji', e:'🥛', t:'Süt Alerjisi vs Laktoz İntoleransı', lead:'Süt alerjisi ile laktoz intoleransı sıklıkla karıştırılır; ancak mekanizmaları, riskleri ve yönetimi tamamen farklıdır.',
             body:`<ul>
               <li><b>Süt Alerjisi (IgE aracılı):</b> Süt proteinine (kazein, whey) bağışıklık sistemi reaksiyonu. Anafilaksi riski var. Tüm süt ürünleri kaçınılmalı.</li>
               <li><b>Laktoz İntoleransı:</b> Laktaz enzimi eksikliği — laktoz sindirilemiyor. Şişkinlik, gaz, ishal. Alerji değil, yaşam tehlikesi yok.</li>
               <li>Laktoz intoleranlılar genellikle az miktarda süt ürününü tolere edebilir; fermente ürünler (yoğurt, peynir) daha iyi tolere edilir.</li>
               <li>Gizli kaynaklar: Ekmek, bisküvi, çorbalar, işlenmiş etler, bazı ilaçlar.</li>
             </ul>
             <div class="fi2-info">💡 Laktoz intoleranlılar için: Laktozsuz süt, soya sütü, badem sütü güvenli alternatiflerdir. Laktaz damlaları da kullanılabilir.</div>`},
          ];

          function renderFi2(cat){
            const grid = document.getElementById('fi2Grid');
            const list = cat==='all' ? arts2 : arts2.filter(a=>a.c===cat);
            const labels = {bozulma:'⏰ Bozulma',dikkat:'⚠️ Dikkat',zehir:'☠️ Zehir',alerji:'🤧 Alerji'};
            const badgeCls = {bozulma:'fi2-badge-bozulma',dikkat:'fi2-badge-dikkat',zehir:'fi2-badge-zehir',alerji:'fi2-badge-alerji'};
            grid.innerHTML = list.map((a,i)=>{
              const key = `fi2_${i}_${cat}`;
              return `<article class="fi2-card">
                <div class="fi2-card-top">
                  <div class="fi2-emoji">${a.e}</div>
                  <div style="flex:1;">
                    <span class="fi2-badge ${badgeCls[a.c]||''}">${labels[a.c]||a.c}</span>
                    <h3 class="fi2-title">${a.t}</h3>
                  </div>
                </div>
                <p class="fi2-lead">${a.lead}</p>
                <div class="fi2-body" id="${key}">${a.body}</div>
                <button class="fi2-expand" onclick="
                  const b=document.getElementById('${key}');
                  const o=b.classList.toggle('open');
                  this.textContent=o?'▲ Daha Az':'▼ Detayları Gör';
                ">▼ Detayları Gör</button>
              </article>`;
            }).join('');
          }

          renderFi2('all');

          document.getElementById('fi2FilterBar').addEventListener('click', function(e){
            const btn=e.target.closest('[data-c2]');
            if(!btn) return;
            this.querySelectorAll('.fi2-chip').forEach(c=>c.classList.remove('active'));
            btn.classList.add('active');
            renderFi2(btn.dataset.c2);
          });
        })();

    // ── PHP → JavaScript veri köprüsü (SERVER nesnesi) ──────────
    // PHP tarafında hesaplanan değerler bu nesne aracılığıyla tarayıcıya aktarılır.
    // json_encode: PHP değerlerini güvenli JS literal'ına çevirir — XSS riski yoktur.
    // Bu nesne sayesinde JavaScript ilk yüklenmede API çağrısı yapmadan veriyi kullanabilir.
    const SERVER = {
      userId: <?= (int)$uid ?>,           // Kullanıcı ID (tam sayı, casting ile güvenli)
      userName: <?= json_encode($name) ?>, // Kullanıcı adı (JSON encode ile XSS güvenli)
      waterToday: <?= $waterToday ?>,      // Bugün içilen su (ml) — su takibi başlangıç değeri
      profileJson: <?= $profileJson ?>     // Vücut profili JSON veya null — BMR/VKİ için
    };

    // ── API çağrısı ──────────────────────────────────────────────
    // Tüm AJAX istekleri bu tek fonksiyon üzerinden geçer.
    // action: api.php'deki action parametresi (ör: 'water_add', 'fridge_list')
    // body: POST gönderilecek veri objesi
    // method: varsayılan POST; GET için 'GET' geç (FormData gönderilmez)
    /**
     * api — Merkezi API istemcisi. api.php'ye fetch ile istek gönderir.
     *
     * @param {string} action - API aksiyonu (ör: 'water_add', 'badge_earn').
     *                          Sorgu parametresi içerebilir (ör: 'menu_list?date=2024-01-01').
     * @param {object} body   - POST gövdesi için anahtar-değer çifti (GET'te kullanılmaz).
     * @param {string} method - 'POST' (varsayılan) veya 'GET'.
     * @returns {object}      - {success, message, data} şeklinde JSON yanıtı.
     *                          Ağ hatası durumunda {success:false, message:'Bağlantı hatası'} döner.
     */
    // Eski api.php?action=X çağrılarını yeni REST endpoint'lerine eşleyen harita.
    // {id} işaretli olanlarda body.id URL'ye taşınır ve DELETE/PUT kullanılır.
    const API_MAP = {
      'fridge_list':               { path: 'api/fridge', method: 'GET' },
      'fridge_add':                { path: 'api/fridge', method: 'POST' },
      'fridge_delete':              { path: 'api/fridge/{id}', method: 'DELETE' },
      'fridge_update':              { path: 'api/fridge/{id}', method: 'PUT' },
      'shop_list':                  { path: 'api/shopping', method: 'GET' },
      'shop_add':                   { path: 'api/shopping', method: 'POST' },
      'shop_toggle':                { path: 'api/shopping/{id}/toggle', method: 'PUT' },
      'shop_delete':                { path: 'api/shopping/{id}', method: 'DELETE' },
      'waste_add':                  { path: 'api/waste', method: 'POST' },
      'waste_list':                 { path: 'api/waste', method: 'GET' },
      'water_add':                  { path: 'api/water', method: 'POST' },
      'water_today':                { path: 'api/water/today', method: 'GET' },
      'water_weekly':               { path: 'api/water/weekly', method: 'GET' },
      'menu_list':                  { path: 'api/menu', method: 'GET' },
      'menu_add':                   { path: 'api/menu', method: 'POST' },
      'menu_delete':                { path: 'api/menu/{id}', method: 'DELETE' },
      'menu_today_calories':        { path: 'api/menu/today-calories', method: 'GET' },
      'menu_weekly_calories':       { path: 'api/menu/weekly-calories', method: 'GET' },
      'menu_add_from_recipe':       { path: 'api/menu/from-recipe', method: 'POST' },
      'food_calories_dropdown':     { path: 'api/food-calories/dropdown', method: 'GET' },
      'badge_earn':                 { path: 'api/badges/earn', method: 'POST' },
      'badge_list':                 { path: 'api/badges', method: 'GET' },
      'badge_daily_status':         { path: 'api/badges/daily-status', method: 'GET' },
      'badge_reset_daily':          { path: 'api/badges/reset-daily', method: 'POST' },
      'password_change':            { path: 'api/password', method: 'PUT' },
      'avatar_upload':              { path: 'api/avatar', method: 'POST' },
      'avatar_get':                 { path: 'api/avatar', method: 'GET' },
      'notifications_list':         { path: 'api/notifications', method: 'GET' },
      'notifications_read_all':     { path: 'api/notifications/read-all', method: 'PUT' },
      'notifications_delete':       { path: 'api/notifications/{id}', method: 'DELETE' },
      'notifications_sync_fridge':  { path: 'api/notifications/sync-fridge', method: 'POST' },
      'recipe_list':                { path: 'api/recipes', method: 'GET' },
      'recipe_delete':              { path: 'api/recipes/{id}', method: 'DELETE' },
      'my_likes':                   { path: 'api/my-likes', method: 'GET' },
      'my_comments':                { path: 'api/my-comments', method: 'GET' },
      'comment_edit':                { path: 'api/comments/{id}', method: 'PUT' },
      'comment_delete':              { path: 'api/comments/{id}', method: 'DELETE' },
      'recipe_like':                 { path: 'api/recipes/like', method: 'POST' },
      'recipe_save_interaction':     { path: 'api/recipes/save', method: 'POST' },
      'recipe_comment':              { path: 'api/recipes/comment', method: 'POST' },
      'recipe_get_interactions':     { path: 'api/recipes/public', method: 'GET' },
      'community_recipes':          { path: 'api/community-recipes', method: 'GET' },
      'ai_recipe_recommendations':  { path: 'api/recipes/ai-recommendations', method: 'GET' },
      'user_recipe_list':           { path: 'api/user-recipes', method: 'GET' },
      'user_recipe_add':            { path: 'api/user-recipes', method: 'POST' },
      'user_recipe_delete':         { path: 'api/user-recipes/{id}', method: 'DELETE' },
      // Aşağıdakiler orijinal api.php'de de HİÇ TANIMLI DEĞİLDİ (önceden de
      // çalışmıyorlardı) — davranış birebir korunuyor, yeni backend eklenmedi.
      'profile_meta_save':          { path: 'api/profile-meta', method: 'POST' },
      'tasks_get':                  { path: 'api/tasks', method: 'GET' },
      'tasks_save':                 { path: 'api/tasks', method: 'POST' },
    };

    async function api(action, body = {}, method = 'POST') {
      let [baseAction, queryString] = action.split('?');
      const mapEntry = API_MAP[baseAction] || { path: 'api/' + baseAction, method };
      let path = mapEntry.path;
      const httpMethod = mapEntry.method || method;

      if (path.includes('{id}')) {
        path = path.replace('{id}', encodeURIComponent(body.id ?? ''));
      }

      let url = path;
      if (queryString) {
        url += (url.includes('?') ? '&' : '?') + queryString;
      }

      const opts = { method: httpMethod };
      if (httpMethod !== 'GET') {
        opts.headers = { 'Content-Type': 'application/json' };
        opts.body = JSON.stringify(body);
      }

      try {
        const r = await fetch(url, opts);
        return await r.json();
      } catch (e) {
        return {
          success: false,
          message: 'Bağlantı hatası'
        };
      }
    }

    /**
     * toast — Ekranın sağ altında anlık bildirim kutusu gösterir.
     *
     * @param {string} msg  - Gösterilecek mesaj metni.
     * @param {string} type - 'success' (yeşil ✅) | 'error' (kırmızı ⚠️) | 'info' (mavi ℹ️)
     *
     * Bildirim 3 saniye sonra otomatik kaybolur.
     */
    function toast(msg, type = 'info') {
      const c = document.getElementById('toastContainer');
      const el = document.createElement('div');
      el.className = 'toast ' + type;
      el.innerHTML = (type === 'success' ? '✅' : type === 'error' ? '⚠️' : 'ℹ️') + ' ' + msg;
      c.appendChild(el);
      setTimeout(() => {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
      }, 3000);
    }

    // ── HTML escape ──────────────────────────────────────────────
    // Veritabanından gelen kullanıcı verilerini HTML içine gömmeden önce
    // XSS saldırılarına karşı özel karakterleri encode eder.
    function esc(s) {
      return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // ── Sayfa navigasyonu ────────────────────────────────────────
    // SPA mantığı: Tüm sayfalar DOM'da mevcut, sadece biri .active class'ı alır.
    // setActivePage() → aktif sayfayı değiştirir, sayfa başına scroll eder,
    // onPageLoad() ile sayfaya özgü veri yükleme tetiklenir.
    const sidebar = document.getElementById('sidebar');
    const allPages = Array.from(document.querySelectorAll('.page'));
    const allNavItems = Array.from(document.querySelectorAll('.nav-item[data-page]'));

    /**
     * setActivePage — SPA sayfa geçişini yönetir.
     *
     * @param {string} pageId - Gösterilecek sayfanın HTML id'si (ör: 'page-badges').
     *
     * Tüm .page elemanları gizlenir, sadece eşleşen .active sınıfı alır.
     * Sidebar kapanır, sayfa başına scroll edilir.
     * onPageLoad() ile o sayfaya özel veri yükleme tetiklenir.
     */
    function setActivePage(pageId) {
      allPages.forEach(p => p.classList.toggle('active', p.id === pageId));
      allNavItems.forEach(n => n.classList.toggle('active', n.dataset.page === pageId));
      sidebar.classList.remove('active');
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
      onPageLoad(pageId);
    }
    allNavItems.forEach(n => n.addEventListener('click', () => setActivePage(n.dataset.page)));
    document.querySelectorAll('[data-goto]').forEach(b => b.addEventListener('click', () => setActivePage(b.dataset.goto)));
    document.getElementById('menuToggle').addEventListener('click', () => sidebar.classList.toggle('active'));
    document.getElementById('btnGoRecipes').addEventListener('click', () => setActivePage('page-recipes'));

    // ── Tema ─────────────────────────────────────────────────────
    // Dark/Light mod tercihi localStorage'da saklanır ('cm_theme' anahtarı).
    // Sayfa yüklenirken geçiş animasyonu engellenir ('no-theme-transition' class),
    // ardından bir sonraki frame'de kaldırılır — böylece yanıp sönme olmaz.
    const themeBtn = document.getElementById('themeToggleSidebar');
    const saved = localStorage.getItem('cm_theme');
    // Sayfa ilk yüklenirken geçiş animasyonu olmasın
    document.body.classList.add('no-theme-transition');
    if (saved === 'dark') document.body.classList.add('dark-mode');
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        document.body.classList.remove('no-theme-transition');
      });
    });
    themeBtn.textContent = document.body.classList.contains('dark-mode') ? '☀️ Temayı Aç' : '🌙 Tema Değiştir';
    themeBtn.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      themeBtn.textContent = document.body.classList.contains('dark-mode') ? '☀️ Temayı Aç' : '🌙 Tema Değiştir';
      localStorage.setItem('cm_theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
    });

    // ── Header scroll glassmorphism efekti ──────────────────────
    // Kullanıcı 60px aşağı kaydırınca header'a 'scrolled' class'ı eklenir.
    // dashboard.css'te .header.scrolled → blur + yarı şeffaf arka plan (glassmorphism).
    // requestAnimationFrame ile scroll olayı verimli şekilde sınırlandırılır (throttle).
    (function initHeaderScroll() {
      const hdr = document.querySelector('header');
      if (!hdr) return;
      let ticking = false;
      window.addEventListener('scroll', () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
          hdr.classList.toggle('scrolled', window.scrollY > 60);
          ticking = false;
        });
      }, { passive: true });
    })();

    // ════════════════════════════════════════════ SU TAKİBİ
    // waterTarget : Günlük su hedefi (ml). Vücut profiline göre hesaplanır (35ml × kg).
    //               Profil girilmezse varsayılan 2000ml kullanılır.
    // waterAmount : O an içilen toplam su (ml). SERVER.waterToday ile başlar (PHP'den gelir).
    // _waterInteracted: Sayfa açılışında rozet yanlışlıkla kazanılmasın diye bayrak.
    //                   Kullanıcı + butonuna ilk bastığında true yapılır.
    let waterTarget = 2000;
    let waterAmount = SERVER.waterToday;

    let _waterInteracted = false; // Sayfa yüklenince rozet kazanılmasın

    /**
     * renderWater — Su takibi arayüzünü günceller.
     *
     * waterAmount (ml) ve waterTarget (ml) değerlerine göre:
     *  - Cam animasyonunun doluluk yüzdesini ayarlar (%0–100).
     *  - Hedef pill etiketini günceller.
     *  - Genel bakış istatistiğini (statWater) litre cinsinden yazar.
     *  - Kullanıcı + düğmesine bastıktan sonra 'water' rozet koşulunu denetler.
     *    (_waterInteracted bayrağı: sayfa açılışında rozet tetiklenmesini engeller.)
     */
    function renderWater() {
      document.getElementById('currentWaterDisplay').textContent = waterAmount;
      const pct = Math.min(100, Math.round(waterAmount / waterTarget * 100));
      document.getElementById('waterFill').style.height = pct + '%';
      document.getElementById('waterTargetPill').textContent = 'Hedef: ' + waterTarget + 'ml';
      document.getElementById('statWater').textContent = (waterAmount / 1000).toFixed(1) + ' L';
      // Sadece kullanıcı + düğmesine bastıktan sonra rozet kontrolü yap
      if (_waterInteracted) checkBadge('water', waterAmount >= waterTarget);
    }

    document.getElementById('waterPlus').addEventListener('click', async () => {
      _waterInteracted = true;
      const r = await api('water_add', {
        amount_ml: 250
      });
      if (r.success) {
        waterAmount = r.data.total_ml;
        renderWater();
        toast('💧 250ml eklendi', 'success');
      } else toast(r.message, 'error');
    });
    document.getElementById('waterMinus').addEventListener('click', async () => {
      // Sunucu tarafında azaltma yok; negatif olamaz
      if (waterAmount <= 0) {
        toast('Su zaten 0!', 'info');
        return;
      }
      waterAmount = Math.max(0, waterAmount - 250);
      renderWater();
      toast('Su azaltıldı (sadece görsel)', 'info');
    });
    document.getElementById('waterReset').addEventListener('click', () => {
      waterAmount = 0;
      renderWater();
      toast('Su sayaçı sıfırlandı (görsel)', 'info');
    });

    // ════════════════════════════════════════════ GÖREVLER
    // Günlük 3 görev: Kahvaltı, Sebze/Meyve, Tuzluğu Koyma.
    // tasks dizisi önce api.php → tasks_get ile sunucudan çekilir,
    // sunucu yanıt vermezse localStorage fallback kullanılır.
    // Her yeni günde tasks temizlenir (taskDate kontrolü).
    // 3/3 görev tamamlanınca ⭐ "Gün Tamam" rozeti kazanılır.
    let tasks = [false, false, false];
    const taskDate = new Date().toDateString();

    /**
     * loadTasks — Günlük görevleri sunucudan yükler.
     *
     * Önce api.php → tasks_get ile kullanıcıya ait görev durumunu çeker.
     * Sunucu yanıt vermezse localStorage'daki tarih kontrolüne düşer:
     *  - Kayıtlı tarih bugünle eşleşmiyorsa görevler sıfırlanır.
     *  - Eşleşiyorsa son kaydedilen durum geri yüklenir.
     * Her iki durumda da ilgili checkbox'lar (t1/t2/t3) ayarlanır.
     */
    async function loadTasks() {
      const r = await api('tasks_get', {}, 'GET');
      if (r.success) {
        tasks = r.data.tasks || [false, false, false];
      } else {
        // fallback: localStorage with date key
        const savedDate = localStorage.getItem('cm_tasks_date');
        if (savedDate !== taskDate) {
          tasks = [false, false, false];
          localStorage.setItem('cm_tasks_date', taskDate);
          localStorage.removeItem('cm_tasks');
        } else {
          tasks = JSON.parse(localStorage.getItem('cm_tasks') || '[false,false,false]');
        }
      }
      ['t1', 't2', 't3'].forEach((id, i) => {
        const el = document.getElementById(id);
        if (el) el.checked = !!tasks[i];
      });
      renderTasks();
    }

    ['t1', 't2', 't3'].forEach((id, i) => {
      const el = document.getElementById(id);
      if (!el) return;
      el.addEventListener('change', async () => {
        tasks[i] = el.checked;
        // Save to server
        await api('tasks_save', {
          tasks: JSON.stringify(tasks)
        });
        // Also save locally as backup
        localStorage.setItem('cm_tasks', JSON.stringify(tasks));
        localStorage.setItem('cm_tasks_date', taskDate);
        renderTasks();
        if (i === 1 && el.checked) checkBadge('plate', true);
        if (i === 2 && el.checked) checkBadge('salt', true);
      });
    });

    /**
     * renderTasks — Görev ilerleme sayacını günceller ve rozet kontrolü yapar.
     *
     * tasks dizisindeki true değerleri sayar.
     * 3/3 tamamlanırsa 'tasks' (Gün Tamam ⭐) günlük rozeti kazandırılır.
     * Aynı gün içinde iki kez kazanılmasını önlemek için cm_daily_badges_earned
     * localStorage kaydı kullanılır (sunucu tarafındaki DATE filtresiyle desteklenir).
     */
    function renderTasks() {
      const done = tasks.filter(Boolean).length;
      document.getElementById('taskProgress').textContent = done + '/3';
      if (done >= 3) {
        // Date-based check: only award if not already earned today
        const earnedToday = JSON.parse(localStorage.getItem('cm_daily_badges_earned') || '{}');
        if (!earnedToday['tasks']) {
          earnedToday['tasks'] = todayDateStr;
          localStorage.setItem('cm_daily_badges_earned', JSON.stringify(earnedToday));
          checkBadge('tasks', true);
        }
      }
    }

    // ════════════════════════════════════════════ AKTİVİTE
    // Uyku (💤 rest), Yürüyüş (🚶 move), Meyve/Ara öğün (🍎 snack) butonları.
    // Her buton tıklanınca checkBadge() ile ilgili günlük rozet kazandırılır.
    // Aktivite butonları sayfada .activity-row içinde gösterilir.
    // activityFeedback elemanı seçim sonrası kısa onay mesajı gösterir.
    const feedback = document.getElementById('activityFeedback');
    document.getElementById('btnLogSleep').addEventListener('click', () => {
      const last = localStorage.getItem('lastSleep');
      const today = new Date().toDateString();
      if (last !== today) {
        localStorage.setItem('lastSleep', today);
        checkBadge('rest', true);
        feedback.textContent = 'Harika! İyi uyku kaydedildi.';
        toast('😴 7+ saat uyku kaydedildi!', 'success');
      } else {
        toast('Bugün zaten kaydedildi!', 'info');
      }
    });
    document.getElementById('btnLogMove').addEventListener('click', () => {
      checkBadge('move', true);
      toast('🚶 20dk yürüyüş!', 'success');
      feedback.textContent = 'Hareket harikası!';
    });
    document.getElementById('btnLogSnack').addEventListener('click', () => {
      checkBadge('snack', true);
      toast('🍎 Sağlıklı ara öğün!', 'success');
      feedback.textContent = 'Meyve seçimi sağlıklı!';
    });

    // ════════════════════════════════════════════ VÜCUT PROFİLİ
    // bodyProfile: kilo, boy, yaş, cinsiyet objesi.
    // Önce SERVER.profileJson (PHP'den), yoksa localStorage kullanılır.
    // renderBodyAnalysis(): Mifflin-St Jeor formülü ile BMR, TDEE, VKİ hesaplar.
    // saveBodyProfile(): user_meta tablosuna JSON olarak kaydeder.
    // Profil güncellendiğinde su hedefi (waterTarget) de yeniden hesaplanır.
    let bodyProfile = SERVER.profileJson || JSON.parse(localStorage.getItem('cm_profile') || 'null');

    /**
     * renderBodyAnalysis — Vücut profili analizini hesaplayıp ekrana yazar.
     *
     * Mifflin-St Jeor formülü ile hesaplar:
     *  - BMR  (Bazal Metabolizma Hızı): dinlenme halinde yakılan kalori.
     *  - TDEE (Günlük Enerji İhtiyacı): aktivite çarpanı 1.375 (hafif aktif).
     *  - VKİ  (Vücut Kitle İndeksi): kilo / boy² (zayıf / normal / fazla / obez).
     *  - Su Hedefi: ağırlık × 35 ml (min 1500, max 3500 ml/gün).
     * Hesaplanan su hedefi waterTarget değişkenine yazılır.
     * Ayarlar formu mevcut profil değerleriyle doldurulur.
     */
    function renderBodyAnalysis() {
      const c = document.getElementById('bodyAnalysisContent');
      if (!bodyProfile) {
        c.innerHTML = '<p style="color:var(--muted);text-align:center;padding:20px;">Profilini kur →</p>';
        return;
      }
      const p = bodyProfile;
      let bmr = (10 * p.weight) + (6.25 * p.height) - (5 * p.age) + (p.gender === 'male' ? 5 : -161);
      bmr = Math.round(bmr);
      const tdee = Math.round(bmr * 1.375);
      const h = p.height / 100;
      const bmi = Math.round(p.weight / (h * h) * 10) / 10;
      let bmiS = 'Normal',
        bmiC = '#22c55e';
      if (bmi < 18.5) {
        bmiS = 'Zayıf';
        bmiC = '#fbbf24';
      } else if (bmi >= 25 && bmi < 30) {
        bmiS = 'Fazla Kilolu';
        bmiC = '#f97316';
      } else if (bmi >= 30) {
        bmiS = 'Obez';
        bmiC = '#ef4444';
      }
      const wt = Math.round(p.weight * 35);
      waterTarget = Math.max(1500, Math.min(3500, wt));
      c.innerHTML = `<div class="analysis-grid">
    <div class="mini-stat"><label>BMR</label><span>${bmr} kcal</span></div>
    <div class="mini-stat"><label>TDEE</label><span>${tdee} kcal</span></div>
  </div><div class="analysis-grid">
    <div class="mini-stat"><label>VKİ</label><span>${bmi} <span class="bmi-badge" style="background:${bmiC}20;color:${bmiC}">${bmiS}</span></span></div>
    <div class="mini-stat"><label>Su Hedefi</label><span>${wt} ml</span></div>
  </div>`;
      // Ayarlar formuna doldur
      ['Weight', 'Height', 'Age', 'Gender'].forEach(k => {
        const el = document.getElementById('ps' + k) || document.getElementById('input' + k);
        if (el && p[k.toLowerCase()] !== undefined) el.value = p[k.toLowerCase()];
      });
      document.getElementById('psWeight').value = p.weight || '';
      document.getElementById('psHeight').value = p.height || '';
      document.getElementById('psAge').value = p.age || '';
      document.getElementById('psGender').value = p.gender || 'female';
      document.getElementById('inputWeight').value = p.weight || '';
      document.getElementById('inputHeight').value = p.height || '';
      document.getElementById('inputAge').value = p.age || '';
      document.getElementById('inputGender').value = p.gender || 'female';
      renderWater();
    }

    /**
     * saveBodyProfile — Vücut profilini hem yerel hem sunucu tarafına kaydeder.
     *
     * @param {object} p - {weight, height, age, gender} profil nesnesi.
     *
     * localStorage'a yazar (çevrimdışı erişim için), ardından api.php →
     * profile_meta_save ile user_meta tablosuna JSON olarak kaydeder.
     * Kayıt sonrası renderBodyAnalysis() çağrılarak arayüz güncellenir.
     */
    async function saveBodyProfile(p) {
      bodyProfile = p;
      localStorage.setItem('cm_profile', JSON.stringify(p));
      await api('profile_meta_save', {
        meta_key: 'body_profile',
        meta_value: JSON.stringify(p)
      });
      renderBodyAnalysis();
    }

    // Profil Modalı: Vücut Analizi panelindeki "Profili Düzenle" butonuyla açılır.
    // body.style.overflow='hidden' → Modal açıkken sayfa scroll'u kilitlenir.
    // Kapanma tetikleyicileri: kapat butonu, overlay tıklaması, ESC tuşu.
    const _profileModal = document.getElementById('profileModal');
    const _btnOpenProfile = document.getElementById('btnOpenProfile');
    const _closeProfileBtn = document.getElementById('closeProfile');

    function openProfileModal() {
      _profileModal.classList.add('active');
      document.body.style.overflow = 'hidden'; // scroll kilitle
    }
    function closeProfileModal() {
      _profileModal.classList.remove('active');
      document.body.style.overflow = '';       // scroll serbest bırak
    }

    _btnOpenProfile.addEventListener('click', openProfileModal);
    _closeProfileBtn.addEventListener('click', closeProfileModal);

    // Overlay dışına tıklayınca kapat (e.target = overlay'in kendisi olmalı)
    _profileModal.addEventListener('click', function(e) {
      if (e.target === _profileModal) closeProfileModal();
    });

    // ESC tuşuyla kapat
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && _profileModal.classList.contains('active')) {
        closeProfileModal();
      }
    });
    document.getElementById('profileForm').addEventListener('submit', async e => {
      e.preventDefault();
      const p = {
        weight: parseFloat(document.getElementById('inputWeight').value),
        height: parseFloat(document.getElementById('inputHeight').value),
        age: parseInt(document.getElementById('inputAge').value),
        gender: document.getElementById('inputGender').value
      };
      await saveBodyProfile(p);
      closeProfileModal();
      toast('Profil güncellendi!', 'success');
    });
    document.getElementById('bodyProfileForm').addEventListener('submit', async e => {
      e.preventDefault();
      const p = {
        weight: parseFloat(document.getElementById('psWeight').value),
        height: parseFloat(document.getElementById('psHeight').value),
        age: parseInt(document.getElementById('psAge').value),
        gender: document.getElementById('psGender').value
      };
      await saveBodyProfile(p);
      toast('Vücut profili kaydedildi!', 'success');
    });

    // ════════════════════════════════════════════ ROZETLER
    // BADGES_CONFIG: 11 rozet tanımı (id, ikon, başlık, açıklama, daily bayrağı).
    // earnedBadges: api'den yüklenen kazanılmış rozet anahtarları {id: true}.
    // checkBadge(id, met): Koşul sağlandıysa api.php → badge_earn çağırır,
    //   rozet ilk kez kazanılıyorsa konfeti patlatır ve toast gösterir.
    // Günlük rozetler (daily:true) her gün sıfırlanır — lastBadgeDate kontrolü.
    // ── Tarih tabanlı günlük rozet sıfırlama ────────────────────
    const todayDateStr = new Date().toISOString().slice(0, 10);
    const lastBadgeDate = localStorage.getItem('cm_last_badge_date');
    if (lastBadgeDate !== todayDateStr) {
      // Yeni gün — günlük rozetleri sıfırla (sadece günlük olanlar)
      const dailyBadges = ['water', 'plate', 'salt', 'rest', 'move', 'snack', 'tasks'];
      const savedBadges = JSON.parse(localStorage.getItem('cm_daily_badges_earned') || '{}');
      // Reset flag
      localStorage.setItem('cm_last_badge_date', todayDateStr);
      localStorage.setItem('cm_daily_badges_earned', '{}');
      // Server-side günlük rozetleri sıfırla
      api('badge_reset_daily', { daily_keys: dailyBadges.join(','), reset_date: todayDateStr })
        .catch(() => {});
    }

    const BADGES_CONFIG = [
      {id:'water',      icon:'💧', title:'Su Ustası',       desc:'Günlük su hedefini tamamla.', daily:true},
      {id:'plate',      icon:'🥗', title:'Renkli Tabak',    desc:'Sebze + meyve ekledin.', daily:true},
      {id:'chef',       icon:'🍳', title:'Ev Şefi',         desc:'1 sağlıklı tarif yaptın.'},
      {id:'salt',       icon:'🧂', title:'Tuz Kontrolcüsü', desc:'Tuzluğu masaya koymadın.', daily:true},
      {id:'rest',       icon:'💤', title:'Dinlenme Ustası',  desc:'7+ saat uyku işaretledin.', daily:true},
      {id:'move',       icon:'🚶‍♀️', title:'Hareket Ettim', desc:'20 dakika yürüyüş yaptın.', daily:true},
      {id:'snack',      icon:'🍎', title:'Ara Öğün Bilgesi', desc:'Meyve/yoğurt seçtin.', daily:true},
      {id:'tips',       icon:'🧠', title:'Bilinçli Seçim',  desc:'Sağlık önerisini okudun.', daily:true},
      {id:'tasks',      icon:'⭐', title:'Gün Tamam',        desc:'3 günlük görevi tamamladın.', daily:true},
      {id:'waste_hero', icon:'♻️', title:'Atık Kahramanı',  desc:'İlk atık kaydını yaptın.'},
      {id:'first_recipe',icon:'📖',title:'İlk Tarifim',     desc:'İlk tarifini paylaştın.'}
    ];
    let earnedBadges = {};
    let dailyBadgeDates = {}; // tarih tabanlı günlük rozet kontrolü

    /**
     * loadBadges — Kullanıcının kazanılmış rozetlerini sunucudan çeker.
     *
     * İki API çağrısı yapar:
     *  1. badge_list       → earnedBadges nesnesini doldurur.
     *                        Günlük rozetler sadece BUGÜN kazanılmışsa listede yer alır
     *                        (sunucu DATE filtresi); ertesi gün çağrı tekrarlanınca
     *                        günlük rozetler otomatik "sıfırlanmış" görünür.
     *  2. badge_daily_status → dailyBadgeDates nesnesini doldurur.
     *                        {badge_key: 'YYYY-MM-DD'} formatı; checkBadge içinde
     *                        duplikat kazanımı önlemek için kullanılır.
     */
    async function loadBadges() {
      try {
        const r = await api('badge_list', {}, 'GET');
        if (r.success && Array.isArray(r.data)) {
          earnedBadges    = {};
          dailyBadgeDates = {};
          r.data.forEach(b => {
            earnedBadges[b.badge_key] = true;
            if (b.earned_date) dailyBadgeDates[b.badge_key] = String(b.earned_date).slice(0,10);
          });
        }
      } catch(e) {}
      renderBadgesPage();
      updateBadgeCount();
    }

    /**
     * checkBadge — Rozet kazanılma koşulunu kontrol eder ve kayıt eder.
     *
     * @param {string}  id  - BADGES_CONFIG içindeki rozet kimliği (örn: 'water', 'chef')
     * @param {boolean} met - Rozet koşulunun sağlanıp sağlanmadığı
     *
     * Çalışma mantığı:
     *  1. Koşul sağlanmadıysa (met=false) hiçbir şey yapma.
     *  2. Günlük rozet ise: sunucudan alınan dailyBadgeDates kontrolüyle
     *     bugün zaten kazanıldıysa atla — sıfırlama sadece gün değişince olur.
     *  3. Kalıcı rozet ise: daha önce kazanıldıysa atla.
     *  4. API'ye kaydet; başarılı dönüşte UI'ı güncelle (konfeti + toast).
     */
    async function checkBadge(id, met) {
      if (!met) return;

      const cfg   = BADGES_CONFIG.find(b => b.id === id);
      const today = new Date().toISOString().slice(0, 10);

      // Günlük rozet: bugün zaten kazanıldıysa atla
      if (cfg && cfg.daily && dailyBadgeDates[id] === today) return;
      // Kalıcı rozet: zaten kazanıldıysa atla
      if (cfg && !cfg.daily && earnedBadges[id]) return;

      // DB'ye kaydet (arka planda — UI beklemiyor)
      const body = { badge_key: id, badge_name: cfg ? cfg.title : id };
      if (cfg && cfg.daily) body.is_daily = '1';
      api('badge_earn', body).catch(() => {});

      // Hemen cache + UI güncelle
      earnedBadges[id] = true;
      if (cfg && cfg.daily) dailyBadgeDates[id] = today;
      renderBadgesPage();
      updateBadgeCount();
      toast('🎉 Yeni Rozet: ' + (cfg ? cfg.title : id) + '!', 'success');
      confettiBoom();
      const s = document.getElementById('statBadges');
      if (s) s.textContent = Object.keys(earnedBadges).length;
    }

    /**
     * updateBadgeCount — Header/genel bakış rozet sayacını günceller.
     * Kazanılan rozet adedini BADGES_CONFIG toplam uzunluğuyla karşılaştırır.
     */
    function updateBadgeCount() {
      const got = Object.keys(earnedBadges).length;
      document.getElementById('badgeCount').textContent = got + '/' + BADGES_CONFIG.length;
    }

    /**
     * renderBadgesPage — Rozetler sayfasındaki ızgarayı (badgesFullGrid) çizer.
     *
     * Her rozet kartı için:
     *  - Kazanılmış (unlocked) veya kilitli (locked) CSS sınıfı atar.
     *  - Günlük rozetlerde:
     *      • Bugün kazanıldıysa         → "✅ Bugün Kazanıldı"
     *      • Önceki gün kazanılmış ise  → "🔄 Yarın Yenilenebilir"
     *      • Hiç kazanılmamışsa         → "🔒 Kilitli"
     *  - Kalıcı rozetlerde: "✅ Kazanıldı" veya "🔒 Kilitli"
     */
    function renderBadgesPage() {
      const today = new Date().toISOString().slice(0, 10);
      document.getElementById('badgesFullGrid').innerHTML = BADGES_CONFIG.map(b => {
        const on = !!earnedBadges[b.id];
        // Günlük rozet: dailyBadgeDates'teki tarih bugünle eşleşiyor mu?
        // slice(0,10) ile datetime formatı da güvenle karşılaştırılır.
        const earnedToday = b.daily
          ? !!(dailyBadgeDates[b.id] && String(dailyBadgeDates[b.id]).slice(0,10) === today)
          : false;
        const active = b.daily ? earnedToday : on;
        let status;
        if (b.daily) {
          if (earnedToday)  status = '✅ Bugün Kazanıldı';
          else if (on)      status = '🔄 Yarın Yenilenebilir';
          else              status = '🔒 Kilitli';
        } else {
          status = on ? '✅ Kazanıldı' : '🔒 Kilitli';
        }
        return `<div class="badge-card ${active?'unlocked':'locked'}">
      <div class="bc-icon">${b.icon}</div>
      <h4>${esc(b.title)}</h4>
      <p>${esc(b.desc)}</p>
      ${b.daily ? '<span style="font-size:.68rem;color:var(--muted);display:block;margin-top:2px;">🔄 Günlük rozet</span>' : ''}
      <span class="badge-status">${status}</span>
    </div>`;
      }).join('');
    }

    /**
     * confettiBoom — Ekrana konfeti yağmuru animasyonu başlatır.
     * Rozet kazanılınca çağrılır; 3.5 saniye sonra temizlenir.
     */
    function confettiBoom() {
      const el = document.getElementById('confetti');
      if (!el) return;
      el.innerHTML = '';
      el.classList.add('show');
      const colors = ['rgba(255,183,3,.95)', 'rgba(34,197,94,.92)', 'rgba(120,180,255,.92)', 'rgba(255,107,107,.92)'];
      for (let i = 0; i < 80; i++) {
        const p = document.createElement('i');
        p.style.cssText = `left:${Math.random()*100}vw;animation-duration:${2+Math.random()*2}s;animation-delay:${Math.random()*.25}s;width:${6+Math.random()*8}px;height:${8+Math.random()*10}px;background:${colors[Math.floor(Math.random()*colors.length)]}`;
        el.appendChild(p);
      }
      setTimeout(() => el.classList.remove('show'), 3500);
    }

    // ════════════════════════════════════════════ DİJİTAL DOLAP (fridge_items kullanılır)
    // cabinetItems: api.php → fridge_list'ten gelen malzeme dizisi.
    // renderCabinet(): SKT'ye göre renk kodu atar (yeşil/sarı/kırmızı).
    // Malzeme silinince AI tarif önerileri de güncellenir (loadAiRecipes tetiklenir).
    let cabinetItems = [];

    /**
     * loadCabinet — Dijital dolap malzemelerini sunucudan çeker ve listeler.
     * api.php → fridge_list endpoint'ini kullanır.
     * Başarı durumunda cabinetItems güncellenir ve renderCabinet() tetiklenir.
     */
    async function loadCabinet() {
      const r = await api('fridge_list', {}, 'GET');
      if (r.success) {
        cabinetItems = r.data;
        renderCabinet();
      }
    }

    /**
     * renderCabinet — Dolap malzeme ızgarasını filtreler ve DOM'a yazar.
     *
     * Arama kutusu değerine göre cabinetItems listesini filtreler.
     * Her malzeme için son kullanma tarihi (expiry_date) hesaplanır:
     *  - < 0 gün  → kırmızı "⛔ Geçti!" etiketi
     *  - 0–2 gün  → sarı "⚠️ N gün kaldı" etiketi
     *  - 3+ gün   → yeşil "✅ N gün kaldı" etiketi
     */
    function renderCabinet() {
      const q = (document.getElementById('cabSearch')?.value || '').toLowerCase();
      const list = cabinetItems.filter(i => i.name.toLowerCase().includes(q));
      const countEl = document.getElementById('cabCountPill');
      if (countEl) countEl.textContent = list.length + ' malzeme';
      const gridEl = document.getElementById('cabinetListGrid');
      if (!gridEl) return;
      gridEl.innerHTML = list.map(item => {
        let expiryClass = '',
          expiryLabel = '';
        if (item.expiry_date) {
          const diff = Math.ceil((new Date(item.expiry_date) - new Date()) / 86400000);
          if (diff < 0) {
            expiryClass = 'expiry-crit';
            expiryLabel = '⛔ Geçti!';
          } else if (diff < 3) {
            expiryClass = 'expiry-warn';
            expiryLabel = '⚠️ ' + diff + ' gün kaldı';
          } else {
            expiryClass = 'expiry-ok';
            expiryLabel = '✅ ' + diff + ' gün kaldı';
          }
        }
        return `<div class="cabinet-card">
      <div class="cc-icon">🥘</div>
      <div class="cc-info">
        <div class="cc-name">${esc(item.name)}</div>
        <div class="cc-meta">${esc(item.quantity||'')} ${item.category?'• '+esc(item.category):''}</div>
        ${expiryLabel?`<span class="cc-expiry ${expiryClass}">${expiryLabel}</span>`:''}
      </div>
      <button class="btn-sm btn-red" onclick="deleteCabinetItem(${item.id})">🗑️</button>
    </div>`;
      }).join('') || '<p style="color:var(--muted);text-align:center;padding:20px;">Buzdolabınız boş.</p>';
    }

    /**
     * deleteCabinetItem — Dolaptaki malzemeyi siler.
     *
     * @param {number} id - Silinecek malzemenin veritabanı ID'si.
     *
     * api.php → fridge_delete ile siler; ardından dolabı yeniler.
     * Malzeme değişince AI tarif önerileri de 400ms gecikmeyle güncellenir.
     */
    async function deleteCabinetItem(id) {
      const r = await api('fridge_delete', {
        id
      });
      if (r.success) {
        await loadCabinet();
        toast('Malzeme silindi', 'success');
        setTimeout(() => loadAiRecipes(true), 400);
      } else toast(r.message, 'error');
    }

    document.getElementById('btnCabAdd')?.addEventListener('click', async () => {
      const name = document.getElementById('cabName')?.value.trim();
      if (!name) {
        toast('Malzeme adı girin!', 'error');
        return;
      }
      const r = await api('fridge_add', {
        name,
        expiry_date: document.getElementById('cabExpiry')?.value || '',
        shelf: 'shelf-1'
      });
      if (r.success) {
        ['cabName', 'cabQty', 'cabCategory', 'cabExpiry'].forEach(id => {
          const el = document.getElementById(id);
          if (el) el.value = '';
        });
        await loadCabinet();
        toast('Malzeme eklendi!', 'success');
        const s = document.getElementById('statCabinet');
        if (s) s.textContent = cabinetItems.length;
        setTimeout(() => loadAiRecipes(true), 400);
        // SKT aralıktaysa bildirim hemen gelsin
        try { await api('notifications_sync_fridge', {}, 'GET'); } catch {}
        await loadNotifications();
      } else toast(r.message, 'error');
    });
    document.getElementById('cabSearch')?.addEventListener('input', renderCabinet);

    // ════════════════════════════════════════════ ALIŞVERİŞ LİSTESİ
    // shopItems: api.php → shop_list endpoint'inden gelen dizi.
    // loadShop()    → Listeyi sunucudan çeker, shopItems'ı günceller.
    // renderShop()  → Tamamlanan/toplam sayıyı pill'e yazar; her kaleme
    //                 checkbox (toggleShopItem) ve sil butonu (deleteShopItem) ekler.
    // btnShopAdd    → Yeni kalem ekle: api.php → shop_add.
    // btnShopClearDone → Tamamlananları toplu sil (Promise.all ile paralel istek).
    // btnShopClearAll  → Onay sonrası tüm listeyi sıfırla.

    /**
     * loadShop — Alışveriş listesini sunucudan yükler.
     * api.php → shop_list endpoint'ini kullanır.
     * shopItems dizisini günceller ve renderShop() ile arayüzü yeniler.
     */
    async function loadShop() {
      const r = await api('shop_list', {}, 'GET');
      if (r.success) {
        shopItems = r.data;
        renderShop();
      }
    }

    /**
     * renderShop — Alışveriş listesi arayüzünü çizer.
     *
     * Tamamlanan/toplam kalem sayısını progress pill'ine yazar.
     * Tamamlanan kalemlere 'done-item' CSS sınıfı ekler.
     * Her kalem için checkbox (toggle) ve silme butonu üretir.
     */
    function renderShop() {
      const done = shopItems.filter(i => i.is_done == 1).length;
      document.getElementById('shopProgressPill').textContent = done + '/' + shopItems.length;
      document.getElementById('statShop').textContent = shopItems.filter(i => !i.is_done).length;
      document.getElementById('shopListWrap').innerHTML = shopItems.map(item => `
    <div class="shop-item-row ${item.is_done==1?'done-item':''}">
      <input type="checkbox" ${item.is_done==1?'checked':''} onchange="toggleShopItem(${item.id})">
      <span class="si-name">${esc(item.name)}</span>
      ${item.quantity?`<span class="si-qty">${esc(item.quantity)}</span>`:''}
      <button class="btn-sm btn-red" onclick="deleteShopItem(${item.id})">✖</button>
    </div>
  `).join('') || '<p style="color:var(--muted);text-align:center;padding:20px;">Liste boş.</p>';
    }

    async function toggleShopItem(id) {
      await api('shop_toggle', {
        id
      });
      await loadShop();
    }
    async function deleteShopItem(id) {
      const r = await api('shop_delete', {
        id
      });
      if (r.success) {
        await loadShop();
        toast('Silindi', 'success');
      }
    }
    document.getElementById('btnShopAdd').addEventListener('click', async () => {
      const name = document.getElementById('shopNewName').value.trim();
      if (!name) {
        toast('Ürün adı girin!', 'error');
        return;
      }
      const r = await api('shop_add', {
        name,
        quantity: document.getElementById('shopNewQty').value.trim()
      });
      if (r.success) {
        document.getElementById('shopNewName').value = '';
        document.getElementById('shopNewQty').value = '';
        await loadShop();
        toast('Eklendi!', 'success');
      } else toast(r.message, 'error');
    });
    document.getElementById('btnShopClearDone').addEventListener('click', async () => {
      const done = shopItems.filter(i => i.is_done == 1);
      await Promise.all(done.map(i => api('shop_delete', {
        id: i.id
      })));
      await loadShop();
      toast('Tamamlananlar temizlendi', 'success');
    });
    document.getElementById('btnShopClearAll').addEventListener('click', async () => {
      if (!confirm('Listeyi tamamen sıfırlayacaksınız. Emin misiniz?')) return;
      await Promise.all(shopItems.map(i => api('shop_delete', {
        id: i.id
      })));
      await loadShop();
      toast('Liste sıfırlandı', 'info');
    });
    document.getElementById('btnShopPrint').addEventListener('click', () => window.print());

    // ════════════════════════════════════════════ TARİF DEFTERİM (Veritabanı)
    // myRecipes    : api.php → recipe_list ile çekilen kayıtlı tarif dizisi.
    // loadMyRecipes() → Tarifleri sunucudan çeker; kullanıcının eklediği
    //                   tarifler (user_recipe_list) de ilk açılışta yüklenir.
    // renderMyRecipes() → Kart ızgarasını oluşturur; detay (openMyRecipe)
    //                     ve sil (deleteMyRecipe) butonları içerir.
    // openBookView() → tarif_defteri.php'yi modal içinde iframe olarak açar.
    let myRecipes = [];
    let rbCurrentId = null;

    /**
     * loadMyRecipes — Tarif defterini (kaydedilen & kullanıcı tarifleri) yükler.
     *
     * api.php → recipe_list ile kayıtlı tarifleri çeker ve renderMyRecipes() çağırır.
     * Kullanıcının kendi eklediği tarifleri (user_recipe_list) de ilk açılışta yükler.
     */
    async function loadMyRecipes() {
      const r = await api('recipe_list', {}, 'GET');
      if (r.success) {
        myRecipes = r.data;
        renderMyRecipes();
      }
      // Kullanıcı tariflerini de ilk açılışta yükle
      if (userRecOffset === 0 && allUserRecs.length === 0) loadUserRecipesList();
    }

    function renderMyRecipes() {
      document.getElementById('rbCountPill').textContent = myRecipes.length + ' tarif';
      const s = document.getElementById('statRecipes');
      if (s) s.textContent = myRecipes.length;
      document.getElementById('recipeBookList').innerHTML = myRecipes.map(rec => `
    <div class="recipe-book-card">
      ${rec.image_url?`<div class="rbc-img" style="background-image:url('${esc(rec.image_url)}')"></div>`:'<div class="rbc-img" style="background:linear-gradient(135deg,#1a1a1a,#2a2a2a);display:grid;place-items:center;font-size:2rem;">📖</div>'}
      <div class="rbc-body">
        <h4>${esc(rec.title)}</h4>
        <p style="font-size:.8rem;">${rec.source?'🔗 '+esc(rec.source.length>40?rec.source.substring(0,40)+'...':rec.source):'Kişisel tarif'}</p>
        ${rec.ingredients?`<p style="font-size:.78rem;color:var(--muted);margin-top:4px;">🧺 ${esc(rec.ingredients.split('\n').slice(0,2).join(', '))}${rec.ingredients.split('\n').length>2?'...':''}</p>`:''}
        <div class="rbc-actions">
          <button class="btn-sm btn-gold" onclick="openMyRecipe(${rec.id})">👁️ Detay</button>
          <button class="btn-sm btn-red" onclick="deleteMyRecipe(${rec.id})">🗑️</button>
        </div>
      </div>
    </div>
  `).join('') || '<p style="color:var(--muted);grid-column:1/-1;text-align:center;padding:30px;">Henüz tarif eklenmedi.<br><a href="tarifler" style="color:#cfae55;">Tariflere git & kaydet →</a></p>';
    }

    // ── Kitap Görünümü (tarif_defteri.php iframe) ────────────────
    // openBookView() : Tam ekran modal içinde tarif_defteri.php'yi iframe yükler.
    // closeBookView(): İframe kaldırılır (src temizlenir) — bellek için önemli.
    // Kitap görünümü dashboard içinde ayrı bir sayfa gibi deneyim sunar.
    function openBookView() {
      document.getElementById('bookModal').style.display = 'flex';
      const container = document.getElementById('bookContainer');
      container.innerHTML = `<iframe src="recipe-book" style="width:100%;height:100%;border:none;" id="bookIframe"></iframe>`;
    }

    function closeBookView() {
      document.getElementById('bookModal').style.display = 'none';
      document.getElementById('bookContainer').innerHTML = '';
    }

    function openMyRecipe(id) {
      const rec = myRecipes.find(r => r.id == id);
      if (!rec) return;
      rbCurrentId = id;
      document.getElementById('rbModalTitle').textContent = rec.title;
      const imgWrap = document.getElementById('rbModalImgWrap');
      imgWrap.innerHTML = rec.image_url ? `<img src="${esc(rec.image_url)}" style="width:100%;height:200px;object-fit:cover;border-radius:12px;margin-bottom:12px;">` : '';
      document.getElementById('rbModalIng').innerHTML = (rec.ingredients || '').split('\n').filter(Boolean).map(l => `<div>• ${esc(l)}</div>`).join('') || '<span style="color:var(--muted)">—</span>';
      document.getElementById('rbModalSteps').innerHTML = (rec.instructions || '').split('\n').filter(Boolean).map((l, i) => `<div>${i+1}. ${esc(l)}</div>`).join('') || '<span style="color:var(--muted)">—</span>';
      const srcBtn = document.getElementById('rbModalSource');
      if (rec.source && rec.source.startsWith('http')) {
        srcBtn.href = rec.source;
        srcBtn.style.display = 'inline-block';
      } else {
        srcBtn.style.display = 'none';
      }
      document.getElementById('rbModal').style.display = 'flex';
    }

    async function deleteMyRecipe(id) {
      if (!confirm('Bu tarifi silmek istediğinize emin misiniz?')) return;
      const r = await api('recipe_delete', {
        id
      });
      if (r.success) {
        await loadMyRecipes();
        toast('Tarif silindi', 'success');
      } else toast(r.message, 'error');
    }

    document.getElementById('rbModalDelete').addEventListener('click', async () => {
      if (rbCurrentId) await deleteMyRecipe(rbCurrentId);
      document.getElementById('rbModal').style.display = 'none';
    });

    // ════════════════════════════════════════════ KULLANICI TARİFLERİ (Tarif Defterim)
    // Topluluk tarafından eklenen tarifler api.php → user_recipe_list ile sayfalanarak yüklenir.
    // userRecOffset : offset değeri — her "Daha Fazla Yükle" tıklamasında 12 artar.
    // userRecLoading: eş zamanlı istek gitmesini engelleyen bayrak (debounce).
    // allUserRecs   : o ana kadar yüklenen tüm tarif nesnelerinin dizisi.
    // append=true   : Mevcut listenin üstüne ekleme yapar (sıfırlamaz).
    let userRecOffset = 0, userRecLoading = false, allUserRecs = [];
    async function loadUserRecipesList(append = false) {
      if (userRecLoading) return;
      userRecLoading = true;
      const grid = document.getElementById('userRecipeGrid');
      const loadMoreBtn = document.getElementById('userRecLoadMore');
      if (!append) { grid.innerHTML = '<p style="color:var(--muted);text-align:center;padding:20px;">⏳ Yükleniyor...</p>'; userRecOffset = 0; allUserRecs = []; }
      try {
        const res = await fetch('api/user-recipes?offset=' + userRecOffset + '&limit=12');
        const json = await res.json();
        if (!json.success) { grid.innerHTML = '<p style="color:var(--muted);text-align:center;padding:20px;">Tarif bulunamadı.</p>'; return; }
        const items = json.data.items || [];
        allUserRecs = append ? [...allUserRecs, ...items] : items;
        document.getElementById('userRecCountPill').textContent = (json.data.total || 0) + ' tarif';
        renderUserRecipesList();
        userRecOffset += items.length;
        if (loadMoreBtn) loadMoreBtn.style.display = json.data.hasMore ? 'block' : 'none';
      } catch(e) { grid.innerHTML = '<p style="color:var(--muted);text-align:center;padding:20px;">Yükleme hatası.</p>'; }
      userRecLoading = false;
    }
    function renderUserRecipesList() {
      const q = (document.getElementById('userRecSearch')?.value || '').toLowerCase();
      const list = allUserRecs.filter(r => r.title.toLowerCase().includes(q));
      const grid = document.getElementById('userRecipeGrid');
      if (!grid) return;
      grid.innerHTML = list.map(item => {
        const imgStyle = item.image_url ? `background:url('${esc(item.image_url)}') center/cover` : 'background:linear-gradient(135deg,#1a1a1a,#2d2d2d)';
        return `<div style="background:var(--card);border:1px solid var(--line);border-radius:14px;overflow:hidden;cursor:pointer;transition:transform .15s,box-shadow .15s;" onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,.15)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
          <div style="height:140px;${imgStyle};display:flex;align-items:flex-end;padding:8px;">
            <span style="background:rgba(0,0,0,.65);color:#cfae55;font-size:.68rem;font-weight:700;padding:3px 9px;border-radius:6px;">👤 ${esc(item.user_name||'Kullanıcı')}</span>
          </div>
          <div style="padding:12px;">
            <h4 style="font-size:.92rem;font-weight:700;color:var(--ink);margin-bottom:5px;">${esc(item.title)}</h4>
            ${item.ingredients_preview ? `<p style="font-size:.76rem;color:var(--muted);margin-bottom:6px;">${esc(item.ingredients_preview)}</p>` : ''}
            <div style="font-size:.7rem;color:var(--muted);margin-bottom:8px;">📅 ${esc(item.saved_at||'')}</div>
            <button class="btn-sm btn-red" style="font-size:.75rem;padding:5px 10px;" onclick="deleteUserRecipe(${item.id})">🗑️ Sil</button>
          </div>
        </div>`;
      }).join('') || '<p style="color:var(--muted);grid-column:1/-1;text-align:center;padding:24px;">Henüz manuel tarif eklenmedi.</p>';
    }
    document.getElementById('userRecSearch')?.addEventListener('input', renderUserRecipesList);
    async function deleteUserRecipe(id) {
      if (!confirm('Bu tarifi silmek istiyor musunuz?')) return;
      const r = await api('user_recipe_delete', { id });
      if (r.success) { allUserRecs = allUserRecs.filter(x => x.id != id); renderUserRecipesList(); toast('Tarif silindi', 'success'); }
      else toast(r.message, 'error');
    }

    // ════════════════════════════════════════════ BADGE: günlük rozetler tarih tabanlı

    // ════════════════════════════════════════════════════════════════
    // CHEFMATE AI CARD ENGINE v2.0
    // Bu bölüm buzdolabındaki malzemeleri tarifteki malzemelerle karşılaştırır.
    // ─────────────────────────────────────────────────────────────────
    // aiParseIngredient(): "2 su bardağı taze süt" → "sut" token normalleştirme.
    //   Adımlar: TR karakter normalize → parantez sil → kesir/sayı temizle
    //   → birim/sıfat kelimeleri filtrele → sinonim eşleştir
    // aiDisplayName(): aiParseIngredient'ten farkı: sıfatları korur (sadece sayı/birim siler).
    //   Kullanıcıya gösterilen temiz malzeme adı için kullanılır.
    // normTR(): Türkçe karakterleri ASCII'ye çevirir (ğ→g, ü→u, ş→s vb.)
    //   Büyük/küçük harf ve karakter farklılıklarından bağımsız karşılaştırma sağlar.
    // UNIT_WORDS, NUM_WORDS, ADJ_WORDS: Malzeme adından çıkarılacak sözcük kümeleri.
    // SYNONYMS: "kuru soğan" → "sogan" gibi eşanlamlı malzeme eşleştirme tablosu.
    // buildAiCard(): Her tarif için uyum yüzdesi, eşleşen/eksik malzeme listesi,
    //   "Detay" ve "Menüye Ekle" butonlarını içeren HTML kartı üretir.
    // loadAiRecipes(): api.php → ai_recipe_recommendations ile verileri çeker,
    //   skeleton animasyonu gösterir, ardından kartları render eder.
    // ════════════════════════════════════════════════════════════════

    // ════════════════════════════════════════════════════════════════
    // CHEFMATE AI KART MOTORU v2.0 — Malzeme Eşleştirme Sistemi
    // ────────────────────────────────────────────────────────────────
    // Amaç: Buzdolabındaki malzemeleri tarif malzemeleriyle karşılaştırıp
    //       "uyum yüzdesi" (match_pct) hesaplamak ve tarif önerileri sunmak.
    //
    // normTR()           : Türkçe karakterleri (ş→s, ğ→g vb.) ASCII'ye çevirir.
    //                      Böylece "şeker" ile "seker" eşleşebilir.
    // UNIT_WORDS         : Ölçü birimleri (bardak, kaşık, kg, ml vb.) — parse'da atlanır.
    // NUM_WORDS          : Sayı kelimeleri (bir, iki, yarım vb.) — parse'da atlanır.
    // ADJ_WORDS          : Sıfatlar (taze, kuru, kırmızı vb.) — parse'da atlanır.
    // SYNONYMS           : Eş anlamlı malzemeleri tek token'a indirger.
    //                      Örn: "kaşar peyniri" → "peynir", "tavuk göğsü" → "tavuk".
    // aiParseIngredient(): Tüm bu setleri kullanarak "2 yemek kaşığı zeytinyağı"
    //                      gibi ham satırı "zeytinyagi" token'ına dönüştürür.
    // buildAiCard()      : Eşleşme yüzdesi + malzeme listesi + butonlar içeren HTML kartı.
    // loadAiRecipes()    : api.php → ai_recipe_recommendations çağrısı; skeleton→kart geçişi.
    // ════════════════════════════════════════════════════════════════

    /**
     * Türkçe karakter normalleştirme — PHP tarafındaki ai_normalize() fonksiyonunun JS kopyası.
     * "2 su bardağı taze süt" → "sut" (token)
     */
    const _TR_MAP = {'ş':'s','ğ':'g','ü':'u','ö':'o','ı':'i','ç':'c','â':'a','î':'i','û':'u'};
    function normTR(s) {
      const m = {'ş':'s','ğ':'g','ü':'u','ö':'o','ı':'i','ç':'c','â':'a','î':'i','û':'u','ê':'e','à':'a','é':'e'};
      return s.toLowerCase().replace(/[şğüöıçâîûêàé]/g, c => m[c] || c);
    }

    // UNIT_WORDS: Ölçü birimi kelimeleri — malzeme adından çıkarılır.
    const UNIT_WORDS = new Set([
      // Ölçü kapları
      'su','bardagi','bardağı','cay','çay','yemek','tatli','tatlı','kasigi','kaşığı',
      'kasik','kaşık','bardak','kase','kâse','fincan','fincani',
      // Ağırlık/hacim
      'kg','kilo','gr','gram','g','mg','ml','litre','lt','dl','cl','ccm','cc',
      // Sayım birimleri
      'adet','tane','demet','tutam','avuc','avuç','dilim','paket','kutu',
      'dal','dis','diş','bas','baş','kisim','kısım','porsiyon',
      'sise','şişe','teneke','torba','poset','poşet','salkım','salkim',
      'damla','top','rulo','yaprak',
    ]);
    const NUM_WORDS = new Set([
      'bir','iki','üç','uc','dört','dort','bes','beş','alti','altı','yedi','sekiz',
      'dokuz','on','yarım','yarim','çeyrek','ceyrek','birkaç','birkac',
      'az','bol','biraz','yeterince','yeterli',
    ]);
    const ADJ_WORDS = new Set([
      'kirmizi','kırmızı','yesil','yeşil','siyah','beyaz','sari','sarı','mor','turuncu',
      'iri','ince','kucuk','küçük','buyuk','büyük','orta','kalin','kalın',
      'taze','kuru','rendelenmis','rendelenmiş','dogranmis','doğranmış',
      'ezilmis','ezilmiş','kiymis','kıyılmış','dilimli','soyulmus','soyulmuş',
      'islatilmis','haslanmis','haşlanmış','pisirmis','pişirilmiş',
      'butun','bütün','sikilmis','kavrulmus','fileto','konserve','hazir','hazır',
      'tuzlu','tuzsuz','light','yagli','dogal','organik','tam',
      'olgun','istege','isteğe','bagli','bağlı',
    ]);
    const SYNONYMS = {
      'tavuk gogsu':'tavuk','tavuk but':'tavuk','pilic':'tavuk',
      'tavuk fillet':'tavuk','tavuk fileto':'tavuk','tavuk eti':'tavuk','tavuk kanat':'tavuk',
      'dana kiyma':'kiyma','kuzu kiyma':'kiyma','karisik kiyma':'kiyma','kiyma et':'kiyma',
      'kasar peyniri':'peynir','kasar':'peynir','beyaz peynir':'peynir',
      'lor peyniri':'peynir','mozzarella':'peynir','parmesan':'peynir',
      'labne':'peynir','kashar':'peynir','cheddar':'peynir','gouda':'peynir',
      'krem peynir':'peynir','ricotta':'peynir','tulum peyniri':'peynir',
      'siviyag':'yag','sivi yag':'yag','aycicek yagi':'yag',
      'kizartma yagi':'yag','kanola yagi':'yag','misir yagi':'yag','bitkisel yag':'yag',
      'tereyag':'tereyagi','margarin':'tereyagi',
      'zeytinyagi':'zeytinyagi','zeytin yagi':'zeytinyagi',
      'domates salca':'salca','biber salca':'salca','domates puresi':'salca',
      'kuru sogan':'sogan','yesil sogan':'sogan','kirmizi sogan':'sogan','arpacik sogan':'sogan',
      'aci biber':'biber','dolmalik biber':'biber','yesil biber':'biber',
      'pul biber':'biber','kapya biber':'biber','sivri biber':'biber','carliston biber':'biber',
      'sarimsak':'sarimsak','sarimsak toz':'sarimsak',
      'somon':'balik','levrek':'balik','cipura':'balik',
      'ton baligi':'balik','hamsi':'balik','palamut':'balik','uskumru':'balik','alabalik':'balik',
      'kirmizi mercimek':'mercimek','yesil mercimek':'mercimek','sari mercimek':'mercimek',
      'spagetti':'makarna','penne':'makarna','fusilli':'makarna',
      'linguine':'makarna','lazanya':'makarna','rigatoni':'makarna','farfalle':'makarna','eriste':'makarna',
      'basmati':'pirinc','baldo pirinc':'pirinc',
      'yumurta sarisi':'yumurta','yumurta beyazi':'yumurta','yumurta aki':'yumurta',
      'kokteyil domates':'domates','cherry domates':'domates',
      'bugday unu':'un','tam bugday unu':'un','çavdar unu':'un',
      'misir unu':'misirunu',
      'greek yogurt':'yogurt','suzme yogurt':'yogurt',
      'tam yag sut':'sut','badem sutu':'sut','soya sutu':'sut',
    };

    function aiParseIngredient(raw) {
      let s = normTR(raw.trim());
      // Parantez içini sil
      s = s.replace(/\([^)]*\)/g, ' ');
      // Kesirler
      s = s.replace(/[½¼¾⅓⅔]/g, ' ').replace(/\b\d+\s*\/\s*\d+\b/g, ' ');
      // Sayılar
      s = s.replace(/\b\d[\d.,]*\b/g, ' ');
      // Her kelimeyi kontrol et: birim/sayı/sıfat → sil
      const words = s.split(/\s+/).filter(w => w.length > 0);
      const kept = words.filter(w => !UNIT_WORDS.has(w) && !NUM_WORDS.has(w) && !ADJ_WORDS.has(w));
      s = kept.join(' ').trim();
      // Sinonim
      if (SYNONYMS[s]) return SYNONYMS[s];
      for (const [k, v] of Object.entries(SYNONYMS)) {
        if (s.includes(k)) return v;
      }
      return s;
    }

    /** Görüntülenebilir kısa ad — sadece sayı/birim sil, sıfatlar kalsın */
    function aiDisplayName(raw) {
      let s = normTR(raw.trim());
      s = s.replace(/\([^)]*\)/g, ' ');
      s = s.replace(/[½¼¾⅓⅔]/g, ' ').replace(/\b\d+\s*\/\s*\d+\b/g, ' ');
      s = s.replace(/\b\d[\d.,]*\b/g, ' ');
      const words = s.split(/\s+/).filter(w => w.length > 0);
      const kept = words.filter(w => !UNIT_WORDS.has(w) && !NUM_WORDS.has(w));
      s = kept.join(' ').trim();
      return s.charAt(0).toUpperCase() + s.slice(1);
    }

    /**
     * rec.matched ve rec.missing artık [{token, display}] formatında gelir.
     * Geriye dönük uyumluluk: string array da desteklenir.
     */
    /**
     * normalizeIngList — API'den gelen matched/missing string dizisini
     * {token, display} formatına dönüştürür.
     *
     * API zaten display_name() ile temizlenmiş malzeme adlarını döndürür
     * ("Domates", "Zeytinyağı", "Beyaz Peynir" gibi). Bu stringleri
     * parçalamak hataları artırır — olduğu gibi göster.
     */
    function normalizeIngList(list) {
      if (!Array.isArray(list)) return [];
      const seen = new Set();
      const result = [];
      list.forEach(item => {
        const display = typeof item === 'string'
          ? item.trim()
          : (item.display || item.token || '');
        if (!display || display.length < 2) return;
        // Token: normalize edilmiş küçük harf (emoji eşleştirmesi için)
        const token = normTR(display.toLowerCase());
        if (seen.has(token)) return;
        seen.add(token);
        result.push({ token, display });
      });
      return result;
    }

    function aiPctClass(p) {
      return p >= 70 ? 'high' : p >= 40 ? 'mid' : 'low';
    }

    const _aiFoodImgs = [
      'https://images.unsplash.com/photo-1547592180-85f173990554?w=400&q=60',
      'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&q=60',
      'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&q=60',
      'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=400&q=60',
      'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&q=60',
      'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=400&q=60',
    ];

    /** Collapsed ingredient list with +N expand */
    /**
     * renderIngList() — Malzeme listesini render eder.
     * - İlk MAX_VISIBLE malzeme görünür
     * - Fazlası "+ N daha göster" collapse butonu ile gizlenir
     * - Her item: emoji + isim + ✓/✗ rozet
     * @param {Array}  items  [{token, display}] veya string[]
     * @param {'have'|'miss'} type
     */
    function renderIngList(items, type) {
      if (!items || !items.length) return '';
      const MAX_VISIBLE = 5;
      const isHave  = type === 'have';
      const accent  = isHave ? '#22c55e' : '#ef4444';
      const bg      = isHave ? 'rgba(34,197,94,.08)' : 'rgba(239,68,68,.07)';
      const border  = isHave ? 'rgba(34,197,94,.2)'  : 'rgba(239,68,68,.18)';
      const icon    = isHave ? '✓' : '✗';
      const header  = isHave
        ? `<div class="ai-ing-label" style="color:${accent};">✅ Elimde var <span class="ai-ing-count">(${items.length})</span></div>`
        : `<div class="ai-ing-label" style="color:${accent};">🛒 Eksik <span class="ai-ing-count">(${items.length})</span></div>`;

      const uid     = 'ail_' + Math.random().toString(36).slice(2, 9);
      const visible = items.slice(0, MAX_VISIBLE);
      const hidden  = items.slice(MAX_VISIBLE);

      const renderItem = item => {
        // normalizeIngList'ten {token, display} olarak gelir
        const token   = item.token   || normTR((item.display || '').toLowerCase());
        const display = item.display || item.token || '';
        const emoji   = getFoodEmoji(token);
        return `<div class="ai-ing-row" style="background:${bg};border:1px solid ${border};">
          <span class="ai-ing-emoji">${emoji}</span>
          <span class="ai-ing-name">${esc(display)}</span>
          <span class="ai-ing-badge" style="color:${accent};border-color:${border};">${icon}</span>
        </div>`;
      };

      let html = `<div class="ai-ing-section" style="margin-bottom:10px;">
        ${header}
        <div id="${uid}_v">${visible.map(renderItem).join('')}</div>`;

      if (hidden.length > 0) {
        html += `<div id="${uid}_h" style="display:none;">${hidden.map(renderItem).join('')}</div>
        <button class="ai-ing-more" onclick="
          const h=document.getElementById('${uid}_h');
          const show=h.style.display==='none';
          h.style.display=show?'':'none';
          this.innerHTML=show?'&#9650; Daha az göster':'&#9660; +${hidden.length} malzeme daha';
        " style="border-color:${border};color:${accent};">&#9660; +${hidden.length} malzeme daha</button>`;
      }

      html += '</div>';
      return html;
    }

    function buildAiCard(rec, idx) {
      const cls      = aiPctClass(rec.match_pct);
      const imgUrl   = rec.image || _aiFoodImgs[idx % _aiFoodImgs.length];
      const matched  = normalizeIngList(rec.matched  || []);
      const missing  = normalizeIngList(rec.missing  || []);
      const missCount = missing.length;

      const accentColor = { high:'#22c55e', mid:'#f97316', low:'#94a3b8' }[cls];
      const barGradient = {
        high:'linear-gradient(90deg,#22c55e,#16a34a)',
        mid: 'linear-gradient(90deg,#f97316,#ea580c)',
        low: 'linear-gradient(90deg,#94a3b8,#64748b)',
      }[cls];

      const safeRec = JSON.stringify({
        title: rec.title, url: rec.url||'', image: imgUrl,
        matched: rec.matched||[], missing: rec.missing||[],
        match_pct: rec.match_pct, match_count: rec.match_count,
        total_required: rec.total_required,
      }).replace(/</g,'\\u003c').replace(/>/g,'\\u003e').replace(/'/g,'\\u0027');

      return `<div class="ai-card">
        <!-- ── Görsel ─────────────────────────── -->
        <div class="ai-card-thumb" style="background-image:url('${esc(imgUrl)}');">
          <div class="ai-card-thumb-grad"></div>
          ${rec.has_skt_bonus ? '<span class="ai-skt-badge">⚠️ SKT Yakın</span>' : ''}
          <span class="ai-pct-badge ai-pct-${cls}">${rec.match_pct}% uyum</span>
          <h3 class="ai-card-thumb-title">${esc(rec.title)}</h3>
        </div>

        <!-- ── Uyum çubuğu ────────────────────── -->
        <div class="ai-card-match-bar">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-size:.72rem;font-weight:700;color:${accentColor};">
              🎯 ${rec.match_count}/${rec.total_required} malzeme
            </span>
            <span style="font-size:.7rem;font-weight:700;color:${missCount>0?'#f87171':'#4ade80'};">
              ${missCount > 0 ? `🛒 ${missCount} eksik` : '✅ Tümü var!'}
            </span>
          </div>
          <div style="height:6px;background:var(--line,#e5e7eb);border-radius:20px;overflow:hidden;">
            <div style="height:100%;width:${rec.match_pct}%;background:${barGradient};
                        border-radius:20px;transition:width .6s ease;"></div>
          </div>
        </div>

        <!-- ── Malzeme listeleri ──────────────── -->
        <div class="ai-card-ing-wrap">
          ${renderIngList(matched, 'have')}
          ${renderIngList(missing, 'miss')}
        </div>

        <!-- ── Butonlar ───────────────────────── -->
        <div class="ai-card-footer">
          <button class="ai-btn-goto" onclick='openAiDetail(${safeRec})'>🔍 Detay</button>
          <button class="ai-btn-menu" onclick='openAiDetail(${safeRec},true)'>📅 Menüye</button>
        </div>
      </div>`;
    }

    // ── AI Tarif Detay Modalı ─────────────────────────────────────
    // createAiDetailModal(): Modal DOM'da yoksa body'e ekler (lazy creation).
    // openAiDetail(rec, goMenu): Seçilen AI tarif kartının tam detayını gösterir.
    //   - Hero görsel + başlık + uyum yüzdesi
    //   - "Elimde var" (yeşil) ve "Eksik" (kırmızı) malzeme grid'i
    //   - "Menüye Ekle" → api.php → menu_add ile bugünün menüsüne kaydeder
    //   - "Tarife Git" → tarifler.php kaynak sayfasına açar
    // IIFE ile modal tek seferlik oluşturulur; tekrar çağrılınca sadece doldurulur.
    (function createAiDetailModal() {
      if (document.getElementById('aiDetailModal')) return;
      document.body.insertAdjacentHTML('beforeend', `
      <div id="aiDetailModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);
           z-index:99999;overflow-y:auto;padding:16px;">
        <div style="max-width:600px;margin:auto;background:#ffffff;border:1px solid #e5e7eb;
             border-radius:20px;overflow:hidden;position:relative;min-height:200px;box-shadow:0 20px 60px rgba(0,0,0,.18);">
          <button id="aiDetailClose" style="position:absolute;top:12px;right:12px;width:32px;height:32px;
            border-radius:50%;background:rgba(255,255,255,.9);border:1px solid #ccc;color:#333;
            font-size:1.2rem;cursor:pointer;z-index:10;line-height:32px;text-align:center;">×</button>
          <div id="aiDetailHero" style="height:200px;background-size:cover;background-position:center;position:relative;">
            <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.6) 0%,transparent 55%);"></div>
            <div style="position:absolute;bottom:14px;left:16px;right:48px;">
              <h2 id="aiDetailTitle" style="font-size:1.15rem;font-weight:800;color:#fff;margin:0;text-shadow:0 1px 4px rgba(0,0,0,.5);"></h2>
              <p id="aiDetailMeta" style="font-size:.78rem;color:rgba(255,255,255,.85);margin:4px 0 0;"></p>
            </div>
          </div>
          <div style="padding:18px 20px;background:#ffffff;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
              <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:12px;">
                <div style="font-size:.68rem;font-weight:800;text-transform:uppercase;color:#16a34a;margin-bottom:8px;letter-spacing:.06em;">✅ Elimde var</div>
                <div id="aiDetailHave" style="display:flex;flex-wrap:wrap;gap:4px;"></div>
              </div>
              <div style="background:#fff1f2;border:1px solid #fca5a5;border-radius:12px;padding:12px;">
                <div style="font-size:.68rem;font-weight:800;text-transform:uppercase;color:#dc2626;margin-bottom:8px;letter-spacing:.06em;">🛒 Eksik malzeme</div>
                <div id="aiDetailMiss" style="display:flex;flex-wrap:wrap;gap:4px;"></div>
              </div>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <button id="aiDetailMenuBtn" style="flex:1;min-width:140px;padding:11px 16px;border-radius:10px;
                border:none;background:linear-gradient(135deg,#cfae55,#b8963e);color:#1a1a1a;
                font-weight:800;font-size:.88rem;cursor:pointer;box-shadow:0 2px 8px rgba(207,174,85,.3);">📅 Menüye Ekle</button>
              <a id="aiDetailLink" href="#" target="_blank" rel="noopener" style="flex:1;min-width:120px;padding:11px 16px;
                border-radius:10px;border:1.5px solid #b8963e;color:#8a6520;font-weight:700;background:#fffbf0;
                font-size:.88rem;text-align:center;text-decoration:none;display:flex;align-items:center;
                justify-content:center;">🍳 Tarife Git</a>
            </div>
          </div>
        </div>
      </div>`);
      document.getElementById('aiDetailClose').onclick = () => document.getElementById('aiDetailModal').style.display='none';
      document.getElementById('aiDetailModal').onclick = function(e){ if(e.target===this) this.style.display='none'; };
    })();

    window.openAiDetail = function(rec, goMenu=false) {
      const modal = document.getElementById('aiDetailModal');
      const hero  = document.getElementById('aiDetailHero');
      hero.style.backgroundImage = `url('${rec.image||''}')`;
      document.getElementById('aiDetailTitle').textContent = rec.title;
      document.getElementById('aiDetailMeta').textContent =
        `${rec.match_count}/${rec.total_required} malzeme \u00B7 %${rec.match_pct} uyum`;

      const haveDiv = document.getElementById('aiDetailHave');
      const missDiv = document.getElementById('aiDetailMiss');

      // normalizeIngList ile parse et — API'den gelen stringleri parçalamadan göster
      const normHave = normalizeIngList(rec.matched || []);
      const normMiss = normalizeIngList(rec.missing || []);

      const renderModalList = (list, type) => {
        if (!list.length) {
          return type === 'have'
            ? '<p style="color:var(--muted,#888);font-size:.82rem;padding:4px 0;">Eşleşen malzeme yok.</p>'
            : '<p style="color:#16a34a;font-size:.82rem;padding:4px 0;">✅ Tüm malzemeler mevcut!</p>';
        }
        const isHave = type === 'have';
        const bg  = isHave ? '#f0fdf4' : '#fff1f2';
        const brd = isHave ? '#86efac' : '#fca5a5';
        const col = isHave ? '#16a34a' : '#dc2626';
        const ico = isHave ? '✓' : '✗';
        return list.map(item => {
          const display = item.display || item.token || '';
          const token   = item.token || '';
          const emoji   = getFoodEmoji(token);
          return `<div style="display:flex;align-items:center;gap:7px;padding:5px 9px;
              border-radius:9px;background:${bg};border:1px solid ${brd};margin-bottom:4px;">
            <span style="font-size:.95rem;">${emoji}</span>
            <span style="font-size:.83rem;font-weight:500;flex:1;color:#111;">${esc(display)}</span>
            <span style="font-size:.7rem;color:${col};font-weight:800;">${ico}</span>
          </div>`;
        }).join('');
      };

      haveDiv.innerHTML = renderModalList(normHave, 'have');
      missDiv.innerHTML = renderModalList(normMiss, 'miss');

      document.getElementById('aiDetailMenuBtn').onclick = () => {
        modal.style.display='none'; aiMenuEkle(rec.title);
      };
      document.getElementById('aiDetailLink').href = rec.url||'tarifler';
      modal.style.display='block';
      if (goMenu) { modal.style.display='none'; aiMenuEkle(rec.title); }
    };

    window.aiMenuEkle = function(title) {
      // Show meal type picker overlay
      // ── Öğün seçici overlay (AI tarif → menüye ekle) ───────────
      // "Menüye Ekle" butonuna tıklanınca 4 öğün tipi arasında seçim yapılır.
      // Seçim sonrası doAddToMenu() → api.php → menu_add_from_recipe action'ı çağrılır.
      // Overlay dışına tıklanınca veya İptal'e basılınca kapanır.
      const existing = document.getElementById('aiMealPickerOverlay');
      if (existing) existing.remove();
      const ov = document.createElement('div');
      ov.id = 'aiMealPickerOverlay';
      ov.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.78);z-index:999999;display:flex;align-items:center;justify-content:center;padding:20px;';
      ov.innerHTML = `
        <div style="background:#141824;border:1px solid rgba(207,174,85,.4);border-radius:18px;padding:28px;max-width:360px;width:100%;text-align:center;">
          <h3 style="margin:0 0 6px;font-size:1.05rem;color:#fff;">📅 Hangi öğüne ekleyelim?</h3>
          <p style="font-size:.82rem;color:var(--muted);margin-bottom:20px;">${esc(title)}</p>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
            ${[['kahvaltı','☀️','rgba(251,191,36,.15)','rgba(251,191,36,.4)'],
               ['öğle','🌤️','rgba(34,197,94,.12)','rgba(34,197,94,.35)'],
               ['akşam','🌙','rgba(139,92,246,.12)','rgba(139,92,246,.35)'],
               ['atıştırmalık','🍎','rgba(249,115,22,.12)','rgba(249,115,22,.35)']].map(([t,i,bg,br])=>`
              <button onclick="doAddToMenu('${esc(title)}','${t}')"
                style="padding:14px 8px;border-radius:12px;border:1.5px solid ${br};background:${bg};
                       color:#fff;font-size:.9rem;font-weight:700;cursor:pointer;">${i}<br><span style="font-size:.75rem;">${t}</span></button>
            `).join('')}
          </div>
          <button onclick="document.getElementById('aiMealPickerOverlay').remove()"
            style="padding:8px 24px;border-radius:8px;border:1px solid #444;background:transparent;color:var(--muted);cursor:pointer;font-size:.82rem;">İptal</button>
        </div>`;
      document.body.appendChild(ov);
      ov.onclick = e => { if(e.target===ov) ov.remove(); };
    };

    window.doAddToMenu = async function(title, mealType) {
      document.getElementById('aiMealPickerOverlay')?.remove();
      const fd = new FormData();
      fd.append('recipe_title', title);
      fd.append('menu_date', new Date().toISOString().slice(0,10));
      fd.append('meal_type', mealType);
      fd.append('calories', 0);
      try {
        const res = await fetch('api/menu/from-recipe', { method:'POST', body:fd });
        const r = await res.json();
        if (r.success) {
          toast(`✅ "${title}" → ${mealType} öğününe eklendi!`, 'success');
          loadMenu();
          loadTodayCalories();
        } else { toast(r.message||'Eklenemedi','error'); }
      } catch(e) { toast('Bağlantı hatası','error'); }
    };

    // ── _aiLoading bayrağı ────────────────────────────────────────
    // Eş zamanlı birden fazla AI öneri isteğinin gönderilmesini engeller.
    // false → istek gönderilebilir; true → önceki istek bitmeden yeni istek atlanır.
    let _aiLoading = false;

    /**
     * loadAiRecipes — AI tarif önerilerini yükler ve ızgarayı günceller.
     *
     * @param {boolean} force - true ise API önbelleği bypass edilir (&force=1 gönderilir).
     *
     * Çalışma adımları:
     *  1. _aiLoading bayrağı → eş zamanlı çift istek önlenir.
     *  2. İstek sırasında skeleton (shimmer animasyon) kartlar gösterilir.
     *  3. api.php → ai_recipe_recommendations endpoint'i buzdolabı malzemeleriyle
     *     eşleşen tarifleri döndürür (match_pct, matched[], missing[]).
     *  4. buildAiCard() ile her tarif kart HTML'ine dönüştürülür.
     *  5. Hata durumunda kullanıcı dostu boş durum mesajı gösterilir.
     */
    window.loadAiRecipes = async function(force = false) {
      if (_aiLoading) return;
      _aiLoading = true;

      const grid = document.getElementById('aiRecGrid');
      const meta = document.getElementById('aiRecMeta');
      const btn  = document.getElementById('btnAiRefresh');
      const ico  = document.getElementById('aiRefreshIco');

      if (!grid) { _aiLoading = false; return; }

      if (btn) btn.disabled = true;
      if (ico) ico.textContent = '⏳';

      // Skeleton göster
      grid.innerHTML = [1,2,3].map(() => `
        <div class="ai-card">
          <div class="ai-card-thumb ai-skel"></div>
          <div class="ai-card-body" style="gap:9px;">
            <div class="ai-skel" style="height:16px;width:68%"></div>
            <div class="ai-skel" style="height:5px"></div>
            <div class="ai-skel" style="height:12px;width:90%"></div>
            <div class="ai-skel" style="height:12px;width:52%"></div>
          </div>
        </div>`).join('');

      try {
        const url = 'api/recipes/ai-recommendations?limit=6'
                  + (force ? '&force=1' : '');
        const res  = await fetch(url);
        const json = await res.json();

        if (!json.success) throw new Error(json.message || 'API hatası');

        const recs  = json.data?.recommendations || [];
        const total = json.data?.total_user_ingredients || 0;
        const eval_ = json.data?.total_recipes_evaluated || 0;

        if (meta) {
          meta.textContent = total > 0
            ? `${total} malzeme · ${eval_} tarif tarandı · ${recs.length} öneri`
            : '';
        }

        if (recs.length === 0) {
          grid.innerHTML = `<div class="ai-empty">
            <span class="ai-empty-icon">🥺</span>
            <b>${esc(json.data?.message || 'Eşleşen tarif bulunamadı.')}</b>
            <p style="margin-top:8px;font-size:.85rem;">
              Daha fazla malzeme ekleyin veya
              <a href="tarifler" style="color:#cfae55;">tarifler sayfasına gidin →</a>
            </p>
          </div>`;
        } else {
          grid.innerHTML = recs.map((r, i) => buildAiCard(r, i)).join('');
        }
      } catch (err) {
        grid.innerHTML = `<div class="ai-empty">
          <span class="ai-empty-icon">⚠️</span>
          <p>Öneriler yüklenemedi: ${esc(err.message)}</p>
        </div>`;
        if (meta) meta.textContent = 'Yükleme hatası';
      } finally {
        _aiLoading = false;
        if (btn) btn.disabled = false;
        if (ico) ico.textContent = '🔄';
      }
    };

    // ── Menü veri deposu ─────────────────────────────────────────
    let menuItems = [];

    async function loadMenu() {
      const dateEl = document.getElementById('menuDate');
      const date = (dateEl ? dateEl.value : '') || '<?= date('Y-m-d') ?>';
      try {
        const res = await fetch('api/menu?date=' + date);
        const r = await res.json();
        if (r.success) {
          menuItems = Array.isArray(r.data) ? r.data : [];
          renderMenu();
        }
      } catch(e) { console.error('loadMenu error:', e); }
    }

    // ── Yemek adından emoji eşleştir ────────────────────────────
    function getFoodEmoji(word) {
      if (!word) return '🍽️';
      const raw = typeof word === 'object' ? (word.token || word.display || '') : word;
      const w = String(raw).toLowerCase().trim();
      const map = [
        // Meyveler
        ['elma','🍎'],['armut','🍐'],['portakal','🍊'],['limon','🍋'],['muz','🍌'],['karpuz','🍉'],
        ['kavun','🍈'],['üzüm','🍇'],['çilek','🍓'],['kiraz','🍒'],['şeftali','🍑'],['kivi','🥝'],
        ['ananas','🍍'],['mango','🥭'],['hindistan cevizi','🥥'],['dut','🫐'],['nar','🍎'],
        // Sebzeler
        ['domates','🍅'],['havuç','🥕'],['mısır','🌽'],['biber','🌶️'],['sivri biber','🌶️'],
        ['salatalık','🥒'],['patlıcan','🍆'],['sarımsak','🧄'],['soğan','🧅'],['patates','🥔'],
        ['brokoli','🥦'],['lahana','🥬'],['marul','🥬'],['ıspanak','🥬'],['kabak','🥦'],
        ['mantar','🍄'],['avokado','🥑'],['soya','🫘'],['nohut','🫘'],['mercimek','🫘'],
        ['fasulye','🫘'],['bezelye','🫘'],['kuşkonmaz','🌿'],['semizotu','🌿'],['roka','🌿'],
        // Et & Protein
        ['tavuk','🍗'],['et','🥩'],['kıyma','🥩'],['balık','🐟'],['somon','🐟'],['ton balığı','🐟'],
        ['yumurta','🥚'],['sosis','🌭'],['sucuk','🌭'],['pastırma','🥓'],['bacon','🥓'],
        ['karides','🦐'],['midye','🦪'],
        // Süt & Süt Ürünleri
        ['süt','🥛'],['yoğurt','🧈'],['peynir','🧀'],['lor','🧀'],['tereyağı','🧈'],
        ['kaymak','🥛'],['kefir','🥛'],['ayran','🥛'],
        // Tahıl & Kuru
        ['ekmek','🍞'],['pirinç','🍚'],['makarna','🍝'],['bulgur','🌾'],['yulaf','🌾'],
        ['un','🌾'],['noodle','🍜'],['erişte','🍜'],['tam buğday','🌾'],
        // Kuru yemiş
        ['ceviz','🌰'],['badem','🌰'],['fındık','🌰'],['fıstık','🥜'],['yer fıstığı','🥜'],
        ['çam fıstığı','🌲'],['kaju','🌰'],['antep fıstığı','🌿'],
        // İçecek & Baharat
        ['su','💧'],['çay','🍵'],['kahve','☕'],['meyve suyu','🧃'],['limonata','🍋'],
        ['tuz','🧂'],['karabiber','🧂'],['kimyon','🌿'],['nane','🌿'],['maydanoz','🌿'],
        ['dereotu','🌿'],['kekik','🌿'],['tarçın','🌿'],['zerdeçal','🌿'],['zencefil','🌿'],
        // Yağ & Sos
        ['zeytinyağı','🫒'],['zeytin','🫒'],['salça','🍅'],['sos','🍶'],['sirke','🍶'],
        ['bal','🍯'],['reçel','🍯'],['ketçap','🍅'],['mayonez','🫙'],
        // Tatlı
        ['şeker','🍬'],['çikolata','🍫'],['dondurma','🍨'],['kek','🎂'],['pasta','🎂'],
        ['kurabiye','🍪'],['börek','🥐'],['poğaça','🥐'],
        // Hazır gıda
        ['pizza','🍕'],['hamburger','🍔'],['sandviç','🥪'],['salata','🥗'],
        ['çorba','🍲'],['pilav','🍚'],['kebap','🥙'],['omlet','🍳'],
      ];
      for (const [key, emoji] of map) {
        if (w.includes(key)) return emoji;
      }
      return '🥄';
    }

    // ── Metin içindeki malzemeleri parçala ve emoji ekle ────────
    function parseIngredients(description) {
      if (!description) return [];
      // Virgül, noktalı virgül veya yeni satır ile ayır
      const parts = description.split(/[,;\n]+/).map(s => s.trim()).filter(s => s.length > 1);
      if (parts.length <= 1) {
        // Tek parça — yemek adıdır, malzeme değil
        return [];
      }
      return parts.map(p => ({ text: p, emoji: getFoodEmoji(p) }));
    }

    function renderMenu() {
      const grid = document.getElementById('menuDayGrid');
      if (!grid) return;

      const icons = { 'kahvaltı':'☀️', 'öğle':'🌤️', 'akşam':'🌙', 'atıştırmalık':'🍎' };
      const gradients = {
        'kahvaltı': 'linear-gradient(135deg,rgba(251,191,36,.18),rgba(251,191,36,.04))',
        'öğle':     'linear-gradient(135deg,rgba(34,197,94,.18),rgba(34,197,94,.04))',
        'akşam':    'linear-gradient(135deg,rgba(139,92,246,.18),rgba(139,92,246,.04))',
        'atıştırmalık':'linear-gradient(135deg,rgba(249,115,22,.18),rgba(249,115,22,.04))'
      };
      const borders = { 'kahvaltı':'rgba(251,191,36,.45)', 'öğle':'rgba(34,197,94,.4)', 'akşam':'rgba(139,92,246,.4)', 'atıştırmalık':'rgba(249,115,22,.4)' };
      const accents = { 'kahvaltı':'#f59e0b', 'öğle':'#22c55e', 'akşam':'#8b5cf6', 'atıştırmalık':'#f97316' };
      const headerBg = {
        'kahvaltı':'linear-gradient(90deg,rgba(251,191,36,.35),transparent)',
        'öğle':    'linear-gradient(90deg,rgba(34,197,94,.35),transparent)',
        'akşam':   'linear-gradient(90deg,rgba(139,92,246,.35),transparent)',
        'atıştırmalık':'linear-gradient(90deg,rgba(249,115,22,.35),transparent)'
      };

      if (!menuItems.length) {
        grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:60px 20px;">
          <div style="font-size:4rem;margin-bottom:12px;filter:grayscale(.3);">🍽️</div>
          <p style="color:var(--muted);font-size:1rem;font-weight:600;"></p>
          <p style="color:var(--muted);font-size:.84rem;margin-top:4px;"></p>
        </div>`;
        return;
      }

      const totalCal  = menuItems.reduce((s,m) => s + (parseInt(m.calories)||0), 0);
      const totalProt = menuItems.reduce((s,m) => s + (parseFloat(m.protein_g)||0), 0);
      const totalCarb = menuItems.reduce((s,m) => s + (parseFloat(m.carb_g)||0), 0);
      const totalFat  = menuItems.reduce((s,m) => s + (parseFloat(m.fat_g)||0), 0);

      grid.innerHTML = `
        <!-- ── Günlük Özet Şerit ── -->
        <div style="grid-column:1/-1;
          background:linear-gradient(135deg,rgba(207,174,85,.14),rgba(249,115,22,.06));
          border:1.5px solid rgba(207,174,85,.35);border-radius:18px;padding:18px 22px;">
          <div style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:12px;">📊 Günlük Özet</div>
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;">
            <div style="text-align:center;background:rgba(249,115,22,.08);border-radius:12px;padding:10px 6px;">
              <div style="font-size:1.4rem;font-weight:800;color:#f97316;">${totalCal}</div>
              <div style="font-size:.67rem;color:var(--muted);margin-top:3px;">🔥 kcal</div>
            </div>
            <div style="text-align:center;background:rgba(59,130,246,.08);border-radius:12px;padding:10px 6px;">
              <div style="font-size:1.4rem;font-weight:800;color:#3b82f6;">${totalProt.toFixed(0)}g</div>
              <div style="font-size:.67rem;color:var(--muted);margin-top:3px;">💪 Protein</div>
            </div>
            <div style="text-align:center;background:rgba(34,197,94,.08);border-radius:12px;padding:10px 6px;">
              <div style="font-size:1.4rem;font-weight:800;color:#22c55e;">${totalCarb.toFixed(0)}g</div>
              <div style="font-size:.67rem;color:var(--muted);margin-top:3px;">🌾 Karb.</div>
            </div>
            <div style="text-align:center;background:rgba(245,158,11,.08);border-radius:12px;padding:10px 6px;">
              <div style="font-size:1.4rem;font-weight:800;color:#f59e0b;">${totalFat.toFixed(0)}g</div>
              <div style="font-size:.67rem;color:var(--muted);margin-top:3px;">🫒 Yağ</div>
            </div>
            <div style="text-align:center;background:rgba(207,174,85,.08);border-radius:12px;padding:10px 6px;">
              <div style="font-size:1.4rem;font-weight:800;color:#cfae55;">${menuItems.length}</div>
              <div style="font-size:.67rem;color:var(--muted);margin-top:3px;">📋 Öğün</div>
            </div>
          </div>
        </div>

        ${menuItems.map(m => {
          const cal   = parseInt(m.calories)||0;
          const prot  = parseFloat(m.protein_g)||0;
          const carb  = parseFloat(m.carb_g)||0;
          const fat   = parseFloat(m.fat_g)||0;
          const pct   = Math.min(100, Math.round(cal/800*100));
          const acc   = accents[m.meal_type]||'#cfae55';
          const isAI  = m.from_ai == 1 || m.source === 'ai';
          const ingredients = parseIngredients(m.description||'');
          const mealName = ingredients.length > 0
            ? m.description.split(/[,;\n]/)[0].trim()
            : (m.description||'');

          return `
          <div style="background:${gradients[m.meal_type]||'rgba(207,174,85,.06)'};
            border:2px solid ${borders[m.meal_type]||'rgba(207,174,85,.3)'};
            border-radius:20px;overflow:hidden;display:flex;flex-direction:column;
            transition:transform .2s,box-shadow .2s;"
            onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,.15)'"
            onmouseout="this.style.transform='';this.style.boxShadow=''">

            <!-- Öğün Tipi Başlığı -->
            <div style="background:${headerBg[m.meal_type]||'rgba(207,174,85,.2)'};
              padding:10px 16px;display:flex;justify-content:space-between;align-items:center;
              border-bottom:1px solid ${borders[m.meal_type]||'rgba(207,174,85,.2)'};">
              <span style="font-size:.78rem;font-weight:800;text-transform:uppercase;
                color:${acc};letter-spacing:.05em;">
                ${icons[m.meal_type]||'🍽️'} ${esc(m.meal_type)}
              </span>
              ${isAI ? `<span style="background:linear-gradient(135deg,#cfae55,#b8963e);color:#1a1a1a;
                font-size:.62rem;font-weight:800;padding:2px 10px;border-radius:20px;letter-spacing:.03em;">🤖 AI</span>` : ''}
            </div>

            <!-- Yemek Adı + Büyük Emoji -->
            <div style="padding:16px 16px 10px;display:flex;align-items:center;gap:14px;">
              <div style="font-size:2.8rem;line-height:1;filter:drop-shadow(0 2px 6px rgba(0,0,0,.15));flex-shrink:0;">
                ${getFoodEmoji(mealName)}
              </div>
              <div style="flex:1;min-width:0;">
                <div style="font-size:1.05rem;font-weight:700;color:var(--ink);line-height:1.3;">
                  ${esc(mealName)}
                </div>
                ${cal ? `<div style="font-size:.78rem;color:${acc};font-weight:700;margin-top:3px;">🔥 ${cal} kcal</div>` : ''}
              </div>
            </div>

            <!-- Kalori Bar -->
            ${cal ? `<div style="padding:0 16px 10px;">
              <div style="display:flex;justify-content:space-between;font-size:.68rem;color:var(--muted);margin-bottom:5px;">
                <span>Günlük hedefin <b style="color:${acc};">${pct}%</b>'i</span>
                <span>${cal} / 800 kcal</span>
              </div>
              <div style="height:6px;background:rgba(0,0,0,.12);border-radius:6px;overflow:hidden;">
                <div style="width:${pct}%;height:100%;background:linear-gradient(90deg,${acc},${acc}bb);border-radius:6px;transition:width .6s ease;"></div>
              </div>
            </div>` : ''}

            <!-- Malzemeler (tek tek emoji ile) -->
            ${ingredients.length > 0 ? `
            <div style="padding:0 16px 12px;">
              <div style="font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;
                color:var(--muted);margin-bottom:8px;">🧺 Malzemeler</div>
              <div style="display:flex;flex-direction:column;gap:5px;">
                ${ingredients.map(ing => `
                  <div style="display:flex;align-items:center;gap:8px;
                    background:rgba(0,0,0,.04);border-radius:8px;padding:5px 10px;">
                    <span style="font-size:1.15rem;flex-shrink:0;">${ing.emoji}</span>
                    <span style="font-size:.82rem;color:var(--ink);font-weight:500;">${esc(ing.text)}</span>
                  </div>`).join('')}
              </div>
            </div>` : ''}

            <!-- Makro Etiketleri -->
            ${(prot > 0 || carb > 0 || fat > 0) ? `
            <div style="display:flex;gap:5px;flex-wrap:wrap;padding:0 16px 12px;">
              ${prot > 0 ? `<span style="background:rgba(59,130,246,.1);color:#2563eb;
                border:1px solid rgba(59,130,246,.25);padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;">
                💪 ${prot}g P</span>` : ''}
              ${carb > 0 ? `<span style="background:rgba(34,197,94,.1);color:#15803d;
                border:1px solid rgba(34,197,94,.25);padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;">
                🌾 ${carb}g K</span>` : ''}
              ${fat > 0 ? `<span style="background:rgba(245,158,11,.1);color:#b45309;
                border:1px solid rgba(245,158,11,.25);padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;">
                🫒 ${fat}g Y</span>` : ''}
            </div>` : ''}

            ${isAI ? `<div style="margin:0 16px 12px;padding:8px 12px;
              background:rgba(207,174,85,.08);border-radius:10px;border-left:3px solid #cfae55;">
              <span style="font-size:.73rem;color:var(--muted);line-height:1.5;">
                🤖 Yapay zeka tarafından dolabınızdaki malzemelere göre önerildi.</span>
            </div>` : ''}

            <!-- Sil Butonu -->
            <div style="margin-top:auto;padding:0 16px 14px;display:flex;justify-content:flex-end;">
              <button onclick="deleteMenu(${m.id})"
                style="padding:6px 16px;border-radius:10px;border:1.5px solid rgba(239,68,68,.3);
                background:rgba(239,68,68,.08);color:#ef4444;font-size:.75rem;font-weight:700;
                cursor:pointer;transition:.15s;"
                onmouseover="this.style.background='rgba(239,68,68,.2)';this.style.borderColor='#ef4444'"
                onmouseout="this.style.background='rgba(239,68,68,.08)';this.style.borderColor='rgba(239,68,68,.3)'">
                🗑️ Sil
              </button>
            </div>
          </div>`;
        }).join('')}`;
    }

    async function deleteMenu(id) {
      const fd = new FormData(); fd.append('id', id);
      const menuDelId = fd.get('id');
      const res = await fetch('api/menu/' + menuDelId, { method:'DELETE' });
      const r = await res.json();
      if (r.success) {
        menuItems = menuItems.filter(m => m.id != id);
        renderMenu();
        loadTodayCalories();
        toast('Öğün silindi', 'success');
      }
    }

    // ════ GIDA KALORİ ARAMA ══════════════════════════════════════
    // foodCalList : api.php → food_calories_dropdown ile yüklenen gıda veritabanı.
    // selectedFood* : Açılır listeden seçilen gıdanın kalori ve makro değerleri.
    //                 Öğün eklenince bu değerler otomatik form alanlarına doldurulur.
    // filterFoodList() : Kullanıcı yazarken listeyi gerçek zamanlı filtreler (maks 5 sonuç).
    // _positionFoodDropdown() : Scroll/resize sonrası dropdown konumunu input'a hizalar.

    let foodCalList = [];
    let selectedFoodCal   = 0;
    let selectedFoodName  = '';
    let selectedFoodId    = 0;
    let selectedFoodProt  = 0;
    let selectedFoodCarb  = 0;
    let selectedFoodFat   = 0;

    // Emoji eşleştirici (gıda adına göre)
    function getFoodEmojiForName(name) {
      return getFoodEmoji(name);
    }

    async function loadFoodCalories() {
      const r = await api('food_calories_dropdown', {}, 'GET');
      if (r.success && Array.isArray(r.data)) {
        foodCalList = r.data;
      }
    }

    function _positionFoodDropdown() {
      const input = document.getElementById('foodSearchInput');
      const dd    = document.getElementById('foodDropdown');
      if (!input || !dd) return;
      const rect = input.getBoundingClientRect();
      dd.style.top    = (rect.bottom + 6) + 'px';
      dd.style.left   = rect.left + 'px';
      dd.style.width  = rect.width + 'px';
    }

    function showFoodDropdown() {
      _positionFoodDropdown();
      filterFoodList(document.getElementById('foodSearchInput')?.value || '');
    }

    function hideFoodDropdown() {
      const dd = document.getElementById('foodDropdown');
      if (dd) dd.style.display = 'none';
    }

    // Scroll veya resize olunca pozisyonu güncelle
    window.addEventListener('scroll', () => {
      const dd = document.getElementById('foodDropdown');
      if (dd && dd.style.display !== 'none') _positionFoodDropdown();
    }, true);
    window.addEventListener('resize', () => {
      const dd = document.getElementById('foodDropdown');
      if (dd && dd.style.display !== 'none') _positionFoodDropdown();
    });

    function filterFoodList(query) {
      const dd = document.getElementById('foodDropdown');
      if (!dd) return;
      const q = query.trim().toLowerCase();
      // Maksimum 5 gıda göster
      const all  = q ? foodCalList.filter(f => f.name.toLowerCase().includes(q)) : foodCalList;
      const list = all.slice(0, 5);

      if (!list.length) {
        dd.innerHTML = `<div style="padding:13px 16px;color:var(--muted);font-size:.85rem;text-align:center;">🔍 Sonuç bulunamadı</div>`;
        _positionFoodDropdown();
        dd.style.display = 'block';
        return;
      }

      const ITEM_H = 56; // piksel - her satırın tahmini yüksekliği
      dd.style.maxHeight = (list.length * ITEM_H + 2) + 'px';
      dd.style.overflowY = list.length >= 5 ? 'auto' : 'visible';

      dd.innerHTML = list.map((f, idx) => {
        const emoji = getFoodEmojiForName(f.name);
        const isLast = idx === list.length - 1;
        const cat = f.category
          ? `<span style="font-size:.67rem;color:#cfae55;background:rgba(207,174,85,.13);padding:2px 7px;border-radius:8px;margin-left:5px;">${f.category}</span>`
          : '';
        const macroHint = (parseFloat(f.protein_g||0) > 0 || parseFloat(f.carb_g||0) > 0 || parseFloat(f.fat_g||0) > 0)
          ? `<span style="font-size:.67rem;color:var(--muted);margin-left:5px;">P:${parseFloat(f.protein_g||0).toFixed(1)}g · K:${parseFloat(f.carb_g||0).toFixed(1)}g · Y:${parseFloat(f.fat_g||0).toFixed(1)}g</span>`
          : '';
        const border = isLast ? '' : 'border-bottom:1px solid rgba(0,0,0,.05);';
        return `<div
          onclick="selectFood(${f.id},${f.calories},${parseFloat(f.protein_g||0)},${parseFloat(f.carb_g||0)},${parseFloat(f.fat_g||0)},'${f.name.replace(/'/g,"\\'")}','${(f.portion_size||'100g').replace(/'/g,"\\'")}','${(f.category||'').replace(/'/g,"\\'")}',this)"
          style="padding:10px 14px;display:flex;align-items:center;gap:10px;cursor:pointer;transition:background .14s;${border}"
          onmouseover="this.style.background='rgba(207,174,85,.1)'"
          onmouseout="this.style.background=''">
          <span style="font-size:1.5rem;flex-shrink:0;width:30px;text-align:center;">${emoji}</span>
          <div style="flex:1;min-width:0;overflow:hidden;">
            <div style="font-size:.9rem;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(f.name)}${cat}</div>
            <div style="font-size:.74rem;color:#f97316;font-weight:700;margin-top:1px;">${f.calories} kcal / ${f.portion_size||'100g'}${macroHint}</div>
          </div>
        </div>`;
      }).join('');

      _positionFoodDropdown();
      dd.style.display = 'block';
    }

    function selectFood(id, cal, prot, carb, fat, name, portion, category, el) {
      selectedFoodId   = id   || 0;
      selectedFoodCal  = cal  || 0;
      selectedFoodProt = prot || 0;
      selectedFoodCarb = carb || 0;
      selectedFoodFat  = fat  || 0;
      selectedFoodName = name;

      // Arama kutusunu doldur
      const si = document.getElementById('foodSearchInput');
      if (si) si.value = name;

      // Kalori alanını doldur
      document.getElementById('menuCal').value = cal;

      // Açıklama boşsa otomatik doldur
      const descEl = document.getElementById('menuDesc');
      if (descEl && !descEl.value.trim()) descEl.value = name;

      // Seçili kart göster — makrolarla birlikte
      const card = document.getElementById('foodSelCard');
      if (card) {
        document.getElementById('foodSelEmoji').textContent = getFoodEmojiForName(name);
        document.getElementById('foodSelName').textContent = name;
        // Makro özeti
        const macros = [
          prot > 0 ? `💪 ${prot.toFixed(1)}g P` : '',
          carb > 0 ? `🌾 ${carb.toFixed(1)}g K` : '',
          fat  > 0 ? `🫒 ${fat.toFixed(1)}g Y`  : '',
        ].filter(Boolean).join('  ');
        document.getElementById('foodSelDetail').innerHTML =
          `<span style="color:var(--muted);">${portion}${category ? ' · ' + category : ''}</span>`
          + (macros ? `<br><span style="font-size:.75rem;font-weight:700;color:var(--ink);margin-top:2px;display:inline-block;">${macros}</span>` : '');
        document.getElementById('foodSelKcal').textContent = cal;
        card.style.display = 'flex';
      }

      hideFoodDropdown();
    }

    function clearFoodSel() {
      const si = document.getElementById('foodSearchInput');
      if (si) si.value = '';
      document.getElementById('menuCal').value = '';
      document.getElementById('menuDesc').value = '';
      selectedFoodId   = 0;
      selectedFoodCal  = 0;
      selectedFoodProt = 0;
      selectedFoodCarb = 0;
      selectedFoodFat  = 0;
      selectedFoodName = '';
      const card = document.getElementById('foodSelCard');
      if (card) card.style.display = 'none';
      hideFoodDropdown();
    }

    // Geriye uyumluluk
    function onFoodSelectChange() {}

    // ════ ÖĞÜN EKLEME ══════════════════════════════════════════

    document.getElementById('btnMenuAdd').addEventListener('click', async () => {
      const desc = document.getElementById('menuDesc').value.trim();
      const cal = parseInt(document.getElementById('menuCal').value) || 0;

      if (!desc) {
        toast('Açıklama veya gıda seçin!', 'error');
        return;
      }

      const r = await api('menu_add', {
        menu_date:   document.getElementById('menuDate').value,
        meal_type:   document.getElementById('menuType').value,
        description: desc,
        calories:    cal,
        food_id:     selectedFoodId  || '',
        protein_g:   selectedFoodProt !== 0 ? selectedFoodProt : '',
        carb_g:      selectedFoodCarb !== 0 ? selectedFoodCarb : '',
        fat_g:       selectedFoodFat  !== 0 ? selectedFoodFat  : '',
      });

      if (r.success) {
        document.getElementById('menuDesc').value = '';
        clearFoodSel();
        await loadMenu();
        await loadTodayCalories();
        const macroMsg = (selectedFoodProt || selectedFoodCarb || selectedFoodFat)
          ? ` · P:${selectedFoodProt.toFixed(1)}g K:${selectedFoodCarb.toFixed(1)}g Y:${selectedFoodFat.toFixed(1)}g` : '';
        toast('🔥 Öğün eklendi! ' + (cal ? cal + ' kcal' : '') + macroMsg, 'success');
      } else {
        toast(r.message, 'error');
      }
    });

    document.getElementById('menuDate').addEventListener('change', loadMenu);

    // ════ BUGÜNÜN TOPLAM KALORİSİ (tüm paneller) ═════════════════

    /**
     * loadTodayCalories — Bugünkü toplam kalori alımını tüm ilgili panellere yazar.
     *
     * api.php → menu_today_calories ile bugün tüketilen kaloriyi ve
     * haftalık ortalamasını çeker. Aynı veriyi üç farklı panele senkronize eder:
     *  1. Günün Menüsü paneli  (calBar, calConsumed, calGoal…)
     *  2. Su+Vücut paneli      (trackerCal…)
     *  3. İçgörüler paneli     (insCal…)
     *
     * Kalori çubuğu rengi:
     *  > %90 hedef → kırmızı | > %70 → turuncu | altında → yeşil→altın
     */
    async function loadTodayCalories() {
      const r = await api('menu_today_calories', {}, 'GET');
      if (!r.success) return;

      const consumed = r.data.total || 0;
      const weekAvg = Math.round(r.data.week_avg || 0);
      const goal = parseInt(document.getElementById('calGoalInput')?.value) || 2000;
      const pct = Math.min(100, Math.round((consumed / goal) * 100));
      const barColor = pct > 90 ? '#e74c3c' : pct > 70 ? '#e67e22' : 'linear-gradient(90deg,#27ae60,#cfae55)';

      // ── Günün Menüsü paneli ──
      const setEl = (id, val) => {
        const e = document.getElementById(id);
        if (e) e.textContent = val;
      };
      const setW = (id, w, bg) => {
        const e = document.getElementById(id);
        if (e) {
          e.style.width = w + '%';
          if (bg) e.style.background = bg;
        }
      };

      setEl('calTodayBadge', consumed + ' kcal');
      setEl('calConsumed', consumed + ' kcal alındı');
      setEl('calGoal', 'Hedef: ' + goal + ' kcal');
      setEl('calStatToday', consumed);
      setEl('calStatWeekAvg', weekAvg || '—');
      setW('calBar', pct, barColor);

      // ── Su+Vücut (tracker) paneli ──
      setEl('trackerCalBadge', consumed + ' kcal');
      setEl('trackerCalToday', consumed);
      setEl('trackerCalWeekAvg', weekAvg || '—');
      setEl('trackerCalConsumed', consumed + ' kcal alındı');
      setEl('trackerCalGoalLbl', 'Hedef: ' + goal + ' kcal');
      setW('trackerCalBar', pct, barColor);

      // ── İçgörüler paneli ──
      setEl('insCalToday', consumed);
      setEl('insCalBadge', consumed + ' kcal');
      setEl('insCalWeekAvg', weekAvg || '—');
    }

    // ════════════════════════════════════════════ ATIK YÖNETİMİ
    // loadWaste()   → api.php → waste_list; son 50 kaydı çeker.
    // renderWaste() → Liste kartlarını DOM'a yazar (ürün, miktar, sebep, tarih).
    // btnWasteAdd   → Yeni atık kaydı ekler; başarıda 'Atık Kahramanı' rozeti kontrol edilir.
    // Leaflet harita : GPS konumuna veya tıklama noktasına göre en yakın
    //                  geri dönüşüm noktası hesaplanır ve marker eklenir.
     /* api.php → waste_list endpoint'ini kullanır; son 50 kayıt döner.
     * wasteItems dizisini günceller ve renderWaste() çağırır.
     */
    async function loadWaste() {
      const r = await api('waste_list', {}, 'GET');
      if (r.success) {
        wasteItems = r.data;
        renderWaste();
      }
    }

    /**
     * renderWaste — Atık kayıt listesini DOM'a yazar.
     * Her kayıt için ürün adı, miktar, sebep ve tarih gösterir.
     * Liste boşsa merkezi bir boş durum mesajı gösterir.
     */
    function renderWaste() {
      document.getElementById('wasteLogList').innerHTML = wasteItems.map(w => `
    <div class="waste-log-item">
      <div class="wl-icon">♻️</div>
      <div class="wl-info">
        <div class="wl-name">${esc(w.item_name)} ${w.amount?'('+esc(w.amount)+')':''}</div>
        <div class="wl-meta">${w.reason?'Sebep: '+esc(w.reason)+' • ':''} ${w.logged_at}</div>
      </div>
    </div>
  `).join('') || '<p style="color:var(--muted);text-align:center;padding:20px;">Kayıt yok.</p>';
    }

    document.getElementById('btnWasteAdd').addEventListener('click', async () => {
      const item = document.getElementById('wasteItem').value.trim();
      if (!item) {
        toast('Ürün adı girin!', 'error');
        return;
      }
      const r = await api('waste_add', {
        item_name: item,
        amount: document.getElementById('wasteAmount').value.trim(),
        reason: document.getElementById('wasteReason').value.trim()
      });
      if (r.success) {
        ['wasteItem', 'wasteAmount', 'wasteReason'].forEach(id => document.getElementById(id).value = '');
        await loadWaste();
        toast('Atık kaydedildi ♻️', 'success');
        // API'den rozet bildirimi gelirse göster
        if (r.data?.badge_awarded) {
          earnedBadges['waste_hero'] = true;
          renderBadgesPage();
          updateBadgeCount();
          confettiBoom();
          toast('🏆 Rozet: Atık Kahramanı kazanıldı! 🌱', 'success');
        } else {
          // Fallback: rozeti doğrudan kontrol et
          checkBadge('waste_hero', true);
        }
      } else toast(r.message, 'error');
    });

    // ════════════════════════════════════════════ İÇGÖRÜLER (Chart.js Grafikleri)
    // loadInsights() → 8 API isteği Promise.all ile paralel gönderilir (performans).
    // Yüklenen veriler:
    //   waterChartInst → Sütun grafik: Son 7 günlük su tüketimi (ml).
    //                    Yeşil=hedef aşıldı, sarı=yakın, kırmızı=düşük.
    //   statsChartInst → Donut grafik: Tarif/Dolap/Alışveriş/Rozet dağılımı.
    //   calChart       → Çizgi grafik: Son 7 günlük kalori alımı (kcal).
    //   badgeChart     → Donut grafik: Kazanılan/kilitli rozet oranı.
    //                    Merkezde X/9 metni özel Canvas eklentisiyle çizilir.
    // Önceki grafik instance'ları destroy() ile bellekten temizlenir (bellek sızıntısı önlenir).
    let waterChartInst = null,
      statsChartInst = null;

    /**
     * loadInsights — İçgörüler sayfasının tüm verilerini paralel yükler.
     *
     * Promise.all ile 8 API isteği eş zamanlı gönderilir:
     *  su bugün, dolap, tarifler, rozetler, alışveriş, bugün kalori,
     *  haftalık kalori, haftalık su.
     * Yükleme sonrası:
     *  - Özet istatistik kartları (insightsGrid) güncellenir.
     *  - "Bugün Yediklerim" menü listesi çizilir.
     *  - Su ve kalori çizgi grafikleri (Chart.js) oluşturulur.
     *  - Rozet donut grafiği çizilir.
     */
    async function loadInsights() {
      // Tüm verileri paralel çek
      const [w, c, r, b, s, cal, calWeekly, wWeekly] = await Promise.all([
        api('water_today', {}, 'GET'),
        api('fridge_list', {}, 'GET'),
        api('recipe_list', {}, 'GET'),
        api('badge_list', {}, 'GET'),
        api('shop_list', {}, 'GET'),
        api('menu_today_calories', {}, 'GET'),
        api('menu_weekly_calories', {}, 'GET'),
        api('water_weekly', {}, 'GET')
      ]);

      const todayCal = cal.success ? (cal.data.total || 0) : 0;
      const weekAvg = cal.success ? Math.round(cal.data.week_avg || 0) : 0;
      const waterToday = w.success ? (w.data.total_ml / 1000).toFixed(1) : '0.0';

      // ── Kalori özet kartı (insightsGrid üstünde statik HTML elementleri) ──
      const setEl = (id, val) => {
        const e = document.getElementById(id);
        if (e) e.textContent = val;
      };
      setEl('insCalToday', todayCal);
      setEl('insCalBadge', todayCal + ' kcal');
      setEl('insCalWeekAvg', weekAvg || '—');
      setEl('insWaterToday', waterToday + ' L');

      // ── insightsGrid küçük istatistik kartları ──
      const expiring = c.success ? c.data.filter(i => {
        if (!i.expiry_date) return false;
        const d = Math.ceil((new Date(i.expiry_date) - new Date()) / 86400000);
        return d >= 0 && d <= 3;
      }).length : 0;

      document.getElementById('insightsGrid').innerHTML = `
        <div class="insight-card"><div class="ic-val" style="color:#3b82f6;">${waterToday} L</div><div class="ic-label">Bugünkü Su</div></div>
        <div class="insight-card"><div class="ic-val" style="color:#f97316;">${todayCal}</div><div class="ic-label">Bugün Kalori (kcal)</div></div>
        <div class="insight-card"><div class="ic-val" style="color:#cfae55;">${weekAvg || '—'}</div><div class="ic-label">Haftalık Ort. (kcal)</div></div>
        <div class="insight-card"><div class="ic-val">${c.success ? c.data.length : 0}</div><div class="ic-label">Dolap Malzeme</div></div>
        <div class="insight-card"><div class="ic-val">${r.success ? r.data.length : 0}</div><div class="ic-label">Kayıtlı Tarif</div></div>
        <div class="insight-card"><div class="ic-val">${b.success ? b.data.length : 0}/11</div><div class="ic-label">Kazanılan Rozet</div></div>
        <div class="insight-card"><div class="ic-val">${s.success ? s.data.filter(i=>!i.is_done).length : 0}</div><div class="ic-label">Alışveriş Kalemi</div></div>
        <div class="insight-card"><div class="ic-val ${expiring > 0 ? 'ic-warn' : ''}">${expiring}</div><div class="ic-label">SKT Yaklaşan</div></div>
      `;

      // ── Bugün Yediklerim (menü listesi) ──
      const todayMenuEl = document.getElementById('insTodayMenu');
      if (todayMenuEl) {
        const mDate = new Date().toISOString().slice(0, 10);
        const mr = await api('menu_list?date=' + mDate, {}, 'GET');
        if (mr.success && mr.data.length) {
          const mealIcons = {
            kahvaltı: '☀️',
            öğle: '🌤️',
            akşam: '🌙',
            atıştırmalık: '🍎'
          };
          todayMenuEl.innerHTML = mr.data.map(m => `
            <div style="display:flex;align-items:center;gap:12px;padding:10px 14px;
                        background:var(--surface);border-radius:10px;border:1px solid var(--line,rgba(255,255,255,.08));">
              <span style="font-size:1.3rem;">${mealIcons[m.meal_type] || '🍽️'}</span>
              <div style="flex:1;">
                <div style="font-weight:700;font-size:.9rem;">${esc(m.description || '')}</div>
                <div style="font-size:.75rem;color:var(--muted);margin-top:2px;text-transform:capitalize;">${esc(m.meal_type)}</div>
              </div>
              ${m.calories ? `<span style="background:rgba(249,115,22,.15);color:#f97316;padding:3px 10px;
                              border-radius:20px;font-size:.8rem;font-weight:700;white-space:nowrap;">
                              🔥 ${m.calories} kcal</span>` : ''}
            </div>
          `).join('');
        } else {
          todayMenuEl.innerHTML = '<p style="color:var(--muted);text-align:center;padding:20px;"></p>';
        }
      }

      // ── Grafikler ──
      const isDark = document.body.classList.contains('dark-mode');
      const textColor = isDark ? '#ccc' : '#555';
      const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)';

      // Su grafiği
      const weeklyData = wWeekly.success ? wWeekly.data : [];
      if (waterChartInst) waterChartInst.destroy();
      const wCtx = document.getElementById('waterChart');
      if (wCtx) {
        waterChartInst = new Chart(wCtx, {
          type: 'bar',
          data: {
            labels: weeklyData.map(d => d.label),
            datasets: [{
              label: 'Su (ml)',
              data: weeklyData.map(d => d.total),
              backgroundColor: weeklyData.map(d => d.total >= 2000 ? '#22c55e' : d.total >= 1500 ? '#cfae55' : '#ef4444'),
              borderRadius: 6,
              borderSkipped: false
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                callbacks: {
                  label: c => (c.raw / 1000).toFixed(1) + ' L'
                }
              }
            },
            scales: {
              y: {
                ticks: {
                  color: textColor,
                  font: {
                    size: 10
                  },
                  callback: v => (v / 1000).toFixed(1) + 'L'
                },
                grid: {
                  color: gridColor
                },
                beginAtZero: true
              },
              x: {
                ticks: {
                  color: textColor,
                  font: {
                    size: 10
                  }
                },
                grid: {
                  display: false
                }
              }
            }
          }
        });
      }

      // Genel durum donut
      if (statsChartInst) statsChartInst.destroy();
      const sCtx = document.getElementById('statsChart');
      if (sCtx) {
        statsChartInst = new Chart(sCtx, {
          type: 'doughnut',
          data: {
            labels: ['Tarifler', 'Dolap', 'Alışveriş', 'Rozetler'],
            datasets: [{
              data: [r.success ? r.data.length : 0, c.success ? c.data.length : 0, s.success ? s.data.length : 0, b.success ? b.data.length : 0],
              backgroundColor: ['#cfae55', '#3b82f6', '#22c55e', '#ef4444'],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  color: textColor,
                  font: {
                    size: 11
                  },
                  padding: 12
                }
              }
            }
          }
        });
      }

      // Kalori çizgi grafiği
      const calData = calWeekly.success ? calWeekly.data : [];
      const calCtx = document.getElementById('calChart');
      if (calCtx) {
        if (calCtx._chartInst) calCtx._chartInst.destroy();
        calCtx._chartInst = new Chart(calCtx, {
          type: 'line',
          data: {
            labels: calData.map(d => d.label),
            datasets: [{
              label: 'Kalori (kcal)',
              data: calData.map(d => d.total),
              borderColor: '#f97316',
              backgroundColor: 'rgba(249,115,22,0.1)',
              borderWidth: 2,
              pointRadius: 4,
              pointBackgroundColor: '#f97316',
              fill: true,
              tension: 0.4
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              y: {
                ticks: {
                  color: textColor,
                  font: {
                    size: 10
                  }
                },
                grid: {
                  color: gridColor
                },
                beginAtZero: true
              },
              x: {
                ticks: {
                  color: textColor,
                  font: {
                    size: 10
                  }
                },
                grid: {
                  display: false
                }
              }
            }
          }
        });
      }

      // Rozet donut
      const earnedCount = b.success ? b.data.length : 0;
      const totalBadges = 11;
      const badgeCtx = document.getElementById('badgeChart');
      if (badgeCtx) {
        if (badgeCtx._chartInst) badgeCtx._chartInst.destroy();
        badgeCtx._chartInst = new Chart(badgeCtx, {
          type: 'doughnut',
          data: {
            labels: ['Kazanıldı', 'Kilitli'],
            datasets: [{
              data: [earnedCount, totalBadges - earnedCount],
              backgroundColor: ['#cfae55', 'rgba(100,100,100,0.3)'],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  color: textColor,
                  font: {
                    size: 11
                  },
                  padding: 12
                }
              },
              tooltip: {
                callbacks: {
                  label: c => c.label + ': ' + c.raw
                }
              }
            }
          },
          plugins: [{
            id: 'centerText',
            beforeDraw(chart) {
              const {
                ctx,
                width,
                height
              } = chart;
              ctx.save();
              ctx.font = 'bold 22px Inter';
              ctx.fillStyle = textColor;
              ctx.textAlign = 'center';
              ctx.textBaseline = 'middle';
              ctx.fillText(earnedCount + '/' + totalBadges, width / 2, height / 2 - 10);
              ctx.font = '12px Inter';
              ctx.fillText('Rozet', width / 2, height / 2 + 14);
              ctx.restore();
            }
          }]
        });
      }
    }

    // ════════════════════════════════════════════ BEĞENİLERİM
    // loadMyLikes() → api.php → my_likes ile kullanıcının beğendiği tarifleri çeker.
    // Her kart tarif görseli, başlık, tarih ve kaynak linki içerir.
    // Beğeni yoksa tariflere yönlendiren boş durum mesajı gösterilir.
    async function loadMyLikes() {
      const container = document.getElementById('myLikesList');
      if (!container) return;
      container.innerHTML = '<p style="color:var(--muted);grid-column:1/-1;text-align:center;padding:30px;">⏳ Yükleniyor...</p>';
      const r = await api('my_likes', {}, 'GET');
      if (!r.success) {
        container.innerHTML = '<p style="color:#ef4444;grid-column:1/-1;text-align:center;padding:30px;">⚠️ Yüklenemedi: ' + (r.message||'Hata') + '</p>';
        return;
      }
      if (!r.data || !r.data.length) {
        container.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px 20px;">
          <div style="font-size:3rem;margin-bottom:12px;">💔</div>
          <p style="color:var(--muted);margin:0 0 14px;">Henüz beğenilen tarif yok.</p>
          <a href="tarifler" class="btn-sm btn-gold" style="text-decoration:none;">Tariflere Git →</a>
        </div>`;
        return;
      }
      container.innerHTML = r.data.map(item => `
    <div class="recipe-book-card" style="transition:transform .2s,box-shadow .2s;" onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.12)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
      ${item.recipe_image
        ? `<div class="rbc-img" style="background-image:url('${esc(item.recipe_image)}');background-size:cover;background-position:center;"></div>`
        : `<div class="rbc-img" style="background:linear-gradient(135deg,#cfae55,#f97316);display:grid;place-items:center;font-size:2.5rem;">❤️</div>`}
      <div class="rbc-body">
        <h4 style="font-size:.95rem;font-weight:700;margin:0 0 6px;line-height:1.3;color:var(--ink);">${esc(item.recipe_title)}</h4>
        <p style="font-size:.74rem;color:var(--muted);margin:0 0 10px;">📅 ${item.created_at}</p>
        <div class="rbc-actions">
          ${item.recipe_url
            ? `<a class="btn-sm btn-gold" href="${esc(item.recipe_url)}" target="_blank" rel="noopener" style="text-decoration:none;font-size:.8rem;">🔗 Kaynağa Git</a>`
            : ''}
        </div>
      </div>
    </div>
  `).join('');
    }

    // ════════════════════════════════════════════ YORUMLARIM
    // loadMyComments() → api.php → my_comments; kullanıcıya ait yorumları çeker.
    // Her yorum satır içinde düzenlenebilir (startCommentEdit / saveCommentEdit).
    // Düzenleme: Metin alanı gösterilir, textarea olarak güncellenir, api → comment_edit.
    // Silme: Onay dialog + api → comment_delete + DOM'dan kaldırma.
    async function loadMyComments() {
      const container = document.getElementById('myCommentsList');
      if (!container) return;
      container.innerHTML = '<p style="color:var(--muted);text-align:center;padding:30px;">⏳ Yükleniyor...</p>';
      const r = await api('my_comments', {}, 'GET');
      if (!r.success) {
        container.innerHTML = '<p style="color:#ef4444;text-align:center;padding:30px;">⚠️ Yüklenemedi: ' + (r.message||'Hata') + '</p>';
        return;
      }
      if (!r.data || !r.data.length) {
        container.innerHTML = `<div style="text-align:center;padding:40px 20px;">
          <div style="font-size:3rem;margin-bottom:12px;">💬</div>
          <p style="color:var(--muted);margin:0 0 14px;">Henüz yorum yapılmamış.</p>
          <a href="tarifler" class="btn-sm btn-gold" style="text-decoration:none;">Tariflere Git →</a>
        </div>`;
        return;
      }
      container.innerHTML = r.data.map(c => `
    <div id="comment-card-${c.id}" style="background:var(--surface);border:1px solid var(--line,#ddd);border-radius:14px;padding:16px;display:flex;gap:14px;align-items:flex-start;transition:box-shadow .2s;">
      ${c.recipe_image
        ? `<img src="${esc(c.recipe_image)}" style="width:68px;height:68px;object-fit:cover;border-radius:10px;flex-shrink:0;" onerror="this.src='';this.style.display='none'">`
        : `<div style="width:68px;height:68px;background:linear-gradient(135deg,#cfae55,#f97316);border-radius:10px;display:grid;place-items:center;font-size:1.8rem;flex-shrink:0;">💬</div>`}
      <div style="flex:1;min-width:0;">
        <div style="font-weight:700;font-size:.95rem;margin-bottom:6px;color:var(--ink);">${esc(c.recipe_title)}</div>
        <div id="comment-text-${c.id}" style="font-size:.9rem;color:var(--ink,#333);background:rgba(207,174,85,.08);padding:10px 13px;border-radius:10px;border-left:3px solid #cfae55;margin-bottom:8px;line-height:1.5;">${esc(c.comment_text)}</div>
        <div id="comment-edit-area-${c.id}" style="display:none;margin-bottom:8px;">
          <textarea id="comment-edit-input-${c.id}" style="width:100%;padding:9px 12px;border-radius:10px;border:1.5px solid rgba(207,174,85,.5);background:var(--surface);color:var(--ink);font-size:.9rem;resize:vertical;min-height:70px;box-sizing:border-box;">${esc(c.comment_text)}</textarea>
          <div style="display:flex;gap:8px;margin-top:6px;">
            <button onclick="saveCommentEdit(${c.id})" class="btn-sm btn-gold" style="font-size:.8rem;">✅ Kaydet</button>
            <button onclick="cancelCommentEdit(${c.id})" class="btn-sm btn-gray" style="font-size:.8rem;">İptal</button>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
          <span style="font-size:.72rem;color:var(--muted);">📅 ${c.created_at}</span>
          ${c.recipe_url ? `<a href="${esc(c.recipe_url)}" target="_blank" rel="noopener" style="font-size:.76rem;color:#cfae55;text-decoration:none;">Tarife Git →</a>` : ''}
          <button onclick="startCommentEdit(${c.id})" style="margin-left:auto;background:rgba(207,174,85,.12);border:1px solid rgba(207,174,85,.3);color:#cfae55;border-radius:8px;padding:4px 10px;font-size:.76rem;cursor:pointer;">✏️ Düzenle</button>
          <button onclick="deleteMyComment(${c.id})" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#ef4444;border-radius:8px;padding:4px 10px;font-size:.76rem;cursor:pointer;">🗑️ Sil</button>
        </div>
      </div>
    </div>
  `).join('');
    }

    function startCommentEdit(id) {
      document.getElementById('comment-text-' + id).style.display = 'none';
      document.getElementById('comment-edit-area-' + id).style.display = 'block';
    }
    function cancelCommentEdit(id) {
      document.getElementById('comment-text-' + id).style.display = 'block';
      document.getElementById('comment-edit-area-' + id).style.display = 'none';
    }
    async function saveCommentEdit(id) {
      const text = document.getElementById('comment-edit-input-' + id)?.value?.trim();
      if (!text) { toast('Yorum boş olamaz.', 'error'); return; }
      const r = await api('comment_edit', { id, comment_text: text });
      if (r.success) {
        document.getElementById('comment-text-' + id).textContent = text;
        cancelCommentEdit(id);
        toast('✅ Yorum güncellendi.', 'success');
      } else {
        toast(r.message || 'Güncelleme başarısız.', 'error');
      }
    }
    async function deleteMyComment(id) {
      if (!confirm('Bu yorumu silmek istediğinize emin misiniz?')) return;
      const r = await api('comment_delete', { id });
      if (r.success) {
        const card = document.getElementById('comment-card-' + id);
        if (card) card.remove();
        toast('🗑️ Yorum silindi.', 'success');
        const container = document.getElementById('myCommentsList');
        if (container && !container.querySelector('[id^="comment-card-"]')) {
          container.innerHTML = '<div style="text-align:center;padding:40px 20px;"><div style="font-size:3rem;margin-bottom:12px;">💬</div><p style="color:var(--muted);">Henüz yorum yapılmamış.</p></div>';
        }
      } else {
        toast(r.message || 'Silme başarısız.', 'error');
      }
    }
    const tipsData = [{
        icon: '🧂',
        title: 'Tuz Tüketimi',
        text: 'Günlük tuz alımı 5 gramı (yaklaşık 1 çay kaşığı) geçmemelidir. Fazla tuz; hipertansiyon, kalp hastalığı ve böbrek sorunlarına yol açar.',
        detail: '💡 <b>Ne yapmalısın?</b> Yemeklerinizi pişirirken az tuz kullanın. Sofrada tuzluk bulundurmayın. Hazır gıdaların etiketlerini okuyun; 100g\'da 1.5g üzeri tuz olan ürünleri sınırlayın. Baharat, limon ve sarımsak mükemmel tuz alternatifidir.<br><br>📊 <b>Türkiye\'de ortalama tuz tüketimi</b> günlük 18g ile sağlıklı sınırın 3-4 katı.<br><br>⚕️ Kaynak: HSGM Türkiye Beslenme Rehberi 2022',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'PDF Broşür',
        color: '#ef4444'
      },
      {
        icon: '🚶‍♀️',
        title: 'Fiziksel Aktivite',
        text: 'Haftada en az 150 dakika orta yoğunlukta aerobik aktivite yapılmalıdır. Bu, günde sadece 22 dakika yürüyüş demektir!',
        detail: '💡 <b>Pratik öneriler:</b> Asansör yerine merdiven kullanın. İşe 1 durak erken inin, yürüyün. Telefon görüşmelerini yürüyerek yapın. Her öğünden 10 dk sonra kısa bir yürüyüş yapın.<br><br>🔥 <b>Avantajları:</b> Kalp sağlığı, kilo kontrolü, uyku kalitesi, ruh hali, kemik yoğunluğu ve bağışıklık sistemi güçlenir.<br><br>⚕️ Kaynak: DSÖ & HSGM Fiziksel Aktivite Rehberi',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'Rehber',
        color: '#22c55e'
      },
      {
        icon: '🥗',
        title: 'Meyve-Sebze Tüketimi',
        text: 'Her gün en az 5 porsiyon (yaklaşık 400g) meyve ve sebze tüketmek; kalp hastalığı, diyabet ve kanser riskini önemli ölçüde azaltır.',
        detail: '💡 <b>Porsiyon rehberi:</b> 1 orta boy elma = 1 porsiyon. 1 avuç kiraz = 1 porsiyon. 3 yemek kaşığı pişmiş ıspanak = 1 porsiyon.<br><br>🌈 <b>Renk çeşitliliği önemli!</b> Her renk farklı antioksidan barındırır: Kırmızı (domates) = Likopen, Turuncu (havuç) = Beta-karoten, Yeşil (brokoli) = K vitamini, Mor (patlıcan) = Antosiyanin.<br><br>⚕️ Kaynak: HSGM Beslenme Piramidi Rehberi',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'Broşür',
        color: '#22c55e'
      },
      {
        icon: '💧',
        title: 'Yeterli Su İçme',
        text: 'Yetişkinler günde ortalama 1.5-2 litre (8 bardak) su içmelidir. Su; vücut ısısını düzenler, toksinleri atar, enerji verir.',
        detail: '💡 <b>Su içmeyi kolaylaştır:</b> Masanıza her zaman bir şişe su koyun. Suya limon/nane/salatalık ekleyin. Telefona her saat hatırlatma kurun. Açlık hissetmeden önce su için — çoğu zaman susuzluk açlık olarak hissedilir.<br><br>⚠️ <b>Dikkat:</b> Egzersiz, sıcak hava ve hastalık dönemlerinde ihtiyaç artar. İdrar açık sarı renkteyse hidrasyon iyidir.<br><br>⚕️ Kaynak: HSGM Su Tüketim Rehberi',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'Bilgi Notu',
        color: '#3b82f6'
      },
      {
        icon: '🥛',
        title: 'Süt & Süt Ürünleri',
        text: 'Kalsiyum ve D vitamini için günde 2-3 porsiyon süt/yoğurt/peynir tüketmelisiniz. Bu, kemik sağlığı ve kas fonksiyonu için kritiktir.',
        detail: '💡 <b>Kaliteli kaynaklar:</b> 1 su bardağı süt (~300mg Ca), 1 kase yoğurt (~250mg Ca), 30g peynir (~200mg Ca).<br><br>🦴 <b>Osteoporoz riski:</b> 18-30 yaş arası kemik yoğunluğu doruğa ulaşır. Bu dönemde yeterli kalsiyum almak, ilerleyen yaşlarda kırık riskini %50 azaltabilir.<br><br>🌱 <b>Laktoz intoleransı için:</b> Laktoz içermeyen ürünler, kefir, fındık sütü veya kalsiyumla zenginleştirilmiş bitkisel sütler tercih edilebilir.<br><br>⚕️ Kaynak: HSGM Kalsiyum & Kemik Sağlığı',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'Bilgi Notu',
        color: '#a78bfa'
      },
      {
        icon: '🥩',
        title: 'Sağlıklı Protein Seçimi',
        text: 'İşlenmiş et (salam, sosis, sucuk) tüketimini haftada 1-2 kez ile sınırlayın. Baklagiller, balık ve yumurta mükemmel protein alternatifleridir.',
        detail: '💡 <b>Protein kaynakları karşılaştırması:</b><br>✅ Baklagiller (kuru fasulye, nohut, mercimek) — Lif + protein + demir kombinasyonu, ucuz ve doyurucu<br>✅ Yumurta — Tam aminoasit profili, A/D/E vitamini<br>✅ Balık (haftada 2 kez) — Omega-3 yağ asitleri<br>⚠️ Kırmızı et — Haftada 2-3 kez, az pişmiş değil<br>❌ Salam/sucuk — Nitrat ve sodyum yüksek, WHO Grup 2A kanserojen<br><br>⚕️ Kaynak: HSGM Protein Tüketim Rehberi',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'Broşür',
        color: '#f97316'
      },
      {
        icon: '🫒',
        title: 'Yağ Tüketimi',
        text: 'Trans yağ ve doymuş yağ yerine zeytinyağı, ceviz, fındık gibi doymamış yağları tercih edin. Yağ kalitesi, miktarı kadar önemlidir.',
        detail: '💡 <b>Yağ rehberi:</b><br>✅ Zeytinyağı — Akdeniz diyetinin temeliyidir, kalp dostu<br>✅ Ceviz/badem/fındık — Omega-3, E vitamini<br>✅ Avokado — Tekli doymamış yağ<br>⚠️ Tereyağı — Ölçülü kullanın<br>❌ Margarin/katı yağ — Trans yağ içerir<br>❌ Kızartma yağı — Tekrar kullanılan yağ toksik bileşik oluşturur<br><br>🔥 <b>Pişirme ipucu:</b> Zeytinyağı 180°C üzerinde bozulur — kızartma için değil, salata/son aşamada kullanın.<br><br>⚕️ Kaynak: HSGM Yağ Tüketim Kılavuzu',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'Bilgi Notu',
        color: '#cfae55'
      },
      {
        icon: '🍞',
        title: 'Tam Tahıl Tercihi',
        text: 'Beyaz ekmek ve pirinç yerine tam buğday, bulgur, yulaf ve çavdar tercih edin. Glisemik indeksi düşük karbonhidratlar kan şekerini dengeler.',
        detail: '💡 <b>Neden tam tahıl?</b> Beyaz un işleme sırasında kepek ve tohum özü çıkarılır — yani lif, B vitamini, demir ve magnezyum kaybolur.<br><br>📊 <b>Glisemik indeks karşılaştırması:</b><br>Bulgur pilavı: 48 (düşük) ✅<br>Basmati pirinç: 52 (orta) ✅<br>Beyaz ekmek: 71 (yüksek) ⚠️<br>Beyaz pirinç: 73 (yüksek) ⚠️<br><br>🌾 <b>Tavsiye:</b> Öğün başına tahıl porsiyon boyutunu yarım tabakla sınırlayın; diğer yarısını sebze doldurun.<br><br>⚕️ Kaynak: HSGM Karbonhidrat Rehberi',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'PDF Broşür',
        color: '#b45309'
      },
      {
        icon: '🍬',
        title: 'Şeker & İşlenmiş Gıda',
        text: 'Günlük serbest şeker alımı toplam enerjinin %10\'undan az (yaklaşık 50g/12 tatlı kaşığı) olmalıdır. Şekerli içecekler en büyük tehlikedir.',
        detail: '💡 <b>Gizli şeker tuzakları:</b><br>1 kutu kola = 35g şeker 🥤<br>1 bardak meyve suyu = 25g şeker 🧃<br>1 paket bisküvi = 20g şeker 🍪<br>1 kaşık ketçap = 4g şeker<br><br>🔍 <b>Etiket okuma ipuçları:</b> "Fruktoz", "mısır şurubu", "dekstroz", "maltoz" hepsi şeker! Bileşenler listesinde ilk sıralarda görmek tehlike işaretidir.<br><br>⚠️ <b>Türkiye\'de:⚠️</b> Şeker tüketimi son 20 yılda 3 katına çıktı. Çocuklarda obezite %20\'yi aşıyor.<br><br>⚕️ Kaynak: DSÖ & HSGM Şeker Azaltma Kılavuzu',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'Broşür',
        color: '#ec4899'
      },
      {
        icon: '🧠',
        title: 'Beyin & Bağırsak Sağlığı',
        text: 'Bağırsak mikrobiyomunuz ruh halinizi, bağışıklık sisteminizi ve beyin fonksiyonlarınızı doğrudan etkiler. Fermente ve lif açısından zengin gıdalar tüketin.',
        detail: '💡 <b>Bağırsak dostları:</b><br>✅ Yoğurt, kefir, turşu — Probiyotik kaynakları<br>✅ Soğan, sarımsak, muz — Prebiyotik (iyi bakterilerin besini)<br>✅ Sebzeler, baklagiller — Çözünür lif<br>✅ Fermente besinler — Mikrobiom çeşitliliği<br><br>🧬 <b>Bağırsak-beyin aksı:</b> Seratoninin %90\'ı bağırsakta üretilir. İyi beslenme = iyi ruh hali.<br><br>⚠️ <b>Dikkat:</b> Antibiyotikler, yüksek şeker, işlenmiş gıdalar ve stres mikrobiyomu bozar.<br><br>⚕️ Kaynak: Uluslararası Beslenme & Mikrobiyom Araştırmaları',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'Bilgi Notu',
        color: '#8b5cf6'
      },
      {
        icon: '😴',
        title: 'Uyku & Beslenme İlişkisi',
        text: '7-9 saat kaliteli uyku; metabolizma, hormon dengesi ve yeme davranışı üzerinde doğrudan etki eder. Uyku eksikliği açlık hormonunu artırır.',
        detail: '💡 <b>Uyku ve açlık hormonları:</b><br>Az uyku → Ghrelin (açlık hormonu) ↑ → Aşırı yeme<br>Az uyku → Leptin (tokluk hormonu) ↓ → Tokluk hissedememe<br>Az uyku → Kortizol ↑ → Karın bölgesi yağlanması<br><br>🌙 <b>Uyku kalitesini artıran besinler:</b><br>Kiraz — Melatonin kaynağı<br>Ceviz — Melatonin + omega-3<br>Papatya çayı — Apigenin rahatlatıcı<br>Magnezyum (muz, yeşil yapraklılar) — Sinir sistemi rahatlatır<br><br>⚠️ Yatmadan 2 saat önce ağır öğün yemeyin.<br><br>⚕️ Kaynak: HSGM Uyku & Sağlık Rehberi',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'Bilgi Notu',
        color: '#6366f1'
      },
      {
        icon: '🫀',
        title: 'Kalp Sağlığı & Beslenme',
        text: 'Kalp-damar hastalıkları Türkiye\'de ölüm nedenlerinin başında gelir. Akdeniz tipi beslenme, kalp hastalığı riskini %30 oranında azaltır.',
        detail: '💡 <b>Kalbe dostane beslenme:</b><br>✅ Zeytinyağı, balık, kuruyemiş — Omega-3 ve tekli doymamış yağlar<br>✅ Sebze-meyve çeşitliliği — Antioksidanlar<br>✅ Baklagiller — Kolesterol düşürür<br>✅ Tam tahıllar — LDL kolesterolü azaltır<br>❌ Trans yağ — Damarları sertleştirir<br>❌ Aşırı tuz — Hipertansiyon<br>❌ Şekerli içecekler — Trigliserit yükseltir<br><br>📊 <b>Hedef değerler:</b><br>LDL < 100 mg/dl, Tansiyon < 120/80 mmHg<br><br>⚕️ Kaynak: Türk Kardiyoloji Derneği & HSGM',
        link: 'https://hsgm.saglik.gov.tr',
        type: 'PDF Broşür',
        color: '#ef4444'
      }
    ];
    let tipsRead = parseInt(localStorage.getItem('tipsRead') || '0');

    /**
     * renderTips — Sağlık rehberi kartlarını (tipsData) DOM'a çizer.
     *
     * Her kart; ikon, başlık, kısa açıklama, detay alanı ve kaynak linki içerir.
     * Karta tıklanınca tipsRead sayacı artar ve localStorage'a yazılır.
     * Kart okunduğunda 'tips' (Bilinçli Seçim 🧠) günlük rozeti tetiklenir.
     */
    function renderTips() {
      document.getElementById('tipsGrid').innerHTML = tipsData.map((t, i) => `
    <article class="tipCard" data-idx="${i}" style="border-top:4px solid ${t.color||'var(--gold)'};">
      <div class="tipHead">
        <div class="tipIcon" style="background:${t.color||'var(--gold)'}22;border-radius:12px;padding:8px;">${t.icon}</div>
        <div style="flex:1;">
          <h3 style="margin:0 0 4px;">${t.title}</h3>
          <p style="margin:0;font-size:.87rem;color:var(--ink,#333);">${t.text}</p>
        </div>
      </div>
      <div class="tip-detail" id="tipDetail${i}" style="display:none;margin-top:12px;padding:14px;background:var(--bg,#f8f8f8);border-radius:10px;font-size:.86rem;line-height:1.7;color:var(--ink,#333);">${t.detail||''}</div>
      <div class="tip-footer" style="margin-top:12px;">
        <button class="btn-sm" style="background:${t.color||'var(--gold)'}22;color:${t.color||'#b8963e'};border:1.5px solid ${t.color||'#cfae55'};border-radius:8px;padding:5px 12px;cursor:pointer;font-weight:700;font-size:.8rem;" onclick="toggleTipDetail(${i},event)">📖 Detayları Göster</button>
        <a href="${t.link}" target="_blank" class="tip-download-btn" style="margin-left:8px;">📥 ${t.type}</a>
        <span class="tip-source" style="float:right;font-size:.75rem;color:var(--muted);">HSGM Kaynakl</span>
      </div>
    </article>
  `).join('');
      document.querySelectorAll('.tipCard').forEach(el => {
        el.addEventListener('click', e => {
          if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') return;
          tipsRead++;
          localStorage.setItem('tipsRead', tipsRead);
          checkBadge('tips', tipsRead >= 5);
        });
      });
    }

    /**
     * toggleTipDetail — Sağlık kartının detay alanını açar/kapatır.
     *
     * @param {number} i - tipsData dizisindeki kart indeksi.
     * @param {Event}  e - Tıklama olayı (bubble propagation durdurulur).
     *
     * Detay açılınca tipsRead artırılır ve rozet koşulu tekrar denetlenir.
     */
    function toggleTipDetail(i, e) {
      e.stopPropagation();
      const d = document.getElementById('tipDetail' + i);
      const btn = e.target;
      const isOpen = d.style.display !== 'none';
      d.style.display = isOpen ? 'none' : 'block';
      btn.textContent = isOpen ? '📖 Detayları Göster' : '🔼 Kapat';
      if (!isOpen) {
        tipsRead++;
        localStorage.setItem('tipsRead', tipsRead);
        checkBadge('tips', tipsRead >= 5);
      }
    }

    // ════════════════════════════════════════════ TARİFLER (statik)
    const shots = [{
        id: 'shot1',
        title: 'Cacık Shot',
        tag: '5 dk',
        img: 'https://images.unsplash.com/photo-1495214783159-3503fd1b572d?w=400',
        time: '5 dk',
        level: 'Çok Kolay',
        cal: '120 kcal',
        ingredients: ['4 kaşık yoğurt', '1 salatalık', 'Nane + tuz'],
        steps: ['Salatalığı rendele', 'Yoğurtla karıştır', 'Nane ekle'],
        filters: ['cokk']
      },
      {
        id: 'shot2',
        title: 'Muzlu Yulaf',
        tag: '7 dk',
        img: 'https://images.unsplash.com/photo-1517673408408-cd1f75b68c2a?w=400',
        time: '7 dk',
        level: 'Çok Kolay',
        cal: '280 kcal',
        ingredients: ['3 kaşık yulaf', '1 muz', 'Süt'],
        steps: ['Yulafı sütle karıştır', 'Muzu dilimle', 'Üstüne ekle'],
        filters: ['cokk', 'kahvalti']
      },
      {
        id: 'shot3',
        title: 'Yeşil Smoothie',
        tag: '5 dk',
        img: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400',
        time: '5 dk',
        level: 'Çok Kolay',
        cal: '140 kcal',
        ingredients: ['1 elma', '1 avuç ıspanak', '1 bardak su'],
        steps: ['Hepsini blendera at', '30 sn çek', 'Soğuk iç'],
        filters: ['cokk', 'icecek']
      }
    ];
    const recipes = [{
        id: 'mercimek',
        title: 'Kırmızı Mercimek Çorbası',
        img: 'https://images.unsplash.com/photo-1547592180-85f173990554?w=400',
        time: '20 dk',
        level: 'Kolay',
        cal: '180 kcal',
        tags: ['Vejetaryen', 'Protein'],
        filters: ['kolay', 'protein'],
        ingredients: ['1 sb kırmızı mercimek', '1 soğan', '1 kaşık salça', '6 sb su', 'Kimyon+karabiber'],
        steps: ['Mercimeği yıka', 'Soğanı çevir', 'Salça ekle', 'Su+mercimek koy', 'Pişince blender']
      },
      {
        id: 'bulgur',
        title: 'Bulgur Salatası',
        img: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400',
        time: '30 dk',
        level: 'Kolay',
        cal: '250 kcal',
        tags: ['Lifli', 'Vejetaryen'],
        filters: ['kolay'],
        ingredients: ['1 sb bulgur', 'Domates+salatalık', 'Maydanoz', 'Zeytinyağı', 'Limon'],
        steps: ['Bulguru şişir', 'Sebzeleri doğra', 'Yeşillik ekle', 'Zeytinyağı+limon']
      },
      {
        id: 'yulaf',
        title: 'Meyveli Yulaf',
        img: 'https://images.unsplash.com/photo-1517673408408-cd1f75b68c2a?w=400',
        time: '10 dk',
        level: 'Çok Kolay',
        cal: '300 kcal',
        tags: ['Kahvaltı'],
        filters: ['cokk', 'kahvalti'],
        ingredients: ['3 kaşık yulaf', '1 sb süt', 'Muz/çilek', 'Tarçın'],
        steps: ['Yulafı sütle ısıt', 'Meyve ekle', 'Tarçın serpip ye']
      },
      {
        id: 'balik',
        title: 'Fırında Somon',
        img: 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=400',
        time: '25 dk',
        level: 'Kolay',
        cal: '350 kcal',
        tags: ['Omega-3', 'Protein'],
        filters: ['kolay', 'protein'],
        ingredients: ['2 somon fileto', 'Zeytinyağı', 'Limon suyu', 'Karabiber'],
        steps: ['Somonu kurula', 'Zeytinyağı+limon ov', '200°C 20dk pişir', 'Salatayla servis']
      },
      {
        id: 'smoothie',
        title: 'Yeşil Detoks',
        img: 'https://images.unsplash.com/photo-1497534446932-c925b458314e?w=400',
        time: '5 dk',
        level: 'Çok Kolay',
        cal: '120 kcal',
        tags: ['İçecek', 'Detoks'],
        filters: ['cokk', 'icecek'],
        ingredients: ['1 elma', '1 avuç ıspanak', 'Yarım limon', '1 sb su'],
        steps: ['Blendera at', 'Pürüzsüz çek', 'Soğuk iç']
      },
      {
        id: 'omlet',
        title: 'Sebzeli Omlet',
        img: 'https://images.unsplash.com/photo-1510693206972-df098062cb71?w=400',
        time: '10 dk',
        level: 'Kolay',
        cal: '220 kcal',
        tags: ['Kahvaltı', 'Protein'],
        filters: ['kolay', 'kahvalti', 'protein'],
        ingredients: ['3 yumurta', '1 sivri biber', '1 domates', 'Tuz+karabiber', 'Zeytinyağı'],
        steps: ['Yumurtaları çırp', 'Sebzeleri doğra', 'Tavada pişir', 'Katla servis et']
      },
      {
        id: 'tavuk-sote',
        title: 'Tavuk Sote',
        img: 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=400',
        time: '25 dk',
        level: 'Kolay',
        cal: '310 kcal',
        tags: ['Protein', 'Ana Yemek'],
        filters: ['kolay', 'protein'],
        ingredients: ['300g tavuk', '2 biber', '1 soğan', 'Zeytinyağı', 'Baharat'],
        steps: ['Tavuğu küp kes', 'Soğanı kavur', 'Tavuk+biberi ekle', 'Baharatla pişir']
      },
      {
        id: 'kefir-smoothie',
        title: 'Kefir Meyveli Smoothie',
        img: 'https://images.unsplash.com/photo-1505252585461-04db1eb84625?w=400',
        time: '5 dk',
        level: 'Çok Kolay',
        cal: '180 kcal',
        tags: ['İçecek', 'Probiyotik'],
        filters: ['cokk', 'icecek', 'kahvalti'],
        ingredients: ['1 sb kefir', '1 muz', '1 avuç çilek', '1 tatlı kaşığı bal'],
        steps: ['Hepsini blendera at', '30 sn çek', 'Soğuk iç']
      }
    ];

    let activeFilter = 'all';

    /**
     * renderShots — Hızlı tarif kartlarını (shots dizisi) DOM'a çizer.
     *
     * shots[] statik veri dizisini map'leyerek HTML kartları üretir.
     * Her karta tıklanınca openModal() ile tarif detay modalı açılır.
     */
    function renderShots() {
      document.getElementById('shotsGrid').innerHTML = shots.map(s => `
    <article class="shot" data-id="${s.id}">
      <div class="shotTop"><b>${s.title}</b><span>${s.tag}</span></div>
      <div class="shotBody">
        <div style="height:130px;border-radius:16px;background:url('${s.img}') center/cover;margin-bottom:8px;"></div>
        <ul><li>Malzeme: ${s.ingredients.slice(0,3).join(' • ')}</li><li>Süre: ${s.time} • ${s.level}</li></ul>
      </div>
    </article>
  `).join('');
      document.querySelectorAll('.shot').forEach(el => el.addEventListener('click', () => openModal(shots.find(x => x.id === el.dataset.id))));
    }

    /**
     * renderRecipeGrid — Ana tarif ızgarasını filtreler ve DOM'a çizer.
     *
     * Arama kutusu (searchInput) ve aktif filtre chip'i (activeFilter) birlikte uygulanır.
     * Zorluk filtresi: 'all' | 'cokk' (Çok Kolay) | 'kolay' | diğer kategoriler.
     * Her kart için beğeni/kaydet durumu recipeInteractionCache'ten alınır.
     */
    function renderRecipeGrid() {
      const q = (document.getElementById('searchInput')?.value || '').toLowerCase();
      const list = recipes.filter(r => {
        if (activeFilter === 'all') return true;
        if (activeFilter === 'kolay') return r.level.includes('Kolay') && !r.level.includes('Çok');
        if (activeFilter === 'cokk') return r.level.includes('Çok Kolay');
        return r.filters.includes(activeFilter);
      }).filter(r => !q || r.title.toLowerCase().includes(q));
      document.getElementById('recipeGrid').innerHTML = list.map(r => {
        const key = r.id;
        const cached = recipeInteractionCache[key] || {};
        const liked = cached.my_like || false;
        const saved = cached.my_save || false;
        return `<article class="recipeCard">
      <div class="rimg" style="background-image:url('${r.img}')" onclick="openModal(recipes.find(x=>x.id==='${r.id}'))"><div class="badgeTiny">✨ Tıkla</div><div class="rTagRow">${r.tags.map(t=>`<span class="rTag">${t}</span>`).join('')}</div></div>
      <div class="rBody">
        <h3>${esc(r.title)}</h3>
        <div class="rMeta"><span>⏱️ ${r.time}</span><span>🔥 ${r.level}</span><span>🍽️ ${r.cal}</span></div>
        <div class="recipe-actions" style="margin-top:8px;">
          <button class="btn-like ${liked?'liked':''}" data-like-key="${key}" onclick="toggleRecipeLike('${key}','${esc(r.title)}','${r.img}','')">
            ${liked?'❤️ Beğenildi':'🤍 Beğen'}
          </button>
          <button class="btn-save-rec ${saved?'saved':''}" data-save-key="${key}" onclick="toggleRecipeSave('${key}','${esc(r.title)}','${r.img}','','${r.ingredients.join('\\n')}','${r.steps.join('\\n')}')">
            ${saved?'📖 Kaydedildi':'🔖 Kaydet'}
          </button>
          <button class="btn-comment" onclick="openCommentModal('${key}','${esc(r.title)}','${r.img}','')">💬</button>
        </div>
      </div>
    </article>`;
      }).join('');
    }
    // Filtreleme butonları — sadece #chips içindekiler (zamanlayıcı chip'leriyle çakışmasın)
    document.querySelectorAll('#chips .chip').forEach(ch => {
      ch.addEventListener('click', () => {
        document.querySelectorAll('#chips .chip').forEach(x => x.classList.remove('active'));
        ch.classList.add('active');
        activeFilter = ch.dataset.filter;
        renderRecipeGrid();
      });
    });
    document.getElementById('searchInput').addEventListener('input', renderRecipeGrid);

    // ── Topluluk Tarifleri (Kullanıcı Paylaşımları) ──────────────
    // api.php → community_recipes ile paginate edilmiş tarifler çekilir (limit=12).
    // communityOffset arttıkça farklı sayfalar yüklenir (sonsuz kaydırma simülasyonu).
    // Tıklanınca openCommunityRecipeModal() ile tarif detayı gösterilir.
    let communityOffset = 0;
    let communityLoading = false;
    const COMMUNITY_LIMIT = 12;

    function switchRecipeTab(tab) {
      const allPanel = document.getElementById('recipeTabAll');
      const comPanel = document.getElementById('recipeTabCommunity');
      const btnAll = document.getElementById('tabRecipesAll');
      const btnCom = document.getElementById('tabRecipesCommunity');
      if (tab === 'all') {
        allPanel.style.display = '';
        comPanel.style.display = 'none';
        btnAll.className = 'btn-sm btn-gold';
        btnCom.className = 'btn-sm btn-gray';
      } else {
        allPanel.style.display = 'none';
        comPanel.style.display = '';
        btnAll.className = 'btn-sm btn-gray';
        btnCom.className = 'btn-sm btn-gold';
        if (communityOffset === 0) loadCommunityRecipes();
      }
    }

    async function loadCommunityRecipes(append = false) {
      if (communityLoading) return;
      communityLoading = true;
      const grid = document.getElementById('communityRecipeGrid');
      const loadMoreBtn = document.getElementById('communityLoadMore');
      if (!append) { grid.innerHTML = '<p style="color:var(--muted);text-align:center;padding:20px;">⏳ Yükleniyor...</p>'; communityOffset = 0; }
      try {
        const res = await fetch('api/community-recipes?offset=' + communityOffset + '&limit=' + COMMUNITY_LIMIT);
        const json = await res.json();
        if (!json.success) { grid.innerHTML = '<p style="color:var(--muted);text-align:center;padding:20px;">Tarif bulunamadı.</p>'; return; }
        const items = json.data.items || [];
        const hasMore = json.data.hasMore || false;
        if (!append) grid.innerHTML = '';
        if (items.length === 0 && !append) {
          grid.innerHTML = '<p style="color:var(--muted);text-align:center;padding:30px;grid-column:1/-1;">Henüz topluluk tarifi eklenmemiş.<br><a href="tarifler" style="color:#cfae55;">Tariflere git →</a></p>';
          return;
        }
        items.forEach(item => {
          const card = document.createElement('div');
          card.style.cssText = 'background:var(--card,#1e1e1e);border:1px solid var(--border,#2a2a2a);border-radius:12px;overflow:hidden;cursor:pointer;';
          card.onclick = () => openCommunityRecipeModal(item);
          const imgStyle = item.image_url ? `background:url('${esc(item.image_url)}') center/cover` : 'background:linear-gradient(135deg,#1a1a1a,#2a2a2a)';
          card.innerHTML = `
            <div style="height:160px;${imgStyle};display:flex;align-items:flex-end;padding:10px;">
              <span style="background:rgba(0,0,0,.7);color:#FFD700;font-size:.7rem;font-weight:700;padding:3px 8px;border-radius:6px;">👤 ${esc(item.user_name||'Kullanıcı')}</span>
            </div>
            <div style="padding:12px;">
              <h4 style="font-size:.95rem;font-weight:700;margin-bottom:6px;color:var(--text,#e0e0e0);">${esc(item.title)}</h4>
              ${item.ingredients_preview ? `<p style="font-size:.78rem;color:var(--muted,#777);margin-bottom:8px;">${esc(item.ingredients_preview)}</p>` : ''}
              <div style="font-size:.72rem;color:var(--muted,#777);">📅 ${esc(item.saved_at||'')}</div>
            </div>`;
          grid.appendChild(card);
        });
        communityOffset += items.length;
        loadMoreBtn.style.display = hasMore ? 'block' : 'none';
      } catch(e) { grid.innerHTML = '<p style="color:var(--muted);text-align:center;padding:20px;">Yükleme hatası.</p>'; }
      communityLoading = false;
    }

    function openCommunityRecipeModal(item) {
      const overlay = document.getElementById('modalOverlay');
      document.getElementById('modalHero').style.backgroundImage = item.image_url ? `url("${item.image_url}")` : 'linear-gradient(135deg,#1a1a1a,#2a2a2a)';
      document.getElementById('modalTitle').textContent = item.title;
      document.getElementById('modalTime').textContent = '👤 ' + (item.user_name || 'Kullanıcı');
      document.getElementById('modalLevel').textContent = '📅 ' + (item.saved_at || '');
      document.getElementById('modalCal').textContent = '';
      const ingLines = (item.ingredients || '').split('\n').filter(Boolean).map(l => `<li>${esc(l.trim())}</li>`).join('');
      document.getElementById('modalIng').innerHTML = ingLines || '<li style="color:var(--muted)">Belirtilmemiş</li>';
      const stepLines = (item.instructions || '').split('\n').filter(Boolean).map(l => `<li>${esc(l.trim())}</li>`).join('');
      document.getElementById('modalSteps').innerHTML = stepLines || '<li style="color:var(--muted)">Belirtilmemiş</li>';
      // clear action buttons for community recipes
      const actionArea = document.getElementById('modalRecipeActions');
      if (actionArea) actionArea.innerHTML = '';
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    // ── Tarif modal (statik tarifler için) ──────────────────────
    const overlay = document.getElementById('modalOverlay');

    /**
     * openModal — Statik tarif kartı için detay modalını açar.
     *
     * @param {object} data - shots[] veya recipes[] dizisinden gelen tarif nesnesi.
     *                        {id, title, img, time, level, cal, ingredients, steps}
     *
     * Modal içeriğini doldurur: görsel, başlık, süre, zorluk, kalori,
     * malzeme listesi ve yapılış adımları.
     * Beğeni/kaydet/yorum butonları recipeInteractionCache'ten okunarak güncellenir.
     */
    function openModal(data) {
      if (!data) return;
      document.getElementById('modalHero').style.backgroundImage = `url("${data.img}")`;
      document.getElementById('modalTitle').textContent = data.title;
      document.getElementById('modalTime').textContent = '⏱️ ' + data.time;
      document.getElementById('modalLevel').textContent = '🔥 ' + data.level;
      document.getElementById('modalCal').textContent = '🍽️ ' + data.cal;
      document.getElementById('modalIng').innerHTML = data.ingredients.map(x => `<li>${x}</li>`).join('');
      document.getElementById('modalSteps').innerHTML = data.steps.map(x => `<li>${x}</li>`).join('');
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      // Etkileşim butonlarını ekle/güncelle
      const cached = recipeInteractionCache[data.id] || {};
      let actionArea = document.getElementById('modalRecipeActions');
      if (!actionArea) {
        actionArea = document.createElement('div');
        actionArea.id = 'modalRecipeActions';
        actionArea.className = 'recipe-actions';
        actionArea.style.cssText = 'padding:0 24px 16px;';
        const btnArea = document.querySelector('.modal-footer-btns') || document.getElementById('btnCook')?.parentElement;
        if (btnArea) btnArea.parentElement.insertBefore(actionArea, btnArea);
        else document.getElementById('modalOverlay').querySelector('.modal')?.appendChild(actionArea);
      }
      const liked = cached.my_like || false;
      const saved = cached.my_save || false;
      actionArea.innerHTML = `
    <button class="btn-like ${liked?'liked':''}" data-like-key="${data.id}" onclick="toggleRecipeLike('${data.id}','${esc(data.title)}','${data.img}','');renderRecipeGrid();">${liked?'❤️ Beğenildi':'🤍 Beğen'}</button>
    <button class="btn-save-rec ${saved?'saved':''}" data-save-key="${data.id}" onclick="toggleRecipeSave('${data.id}','${esc(data.title)}','${data.img}','','${data.ingredients.join('\\n')}','${data.steps.join('\\n')}');renderRecipeGrid();">${saved?'📖 Kaydedildi':'🔖 Tarif Defterime Kaydet'}</button>
    <button class="btn-comment" onclick="openCommentModal('${data.id}','${esc(data.title)}','${data.img}','')">💬 Yorum</button>
  `;
    }

    /**
     * closeModal — Tarif detay modalını kapatır ve sayfa scroll'unu geri açar.
     */
    function closeModal() {
      overlay.classList.remove('active');
      document.body.style.overflow = 'auto';
    }
    document.getElementById('modalClose').addEventListener('click', closeModal);
    document.getElementById('btnClose2').addEventListener('click', closeModal);
    overlay.addEventListener('click', e => {
      if (e.target === overlay) closeModal();
    });
    document.getElementById('btnCook').addEventListener('click', () => {
      checkBadge('chef', true);
      toast('🍳 Ev Şefi rozeti kazanıldı!', 'success');
      closeModal();
    });
    document.getElementById('btnPrintRecipe').addEventListener('click', () => window.print());

    // ════════════════════════════════════════════ MUTFAK ZAMANLAYICISI
    // TimerModule: IIFE (Immediately Invoked Function Expression) ile kapsüllendi.
    //   Böylece iç değişkenler (interval, total, remaining, running, paused)
    //   global scope'u kirletmez; yalnızca module metodları dışarıya açılır.
    // Çalışma mantığı:
    //   • SVG circle stroke-dashoffset animasyonu → görsel geri sayım halka efekti.
    //     circumference = 2πr; dashoffset azaldıkça halka dolup boşalır.
    //   • playAlarm() → Web Audio API ile tarayıcıda ses üretir (harici dosya gerekmez).
    //     Oscillator → Gain → AudioContext; 0.5s sine dalgası 880Hz alarm sesi.
    //   • Preset chip'leri (1/5/10/30 dk) → input alanlarını otomatik doldurur.
    //   • Süre bitince modal (#timer-modal) gösterilir ve patlama sesi çalar.
    const TimerModule = (() => {
      const circle = document.getElementById('progress-circle');
      if (!circle) return {
        reset: () => {}
      };
      const circumference = circle.r.baseVal.value * 2 * Math.PI;
      circle.style.strokeDasharray = `${circumference} ${circumference}`;
      circle.style.strokeDashoffset = 0;
      let interval = null,
        total = 0,
        remaining = 0,
        running = false,
        paused = false;
      const display = document.getElementById('time-display');
      const inMin = document.getElementById('in-min'),
        inSec = document.getElementById('in-sec');
      const inputGroup = document.getElementById('input-group');
      const potWrapper = document.getElementById('potWrapper');
      const statusLabel = document.getElementById('status-label');
      const btnStart = document.getElementById('btn-big-start');
      const iconControls = document.getElementById('icon-controls');
      const btnPause = document.getElementById('btn-pause');
      const btnReset = document.getElementById('btn-reset');
      const modal = document.getElementById('timer-modal');
      const modalOk = document.getElementById('timer-modal-ok');

      function setProgress(pct) {
        circle.style.strokeDashoffset = circumference - (pct / 100) * circumference;
      }

      function updateDisplay(s) {
        const m = Math.floor(s / 60),
          sc = s % 60;
        display.textContent = String(m).padStart(2, '0') + ':' + String(sc).padStart(2, '0');
      }

      function playAlarm() {
        try {
          const ctx = new(window.AudioContext || window.webkitAudioContext)();
          const o = ctx.createOscillator();
          const g = ctx.createGain();
          o.type = 'sine';
          o.frequency.value = 880;
          g.gain.setValueAtTime(.12, ctx.currentTime);
          g.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + .5);
          o.connect(g);
          g.connect(ctx.destination);
          o.start();
          o.stop(ctx.currentTime + .5);
        } catch (e) {}
      }

      function finish() {
        clearInterval(interval);
        remaining = 0;
        updateDisplay(0);
        setProgress(0);
        statusLabel.textContent = 'Yemek Hazır!';
        potWrapper.classList.remove('active');
        playAlarm();
        modal.classList.add('show');
      }

      function start() {
        if (running && !paused) return;
        if (!paused) {
          const m = parseInt(inMin.value) || 0,
            s = parseInt(inSec.value) || 0;
          total = (m * 60) + s;
          if (total <= 0) {
            toast('Süre girin!', 'error');
            return;
          }
          remaining = total;
          updateDisplay(remaining);
          setProgress(100);
        }
        running = true;
        paused = false;
        inputGroup.classList.remove('active');
        potWrapper.classList.add('active');
        btnStart.style.display = 'none';
        iconControls.style.display = 'flex';
        statusLabel.textContent = 'Pişiyor...';
        clearInterval(interval);
        interval = setInterval(() => {
          remaining--;
          setProgress(remaining / total * 100);
          updateDisplay(remaining);
          if (remaining <= 0) finish();
        }, 1000);
        btnPause.textContent = '⏸';
      }

      function pause() {
        if (!running) return;
        if (paused) {
          paused = false;
          statusLabel.textContent = 'Pişiyor...';
          btnPause.textContent = '⏸';
          potWrapper.style.opacity = '';
          clearInterval(interval);
          interval = setInterval(() => {
            remaining--;
            setProgress(remaining / total * 100);
            updateDisplay(remaining);
            if (remaining <= 0) finish();
          }, 1000);
        } else {
          paused = true;
          clearInterval(interval);
          statusLabel.textContent = 'Duraklatıldı';
          btnPause.textContent = '▶';
          potWrapper.style.opacity = '.35';
        }
      }

      function reset() {
        clearInterval(interval);
        running = false;
        paused = false;
        total = 0;
        remaining = 0;
        updateDisplay(0);
        setProgress(0);
        inputGroup.classList.add('active');
        potWrapper.classList.remove('active');
        potWrapper.style.opacity = '';
        btnStart.style.display = 'flex';
        iconControls.style.display = 'none';
        statusLabel.textContent = 'Süreyi ayarla ve başlat';
        inMin.value = '05';
        inSec.value = '00';
      }

      function closeModal() {
        modal.classList.remove('show');
        reset();
      }

      function setPreset(m) {
        if (running) return;
        inMin.value = String(m).padStart(2, '0');
        inSec.value = '00';
        statusLabel.textContent = m + ' Dakika ayarlandı';
      }
      btnStart.addEventListener('click', start);
      btnPause.addEventListener('click', pause);
      btnReset.addEventListener('click', reset);
      modalOk.addEventListener('click', closeModal);
      document.querySelectorAll('#page-timer .chip[data-preset]').forEach(b => b.addEventListener('click', () => setPreset(parseInt(b.dataset.preset))));
      [inMin, inSec].forEach(inp => inp.addEventListener('keypress', e => {
        if (e.key === 'Enter') start();
      }));
      return {
        reset
      };
    })();

    // ════════════════════════════════════════════ ATIK MERKEZİ HARİTASI
    // wasteCenters: Konya'daki sıfır atık / geri dönüşüm nokta koordinatları.
    // initWasteMap() : Leaflet.js ile OpenStreetMap haritası oluşturur (tek seferlik).
    //   wasteMap != null kontrolü: sayfa geçişlerinde harita tekrar oluşturulmaz.
    //   Her merkez L.marker olarak haritaya eklenir, popup ile ismi gösterilir.
    // findNearest() : Haversine formülüyle kullanıcı konumuna en yakın merkezi hesaplar
    //   ve haritaya özel bir marker ekler; mesafe km olarak gösterilir.
    // GPS butonu → navigator.geolocation.getCurrentPosition ile konum alınır.
    const wasteCenters = [{
        name: 'Sıfır Atık - Selçuklu',
        lat: 37.894,
        lng: 32.495
      }, {
        name: 'Sıfır Atık - Meram',
        lat: 37.712,
        lng: 32.46
      },
      {
        name: 'Sıfır Atık - Karatay',
        lat: 37.765,
        lng: 32.39
      }, {
        name: 'Bosna Hersek Mah.',
        lat: 37.902,
        lng: 32.51
      },
      {
        name: 'Kampüs',
        lat: 37.935,
        lng: 32.505
      }, {
        name: 'İstiklal Mah.',
        lat: 37.735,
        lng: 32.485
      }
    ];
    let wasteMap = null;

    /**
     * initWasteMap — Leaflet.js atık merkezi haritasını başlatır.
     *
     * Harita zaten başlatıldıysa (wasteMap != null) tekrar oluşturmaz.
     * Konya merkezi koordinatlarını varsayılan görünüm olarak kullanır.
     * OpenStreetMap tile katmanı eklenir; wasteCenters listesindeki her
     * merkez için marker ve popup yerleştirilir.
     */
    function initWasteMap() {
      if (wasteMap) return;
      wasteMap = L.map('wasteMap').setView([37.871, 32.484], 12);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
      }).addTo(wasteMap);
      wasteCenters.forEach(c => L.circleMarker([c.lat, c.lng], {
        radius: 7,
        color: '#22c55e'
      }).addTo(wasteMap).bindPopup('♻️ ' + c.name));
      wasteMap.on('click', e => findNearest(e.latlng.lat, e.latlng.lng));
      setTimeout(() => wasteMap.invalidateSize(), 250);
    }

    function findNearest(lat, lng) {
      if (!wasteMap) return;
      L.marker([lat, lng]).addTo(wasteMap).bindPopup('📍 Konumunuz').openPopup();
      let min = Infinity,
        nearest = null;
      wasteCenters.forEach(c => {
        const d = wasteMap.distance([lat, lng], [c.lat, c.lng]);
        if (d < min) {
          min = d;
          nearest = c;
        }
      });
      if (nearest) {
        document.getElementById('shiningAddressContainer').innerHTML = `📍 En Yakın: <b>${esc(nearest.name)}</b> (~${(min/1000).toFixed(1)} km)`;
        L.marker([nearest.lat, nearest.lng]).addTo(wasteMap).bindPopup('♻️ ' + nearest.name).openPopup();
      }
    }
    document.getElementById('btnUseGPS').addEventListener('click', () => {
      initWasteMap();
      if (!navigator.geolocation) {
        toast('Konum desteklenmiyor', 'error');
        return;
      }
      toast('Konum alınıyor...', 'info');
      navigator.geolocation.getCurrentPosition(p => findNearest(p.coords.latitude, p.coords.longitude), () => toast('Konum alınamadı', 'error'), {
        timeout: 8000
      });
    });
    document.getElementById('btnGuideClick').addEventListener('click', () => {
      initWasteMap();
      toast('Haritaya tıklayın', 'info');
      setTimeout(() => {
        if (wasteMap) wasteMap.invalidateSize();
      }, 200);
    });

    // ════════════════════════════════════════════ ŞİFRE DEĞİŞTİR
    // Mevcut şifre + yeni şifre + tekrar alanları sunucu tarafında doğrulanır.
    // api.php → password_change: mevcut şifre hash'le eşleşmiyorsa hata döner.
    // Başarıda form sıfırlanır, başarısızda hata toast'ı gösterilir.
    document.getElementById('passwordChangeForm').addEventListener('submit', async e => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const r = await api('password_change', Object.fromEntries(fd));
      if (r.success) {
        e.target.reset();
        toast('Şifre değiştirildi!', 'success');
      } else toast(r.message, 'error');
    });

    // ── Avatar önizleme & yükleme ─────────────────────────────
    // Dosya seçilince FileReader ile base64 preview gösterilir (anlık önizleme).
    // Yükle butonuna basılınca api.php → avatar_upload ile sunucuya gönderilir.
    // Sınır: maks 2MB; desteklenen formatlar: JPEG, PNG, WEBP, GIF.
    // Başarı sonrası sidebar'daki küçük avatar (#sidebarAvatarImg) de güncellenir.
    (function() {
      const input = document.getElementById('avatarInput');
      const preview = document.getElementById('avatarPreview');
      const uploadBtn = document.getElementById('btnUploadAvatar');
      if (!input) return;

      // Sayfa açılışında mevcut avatarı yükle
      api('avatar_get', {}, 'GET').then(r => {
        if (r.success && r.data?.avatar_url) {
          preview.src = r.data.avatar_url;
          // Sidebar'daki avatar'ı da güncelle
          const sb = document.getElementById('sidebarAvatarImg');
          if (sb) sb.src = r.data.avatar_url;
        }
      });

      input.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
          toast('Dosya 2MB\'ı geçemez.', 'error'); return;
        }
        const reader = new FileReader();
        reader.onload = e => {
          preview.src = e.target.result;
          uploadBtn.style.display = 'inline-flex';
        };
        reader.readAsDataURL(file);
      });
    })();

    /**
     * uploadAvatar — Seçilen profil fotoğrafını sunucuya yükler.
     *
     * avatarInput'tan dosyayı okur; FormData ile api.php → avatar_upload
     * endpoint'ine POST eder. Başarı durumunda:
     *  - Yükleme butonu gizlenir.
     *  - Sidebar'daki küçük avatar görseli güncellenir.
     */
    async function uploadAvatar() {
      const input = document.getElementById('avatarInput');
      const file = input?.files[0];
      if (!file) { toast('Önce resim seçin.', 'error'); return; }
      const fd = new FormData();
      fd.append('avatar', file);
      const res = await fetch('api/avatar', { method: 'POST', body: fd });
      const r = await res.json();
      if (r.success) {
        toast('✅ Profil resmi güncellendi!', 'success');
        document.getElementById('btnUploadAvatar').style.display = 'none';
        // Sidebar avatarı da güncelle
        const sb = document.getElementById('sidebarAvatarImg');
        if (sb) sb.src = r.data.avatar_url;
      } else {
        toast(r.message || 'Yükleme başarısız.', 'error');
      }
    }

    // ════════════════════════════════════════════ SAYFA GEÇİŞ YÖNETİCİSİ
    // onPageLoad() her sayfa geçişinde setActivePage() tarafından çağrılır.
    // Her sayfanın ihtiyaç duyduğu veriyi sadece o sayfa açıldığında yükler
    // (tembel yükleme / lazy loading). Bu sayede sayfa ilk açılışında tüm
    // API istekleri tek seferde gitmez; gereksiz yük oluşmaz.
    // Buzdolabı iframe'i 'data-loaded' bayrağıyla yalnızca ilk açılışta yüklenir.
    /**
     * onPageLoad — Sayfa geçişinde o sayfaya özel veri yüklemeyi tetikler.
     *
     * @param {string} pageId - Aktif hale gelen sayfanın HTML id'si.
     *
     * Her sayfanın ilk gösterilmesinde ilgili load/init fonksiyonunu çağırır.
     * Buzdolabı iframe'i 'data-loaded' bayrağıyla yalnızca bir kez yüklenir.
     */
    function onPageLoad(pageId) {
      if (pageId === 'page-digital-cabinet') loadCabinet();
      else if (pageId === 'page-recipe-book') loadMyRecipes();
      else if (pageId === 'page-shopping-list') loadShop();
      else if (pageId === 'page-daily-menu') {
        loadMenu();
        loadAiRecipes();
      } else if (pageId === 'page-waste') {
        loadWaste();
        initWasteMap();
      } else if (pageId === 'page-insights') {
        loadInsights();
        loadTodayCalories();
      } else if (pageId === 'page-badges') loadBadges();
      else if (pageId === 'page-my-likes') loadMyLikes();
      else if (pageId === 'page-my-comments') loadMyComments();
      else if (pageId === 'page-digital-cabinet') {
        // Reload iframe to ensure it has fresh session
        const iframe = document.getElementById('fridgeIframe');
        if (iframe && !iframe.dataset.loaded) {
          iframe.src = 'fridge';
          iframe.dataset.loaded = '1';
        }
      }
    }

    // ════════════════════════════════════════════ BİLDİRİM SİSTEMİ
    // notifData : api.php → notifications_list'ten gelen bildirim nesneleri dizisi.
    // Zil simgesindeki kırmızı #notifBadge: okunmamış sayısı > 0 ise görünür.
    // Bildirimler her 60 saniyede bir otomatik güncellenir (setInterval).
    // Tip renk kodu: info=mavi · success=yeşil · warning=sarı · danger=kırmızı
    // SKT'si 3 gün içinde dolacak malzemeler api.php → notifications_sync_fridge
    // ile otomatik bildirime dönüştürülür; kullanıcı bunları burada görür.
    let notifData = [];

    /**
     * loadNotifications — Bildirimleri sunucudan çeker ve arayüzü günceller.
     *
     * api.php → notifications_list ile okunmamış bildirim sayısını ve
     * bildirim listesini alır. Zil simgesi üzerindeki kırmızı sayaç (notifBadge)
     * okunmamış sayısına göre gösterilir/gizlenir.
     * renderNotifications() ile liste DOM'a yazılır.
     */
    let _prevUnread = 0;
    async function loadNotifications() {
      const r = await api('notifications_list', {}, 'GET');
      if (!r.success) return;
      notifData = r.data.notifications || [];
      const unread = r.data.unread_count || 0;
      const badge = document.getElementById('notifBadge');
      badge.textContent = unread;
      badge.classList.toggle('show', unread > 0);
      // Yeni bildirim geldiyse ses çal
      if (unread > _prevUnread) {
        try {
          const ctx = new (window.AudioContext || window.webkitAudioContext)();
          const o = ctx.createOscillator();
          const g = ctx.createGain();
          o.connect(g); g.connect(ctx.destination);
          o.type = 'sine';
          o.frequency.setValueAtTime(880, ctx.currentTime);
          o.frequency.setValueAtTime(660, ctx.currentTime + 0.1);
          g.gain.setValueAtTime(0.4, ctx.currentTime);
          g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
          o.start(ctx.currentTime);
          o.stop(ctx.currentTime + 0.4);
        } catch(e) {}
      }
      _prevUnread = unread;
      renderNotifications();
    }

    // fridge iframe'den bildirim yenileme mesajını dinle
    window.addEventListener('message', async function(e) {
      if (e.data && e.data.type === 'notify_refresh') {
        try { await api('notifications_sync_fridge', {}, 'GET'); } catch {}
        await loadNotifications();
      }
    });

    /**
     * renderNotifications — Bildirim panelindeki listeyi DOM'a çizer.
     *
     * Her bildirim için tip ikonunu (ℹ️/✅/⚠️/⛔) başlık ve mesajla birlikte gösterir.
     * Okunmamış bildirimler 'unread' CSS sınıfıyla vurgulanır.
     * Silme butonu deleteNotif() fonksiyonunu tetikler.
     */
    function renderNotifications() {
      const list = document.getElementById('notifList');
      if (!notifData.length) {
        list.innerHTML = '<div class="notif-empty">🔕 Bildirim yok.</div>';
        return;
      }

      // Tip → ikon eşlemesi
      const typeIcon = { info: 'ℹ️', success: '✅', warning: '⚠️', danger: '⛔' };

      list.innerHTML = notifData.map(n => `
    <div class="notif-item ${n.is_read == 0 ? 'unread' : ''}">
      <div class="ni-dot ${n.type || 'info'}"></div>
      <div class="ni-body">
        <div class="ni-title">${typeIcon[n.type] || ''} ${esc(n.title)}</div>
        <div class="ni-msg">${esc(n.message || '')}</div>
        <div class="ni-time">${n.created_at}</div>
      </div>
      <button class="ni-del" onclick="deleteNotif(${n.id})" title="Sil">✖</button>
    </div>
  `).join('');
    }

    async function deleteNotif(id) {
      await api('notifications_delete', { id });
      await loadNotifications();
    }

    document.getElementById('notifBtn').addEventListener('click', async (e) => {
      e.stopPropagation();
      const panel = document.getElementById('notifPanel');
      const isOpen = panel.classList.contains('open');
      panel.classList.toggle('open');

      if (!isOpen) {
        // Zil açılınca önce SKT kontrolü yap, sonra listele
        try { await api('notifications_sync_fridge', {}, 'GET'); } catch {}
        await loadNotifications();

        // 1.5 saniye sonra okundu işaretle
        if (notifData.some(n => n.is_read == 0)) {
          setTimeout(async () => {
            await api('notifications_read_all');
            await loadNotifications();
          }, 1500);
        }
      }
    });

    document.addEventListener('click', e => {
      const panel = document.getElementById('notifPanel');
      if (!panel.contains(e.target) && e.target.id !== 'notifBtn') {
        panel.classList.remove('open');
      }
    });

    document.getElementById('btnReadAll').addEventListener('click', async () => {
      await api('notifications_read_all');
      await loadNotifications();
      toast('Tüm bildirimler okundu işaretlendi', 'success');
    });

    // ════════════════════════════════════════════ TARİF ETKİLEŞİMLERİ
    // currentCommentRecipe    : Yorum modalında açık olan tarifin bilgileri.
    // recipeInteractionCache  : recipe_key → {my_like, my_save} önbelleği.
    //   Önbellek sayesinde aynı tarif için API tekrar çağrılmaz; buton durumu
    //   hemen doğru şekilde gösterilir.
    // toggleRecipeLike()  → api.php → recipe_like (toggle); beğeni ekler/kaldırır.
    // toggleRecipeSave()  → api.php → recipe_save_interaction; tarif defterine kaydeder.
    // openCommentModal()  → Yorum listesini yükler, kullanıcı yorum gönderebilir.
    let currentCommentRecipe = null;
    let recipeInteractionCache = {}; // recipe_key -> {my_like, my_save}

    /**
     * toggleRecipeLike — Tarif beğeni durumunu açar/kapatır.
     *
     * @param {string} recipeKey   - Tarifin benzersiz anahtarı.
     * @param {string} recipeTitle - Tarif başlığı (DB kaydı için).
     * @param {string} recipeImage - Tarif görseli URL'si.
     * @param {string} recipeUrl   - Tarifin kaynak URL'si.
     *
     * api.php → recipe_like endpoint'i toggle mantığıyla çalışır:
     * daha önce beğenilmişse beğeniyi kaldırır, değilse ekler.
     * Sonuç recipeInteractionCache'e yazılır ve buton görsel olarak güncellenir.
     */
    async function toggleRecipeLike(recipeKey, recipeTitle, recipeImage, recipeUrl) {
      const r = await api('recipe_like', {
        recipe_key: recipeKey,
        recipe_title: recipeTitle,
        recipe_image: recipeImage || '',
        recipe_url: recipeUrl || ''
      });
      if (r.success) {
        if (!recipeInteractionCache[recipeKey]) recipeInteractionCache[recipeKey] = {
          my_like: false,
          my_save: false
        };
        recipeInteractionCache[recipeKey].my_like = r.data.liked;
        // Update button visually
        const btn = document.querySelector(`[data-like-key="${CSS.escape(recipeKey)}"]`);
        if (btn) {
          btn.classList.toggle('liked', r.data.liked);
          btn.textContent = r.data.liked ? '❤️ Beğenildi' : '🤍 Beğen';
        }
        toast(r.data.liked ? '❤️ Tarif beğenildi!' : '💔 Beğeni kaldırıldı', 'success');
      } else toast(r.message, 'error');
    }

    /**
     * toggleRecipeSave — Tarifi kişisel tarif defterine ekler veya çıkarır.
     *
     * @param {string} recipeKey    - Tarifin benzersiz anahtarı.
     * @param {string} recipeTitle  - Tarif başlığı.
     * @param {string} recipeImage  - Tarif görseli URL'si.
     * @param {string} recipeUrl    - Kaynak URL.
     * @param {string} ingredients  - Satır satır malzeme listesi.
     * @param {string} instructions - Satır satır yapılış adımları.
     *
     * api.php → recipe_save_interaction ile toggle çalışır.
     * Sonuç recipeInteractionCache'e yazılır ve kaydet butonu güncellenir.
     */
    async function toggleRecipeSave(recipeKey, recipeTitle, recipeImage, recipeUrl, ingredients, instructions) {
      const r = await api('recipe_save_interaction', {
        recipe_key: recipeKey,
        recipe_title: recipeTitle,
        recipe_image: recipeImage || '',
        recipe_url: recipeUrl || '',
        ingredients: ingredients || '',
        instructions: instructions || ''
      });
      if (r.success) {
        if (!recipeInteractionCache[recipeKey]) recipeInteractionCache[recipeKey] = {
          my_like: false,
          my_save: false
        };
        recipeInteractionCache[recipeKey].my_save = r.data.saved;
        const btn = document.querySelector(`[data-save-key="${CSS.escape(recipeKey)}"]`);
        if (btn) {
          btn.classList.toggle('saved', r.data.saved);
          btn.textContent = r.data.saved ? '📖 Kaydedildi' : '🔖 Kaydet';
        }
        toast(r.data.saved ? '📖 Tarif defterine kaydedildi!' : '🗑️ Kaydedilen tariften kaldırıldı', 'success');
      } else toast(r.message, 'error');
    }

    async function openCommentModal(recipeKey, recipeTitle, recipeImage, recipeUrl) {
      currentCommentRecipe = {
        key: recipeKey,
        title: recipeTitle,
        image: recipeImage,
        url: recipeUrl
      };
      document.getElementById('commentModalTitle').textContent = '💬 ' + recipeTitle;
      document.getElementById('commentInput').value = '';
      document.getElementById('commentModal').classList.add('open');
      // Yorumları yükle
      const r = await api('recipe_get_interactions?recipe_key=' + encodeURIComponent(recipeKey), {}, 'GET');
      if (r.success) {
        const comments = r.data.comments || [];
        document.getElementById('commentList').innerHTML = comments.length ?
          comments.map(c => `<div class="comment-card"><div class="cc-user">👤 ${esc(c.user_name||'Kullanıcı')}</div><div class="cc-text">${esc(c.comment_text)}</div><div class="cc-date">${c.created_at}</div></div>`).join('') :
          '<p style="color:var(--muted);text-align:center;">Henüz yorum yok. İlk yorumu sen yap!</p>';
      }
    }

    document.getElementById('btnSubmitComment').addEventListener('click', async () => {
      if (!currentCommentRecipe) {
        toast('Tarif seçili değil', 'error');
        return;
      }
      const text = document.getElementById('commentInput').value.trim();
      if (!text) {
        toast('Yorum boş olamaz!', 'error');
        return;
      }
      const r = await api('recipe_comment', {
        recipe_key: currentCommentRecipe.key,
        recipe_title: currentCommentRecipe.title,
        recipe_image: currentCommentRecipe.image || '',
        recipe_url: currentCommentRecipe.url || '',
        comment_text: text
      });
      if (r.success) {
        document.getElementById('commentInput').value = '';
        toast('💬 Yorum gönderildi!', 'success');
        await openCommentModal(currentCommentRecipe.key, currentCommentRecipe.title, currentCommentRecipe.image, currentCommentRecipe.url);
      } else toast(r.message, 'error');
    });

    document.getElementById('commentModal').addEventListener('click', e => {
      if (e.target === document.getElementById('commentModal')) document.getElementById('commentModal').classList.remove('open');
    });

    // ════════════════════════════════════════════ İLK YÜKLEME (async IIFE)
    // Sayfa DOM'a yüklendikten hemen sonra çalışır.
    // Yükleme sırası (performans için optimize edilmiştir):
    //   1. loadTasks()        → Günlük görevler (DB veya localStorage fallback)
    //   2. renderBodyAnalysis → Vücut analizi (PHP'den gelen profileJson kullanılır)
    //   3. renderShots/RecipeGrid/Tips → Statik içerikler (API çağrısı olmadan hızlı render)
    //   4. loadBadges()       → Kazanılmış rozetler yüklenir, sayaç güncellenir
    //   5. renderWater()      → Su takibi PHP'den gelen SERVER.waterToday ile başlar
    //   6. loadNotifications() → Bildirimler ve zil rozeti
    //   7. loadTodayCalories() → Kalori sayaçları tüm panellerde güncellenir
    //   8. loadFoodCalories()  → Gıda veritabanı arka planda yüklenir (dropdown için)
    //   9. loadMenu()          → Bugünkü öğünler
    //  10. loadAiRecipes()     → 900ms gecikmeyle AI önerileri (diğer yüklemeler bitmeden)
    //  11. setInterval(loadNotifications, 60000) → Bildirimler 60s'de bir yenilenir
    //  12. Promise.all → Genel Bakış istatistik kartları API'den güncellenir
    //  13. badge_daily_status → Günlük rozet tarihlerini senkronize eder
    (async function init() {
      await loadTasks();
      renderBodyAnalysis();
      renderShots();
      renderRecipeGrid();
      renderTips();
      await loadBadges();
      renderWater();
      await loadNotifications();
      await loadTodayCalories(); // kalori sayaçlarını başlat
      loadFoodCalories(); // gıda veritabanını arka planda yükle
      loadMenu(); // bugünkü menüyü yükle
      setTimeout(() => loadAiRecipes(), 900); // AI önerileri — biraz geciktir
      setInterval(loadNotifications, 60000);

      // ── Genel Bakış istatistiklerini otomatik güncelle ──────────
      try {
        const [w, c, r, b, s] = await Promise.all([
          api('water_today', {}, 'GET'),
          api('fridge_list', {}, 'GET'),
          api('recipe_list', {}, 'GET'),
          api('badge_list', {}, 'GET'),
          api('shop_list', {}, 'GET'),
        ]);
        const statWaterEl = document.getElementById('statWater');
        if (statWaterEl && w.success) statWaterEl.textContent = ((w.data.total_ml||0)/1000).toFixed(1) + ' L';
        const statCabEl = document.getElementById('statCabinet');
        if (statCabEl && c.success) statCabEl.textContent = c.data.length;
        const statRecEl = document.getElementById('statRecipes');
        if (statRecEl && r.success) statRecEl.textContent = r.data.length;
        const statBadEl = document.getElementById('statBadges');
        if (statBadEl && b.success) statBadEl.textContent = b.data.length;
        const statShopEl = document.getElementById('statShop');
        if (statShopEl && s.success) statShopEl.textContent = s.data.filter(i => !i.is_done).length;
      } catch(e) { /* sessiz hata */ }

      // ── Günlük rozet sıfırlama kontrolü ─────────────────────────
      try {
        const bdRes = await api('badge_daily_status', {}, 'GET');
        if (bdRes.success) {
          dailyBadgeDates = bdRes.data || {};
          renderBadgesPage();
        }
      } catch(e) {}
    })();

    // ─── 1. Gıda dropdown tema CSS (foodSelect <select> kullanılıyor) ─────────────

    // ─── 2. GIDA DROPDOWN — dropdown tabanlı sistem aktif, eski searchFood/selectFoodEx kaldırıldı ──

    // ─── 3. SU + VÜCUT SAYFASINA KALORİ PANELİ EKLE ─────────────────────────────
    // Kalori takibi kaldırıldı Su+Vücut sayfasından — sadece Günün Menüsü sayfasında aktif
    // (trackerCalPanel, trackerCalBadge vb. elementler devre dışı bırakıldı)

    // ─── 4. GÜNÜN MENÜSÜ — BUGÜN YEDİKLERİM + TOPLAM KALORİ KUTUSU ─────────────
    // addTodayEatenBox(): Kalori özet kutusu (bar + haftalık ortalama + hedef input'u)
    // Günün Menüsü sayfasının alt panelinden sonra DOM'a programatik eklenir.
    // #calBar → genişliği (consumed / goal * 100)% olarak ayarlanır; rengi yüke göre değişir.
    // #calGoalInput → Kullanıcı kendi günlük kalori hedefini girebilir (500–5000 kcal).
    // refreshTodayMealsList() → Bugünkü öğünler listesini bu kutu içinde günceller.
    (function addTodayEatenBox() {
      function insertBox() {
        const menuPage = document.getElementById('page-daily-menu');
        if (!menuPage || document.getElementById('todayEatenBox')) return;

        const box = document.createElement('div');
        box.className = 'panel';
        box.id = 'todayEatenBox';
        box.style.marginTop = '16px';
        box.innerHTML = `
      <div class="panelHd" style="background:linear-gradient(135deg,rgba(249,115,22,.08),transparent);">
        <b>🍽️ Bugün Yediklerim</b>
        <span id="calTodayBadge" style="background:#f97316;color:#fff;padding:3px 12px;border-radius:20px;font-size:.85rem;font-weight:700;">0 kcal</span>
      </div>
      <div class="panelBd">
        <!-- Kalori çubuğu -->
        <div style="margin-bottom:10px;">
          <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:4px;">
            <span id="calConsumed" style="color:var(--muted);">0 kcal alındı</span>
            <span id="calGoal" style="color:var(--muted);">Hedef: 2000 kcal</span>
          </div>
          <div style="background:var(--line,#eee);border-radius:20px;height:10px;overflow:hidden;">
            <div id="calBar" style="height:100%;border-radius:20px;transition:width .4s;width:0%;background:linear-gradient(90deg,#27ae60,#cfae55);"></div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px;">
            <div style="text-align:center;background:var(--bg,#f8f8f8);border-radius:10px;padding:10px;">
              <div style="font-size:1.3rem;font-weight:800;color:#f97316;" id="calStatToday">0</div>
              <div style="font-size:.7rem;color:var(--muted);">Bugün (kcal)</div>
            </div>
            <div style="text-align:center;background:var(--bg,#f8f8f8);border-radius:10px;padding:10px;">
              <div style="font-size:1.3rem;font-weight:800;color:#22c55e;" id="calStatWeekAvg">—</div>
              <div style="font-size:.7rem;color:var(--muted);">Haftalık Ort.</div>
            </div>
          </div>
          <!-- input hedef kalori -->
          <div style="display:flex;align-items:center;gap:8px;margin-top:10px;font-size:.83rem;">
            <label for="calGoalInput" style="color:var(--muted);white-space:nowrap;">🎯 Kalori Hedefi:</label>
            <input type="number" id="calGoalInput" value="2000" min="500" max="5000"
              style="width:100px;padding:5px 10px;border-radius:8px;border:1px solid var(--line,#ddd);
                     background:var(--surface,#fff);color:var(--ink,#222);font-size:.85rem;"
              onchange="if(typeof loadTodayCalories==='function') loadTodayCalories()">
            <span style="color:var(--muted);font-size:.75rem;">kcal/gün</span>
          </div>
        </div>
        <!-- Öğün listesi -->
        <div id="todayMealsList" style="display:flex;flex-direction:column;gap:6px;margin-top:4px;">
          <p style="color:var(--muted);text-align:center;padding:12px;">Yükleniyor...</p>
        </div>
      </div>
    `;

        // Mevcut menü panelinden SONRA ekle
        const menuDayPanel = menuPage.querySelector('.panel:last-of-type');
        if (menuDayPanel) {
          menuDayPanel.insertAdjacentElement('afterend', box);
        } else {
          menuPage.appendChild(box);
        }
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', insertBox);
      } else {
        setTimeout(insertBox, 200);
      }
    })();

    // Bugün yediklerim listesini güncelle
    async function refreshTodayMealsList() {
      const listEl = document.getElementById('todayMealsList');
      if (!listEl) return;

      const mDate = new Date().toISOString().slice(0, 10);
      try {
        const r = await api('menu_list?date=' + mDate, {}, 'GET');
        if (!r.success || !r.data.length) {
          listEl.innerHTML = '<p style="color:var(--muted);text-align:center;padding:12px;"></p>';
          return;
        }
        const icons = {
          kahvaltı: '☀️',
          öğle: '🌤️',
          akşam: '🌙',
          atıştırmalık: '🍎'
        };
        listEl.innerHTML = r.data.map(m => `
      <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;
                  background:var(--surface,#fff);border-radius:10px;
                  border:1px solid var(--line,rgba(0,0,0,.07));">
        <span style="font-size:1.2rem;">${icons[m.meal_type] || '🍽️'}</span>
        <div style="flex:1;">
          <div style="font-weight:700;font-size:.88rem;">${esc(m.description || '')}</div>
          <div style="font-size:.73rem;color:var(--muted);text-transform:capitalize;">${esc(m.meal_type)}</div>
        </div>
        ${m.calories ? `<span style="background:rgba(249,115,22,.12);color:#f97316;
                        padding:3px 10px;border-radius:20px;font-size:.78rem;font-weight:700;">
                        🔥 ${m.calories} kcal</span>` : ''}
      </div>
    `).join('');
      } catch (e) {
        listEl.innerHTML = '<p style="color:var(--muted);text-align:center;">Yüklenemedi.</p>';
      }
    }

    // loadMenu override — orijinal + bugün yediklerim refresh
    const _origLoadMenu = window.loadMenu;
    window.loadMenu = async function() {
      if (typeof _origLoadMenu === 'function') await _origLoadMenu();
      await refreshTodayMealsList();
    };

    // ─── 5. BUZDOLABI MALZEMELERİ → GÜNÜN MENÜSÜNE EKLE ─────────────────────────
    // addFridgePanelToMenu(): Günün Menüsü sayfasına buzdolabı malzeme listesi paneli ekler.
    // Malzemeler SKT'ye göre sıralanır (yaklaşan önce) ve renk kodlanır.
    // "Tüket" butonu → fridge_delete çağırır + AI tarif önerilerini günceller.
    (function addFridgePanelToMenu() {
      function insertFridgePanel() {
        const menuPage = document.getElementById('page-daily-menu');
        if (!menuPage || document.getElementById('fridgeToMenuPanel')) return;

        const panel = document.createElement('div');
        panel.className = 'panel';
        panel.id = 'fridgeToMenuPanel';
        panel.style.cssText = 'margin-bottom:16px;border:1.5px solid rgba(59,130,246,.3);';
        panel.innerHTML = `
      <div class="panelHd" style="background:linear-gradient(135deg,rgba(59,130,246,.08),transparent);">
        <b>❄️ Buzdolabındaki Malzemeler</b>
        <button class="btn-sm btn-gold" id="btnLoadFridgeItems" style="padding:5px 12px;font-size:.8rem;">🔄 Listele</button>
      </div>
      <div class="panelBd" id="fridgeItemsForMenu">
        <p style="color:var(--muted);text-align:center;padding:12px 0;">Butona tıklayın, buzdolabınızdaki malzemeleri görün.</p>
      </div>
    `;

        // AI tarif önerisi panelinden sonra ekle
        const suggestPanel = document.querySelector('#page-daily-menu .panel');
        if (suggestPanel) {
          suggestPanel.insertAdjacentElement('afterend', panel);
        } else {
          menuPage.prepend(panel);
        }

        document.getElementById('btnLoadFridgeItems').addEventListener('click', loadFridgeItemsForMenu);
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', insertFridgePanel);
      } else {
        setTimeout(insertFridgePanel, 300);
      }
    })();

    async function loadFridgeItemsForMenu() {
      const container = document.getElementById('fridgeItemsForMenu');
      if (!container) return;
      container.innerHTML = '<p style="color:var(--muted);text-align:center;padding:10px;">Yükleniyor...</p>';

      // Hem fridge hem cabinet listele
      const fr = await api('fridge_list', {}, 'GET');
      const allItems = (fr.success ? fr.data : []).map(i => ({ ...i, src: 'fridge' }));

      if (!allItems.length) {
        container.innerHTML = '<p style="color:var(--muted);text-align:center;padding:12px;">Buzdolabı ve dolap boş. Malzeme ekleyin!</p>';
        return;
      }

      // SKT uyarısı: 3 gün veya daha az kalanlar önce
      allItems.sort((a, b) => {
        const da = a.expiry_date ? new Date(a.expiry_date) : new Date('2099-01-01');
        const db_ = b.expiry_date ? new Date(b.expiry_date) : new Date('2099-01-01');
        return da - db_;
      });

      const today = new Date();
      today.setHours(0, 0, 0, 0);

      container.innerHTML = `
  <p style="font-size:.8rem;color:var(--muted);margin-bottom:10px;">
    ${allItems.length} malzeme bulundu.
  </p>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;">
    ${allItems.map(item => {
      let sktBadge = '';
      let cardBorder = 'var(--line,#ddd)';

      if (item.expiry_date) {
        const exp = new Date(item.expiry_date);
        const diffDays = Math.ceil((exp - today) / 86400000);

        if (diffDays < 0) {
          sktBadge = `<span style="background:#fee2e2;color:#991b1b;padding:2px 7px;border-radius:20px;font-size:.7rem;font-weight:700;">⛔ SKT Geçti</span>`;
          cardBorder = '#ef4444';
        } else if (diffDays <= 3) {
          sktBadge = `<span style="background:#fef9c3;color:#854d0e;padding:2px 7px;border-radius:20px;font-size:.7rem;font-weight:700;">⚠️ ${diffDays} gün kaldı</span>`;
          cardBorder = '#f59e0b';
        } else {
          sktBadge = `<span style="background:#dcfce7;color:#166534;padding:2px 7px;border-radius:20px;font-size:.7rem;font-weight:700;">✅ ${diffDays} gün</span>`;
        }
      }

      const srcIcon = item.src === 'fridge' ? '❄️' : '🧊';
      const safeName = esc(item.name).replace(/'/g, "&apos;");

      return `
        <div style="background:var(--surface,#fff);border:1px solid ${cardBorder};border-radius:10px;padding:10px;">
          <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
            <span>${srcIcon}</span>
            <span style="font-weight:700;font-size:.88rem;">${esc(item.name)}</span>
          </div>

          ${item.quantity ? `<div style="font-size:.75rem;color:var(--muted);">📦 ${esc(item.quantity)}</div>` : ''}

          ${sktBadge ? `<div style="margin:4px 0;">${sktBadge}</div>` : ''}

          <div style="display:flex;gap:5px;margin-top:8px;flex-wrap:wrap;">
            <button class="btn-sm btn-red" style="font-size:.78rem;padding:5px 12px;width:100%;"
              onclick="consumeFridgeItem(this, '${safeName}', ${item.id}, '${item.src}')">🍴 Tüket</button>
          </div>
        </div>
      `;
    }).join('')}
  </div>
`;
}

// ── Tüket butonu handler ────────────────────────────────────────────
window.consumeFridgeItem = async function(btn, itemName, itemId, src) {
  const card = btn?.closest ? btn.closest('div[style]') : null;
  if (card) { card.style.opacity = '0.4'; card.style.pointerEvents = 'none'; }
  const r = await api('fridge_delete', { id: itemId });
  if (r.success) {
    toast(`✅ "${itemName}" tüketildi!`, 'success');
    loadFridgeItemsForMenu();
    const fridgeFrame = document.getElementById('fridgeFrame') || document.querySelector('iframe[src*="fridge"]');
    if (fridgeFrame) {
      fridgeFrame.contentWindow.postMessage({ type: 'fridge_refresh' }, '*');
      setTimeout(() => { try { fridgeFrame.contentWindow.location.reload(); } catch(e) {} }, 300);
    }
    setTimeout(() => { if (typeof loadAiRecipes === 'function') loadAiRecipes(true); }, 500);
  } else {
    if (card) { card.style.opacity = '1'; card.style.pointerEvents = ''; }
    toast(r.message || 'Silinemedi', 'error');
  }
};

window.addFridgeItemToMenu = async function(itemName, mealType) {
  const menuDate = document.getElementById('menuDate')?.value || new Date().toISOString().slice(0,10);
  const r = await api('menu_add', {
    menu_date: menuDate,
    meal_type: mealType,
    description: itemName,
    calories: 0
  });
  if (r.success) {
    if (typeof loadMenu === 'function') await loadMenu();
    if (typeof loadTodayCalories === 'function') await loadTodayCalories();
    toast(`✅
    "${itemName}"
    $ {
      mealType
    }
    öğününe eklendi!`, 'success');
  } else {
    toast(r.message || 'Eklenemedi', 'error');
  }
};

// ─── 6. İÇGÖRÜLER — BUZDOLABI + DOLAP TOPLAM DOĞRU HESAPLAMA ────────────────
// loadInsights override: Orijinal fonksiyon çalıştıktan sonra buzdolabı verisi
// ayrıca çekilir ve insightsGrid içindeki "Dolap Malzeme" kartı güncellenir.
// Bu pattern, orijinal fonksiyonu bozmadan üstüne davranış ekler (monkey-patching).
const _origLoadInsights = window.loadInsights;
window.loadInsights = async function() {
  // Önce orijinali çalıştır (varsa)
  if (typeof _origLoadInsights === 'function') {
    await _origLoadInsights();
  }

  // Fridge listesini ayrıca çek ve insight grid'i güncelle
  try {
    const fr = await api('fridge_list', {}, 'GET');
    const totalItems = fr.success ? fr.data.length : 0;

    // insight-card içindeki dolap sayısını güncelle
const insightCards = document.querySelectorAll('#insightsGrid .insight-card');

insightCards.forEach(card => {
  const label = card.querySelector('.ic-label');

  if (
    label &&
    (label.textContent.includes('Dolap Malzeme') ||
     label.textContent.includes('Buzdolabı'))
  ) {
    const valEl = card.querySelector('.ic-val');

    if (valEl) valEl.textContent = totalItems;

    label.textContent = `Buzdolabı Malzeme`;
  }
});
    // statCabinet (genel bakış)
    const statCab = document.getElementById('statCabinet');
    if (statCab) statCab.textContent = totalItems;

  } catch(e) {
    // sessiz hata
  }
};

// ─── fridge iframe'den bildirim yenileme mesajı ──────────────────────────────
// fridge.js ürün ekleyince window.parent.postMessage({type:'notify_refresh'}) gönderir.
// Dashboard bu mesajı alınca SKT kontrolü yapıp zili günceller.
window.addEventListener('message', async function(e) {
  if (e.data && e.data.type === 'notify_refresh') {
    try { await api('notifications_sync_fridge', {}, 'GET'); } catch {}
    await loadNotifications();
  }
});
// api.php → notifications_sync_fridge: Buzdolabındaki malzemelerin SKT'leri kontrol edilir.
// SKT 3 gün veya daha az kalan malzemeler için otomatik bildirim kaydı oluşturulur.
// Bu işlem arka planda sessizce çalışır; hata olsa da kullanıcı etkilenmez.
(async function initSktCheck() {
  try {
    await api('notifications_sync_fridge', {}, 'GET');
  } catch(e) {}
})();

// ─── 8. loadTodayCalories PATCH — "Bugün Yediklerim" listesini de güncelle ───
// Orijinal loadTodayCalories yalnızca kalori sayaçlarını günceller.
// Bu override: kalori güncellemesinin ardından refreshTodayMealsList() de çalıştırır.
// Böylece öğün eklendiğinde / silindiğinde liste ve sayaç her zaman senkron kalır.
const _origLoadTodayCalories = window.loadTodayCalories;
window.loadTodayCalories = async function() {
  if (typeof _origLoadTodayCalories === 'function') {
    await _origLoadTodayCalories();
  }
  // Bugün yediklerim listesini de yenile
  await refreshTodayMealsList();
};

// ─── 9. esc() GLOBAL FALLBACK ────────────────────────────────────────────────
// Dashboard'un ana <script> bloğunda tanımlanan esc() fonksiyonu yalnızca o scope'ta
// geçerlidir. Aşağıdaki ek scriptler (IIFE'ler) bu fonksiyona erişemez.
// Bu fallback: window.esc yoksa aynı XSS koruma mantığını global olarak tanımlar.
if (typeof window.esc !== 'function') {
  window.esc = function(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  };
}

// ════════════════════════════════════════════ SAĞLIKLI TARİFLER BLOG VERİSİ
// BLOG_DATA: Statik blog içeriği (API çağrısı yok).
// Kategoriler: gida | beslenme | tarif | immun | kilo | kanser
// Her nesne: cat, emoji, title, lead, immun/kilo/kanser yıldızları, detail,
//            ve opsiyonel ingredients + steps (tarif kartları için).
// renderBlogGrid() bu diziyi filtreler ve activeBlogCat'e göre ekrana basar.
const BLOG_DATA = [
  // ── Gıda Faydaları ─────────────────────────────────
  {
    cat:'gida', emoji:'🧄', title:'Sarımsak: Doğanın Antibiyotiği',
    lead:'Sarımsak içindeki allisin bileşiği güçlü antimikrobiyal ve antiinflamatuar özellikler taşır. Düzenli tüketim bağışıklığı güçlendirir.',
    immun:'⭐⭐⭐⭐⭐', kilo:'⭐⭐', kanser:'⭐⭐⭐⭐',
    detail:`<b>Ne işe yarar?</b> Sarımsak; tansiyon düşürür, kolesterol dengesini sağlar, kan şekerini dengeler ve iltihap azaltır.<br><br>
<b>🛡️ Bağışıklık etkisi:</b> Allisin bileşiği beyaz kan hücrelerini aktive eder. Soğuk mevsimde günde 1–2 diş çiğ sarımsak tüketmek grip süresini %61 kısaltır.<br><br>
<b>⚖️ Kilo verme etkisi:</b> Yağ yakımını hızlandıran enzimleri tetikler, tokluk hissi sağlar, kalori yoktur (~5 kcal/diş).<br><br>
<b>💊 Kanser koruyuculuğu:</b> Bağırsak ve mide kanseri riskini azaltır. Allilsülfür bileşikleri tümör hücrelerini inhibe eder.`
  },
  {
    cat:'gida', emoji:'🌿', title:'Zerdeçal: Altın Baharat',
    lead:'Curcumin içeriği ile zerdeçal dünyanın en güçlü antiinflamatuar doğal maddelerinden biridir. Karabiber ile birlikte alındığında emilimi %2000 artar.',
    immun:'⭐⭐⭐⭐', kilo:'⭐⭐⭐', kanser:'⭐⭐⭐⭐⭐',
    detail:`<b>Ne işe yarar?</b> Eklem ağrısı azaltır, beyin sağlığını korur (BDNF proteini artırır), karaciğer detoksu sağlar.<br><br>
<b>🛡️ Bağışıklık etkisi:</b> Makrofaj aktivasyonunu artırır. Doğal antiseptik özelliği sayesinde bakteri ve viral enfeksiyonlara karşı koruyucudur.<br><br>
<b>⚖️ Kilo verme etkisi:</b> Yağ hücrelerinin büyümesini baskılar, insülin direncini azaltır.<br><br>
<b>💊 Kanser koruyuculuğu:</b> NF-kB sinyal yolunu bloke ederek kanser hücrelerinin büyümesini yavaşlatır. Kolon, meme ve pankreas kanseri üzerinde araştırılmaktadır.`
  },
  {
    cat:'gida', emoji:'🥑', title:'Avokado: Kalp Dostu Yağ',
    lead:'Avokado tekli doymamış yağ, K vitamini, folat ve potasyum bakımından son derece zengindir. Kalp sağlığı için mükemmel bir süperfoodtur.',
    immun:'⭐⭐⭐', kilo:'⭐⭐⭐', kanser:'⭐⭐⭐',
    detail:`<b>Ne işe yarar?</b> LDL (kötü) kolesterolü düşürür, HDL (iyi) kolesterolü yükseltir. Göz sağlığını korur (lutein, zeaksantin).<br><br>
<b>🛡️ Bağışıklık etkisi:</b> E vitamini antioksidan kalkanı sağlar. B6 vitamini bağışıklık hücresi üretimini destekler.<br><br>
<b>⚖️ Kilo verme etkisi:</b> Yüksek lif içeriği uzun süre tok tutar. Sağlıklı yağlar açlık hormonunu baskılar.<br><br>
<b>💊 Kanser koruyuculuğu:</b> Avokatin B maddesi AML (akut miyeloid lösemi) hücrelerine karşı araştırılmaktadır. Folat DNA hasarını engeller.`
  },
  {
    cat:'gida', emoji:'🍵', title:'Yeşil Çay: Metabolizma Hızlandırıcı',
    lead:'Yeşil çay EGCG (epigallokateşin gallat) içeriğiyle metabolizmayı %3–4 hızlandırır, zihinsel odaklanmayı artırır ve güçlü antioksidan etki gösterir.',
    immun:'⭐⭐⭐⭐', kilo:'⭐⭐⭐⭐', kanser:'⭐⭐⭐⭐',
    detail:`<b>Ne işe yarar?</b> Kan şekerini dengeler, kolesterol düşürür, beyin fonksiyonlarını güçlendirir, karaciğer yağlanmasını azaltır.<br><br>
<b>🛡️ Bağışıklık etkisi:</b> Kateşinler virüslerin hücre duvarına tutunmasını engeller. Günde 3 bardak tüketim önerilir.<br><br>
<b>⚖️ Kilo verme etkisi:</b> Yağ yakımını %17 artırır. Egzersizle birleştiğinde etkisi 2 katına çıkar.<br><br>
<b>💊 Kanser koruyuculuğu:</b> Meme, prostat ve kolorektal kanser riskini azalttığına dair güçlü epidemiyolojik kanıtlar mevcuttur.`
  },
  {
    cat:'gida', emoji:'🫐', title:'Yaban Mersini: Antioksidan Şampiyonu',
    lead:'Yaban mersini, ORAC skoruna göre en yüksek antioksidan kapasiteli meyvelerden biridir. Beyin yaşlanmasını yavaşlatır.',
    immun:'⭐⭐⭐⭐', kilo:'⭐⭐⭐', kanser:'⭐⭐⭐⭐',
    detail:`<b>Ne işe yarar?</b> Hafıza ve öğrenme kapasitesini artırır, oksidatif stresi azaltır, idrar yolu sağlığını destekler, gözü korur.<br><br>
<b>🛡️ Bağışıklık etkisi:</b> Antosiyaninler NK hücre aktivitesini artırır. Üst solunum yolu enfeksiyonlarına karşı koruyucudur.<br><br>
<b>⚖️ Kilo verme etkisi:</b> Düşük glisemik indeks, yüksek lif; insülin salgısını kontrol eder.<br><br>
<b>💊 Kanser koruyuculuğu:</b> Pterosilben maddesi kolon kanser hücrelerine karşı araştırılmaktadır.`
  },
  {
    cat:'gida', emoji:'🥦', title:'Brokoli: Süper Sebze',
    lead:'Brokoli sulforafan içeriğiyle hem kanser önleyici hem de detoks enzimlerini aktive eden güçlü bir krusifer sebzedir.',
    immun:'⭐⭐⭐⭐', kilo:'⭐⭐⭐⭐', kanser:'⭐⭐⭐⭐⭐',
    detail:`<b>Ne işe yarar?</b> Kemik sağlığını güçlendirir (K vitamini), sindirim sistemini düzenler, tiroid fonksiyonunu destekler.<br><br>
<b>🛡️ Bağışıklık etkisi:</b> C vitamini miktarı portakalı geçer (100g = 89mg C). Bağırsak mikrobiyomunu besler.<br><br>
<b>⚖️ Kilo verme etkisi:</b> 100g = sadece 34 kcal. Yüksek lif tokluk sağlar. Termojenik etki gösterir.<br><br>
<b>💊 Kanser koruyuculuğu:</b> Sulforafan meme, prostat, kolon kanserinde tümör büyümesini baskılar. Pişirme süresi kısa tutulmalıdır.`
  },
  {
    cat:'gida', emoji:'🫘', title:'Baklagiller: Proteinin Bitkisel Şampiyonu',
    lead:'Nohut, mercimek, fasulye — bu ucuz ve doyurucu besinler kalp, bağırsak ve kan şekeri sağlığı için ideal bir üçlü kombinasyondur.',
    immun:'⭐⭐⭐', kilo:'⭐⭐⭐⭐⭐', kanser:'⭐⭐⭐',
    detail:`<b>Ne işe yarar?</b> Demir eksikliğini giderir, sinir sistemini destekler (B vitamini), bağırsak mikrobiyomunu besler (prebiyotik lif).<br><br>
<b>🛡️ Bağışıklık etkisi:</b> Çinko ve selenyum içeriği bağışıklık hücrelerinin üretimini destekler.<br><br>
<b>⚖️ Kilo verme etkisi:</b> Yüksek protein + lif kombinasyonu uzun süreli tokluk sağlar. Kan şekerini dengeler, şeker krizlerini önler.<br><br>
<b>💊 Kanser koruyuculuğu:</b> İzoflavonlar hormona bağımlı kanser riskini azaltır. Kolon kanserinde koruyucu etki gösterir.`
  },

  // ── Beslenme Önerileri ────────────────────────────────
  {
    cat:'beslenme', emoji:'🌈', title:'Tabak Renk Kuralı',
    lead:'Sağlıklı bir tabak 5 farklı renk içermelidir. Her renk farklı fitokimyasallar barındırır: kırmızı = likopen, yeşil = klorofil, mor = antosiyanin.',
    immun:'⭐⭐⭐⭐', kilo:'⭐⭐⭐', kanser:'⭐⭐⭐',
    detail:`<b>Renk Rehberi:</b><br>
🔴 Kırmızı: Domates, kırmızı biber, çilek → Likopen (kalp, kanser)<br>
🟠 Turuncu: Havuç, balkabağı, kavun → Beta-karoten (göz, bağışıklık)<br>
🟡 Sarı: Mısır, limon, zencefil → Zeaksantin (göz sağlığı)<br>
🟢 Yeşil: Ispanak, brokoli, fesleğen → Klorofil, K vitamini<br>
🟣 Mor: Patlıcan, yaban mersini, kırmızı lahana → Antosiyanin<br>
⚪ Beyaz: Sarımsak, soğan, mantar → Alliin, beta-glukan<br><br>
<b>Öneri:</b> Her öğünde tabağının yarısını bu renkli sebzeler oluştursun.`
  },
  {
    cat:'beslenme', emoji:'⏰', title:'Aralıklı Oruç (16:8)',
    lead:'16 saatlik açlık, 8 saatlik yeme penceresi. Metabolizmanı sıfırlar, hücresel temizlik (otofaji) başlatır, insülin duyarlılığını artırır.',
    immun:'⭐⭐⭐', kilo:'⭐⭐⭐⭐⭐', kanser:'⭐⭐⭐',
    detail:`<b>Nasıl yapılır?</b> Örnek program: Akşam 20:00 son yemek → Öğle 12:00 ilk yemek. Bu süre boyunca su, siyah kahve, şekersiz çay serbesttir.<br><br>
<b>⚖️ Kilo verme etkisi:</b> Haftalık %0.5–1 yağ kaybı mümkün. Kas kütlesini korur. İnsülin düşünce yağ depolarına erişim kolaylaşır.<br><br>
<b>🛡️ Bağışıklık etkisi:</b> Otofaji sürecinde hasarlı hücreler temizlenir. Bağışıklık sistemi "yenilenir".<br><br>
<b>⚠️ Dikkat:</b> Diyabetikler, hamileler ve gençler doktor danışmanlığı olmadan uygulamamalıdır.`
  },
  {
    cat:'beslenme', emoji:'🧘', title:'Mindful Eating (Bilinçli Yeme)',
    lead:'Yavaş yemek, her lokmayı tatmak ve açlık sinyallerine kulak vermek kilo yönetiminde ve sindirimde ilaçsız mucize etki yaratır.',
    immun:'⭐⭐', kilo:'⭐⭐⭐⭐', kanser:'⭐',
    detail:`<b>Bilinçli yeme kuralları:</b><br>
1️⃣ Her lokma 20–30 kez çiğnenmeli<br>
2️⃣ Yemekte telefon/TV kapalı olmalı<br>
3️⃣ Tabağın %80'ini doldur, son 20'yi bekle<br>
4️⃣ Açlık skalası 1–10'u kullan (7'de dur)<br>
5️⃣ Küçük tabak kullan (otomatik porsiyon azalır)<br><br>
<b>⚖️ Bilimsel kanıt:</b> Bilinçli yeme, 6 ay içinde ortalama 4–5 kg kayıp sağlar. Tıkınırcasına yeme bozukluğunu %60 azaltır.`
  },

  // ── Tarifler ──────────────────────────────────────────
  {
    cat:'tarif', emoji:'🍵', title:'Zerdeçal Altın Süt',
    lead:'Bağışıklık güçlendirici, anti-inflamatuar, uyku kalitesini artıran bu içeceği akşamları içmek birçok sorunu önler.',
    immun:'⭐⭐⭐⭐⭐', kilo:'⭐⭐', kanser:'⭐⭐⭐',
    ingredients:['1 bardak süt veya bitkisel süt','1 tsp zerdeçal','½ tsp tarçın','1 tutam karabiber','1 tsp bal (opsiyonel)'],
    steps:['Sütü ısıt, kaynamasın.','Zerdeçal, tarçın ve karabiberi ekle.','2 dk karıştır.','Bal ekleyip sıcak iç.'],
    detail:`<b>Neden karabiber?</b> Piperin maddesi curcuminin emilimini %2000 artırır — vazgeçme!<br><br>
<b>Ne zaman iç?</b> Yatmadan 1 saat önce ideal. Melatonin artışı destekler, derin uyku kalitesini yükseltir.<br><br>
<b>Varyasyon:</b> Zencefil tozu ekle (½ tsp) → sindirim güçlenir.`
  },
  {
    cat:'tarif', emoji:'🥗', title:'Akdeniz Kase Salatası',
    lead:'Domates, salatalık, zeytin, roka, feta peyniri ve zeytinyağı ile hazırlanan bu salata kalp sağlığını ve uzun ömrü destekler.',
    immun:'⭐⭐⭐⭐', kilo:'⭐⭐⭐⭐', kanser:'⭐⭐⭐',
    ingredients:['2 domates','1 salatalık','½ kırmızı soğan','10 adet siyah zeytin','50g beyaz peynir','Roka','2 tbsp zeytinyağı','Kekik + kuru nane'],
    steps:['Tüm sebzeleri doğra.','Zeytinyağı ve baharatları gezdир.','Peyniri ufala, zeytinleri ekle.','Servis et.'],
    detail:`<b>Akdeniz diyeti avantajı:</b> 25 ülkede yapılan meta-analizler kalp hastalığı riskini %30 azalttığını kanıtlamıştır.<br><br>
<b>Protein içeriği:</b> Feta peyniri + zeytinyağı kombinasyonu uzun tokluk sağlar (yaklaşık 280 kcal / porsiyon).`
  },
  {
    cat:'tarif', emoji:'🍲', title:'Kırmızı Mercimek Çorbası',
    lead:'5 malzeme, 20 dakika. Protein ve demirden zengin, hem bitkisel hem de son derece besleyici bir çorba.',
    immun:'⭐⭐⭐', kilo:'⭐⭐⭐⭐', kanser:'⭐⭐',
    ingredients:['1 sb kırmızı mercimek','1 orta soğan','1 kaşık domates salçası','6 sb su','Kimyon, karabiber, tuz'],
    steps:['Soğanı zeytinyağında kavur.','Salçayı ekle, 2 dk çevir.','Mercimek ve suyu ekle.','20 dk haşla.','Blenderdan geçir, baharat ekle.'],
    detail:`<b>Besin değeri (1 porsiyon):</b> ~180 kcal · 12g protein · 8g lif · Yüksek demir ve folat<br><br>
<b>İpucu:</b> Limon suyu sıkarak C vitamini ekle — demir emilimi 3 katına çıkar.`
  },
  {
    cat:'tarif', emoji:'🥤', title:'Bağışıklık Güçlendirici Shot',
    lead:'Sabahları aç karnına içilen bu 3 malzemeli shot, grip ve soğuk algınlığına karşı güçlü bir kalkan oluşturur.',
    immun:'⭐⭐⭐⭐⭐', kilo:'⭐⭐⭐', kanser:'⭐⭐⭐',
    ingredients:['1 limon suyu','1 cm zencefil (rendelenmiş)','1 tutam zerdeçal','1 tutam karabiber','50ml ılık su'],
    steps:['Zencefili rendele.','Tüm malzemeleri bardağa koy.','Karıştır.','Sabahları aç karnına iç.'],
    detail:`<b>Bileşik etki:</b> Limon (C vitamini) + zencefil (gingerol) + zerdeçal (curcumin) bağışıklığı üçlü destekler.<br><br>
<b>Dikkat:</b> Mide hassasiyeti varsa yemekten sonra al. Karabiber curcumin emilimini artırmak için zorunludur.`
  },
  {
    cat:'tarif', emoji:'🌯', title:'Kinoa Sebzeli Kase',
    lead:'Tam protein, glutensiz ve yüksek lifli kinoa; ızgara sebzeler ile birleşince mükemmel bir öğle yemeği haline gelir.',
    immun:'⭐⭐⭐', kilo:'⭐⭐⭐⭐', kanser:'⭐⭐',
    ingredients:['1 sb kinoa','2 sb su','1 kabak','1 biber','1 havuç','Zeytinyağı','Tuz, kekik'],
    steps:['Kinoa 15 dk pişir.','Sebzeleri küp kes, fırında 200°C 20 dk pişir.','Kinoa ve sebzeleri kaseye koy.','Zeytinyağı gezdir, servis et.'],
    detail:`<b>Kinoa avantajı:</b> 9 temel amino asidin tümünü içeren bitkisel tam protein. Glutensizdirlik + yüksek lif + düşük GI üçlüsü.<br><br>
<b>Besin değeri:</b> ~320 kcal · 14g protein · 5g lif / porsiyon`
  },

  // ── Bağışıklık ────────────────────────────────────────
  {
    cat:'immun', emoji:'🦠', title:'Bağışıklık Sistemi & Beslenme',
    lead:'Günlük beslenme alışkanlıkları bağışıklık sisteminizin %70 ini belirler. Doğru besinler hem savunmayı hem de toparlanmayı güçlendirir.',
    immun:'⭐⭐⭐⭐⭐', kilo:'⭐⭐', kanser:'⭐⭐⭐',
    detail:`<b>En güçlü bağışıklık destekçileri:</b><br>
🧄 Sarımsak — Allisin (antiviral, antibakteriyal)<br>
🍋 C Vitamini — Günlük 200mg yeterli (çilek, portakal, biber)<br>
🌞 D Vitamini — NK hücre aktivasyonu. Kış aylarında takviye şart.<br>
🌰 Çinko — T hücre üretimi. Kabak çekirdeği, et, nohut.<br>
🍵 EGCG (yeşil çay) — Makrofaj aktivasyonu<br>
🥛 Probiyotikler — Bağırsak bariyeri = ilk savunma hattı<br><br>
<b>Kaçınılacaklar:</b> Şeker (5 saat bağışıklığı baskılar), işlenmiş gıda, alkol, kronik stres.`
  },
  {
    cat:'immun', emoji:'🌞', title:'D Vitamini: Sessiz Bağışıklık Koruyucusu',
    lead:'Türkiye nüfusunun %75 inde D vitamini eksikliği görülmektedir. Bu vitamin yalnızca kemik değil, bağışıklık ve ruh hali için de kritiktir.',
    immun:'⭐⭐⭐⭐⭐', kilo:'⭐⭐', kanser:'⭐⭐⭐⭐',
    detail:`<b>Kaynaklar:</b> Güneş ışığı (yüz + kollar, 15 dk/gün), somon, sardalya, yumurta sarısı, takviye.<br><br>
<b>Önerilen seviye:</b> Kan seviyesi 40–60 ng/mL olmalı. Kış aylarında 1000–2000 IU takviye önerilebilir (doktor onayıyla).<br><br>
<b>💊 Kanser koruyuculuğu:</b> Kolorektal kanser riskini %30 azaltır. Meme kanseri üzerinde kapsamlı çalışmalar sürmektedir.`
  },

  // ── Kilo ─────────────────────────────────────────────
  {
    cat:'kilo', emoji:'⚖️', title:'Metabolizma Hızlandırıcı 5 Adım',
    lead:'Diyet yapmadan metabolizmanızı hızlandırabilirsiniz. Bu 5 kanıta dayalı adım günlük kalori yakımınızı artırır.',
    immun:'⭐⭐', kilo:'⭐⭐⭐⭐⭐', kanser:'⭐',
    detail:`<b>1. Yeterli protein ye:</b> Protein sindirimi %25–30 enerji harcar (termojenik etki). Her öğünde protein olsun.<br>
<b>2. Yeşil çay & kahve iç:</b> Kafein metabolizmayı %3–11 hızlandırır. EGCG yağ yakımını artırır.<br>
<b>3. Bol su iç:</b> 500ml su içmek 1 saat boyunca metabolizmayı %30 artırır.<br>
<b>4. Yeterli uyu:</b> Uyku eksikliği → Ghrelin ↑, Leptin ↓ = daha fazla yeme.<br>
<b>5. HIIT egzersizi:</b> Kısa yoğun antrenman, saatlerce sonrasında kalori yakmaya devam eder (EPOC etkisi).`
  },
  {
    cat:'kilo', emoji:'🥗', title:'Satiyet Endeksi: Tok Tutan Besinler',
    lead:'Hangi besin daha çok tok tutar? Beyaz ekmek referans alındığında bazı besinler 7 kat daha fazla tokluk sağlar.',
    immun:'⭐', kilo:'⭐⭐⭐⭐⭐', kanser:'⭐⭐',
    detail:`<b>Yüksek Tokluk Skoru Besinler:</b><br>
🥔 Haşlanmış Patates: 323 (en yüksek!) — glisemik düşüklük önemli<br>
🐟 Balık: 225<br>
🥣 Yulaf ezmesi: 209<br>
🍊 Meyve (ortalama): 162<br>
🥩 Et: 176<br>
🥚 Yumurta: 150<br>
🍞 Beyaz ekmek: 100 (referans)<br>
🥐 Kruvasan: 47 (en düşük!)<br><br>
<b>Formül:</b> Protein + Lif + Su içeriği yüksek = doyurucudur.`
  },

  // ── Kanser ───────────────────────────────────────────
  {
    cat:'kanser', emoji:'🛡️', title:'Kanser Önleyici Beslenme Piramidi',
    lead:'Araştırmalar tüm kanser vakalarının %30–35 inin beslenme alışkanlıklarıyla ilişkili olduğunu gösteriyor. Doğru beslenme en güçlü kalkandır.',
    immun:'⭐⭐⭐', kilo:'⭐⭐', kanser:'⭐⭐⭐⭐⭐',
    detail:`<b>Koruyucu besinler:</b><br>
🥦 Brokoli, lahana, karnabahar → Sulforafan, indol-3-karbinol<br>
🍅 Domates (pişmiş) → Likopen (prostat kanseri)<br>
🧄 Sarımsak, soğan → Allilsülfürler<br>
🫐 Yaban mersini, kiraz → Antosiyaninler<br>
🌾 Tam tahıl → Lif (kolon kanseri riski düşürür)<br>
🐟 Omega-3 (balık) → İltihap baskılar, tümör büyümesi yavaşlar<br><br>
<b>Kaçınılacaklar:</b><br>
❌ İşlenmiş et (salam, sucuk, pastırma) — WHO Grup 1 kanserojen<br>
❌ Aşırı alkol → 6 kanser türüyle doğrudan ilişkili<br>
❌ Kızarmış gıda → Akrilamid oluşumu<br>
❌ Şekerli içecekler → İnsülin artışı tümör büyümesini tetikler`
  }
];

// activeBlogCat: Aktif kategori filtresi — "all" ise tüm kartlar gösterilir.
let activeBlogCat = 'all';

/**
 * renderBlogGrid — Blog/rehber ızgarasını (blogGrid) filtreli olarak çizer.
 *
 * activeBlogCat değişkenine göre BLOG_DATA'yı filtreler ('all' ise tümü).
 * Her kart; kategori etiketi, başlık, özet, etki yıldızları ve detay butonu içerir.
 * Malzeme ve adım bilgisi varsa tarif formatında gösterir.
 */
function renderBlogGrid() {
  const grid = document.getElementById('blogGrid');
  if (!grid) return;
  const list = activeBlogCat === 'all'
    ? BLOG_DATA
    : BLOG_DATA.filter(b => b.cat === activeBlogCat);

  grid.innerHTML = list.map((b, i) => {
    const catLabels = {tarif:'🍽️ Tarif', beslenme:'🥗 Beslenme', gida:'🥑 Gıda Faydası', immun:'🛡️ Bağışıklık', kilo:'⚖️ Kilo', kanser:'💊 Kanser Koruma'};
    const catClass = 'cat-' + b.cat;
    const ings = b.ingredients ? `<div class="recipe-ings">${b.ingredients.map(i=>`<span class="recipe-ing-tag">🥘 ${esc(i)}</span>`).join('')}</div>` : '';
    const steps = b.steps ? `<ol class="recipe-steps">${b.steps.map(s=>`<li>${esc(s)}</li>`).join('')}</ol>` : '';
    return `<div class="blog-card" data-bidx="${i}">
      <div class="blog-card-header">
        <span class="blog-card-emoji">${b.emoji}</span>
        <div class="blog-card-meta">
          <span class="blog-card-cat ${catClass}">${catLabels[b.cat]||b.cat}</span>
          <h3>${esc(b.title)}</h3>
        </div>
      </div>
      <div class="blog-card-lead">${esc(b.lead)}</div>
      <div class="blog-card-stats">
        ${b.immun ? `<span class="blog-stat-pill pill-immun">🛡️ Bağışıklık: ${b.immun}</span>` : ''}
        ${b.kilo  ? `<span class="blog-stat-pill pill-kilo">⚖️ Kilo: ${b.kilo}</span>` : ''}
        ${b.kanser? `<span class="blog-stat-pill pill-kanser">💊 Kanser: ${b.kanser}</span>` : ''}
      </div>
      <div class="blog-card-body" id="blogBody${i}">
        ${ings}
        ${steps}
        <div style="margin-top:10px;">${b.detail||''}</div>
      </div>
      <button class="blog-expand-btn" onclick="toggleBlogCard(${i}, this)">📖 Detayları Göster</button>
    </div>`;
  }).join('') || '<p style="color:var(--muted);text-align:center;grid-column:1/-1;padding:40px;">Bu kategoride içerik yok.</p>';
}

/**
 * toggleBlogCard — Blog kartının detay içeriğini açar/kapatır.
 *
 * @param {number} idx - BLOG_DATA dizisindeki kart indeksi.
 * @param {HTMLElement} btn - Tıklanan "Detayları Göster" butonu.
 *
 * Kart açılınca tipsRead sayacı artırılır ve 'tips' rozet koşulu denetlenir.
 */
function toggleBlogCard(idx, btn) {
  const body = document.getElementById('blogBody' + idx);
  if (!body) return;
  const open = body.classList.toggle('open');
  btn.textContent = open ? '🔼 Kapat' : '📖 Detayları Göster';
  // badge rozet
  if (open) {
    tipsRead = (tipsRead||0) + 1;
    localStorage.setItem('tipsRead', tipsRead);
    checkBadge('tips', tipsRead >= 5);
  }
}

// Blog filtre butonları — chip tıklanınca activeBlogCat güncellenir ve ızgara yeniden çizilir.
// Aktif chip altın renk alır (.active class), diğerlerinden kaldırılır.
document.getElementById('blogFilterBar')?.querySelectorAll('.blog-chip').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('#blogFilterBar .blog-chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeBlogCat = btn.dataset.bcat;
    renderBlogGrid();
  });
});

// Render on page load
renderBlogGrid();

console.log('✅ dashboard_fix.js yüklendi');
  
  </script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
</body>
</html>
