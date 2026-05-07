<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class IndexingApiService
{
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const PUBLISH_URL = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
    private const SCOPE = 'https://www.googleapis.com/auth/indexing';
    private const TOKEN_CACHE_KEY = 'google_indexing_api_access_token';

    public function notifyUpdate(string $url): array
    {
        return $this->publish($url, 'URL_UPDATED');
    }

    public function notifyDelete(string $url): array
    {
        return $this->publish($url, 'URL_DELETED');
    }

    private function publish(string $url, string $type): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->post(self::PUBLISH_URL, [
                'url'  => $url,
                'type' => $type,
            ]);

        return [
            'status' => $response->status(),
            'body'   => $response->body(),
            'ok'     => $response->successful(),
        ];
    }

    private function getAccessToken(): string
    {
        $cached = Cache::get(self::TOKEN_CACHE_KEY);
        if ($cached) {
            return $cached;
        }

        $sa = $this->loadServiceAccount();
        $jwt = $this->buildSignedJwt($sa);

        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('Failed to fetch Google Indexing API access token: ' . $response->body());
        }

        $data = $response->json();
        $token = $data['access_token'] ?? null;
        $expiresIn = (int) ($data['expires_in'] ?? 3600);

        if (!$token) {
            throw new RuntimeException('Access token missing in OAuth response.');
        }

        Cache::put(self::TOKEN_CACHE_KEY, $token, now()->addSeconds(max(60, $expiresIn - 60)));

        return $token;
    }

    private function loadServiceAccount(): array
    {
        $path = config('services.google_indexing.service_account_json');
        if (!$path || !is_file($path)) {
            throw new RuntimeException(
                'GOOGLE_INDEXING_SERVICE_ACCOUNT_JSON is not set or file does not exist: ' . ($path ?: '(empty)')
            );
        }

        $json = json_decode((string) file_get_contents($path), true);
        if (!is_array($json) || empty($json['client_email']) || empty($json['private_key'])) {
            throw new RuntimeException('Service account JSON is invalid (client_email/private_key missing).');
        }

        return $json;
    }

    private function buildSignedJwt(array $sa): string
    {
        $now = time();
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claims = [
            'iss'   => $sa['client_email'],
            'scope' => self::SCOPE,
            'aud'   => self::TOKEN_URL,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES)),
            $this->base64UrlEncode(json_encode($claims, JSON_UNESCAPED_SLASHES)),
        ];
        $signingInput = implode('.', $segments);

        $signature = '';
        $ok = openssl_sign($signingInput, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256);
        if (!$ok) {
            throw new RuntimeException('Failed to sign JWT with service account private key.');
        }

        $segments[] = $this->base64UrlEncode($signature);
        return implode('.', $segments);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
