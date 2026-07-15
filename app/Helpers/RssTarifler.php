<?php

namespace App\Helpers;

final class RssTarifler
{
    public const CACHE_DIR = __DIR__ . '/../../cache/tarifler/';
    public const CACHE_TIME = 3600;
    public const USER_AGENT = 'Mozilla/5.0 (compatible; ChefMate/1.0; RSS Reader)';
    public const RSS_BASE = 'https://www.lezizyemeklerim.com/rss/yemek-tarifleri';
    public const IMG_CACHE_DIR = __DIR__ . '/../../cache/img/';
    public const IMG_CACHE_TIME = 60 * 60 * 24 * 3;

    public static function kategoriler(): array
    {
        return [
            'hepsi'                => ['isim' => 'Tümü',               'slug' => '',                        'ikon' => '🍽️'],
            'kullanici-tarifleri'  => ['isim' => 'Kullanıcılardan',    'slug' => 'kullanici-tarifleri',     'ikon' => '👨‍🍳'],
            'et-yemekleri'         => ['isim' => 'Et Yemekleri',       'slug' => 'et-yemekleri',             'ikon' => '🥩'],
            'tatli-tarifleri'      => ['isim' => 'Tatlılar',           'slug' => 'tatli-tarifleri',          'ikon' => '🍮'],
            'sebze-yemekleri'      => ['isim' => 'Vejetaryen',         'slug' => 'sebze-yemekleri',          'ikon' => '🥦'],
            'corba-tarifleri'      => ['isim' => 'Çorbalar',           'slug' => 'corba-tarifleri',          'ikon' => '🍲'],
            'hamurisi-tarifleri'   => ['isim' => 'Hamur İşleri',       'slug' => 'hamurisi-tarifleri',       'ikon' => '🥐'],
            'kek-tarifleri'        => ['isim' => 'Kekler',             'slug' => 'kek-tarifleri',            'ikon' => '🎂'],
            'kahvaltilik-tarifler' => ['isim' => 'Kahvaltılık',        'slug' => 'kahvaltilik-tarifler',     'ikon' => '🍳'],
            'balik-yemekleri'      => ['isim' => 'Balık',              'slug' => 'balik-yemekleri',          'ikon' => '🐟'],
            'kurabiye-tarifleri'   => ['isim' => 'Kurabiye',           'slug' => 'kurabiye-tarifleri',       'ikon' => '🍪'],
            'diyet-yemekleri'      => ['isim' => 'Diyet',              'slug' => 'diyet-yemekleri',          'ikon' => '🥗'],
            'kofte-tarifleri'      => ['isim' => 'Köfte',              'slug' => 'kofte-tarifleri',          'ikon' => '🍢'],
        ];
    }

