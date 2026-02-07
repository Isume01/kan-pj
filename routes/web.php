<?php


use App\Http\Controllers\PullRequestController;
use App\Http\Controllers\ClosedPullRequestController;
// 一覧表示
Route::get('/', [PullRequestController::class, 'index'])->name('repo.index');

// URL入力
Route::post('/repositories', [PullRequestController::class, 'store'])->name('repo.store');

// リフレッシュ
Route::post('/repositories/{repository}/refresh', [PullRequestController::class, 'refresh'])->name('repo.refresh');



Route::get('/repositories/closed/{repo}', [ClosedPullRequestController::class, 'indexClosed'])
    ->where('repo', '.*')
    ->name('repo.closed');

// 一覧からクリックした時に、新しいタブで開かれる画面です
Route::get('/repositories/pulls/{number}', [ClosedPullRequestController::class, 'show'])->name('repo.show');

