@extends("layouts.app")
@section("title", "Projects")
@section("page_title", "Projects")
@section("page_subtitle", "Manage your repositories and deployments.")

@section("actions")
    <a class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20 hover:shadow-blue-600/40 transform active:scale-95" href="{{ route("console.projects.create") }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
        </svg>
        Initialize
    </a>
@endsection

@section("content")
    {{-- 
        X-DATA LOGIC:
        1. viewMode: Grid/List switch.
        2. search: String pencarian.
        3. filterStatus: Status yang dipilih (active/maintenance).
        4. showFilter: Toggle dropdown filter.
        5. showSort: Toggle dropdown sort.
    --}}
    <div x-data="{
        viewMode: localStorage.getItem('project_view_mode') || 'grid',
        search: '',
        filterStatus: 'all',
        showFilter: false,
        showSort: false,
        setView(mode) {
            this.viewMode = mode;
            localStorage.setItem('project_view_mode', mode);
        },
        // Logika Filter untuk setiap item
        isVisible(el) {
            const name = el.dataset.name.toLowerCase();
            const status = el.dataset.status.toLowerCase();
            const uuid = el.dataset.uuid.toLowerCase();
            const query = this.search.toLowerCase();
    
            const matchesSearch = name.includes(query) || uuid.includes(query);
            const matchesFilter = this.filterStatus === 'all' || status === this.filterStatus;
    
            return matchesSearch && matchesFilter;
        }
    }">

        {{-- Control Bar --}}
        <div class="sticky top-0 z-30 mb-8 flex flex-col gap-4 rounded-2xl bg-white/80 p-2 backdrop-blur-xl border border-zinc-200 shadow-sm md:flex-row md:items-center md:justify-between transition-all">

            {{-- SEARCH BAR --}}
            <div class="flex flex-1 items-center gap-2 min-w-0">
                <div class="relative flex-1 group">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-zinc-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <input class="block w-full rounded-xl border-0 bg-zinc-100/50 py-2.5 pl-10 pr-3 text-sm text-zinc-900 placeholder:text-zinc-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all shadow-inner font-medium truncate" placeholder="Search repositories..." type="text" x-model="search">
                </div>
            </div>

            {{-- ACTION GROUP (FIX: Hapus overflow-x-auto agar dropdown tidak kepotong) --}}
            <div class="flex items-center gap-2 pl-2 flex-shrink-0">

                {{-- 1. FILTER DROPDOWN --}}
                <div class="relative">
                    <button :class="filterStatus !== 'all' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-white text-zinc-600 border-zinc-200'" @click.outside="showFilter = false" @click="showFilter = !showFilter" class="flex items-center gap-2 px-3 py-2.5 hover:bg-zinc-50 border rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm whitespace-nowrap z-20 relative">
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        {{-- Teks hanya muncul di layar SM ke atas --}}
                        <span class="hidden sm:inline" x-text="filterStatus === 'all' ? 'Status' : filterStatus"></span>
                        <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </button>

                    {{-- Dropdown Menu (Z-INDEX 50 PENTING) --}}
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-zinc-100 z-50 overflow-hidden py-1 ring-1 ring-black/5" x-cloak x-show="showFilter" x-transition.origin.top.right>
                        <div class="px-3 py-2 border-b border-zinc-50 bg-zinc-50/50">
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Filter By Status</span>
                        </div>
                        <button @click="filterStatus = 'all'; showFilter = false" class="w-full text-left px-4 py-2.5 text-xs font-medium hover:bg-blue-50 hover:text-blue-700 flex items-center justify-between group transition-colors">
                            <span :class="filterStatus === 'all' ? 'text-blue-700 font-bold' : 'text-zinc-600'">All Projects</span>
                            <span class="text-blue-600" x-show="filterStatus === 'all'">✓</span>
                        </button>
                        <button @click="filterStatus = 'active'; showFilter = false" class="w-full text-left px-4 py-2.5 text-xs font-medium hover:bg-emerald-50 hover:text-emerald-700 flex items-center justify-between group transition-colors">
                            <span :class="filterStatus === 'active' ? 'text-emerald-700 font-bold' : 'text-zinc-600'">Active Only</span>
                            <span class="text-emerald-600" x-show="filterStatus === 'active'">✓</span>
                        </button>
                        <button @click="filterStatus = 'maintenance'; showFilter = false" class="w-full text-left px-4 py-2.5 text-xs font-medium hover:bg-amber-50 hover:text-amber-700 flex items-center justify-between group transition-colors">
                            <span :class="filterStatus === 'maintenance' ? 'text-amber-700 font-bold' : 'text-zinc-600'">Maintenance</span>
                            <span class="text-amber-600" x-show="filterStatus === 'maintenance'">✓</span>
                        </button>
                    </div>
                </div>

                {{-- 2. SORT BUTTON --}}
                <div class="relative">
                    <button @click.outside="showSort = false" @click="showSort = !showSort" class="flex items-center gap-2 px-3 py-2.5 bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-600 rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm whitespace-nowrap z-20 relative">
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        <span class="hidden sm:inline">Sort</span>
                    </button>
                    {{-- Sort Dropdown --}}
                    <div class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-2xl border border-zinc-100 z-50 overflow-hidden py-1 ring-1 ring-black/5" x-cloak x-show="showSort" x-transition.origin.top.right>
                        <div class="px-3 py-2 border-b border-zinc-50 bg-zinc-50/50">
                            <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Sort Order</span>
                        </div>
                        <a class="block px-4 py-2.5 text-xs font-medium text-zinc-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" href="?sort=newest">Newest First</a>
                        <a class="block px-4 py-2.5 text-xs font-medium text-zinc-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" href="?sort=oldest">Oldest First</a>
                        <a class="block px-4 py-2.5 text-xs font-medium text-zinc-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" href="?sort=name_asc">Name (A-Z)</a>
                    </div>
                </div>

                <div class="h-6 w-px bg-zinc-200 mx-1"></div>

                {{-- 3. VIEW TOGGLE --}}
                <div class="flex bg-zinc-100/50 p-1 rounded-xl border border-zinc-200/50">
                    <button :class="viewMode === 'grid' ? 'bg-white text-blue-600 shadow-sm ring-1 ring-zinc-200' : 'text-zinc-400 hover:text-zinc-600 hover:bg-zinc-200/50'" @click="setView('grid')" class="p-1.5 rounded-lg transition-all" title="Grid">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </button>
                    <button :class="viewMode === 'list' ? 'bg-white text-blue-600 shadow-sm ring-1 ring-zinc-200' : 'text-zinc-400 hover:text-zinc-600 hover:bg-zinc-200/50'" @click="setView('list')" class="p-1.5 rounded-lg transition-all" title="List">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Project Container --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
                {{-- Dynamic Count Label --}}
                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest">
                    Repositories <span x-cloak x-show="search">(Filtered)</span>
                </h3>
            </div>

            {{-- 
                WRAPPER LOOP 
                Kita tempelkan data attributes ke setiap item agar bisa difilter oleh JS
            --}}
            <div :class="viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6' : 'space-y-3'">
                @forelse ($projects as $project)
                    {{-- WRAPPER ITEM (Mengandung Data untuk Filter) --}}
                    <div class="h-full" data-name="{{ $project->name }}" data-status="{{ $project->status ?? "active" }}" data-uuid="{{ $project->id }}" x-show="isVisible($el)">

                        {{-- ========= TAMPILAN GRID ========= --}}
                        <a class="group relative flex flex-col justify-between rounded-3xl bg-white p-6 shadow-sm border border-zinc-200 hover:border-blue-500/30 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-1 overflow-hidden h-full" href="{{ route("console.projects.show", $project) }}" x-show="viewMode === 'grid'">
                            {{-- (Isi Grid Sama seperti sebelumnya) --}}
                            <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <div>
                                <div class="flex justify-between items-start mb-6">
                                    <div class="h-12 w-12 rounded-2xl bg-zinc-50 border border-zinc-100 flex items-center justify-center text-lg font-black text-zinc-700 group-hover:bg-blue-600 group-hover:text-white group-hover:scale-110 transition-all duration-300 shadow-sm relative z-10">
                                        {{ substr($project->name, 0, 1) }}
                                    </div>
                                    @php
                                        $statusColors = match ($project->status ?? "active") {
                                            "active" => "bg-emerald-50 border-emerald-100 text-emerald-600 group-hover:bg-emerald-100",
                                            "maintenance" => "bg-amber-50 border-amber-100 text-amber-600",
                                            default => "bg-zinc-50 border-zinc-100 text-zinc-600",
                                        };
                                    @endphp
                                    <div class="px-3 py-1 rounded-full border text-[10px] font-bold uppercase tracking-wide transition-colors {{ $statusColors }}">
                                        {{ $project->status ?? "Active" }}
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
                                <div class="text-xs font-bold text-zinc-400 group-hover:text-blue-600 transition-colors flex items-center gap-1">View Details <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg></div>
                            </div>
                        </a>

                        {{-- ========= TAMPILAN LIST ========= --}}
                        <a class="group flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm border border-zinc-200 hover:border-blue-500/30 hover:shadow-md transition-all duration-200 h-full" href="{{ route("console.projects.show", $project) }}" x-show="viewMode === 'list'">
                            {{-- (Isi List Sama seperti sebelumnya) --}}
                            <div class="h-10 w-10 rounded-xl bg-zinc-50 border border-zinc-100 flex items-center justify-center text-sm font-black text-zinc-700 group-hover:bg-blue-600 group-hover:text-white transition-colors shrink-0">
                                {{ substr($project->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                <div class="col-span-1">
                                    <h4 class="text-sm font-bold text-zinc-900 group-hover:text-blue-600 truncate">{{ $project->name }}</h4>
                                    <div class="flex items-center gap-1 text-[10px] text-zinc-400 font-mono mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        </svg>
                                        <span class="truncate">{{ str_replace(["https://", "http://", "github.com/", ".git"], "", $project->repository_url) }}</span>
                                    </div>
                                </div>
                                <div class="hidden md:block col-span-1">
                                    <span class="text-[10px] font-mono text-zinc-400 bg-zinc-50 px-2 py-1 rounded border border-zinc-100">{{ $project->id }}</span>
                                </div>
                                <div class="col-span-1 flex items-center justify-end gap-4">
                                    @php
                                        $statusListColors = match ($project->status ?? "active") {
                                            "active" => "text-emerald-600 bg-emerald-50 border-emerald-100",
                                            "maintenance" => "text-amber-600 bg-amber-50 border-amber-100",
                                            default => "text-zinc-600 bg-zinc-50 border-zinc-100",
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border {{ $statusListColors }}">{{ $project->status ?? "Active" }}</span>
                                    <div class="h-6 w-6 rounded-full bg-zinc-200 border border-white flex items-center justify-center text-[8px] font-bold text-zinc-600">{{ substr(optional($project->owner->first())->name ?? "S", 0, 1) }}</div>
                                </div>
                            </div>
                        </a>
                    </div>

                @empty
                    <div class="col-span-full py-20 text-center rounded-3xl border-2 border-dashed border-zinc-200 bg-zinc-50/50">
                        {{-- Empty State Content --}}
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
    </div>
@endsection
