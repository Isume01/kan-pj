<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repository;
use App\Services\GeminiService;
use App\Services\GithubService;

class AiReviewController extends Controller
{

    protected $githubService;

    protected $geminiService;

    public function __construct(GithubService $githubService, GeminiService $geminiService)
    {
        $this->githubService = $githubService;
        $this->geminiService = $geminiService;
    }

    public function storeConvention(Request $request, $repoName)
    {
        $repo = Repository::where('full_name', $repoName)->firstOrFail();

        // 規約を保存または更新 (updateOrCreate)
        $repo->codingConvention()->updateOrCreate(
            ['repository_id' => $repo->id],
            [
                'name' => 'Default Convention',
                'content' => $request->input('content'),
                'is_active' => true,
            ]
        );

        return back()->with('status', '規約を保存しました！');
    }

    public function executeReview(Request $request, $repoName, $number)
    {
        $repo = Repository::where('full_name', $repoName)->firstOrFail();
        $convention = $repo->codingConvention->content; // 先ほど保存した規約を取得

        // GitHubからPRの差分を取得 (GitHubService経由)
        $diffText = $this->githubService->getPullRequestDiff($repoName, $number);

        // Geminiにレビュー依頼
        $reviewResult = $this->geminiService->reviewWithConvention($diffText, $convention);

        // DBに保存
        $repo->pullRequests()->where('number', $number)->first()->aiReviews()->create([
            'coding_convention_id' => $repo->codingConvention->id,
            'review_result' => $reviewResult,
            'status' => 'completed'
        ]);

        return back()->with('success', 'AIレビューが完了しました！');
    }
}
