<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\FridgeModel;
use App\Models\ShoppingModel;
use App\Models\WasteModel;
use InvalidArgumentException;

final class FridgeController extends Controller
{
    public function index(Request $request): void
    {
        $userId = $this->currentUserId(); 
        $items = (new FridgeModel())->listForUser($userId);

        $this->json(['success' => true, 'data' => $items], 200);
    }

    public function store(Request $request): void
    {
        $userId = $this->currentUserId();

        try {
            $newId = (new FridgeModel())->create($userId, $request->all());
        } catch (InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            return;
        }

        $this->json(['success' => true, 'message' => 'Ürün eklendi.', 'data' => ['id' => $newId]], 201);
    }

    public function destroy(Request $request): void
    {
        $userId = $this->currentUserId();
        $id = (int)$request->param('id');

        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Geçersiz id.'], 400);
            return;
        }

        $deleted = (new FridgeModel())->delete($id, $userId);

        if (!$deleted) {

            $this->json(['success' => false, 'message' => 'Ürün bulunamadı.'], 404);
            return;
        }

        $this->json(['success' => true, 'message' => 'Ürün silindi.', 'data' => []], 200);
    }

    public function update(Request $request): void
    {
        $userId = $this->currentUserId();
        $id = (int)$request->param('id');

        $updated = (new FridgeModel())->update($id, $userId, $request->all());

        if (!$updated) {
            $this->json(['success' => false, 'message' => 'Güncellenecek geçerli alan yok.'], 400);
            return;
        }

        $this->json(['success' => true, 'message' => 'Ürün güncellendi.', 'data' => []], 200);
    }

    public function shoppingIndex(Request $request): void
    {
        $userId = $this->currentUserId();
        $this->json(['success' => true, 'data' => (new ShoppingModel())->listForUser($userId)], 200);
    }

    public function shoppingStore(Request $request): void
    {
        $userId = $this->currentUserId();
        $name = (string)$request->input('name', '');
        $qty  = $request->input('quantity');

        try {
            $newId = (new ShoppingModel())->add($userId, $name, $qty !== null && $qty !== '' ? (string)$qty : null);
        } catch (InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            return;
        }

        $this->json(['success' => true, 'message' => 'Ürün eklendi.', 'data' => ['id' => $newId]], 201);
    }

    public function shoppingToggle(Request $request): void
    {
        $userId = $this->currentUserId();
        $id = (int)$request->param('id');

        (new ShoppingModel())->toggle($id, $userId);
        $this->json(['success' => true, 'message' => 'Durum değiştirildi.', 'data' => []], 200);
    }

    public function shoppingDestroy(Request $request): void
    {
        $userId = $this->currentUserId();
        $id = (int)$request->param('id');

        (new ShoppingModel())->delete($id, $userId);
        $this->json(['success' => true, 'message' => 'Ürün silindi.', 'data' => []], 200);
    }

    public function wasteIndex(Request $request): void
    {
        $userId = $this->currentUserId();
        $this->json(['success' => true, 'data' => (new WasteModel())->listForUser($userId)], 200);
    }

    public function wasteStore(Request $request): void
    {
        $userId = $this->currentUserId();
        $item = (string)$request->input('item_name', '');
        $amount = $request->input('amount');
        $reason = $request->input('reason');

        try {
            (new WasteModel())->add($userId, $item, $amount ?: null, $reason ?: null);
        } catch (InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            return;
        }

        $badgeAwarded = (new \App\Models\BadgeModel())->earnNamed($userId, 'waste_hero', 'Atık Kahramanı');
        $this->json(['success' => true, 'message' => 'Atık kaydedildi.', 'data' => ['badge_awarded' => $badgeAwarded]], 201);
    }
}
