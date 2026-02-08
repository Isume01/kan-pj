<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Closed PRs - {{ $repo }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🚀</span>
                <span class="text-xl font-bold tracking-tight">PULLREQUEST AI REVIEWER</span>
            </div>
            <a href="{{ route('repo.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                ← ダッシュボードへ戻る
            </a>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto p-6 md:p-8">
        <header class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">📜 クローズ済みプルリクエスト</h1>
                <p class="text-slate-500 mt-1">リポジトリ: {{ $repo }}</p>
            </div>

            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">表示件数</span>
                <form action="{{ route('repo.closed', $repo) }}" method="GET">
                    <select name="per_page" onchange="this.form.submit()"
                            class="text-sm font-bold text-indigo-600 outline-none cursor-pointer bg-transparent">
                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5件</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10件</option>
                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20件</option>
                    </select>
                </form>
            </div>
        </header>

        <div class="grid gap-4 min-w-0">
            @forelse($pullRequests as $pr)
               <div class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all min-w-0 overflow-hidden">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-slate-100 text-slate-500 text-xs font-bold px-2 py-0.5 rounded">#{{ $pr->number }}</span>
                                <span class="text-xs text-slate-400">{{ $pr->user_login }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $pr->title }}</h3>
                            <p class="text-slate-600 text-sm line-clamp-2">{{ $pr->body ?? '説明なし' }}</p>
                        </div>

                        <button onclick="toggleDiff(this, '{{ $repo }}', {{ $pr->number }})"
                           class="ml-4 shrink-0 inline-flex items-center gap-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-5 py-2.5 rounded-xl text-sm font-bold transition-all active:scale-95">
                            <span class="icon">🔍</span> 差分を確認
                        </button>
                    </div>

                   <div id="diff-container-{{ $pr->number }}" class="hidden mt-6 pt-6 border-t border-slate-100 w-full max-w-full overflow-hidden">
                        <div class="loading text-center py-4 text-slate-400 flex items-center justify-center gap-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-indigo-500 border-t-transparent"></div>
                            読み込み中...
                        </div>
                        <div class="content w-full max-w-full overflow-hidden"></div>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-2xl border-2 border-dashed border-slate-200">
                    <p class="text-slate-400">クローズ済みのプルリクエストは見つかりませんでした。</p>
                </div>
            @endforelse
        </div>
    </main>

    <footer class="text-center py-12 text-slate-400 text-sm">
        &copy; 2026 KAN'TEST Project.
    </footer>

    <script>
        async function toggleDiff(button, repo, number) {
            const container = document.getElementById(`diff-container-${number}`);
            const contentArea = container.querySelector('.content');
            const loadingArea = container.querySelector('.loading');
            const icon = button.querySelector('.icon');

            if (!container.classList.contains('hidden')) {
                container.classList.add('hidden');
                icon.innerText = '🔍';
                return;
            }

            container.classList.remove('hidden');
            icon.innerText = '🔼';

            if (contentArea.innerHTML !== '') return;

            try {
                loadingArea.classList.remove('hidden');

                const response = await fetch(`/repositories/pulls/${repo}&${number}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error('取得失敗');

                const html = await response.text();
                contentArea.innerHTML = html;
                loadingArea.classList.add('hidden');
            } catch (error) {
                contentArea.innerHTML = '<p class="text-red-500 text-sm">差分の取得に失敗しました。時間をおいて再度お試しください。</p>';
                loadingArea.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
