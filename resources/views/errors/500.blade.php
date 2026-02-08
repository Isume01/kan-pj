<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Internal Server Error - PULLREQUEST AI REVIEWER</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased flex items-center justify-center min-h-screen">

    <div class="max-w-md w-full p-8 text-center">
        <div class="mb-6 inline-flex items-center justify-center w-20 h-20 bg-rose-100 text-rose-600 rounded-full text-4xl shadow-sm">
            ⚠️
        </div>

        <h1 class="text-4xl font-extrabold text-slate-800 mb-4 tracking-tight">500</h1>
        <h2 class="text-xl font-bold text-slate-700 mb-2">サーバーで問題が発生しました</h2>
        <p class="text-slate-500 mb-8 leading-relaxed">
            申し訳ありません。リクエストの処理中に予期せぬエラーが発生しました。しばらく時間を置いてから再度お試しください。
        </p>

        <div class="flex flex-col gap-3">
            <a href="/" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md active:scale-95">
                ダッシュボードに戻る
            </a>
            <button onclick="location.reload()" class="text-slate-500 hover:text-slate-800 text-sm font-medium transition-colors">
                ページを再読み込みする
            </button>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-200 text-slate-400 text-xs italic">
            Reference ID: {{ substr(md5(time()), 0, 8) }}
        </div>
    </div>

</body>
</html>
