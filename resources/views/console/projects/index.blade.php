@extends("layouts.app")
@section("title", "Project Console")
@section("page_title", "Deployment Matrix")

@section("content")
    <div class="space-y-8 pb-20 text-slate-900">

        {{-- 1. NOTIFICATION SYSTEM --}}
        @if (session("success") || session("error"))
            <div class="fixed top-4 right-4 z-[60] {{ session("success") ? "bg-emerald-500" : "bg-rose-500" }} text-white px-6 py-3 rounded-2xl shadow-2xl animate-bounce">
                <p class="text-xs font-black uppercase tracking-widest">
                    {{ session("success") ?? session("error") }}
                </p>
            </div>
        @endif

        {{-- 2. STREAMLINED HEADER (Flux Style) --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <div class="h-1 w-6 bg-indigo-600 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Deployment Protocol</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Project Matrix</h1>
                <p class="text-xs text-slate-500 font-medium">
                    Orchestrating <span class="text-indigo-600 font-bold">{{ $projects->count() }} active repositories</span> within the network.
                </p>
            </div>

            {{-- Compact Stats & Actions --}}
            <div class="flex items-center gap-4 px-5 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm">
                {{-- Stat 1 --}}
                <div class="text-center min-w-[50px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Total</span>
                    <span class="text-sm font-black text-slate-900">{{ $projects->count() }}</span>
                </div>
                <div class="w-px h-6 bg-slate-100"></div>

                {{-- Stat 2 --}}
                <div class="text-center min-w-[50px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Healthy</span>
                    <span class="text-sm font-black text-emerald-600">{{ $projects->count() }}</span> {{-- Logic health check bisa ditambahkan nanti --}}
                </div>
                <div class="w-px h-6 bg-slate-100"></div>

                {{-- Action Button --}}
                {{-- Kita arahkan ke Route Create Wizard yang sudah kita buat --}}
                <a class="px-4 py-1.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-indigo-600 transition-all shadow-md flex items-center gap-2" href="{{ route("console.projects.create") }}">
                    + Initialize
                </a>
            </div>
        </div>

        {{-- 3. PROJECT GRID (Tetap Grid karena Project lebih cocok Card daripada Table) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($projects as $project)
                {{-- CARD MODULE (FULL CLICKABLE) --}}
                <a class="group relative bg-white rounded-[2rem] border border-slate-200 hover:border-indigo-500 transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/10 overflow-hidden flex flex-col h-full cursor-pointer" href="{{ route("console.projects.show", $project) }}">

                    {{-- Card Header --}}
                    <div class="p-6 pb-0 flex justify-between items-start">
                        <div class="h-14 w-14 rounded-2xl bg-slate-50 border border-slate-100 text-slate-900 flex items-center justify-center font-black text-xl group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-500 transition-colors shadow-sm">
                            {{ substr($project->name, 0, 1) }}
                        </div>

                        <div class="flex items-center gap-2 px-2 py-1 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Active</span>
                        </div>
                    </div>

                    {{-- Card Content --}}
                    <div class="p-6 flex-1">
                        <h3 class="text-xl font-black text-slate-900 tracking-tight mb-1 group-hover:text-indigo-600 transition-colors">
                            {{ $project->name }}
                        </h3>
                        <div class="text-[10px] font-mono text-slate-400 mb-4 truncate">
                            ID: {{ $project->id }}
                        </div>

                        <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-100 group-hover:bg-white group-hover:border-indigo-100 transition-colors">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                            <span class="text-[10px] font-bold text-indigo-600 truncate font-mono w-full">
                                {{ str_replace(["https://", "http://", "github.com/", ".git"], "", $project->repository_url) }}
                            </span>
                        </div>
                    </div>

                    {{-- Metrics Strip --}}
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 grid grid-cols-2 gap-4 group-hover:bg-slate-50/80">
                        <div>
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest">Environments</span>
                            <span class="block text-sm font-black text-slate-800">{{ $project->environments->count() }} <span class="text-[10px] text-slate-400 font-medium">Nodes</span></span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest">Owner</span>
                            <span class="block text-sm font-black text-slate-800 truncate">
                                {{ optional($project->owner->first())->name ?? "SYSTEM" }}
                            </span>
                        </div>
                    </div>

                    {{-- Hover Indicator --}}
                    <div class="absolute inset-x-0 bottom-0 h-1 bg-indigo-600 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
                </a>

            @empty
                {{-- EMPTY STATE --}}
                <div class="col-span-3 flex flex-col items-center justify-center py-24 border-2 border-dashed border-slate-200 rounded-[2.5rem] bg-slate-50/50">
                    <div class="h-20 w-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-xl shadow-slate-200/50">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight mb-2">NO SIGNALS DETECTED</h3>
                    <p class="text-xs text-slate-500 font-medium mb-8">The matrix is empty. Initialize a new deployment target to begin.</p>
                    <a class="px-8 py-3 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg transition-all" href="{{ route("console.projects.create") }}">
                        Initialize Target
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Script untuk Toast Auto-Hide --}}
    @push("scripts")
        <script>
            setTimeout(() => {
                document.querySelectorAll('.animate-bounce').forEach(t => t.style.display = 'none');
            }, 4000);
        </script>
    @endpush
@endsection