    public static function sayfaCek(string $url): string
    {
        if (!is_dir(self::CACHE_DIR)) {
            mkdir(self::CACHE_DIR, 0755, true);
        }
        $cacheKey = self::CACHE_DIR . md5($url) . '.xml';
        if (file_exists($cacheKey) && (time() - filemtime($cacheKey)) < self::CACHE_TIME) {
            return file_get_contents($cacheKey);
        }
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_USERAGENT      => self::USER_AGENT,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/rss+xml, application/xml, text/xml, */*',
                'Accept-Language: tr-TR,tr;q=0.9',
            ],
            CURLOPT_ENCODING       => 'gzip',
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $data = curl_exec($ch);
        $kod  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($data && $kod === 200) {
            file_put_contents($cacheKey, $data);
            return $data;
        }
        return '';
    }

    public static function cacheRead(string $path, int $ttl): ?string
    {
        if (file_exists($path) && (time() - filemtime($path)) < $ttl) {
            return file_get_contents($path);
        }
        return null;
    }

    public static function cacheWrite(string $path, string $data): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, $data);
    }

    public static function httpGetHtml(string $url): string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.7,en;q=0.6',
            ],
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $html = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($html && $code >= 200 && $code < 300) {
            return $html;
        }
        return '';
    }

    public static function scrapeBestImageFromRecipePage(string $url): string
    {
        $cacheFile = self::IMG_CACHE_DIR . md5($url) . '.txt';
        $cached = self::cacheRead($cacheFile, self::IMG_CACHE_TIME);
        if ($cached !== null) {
            $cached = trim($cached);
            if ($cached !== '') {
                return $cached;
            }
        }

        $html = self::httpGetHtml($url);
        if (!$html) {
            return '';
        }

        $img = '';
        if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
            $img = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
        }
        if (!$img && preg_match('/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
            $img = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
        }
        if (!$img && preg_match('/<img[^>]+(?:data-src|data-lazy-src|src)=["\']([^"\']+)["\'][^>]*>/i', $html, $m)) {
            $img = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
        }

        if ($img && str_starts_with($img, '//')) {
            $img = 'https:' . $img;
        }
        if ($img && str_starts_with($img, '/')) {
            $p = parse_url($url);
            $img = ($p['scheme'] ?? 'https') . '://' . ($p['host'] ?? '') . $img;
        }

        self::cacheWrite($cacheFile, $img);
        return $img;
    }

    public static function rssParseEt(string $xml): array
    {
        if (empty($xml)) {
            return [];
        }
        libxml_use_internal_errors(true);
        $feed = simplexml_load_string($xml);
        if (!$feed) {
            return [];
        }
        $tarifler = [];
        $items = $feed->channel->item ?? [];
        foreach ($items as $item) {
            $ns = $item->getNamespaces(true);
            $resim = '';
            foreach ($item->enclosure as $enc) {
                $resim = (string)($enc['url'] ?? '');
                break;
            }
            if (empty($resim) && isset($ns['media'])) {
                $m = $item->children($ns['media']);
                $resim = (string)($m->content['url'] ?? '');
            }
            if (empty($resim) && isset($ns['content'])) {
                $c = (string)($item->children($ns['content'])->encoded ?? '');
                preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $c, $m2);
                if (!empty($m2[1])) {
                    $resim = $m2[1];
                }
            }
            if (empty($resim)) {
                preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', (string)$item->description, $m3);
                if (!empty($m3[1])) {
                    $resim = htmlspecialchars_decode($m3[1]);
                }
            }
            $baslik = html_entity_decode(strip_tags((string)$item->title), ENT_QUOTES, 'UTF-8');
            $link = (string)$item->link;
            $tarih = (string)$item->pubDate;
            $ozet = strip_tags(html_entity_decode((string)$item->description, ENT_QUOTES, 'UTF-8'));
            $ozet = mb_substr(trim(preg_replace('/\s+/', ' ', $ozet)), 0, 130);
            if (!empty($baslik) && !empty($link)) {
                $tarifler[] = [
                    'baslik' => $baslik,
                    'resim'  => $resim,
                    'url'    => $link,
                    'tarih'  => $tarih ? date('d.m.Y', strtotime($tarih)) : '',
                    'ozet'   => $ozet,
                ];
            }
        }
        return $tarifler;
    }

    public static function tumTarifleriCek(): array
    {
        $kategoriler = self::kategoriler();
        $sirali = ['et-yemekleri', 'tatli-tarifleri', 'corba-tarifleri', 'hamurisi-tarifleri', 'sebze-yemekleri', 'kahvaltilik-tarifler', 'kek-tarifleri', 'balik-yemekleri', 'kurabiye-tarifleri', 'kofte-tarifleri'];
        $sonuc = [];
        foreach ($sirali as $s) {
            if (!isset($kategoriler[$s])) continue;
            $list = self::rssParseEt(self::sayfaCek(self::RSS_BASE . '/' . $kategoriler[$s]['slug']));
            $sonuc = array_merge($sonuc, $list);
        }
        $unique = [];
        $seen = [];
        foreach ($sonuc as $t) {
            $key = md5($t['url'] ?? $t['baslik']);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $t;
            }
        }
        return $unique;
    }

    public static function getirTariflerIcinKategori(string $aktifKat, string $aramaMetni): array
    {
        $kategoriler = self::kategoriler();

        if (!empty($aramaMetni)) {
            $araListesi = ['et-yemekleri', 'tatli-tarifleri', 'corba-tarifleri', 'hamurisi-tarifleri', 'sebze-yemekleri', 'kahvaltilik-tarifler', 'kek-tarifleri', 'balik-yemekleri'];
            $items = [];
            foreach ($araListesi as $s) {
                foreach (self::rssParseEt(self::sayfaCek(self::RSS_BASE . '/' . $kategoriler[$s]['slug'])) as $t) {
                    if (mb_stripos($t['baslik'], $aramaMetni) !== false) {
                        $items[] = $t;
                    }
                }
            }
            return $items;
        }

        if ($aktifKat === 'hepsi') {
            return self::tumTarifleriCek();
        }

        return self::rssParseEt(self::sayfaCek(self::RSS_BASE . '/' . $kategoriler[$aktifKat]['slug']));
    }
}
