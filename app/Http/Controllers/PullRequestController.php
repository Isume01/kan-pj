<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;
use App\Services\GithubService;
use App\Services\GeminiService;

class PullRequestController extends Controller
{
    protected $githubService;

    protected $geminiService;

    public function __construct(GithubService $githubService, GeminiService $geminiService)
    {
        $this->githubService = $githubService;
        $this->geminiService = $geminiService;
    }

    public function index()
    {
        $repositories = Repository::all();
        return view('pull_requests.index', compact('repositories'));
    }

    public function showRepo($owner, $repoName)
    {
        $fullName = "{$owner}/{$repoName}";
        $repositories = Repository::all(); // サイドバー用

        $selectedRepo = Repository::query()
            ->with([
                'codingConvention',
                'pullRequests' => function ($query) {
                    $query->where('is_closed', false)
                          ->with(['aiSummary', 'aiReviews']);
                }
            ])
            ->where('full_name', $fullName)
            ->firstOrFail();

        return view('pull_requests.index', compact('repositories', 'selectedRepo'));
    }
    public function store(Request $request)
    {
        $url = $request->input('repo_url');
        $originalRepoName = str_replace('https://github.com/', '', $url);
        $fullName = str_replace('.git', '', $originalRepoName);

        Repository::firstOrCreate(['full_name' => $fullName]);

        return redirect()->route('repo.index')->with('success', "{$fullName} を登録しました！");
    }

    // リフレッシュボタンの処理
    public function refresh(Repository $repository)
    {
        try {
            // 既存の同期ロジック（GitHubからPR取得など）を実行
            $this->githubService->getPullRequests($repository->full_name);

            $unsummarizedPrs = $repository->pullRequests()
                ->where('is_closed', false)
                ->whereDoesntHave('aiSummary')
                ->get();
            foreach ($unsummarizedPrs as $pr) {
                $this->githubService->summarizePullRequest($pr);
            }
            // AJAXリクエストの場合はJSONを返す
            if (request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sync completed'
                ]);
            }

            // 万が一通常のフォーム送信が残っていても動作するように
            return back()->with('success', '同期しました');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }
}
