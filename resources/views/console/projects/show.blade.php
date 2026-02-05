@extends("layouts.app")
@section("title", $project->name)
@section("page_title", "Project Control Panel")

@section("content")
    <div class="space-y-8 pb-20 text-slate-900">

        {{-- 1. PROJECT HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <div class="h-1 w-6 bg-indigo-600 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Project Identity: {{ $project->id }}</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">{{ $project->name }}</h1>
                <p class="text-xs text-slate-500 font-medium font-mono">
                    REPO_URL // <span class="text-indigo-600 underline">{{ $project->repository_url }}</span>
                </p>
            </div>

            <div class="flex items-center gap-4">
                <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-50 transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2.5" />
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2.5" />
                    </svg>
                    Settings
                </button>
            </div>
        </div>

        {{-- 2. ENVIRONMENT GRID (THE CORE) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @foreach ($project->environments as $env)
                <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 shadow-sm flex flex-col justify-between group hover:shadow-xl hover:shadow-slate-200/50 transition-all">
                    <div>
                        <div class="flex items-center justify-between mb-8">
                            <span class="px-4 py-1.5 bg-slate-100 text-slate-500 text-[9px] font-black uppercase tracking-widest rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                {{ $env->name }}
                            </span>
                            @if ($env->server_app_id)
                                <span class="flex items-center gap-1.5 text-emerald-600 text-[9px] font-black uppercase tracking-widest">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Provisioned
                                </span>
                            @else
                                <span class="flex items-center gap-1.5 text-slate-300 text-[9px] font-black uppercase tracking-widest">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-200"></span>
                                    Standby
                                </span>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Access Port</p>
                                <p class="text-xl font-black text-slate-900 font-mono">{{ $env->assigned_port }}</p>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Target Host</p>
                                @if ($env->server_app_id)
                                    <p class="text-sm font-bold text-slate-700 tracking-tight">{{ $env->appServer->name }} // {{ $env->appServer->ip_address }}</p>
                                @else
                                    <p class="text-sm font-bold text-rose-400 italic tracking-tight">No server assigned</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-slate-50">
                        @if (!$env->server_app_id)
                            <button class="w-full py-4 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200" onclick="toggleAssignModal('{{ $env->id }}', '{{ $env->name }}')">
                                Assign Infrastructure
                            </button>
                        @else
                            <form action="{{ route("console.projects.deploy", $env->id) }}" method="POST">
                                @csrf
                                <button class="w-full py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                                    </svg>
                                    Deploy Cluster
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 3. ASSIGN SERVER MODAL --}}
    <div class="fixed inset-0 z-[100] items-center justify-center hidden bg-slate-900/60 backdrop-blur-sm px-4" id="assignModal">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl border border-slate-200 transform transition-all">
            <div class="mb-8">
                <div class="h-1.5 w-12 bg-indigo-600 rounded-full mb-4"></div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Assign Infrastructure</h3>
                <p class="text-xs text-slate-500 font-medium">Select a physical server node for environment: <span class="text-indigo-600 font-bold uppercase" id="env_name_display"></span></p>
            </div>

            <form action="" class="space-y-5" id="assignForm" method="POST">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Available Server Nodes</label>
                    <select class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-bold text-slate-700 appearance-none" name="server_id" required>
                        <option disabled selected value="">Select Node</option>
                        @foreach ($servers as $server)
                            <option value="{{ $server->id }}">{{ $server->name }} ({{ $server->ip_address }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-4 pt-4">
                    <button class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleAssignModal()" type="button">Abort</button>
                    <button class="flex-1 px-6 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200" type="submit">Link Server</button>
                </div>
            </form>
        </div>
    </div>

    @push("scripts")
        <script>
            function toggleAssignModal(envId = null, envName = null) {
                const modal = document.getElementById('assignModal');
                const form = document.getElementById('assignForm');
                const display = document.getElementById('env_name_display');

                if (envId) {
                    form.action = `/console/projects/environments/${envId}/assign-server`;
                    display.textContent = envName;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }
        </script>
    @endpush
@endsection
