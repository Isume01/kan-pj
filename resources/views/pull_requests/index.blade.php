<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexusrefresh - AI Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🚀</span>
                <span class="text-xl font-bold tracking-tight">Nexusrefresh</span>
            </div>

            <form action="{{ route('repo.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="repo_url" placeholder="repo URL (owner/repo)"
                       class="border border-slate-300 px-4 py-1.5 rounded-lg text-sm w-64 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg text-sm font-semibold transition">
                    リポジトリ追加
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto p-6 md:p-8">

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-8 flex items-center gap-3 animate-in fade-in duration-300">
                <span>✅</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @foreach($repositories as $repo)
            <section class="mb-20">
                {{-- リポジトリヘッダー --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 text-3xl">📦</div>
                        <div>
                            <h2 class="text-3xl font-bold text-slate-800">{{ $repo->full_name }}</h2>
                            <p class="text-sm text-slate-500">Active Pull Requests</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <form action="{{ route('repo.refresh', $repo->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-4 py-2 rounded-xl text-sm font-bold transition-all active:scale-95">
                                🔄 同期
                            </button>
                        </form>
                        <a href="{{ route('repo.closed', ['repo' => $repo->full_name]) }}" target="_blank"
                           class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all active:scale-95">
                            📁 履歴
                        </a>
                    </div>
                </div>

                {{-- ★ コーディング規約設定エリア (リポジトリ単位に集約) ★ --}}
                <div class="mb-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <details class="group">
                        <summary class="flex items-center justify-between p-4 cursor-pointer hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="text-lg">📋</span>
                                <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Coding Conventions</h4>
                                @if($repo->codingConvention)
                                    <span class="text-[10px] bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-full font-bold">設定済み</span>
                                @endif
                            </div>
                            <span class="text-slate-400 group-open:rotate-180 transition-transform">▼</span>
                        </summary>
                        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                            <form action="{{ route('repo.convention.store', $repo->full_name) }}"
                                  method="POST"
                                  onsubmit="return validateSave(event, {{ $repo->id }})">
                                @csrf
                                <textarea id="convention-{{ $repo->id }}" name="content"
                                    placeholder="例：関数のネストは3段まで、命名はキャメルケース..."
                                    class="w-full text-sm p-4 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">{{ optional($repo->codingConvention)->content }}</textarea>

                                <div class="mt-3 flex justify-end">
                                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all">
                                        {{ $repo->codingConvention ? '規約を更新する' : '規約を保存する' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </details>
                </div>

                {{-- PRリスト --}}
                <div class="grid gap-6">
                    @forelse($repo->pullRequests as $pr)
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1 text-xs text-slate-400 font-medium">
                                            <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-bold">#{{ $pr->number }}</span>
                                            <span>by {{ $pr->user_login }}</span>
                                            <span>•</span>
                                            <span>{{ $pr->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-900">{{ $pr->title }}</h3>
                                    </div>
                                    <a href="{{ $pr->html_url }}" target="_blank" class="text-slate-400 hover:text-indigo-600 p-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>

                                {{-- AI Summary Area --}}
                                <div class="bg-indigo-50/50 rounded-xl p-4 border border-indigo-100 mb-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm">🤖</span>
                                        <h4 class="text-xs font-bold text-indigo-900 tracking-wider uppercase">AI Summary</h4>
                                    </div>
                                    <div class="text-slate-700 text-sm leading-relaxed">
                                        @if($pr->aiSummary)
                                            {!! nl2br(e($pr->aiSummary->summary)) !!}
                                        @else
                                            <p class="text-slate-400 italic">同期ボタンを押して要約を生成してください</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- ★ AI Review Result Area (ここに追加) ★ --}}
                                @if($pr->aiReviews && $pr->aiReviews->isNotEmpty())
                                    <div class="bg-amber-50/50 rounded-xl p-4 border border-amber-100 mb-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-sm">🧐</span>
                                            <h4 class="text-xs font-bold text-amber-900 tracking-wider uppercase">AI Code Review</h4>
                                        </div>
                                        <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $pr->aiReviews->last()->review_result }}</div>
                                    </div>
                                @endif

                                {{-- Action Buttons --}}
                                <div class="flex flex-col gap-4">
                                    <div class="flex items-center gap-3">
                                        <button onclick="toggleDiff(...)">🔍 差分</button>

                                        <button type="button"
                                                id="review-btn-{{ $pr->number }}"
                                                onclick="startAiReview('{{ $repo->full_name }}', {{ $pr->number }})"
                                                class="shrink-0 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-md shadow-indigo-200">
                                            <span class="icon">🚀</span> AIレビューを実行
                                        </button>
                                    </div>

                                    <div id="progress-container-{{ $pr->number }}" class="hidden w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                        <div id="progress-bar-{{ $pr->number }}"
                                             class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                                             style="width: 0%"></div>
                                        <p class="text-[10px] text-slate-500 mt-1 animate-pulse">AIがコードを解析中...（大規模なPRの場合は1分ほどかかることがあります）</p>
                                    </div>
                                </div>

                                {{-- Ajax Diff Container --}}
                                <div id="diff-container-{{ $pr->number }}" class="hidden mt-4 pt-4 border-t border-slate-100">
                                    <div class="loading text-center py-4 text-slate-400 text-xs">読み込み中...</div>
                                    <div class="content overflow-hidden"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white rounded-2xl border-2 border-dashed border-slate-200 text-slate-400">
                            アクティブなPRはありません。
                        </div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </main>
    <footer class="text-center py-12 text-slate-400 text-xs">
        &copy; 2026 Nexusrefresh Project.
    </footer>
    <script>
        // 規約保存時のチェック
        function validateSave(event, repoId) {
            const textarea = document.getElementById(`convention-${repoId}`);

            if (!textarea || textarea.value.trim() === "") {
                alert("⚠️ 規約内容が空です。内容を入力してから保存してください。");
                textarea.focus();
                return false; // 送信を中止
            }
            return true; // 送信を実行
        }

        function checkConvention(event, repoId) {
            const textarea = document.getElementById(`convention-${repoId}`);

            if (!textarea || textarea.value.trim() === "") {
                alert("⚠️ コーディング規約が設定されていません。先に規約を保存してください。");
                const details = textarea.closest('details');
                if (details) details.open = true;
                textarea.focus();
                return false;
            }
            return true;
        }

        async function startAiReview(repoName, prNumber) {
            const btn = document.getElementById(`review-btn-${prNumber}`);
            const container = document.getElementById(`progress-container-${prNumber}`);
            const bar = document.getElementById(`progress-bar-${prNumber}`);

            // UIの初期化
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            container.classList.remove('hidden');

            // 擬似的な進捗アニメーション
            let width = 0;
            const interval = setInterval(() => {
                if (width < 90) { // 90%までは自動で進む
                    width += Math.random() * 2;
                    bar.style.width = width + "%";
                }
            }, 1000);

            try {
                const response = await fetch(`/repositories/pulls/${repoName}&${prNumber}/review`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Review failed');

                const result = await response.json();

                // 完了！
                clearInterval(interval);
                bar.style.width = "100%";

                setTimeout(() => {
                    alert("✅ レビューが完了しました！ページを更新します。");
                    location.reload(); // 結果を表示するためにリロード
                }, 500);

            } catch (error) {
                clearInterval(interval);
                alert("❌ エラーが発生しました。時間をおいて再度お試しください。");
                btn.disabled = false;
                btn.classList.remove('opacity-50');
                container.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
