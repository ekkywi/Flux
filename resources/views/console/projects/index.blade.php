@extends("layouts.app")
@section("title", "Projects")
@section("page_title", "Projects")
@section("page_subtitle", "Manage your repositories and deployments.")

{{-- Tombol Aksi di Header Kanan --}}
@section("actions")
    <a class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20 hover:shadow-blue-600/40 transform active:scale-95" href="{{ route("console.projects.create") }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
        </svg>
        Initialize Project
    </a>
@endsection

@section("content")

    {{-- Control Bar --}}
    <div class="sticky top-0 z-30 mb-8 flex flex-col gap-4 rounded-2xl bg-white/80 p-2 backdrop-blur-xl border border-zinc-200 shadow-sm md:flex-row md:items-center md:justify-between transition-all">
        <div class="flex flex-1 items-center gap-2">
            <div class="relative flex-1 md:max-w-md group">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-zinc-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
                <input class="block w-full rounded-xl border-0 bg-zinc-100/50 py-2.5 pl-10 pr-3 text-sm text-zinc-900 placeholder:text-zinc-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all shadow-inner font-medium" placeholder="Search repositories..." type="text">
            </div>
            <button class="flex items-center gap-2 px-4 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-600 rounded-xl text-xs font-bold uppercase tracking-wide transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                Filter
            </button>
        </div>
        <div class="hidden md:flex items-center gap-2 border-l border-zinc-200 pl-4 ml-2">
            <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">View</span>
            <button class="p-2 rounded-lg bg-white text-blue-600 shadow-sm ring-1 ring-zinc-200"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg></button>
        </div>
    </div>

    {{-- Grid Project --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Active Repositories ({{ $projects->count() }})</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($projects as $project)
                <a class="group relative flex flex-col justify-between rounded-3xl bg-white p-6 shadow-sm border border-zinc-200 hover:border-blue-500/30 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-1 overflow-hidden" href="{{ route("console.projects.show", $project) }}">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div class="h-12 w-12 rounded-2xl bg-zinc-50 border border-zinc-100 flex items-center justify-center text-lg font-black text-zinc-700 group-hover:bg-blue-600 group-hover:text-white group-hover:scale-110 transition-all duration-300 shadow-sm relative z-10">
                                {{ substr($project->name, 0, 1) }}
                            </div>
                            <div class="px-3 py-1 rounded-full bg-cyan-50 border border-cyan-100 text-[10px] font-bold text-cyan-600 uppercase tracking-wide group-hover:bg-cyan-100 transition-colors">
                                Active
                            </div>
                        </div>

                        <h4 class="text-xl font-bold text-zinc-900 group-hover:text-blue-600 transition-colors tracking-tight">{{ $project->name }}</h4>
                        <p class="text-xs font-mono text-zinc-400 mt-1 truncate">{{ $project->id }}</p>

                        <div class="mt-4 flex items-center gap-2 text-xs font-medium text-zinc-500 bg-zinc-50 w-fit px-3 py-1.5 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all border border-transparent group-hover:border-zinc-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                            <span class="truncate max-w-[150px]">{{ str_replace(["https://", "http://", "github.com/", ".git"], "", $project->repository_url) }}</span>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-zinc-50 flex items-center justify-between relative z-10">
                        <div class="flex -space-x-2">
                            <div class="h-8 w-8 rounded-full border-2 border-white bg-zinc-200 flex items-center justify-center text-[10px] font-bold text-zinc-600">
                                {{ substr(optional($project->owner->first())->name ?? "S", 0, 1) }}
                            </div>
                        </div>
                        <div class="text-xs font-bold text-zinc-400 group-hover:text-blue-600 transition-colors flex items-center gap-1">
                            View Details
                            <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-20 text-center rounded-3xl border-2 border-dashed border-zinc-200 bg-zinc-50/50">
                    <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center text-zinc-300 mb-4 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <h3 class="text-zinc-900 font-bold text-lg">No Projects Found</h3>
                    <p class="text-zinc-500 text-sm mt-1 mb-6">Initialize a new repository to get started.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
