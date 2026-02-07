<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class GeminiService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * PRの差分テキストをAIに投げて要約を受け取る
     */
    public function summarizeDiff(string $diffText): string
    {
        $prompt = "
            あなたはシニアソフトウェアエンジニアです。

            以下のGitHubのプルリクエストの差分（diff）を解析し、

            1. 変更の要約（2行程度）
            2. 修正による影響範囲
            3. 懸念点（もしあれば）

            を日本語で簡潔に報告してください。

            ---
            {$diffText}
            ---
            ";

        // Gemini API へのリクエスト
        $model = "gemini-2.5-flash";
        $url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$this->apiKey}";
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

        /** @var Response $response */
        if ($response->failed()) {
            throw new \Exception("AI API Error: " . $response->body());
        }

        return $response->json('candidates.0.content.parts.0.text');
    }
}
