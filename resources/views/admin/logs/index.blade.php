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
                                        <div class="h-8 w-8 rounded-lg bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                            {{ $log->user ? strtoupper(substr($log->user->first_name, 0, 1)) : "S" }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate">{{ $log->user->first_name ?? "System" }}</p>
                                            <p class="text-[9px] font-mono text-slate-400 uppercase">{{ $log->user->role ?? "Automated" }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Event / Action --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-wider">{{ $log->action }}</span>
                                            <span class="text-[9px] text-slate-300">•</span>
                                            <span class="text-[9px] font-bold text-slate-500 uppercase">{{ $log->category }}</span>
                                        </div>
                                        @if ($log->metadata)
                                            <p class="text-[10px] text-slate-400 font-medium italic">
                                                Target: {{ $log->metadata["username"] ?? ($log->metadata["provisioned_email"] ?? "N/A") }}
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                {{-- Severity Badge --}}
                                <td class="px-6 py-4">
                                    @php
                                        $sevClasses = match ($log->severity) {
                                            "critical" => "bg-rose-50 text-rose-600 border-rose-100",
                                            "warning" => "bg-amber-50 text-amber-600 border-amber-100",
                                            default => "bg-emerald-50 text-emerald-600 border-emerald-100",
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 border {{ $sevClasses }} text-[8px] font-black uppercase tracking-widest rounded-md">
                                        {{ $log->severity }}
                                    </span>
                                </td>

                                {{-- Trace (IP Address) --}}
                                <td class="px-6 py-4 text-right">
                                    <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                                        {{ $log->ip_address }}
                                    </span>
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
