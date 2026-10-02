<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use JsonException;
use RuntimeException;

class GeminiService
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
        private readonly string $baseUrl,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            apiKey: config('services.gemini.api_key', ''),
            model: config('services.gemini.model', 'gemini-3.6-flash'),
            baseUrl: rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/'),
        );
    }

    /**
     * Kirim prompt bebas ke Gemini dan kembalikan teks balasan.
     *
     * @throws RuntimeException bila key kosong, HTTP gagal, atau respon tidak valid.
     */
    public function generate(string $prompt): string
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Kunci API Gemini belum diatur. Isi GEMINI_API_KEY di file .env backend.');
        }

        $attempts = 0;
        while (true) {
            $attempts++;

            try {
                $response = Http::timeout(60)
                    ->withHeader('Content-Type', 'application/json')
                    ->withQueryParameters(['key' => $this->apiKey])
                    ->post("{$this->baseUrl}/models/{$this->model}:generateContent", [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]],
                        ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 2048,
                        'responseMimeType' => 'application/json',
                    ],
                    ]);
            } catch (ConnectionException $e) {
                throw new RuntimeException('Gagal terhubung ke Gemini: ' . $e->getMessage());
            }

            if ($response->status() === 503 && $attempts < 3) {
                sleep(5 * $attempts);
                continue;
            }

            if ($response->clientError()) {
                $code = $response->status();
                $message = is_array($response->json('error'))
                    ? ($response->json('error.message') ?? 'Permintaan Gemini ditolak (kode ' . $code . ')')
                    : 'Permintaan Gemini ditolak (kode ' . $code . ')';
                throw new RuntimeException($message);
            }

            if ($response->serverError()) {
                throw new RuntimeException('Layanan Gemini sedang gangguan (kode ' . $response->status() . '). Coba lagi beberapa saat.');
            }

            break;
        }

        $content = $response->json('candidates.0.content.parts.0.text');
        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException('Gemini tidak mengembalikan jawaban yang valid.');
        }

        return $content;
    }

    /**
     * Minta Gemini menghasilkan JSON dan kembalikan sebagai array PHP.
     *
     * Konten dibersihkan dari fenced code block (```json ... ```) bila ada.
     *
     * @throws RuntimeException bila JSON tidak bisa di-decode.
     */
    public function generateJson(string $prompt): array
    {
        $text = trim($this->generate($prompt));

        $decoded = $this->decodeJsonCandidate($text);
        if ($decoded !== null) {
            return $decoded;
        }

        // Kalau ada teks di sekitar JSON (markdown/prosa), ambil blok { ... }
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $decoded = $this->decodeJsonCandidate($matches[0]);
            if ($decoded !== null) {
                return $decoded;
            }
        }

        \Illuminate\Support\Facades\Log::error('Gemini JSON Decode Failed', ['raw' => $text]);
        throw new RuntimeException("Format tidak dipahami. AI merespons tidak standar. Coba lagi.");
    }

    private function decodeJsonCandidate(string $text): ?array
    {
        $json = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $json = preg_replace('/\s*```$/', '', $json);
        $json = trim($json);

        // Fix trailing commas in JSON which causes PHP json_decode to fail
        $json = preg_replace('/,\s*([\]}])/m', '$1', $json);

        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            if (is_array($decoded)) {
                return $decoded;
            }
        } catch (JsonException $e) {
            // Abaikan error, kembalikan null
        }
        return null;
    }
}