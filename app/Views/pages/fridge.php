<?php

$pageTitle = 'Dijital Buzdolabı';
?>
<!doctype html>
<html lang="tr">

<head>
  <meta charset="utf-8">
  <title>ChefMate AI | Dijital Buzdolabı</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/fridge.css">
</head>

<body class="fridge-body">
<body class="fridge-body">

  <!--
    ANA SARMALAYICI (#mainWrapper)
    Buzdolabı gövdesi ve sağ paneli yan yana tutar.
    Kapı açıldığında toggleDoor() bu elemana .active-panel class'ı ekler;
    bu class padding-left'i azaltarak sağ panelin kayarak görünmesini sağlar.
    perspective: 2500px → kapı açılma animasyonuna gerçekçi 3D derinlik verir.
  -->
  <div class="fridge-main-wrapper" id="mainWrapper">

    <!--
      BUZDOLABI ANA GÖVDE (#fridgeUnit)
      Metal görünümlü koyu gövde (450×750px).
      İçinde 3 ana bölüm barındırır:
        1) smart-sensor-bar  → Üst LED ve seri numarası şeridi
        2) upper-fridge      → Üst bölme (buzdolabı) — 2 kapı + 2 raf
        3) lower-freezer     → Alt bölme (buzluk) — 2 kapı + 1 raf
    -->
    <div class="fridge-unit" id="fridgeUnit">

      <!-- AKILLI SENSÖR ŞERİDİ
           Gövdenin en üstünde yer alır.
           sensor-eye    → Kızıl noktalı dekoratif "kamera/sensör" görünümü
           status-leds   → 3 LED: yeşil=güç aktif, mavi=WiFi bağlı, altın=AI aktif
                           Her LED css @keyframes pulse ile yanıp söner
           sensor-serial → Sağda "UNIT-ID: AI-8800 PRO" modeli/seri etiketi
      -->
      <div class="smart-sensor-bar">
        <div class="sensor-eye"></div>

        <div class="status-leds">
          <div class="led led-power pulse-green"></div>
          <div class="led led-wifi pulse-blue"></div>
          <div class="led led-ai pulse-gold"></div>
        </div>

        <div class="sensor-serial">UNIT-ID: AI-8800 PRO</div>
      </div>

      <!-- ÜST BÖLME (Buzdolabı)
           İki kapı yan yana; her biri tıklanınca toggleDoor() çağrılır.
           Sol kapı:  door-left-top  → transform-origin: left  → sola açılır
           Sağ kapı:  door-right-top → transform-origin: right → sağa açılır
           CSS class .open → rotateY(-115deg) ile 3D perspektif kapı açılması
           #tempDisplay → Sağ kapıda sıcaklık etiketi; tıklanınca sıcaklık popup'ı açılır
           fridge-interior → Ürün emojilerinin yerleştiği iç alan (2 raf: shelf-1, shelf-2)
      -->
      <div class="fridge-section upper-fridge">

        <div class="door door-left-top" onclick="toggleDoor('left-top')">
          <div class="pro-handle"></div>
        </div>

        <div class="door door-right-top" onclick="toggleDoor('right-top')">
          <div class="pro-handle"></div>
          <div class="smart-tag" id="tempDisplay" onclick="openTempControl(event)">3°C</div>
        </div>

        <div class="fridge-interior">
          <div class="shelf-line" id="shelf-1"></div>
          <div class="shelf-line" id="shelf-2"></div>
        </div>

      </div>

      <!-- ALT BUZLUK
           Üst bölmenin yarısı kadar yükseklikte (flex: 1 vs 2).
           Aynı kapı mekanizması; kısa kol (.pro-handle-short) kullanılır.
           freezer-bg class'ı: Açık mavi buz rengi iç arka plan.
           freezer-shelf: Dondurulmuş ürünlerin gösterildiği tek raf.
      -->
      <div class="fridge-section lower-freezer">

        <div class="door door-left-bottom" onclick="toggleDoor('left-bottom')">
          <div class="pro-handle-short"></div>
        </div>

        <div class="door door-right-bottom" onclick="toggleDoor('right-bottom')">
          <div class="pro-handle-short"></div>
        </div>

        <div class="fridge-interior freezer-bg">
          <div class="shelf-line" id="freezer-shelf"></div>
        </div>

      </div>

    </div>

    <!-- SAĞ ENVANTER PANELİ (#sidePanel)
         Varsayılan olarak ekran dışında (right: -420px); herhangi bir kapı
         açılınca .fridge-main-wrapper.active-panel ile right: 40px'e kayar.
         panel-header        → "CHEFMATE PRO" başlığı + "SİSTEM AKTİF" dönen ikon
         inventory-scroll-area (#inventoryList) → fridge.js tarafından doldurulan
                               dinamik ürün kartları listesi (inv-card elemanları)
         panel-footer        → "TARİFLERE GİT" ve "EKLE / PANEL" butonları
    -->
    <div class="side-ai-panel" id="sidePanel">

      <div class="panel-header">
        <h3 class="panel-title">CHEFMATE <span class="text-warning">PRO</span></h3>
        <p class="panel-subtitle">
          <i class="fas fa-circle-notch fa-spin text-success me-1"></i> SİSTEM AKTİF
        </p>
      </div>

      <div class="inventory-scroll-area" id="inventoryList"></div>

      <div class="panel-footer">
        <a href="tarifler" class="btn-pro btn-main-ai" onclick="top.location.href='tarifler'; return false;">
          <i class="fas fa-magic me-2"></i>TARİFLERE GİT
        </a>
        <div class="btn-group-pro">
          <button class="btn-pro btn-add" onclick="openAddModal()">EKLE</button>
          <a href="dashboard" class="btn-pro btn-back" onclick="top.location.href='dashboard'; return false;">PANEL</a>
        </div>
      </div>

    </div>

  </div>

  <!-- ÜRÜN EKLE MODALI (#addModal)
       openAddModal() ile display:flex yapılır; closeAddModal() ile gizlenir.
       İşleyiş adımları:
         1) Arama kutusu (#productSearch) → searchInDatabase() ile productDB filtrelenir
         2) Sonuçlar (#searchResults) listesinden bir ürün seçilir → lastSelectedProduct ayarlanır
         3) Ürün bulunamazsa #createNewBox gösterilir → özel ürün adı
         4) Raf seçimi (#shelfSelect): Üst Raf / Alt Raf / Buzluk
         5) SKT tarihi (#expiryDateInput) zorunludur
         6) "SİSTEME İŞLE" → addNewItem() çağrılır → api.php → fridge_add
  -->
  <div id="addModal" class="custom-overlay-modal">
    <div class="modal-card">
      <h4 class="text-warning font-cinzel text-center">SİSTEME ÜRÜN GİRİŞİ</h4>

      <input type="text" id="productSearch" class="pro-input-field mt-3" placeholder="Ürün ara..."
        oninput="searchInDatabase()">

      <div id="searchResults" class="db-search-results-pro list-visible mt-2"></div>
      <div id="selectedDisplay" class="selected-badge-pro d-none"></div>

      <!-- Özel ürün oluşturma kutusu
           Arama sonucu boş gelince otomatik görünür.
           Kullanıcı ürün adı yazar
           createCustomProductFromModal() ile base64 ikonlu yeni ürün
           localStorage'a kaydedilir ve lastSelectedProduct olarak seçilir.
      -->
      <!-- ✅ Yeni ürün ekleme alanı -->
      <div id="createNewBox" class="new-product-box d-none">
        <div style="font-weight:800; color: var(--gold);">Ürün bulunamadı</div>
        <div class="mini-note">İsmini yazarak yeni ürün ekleyebilirsin.</div>

        <label class="label-pro mt-2">ÜRÜN ADI</label>
        <input id="newProductName" class="pro-input-field" placeholder="Örn: Ejder meyvesi" />

        <button class="btn-confirm mt-3" onclick="createCustomProductFromModal()">
          + ÜRÜNÜ EKLE
        </button>
      </div>

      <div class="form-row-pro mt-3">
        <div class="form-col">
          <label class="label-pro">KONUM</label>
          <select id="shelfSelect" class="pro-input-field">
            <option value="shelf-1">Üst Raf</option>
            <option value="shelf-2">Alt Raf</option>
            <option value="freezer-shelf">Buzluk</option>
          </select>
        </div>

        <div class="form-col">
          <label class="label-pro">S.K.T.</label>
          <input type="date" id="expiryDateInput" class="pro-input-field">
        </div>
      </div>

      <div class="modal-footer-pro mt-4 text-center">
        <button class="btn-confirm" onclick="addNewItem()">SİSTEME İŞLE</button>
        <button class="btn-cancel" onclick="closeAddModal()">İPTAL</button>
      </div>

    </div>
  </div>

  <!-- SICAKLIK AYAR POPUP'I (#tempControlPanel)
       Sağ üst kapıdaki sıcaklık etiketine (#tempDisplay) tıklanınca açılır.
       openTempControl(event) → tıklama koordinatına konumlandırılır.
       +/− butonları changeTemp(±1) ile sıcaklığı değiştirir (−20...+10 arası).
       Popup dışına tıklanınca window click listener ile otomatik kapanır.
  -->
  <div id="tempControlPanel" class="temp-popup">
    <h6 style="font-size: 0.7rem; color: #888; margin-bottom: 10px;">SICAKLIK AYARI</h6>

    <div class="d-flex align-items-center justify-content-between">
      <button class="temp-btn" onclick="changeTemp(-1)">-</button>
      <span id="targetTemp" style="font-weight: 800; color: #FFD700;">3°C</span>
      <button class="temp-btn" onclick="changeTemp(1)">+</button>
    </div>
  </div>

  <script src="assets/js/fridge.js"></script>
</body>

</html>
