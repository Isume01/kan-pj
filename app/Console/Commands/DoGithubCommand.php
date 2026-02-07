<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GithubService;
use Exception;

class DoGithubCommand extends Command
{
    /**
     * コマンドの呼び出し名
     */
    protected $signature = 'github:sync {repo : (repo name)}';

    /**
     * コマンドの説明
     */
    protected $description = 'GitHubから特定のPRを取得し、データベースに同期します';

    /**
     * コマンドの実行ロジック
     */
    public function handle(GithubService $service)
    {
        $repoName = $this->argument('repo');

        // バリデーション: owner/repo の形式かチェック
        if (!str_contains($repoName, '/')) {
            return Command::FAILURE;
        }

        $this->info("--------------------------------------------------");
        $this->info("以下リポジトリと同期する: {$repoName}");
        $this->info("Connecting to GitHub API...");
        $this->info("--------------------------------------------------");

        try {
            // Serviceのメソッドを呼び出し
            $count = $service->getPullRequests($repoName);

            if ($count === 0) {
                $this->warn("コネクタ成功。プルリクエストがありません。");
            } else {
                $this->info("成功! {$count} 件のプルリクエストの結果が保存されました。");
            }

            return Command::SUCCESS;

        } catch (Exception $e) {
            // エラーが発生した場合の処理
            $this->newLine();
            $this->error("エラー");
            $this->error("Message: " . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
