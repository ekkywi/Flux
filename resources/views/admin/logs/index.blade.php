@extends("layouts.app")
@section("title", "System Logs")
@section("page_title", "Audit Trail")

@section("content")
    <div class="space-y-8 pb-20 text-slate-900">

        {{-- 1. STREAMLINED HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <div class="h-1 w-6 bg-indigo-600 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Security Protocol</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">System Audit</h1>
                <p class="text-xs text-slate-500 font-medium">Monitoring <span class="text-indigo-600 font-bold">{{ $logs->total() }} recorded events</span> in the black box.</p>
            </div>

            {{-- Compact Stats --}}
            <div class="flex items-center gap-4 px-5 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="text-center min-w-[50px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Total Logs</span>
                    <span class="text-sm font-black text-slate-900">{{ $logs->total() }}</span>
                </div>
                <div class="w-px h-6 bg-slate-100"></div>
                <div class="text-center min-w-[50px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Critical</span>
                    <span class="text-sm font-black text-rose-600">{{ \App\Models\AuditLog::where("severity", "critical")->count() }}</span>
                </div>
                <div class="w-px h-6 bg-slate-100"></div>
                <div class="text-center min-w-[80px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Server Time</span>
                    <span class="text-xs font-mono font-bold text-indigo-600" id="serverClock">00:00:00</span>
                </div>
            </div>
        </div>

        {{-- 1.5 SEARCH & FILTER BAR --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm mb-6">
            <form action="{{ route("admin.logs.index") }}" class="space-y-4" method="GET">
                <div class="flex flex-col lg:flex-row gap-4">
                    {{-- Search Input (Flex 1) --}}
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                            </svg>
                        </div>
                        <input class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-slate-200 rounded-xl text-xs font-medium focus:ring-indigo-500 focus:border-indigo-500 transition-all" name="search" placeholder="Search actor, action, or metadata..." type="text" value="{{ request("search") }}">
                    </div>

                    {{-- Time & Severity Group --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:flex gap-2">
                        {{-- Year --}}
                        <select class="px-4 py-2.5 bg-slate-50 border-slate-200 rounded-xl text-[10px] font-black uppercase text-slate-700 focus:ring-indigo-500 appearance-none cursor-pointer min-w-[100px]" name="year">
                            <option value="">Year</option>
                            @for ($y = date("Y"); $y >= 2024; $y--)
                                <option {{ request("year") == $y ? "selected" : "" }} value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>

                        {{-- Month --}}
                        <select class="px-4 py-2.5 bg-slate-50 border-slate-200 rounded-xl text-[10px] font-black uppercase text-slate-700 focus:ring-indigo-500 appearance-none cursor-pointer min-w-[100px]" name="month">
                            <option value="">Month</option>
                            @foreach (range(1, 12) as $m)
                                <option {{ request("month") == $m ? "selected" : "" }} value="{{ $m }}">
                                    {{ date("F", mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Day --}}
                        <select class="px-4 py-2.5 bg-slate-50 border-slate-200 rounded-xl text-[10px] font-black uppercase text-slate-700 focus:ring-indigo-500 appearance-none cursor-pointer min-w-[80px]" name="day">
                            <option value="">Day</option>
                            @foreach (range(1, 31) as $d)
                                <option {{ request("day") == $d ? "selected" : "" }} value="{{ $d }}">{{ sprintf("%02d", $d) }}</option>
                            @endforeach
                        </select>

                        {{-- Severity --}}
                        <select class="px-4 py-2.5 bg-slate-50 border-slate-200 rounded-xl text-[10px] font-black uppercase text-rose-600 focus:ring-indigo-500 appearance-none cursor-pointer min-w-[120px]" name="severity">
                            <option value="">Severity</option>
                            <option {{ request("severity") == "critical" ? "selected" : "" }} value="critical">CRITICAL</option>
                            <option {{ request("severity") == "warning" ? "selected" : "" }} value="warning">WARNING</option>
                            <option {{ request("severity") == "info" ? "selected" : "" }} value="info">INFO</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-2">
                        <button class="px-6 py-2.5 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200" type="submit">
                            Apply
                        </button>
                        @if (request()->anyFilled(["search", "severity", "year", "month", "day"]))
                            <a class="px-4 py-2.5 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-100 transition-all flex items-center justify-center" href="{{ route("admin.logs.index") }}">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- 2. AUDIT LOG TABLE --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Timestamp</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Actor</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Event / Action</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Severity</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Trace</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/30 transition-colors group">
                                {{-- Timestamp --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-900">{{ $log->created_at->format("d M Y") }}</span>
                                        <span class="text-[10px] font-mono text-slate-400">{{ $log->created_at->format("H:i:s") }}</span>
                                    </div>
                                </td>

                                {{-- Actor --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-lg bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-all uppercase">
                                            {{ $log->user ? substr($log->user->first_name, 0, 1) : "S" }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate">{{ $log->user->first_name ?? "System" }}</p>
                                            <p class="text-[9px] font-mono text-slate-400 uppercase tracking-tight">
                                                {{ $log->user->role ?? "Automated Process" }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Event / Action --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        {{-- Header: Action & Category --}}
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-wider">{{ $log->action }}</span>
                                            <span class="text-[9px] text-slate-300">•</span>
                                            <span class="text-[9px] font-bold text-slate-500 uppercase">{{ $log->category }}</span>
                                        </div>

                                        {{-- Metadata Area --}}
                                        <div class="flex flex-wrap gap-1.5 items-center mt-1">

                                            {{-- 1. IDENTITAS TARGET (Snapshot) --}}
                                            @if (isset($log->metadata["target_user_email"]))
                                                <span class="px-1.5 py-0.5 bg-slate-900 text-white text-[8px] font-bold rounded uppercase shadow-sm">
                                                    Target: {{ $log->metadata["target_user_email"] }}
                                                </span>
                                            @endif

                                            {{-- 2. LOGIKA PERUBAHAN DATA (Diff View untuk Update) --}}
                                            @if (isset($log->metadata["before"]) && isset($log->metadata["after"]))
                                                @foreach ($log->metadata["after"] as $field => $newValue)
                                                    <div class="flex items-center gap-1 px-1.5 py-0.5 bg-indigo-50 border border-indigo-100 rounded text-[8px] font-mono shadow-sm">
                                                        <span class="text-slate-400 uppercase">{{ str_replace("_", " ", $field) }}:</span>
                                                        <span class="text-slate-400 line-through decoration-slate-300">{{ $log->metadata["before"][$field] ?? "NULL" }}</span>
                                                        <svg class="w-2.5 h-2.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
                                                        </svg>
                                                        <span class="text-indigo-600 font-black">{{ is_array($newValue) ? "JSON" : $newValue }}</span>
                                                    </div>
                                                @endforeach
                                            @endif

                                            {{-- 3. INFORMASI UMUM (Info lain yang bukan before/after/target) --}}
                                            @php
                                                $excludeKeys = ["before", "after", "target_user_email", "target_user_name", "username"];
                                                $generalInfo = collect($log->metadata)->except($excludeKeys);
                                            @endphp

                                            @foreach ($generalInfo as $key => $val)
                                                <div class="flex items-center gap-1 px-1.5 py-0.5 bg-slate-100 border border-slate-200 rounded text-[8px] font-mono text-slate-600 shadow-sm">
                                                    <span class="text-slate-400 uppercase font-medium">{{ str_replace("_", " ", $key) }}:</span>
                                                    <span class="font-bold uppercase">{{ is_array($val) ? "Object" : $val }}</span>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </td>

                                {{-- Severity --}}
                                <td class="px-6 py-4">
                                    @php
                                        // AMAN: Mengambil string value dari Enum
                                        $status = $log->severity->value ?? "info";
                                        $color = match ($status) {
                                            "critical" => "text-rose-600 bg-rose-50 border-rose-100",
                                            "warning" => "text-amber-600 bg-amber-50 border-amber-100",
                                            default => "text-emerald-600 bg-emerald-50 border-emerald-100",
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 border {{ $color }} text-[8px] font-black uppercase tracking-widest rounded-md">
                                        {{ $status }}
                                    </span>
                                </td>

                                {{-- Trace (Technical Fingerprint) --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end gap-1.5">
                                        {{-- 1. IP Address --}}
                                        <span class="text-[10px] font-mono font-bold text-slate-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                                            {{ $log->ip_address ?? "0.0.0.0" }}
                                        </span>

                                        {{-- 2. Correlation ID (Jika ada) --}}
                                        @if ($log->correlation_id)
                                            <div class="flex items-center gap-1 text-[8px] font-mono text-indigo-400 uppercase tracking-tighter" title="Correlation ID: {{ $log->correlation_id }}">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                </svg>
                                                <span>{{ substr($log->correlation_id, 0, 8) }}...</span>
                                            </div>
                                        @endif

                                        {{-- 3. Device/User Agent --}}
                                        <span class="text-[8px] text-slate-300 truncate max-w-[120px] font-medium" title="{{ $log->user_agent }}">
                                            {{ Str::limit($log->user_agent, 20) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-20 text-center" colspan="5">
                                    <p class="text-slate-400 text-xs font-medium uppercase tracking-widest">No activity recorded in the audit trail.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-GB', {
                hour12: false
            });
            const clockElement = document.getElementById('serverClock');
            if (clockElement) clockElement.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
@endpush
