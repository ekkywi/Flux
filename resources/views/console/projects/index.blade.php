@extends("layouts.app")
@section("title", "Project Inventory")
@section("page_title", "Orchestration Core")

@section("content")
    <div class="space-y-10 pb-20 text-slate-900">

        {{-- 1. HEADER DENGAN METRICS --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <div class="h-1.5 w-8 bg-indigo-600 rounded-full"></div>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em]">Infrastructure Fleet</span>
                </div>
                <h1 class="text-4xl font-black tracking-tighter text-slate-900">Project Inventory</h1>
                <p class="text-xs text-slate-500 font-medium italic">
                    Managing <span class="text-indigo-600 font-bold">{{ $projects->count() }} active services</span> within the cluster.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-4 px-6 py-3 bg-white border border-slate-200 rounded-[20px] shadow-sm">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Healthy Nodes</span>
                        <span class="text-lg font-black text-emerald-600">{{ $projects->where("onboarding_status", "ready")->count() }}</span>
                    </div>
                    <div class="w-px h-8 bg-slate-100"></div>
                    <button class="px-5 py-2 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 transition-all shadow-lg" onclick="toggleOnboardModal()">
                        + New Onboard
                    </button>
                </div>
            </div>
        </div>

        {{-- 2. MODERN GRID CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            @foreach ($projects as $project)
                <div class="group relative bg-white rounded-[2.5rem] border border-slate-200 p-8 hover:shadow-2xl hover:shadow-slate-200/60 transition-all duration-500 hover:-translate-y-2 overflow-hidden">

                    {{-- Status Glow Background (Subtle) --}}
                    <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full {{ $project->onboarding_status === "ready" ? "bg-emerald-50" : "bg-amber-50" }} blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>

                    <div class="relative z-10 flex flex-col h-full">
                        {{-- Card Top: Stack & Status --}}
                        <div class="flex justify-between items-start mb-10">
                            <div class="h-14 w-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500 shadow-sm border border-slate-100 group-hover:border-indigo-400">
                                @if ($project->stack_type === "laravel")
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" stroke-width="2.5" />
                                    </svg>
                                @else
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2.5" />
                                    </svg>
                                @endif
                            </div>

                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ $project->onboarding_status === "ready" ? "bg-emerald-50 text-emerald-600" : "bg-amber-50 text-amber-600" }} text-[9px] font-black uppercase tracking-widest border {{ $project->onboarding_status === "ready" ? "border-emerald-100" : "border-amber-100" }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $project->onboarding_status === "ready" ? "bg-emerald-500 animate-pulse" : "bg-amber-400" }}"></span>
                                {{ $project->onboarding_status === "ready" ? "Operational" : "Needs Setup" }}
                            </span>
                        </div>

                        {{-- Project Info --}}
                        <div class="mb-8">
                            <h3 class="text-xl font-black text-slate-900 tracking-tight group-hover:text-indigo-600 transition-colors mb-1">{{ $project->name }}</h3>
                            <p class="text-[10px] font-mono text-slate-400 truncate tracking-tighter">{{ $project->repository_url }}</p>
                        </div>

                        {{-- Environment Progress --}}
                        <div class="flex items-center gap-2 mb-10">
                            @foreach ($project->environments as $env)
                                <div class="flex-1 h-1.5 rounded-full {{ $env->server_app_id ? "bg-indigo-600" : "bg-slate-100" }}" title="{{ $env->name }}"></div>
                            @endforeach
                        </div>

                        {{-- Footer: Meta & Action --}}
                        <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-[0.2em] mb-0.5">Stack Protocol</span>
                                <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $project->stack_type }} // PHP {{ $project->stack_options["php_version"] ?? "8.x" }}</span>
                            </div>

                            <a class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all border border-slate-100" href="{{ route("console.projects.show", $project->id) }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 3. ONBOARD MODAL (Identical Style) --}}
    <div class="fixed inset-0 z-[100] items-center justify-center hidden bg-slate-900/60 backdrop-blur-sm px-4" id="onboardModal">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl border border-slate-200 transform transition-all">
            <div class="mb-8">
                <div class="h-1.5 w-12 bg-indigo-600 rounded-full mb-4"></div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Onboard Project</h3>
                <p class="text-xs text-slate-500 font-medium">Inject repository into the automated orchestration pipeline.</p>
            </div>

            <form action="{{ route("console.projects.store") }}" class="space-y-5" id="onboardForm" method="POST">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Project Name</label>
                    <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-bold" name="name" placeholder="Flux Internal System" required type="text">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Repository URL (Gitea)</label>
                    <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm font-mono focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-bold text-indigo-600" name="repository_url" placeholder="http://.../repo.git" required type="url">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Stack</label>
                        <select class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-bold" name="stack_type" required>
                            <option value="laravel">LARAVEL</option>
                            <option value="nodejs">NODE.JS</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">PHP Version</label>
                        <select class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-bold" name="stack_options[php_version]">
                            <option value="8.2">PHP 8.2</option>
                            <option selected value="8.3">PHP 8.3</option>
                            <option value="8.4">PHP 8.4</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleOnboardModal()" type="button">Abort</button>
                    <button class="flex-1 px-6 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200" type="submit">Initialize</button>
                </div>
            </form>
        </div>
    </div>

    @push("scripts")
        <script>
            function toggleOnboardModal() {
                const modal = document.getElementById('onboardModal');
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
            }

            document.getElementById('onboardForm').onsubmit = function() {
                Swal.fire({
                    title: 'INITIALIZING_',
                    html: '<p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Registering webhooks & validating repository...</p>',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    customClass: {
                        popup: 'flux-popup',
                        title: 'flux-title'
                    }
                });
            };
        </script>
    @endpush
@endsection
