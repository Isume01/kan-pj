<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PULLREQUEST AI REVIEWER - AI Review Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased overflow-hidden">

    <div class="flex h-screen w-full">
        <aside class="w-72 bg-slate-50 border-r border-slate-200 flex flex-col shrink-0">
            <div class="p-6 flex items-center gap-3">
                <span class="text-2xl">🚀</span>
                <span class="text-lg font-bold tracking-tight text-slate-800">PULLREQUEST AI REVIEWER</span>
            </div>

            <div class="px-4 mb-4">

                <a href="{{ route('repo.index') }}" class="w-full flex items-center gap-3 px-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                    <span>➕</span> 新しいリポジトリ
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-2 space-y-1">
                @foreach($repositories as $repo)
                    @php
                        [$owner, $name] = explode('/', $repo->full_name);
                        $isActive = isset($selectedRepo) && $selectedRepo->id === $repo->id;
                    @endphp
                    <a href="{{ route('repo.show_details', ['owner' => $owner, 'repoName' => $name]) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all {{ $isActive ? 'bg-indigo-100 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-200/50' }}">
                        <span>📦</span> {{ $repo->full_name }}
                    </a>
                @endforeach
            </nav>
            <div class="p-4 border-t border-slate-200 text-[10px] text-slate-400 text-center">
                &copy; 2026 KAN'TEST Project.
            </div>
        </aside>

        <main class="flex-1 flex flex-col relative overflow-y-auto">
            @if(isset($selectedRepo))
                <div id="repo-content-area" class="p-8 max-w-5xl mx-auto w-full">
                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl mb-8">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-3xl font-bold">{{ $selectedRepo->full_name }}</h2>
                        <div class="flex gap-2">
                            <a href="{{ route('repo.closed', ['repo' => $selectedRepo->full_name]) }}"
                               class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all active:scale-95 shadow-md">
                                📁 履歴を確認
                            </a>
                            <div class="flex gap-2 items-center">
                                <button type="button"
                                        id="sync-btn-{{ $selectedRepo->id }}"
                                        onclick="startSync({{ $selectedRepo->id }})"
                                        class="inline-flex items-center gap-2 bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-slate-50 transition-all active:scale-95">
                                    <span class="icon">🔄</span> <span class="text">同期</span>
                                </button>


                                <div id="sync-loader-{{ $selectedRepo->id }}" class="hidden">
                                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 規約設定 --}}
                    <div class="mb-8 bg-slate-50 rounded-3xl p-6 border border-slate-200">
                        <h4 class="text-xs font-bold text-slate-500 uppercase mb-4 tracking-widest">Coding Conventions</h4>
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

                    {{-- PRリスト --}}
                    <div class="space-y-6">
                        @forelse($selectedRepo->pullRequests as $pr)
                            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1 text-xs text-slate-400 font-medium">
                                            <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-bold">#{{ $pr->number }}</span>
                                            <span>by {{ $pr->user_login }}</span>
                                        </div>
                                        <h3 class="text-lg font-bold text-slate-900">{{ $pr->title }}</h3>
                                    </div>
                                    <a href="{{ $pr->html_url }}" target="_blank" class="text-slate-400 hover:text-indigo-600 p-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>

                                {{-- AI要約 --}}
                                @if($pr->aiSummary)
                                    <div class="bg-indigo-50/30 rounded-2xl p-4 border border-indigo-100/50 mb-4 prose prose-sm prose-indigo max-w-none">
                                        {!! (new \Parsedown())->text($pr->aiSummary->summary) !!}
                                    </div>
                                @endif

                                {{-- AIレビュー結果 --}}
                                @if($pr->aiReviews && $pr->aiReviews->isNotEmpty())
                                    <div class="bg-amber-50/30 rounded-2xl p-4 border border-amber-100/50 mb-4 prose prose-sm prose-amber max-w-none">
                                        {!! (new \Parsedown())->text($pr->aiReviews->last()->review_result) !!}
                                    </div>
                                @endif

                                {{-- ボタン --}}
                                @php
                                    // 規約が存在し、かつ中身が空でないかチェック
                                    $hasConvention = optional($selectedRepo->codingConvention)->content ? true : false;
                                @endphp

                                <div class="relative group inline-block">
                                    {{-- 規約がない場合、ボタンの上に透明なレイヤーを置いてホバーを検知 --}}
                                    @if(!$hasConvention)
                                        <div class="absolute inset-0 z-10 cursor-not-allowed" title="コーディング規約を入力してください"></div>
                                    @endif

                                    <button type="button"
                                            id="review-btn-{{ $pr->number }}"
                                            onclick="startAiReview('{{ $selectedRepo->full_name }}', {{ $pr->number }})"
                                            @disabled(!$hasConvention)
                                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md active:scale-95
                                            {{ $hasConvention
                                                ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-100'
                                                : 'bg-slate-200 text-slate-400 border border-slate-300'
                                            }}">
                                        🚀 AIレビューを実行
                                    </button>

                                    @if(!$hasConvention)
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1.5 bg-slate-800 text-white text-[10px] rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-20">
                                            ⚠️ コーディング規約を入力してください
                                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-800"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-slate-400">PRがありません。</div>
                        @endforelse
                    </div>
                </div>
            @else
                <div id="home-view" class="flex-1 flex flex-col items-center justify-center p-6 min-h-screen">
                    <div class="w-full max-w-2xl text-center">
                        <h1 class="text-5xl font-bold text-slate-800 mb-8 tracking-tight">AI Reviewer</h1>
                        <form action="{{ route('repo.store') }}" method="POST" class="relative group">
                            @csrf
                            <input type="text" name="repo_url" placeholder="owner/repository-name を入力して追加..."
                                   class="w-full bg-white border border-slate-200 py-5 pl-14 pr-32 rounded-3xl text-lg shadow-xl shadow-slate-200/50 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                            <div class="absolute left-5 top-5 text-2xl group-focus-within:animate-bounce">🔍</div>
                            <button type="submit" class="absolute right-3 top-3 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-2xl text-sm font-bold transition-all">
                                追加する
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <script>
    function validateSave(event, repoId) {
            const textarea = document.getElementById(`convention-${repoId}`);

            if (!textarea || textarea.value.trim() === "") {
                alert("⚠️ 規約内容が空です。内容を入力してから保存してください。");
                textarea.focus();
                return false; // 送信を中止
            }
            return true; // 送信を実行
        }

        async function startAiReview(repoName, prNumber) {
            const btn = document.getElementById(`review-btn-${prNumber}`);
            const container = document.getElementById(`progress-container-${prNumber}`);
            const bar = document.getElementById(`progress-bar-${prNumber}`);


            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            container.classList.remove('hidden');

            let width = 0;
            const interval = setInterval(() => {
                if (width < 90) {
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

                clearInterval(interval);
                bar.style.width = "100%";

                setTimeout(() => {
                    alert("✅ レビュー完了！ページを更新します。");
                    location.reload();
                }, 500);

            } catch (error) {
                clearInterval(interval);
                alert("❌ エラーが発生しました。");
                btn.disabled = false;
                btn.classList.remove('opacity-50');
                container.classList.add('hidden');
            }
        }

        async function startSync(repoId) {
            const btn = document.getElementById(`sync-btn-${repoId}`);
            const loader = document.getElementById(`sync-loader-${repoId}`);
            const btnText = btn.querySelector('.text');
            const btnIcon = btn.querySelector('.icon');

            // UI状態を「処理中」に変更
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            loader.classList.remove('hidden');
            btnText.innerText = "同期中...";
            btnIcon.classList.add('animate-spin');

            try {
                const response = await fetch(`/repositories/${repoId}/refresh`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Sync failed');

                // 完了！
                btnText.innerText = "完了！";
                btnIcon.classList.remove('animate-spin');

                // 数秒後にリロードして最新データを反映（URLは維持されるのでタブはそのまま！）
                setTimeout(() => {
                    location.reload();
                }, 800);

            } catch (error) {
                console.error(error);
                alert("❌ 同期に失敗しました。GitHubの接続やレートリミットを確認してください。");

                // 状態を戻す
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                loader.classList.add('hidden');
                btnText.innerText = "同期";
            }
        }
    </script>
</body>
</html>
