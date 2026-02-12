@extends("layouts.app")
@section("title", "System Logs")
@section("page_title", "Audit Trail")
@section("page_subtitle", "Chronological record of system events and security incidents.")

@section("content")
    <div class="space-y-6 pb-20">

        {{-- 1. CONTROL BAR (Sticky & Blurry) --}}
        <div class="sticky top-0 z-30 flex flex-col gap-4 rounded-2xl bg-white/80 p-2 backdrop-blur-xl border border-zinc-200 shadow-sm md:flex-row md:items-center md:justify-between transition-all">

            {{-- Left: Status & Clock --}}
            <div class="flex items-center gap-3 px-4">
                <div class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 leading-none">Live Audit</h2>
                    <div class="flex items-center gap-2 mt-0.5">
                        <p class="text-[10px] font-medium text-zinc-500">
                            <span class="font-bold text-zinc-900">{{ $logs->total() }}</span> events recorded
                        </p>
                        <span class="text-zinc-300">•</span>
                        <p class="text-[10px] font-mono font-bold text-indigo-600" id="serverClock">00:00:00</p>
                    </div>
                </div>
            </div>

            {{-- Right: Filters (Compact) --}}
            <form action="{{ route("admin.logs.index") }}" class="flex flex-wrap items-center gap-2 pl-2" method="GET">

                {{-- Search --}}
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-indigo-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <input class="pl-9 pr-4 py-1.5 bg-zinc-50 border border-zinc-200 rounded-lg text-[11px] font-medium focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 w-[140px] md:w-[200px] transition-all outline-none placeholder:text-zinc-400" name="search" placeholder="Search logs..." type="text" value="{{ request("search") }}">
                </div>

                {{-- Severity Filter --}}
                <select class="px-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-lg text-[11px] font-bold uppercase text-zinc-600 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none cursor-pointer" name="severity" onchange="this.form.submit()">
                    <option value="">All Severity</option>
                    <option {{ request("severity") == "critical" ? "selected" : "" }} value="critical">Critical</option>
                    <option {{ request("severity") == "warning" ? "selected" : "" }} value="warning">Warning</option>
                    <option {{ request("severity") == "info" ? "selected" : "" }} value="info">Info</option>
                </select>

                {{-- Date Filters (Compact) --}}
                <div class="hidden md:flex items-center gap-1 bg-zinc-50 rounded-lg border border-zinc-200 p-0.5">
                    <select class="bg-transparent text-[10px] font-bold text-zinc-600 px-2 py-1 outline-none cursor-pointer" name="year">
                        <option value="">Year</option>
                        @for ($y = date("Y"); $y >= 2024; $y--)
                            <option {{ request("year") == $y ? "selected" : "" }} value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    <div class="w-px h-3 bg-zinc-300"></div>
                    <select class="bg-transparent text-[10px] font-bold text-zinc-600 px-2 py-1 outline-none cursor-pointer" name="month">
                        <option value="">Mon</option>
                        @foreach (range(1, 12) as $m)
                            <option {{ request("month") == $m ? "selected" : "" }} value="{{ $m }}">{{ date("M", mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Apply/Reset --}}
                <button class="hidden" type="submit"></button> {{-- Hidden submit for enter key --}}

                @if (request()->anyFilled(["search", "severity", "year", "month"]))
                    <a class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" href="{{ route("admin.logs.index") }}" title="Reset Filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </a>
                @endif
            </form>
        </div>

        {{-- 2. LOGS TABLE --}}
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50/50 border-b border-zinc-200">
                            <th class="px-6 py-3 text-[9px] font-black text-zinc-400 uppercase tracking-widest w-[140px]">Timestamp</th>
                            <th class="px-6 py-3 text-[9px] font-black text-zinc-400 uppercase tracking-widest w-[180px]">Actor</th>
                            <th class="px-6 py-3 text-[9px] font-black text-zinc-400 uppercase tracking-widest">Event / Changes</th>
                            <th class="px-6 py-3 text-[9px] font-black text-zinc-400 uppercase tracking-widest text-right w-[150px]">Trace</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-zinc-50/50 transition-colors group">
                                {{-- 1. Timestamp --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-zinc-900">{{ $log->created_at->format("d M Y") }}</span>
                                        <span class="text-[10px] font-mono text-zinc-400">{{ $log->created_at->format("H:i:s") }}</span>

                                        {{-- Severity Badge --}}
                                        <div class="mt-2">
                                            @php
                                                $sev = $log->severity->value ?? "info";
                                                $badges = [
                                                    "critical" => "bg-rose-50 text-rose-600 border-rose-100",
                                                    "warning" => "bg-amber-50 text-amber-600 border-amber-100",
                                                    "info" => "bg-blue-50 text-blue-600 border-blue-100",
                                                ];
                                                $badgeClass = $badges[$sev] ?? $badges["info"];
                                            @endphp
                                            <span class="inline-flex px-1.5 py-0.5 border rounded {{ $badgeClass }} text-[8px] font-black uppercase tracking-widest">
                                                {{ $sev }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- 2. Actor --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex items-start gap-3">
                                        <div class="h-8 w-8 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center text-[10px] font-black text-zinc-500 group-hover:bg-zinc-900 group-hover:text-white group-hover:border-zinc-900 transition-all uppercase mt-0.5">
                                            {{ $log->user ? substr($log->user->first_name, 0, 1) : "S" }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-zinc-900 truncate">{{ $log->user->first_name ?? "System" }}</p>
                                            <p class="text-[10px] font-medium text-zinc-500 truncate">{{ $log->user->last_name ?? "Auto" }}</p>
                                            <p class="text-[9px] font-mono text-zinc-400 uppercase tracking-tight mt-0.5">
                                                {{ $log->user->role ?? "BOT" }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- 3. Event Detail --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col gap-2">
                                        {{-- Header --}}
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-wider bg-indigo-50 px-1.5 rounded">{{ $log->action }}</span>
                                            <span class="text-[10px] font-bold text-zinc-500 uppercase">// {{ $log->category }}</span>
                                        </div>

                                        {{-- Dynamic Metadata Display --}}
                                        <div class="space-y-1.5">
                                            {{-- A. Target Info --}}
                                            @if (isset($log->metadata["target_user_email"]))
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[9px] font-bold text-zinc-400 uppercase">Target:</span>
                                                    <span class="text-[10px] font-mono font-bold text-zinc-700 bg-zinc-100 px-1.5 rounded border border-zinc-200">
                                                        {{ $log->metadata["target_user_email"] }}
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- B. Data Changes (Diff) --}}
                                            @if (isset($log->metadata["before"]) && isset($log->metadata["after"]))
                                                <div class="bg-zinc-50 rounded-lg border border-zinc-200 p-2 space-y-1.5 mt-1 w-full max-w-lg">
                                                    @foreach ($log->metadata["after"] as $field => $newValue)
                                                        @php
                                                            $oldValue = $log->metadata["before"][$field] ?? "NULL";
                                                            $isJson = is_array($newValue);
                                                            $newDisplay = $isJson ? json_encode($newValue) : $newValue;
                                                            $oldDisplay = is_array($oldValue) ? json_encode($oldValue) : $oldValue;
                                                        @endphp
                                                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 text-[10px] font-mono border-b border-zinc-100 last:border-0 pb-1 last:pb-0">
                                                            <span class="font-bold text-zinc-400 uppercase w-24 shrink-0">{{ str_replace("_", " ", $field) }}</span>
                                                            <div class="flex items-center gap-2 overflow-hidden">
                                                                <span class="text-rose-500 line-through decoration-rose-200 truncate max-w-[100px]" title="{{ $oldDisplay }}">{{ $oldDisplay }}</span>
                                                                <svg class="w-3 h-3 text-zinc-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                                </svg>
                                                                <span class="text-emerald-600 font-bold truncate max-w-[150px]" title="{{ $newDisplay }}">{{ $newDisplay }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- C. General Info (Reason, Notes, etc) --}}
                                            @php
                                                $exclude = ["before", "after", "target_user_email", "target_user_name", "username", "target_id", "target_type"];
                                                $extras = collect($log->metadata)->except($exclude);
                                            @endphp
                                            @if ($extras->isNotEmpty())
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach ($extras as $key => $val)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-white border border-zinc-200 rounded text-[9px] text-zinc-500 shadow-sm">
                                                            <span class="font-bold uppercase text-zinc-400">{{ str_replace("_", " ", $key) }}:</span>
                                                            <span class="font-mono max-w-[200px] truncate">{{ is_array($val) ? "JSON" : $val }}</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- 4. Technical Trace --}}
                                <td class="px-6 py-4 align-top text-right">
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="text-[10px] font-mono font-bold text-zinc-600 bg-zinc-100 px-1.5 py-0.5 rounded border border-zinc-200">
                                            {{ $log->ip_address ?? "0.0.0.0" }}
                                        </span>
                                        <span class="text-[9px] text-zinc-400 truncate max-w-[120px]" title="{{ $log->user_agent }}">
                                            {{ Str::limit($log->user_agent, 15, "...") }}
                                        </span>
                                        @if ($log->correlation_id)
                                            <span class="text-[8px] font-mono text-zinc-300 uppercase tracking-widest mt-1">
                                                CID: {{ substr($log->correlation_id, 0, 6) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-24 text-center" colspan="4">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-12 w-12 rounded-xl bg-zinc-50 border border-zinc-100 flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <p class="text-zinc-900 font-bold text-sm">No Audit Records</p>
                                        <p class="text-zinc-500 text-xs">System logs are clean for this period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-zinc-200 bg-zinc-50/50">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        // Simple Live Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-GB', {
                hour12: false
            });
            const el = document.getElementById('serverClock');
            if (el) el.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
@endpush
