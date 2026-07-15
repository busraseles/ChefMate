<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\FlaskClient;
use App\Core\Request;

final class PredictionController extends Controller
{
    public function store(Request $request): void
    {
        $file = $request->file('image');

        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'Geçerli bir resim dosyası gönderilmedi.'], 400);
            return;
        }

        $result = (new FlaskClient())->predict($file['tmp_name'], $file['name'] ?? null);

        if (empty($result['success'])) {

            
            $this->json([
                'success' => false,
                'message' => $result['message'] ?? 'Tahmin başarısız oldu.',
            ], 502);
            return;
        }

        $this->json([
            'success'    => true,
            'food'       => $result['food'] ?? null,
            'confidence' => $result['confidence'] ?? null,
        ], 200);
    }
}
