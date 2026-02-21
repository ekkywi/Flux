<div class="flex items-center justify-between">
    <h3 class="text-sm font-black text-zinc-900 uppercase tracking-widest flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-600"></span> Active Environments</h3>
    @can("update", $project)
        <button class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-all" onclick="openAddEnvModal()">
            + Provision Node
        </button>
    @endcan
</div>
<div class="space-y-4">
    @forelse($project->environments as $env)
        @php
            $isProd = $env->type === "production";
            $borderColor = $isProd ? "border-l-rose-500" : "border-l-blue-500";
            $iconColor = $isProd ? "text-rose-600 bg-rose-50 border-rose-100" : "text-blue-600 bg-blue-50 border-blue-100";
            $badgeColor = $isProd ? "bg-rose-100 text-rose-700" : "bg-blue-100 text-blue-700";
            $isLocked = in_array($project->status, ["maintenance", "archived"]);

            // Logic Delete (Production vs Others)
            $currentUser = auth()->user();
            $myRole = $project->members->firstWhere("id", $currentUser->id)?->pivot->role;
            $isSysAdmin = $currentUser->role === "System Administrator";
            $canDeleteEnv = $env->type !== "production" ? true : $isSysAdmin || $myRole === "owner";
        @endphp

        <div class="bg-white rounded-2xl border border-zinc-200 p-6 shadow-sm hover:shadow-lg hover:shadow-zinc-200/50 transition-all group border-l-4 {{ $borderColor }}">
            <div class="flex flex-col md:flex-row justify-between md:items-center gap-6">
                {{-- Info --}}
                <div class="flex items-start gap-5">
                    <div class="h-12 w-12 rounded-xl {{ $iconColor }} border flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h2 class="text-lg font-black text-zinc-900">{{ $env->name }}</h2>
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest {{ $badgeColor }}">{{ $env->type }}</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-xs font-mono text-zinc-500">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span>{{ $env->branch }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span>Upd. {{ $env->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pl-0 md:pl-6 border-l-0 md:border-l border-zinc-100 w-full md:w-auto justify-end">
                    @if ($canDeleteEnv)
                        @can("update", $project)
                            <button class="h-10 w-10 flex items-center justify-center text-zinc-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-100 rounded-xl transition-all" onclick="confirmDeleteEnv('{{ $env->id }}', '{{ $env->name }}', '{{ $env->type }}')" title="Teardown Environment">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </button>
                        @endcan
                    @endif

                    @php
                        $canDeploy = auth()
                            ->user()
                            ->can("deploy", [$project, $env]);
                    @endphp

                    @if ($canDeploy)
                        <button class="px-6 py-3 rounded-xl bg-zinc-900 text-white font-bold text-xs uppercase tracking-widest transition-all shadow-lg shadow-zinc-900/10 flex items-center gap-2 group-hover:-translate-y-0.5 {{ $isLocked ? "opacity-50 cursor-not-allowed grayscale" : "hover:bg-blue-600" }}" onclick="{{ $isLocked ? "Toast.fire({icon:'warning', title:'Project is locked'})" : "deployConfirm('{$env->name}')" }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                            Deploy
                        </button>
                    @else
                        <button class="px-6 py-3 rounded-xl bg-zinc-100 text-zinc-400 font-bold text-xs uppercase tracking-widest cursor-not-allowed flex items-center gap-2" onclick="{{ $isLocked ? "Toast.fire({icon:'warning', title:'Project is locked'})" : "deployConfirm('{$env->id}', '{$env->name}')" }}" title="Access Denied: Only Managers/Owners can deploy to Production">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                            Locked
                        </button>
                    @endif
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
