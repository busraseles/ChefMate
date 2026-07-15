<?php

$pageTitle = 'Hakkımızda';
$extraCss = ['assets/css/about.css'];
$activeNav = 'about';
$navbarStyle = 'solid'; 
?>
<!doctype html>
<html lang="tr" data-theme="light">
<head>
<?php require __DIR__ . '/../layouts/head.php'; ?>
</head>
<body>

<?php require __DIR__ . '/../layouts/navbar.php'; ?>

<!-- ============================================================
     ANA İÇERİK — Makale stili düzende projeyi tanıtan bölüm
     data-aos attribute'ları scroll sırasında animasyon tetikler
     ============================================================ -->
<main class="editorial-container">

  <!-- SAYFA BAŞLIĞI: "Mutfağın Akıllı Dönüşümü" -->
  <header data-aos="fade-up">
    <h1 class="main-title">Mutfağın <span>Akıllı</span> <br> Dönüşümü</h1>
    <!-- Altın sol çizgili giriş paragrafı -->
    <p class="lead-text">
      Günümüzde mutfakta geçirilen zamanı sadece yemek pişirmekten öteye taşıyan,
      yapay zeka destekli bir asistan hayal edin.
    </p>
  </header>

  <div class="article-body">

    <!-- GENEL TANITIM PARAGRAFİ -->
    <p data-aos="fade-up">
      <span class="highlight-gold">ChefMate.AI</span>, "Evdekilerle Harikalar Yarat" mottosuyla yola çıkan,
      modern mutfak yönetimini dijital bir ekosistemle birleştiren kapsamlı bir platformdur.
      Bu proje, geleneksel yemek tarifi sitelerinin ötesine geçerek; sürdürülebilirlik,
      akıllı envanter yönetimi ve kişiselleştirilmiş sağlık takibini tek bir noktada buluşturur.
    </p>

    <!-- VİZYON BÖLÜMÜ BAŞLIĞI -->
    <h2 class="section-heading" data-aos="fade-right">Vizyon ve Yetenekler</h2>
    <p data-aos="fade-up">
      ChefMate.AI, kullanıcılarının elindeki malzemeleri en verimli şekilde kullanmalarını sağlamak ve
      "Bugün ne pişirsem?" sorusuna bilimsel ve yaratıcı çözümler sunmak için tasarlanmıştır.
    </p>

    <!-- MODÜLLER LİSTESİ: Her modül projenin bir ana özelliğini açıklar -->
    <div class="feature-list">

      <!-- MODÜL 01: Yapay zeka ile malzeme eşleştirme ve kamera tanıma -->
      <div class="feature-item" data-aos="fade-up">
        <span class="feature-num">MODÜL 01</span>
        <span class="feature-title">Yapay Zeka Destekli Akıllı Tarif Bulucu</span>
        <p>
          Platformun kalbinde yer alan AI algoritması, kullanıcının elindeki malzemeleri analiz ederek saniyeler içinde eşleştirme yapar.
          Sadece metin girişiyle değil, <span class="highlight-gold">kamera tanıma sistemi</span> sayesinde malzemelerin fotoğrafını çekerek de sisteme veri girişi yapılabilmektedir.
        </p>
      </div>

      <!-- MODÜL 02: Son kullanma tarihi takibi ve gıda israfını önleme -->
      <div class="feature-item" data-aos="fade-up">
        <span class="feature-num">MODÜL 02</span>
        <span class="feature-title">Dijital Dolap ve Envanter Yönetimi</span>
        <p>
          Mutfağınızdaki ürünlerin dijital bir ikizini oluşturur. Ürünlerin son kullanma tarihlerini ve saklama koşullarını takip ederek
          <span class="highlight-emerald">gıda israfını minimuma</span>
          indirir. Profesyonel bir mutfak yönetimi deneyimi sunar.
        </p>
      </div>

      <!-- MODÜL 03: Su/hidrasyon takibi ve BMR hesaplama paneli -->
      <div class="feature-item" data-aos="fade-up">
        <span class="feature-num">MODÜL 03</span>
        <span class="feature-title">Kişisel Sağlık ve Vücut Analiz Paneli</span>
        <p>
          "Su + Vücut" paneli üzerinden hidrasyon takibi yapabilir; boy, kilo ve yaş verilerinizle
          Bazal Metabolizma Hızınızı (<span class="math-box">$BMR$</span>) ve günlük kalori ihtiyacınızı hesaplayabilirsiniz.
        </p>
      </div>

      <!-- MODÜL 04: Animasyonlu tarif defteri, sıfır atık haritası ve zamanlayıcı -->
      <div class="feature-item" data-aos="fade-up">
        <span class="feature-num">MODÜL 04</span>
        <span class="feature-title">İnteraktif Mutfak Araçları</span>
        <p>
          Dijital Tarif Defteri, gerçek bir kitap sayfa çevirme animasyonuyla tasarlanmıştır.
          <span class="highlight-emerald">"Konya Sıfır Atık Haritası"</span> gibi entegrasyonlarla çevreci bir yaklaşım sergilerken,
          modüler zamanlayıcılar ile mutfak deneyimini optimize eder.
        </p>
      </div>

    </div>

    <!-- TEKNİK MİMARİ BÖLÜMÜ: Modüler yapı, JS animasyonları ve tema desteği -->
    <h2 class="section-heading" data-aos="fade-right">Teknik Mimari</h2>
    <p data-aos="fade-up">
      ChefMate.AI, kullanıcı dostu bir arayüzü güçlü bir UX ile harmanlar. Her bir fonksiyon bağımsız ama birbiriyle konuşan
      <span class="highlight-gold">modüler yapılar</span> halinde geliştirilmiştir. Gelişmiş JS animasyonları ve dinamik tema desteği
      (Dark/Light Mode) ile kullanıcı verileri gerçek zamanlı olarak işlenir.
    </p>

    <!-- SONUÇ: Projenin özetini vurgulayan kapanış bölümü -->
    <div style="margin-top: 100px; text-align: center;" data-aos="zoom-in">
      <h3 style="font-family: var(--font-serif); font-size: 2.5rem;">Sonuç</h3>
      <p style="max-width: 700px; margin: 20px auto; color: var(--muted);">
        ChefMate.AI, bir yemek tarifi sitesinden çok daha fazlasıdır; o bir yaşam biçimidir.
        Geleceğin mutfak teknolojilerinin bugünkü temsilcisidir.
      </p>
    </div>

  </div>

  <!-- SAYFA ALTI: Telif hakkı bilgisi -->
  <footer class="footer-simple">
    &copy; 2026 ChefMate.AI. Tüm hakları saklıdır.
  </footer>

</main>

<?php require __DIR__ . '/../layouts/scripts_footer.php'; ?>
</body>
</html>
