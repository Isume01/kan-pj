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
            //0209追記　APIの待ち時間増加
            ->retry(10, 10000)
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

    /**
     * PRの差分をユーザー指定のコーディング規約に基づいてレビューする
     */
    public function reviewWithConvention(string $diffText, string $convention): string
    {
        $prompt = "
            あなたは厳格かつ建設的なシニアソフトウェアエンジニアです。
            提出されたGitHubの差分（diff）を、以下の【コーディング規約】に照らし合わせてレビューしてください。

            【コーディング規約】
            {$convention}

            【レビューのガイドライン】
            1. 規約遵守: 提供された規約に違反している箇所を特定し、修正案を提示してください。
            2. ロジックの維持: アルゴリズムやビジネスロジック自体への変更ではなく、書き方やスタイル、規約に基づく改善に集中してください。
            3. 具体的指摘: 「どのファイルのどのあたり」が「どう悪いか」をMarkdown形式で簡潔に記述してください。

            ---
            【解析対象の差分 (diff)】
            {$diffText}
            ---
            ";

        $model = "gemini-2.5-flash";
        $url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$this->apiKey}";

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(120)
            ->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'topP' => 0.8,
                ]
            ]);

        if ($response->failed()) {
            throw new \Exception("AI Review Error: " . $response->body());
        }

        return $response->json('candidates.0.content.parts.0.text') ?? 'レビュー結果を生成できませんでした。';
    }
}
