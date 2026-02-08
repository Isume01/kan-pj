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

        $pullRequestsData = $response->json();

        if (empty($pullRequestsData)) {
            return 0;
        }

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
                    'is_closed'    => false,
                ]
            );
        }

        return count($prs);
    }

    /**
     * クローズ済みのもの最新PRを取得してDBに保存する
     */
    public function getClosedPullRequests(string $fullName, int $perPage = 10)
    {
        $repository = Repository::firstOrCreate(['full_name' => $fullName]);

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withToken($this->token)
            ->get("https://api.github.com/repos/{$fullName}/pulls", [
                'state'    => 'closed', // クローズ済みを指定
                'per_page' => $perPage,  // 件数を動的に変更
            ]);

        if ($response->failed()) {
            throw new \Exception("GitHub API Error: " . $response->body());
        }

        $prs = $response->json();

        if (empty($prs)) {
            return 0;
        }

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
                    'is_closed'   => true,
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

    /**
     * DBに保存されたURLを使用して、GitHubから差分（diff）の生テキストを取得する
     */
    public function getPullRequestDiff(string $repoName, int $number): string
    {
        $repository = Repository::where('full_name', $repoName)->firstOrFail();

        $pr = $repository->pullRequests()
            ->where('number', $number)
            ->firstOrFail();

        $diffUrl = $pr->diff_url;

        if (!$diffUrl) {
            throw new \Exception("DBにdiff_urlが保存されていません。同期を先に行う必要があります。");
        }

        $response = Http::withToken($this->token)->get($diffUrl);

        if ($response->failed()) {
            throw new \Exception("GitHubから差分のフェッチに失敗しました。URL: {$diffUrl}");
        }

        return $response->body();
    }
}
