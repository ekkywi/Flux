<div class="flex items-center justify-between">
    <h3 class="text-sm font-black text-zinc-900 uppercase tracking-widest flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-600"></span> Active Environments</h3>
    @can("update", $project)
        <button class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-all" onclick="openAddEnvModal()">
            + Provision Node
        </button>
    @endcan
</div>

<div class="space-y-4" id="environments-list">
    @forelse($project->environments as $env)

        <div @class([
            "bg-white rounded-2xl border border-zinc-200 p-6 shadow-sm hover:shadow-lg hover:shadow-zinc-200/50 transition-all group border-l-4",
            "border-l-rose-500" => $env->is_prod,
            "border-l-blue-500" => !$env->is_prod,
            "ring-2 ring-indigo-500/30" => $env->has_update,
        ])>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">

                {{-- ================= LEFT COLUMN: INFO & METADATA ================= --}}
                <div class="flex items-start gap-4 flex-1 w-full min-w-0">
                    <div @class([
                        "h-12 w-12 rounded-xl border flex items-center justify-center shrink-0 shadow-sm mt-0.5",
                        "text-rose-600 bg-rose-50 border-rose-100" => $env->is_prod,
                        "text-blue-600 bg-blue-50 border-blue-100" => !$env->is_prod,
                    ])>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        {{-- Judul & Badge --}}
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h2 class="text-lg font-black text-zinc-900 truncate max-w-full">{{ $env->name }}</h2>
                            <span @class([
                                "px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest",
                                "bg-rose-100 text-rose-700" => $env->is_prod,
                                "bg-blue-100 text-blue-700" => !$env->is_prod,
                            ])>{{ $env->type }}</span>

                            @if ($env->has_update)
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-indigo-100 text-indigo-700 flex items-center gap-1 animate-pulse border border-indigo-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Update Ready
                                </span>
                            @endif
                        </div>

                        {{-- URL --}}
                        @if ($env->port && $env->status === "running")
                            <a class="text-xs font-bold text-blue-500 hover:text-blue-700 flex items-center gap-1 mb-3 transition-colors w-fit truncate max-w-full" href="http://{{ $env->server->ip_address }}:{{ $env->port }}" target="_blank">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span class="truncate">http://{{ $env->server->ip_address }}:{{ $env->port }}</span>
                            </a>
                        @endif

                        {{-- Info Box --}}
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-mono text-zinc-500 bg-zinc-50 w-fit px-3 py-1.5 rounded-lg border border-zinc-100 mt-2">
                            <div class="flex items-center gap-1.5" title="Git Branch">
                                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span class="truncate max-w-[100px]">{{ $env->branch }}</span>
                            </div>
                            <span class="text-zinc-300 hidden sm:inline">|</span>
                            <div class="flex items-center gap-1.5" title="Deployed Hash">
                                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span>{{ $env->deployed_commit_hash ? substr($env->deployed_commit_hash, 0, 7) : "No Commit" }}</span>
                            </div>
                            <span class="text-zinc-300 hidden sm:inline">|</span>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span>{{ $env->updated_at->diffForHumans(null, true, true) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= RIGHT COLUMN: ACTIONS ================= --}}
                <div class="flex flex-col md:items-end justify-center gap-3 w-full md:w-auto mt-4 md:mt-0 pt-4 md:pt-0 border-t md:border-t-0 md:border-l border-zinc-100 md:pl-6 shrink-0">

                    {{-- ROW 1: PRIMARY ACTIONS --}}
                    <div class="flex items-center flex-wrap md:justify-end gap-2 w-full">
                        @can("update", $project)
                            <form action="{{ route("console.environments.toggle-auto-deploy", $env->id) }}" class="m-0" method="POST">
                                @csrf
                                <button class="h-9 px-3 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all border flex items-center gap-1.5 {{ $env->auto_deploy ? "bg-green-50 text-green-700 border-green-200 hover:bg-green-100 shadow-sm" : "bg-white text-zinc-400 border-zinc-200 hover:bg-zinc-50" }}" title="Toggle Auto Deployment on Git Push" type="submit">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $env->auto_deploy ? "bg-green-500 animate-pulse" : "bg-zinc-300" }}"></span>
                                    {{ $env->auto_deploy ? "Auto: ON" : "Auto: OFF" }}
                                </button>
                            </form>
                        @endcan

                        @can("deploy", $env)
                            @if (in_array($env->status, ["stopping", "deploying", "queued", "starting"]))
                                <button class="h-9 px-4 rounded-xl bg-zinc-100 text-zinc-400 font-bold text-[10px] uppercase tracking-widest cursor-not-allowed flex items-center gap-2 shrink-0" disabled>
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    Processing
                                </button>
                            @else
                                @if ($env->status === "running")
                                    <form action="{{ route("console.projects.environments.stop", [$project, $env]) }}" class="m-0" id="stop-form-{{ $env->id }}" method="POST">
                                        @csrf
                                        <button class="h-9 w-10 rounded-xl bg-rose-50 border border-rose-100 hover:bg-rose-100 text-rose-600 transition-all flex items-center justify-center shadow-sm shrink-0" onclick="stopConfirm('{{ $env->id }}', '{{ $env->name }}')" title="Stop Node" type="button">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M6 6h12v12H6z" />
                                            </svg>
                                        </button>
                                    </form>
                                @elseif ($env->status === "stopped")
                                    <form action="{{ route("console.projects.environments.start", [$project, $env]) }}" class="m-0" id="start-form-{{ $env->id }}" method="POST">
                                        @csrf
                                        <button class="h-9 w-10 rounded-xl bg-emerald-50 border border-emerald-100 hover:bg-emerald-100 text-emerald-600 transition-all flex items-center justify-center shadow-sm shrink-0" onclick="startConfirm('{{ $env->id }}', '{{ $env->name }}')" title="Start Node" type="button">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                                @if ($env->has_update && !$env->auto_deploy)
                                    <form action="{{ route("console.environments.deploy-update", $env->id) }}" class="m-0 shrink-0" method="POST">
                                        @csrf
                                        <button class="h-9 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold uppercase tracking-widest rounded-xl transition-all shadow-md shadow-indigo-500/30 flex items-center gap-2 group relative overflow-hidden shrink-0" type="submit">
                                            <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                                            <svg class="w-4 h-4 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                                            </svg>
                                            <span class="relative z-10">Deploy {{ substr($env->latest_commit_hash, 0, 7) }}</span>
                                        </button>
                                    </form>
                                @else
                                    <button class="h-9 px-5 rounded-xl bg-zinc-900 text-white font-bold text-[10px] uppercase tracking-widest transition-all shadow-md flex items-center gap-2 shrink-0 hover:-translate-y-0.5 {{ $project->is_locked ? "opacity-50 cursor-not-allowed grayscale" : "hover:bg-blue-600" }}" onclick="{{ $project->is_locked ? "Toast.fire({icon:'warning', title:'Project is locked'})" : "deployConfirm('{$env->id}', '{$env->name}')" }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        {{ $env->status === "uninitialized" ? "Deploy" : "Redeploy" }}
                                    </button>
                                @endif
                            @endif
                        @else
                            <button class="h-9 px-4 rounded-xl bg-zinc-100 text-zinc-400 font-bold text-[10px] uppercase tracking-widest cursor-not-allowed flex items-center gap-2 shrink-0" title="Access Denied: Production Locked">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                Locked
                            </button>
                        @endcan
                    </div>

                    {{-- ROW 2: UTILITY TOOLBAR --}}
                    <div class="flex items-center justify-start md:justify-end w-full">
                        <div class="flex items-center p-1 rounded-xl bg-zinc-50/80 border border-zinc-200/60 shadow-inner w-fit">
                            @if ($env->deployments()->count() > 0)
                                <button class="p-1.5 rounded-lg text-zinc-400 hover:text-blue-600 hover:bg-white hover:shadow-sm transition-all" onclick="openTerminal('{{ $env->id }}', '{{ $env->name }}')" title="View Deployment Logs" type="button">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </button>
                            @endif

                            @can("update", $project)
                                @if ($env->status === "running")
                                    <button class="p-1.5 rounded-lg text-zinc-400 hover:text-emerald-500 hover:bg-white hover:shadow-sm transition-all" onclick="openWebCLI('{{ $env->id }}', '{{ $env->name }}', '{{ route("console.projects.environments.command", [$project, $env]) }}')" title="Ad-Hoc Web CLI" type="button">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        </svg>
                                    </button>
                                @endif

                                <button class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 hover:bg-white hover:shadow-sm transition-all" onclick="openEnvSettings('{{ $env->id }}', '{{ $env->name }}', `{{ base64_encode($env->deploy_script) }}`)" title="Environment Settings" type="button">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </button>
                            @endcan

                            @can("delete", $env)
                                <div class="w-px h-4 bg-zinc-200 mx-1"></div>
                                <button class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-white hover:shadow-sm transition-all" onclick="confirmDeleteEnv('{{ $env->id }}', '{{ $env->name }}', '{{ $env->type }}')" title="Delete Environment" type="button">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-zinc-50 rounded-2xl border-2 border-dashed border-zinc-200 p-10 flex flex-col items-center justify-center text-center">
            <div class="h-14 w-14 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm border border-zinc-100"><svg class="w-6 h-6 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg></div>
            <h3 class="text-sm font-black text-zinc-900 uppercase tracking-widest mb-1">No Environments</h3>
            <p class="text-xs text-zinc-500">Initialize a new environment to begin deployment sequence.</p>
        </div>
    @endforelse
</div>
