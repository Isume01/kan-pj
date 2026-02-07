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

    {{-- ナビゲーション --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🚀</span>
                <span class="text-xl font-bold tracking-tight">Nexusrefresh</span>
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

            {{-- 件数調整セレクトボックス --}}
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

        {{-- PR一覧カード --}}
        <div class="grid gap-4">
            @forelse($pullRequests as $pr)
                <div class="group bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="bg-slate-100 text-slate-500 text-xs font-bold px-2 py-0.5 rounded">#{{ $pr->number }}</span>
                                <span class="text-xs text-slate-400">{{ $pr->user_login }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $pr->title }}</h3>
                            <p class="text-slate-600 text-sm line-clamp-2">{{ $pr->body ?? '説明なし' }}</p>
                        </div>

                        {{-- 詳細表示ボタン（別タブ） --}}
                        <a href="{{ route('repo.show', ['repo' => $repo, 'number' => $pr->number]) }}"
                           target="_blank"
                           class="ml-4 shrink-0 inline-flex items-center gap-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-5 py-2.5 rounded-xl text-sm font-bold transition-all active:scale-95">
                            📄 差分を確認
                        </a>
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
        &copy; 2026 Nexusrefresh Project.
    </footer>

</body>
</html>
