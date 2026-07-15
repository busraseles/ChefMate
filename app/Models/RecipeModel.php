<?php

namespace App\Models;

use App\Core\Database;
use PDO;

final class RecipeModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function listForUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, title, ingredients, instructions, image_url, source, saved_at
            FROM saved_recipes
            WHERE user_id = ?
            ORDER BY saved_at DESC
        ");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $lines = array_values(array_filter(
                array_map('trim', explode("\n", (string)($row['ingredients'] ?? ''))),
                static fn($line) => $line !== ''
            ));
            $row['ingredients_array'] = $lines;
            $row['ingredients_text']  = implode("\n", $lines);
        }
        unset($row);

        return $rows;
    }

    public function create(int $userId, string $title, string $ingredientsText, string $instructions, ?string $imageUrl, ?string $source): int
    {
        if (trim($title) === '') {
            throw new \InvalidArgumentException('Tarif adı boş olamaz.');
        }

        $check = $this->db->prepare('SELECT id FROM saved_recipes WHERE user_id = ? AND title = ? LIMIT 1');
        $check->execute([$userId, $title]);

        if ($check->fetch()) {
            throw new \InvalidArgumentException('Bu tarif zaten kaydedilmiş.');
        }

        $stmt = $this->db->prepare("
            INSERT INTO saved_recipes (user_id, title, ingredients, instructions, image_url, source)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $title, $ingredientsText, $instructions, $imageUrl, $source]);

        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM saved_recipes WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);

        return $stmt->rowCount() > 0;
    }

    public function communityRecipes(int $offset, int $limit): array
    {
        $offset = max(0, $offset);
        $limit  = min(24, max(1, $limit));

        $total = (int)$this->db->query('SELECT COUNT(*) FROM saved_recipes')->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT sr.id, sr.title, sr.ingredients, sr.instructions, sr.image_url, sr.source,
                   DATE_FORMAT(sr.saved_at, '%d.%m.%Y') AS saved_at,
                   u.name AS user_name
            FROM saved_recipes sr
            LEFT JOIN users u ON u.id = sr.user_id
            ORDER BY sr.saved_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        foreach ($rows as &$r) {
            $arr = \App\Helpers\RecipeScraper::flattenIngredients($r['ingredients'] ?? '');
            $r['ingredients_array']   = $arr;
            $r['ingredients_text']    = implode("\n", $arr);
            $r['ingredients_preview'] = mb_substr(implode(', ', $arr), 0, 120, 'UTF-8');
        }
        unset($r);

        return [
            'items'   => $rows,
            'offset'  => $offset + count($rows),
            'total'   => $total,
            'hasMore' => ($offset + $limit) < $total,
        ];
    }

    public function fetchRecipesProxy(string $query, int $page): array
    {
        $url = $query !== ''
            ? 'https://www.nefisyemektarifleri.com/arama/?q=' . urlencode($query) . "&sayfa=$page"
            : "https://www.nefisyemektarifleri.com/yemek-tarifleri/?sayfa=$page";

        $html = \App\Helpers\RecipeScraper::fetchUrl($url, 10);

        if ($html === '') {
            return ['recipes' => \App\Helpers\RecipeScraper::getFallbackRecipeList(), 'source' => 'fallback'];
        }

        $recipes = \App\Helpers\RecipeScraper::parseRecipeCardsFromHtml($html);
        if (empty($recipes)) {
            return ['recipes' => \App\Helpers\RecipeScraper::getFallbackRecipeList(), 'source' => 'fallback'];
        }

        return ['recipes' => $recipes, 'source' => 'remote'];
    }

    public function recipeDetail(string $url): array
    {
        $rawIngredients = \App\Helpers\RecipeScraper::scrapeRecipeIngredientsOnly($url);
        $image = \App\Helpers\RecipeScraper::getRecipePageImage($url);

        $displayIngredients = [];
        foreach ($rawIngredients as $raw) {
            $d = \App\Helpers\RecipeScraper::displayName($raw);
            if ($d !== '') {
                $displayIngredients[] = $d;
            }
        }
        $displayIngredients = array_values(array_unique($displayIngredients));

        $title = '';
        $steps = [];
        $html = \App\Helpers\RecipeScraper::fetchUrl($url, 12);

        if ($html !== '') {
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            $h1 = $xpath->query('//h1')->item(0);
            if ($h1) {
                $title = trim($h1->textContent);
            }
            if ($title === '') {
                $tn = $xpath->query('//title')->item(0);
                if ($tn) {
                    $title = trim($tn->textContent);
                }
            }

            foreach ($xpath->query("//script[@type='application/ld+json']") as $script) {
                $jld = json_decode($script->textContent, true);
                if (!$jld) continue;
                $graphs = $jld['@graph'] ?? [$jld];
                foreach ($graphs as $g) {
                    if (empty($g['recipeInstructions'])) continue;
                    foreach ($g['recipeInstructions'] as $step) {
                        $t = is_string($step) ? $step : (string)($step['text'] ?? $step['name'] ?? '');
                        $t = trim(strip_tags($t));
                        if ($t !== '') {
                            $steps[] = $t;
                        }
                    }
                    if (!empty($steps)) break 2;
                }
            }
        }

        return [
            'title' => $title, 'url' => $url, 'img' => $image,
            'ingredients' => $displayIngredients, 'steps' => $steps,
            'desc' => '', 'time' => '', 'serving' => '',
        ];
    }

    public function aiRecommendations(int $userId, int $limit, bool $force): array
    {
        $stmt = $this->db->prepare('SELECT name, expiry_date FROM fridge_items WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        $userRows = $stmt->fetchAll();

        if (empty($userRows)) {
            return [
                'recommendations' => [], 'total_user_ingredients' => 0,
                'total_recipes_evaluated' => 0, 'matched_any' => 0,
                'message' => 'Buzdolabınız boş. Önce malzeme ekleyin.',
            ];
        }

        $userTokens = [];
        $sktTokens  = [];
        $today = new \DateTime('today');

        foreach ($userRows as $row) {
            $name = trim((string)($row['name'] ?? ''));
            if ($name === '') continue;

            $token = \App\Helpers\RecipeScraper::aiNormalizeIngredient($name);
            if ($token === '') continue;

            $userTokens[$token] = $name;

            $expiry = trim((string)($row['expiry_date'] ?? ''));
            if ($expiry !== '') {
                try {
                    $diff = (int)$today->diff(new \DateTime($expiry))->format('%r%a');
                    if ($diff >= 0 && $diff <= 3) {
                        $sktTokens[$token] = true;
                    }
                } catch (\Throwable) {
                }
            }
        }

        $listDir = dirname(__DIR__, 2) . '/cache/ai_list/';
        \App\Helpers\RecipeScraper::ensureDir($listDir);
        $listFile = $listDir . 'recipe_list.json';
        $recipeList = [];

        if (!$force && file_exists($listFile) && (time() - filemtime($listFile)) < 3600) {
            $recipeList = json_decode((string)file_get_contents($listFile), true) ?: [];
        }

        if ($force) {
            $ingCacheDir = dirname(__DIR__, 2) . '/cache/ai_ing/';
            if (is_dir($ingCacheDir)) {
                foreach (glob($ingCacheDir . '*.json') ?: [] as $cf) {
                    @unlink($cf);
                }
            }
        }

        if (empty($recipeList)) {
            $rssUrls = [
                'https://www.lezizyemeklerim.com/rss/yemek-tarifleri/sebze-yemekleri',
                'https://www.lezizyemeklerim.com/rss/yemek-tarifleri/et-yemekleri',
                'https://www.lezizyemeklerim.com/rss/yemek-tarifleri/corba-tarifleri',
                'https://www.lezizyemeklerim.com/rss/yemek-tarifleri/kahvaltilik-tarifler',
                'https://www.lezizyemeklerim.com/rss/yemek-tarifleri/hamurisi-tarifleri',
            ];

            foreach ($rssUrls as $rssUrl) {
                $xml = \App\Helpers\RecipeScraper::fetchUrl($rssUrl, 10);
                if ($xml === '') continue;

                libxml_use_internal_errors(true);
                $feed = simplexml_load_string($xml);
                if (!$feed) continue;

                $count = 0;
                foreach ($feed->channel->item ?? [] as $item) {
                    $title = trim(html_entity_decode((string)$item->title, ENT_QUOTES, 'UTF-8'));
                    $link  = trim((string)$item->link);
                    if ($title === '' || $link === '') continue;

                    $img = '';
                    foreach ($item->enclosure as $enc) {
                        $img = (string)($enc['url'] ?? '');
                        if ($img !== '') break;
                    }

                    if (!$img) {
                        $descHtml = (string)($item->description ?? '');
                        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $descHtml, $m)) {
                            $img = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
                        }
                    }

                    $recipeList[] = ['key' => md5($link), 'title' => $title, 'image' => $img, 'url' => $link];

                    $count++;
                    if ($count >= 8) break;
                }
            }

            if (empty($recipeList)) {
                $recipeList = \App\Helpers\RecipeScraper::getFallbackRecipeList();
            }

            $seen = [];
            $unique = [];
            foreach ($recipeList as $r) {
                if (!isset($seen[$r['key']])) {
                    $seen[$r['key']] = true;
                    $unique[] = $r;
                }
            }
            $recipeList = $unique;

            file_put_contents($listFile, json_encode($recipeList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $scored = [];

        foreach ($recipeList as $recipe) {
            $rawIngredients = \App\Helpers\RecipeScraper::scrapeRecipeIngredientsOnly($recipe['url']);
            if (empty($rawIngredients)) continue;

            $recipeItems = [];
            foreach ($rawIngredients as $rawIng) {
                $tok = \App\Helpers\RecipeScraper::aiNormalizeIngredient($rawIng);
                $display = \App\Helpers\RecipeScraper::displayName($rawIng);
                if ($tok === '' || $display === '') continue;
                if (!isset($recipeItems[$tok])) {
                    $recipeItems[$tok] = ['display' => $display];
                }
            }

            $total = count($recipeItems);
            if ($total === 0) continue;

            $matched = [];
            $missing = [];

            foreach ($recipeItems as $tok => $item) {
                $hit = false;
                $hitName = '';

                if (isset($userTokens[$tok])) {
                    $hit = true;
                    $hitName = $userTokens[$tok];
                } else {
                    $tokLen = mb_strlen($tok, 'UTF-8');
                    foreach ($userTokens as $uTok => $uRaw) {
                        $uLen = mb_strlen($uTok, 'UTF-8');
                        if ($tokLen < 5 || $uLen < 5) continue;
                        if (max($tokLen, $uLen) > min($tokLen, $uLen) * 2) continue;
                        if (mb_strpos($tok, $uTok, 0, 'UTF-8') !== false || mb_strpos($uTok, $tok, 0, 'UTF-8') !== false) {
                            $hit = true;
                            $hitName = $uRaw;
                            break;
                        }
                    }
                }

                if ($hit) {
                    $matched[] = $hitName !== '' ? $hitName : $item['display'];
                } else {
                    $missing[] = $item['display'];
                }
            }

            $matchCount = count(array_unique($matched));
            $matchPct = $total > 0 ? (int)round(($matchCount / $total) * 100) : 0;

            $sktBonus = 0;
            foreach (array_keys($recipeItems) as $tok) {
                if (isset($sktTokens[$tok])) $sktBonus += 12;
            }

            $image = trim((string)($recipe['image'] ?? ''));
            if ($image === '') {
                $image = \App\Helpers\RecipeScraper::getRecipePageImage($recipe['url']);
            }

            $scored[] = [
                'key' => $recipe['key'], 'title' => $recipe['title'], 'image' => $image, 'url' => $recipe['url'],
                'match_count' => $matchCount, 'total_required' => $total,
                'missing_count' => count(array_unique($missing)), 'match_pct' => $matchPct,
                'score' => $matchPct + $sktBonus,
                'matched' => array_values(array_unique($matched)),
                'missing' => array_values(array_unique($missing)),
                'has_skt_bonus' => $sktBonus > 0,
            ];
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

        $results = array_values(array_filter($scored, fn($r) => ($r['match_count'] ?? 0) > 0));

        return [
            'recommendations' => array_slice($results, 0, $limit),
            'total_user_ingredients' => count($userTokens),
            'total_recipes_evaluated' => count($scored),
            'matched_any' => count($results),
        ];
    }
}
