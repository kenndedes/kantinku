<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XoftwarePayService
{
    private $apiKey;
    private $merchantId;
    private $baseUrl;
    private $notifyUrl;

    public function __construct()
    {
        $this->apiKey = config('services.xoftware.api_key');
        $this->merchantId = config('services.xoftware.merchant_id');
        $this->baseUrl = config('services.xoftware.base_url');
        $this->notifyUrl = config('services.xoftware.notify_url');
    }

    private function generateSignature($method, $path, $body = null)
    {
        // Provider timestamp uses UNIX seconds
        $timestamp = (string) time();

        // Body string must be raw JSON (unescaped) or empty string
        $bodyString = ($body === null)
            ? ''
            : (is_string($body) ? $body : json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // Signature message: timestamp\nMETHOD\nPATH\nBODY
        $message = $timestamp . "\n" . strtoupper($method) . "\n" . $path . "\n" . $bodyString;

        // Base64(HMAC_SHA256(message, api_key)) per spec
        $signature = base64_encode(
            hash_hmac('sha256', $message, config('services.xoftware.api_key'), true)
        );

        Log::info('Signature Gen', [
            'timestamp' => $timestamp,
            'path' => $path,
            'signature_preview' => substr($signature, 0, 20),
            'message' => $message,
        ]);

        return [
            'timestamp' => $timestamp,
            'signature' => $signature,
            'bodyString' => $bodyString,
        ];
    }

    private function makeRequest($method, $path, $body = null)
    {
        $url = rtrim($this->baseUrl, '/') . $path;
        // Sign the actual path with /v1/api prefix per docs
        $signaturePath = parse_url($url, PHP_URL_PATH) ?: $path;
        $signatureData = $this->generateSignature(strtoupper($method), $signaturePath, $body);

        $headers = [
            'X-API-Key' => config('services.xoftware.api_key'),
            'X-Timestamp' => $signatureData['timestamp'],
            'X-Signature' => $signatureData['signature'],
            'Content-Type' => 'application/json',
        ];

        Log::info('API Request', ['method' => $method, 'url' => $url]);
        
        try {
            if ($method === 'GET') {
                $response = Http::withHeaders($headers)->get($url);
            } elseif ($method === 'POST') {
                // Use the exact body string that was signed to avoid signature mismatch
                $jsonBody = $signatureData['bodyString'];
                $response = Http::withHeaders($headers)
                    ->withBody($jsonBody, 'application/json')
                    ->post($url);
            } else {
                throw new \Exception("Unsupported HTTP method: $method");
            }
            
            Log::info('API Response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getBalance()
    {
        return $this->makeRequest('POST', '/balance', [
            'merchant_id' => $this->merchantId,
        ]);
    }

    public function createTransaction($merchant_id, $ref_id, $amount, $channel_code, $notify_url)
    {
        $payload = [
            'merchant_id' => (int) $merchant_id,
            'ref_id' => $ref_id,
            'amount' => (int) $amount,
            'channel_code' => $channel_code,
            'expires_in_minutes' => 15,
            'notify_url' => $notify_url,
            'note' => 'Top up saldo user ' . auth()->id(),
            'metadata' => [],
        ];

        Log::info('Xoftware Payload', $payload);

        $response = $this->makeRequest('POST', '/transactions', $payload);
        
        // Response is nested in response['data']
        if (isset($response['data'])) {
            return $response['data'];
        }
        return $response;
    }

    public function getTransactionStatus($ref_id)
    {
        $response = $this->makeRequest('POST', '/transactions/status', [
            'merchant_id' => $this->merchantId,
            'ref_id' => $ref_id,
        ]);
        
        // Response is nested in response['data']
        if (isset($response['data'])) {
            return $response['data'];
        }
        return $response;
    }

    public function cancelTransaction($ref_id)
    {
        $response = $this->makeRequest('POST', '/transactions/cancel', [
            'merchant_id' => $this->merchantId,
            'ref_id' => $ref_id,
        ]);
        
        // Response is nested in response['data']
        if (isset($response['data'])) {
            return $response['data'];
        }
        return $response;
    }

    public function verifyWebhookSignature($payload, $signature)
    {
        $bodyString = json_encode($payload);
        $generatedSignature = hash_hmac('sha256', $bodyString, $this->apiKey);
        
        return hash_equals($generatedSignature, $signature);
    }
}
