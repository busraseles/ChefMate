<div align="center">

# 🍳 ChefMate

**Akıllı bir mutfak yönetim platformu — buzdolabınızı takip edin, tarifler keşfedin ve malzemeleri yapay zekâ ile tanıyın.**

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![Flask](https://img.shields.io/badge/Flask-3.0-000000?style=flat-square&logo=flask&logoColor=white)](https://flask.palletsprojects.com/)
[![Ultralytics YOLO](https://img.shields.io/badge/YOLO11-Ultralytics-00FFFF?style=flat-square&logo=python&logoColor=black)](https://github.com/ultralytics/ultralytics)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square)](#-lisans)

[Özellikler](#-özellikler) • [Mimari](#-mimari) • [Teknoloji Yığını](#-teknoloji-yığını) • [Kurulum](#-kurulum) • [Yapay Zekâ Motoru](#-yapay-zekâ-motoru) • [Güvenlik](#-güvenlik)

</div>

---

## 📖 Genel Bakış

**ChefMate**, kullanıcıların gıda israfını azaltmasına, öğün planlamasına ve elindeki malzemelerle yemek yapmasına yardımcı olan uçtan uca bir mutfak yönetim platformudur. Kullanıcılar; son kullanma tarihi uyarılarıyla buzdolabı envanterini takip edebilir, özel eğitilmiş bir görüntü tanıma modeliyle fotoğraftan malzeme tanıyabilir, eldeki malzemelere göre tarif önerileri alabilir ve topluluk etkileşimiyle kişisel bir tarif koleksiyonu yönetebilir — tüm bunlar tek bir web uygulaması üzerinden.

Arka uç, kendi geliştirilmiş bir **PHP MVC framework**'üdür; bu yapı, malzeme tanıma için bir YOLO11 görüntü sınıflandırma modeli sunan özel bir **Python/Flask mikroservisi** ile birlikte çalışır.

---

## ✨ Özellikler

- 🥕 **Buzdolabı Envanter Takibi** — son kullanma tarihleriyle ürün ekleme, düzenleme, kaldırma ve otomatik son kullanma bildirimleri
- 📸 **Yapay Zekâ ile Malzeme Tanıma** — özel eğitilmiş bir YOLO11 modeliyle fotoğraftan gıda öğesi tanıma
- 🍽️ **Yapay Zekâ Tarif Önerileri** — mevcut buzdolabı içeriğine ve süresi yakında dolacak ürünlere göre sıralanan tarifler
- 📚 **Tarif Keşfi** — harici bir RSS kaynağından beslenen, kategorilere ayrılmış ve önbelleğe alınan tarif akışı
- 👨‍🍳 **Kişisel Tarif Defteri** — görsel ve talimatlarla özel tarifler oluşturma, yükleme ve yönetme
- 🌐 **Topluluk Etkileşimi** — diğer kullanıcıların paylaştığı tarifleri beğenme, kaydetme ve yorum yapma
- 🛒 **Alışveriş Listesi** — durumu değiştirilebilen bir alışveriş yapılacaklar listesi
- 🗑️ **İsraf Takibi** — ev israfı konusunda farkındalık oluşturmak için atılan ürünleri kaydetme
- 📊 **Panel ve Analizler** — öğünler, kaloriler, su tüketimi ve başarımların bütünleşik görünümü
- 🔥 **Kalori ve Öğün Planlayıcı** — öğün kaydetme, günlük/haftalık kalori takibi, tariflerden doğrudan giriş ekleme
- 💧 **Su Tüketimi Takibi** — haftalık trend görünümleriyle günlük kayıt
- 🏆 **Rozetler ve Seriler** — oyunlaştırılmış günlük başarımlar
- 🔔 **Bildirim Merkezi** — okundu/okunmadı durumuyla merkezi, kullanıcıya özel bildirimler
- 🔐 **Kimlik Doğrulama** — CSRF korumalı ve güçlendirilmiş çerezlere sahip oturum tabanlı giriş/kayıt

---

## 🏗️ Mimari

ChefMate, HTTP üzerinden birbiriyle iletişim kuran iki bağımsız olarak dağıtılabilen servisten oluşur:

```
┌────────────────────────┐        HTTP (multipart/form-data)        ┌──────────────────────────┐
│   PHP MVC Uygulaması   │ ─────────────────────────────────────▶  │   Flask Yapay Zekâ       │
│   (public/index.php)   │                                          │   Servisi                │
│                        │ ◀─────────────────────────────────────  │   (python-api/app.py)    │
│  Router → Middleware   │             JSON tahmin                  │                          │
│  → Controller → Model  │                                          │  YOLO11-cls (Ultralytics)│
│                        │                                          │  + OpenCV ön işleme      │
└──────────┬─────────────┘                                          └──────────────────────────┘
           │
           ▼
   ┌───────────────┐
   │  MySQL (PDO)  │
   └───────────────┘
```

1. Tüm istekler, tek bir ön denetleyici (`public/index.php`) üzerinden yönlendirilir.
2. Özel bir `Router`, istekleri `routes/web.php` ve `routes/api.php` ile eşleştirir, yol parametrelerini çözer ve bağlı ara katman yazılımlarını (`AuthMiddleware`, `RoleMiddleware`) çalıştırır.
3. Denetleyiciler iş mantığını koordine eder ve kalıcılık işlemlerini, PDO hazırlanmış ifadeleri aracılığıyla MySQL ile iletişim kuran Modellere devreder.
4. Görüntü sınıflandırma istekleri, `FlaskClient` (cURL) aracılığıyla Python mikroservisine iletilir.

<details>
<summary><strong>Neden ayrı bir yapay zekâ servisi?</strong></summary>

ML iş yükünü özel bir Flask sürecinde izole etmek; modelin bellekte yalnızca bir kez yüklenip istekler arasında yeniden kullanılmasını, görüntü çözümlemesinin OpenCV ile doğrudan ham istek baytları üzerinde yapılmasını (gereksiz disk I/O olmadan) ve PHP uygulamasının Python bağımlılıklarından tamamen bağımsız kalmasını sağlar — her servis birbirinden bağımsız olarak dağıtılabilir ve ölçeklendirilebilir.

</details>

---

## 🛠️ Teknoloji Yığını

| Katman                | Teknoloji                                                       |
|-------------------------|---------------------------------------------------------------------|
| Arka Uç Framework'ü        | Özel PHP MVC (harici framework bağımlılığı yok)                        |
| Yapay Zekâ Mikroservisi      | Python 3, Flask                                                           |
| Makine Öğrenmesi                | Ultralytics YOLO11 (sınıflandırma), PyTorch                                 |
| Görüntü İşleme                    | OpenCV, NumPy                                                                  |
| Veritabanı                          | MySQL (PDO, hazırlanmış ifadeler)                                               |
| Ön Uç                                  | HTML, CSS, JavaScript                                               |
| Kimlik Doğrulama ve Oturum                | PHP yerel oturumları, `password_hash`/`password_verify`, CSRF token'ları           |
| Sunucu                                      | Apache (`.htaccess` yönlendirme) veya PHP destekleyen herhangi bir sunucu             |

---

## 📁 Proje Yapısı

```
chefmate/
├── app/
│   ├── Controllers/        # Auth, Dashboard, Fridge, Page, Prediction, Recipe, User
│   ├── Models/              # Badge, Dashboard, Fridge, Menu, Recipe, Shopping, User, Waste, Water
│   ├── Views/                # PHP görünüm şablonları (auth, pages, recipes, layouts)
│   ├── Core/                 # Router, Request, Response, Database, Csrf, FlaskClient, Validator
│   │   └── Middleware/       # AuthMiddleware, RoleMiddleware
│   └── Helpers/               # Security, RecipeScraper, RssTarifler, Url
├── routes/
│   ├── web.php                # Tarayıcıya yönelik rotalar
│   └── api.php                # JSON API rotaları
├── config/
│   └── config.example.php     # Yapılandırma şablonu
├── public/
│   ├── index.php               # Ön denetleyici / giriş noktası
│   └── assets/                 # CSS, JS, görseller
├── python-api/
│   ├── app.py                   # Flask HTTP giriş noktası
│   ├── services/predictor.py     # YOLO11 çıkarım mantığı
│   ├── model/chefmate_cls_v22.pt # Eğitilmiş sınıflandırma modeli
│   └── requirements.txt
├── cache/                       # RSS, tarif ve yapay zekâ öneri önbellekleri
└── storage/logs/                # Uygulama günlükleri
```

---

## 🚀 Kurulum

### Ön Koşullar

- `pdo_mysql`, `curl` ve `mbstring` uzantılarına sahip PHP 8.1+
- MySQL 8.0+
- Python 3.10+
- Apache (veya URL yönlendirmesi destekleyen başka bir web sunucusu)

### Kurulum Adımları

```bash
git clone https://github.com/<your-org>/chefmate.git
cd chefmate
cp config/config.example.php config/config.php
```

config/config.php dosyasını kendi ortamınıza uygun şekilde düzenleyin.

Not: Veritabanı şeması güvenlik ve proje bütünlüğü nedeniyle bu herkese açık depoda paylaşılmamaktadır.

**PHP uygulamasını başlatın:**

```bash
php -S localhost:8000 -t public
```

**Yapay zekâ mikroservisini başlatın:**

```bash
cd python-api
python -m venv venv
source venv/bin/activate      # Windows: venv\Scripts\activate
pip install -r requirements.txt
python app.py
```

Flask servisi `http://127.0.0.1:5000` adresinde başlar ve `/predict` ile `/health` uç noktalarını sunar.

---

## ⚙️ Yapılandırma

| Anahtar                    | Açıklama                                                    |
|-------------------------------|------------------------------------------------------------------|
| `db.host` / `db.name`            | MySQL sunucu adresi ve veritabanı adı                              |
| `db.user` / `db.pass`              | Veritabanı kimlik bilgileri                                          |
| `app.env`                            | `development` veya `production` — hata ayrıntı seviyesini kontrol eder |
| `flask.base_url`                       | Yapay zekâ mikroservisinin temel adresi                                  |
| `flask.timeout`                          | Tahmin çağrıları için zaman aşımı süresi (saniye)                          |

> `config/config.php` git tarafından yok sayılır — gerçek kimlik bilgilerini asla commit etmeyin. Şablon olarak `config/config.example.php` dosyasını kullanın.

---

## 🤖 Yapay Zekâ Motoru

Malzeme tanıma, özel bir Flask mikroservisi tarafından sunulan **özel eğitilmiş bir YOLO11 sınıflandırma modeli** (`chefmate_cls_v22.pt`) ile çalışır.

**Tahmin süreci:**

1. PHP uygulaması, yüklenen görüntüyü `multipart/form-data` aracılığıyla Flask'ın `/predict` uç noktasına gönderir.
2. Servis dosya uzantısını doğrular ve ham baytları okur.
3. **OpenCV**, görüntüyü çözer (`cv2.imdecode`) ve BGR formatından RGB'ye dönüştürür.
4. Bir kez yüklenip tekil (singleton) olarak yeniden kullanılan YOLO11 modeli, en olası sınıfı ve güven skorunu döndürür:

```json
{ "success": true, "food": "tomato", "confidence": 0.97 }
```

Tahminler doğrudan buzdolabı envanterine aktarılır; böylece kullanıcılar malzemeleri elle yazmak yerine bir fotoğrafla ekleyebilir.

---

## 🔒 Güvenlik

- Şifreler `password_hash()` ile hash'lenir, `password_verify()` ile doğrulanır — asla düz metin olarak saklanmaz
- CSRF token'ları (kriptografik olarak rastgele, `hash_equals` ile doğrulanır) tüm durum değiştiren istekleri korur
- Oturum çerezleri güçlendirilmiştir: `HttpOnly`, `SameSite=Lax`, HTTPS üzerinde `Secure`, katı oturum modu
- Oturum kimlik sabitleme saldırılarını önlemek için girişte oturum kimliği yeniden oluşturulur
- Her yanıtta güvenlik başlıkları gönderilir: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`
- Tüm veritabanı erişimi, SQL enjeksiyonunu önlemek için PDO hazırlanmış ifadeleri kullanır
- Yüklenen dosyalar, kaydedilmeden önce MIME türü ve boyutuna göre doğrulanır
- Rota düzeyindeki `AuthMiddleware`/`RoleMiddleware`, korunan uç noktaları denetler ve API istemcileri için JSON `401`/`403` yanıtları döndürür
- Üretim modu, ayrıntılı hata çıktısını gizler ve istisnaları sunucu tarafında günlüğe kaydeder

---

<div align="center">

## 👨‍💻 Geliştirici

PHP MVC ve Python/Flask tabanlı yapay zekâ servislerini bir araya getiren full-stack bir web uygulaması olarak geliştirildi.

**[⬆ başa dön](#-chefmate)**

</div>
