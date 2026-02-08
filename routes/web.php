<?php


use App\Http\Controllers\PullRequestController;
use App\Http\Controllers\ClosedPullRequestController;
use App\Http\Controllers\AiReviewController;



// URL入力
Route::post('/repositories', [PullRequestController::class, 'store'])->name('repo.store');

// リフレッシュ
Route::post('/repositories/{repository}/refresh', [PullRequestController::class, 'refresh'])->name('repo.refresh');



Route::get('/repositories/closed/{repo}', [ClosedPullRequestController::class, 'indexClosed'])
    ->where('repo', '.*')
    ->name('repo.closed');

Route::get('/repositories/pulls/{repo}&{number}', [ClosedPullRequestController::class, 'show'])
    ->name('repo.show')
    ->where([
        'repo' => '[a-zA-Z0-9._/-]+',
        'number' => '[0-9]+'
    ]);

Route::post('/repositories/{repo}/convention', [AiReviewController::class, 'storeConvention'])
->name('repo.convention.store')
->where('repo', '[a-zA-Z0-9._/-]+');

Route::post('/repositories/pulls/{repo}&{number}/review', [AiReviewController::class, 'executeReview'])
    ->name('repo.review.execute')
    ->where([
        'repo' => '[a-zA-Z0-9._/-]+',
        'number' => '[0-9]+'
    ]);

// 一覧表示
Route::get('/', [PullRequestController::class, 'index'])->name('repo.index');

Route::get('/{owner}/{repoName}', [PullRequestController::class, 'showRepo'])
    ->name('repo.show_details')
    ->where(['owner' => '[a-zA-Z0-9._-]+', 'repo' => '[a-zA-Z0-9._-]+']);
