<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use App\Services\GeminiService;
use App\Models\Repository;
use App\Models\PullRequest;

class GithubService
{
    protected $token;

    public function __construct() {
        $this->token = config('services.github.token');
    }

    /**
     * 特定のリポジトリの最新PRを取得してDBに保存する
     */
    public function getPullRequests(string $fullName) {
        // DBにリポジトリを登録・取得
        $repository = Repository::firstOrCreate(['full_name' => $fullName]);

        /** @var Response $response */
        $response = Http::withToken($this->token)
            ->get("https://api.github.com/repos/{$fullName}/pulls", [
                'state' => 'open',
                'per_page' => 10,
            ]);

        if ($response->failed()) {
            throw new \Exception("GitHub API Error: " . $response->body());
        }

        $prs = $response->json();

        // 取得したデータをDBに保存
        foreach ($prs as $prData) {
            PullRequest::updateOrCreate(
                ['github_pr_id' => $prData['id']],
                [
                    'repository_id' => $repository->id,
                    'number'      => $prData['number'],
                    'title'       => $prData['title'],
                    'body'        => $prData['body'],
                    'user_login'  => $prData['user']['login'],
                    'state'       => $prData['state'],
                    'diff_url'    => $prData['diff_url'],
                    'html_url'    => $prData['html_url'],
                ]
            );
        }

        return count($prs);
    }

    /**
     * Gemini呼び出し、PRを要約する。
     */
    public function summarizePullRequest(PullRequest $pr)
        {
        // diff_url から生の差分テキストを取得
        $diffResponse = Http::get($pr->diff_url);

        if ($diffResponse->failed()) return;

        $diffText = $diffResponse->body();

        // AIサービスを呼び出し
        $aiService = app(GeminiService::class);
        $summary = $aiService->summarizeDiff($diffText);

        // モデルに保存
        $pr->aiSummary()->create([
            'summary' => $summary,
            'model_name' => 'gemini-2.5-flash',
        ]);
    }
}
