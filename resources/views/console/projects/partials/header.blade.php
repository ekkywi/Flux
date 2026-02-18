<div class="bg-white rounded-[2rem] border border-zinc-200 p-8 shadow-sm relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-50 to-transparent rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

    <div class="relative z-10 flex flex-col xl:flex-row xl:items-start justify-between gap-6">
        <div class="space-y-3 flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-4xl font-black tracking-tighter text-zinc-900">{{ $project->name }}</h1>
                @php
                    $statusColors = match ($project->status ?? "active") {
                        "active" => "bg-emerald-100 text-emerald-700 border-emerald-200",
                        "maintenance" => "bg-amber-100 text-amber-700 border-amber-200",
                        "archived" => "bg-zinc-100 text-zinc-600 border-zinc-200",
                        default => "bg-zinc-100 text-zinc-600 border-zinc-200",
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusColors }}">{{ $project->status ?? "ACTIVE" }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-zinc-500">
                <div class="flex items-center gap-1.5 cursor-pointer hover:text-blue-600 transition-colors" onclick="copyToClipboard('{{ $project->id }}', 'UUID Copied!')">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span class="font-mono">{{ $project->id }}</span>
                </div>
                <span class="text-zinc-300">|</span>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span>Created {{ $project->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
            <div class="flex items-center gap-2">
                <a class="flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 text-zinc-600 hover:text-zinc-900 hover:border-zinc-300 text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-sm group" href="{{ $project->repository_url }}" target="_blank">
                    <svg class="w-4 h-4 text-zinc-400 group-hover:text-[#181717] transition-colors" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                    </svg>
                    <span>Repo</span>
                </a>

                @can("update", $project)
                    <button class="flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 text-zinc-600 hover:text-zinc-900 hover:border-zinc-300 text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-sm" onclick="openEditProjectModal()">
                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        <span>Settings</span>
                    </button>
                @endcan

                @can("delete", $project)
                    <button class="flex items-center gap-2 px-4 py-2 bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-600 hover:text-white hover:border-rose-600 text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-sm" onclick="confirmTermination()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        <span>Terminate</span>
                    </button>
                @endcan
            </div>

            @php
                $owner = $project->owner->first();
                $ownerName = $owner ? "{$owner->first_name} {$owner->last_name}" : "System Administrator";
            @endphp
            <div class="flex items-center gap-3 bg-zinc-50 pl-4 pr-6 py-2 rounded-full border border-zinc-200">
                <img alt="Owner" class="h-8 w-8 rounded-full border border-zinc-200" src="https://ui-avatars.com/api/?name={{ urlencode($ownerName) }}&background=random&color=fff">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Project Owner</p>
                    <p class="text-xs font-bold text-zinc-700">{{ $ownerName }}</p>
                </div>
            </div>
        </div>
    </div>

    @if ($project->description)
        <div class="mt-6 pt-6 border-t border-zinc-100">
            <p class="text-sm text-zinc-500 italic max-w-3xl leading-relaxed">"{{ $project->description }}"</p>
        </div>
    @endif
</div>
