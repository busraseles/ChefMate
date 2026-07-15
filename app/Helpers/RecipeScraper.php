<?php

namespace App\Helpers;

use DOMDocument;
use DOMElement;
use DOMXPath;

final class RecipeScraper
{
    public static function normalizeTr(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = str_replace(
            ['ş', 'ğ', 'ü', 'ö', 'ı', 'ç', 'Ş', 'Ğ', 'Ü', 'Ö', 'İ', 'Ç'],
            ['s', 'g', 'u', 'o', 'i', 'c', 's', 'g', 'u', 'o', 'i', 'c'],
            $text
        );
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim((string)$text);
    }

    public static function displayName(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }
        return mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');
    }

    public static function ensureDir(string $dir): void
    {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    public static function fetchUrl(string $url, int $timeout = 12): string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (ChefMateAI)',
            CURLOPT_HTTPHEADER     => ['Accept-Language: tr-TR,tr;q=0.9'],
            CURLOPT_ENCODING       => 'gzip',
        ]);

        $html = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$html || $code < 200 || $code >= 400) {
            return '';
        }

        return (string)$html;
    }

    public static function flattenIngredients(mixed $value): array
    {
        $rawItems = [];

        $pushRaw = function (string $item) use (&$rawItems): void {
            $item = html_entity_decode($item, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $item = strip_tags($item);
            $item = preg_replace('/\s+/u', ' ', trim($item));

            if ($item === '') {
                return;
            }

            if (preg_match('/^\[object object\]$/iu', $item)) return;
            if (preg_match('/^object object$/iu', $item)) return;
            if (preg_match('/^[\-\–\—_,.;:\/\\\\]+$/u', $item)) return;

            $rawItems[] = $item;
        };

        $walk = function (mixed $node) use (&$walk, $pushRaw): void {
            if ($node === null) {
                return;
            }

            if (is_string($node)) {
                $node = trim($node);
                if ($node === '') {
                    return;
                }

                if (
                    (str_starts_with($node, '[') && str_ends_with($node, ']')) ||
                    (str_starts_with($node, '{') && str_ends_with($node, '}'))
                ) {
                    $decoded = json_decode($node, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $walk($decoded);
                        return;
                    }
                }

                $parts = preg_split('/[\r\n]+/u', $node);
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part !== '') {
                        $pushRaw($part);
                    }
                }
                return;
            }

            if (is_array($node)) {
                foreach ($node as $item) {
                    if (is_array($item)) {
                        foreach (
                            [
                                'name', 'ingredient', 'ingredients', 'text',
                                'value', 'title', 'label', 'item', 'malzeme'
                            ] as $field
                        ) {
                            if (isset($item[$field])) {
                                if (is_string($item[$field])) {
                                    $pushRaw($item[$field]);
                                    continue 2;
                                }

                                if (is_array($item[$field]) || is_object($item[$field])) {
                                    $walk($item[$field]);
                                    continue 2;
                                }
                            }
                        }

                        $combined = trim(
                            (string)($item['amount'] ?? '') . ' ' .
                                (string)($item['unit'] ?? '') . ' ' .
                                (string)($item['name'] ?? '')
                        );

                        if ($combined !== '') {
                            $pushRaw($combined);
                            continue;
                        }

                        $walk($item);
                        continue;
                    }

                    if (is_object($item)) {
                        $walk((array)$item);
                        continue;
                    }

                    if (is_scalar($item)) {
                        $pushRaw((string)$item);
                    }
                }
                return;
            }

            if (is_object($node)) {
                $walk((array)$node);
            }
        };

        $walk($value);

        $clean = [];
        foreach ($rawItems as $raw) {
            $food = self::ingredientToFoodOnly($raw);
            if ($food !== '') {
                $clean[] = $food;
            }
        }

        return array_values(array_unique($clean));
    }

    public static function ingredientsToText(mixed $value): string
    {
        return implode("\n", self::flattenIngredients($value));
    }

    public static function ingredientToFoodOnly(string $raw): string
    {
        $s = trim($raw);
        if ($s === '') {
            return '';
        }

        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $s = strip_tags($s);
        $s = preg_replace('/\([^)]*\)/u', ' ', $s);
        $s = preg_replace('/\[[^\]]*\]/u', ' ', $s);
        $s = preg_replace('/\s+/u', ' ', $s);
        $s = trim($s);

        if ($s === '') {
            return '';
        }

        if (preg_match('/\[object object\]/iu', $s)) {
            return '';
        }

        $n = self::normalizeTr($s);

        $n = preg_replace('/\b\d+\s*\/\s*\d+\b/u', ' ', $n);
        $n = preg_replace('/\b\d+[.,]?\d*\b/u', ' ', $n);

        $n = preg_replace(
            '/\b(adet|tane|gram|gr|kg|kilo|ml|cl|lt|litre|bardak|su bardagi|cay bardagi|yemek kasigi|tatli kasigi|cay kasigi|kase|tabak|dilim|parca|paket|poset|kutu|demet|dal|dis|avuc|tutam|porsiyon|kepce)\b/u',
            ' ',
            $n
        );

        $n = preg_replace(
            '/\b(taze|kuru|buyuk|kucuk|orta|ince|iri|dogranmis|dogranmış|rendelenmis|ezilmis|haslanmis|haslanan|pisirilmis|yarim|yarım|yaklasik|alabildigi|gerektigi|kadar|icin|ile|ve|veya|uzeri|servis|istege|bagli|opsiyonel|sosu|suyu|suyunu|kabugu|cekirdegi|cekirdekleri|kabuklari)\b/u',
            ' ',
            $n
        );

        $n = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $n);
        $n = preg_replace('/\s+/u', ' ', $n);
        $n = trim($n);

        if ($n === '') {
            return '';
        }

        $multiMap = [
            'beyaz peynir'    => 'beyaz peynir',
            'kasar peyniri'   => 'kaşar peyniri',
            'lor peyniri'     => 'lor peyniri',
            'tulum peyniri'   => 'tulum peyniri',
            'labne peyniri'   => 'labne',
            'domates salcasi' => 'domates salçası',
            'biber salcasi'   => 'biber salçası',
            'zeytin yagi'     => 'zeytinyağı',
            'sivi yag'        => 'sıvı yağ',
            'tere yag'        => 'tereyağı',
            'kuru sogan'      => 'soğan',
            'kirmizi sogan'   => 'soğan',
            'yesil sogan'     => 'taze soğan',
            'taze sogan'      => 'taze soğan',
            'kirmizi biber'   => 'biber',
            'yesil biber'     => 'biber',
            'kapya biber'     => 'biber',
            'dolmalik biber'  => 'biber',
            'tavuk gogsu'     => 'tavuk',
            'tavuk but'       => 'tavuk',
            'dana kiyma'      => 'kıyma',
            'kuzu kiyma'      => 'kıyma',
            'baldo pirinc'    => 'pirinç',
            'basmati pirinc'  => 'pirinç',
            'misir unu'       => 'mısır unu',
            'bugday unu'      => 'un',
            'toz seker'       => 'şeker',
            'pudra sekeri'    => 'pudra şekeri',
            'pul biber'       => 'pul biber',
            'toz biber'       => 'toz biber',
        ];

        foreach ($multiMap as $from => $to) {
            if (str_contains($n, $from)) {
                return $to;
            }
        }

        $tokens = preg_split('/\s+/u', $n, -1, PREG_SPLIT_NO_EMPTY);
        if (!$tokens) {
            return '';
        }

        $reject = [
            'kadar', 'icin', 'ile', 've', 'veya', 'alabildigi', 'gerektigi',
            'haslanan', 'suyu', 'suyunu', 'uzeri', 'servis', 'kagidi',
            'kağıdı', 'tozu', 'kavanoz', 'pisirme', 'pişirme',
        ];

        $filtered = [];
        foreach ($tokens as $tok) {
            if (mb_strlen($tok, 'UTF-8') < 2) {
                continue;
            }
            if (in_array($tok, $reject, true)) {
                continue;
            }
            $filtered[] = $tok;
        }

        if (empty($filtered)) {
            return '';
        }

        $singleMap = [
            'sut' => 'süt', 'yumurta' => 'yumurta', 'un' => 'un', 'seker' => 'şeker',
            'tuz' => 'tuz', 'yogurt' => 'yoğurt', 'peynir' => 'peynir',
            'tereyag' => 'tereyağı', 'tereyagi' => 'tereyağı', 'zeytinyagi' => 'zeytinyağı',
            'yag' => 'yağ', 'salca' => 'salça', 'domates' => 'domates', 'sogan' => 'soğan',
            'sarimsak' => 'sarımsak', 'patates' => 'patates', 'kabak' => 'kabak',
            'brokoli' => 'brokoli', 'pancar' => 'pancar', 'havuc' => 'havuç',
            'patlican' => 'patlıcan', 'salatalik' => 'salatalık', 'biber' => 'biber',
            'pirinc' => 'pirinç', 'bulgur' => 'bulgur', 'makarna' => 'makarna',
            'mercimek' => 'mercimek', 'nohut' => 'nohut', 'fasulye' => 'fasulye',
            'limon' => 'limon', 'maydanoz' => 'maydanoz', 'dereotu' => 'dereotu',
            'nane' => 'nane', 'kekik' => 'kekik', 'karabiber' => 'karabiber',
            'kimyon' => 'kimyon', 'bal' => 'bal', 'pekmez' => 'pekmez',
            'tavuk' => 'tavuk', 'kiyma' => 'kıyma', 'roka' => 'roka',
            'marul' => 'marul', 'zeytin' => 'zeytin', 'limonun' => 'limon',
        ];

        for ($i = count($filtered) - 1; $i >= 0; $i--) {
            $tok = $filtered[$i];
            if (isset($singleMap[$tok])) {
                return $singleMap[$tok];
            }
        }

        $last = end($filtered);
        if (!is_string($last) || $last === '') {
            return '';
        }

        if (in_array($last, $reject, true)) {
            return '';
        }

        if (mb_strlen($last, 'UTF-8') < 2) {
            return '';
        }

        return $last;
    }

    public static function aiNormalizeIngredient(string $raw): string
    {
        $food = self::ingredientToFoodOnly($raw);

        if ($food === '') {
            return '';
        }

        $s = self::normalizeTr($food);

        $map = [
            'kasar peyniri' => 'peynir', 'beyaz peynir' => 'peynir', 'lor peyniri' => 'peynir',
            'tulum peyniri' => 'peynir', 'labne' => 'peynir', 'kirmizi biber' => 'biber',
            'yesil biber' => 'biber', 'kapya biber' => 'biber', 'dolmalik biber' => 'biber',
            'kuru sogan' => 'sogan', 'kirmizi sogan' => 'sogan', 'yesil sogan' => 'sogan',
            'domates salcasi' => 'salca', 'biber salcasi' => 'salca', 'tavuk gogsu' => 'tavuk',
            'tavuk but' => 'tavuk', 'dana kiyma' => 'kiyma', 'kuzu kiyma' => 'kiyma',
            'sivi yag' => 'yag', 'zeytin yagi' => 'zeytinyagi', 'tere yag' => 'tereyagi',
            'tereyag' => 'tereyagi', 'baldo pirinc' => 'pirinc', 'basmati pirinc' => 'pirinc',
            'spagetti' => 'makarna', 'penne' => 'makarna', 'fusilli' => 'makarna',
            'vanilin tozu' => 'vanilin',
        ];

        return $map[$s] ?? $s;
    }

    public static function scrapeRecipeIngredientsOnly(string $url): array
    {
        $cacheDir = dirname(__DIR__, 2) . '/cache/ai_ing/';
        self::ensureDir($cacheDir);
        $cacheFile = $cacheDir . md5($url) . '.json';

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 259200) {
            $cached = json_decode((string)file_get_contents($cacheFile), true);
            if (is_array($cached) && !empty($cached['raw'])) {
                return $cached['raw'];
            }
        }

        $html = self::fetchUrl($url, 12);
        if ($html === '') {
            return [];
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);

        $rawList = [];
        $image = '';

        $imgNode = $xpath->query("//meta[@property='og:image']")->item(0);
        if ($imgNode instanceof DOMElement) {
            $image = trim($imgNode->getAttribute('content'));
        }

        foreach ($xpath->query("//script[@type='application/ld+json']") as $script) {
            $decoded = json_decode($script->textContent, true);
            if (!$decoded) continue;
            $graphs = isset($decoded['@graph']) ? $decoded['@graph'] : [$decoded];
            foreach ($graphs as $g) {
                if (empty($g['recipeIngredient'])) continue;
                $ings = is_array($g['recipeIngredient']) ? $g['recipeIngredient'] : [$g['recipeIngredient']];
                foreach ($ings as $item) {
                    $text = is_string($item) ? $item : (string)($item['name'] ?? $item['text'] ?? '');
                    $text = trim(html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8'));
                    if ($text !== '' && !preg_match('/\[object/i', $text)) {
                        $rawList[] = $text;
                    }
                }
                if (!$image && !empty($g['image'])) {
                    $image = is_string($g['image']) ? $g['image'] : (string)($g['image']['url'] ?? ($g['image'][0] ?? ''));
                }
            }
            if (!empty($rawList)) break;
        }

        if (empty($rawList)) {
            $h2nodes = $xpath->query("//h2");
            foreach ($h2nodes as $h2) {
                $h2text = mb_strtoupper(trim($h2->textContent), 'UTF-8');
                if (str_contains($h2text, 'MALZEME')) {
                    $sibling = $h2->nextSibling;
                    while ($sibling) {
                        if ($sibling->nodeName === 'ul') {
                            foreach ($sibling->childNodes as $li) {
                                if ($li->nodeName === 'li') {
                                    $text = trim($li->textContent);
                                    if ($text !== '') $rawList[] = $text;
                                }
                            }
                            break;
                        }
                        $sibling = $sibling->nextSibling;
                    }
                    if (!empty($rawList)) break;
                }
            }
        }

        if (empty($rawList)) {
            $nodes = $xpath->query("//*[@itemprop='recipeIngredient']");
            if ($nodes && $nodes->length > 0) {
                foreach ($nodes as $node) {
                    $text = trim($node->textContent);
                    if ($text !== '') $rawList[] = $text;
                }
            }
        }

        if (empty($rawList)) {
            $queries = [
                "//ul[contains(@class,'ingredient')]//li",
                "//ul[contains(@class,'malzeme')]//li",
                "//ul[contains(@class,'Malzeme')]//li",
                "//div[contains(@class,'ingredient')]//li",
                "//div[contains(@class,'malzeme')]//li",
                "//ul[contains(@class,'recipe-ingr')]//li",
            ];
            foreach ($queries as $q) {
                $nodes = $xpath->query($q);
                if (!$nodes || $nodes->length === 0) continue;
                foreach ($nodes as $node) {
                    $text = trim($node->textContent);
                    if ($text !== '') $rawList[] = $text;
                }
                if (!empty($rawList)) break;
            }
        }

        if (empty($rawList)) {
            $liNodes = $xpath->query("//ul//li");
            if ($liNodes) {
                foreach ($liNodes as $li) {
                    $text = trim($li->textContent);
                    if ($text === '' || mb_strlen($text) > 120) continue;
                    if (preg_match('/\d|gram|ml|bardak|kaşık|adet|dilim|demet|çay kaşığı|yemek kaşığı/iu', $text)) {
                        $rawList[] = $text;
                    }
                }
            }
        }

        $rawList = array_values(array_unique(array_filter($rawList, function (string $item): bool {
            $item = trim($item);
            if ($item === '') return false;
            if (preg_match('/\[object/i', $item)) return false;
            if (mb_strlen($item) > 150) return false;
            return true;
        })));

        file_put_contents($cacheFile, json_encode(
            ['raw' => $rawList, 'image' => $image],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ));

        return $rawList;
    }

    public static function getRecipePageImage(string $url): string
    {
        $cacheFile = dirname(__DIR__, 2) . '/cache/ai_ing/' . md5($url) . '.json';
        if (file_exists($cacheFile)) {
            $cached = json_decode((string)file_get_contents($cacheFile), true);
            if (!empty($cached['image']) && is_string($cached['image'])) {
                return $cached['image'];
            }
        }
        return '';
    }

    public static function parseRecipeCardsFromHtml(string $html): array
    {
        $recipes = [];

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);

        $links = $xpath->query("//a[@href]");
        foreach ($links as $a) {
            if (!$a instanceof DOMElement) continue;

            $href = trim($a->getAttribute('href'));
            if ($href === '' || !preg_match('#^https?://#i', $href)) continue;

            $title = trim($a->textContent);
            if ($title === '' || mb_strlen($title) < 3) continue;

            $img = '';
            $imgNode = $xpath->query(".//img", $a)->item(0);
            if ($imgNode instanceof DOMElement) {
                $img = trim($imgNode->getAttribute('src') ?: $imgNode->getAttribute('data-src'));
            }

            $key = md5($href);
            $recipes[$key] = [
                'key'   => $key,
                'title' => $title,
                'image' => $img,
                'url'   => $href,
            ];

            if (count($recipes) >= 20) break;
        }

        return array_values($recipes);
    }

    public static function getFallbackRecipeList(): array
    {
        return [
            ['key' => md5('menemen'), 'title' => 'Menemen', 'image' => '', 'url' => 'https://www.lezizyemeklerim.com/menemen/'],
            ['key' => md5('mercimek-corbasi'), 'title' => 'Mercimek Çorbası', 'image' => '', 'url' => 'https://www.lezizyemeklerim.com/mercimek-corbasi/'],
            ['key' => md5('tavuk-sote'), 'title' => 'Tavuk Sote', 'image' => '', 'url' => 'https://www.lezizyemeklerim.com/tavuk-sote/'],
            ['key' => md5('bulgur-pilavi'), 'title' => 'Bulgur Pilavı', 'image' => '', 'url' => 'https://www.lezizyemeklerim.com/bulgur-pilavi/'],
            ['key' => md5('cacik'), 'title' => 'Cacık', 'image' => '', 'url' => 'https://www.lezizyemeklerim.com/cacik/'],
        ];
    }
}
