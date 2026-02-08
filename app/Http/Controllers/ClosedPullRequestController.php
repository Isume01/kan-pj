<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;
use App\Services\GithubService;
use Illuminate\Support\Facades\Http;

class ClosedPullRequestController extends Controller
{
    protected $githubService;

    public function __construct(GithubService $githubService)
    {
        $this->githubService = $githubService;
    }

    public function indexClosed(Request $request, $repo)
    {
        $perPage = $request->input('per_page', 10);
        $this->githubService->getClosedPullRequests($repo, $perPage);

        $repository = Repository::where('full_name', $repo)->firstOrFail();
        $pullRequests = $repository->pullRequests()
            ->orderBy('number', 'desc')
            ->take($perPage)
            ->get();

        return view('closed_pull_requests.closed', compact('pullRequests', 'perPage', 'repo'));
    }

    public function show(Request $request, $repo, $number)
    {
        $response = Http::withToken(config('services.github.token'))
            ->get("https://api.github.com/repos/{$repo}/pulls/{$number}/files");

        if ($response->failed()) {
            return $request->ajax() ? response('Error', 404) : abort(404);
        }

        $files = $response->json();

        // Ajaxリクエストの場合は、パーツ用のViewを返す
        if ($request->ajax()) {
            return view('closed_pull_requests.parts.partial_diff', compact('files', 'number', 'repo'))->render();
        }
    }
}
