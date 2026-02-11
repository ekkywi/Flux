@extends("layouts.app")

{{-- Konfigurasi Header --}}
@section("title", "Dashboard")
@section("page_title", "Command Center")
@section("page_subtitle", "System overview and performance metrics.")

{{-- Tombol Aksi di Header Kanan --}}
@section("actions")
    <div class="flex items-center gap-3">
        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest hidden md:block">
            Last Sync: {{ now()->format("H:i") }}
        </span>
        <a class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20 hover:shadow-blue-600/40 active:scale-95" href="{{ route("console.projects.create") }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
            </svg>
            <span class="hidden md:inline">Quick Deploy</span>
        </a>
    </div>
@endsection

@section("content")
    {{-- A. WELCOME BANNER (Background Midnight Blue - Senada Sidebar) --}}
    <div class="relative overflow-hidden rounded-3xl bg-[#0B1120] p-8 text-white shadow-xl shadow-blue-900/10 mb-8 border border-blue-900/30 group">
        {{-- Decorative Glows --}}
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-blue-600 rounded-full blur-[80px] opacity-20 group-hover:opacity-30 transition-opacity duration-1000"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-cyan-500 rounded-full blur-[80px] opacity-10 group-hover:opacity-20 transition-opacity duration-1000"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-white">Welcome back, {{ Auth::user()->first_name }}! 👋</h2>
                <p class="text-zinc-400 mt-2 max-w-xl text-sm leading-relaxed font-medium">
                    System integrity is at <span class="text-cyan-400 font-bold">100%</span>. You have <span class="text-white font-bold underline decoration-blue-500 underline-offset-4">{{ $pendingCount ?? 0 }} pending tasks</span> requiring your attention today.
                </p>
            </div>

            {{-- Mini Stats --}}
            <div class="flex items-center gap-4 bg-white/5 p-2 pr-6 rounded-2xl border border-white/10 backdrop-blur-md">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">System Load</p>
                    <p class="text-lg font-black text-white">Optimal</p>
                </div>
            </div>
        </div>
    </div>

    {{-- B. STATS GRID (Bento Style) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Card 1 --}}
        <div class="bg-white p-6 rounded-3xl border border-zinc-200 shadow-sm hover:border-blue-300 hover:shadow-lg hover:shadow-blue-500/5 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-cyan-600 bg-cyan-50 px-2 py-1 rounded-lg">+12%</span>
            </div>
            <p class="text-sm font-bold text-zinc-400 uppercase tracking-widest">Total Projects</p>
            <p class="text-4xl font-black text-zinc-900 mt-1 tracking-tight">24</p>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white p-6 rounded-3xl border border-zinc-200 shadow-sm hover:border-blue-300 hover:shadow-lg hover:shadow-blue-500/5 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 bg-zinc-100 text-zinc-600 rounded-xl group-hover:bg-zinc-800 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" stroke-width="2" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-zinc-400 bg-zinc-50 px-2 py-1 rounded-lg">Stable</span>
            </div>
            <p class="text-sm font-bold text-zinc-400 uppercase tracking-widest">Active Nodes</p>
            <div class="flex items-baseline gap-1 mt-1">
                <p class="text-4xl font-black text-zinc-900 tracking-tight">08</p>
                <span class="text-sm font-bold text-zinc-400">/ 10</span>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white p-6 rounded-3xl border border-zinc-200 shadow-sm hover:border-blue-300 hover:shadow-lg hover:shadow-blue-500/5 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 bg-violet-50 text-violet-600 rounded-xl group-hover:bg-violet-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" />
                    </svg>
                </div>
                <div class="flex -space-x-2">
                    <div class="h-8 w-8 rounded-full bg-zinc-200 border-2 border-white"></div>
                    <div class="h-8 w-8 rounded-full bg-zinc-300 border-2 border-white"></div>
                    <div class="w-8 h-8 rounded-full bg-zinc-100 flex items-center justify-center text-[9px] font-bold text-zinc-500 border-2 border-white">+3</div>
                </div>
            </div>
            <p class="text-sm font-bold text-zinc-400 uppercase tracking-widest">Personnel</p>
            <p class="text-4xl font-black text-zinc-900 mt-1 tracking-tight">14</p>
        </div>
    </div>

    {{-- C. RECENT ACTIVITY TABLE --}}
    <div class="rounded-3xl bg-white border border-zinc-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-zinc-100 flex items-center justify-between">
            <h3 class="font-bold text-zinc-900 text-lg">Recent Deployment Activity</h3>
            <a class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline uppercase tracking-wide" href="#">View Full Logs</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-50/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Project</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Environment</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @for ($i = 0; $i < 3; $i++)
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">F</div>
                                    <span class="text-sm font-bold text-zinc-700">Flux_Core_API</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-zinc-100 text-zinc-600 text-xs font-bold font-mono">
                                    <svg class="w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    production
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-cyan-600 text-[11px] font-bold uppercase tracking-wide bg-cyan-50 px-2 py-1 rounded-full border border-cyan-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                                    Deployed
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xs font-medium text-zinc-400 font-mono">2 mins ago</span>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
@endsection
