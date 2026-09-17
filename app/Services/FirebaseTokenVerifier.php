<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FirebaseTokenVerifier
{
    private string $projectId;

    public function __construct()
    {
        $this->projectId = (string) config('services.firebase.project_id', '');
    }

    /** Verifikasi ID token Firebase (firebase_auth). Mengembalikan payload claims atau null jika tidak valid. */
    public function verify(string $idToken): ?array
    {
        $parts = explode('.', $idToken);
        if (count($parts) !== 3) {
            return null;
        }

        $header = $this->jsonDecodeSegment($parts[0]);
        $payload = $this->jsonDecodeSegment($parts[1]);
        $signature = $this->urlBase64Decode($parts[2]);

        if ($header === null || $payload === null || $signature === null) {
            return null;
        }

        if (($payload['iss'] ?? null) !== "https://securetoken.google.com/{$this->projectId}") {
            return null;
        }

        if (($payload['aud'] ?? null) !== $this->projectId) {
            return null;
        }

        if (empty($payload['sub'])) {
            return null;
        }

        if (($payload['exp'] ?? 0) < time()) {
            return null;
        }

        $kid = $header['kid'] ?? null;
        $certificates = $this->certificates();
        if ($kid === null || !isset($certificates[$kid])) {
            return null;
        }

        $verified = openssl_verify(
            $parts[0].'.'.$parts[1],
            $signature,
            $certificates[$kid],
            'SHA256',
        );

        if ($verified !== 1) {
            return null;
        }

        return $payload;
    }

    private function jsonDecodeSegment(string $segment): ?array
    {
        $decoded = $this->urlBase64Decode($segment);
        if ($decoded === null) {
            return null;
        }
        $json = json_decode($decoded, true);
        return is_array($json) ? $json : null;
    }

    private function urlBase64Decode(string $segment): ?string
    {
        $remainder = strlen($segment) % 4;
        if ($remainder > 0) {
            $segment .= str_repeat('=', 4 - $remainder);
        }
        $decoded = base64_decode(strtr($segment, '-_', '+/'), true);
        return $decoded === false ? null : $decoded;
    }

    private function certificates(): array
    {
        return Cache::remember('firebase_x509_certs', 3600, function () {
            $response = Http::timeout(10)->get(
                'https://www.googleapis.com/service_accounts/v1/metadata/x509/securetoken@system.gserviceaccount.com',
            );

            if (! $response->successful()) {
                return [];
            }

            $certs = $response->json();
            return is_array($certs) ? $certs : [];
        });
    }
}