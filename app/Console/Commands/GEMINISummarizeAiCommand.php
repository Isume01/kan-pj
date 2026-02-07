<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Repository;
use App\Models\PullRequest;
use App\Services\GithubService;

class GEMINISummarizeAiCommand extends Command
{
    protected $signature = 'ai:summarize {repo : (repo name)}';

    protected $description = '指定したリポジトリの未要約プルリクエストをAIで要約します';

    public function handle(GithubService $githubService)
    {
        $repoName = $this->argument('repo');

        // DBからリポジトリを取得
        $repository = Repository::where('full_name', $repoName)->first();

        if (!$repository) {
            $this->error("リポジトリ {$repoName} が存在しない。");
            return Command::FAILURE;
        }

        // 要約がまだ存在しないPRを抽出
        $unsummarizedPrs = $repository->pullRequests()
            ->whereDoesntHave('aiSummary')
            ->get();

        if ($unsummarizedPrs->isEmpty()) {
            $this->info("該当リポジトリ　$repoName} は要約済みです。");
            return Command::SUCCESS;
        }

        $this->info("{$unsummarizedPrs->count()} 件要約中。");
        $this->output->progressStart($unsummarizedPrs->count());

        foreach ($unsummarizedPrs as $pr) {
            try {
                $githubService->summarizePullRequest($pr);
                $this->output->progressAdvance();
                sleep(5);
            } catch (\Exception $e) {
                $this->newLine();
                $this->error($e->getMessage());
            }
        }

        $this->output->progressFinish();
        $this->info("要約成功です。");

        return Command::SUCCESS;
    }
}
