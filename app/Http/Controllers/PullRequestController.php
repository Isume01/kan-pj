<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;
use App\Services\GithubService;

class PullRequestController extends Controller
{
    public function index()
    {
        $repositories = Repository::with(['pullRequests.aiSummary'])->get();

        return view('pull_requests.index', compact('repositories'));
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
    public function refresh(Repository $repository, GithubService $githubService)
    {
        // 1. GitHubから最新のPRを取得
        $githubService->getPullRequests($repository->full_name);

        // 2. 未要約のPRをAIで要約（1件ずつ sleep を入れるため少し時間がかかります）
        $unsummarizedPrs = $repository->pullRequests()->whereDoesntHave('aiSummary')->get();
        foreach ($unsummarizedPrs as $pr) {
            $githubService->summarizePullRequest($pr);
        }

        return redirect()->route('repo.index')->with('success', "最新の要約を生成しました。");
    }
}
