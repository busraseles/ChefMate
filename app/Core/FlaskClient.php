<?php

namespace App\Core;

use CURLFile;

final class FlaskClient
{
    private string $baseUrl;
    private int $timeout;
    private int $connectTimeout = 3;

    public function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $this->baseUrl = rtrim($config['flask']['base_url'], '/');
        $this->timeout = (int)$config['flask']['timeout'];
    }

    public function predict(string $filePath, ?string $originalFilename = null): array
    {
        if (!is_file($filePath)) {
            return ['success' => false, 'message' => 'Gönderilecek resim dosyası sunucuda bulunamadı.'];
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        $sendName = $originalFilename ?: basename($filePath);
        $curlFile = new CURLFile($filePath, $mimeType, $sendName);

        $ch = curl_init($this->baseUrl . '/predict');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => ['image' => $curlFile],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
        ]);

        $response = curl_exec($ch);
        $curlErrNo = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        
        if ($curlErrNo !== 0) {
            return [
                'success' => false,
                'message' => 'Malzeme tanıma servisine şu anda ulaşılamıyor. Lütfen daha sonra tekrar deneyin.',
                'debug'   => $curlError, 
            ];
        }

        $data = json_decode((string)$response, true);

        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Malzeme tanıma servisinden geçersiz bir yanıt alındı.'];
        }

        return $data;
    }

    public function isHealthy(): bool
    {
        $ch = curl_init($this->baseUrl . '/health');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 2,
            CURLOPT_CONNECTTIMEOUT => 2,
        ]);
        curl_exec($ch);
        $ok = curl_errno($ch) === 0;
        curl_close($ch);

        return $ok;
    }
}
