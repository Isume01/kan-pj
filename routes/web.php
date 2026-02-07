<?php


use App\Http\Controllers\PullRequestController;

// 一覧表示
Route::get('/', [PullRequestController::class, 'index'])->name('repo.index');

// URL入力
Route::post('/repositories', [PullRequestController::class, 'store'])->name('repo.store');

// リフレッシュ
Route::post('/repositories/{repository}/refresh', [PullRequestController::class, 'refresh'])->name('repo.refresh');
