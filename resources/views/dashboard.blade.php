@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Pull Request Monitoring</h1>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition flex items-center shadow-lg">
            <i class="fa-solid fa-arrows-rotate mr-2"></i> Sync Now
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded">Merged</span>
                    <span class="text-gray-400 text-xs italic">#PR-1254</span>
                </div>
                <h3 class="text-lg font-bold mb-2">Update API Gateway Logic</h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-2 text-justify">
                    This PR refactors the core gateway logic to support multi-tenant routing...
                </p>
                
                <div class="border-t border-gray-100 pt-4">
                    <div class="flex items-center text-xs font-bold text-indigo-600 mb-2 uppercase tracking-wider">
                        <i class="fa-solid fa-robot mr-2"></i> AI Summary
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed italic">
                        "この変更は、メモリリークのリスクを15%削減しますが、古いルーティング定義との互換性に注意が必要です。"
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden opacity-75">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded animate-pulse">Analyzing...</span>
                </div>
                <h3 class="text-lg font-bold mb-2 text-gray-400">Implement New Webhook Handler</h3>
                <div class="h-20 flex items-center justify-center">
                    <i class="fa-solid fa-spinner fa-spin text-indigo-400 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection