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
            ->where('is_closed', true)
            ->orderBy('number', 'desc')
            ->get();

        return view('closed_pull_requests.closed', compact('pullRequests', 'perPage', 'repo'));
    }

    public function show($repo, $number)
    {
        $response = Http::withToken(config('services.github.token'))
            ->get("https://api.github.com/repos/{$repo}/pulls/{$number}/files");

        if ($response->failed()) {
            abort(404);
        }

        $files = $response->json();

        return view('closed_pull_requests.show', compact('files', 'number', 'repo'));
    }
}
