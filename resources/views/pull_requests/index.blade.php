<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexusrefresh - AI PR Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
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
                       class="border border-slate-300 px-4 py-1.5 rounded-lg text-sm w-64 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg text-sm font-semibold transition">
                    リポジトリ追加
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto p-6 md:p-8">

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-8 flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                <span class="text-xl">✅</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @foreach($repositories as $repo)
            <section class="mb-16">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg shadow-sm border border-slate-200 text-2xl">📦</div>
                        <div>
                            <h2 class="text-2xl font-bold text-slate-800">{{ $repo->full_name }}</h2>
                            <p class="text-sm text-slate-500">最新のプルリクエスト同期</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <form action="{{ route('repo.refresh', $repo->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="this.innerHTML='<span class=\'animate-spin\'>⏳</span> 要約中...'; this.disabled=true; this.form.submit();"
                                    class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 px-5 py-2 rounded-xl text-sm font-bold shadow-sm transition-all active:scale-95">
                                🔄 同期＆AI要約を実行
                            </button>
                        </form>

                        <a href="{{ route('repo.closed', ['repo' => $repo->full_name]) }}" target="_blank"
                           class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-sm transition-all active:scale-95">
                            📁 履歴・差分を確認
                        </a>
                    </div>
                </div>

                <div class="grid gap-8">
                    @forelse($repo->pullRequests as $pr)
                        <div class="group bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-6">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2 py-0.5 rounded">#{{ $pr->number }}</span>
                                            <span class="text-xs text-slate-400 font-medium">{{ $pr->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                            {{ $pr->title }}
                                        </h3>
                                        <div class="flex items-center gap-2 mt-2">
                                            <div class="w-5 h-5 bg-slate-200 rounded-full flex items-center justify-center text-[10px]">👤</div>
                                            <span class="text-sm text-slate-600 font-medium">{{ $pr->user_login }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ $pr->html_url }}" target="_blank" class="text-slate-400 hover:text-indigo-500 transition-colors p-2 bg-slate-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>

                                <div class="relative">
                                    <div class="absolute -left-6 top-0 bottom-0 w-1 bg-indigo-500 rounded-r-full"></div>
                                    <div class="bg-indigo-50/50 rounded-xl p-5 border border-indigo-100">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-lg">🤖</span>
                                            <h4 class="text-sm font-bold text-indigo-900 tracking-wider uppercase">AI Summary</h4>
                                        </div>
                                        <div class="text-slate-700 leading-relaxed text-sm space-y-2">
                                            @if($pr->aiSummary)
                                                {!! nl2br(e($pr->aiSummary->summary)) !!}
                                            @else
                                                <p class="text-slate-400 italic">⚠️ 要約が生成されていません。同期ボタンを押してください。</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white rounded-2xl border-2 border-dashed border-slate-200">
                            <p class="text-slate-400">プルリクエストが見つかりませんでした。</p>
                        </div>
                    @endforelse
                </div>
            </section>
        @endforeach

    </main>

    <footer class="text-center py-12 text-slate-400 text-sm">
        &copy; 2026 Nexusrefresh Project. Created by Kan.
    </footer>

</body>
</html>
