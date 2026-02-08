<div class="space-y-6 w-full max-w-full overflow-hidden">
    @foreach($files as $file)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col w-full">
            {{-- ファイル名 --}}
            <div class="bg-slate-50 px-3 py-2 border-b border-slate-200 flex justify-between items-center shrink-0">
                <span class="font-mono text-[11px] font-bold text-slate-600 truncate mr-4" title="{{ $file['filename'] }}">
                    {{ $file['filename'] }}
                </span>
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-200 text-slate-500 shrink-0">
                    {{ $file['status'] }}
                </span>
            </div>

            <div class="relative w-full overflow-x-auto bg-slate-50">
                <pre class="p-4 text-[12px] font-mono leading-5 text-slate-700 w-full"><code>@isset($file['patch'])
                    @foreach(explode("\n", $file['patch']) as $line)
                    @php
                        $bg = '';
                        if (str_starts_with($line, '+')) $bg = 'bg-emerald-50 text-emerald-700';
                        elseif (str_starts_with($line, '-')) $bg = 'bg-rose-50 text-rose-700';
                        elseif (str_starts_with($line, '@@')) $bg = 'bg-slate-100/50 text-slate-400 font-bold';
                    @endphp
                    <span class="{{ $bg }} block px-2 whitespace-pre w-fit min-w-full">{{ $line }}</span>
                    @endforeach
                    @else
                    <div class="px-4 py-8 text-center text-slate-400 italic">差分なし</div>
                    @endisset</code></pre>
            </div>
        </div>
    @endforeach
</div>
