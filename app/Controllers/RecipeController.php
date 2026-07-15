<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Helpers\RssTarifler;
use App\Models\RecipeInteractionModel;
use App\Models\RecipeModel;
use App\Models\UserRecipeModel;
use InvalidArgumentException;

final class RecipeController extends Controller
{
    public function book(Request $request): void
    {
        $userId = $this->currentUserId();
        $recipes = (new RecipeModel())->listForUser($userId);

        $this->view('recipes.book', [
            'tarifler'    => $recipes,
            'currentName' => $_SESSION['user_name'] ?? 'Kullanıcı',
        ]);
    }

    public function index(Request $request): void
    {
        $userId = $this->currentUserId();
        $recipes = (new RecipeModel())->listForUser($userId);

        $this->json(['success' => true, 'data' => $recipes], 200);
    }

    public function store(Request $request): void
    {
        $userId = $this->currentUserId();

        $title        = trim((string)$request->input('title', ''));
        $ingredients  = $request->input('ingredients', '');
        $instructions = trim((string)$request->input('instructions', ''));
        $imageUrl     = trim((string)$request->input('image_url', '')) ?: null;
        $source       = trim((string)$request->input('source', '')) ?: null;

        
        $ingredientsText = is_array($ingredients)
            ? implode("\n", array_map('strval', $ingredients))
            : (string)$ingredients;

        try {
            $newId = (new RecipeModel())->create($userId, $title, $ingredientsText, $instructions, $imageUrl, $source);
        } catch (InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            return;
        }

        $this->json(['success' => true, 'message' => 'Tarif kaydedildi.', 'data' => ['id' => $newId]], 201);
    }

    public function destroy(Request $request): void
    {
        $userId = $this->currentUserId();
        $id = (int)$request->param('id');

        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Geçersiz id.'], 400);
            return;
        }

        $deleted = (new RecipeModel())->delete($id, $userId);

        if (!$deleted) {
            $this->json(['success' => false, 'message' => 'Tarif bulunamadı.'], 404);
            return;
        }

        $this->json(['success' => true, 'message' => 'Tarif silindi.', 'data' => []], 200);
    }

    public function community(Request $request): void
    {
        $offset = (int)$request->query('offset', 0);
        $limit  = (int)$request->query('limit', 12);

        $this->json(['success' => true, 'data' => (new RecipeModel())->communityRecipes($offset, $limit)], 200);
    }

    public function proxy(Request $request): void
    {
        $page  = max(1, (int)$request->query('page', 1));
        $query = (string)$request->query('q', '');

        $this->json(['success' => true, 'data' => (new RecipeModel())->fetchRecipesProxy($query, $page)], 200);
    }

    public function detail(Request $request): void
    {
        $url = (string)$request->query('url', '');

        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            $this->json(['success' => false, 'message' => 'Geçersiz URL.'], 400);
            return;
        }

        $this->json(['success' => true, 'data' => (new RecipeModel())->recipeDetail($url)], 200);
    }

    public function aiRecommendations(Request $request): void
    {
        $userId = $this->currentUserId();
        $limit = max(3, min(10, (int)$request->query('limit', 6)));
        $force = (bool)$request->query('force', false);

        $this->json(['success' => true, 'data' => (new RecipeModel())->aiRecommendations($userId, $limit, $force)], 200);
    }

    public function myLikes(Request $request): void
    {
        $userId = $this->currentUserId();
        $this->json(['success' => true, 'data' => (new RecipeInteractionModel())->myLikes($userId)], 200);
    }

    public function myComments(Request $request): void
    {
        $userId = $this->currentUserId();
        $this->json(['success' => true, 'data' => (new RecipeInteractionModel())->myComments($userId)], 200);
    }

    public function editComment(Request $request): void
    {
        $userId = $this->currentUserId();
        $id = (int)$request->param('id');
        $text = trim((string)$request->input('comment_text', ''));

        if ($text === '') {
            $this->json(['success' => false, 'message' => 'Yorum boş olamaz.'], 400);
            return;
        }

        try {
            (new RecipeInteractionModel())->editComment($id, $userId, $text);
        } catch (InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 404);
            return;
        }

        $this->json(['success' => true, 'message' => 'Yorum güncellendi.', 'data' => []], 200);
    }

    public function deleteComment(Request $request): void
    {
        $userId = $this->currentUserId();
        $id = (int)$request->param('id');

        try {
            (new RecipeInteractionModel())->deleteComment($id, $userId);
        } catch (InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 404);
            return;
        }

        $this->json(['success' => true, 'message' => 'Yorum silindi.', 'data' => []], 200);
    }

    public function likePublic(Request $request): void
    {
        $userId = $this->currentUserId();
        $key = (string)$request->input('recipe_key', '');

        if ($key === '') {
            $this->json(['success' => false, 'message' => 'recipe_key boş olamaz.'], 400);
            return;
        }

        $liked = (new RecipeInteractionModel())->toggleLike(
            $userId, $key,
            (string)$request->input('recipe_title', ''),
            (string)$request->input('recipe_image', ''),
            (string)$request->input('recipe_url', '')
        );

        $this->json(['success' => true, 'message' => $liked ? 'Tarif beğenildi.' : 'Beğeni kaldırıldı.', 'data' => ['liked' => $liked]], 200);
    }

    public function savePublic(Request $request): void
    {
        $userId = $this->currentUserId();
        $key = (string)$request->input('recipe_key', '');

        if ($key === '') {
            $this->json(['success' => false, 'message' => 'recipe_key boş olamaz.'], 400);
            return;
        }

        $saved = (new RecipeInteractionModel())->toggleSave(
            $userId, $key,
            (string)$request->input('recipe_title', ''),
            (string)$request->input('recipe_image', ''),
            (string)$request->input('recipe_url', '')
        );

        $this->json(['success' => true, 'message' => $saved ? 'Tarif kaydedildi.' : 'Kayıt kaldırıldı.', 'data' => ['saved' => $saved]], 200);
    }

    public function commentPublic(Request $request): void
    {
        $userId = $this->currentUserId();
        $key = (string)$request->input('recipe_key', '');
        $text = (string)$request->input('comment_text', '');

        if ($key === '') {
            $this->json(['success' => false, 'message' => 'recipe_key boş olamaz.'], 400);
            return;
        }
        if ($text === '') {
            $this->json(['success' => false, 'message' => 'Yorum boş olamaz.'], 400);
            return;
        }

        $newId = (new RecipeInteractionModel())->addComment(
            $userId, $key,
            (string)$request->input('recipe_title', ''),
            (string)$request->input('recipe_image', ''),
            (string)$request->input('recipe_url', ''),
            $text
        );

        $this->json(['success' => true, 'message' => 'Yorum eklendi.', 'data' => ['id' => $newId]], 201);
    }

    public function getPublic(Request $request): void
    {
        $userId = $this->currentUserId();
        $key = (string)$request->query('recipe_key', '');

        if ($key === '') {
            $this->json(['success' => false, 'message' => 'recipe_key boş olamaz.'], 400);
            return;
        }

        $this->json(['success' => true, 'data' => (new RecipeInteractionModel())->getPublic($key, $userId)], 200);
    }

    public function userRecipeIndex(Request $request): void
    {
        $userId = $this->currentUserId();
        $offset = (int)$request->query('offset', 0);
        $limit  = (int)$request->query('limit', 12);

        $this->json(['success' => true, 'data' => (new UserRecipeModel())->listForUser($userId, $offset, $limit)], 200);
    }

    public function userRecipeStore(Request $request): void
    {
        $userId = $this->currentUserId();
        $title = (string)$request->input('title', '');
        $ingredients = trim((string)$request->input('ingredients', ''));
        $instructions = trim((string)$request->input('instructions', ''));
        $category = (string)$request->input('category', '') ?: null;
        $imageUrl = $request->input('image_url');
        $file = $request->file('recipe_image');

        try {
            $result = (new UserRecipeModel())->add($userId, $title, $ingredients, $instructions, $category, $file, $imageUrl);
        } catch (InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            return;
        }

        $this->json(['success' => true, 'message' => 'Tarif eklendi.', 'data' => $result], 201);
    }

    public function userRecipeDestroy(Request $request): void
    {
        $userId = $this->currentUserId();
        $id = (int)$request->param('id');

        (new UserRecipeModel())->delete($id, $userId);
        $this->json(['success' => true, 'message' => 'Tarif silindi.', 'data' => []], 200);
    }

    public function tarifler(Request $request): void
    {
        $kategoriler = RssTarifler::kategoriler();
        $aktifKatRaw = (string)$request->query('kategori', 'hepsi');
        $aktifKat = ($aktifKatRaw === 'kullanici-tarifleri') ? 'kullanici-tarifleri'
            : (isset($kategoriler[$aktifKatRaw]) ? $aktifKatRaw : 'hepsi');
        $aramaMetni = trim((string)$request->query('ara', ''));

        if ((string)$request->query('ajax', '') === '1') {
            $this->tariflerAjaxResponse($aktifKat, $aramaMetni, (int)$request->query('offset', 0));
            return;
        }

        if ((string)$request->query('cache_temizle', '') === '1') {
            foreach (glob(RssTarifler::CACHE_DIR . '*.xml') ?: [] as $f) {
                @unlink($f);
            }
            header('Location: tarifler');
            exit;
        }

        $hata = '';
        $userRecipeModel = new UserRecipeModel();

        if ($aktifKat === 'kullanici-tarifleri') {
            $tarifler = $userRecipeModel->browseFormat(0, 12);
            $totalCards = $userRecipeModel->countAll();
        } elseif (!empty($aramaMetni)) {
            $tarifler = array_slice(RssTarifler::getirTariflerIcinKategori($aktifKat, $aramaMetni), 0, 12);
            $totalCards = count($tarifler);
        } elseif ($aktifKat === 'hepsi') {
            $all = RssTarifler::tumTarifleriCek();
            $tarifler = array_slice($all, 0, 12);
            $totalCards = count($all);
        } else {
            $all = RssTarifler::getirTariflerIcinKategori($aktifKat, '');
            if (empty($all)) {
                $hata = "RSS feed'den tarif alınamadı. Sunucunuzun cURL ile dışa bağlanabildiğinden emin olun.";
            }
            $tarifler = array_slice($all, 0, 12);
            $totalCards = count($all);
        }

        if (!empty($tarifler) && $aktifKat !== 'kullanici-tarifleri') {
            foreach ($tarifler as &$tarif) {
                if (empty($tarif['resim']) && !empty($tarif['url'])) {
                    $tarif['resim'] = RssTarifler::scrapeBestImageFromRecipePage($tarif['url']);
                }
                $tarif['rkey'] = md5($tarif['url']);
            }
            unset($tarif);
        }

        $userId = $this->currentUserId();
        $myLikes = [];
        $mySaves = [];
        if ($userId) {
            $map = (new RecipeInteractionModel())->myLikesAndSavesMap($userId);
            $myLikes = $map['likes'];
            $mySaves = $map['saves'];
        }

        $this->view('recipes.tarifler', [
            'kategoriler'   => $kategoriler,
            'aktifKat'      => $aktifKat,
            'aramaMetni'    => $aramaMetni,
            'hata'          => $hata,
            'tarifler'      => $tarifler,
            'totalCards'    => $totalCards,
            'currentUserId' => $userId ?? 0,
            'isLoggedIn'    => $userId !== null,
            'myLikes'       => $myLikes,
            'mySaves'       => $mySaves,
        ]);
    }

    private function tariflerAjaxResponse(string $aktifKat, string $aramaMetni, int $offset): void
    {
        $offset = max(0, $offset);
        $limit = 12;
        $userRecipeModel = new UserRecipeModel();

        if ($aktifKat === 'kullanici-tarifleri') {
            $total = $userRecipeModel->countAll();
            $items = $userRecipeModel->browseFormat($offset, $limit);
            $this->json(['items' => $items, 'offset' => $offset + count($items), 'total' => $total, 'hasMore' => ($offset + $limit) < $total], 200);
            return;
        }

        $allItems = RssTarifler::getirTariflerIcinKategori($aktifKat, $aramaMetni);
        $pageItems = array_slice($allItems, $offset, $limit);
        $total = count($allItems);

        foreach ($pageItems as &$tarif) {
            if (empty($tarif['resim']) && !empty($tarif['url'])) {
                $tarif['resim'] = RssTarifler::scrapeBestImageFromRecipePage($tarif['url']);
            }
            $tarif['rkey'] = md5($tarif['url']);
        }
        unset($tarif);

        $this->json(['items' => $pageItems, 'offset' => $offset + count($pageItems), 'total' => $total, 'hasMore' => ($offset + $limit) < $total], 200);
    }
}
