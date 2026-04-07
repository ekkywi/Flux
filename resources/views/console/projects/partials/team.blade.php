<div class="bg-white rounded-[2rem] border border-zinc-200 p-6 shadow-sm relative overflow-hidden">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <h3 class="text-xs font-black text-zinc-900 uppercase tracking-widest">Personnel</h3>
            <span class="bg-zinc-100 text-zinc-500 text-[9px] font-bold px-1.5 py-0.5 rounded">{{ $project->members->count() }}</span>
        </div>

        @can("addMember", $project)
            <button class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-900 text-white hover:bg-blue-600 rounded-lg text-[9px] font-bold uppercase tracking-widest transition-all shadow-sm" onclick="openAddMemberModal()">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                Add
            </button>
        @endcan
    </div>

    <div class="space-y-3">
        @forelse($project->members as $member)
            @php
                $fullName = "{$member->first_name} {$member->last_name}";
                $role = $member->pivot->role;
                $isOwnerRole = $role === "owner";
                $badgeClass = match ($role) {
                    "owner" => "text-amber-700 bg-amber-50 border border-amber-100",
                    "manager" => "text-indigo-600 bg-indigo-50 border border-indigo-100",
                    default => "text-zinc-500 bg-zinc-100 border border-zinc-200",
                };
            @endphp

            <div class="flex items-center gap-3 p-2 hover:bg-zinc-50 rounded-xl transition-all group relative pr-10">
                <img alt="{{ $fullName }}" class="h-8 w-8 rounded-lg border border-zinc-200 shrink-0" src="https://ui-avatars.com/api/?name={{ urlencode($fullName) }}&background=random&size=64">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-zinc-900 truncate">{{ $fullName }}</p>
                        @if ($isOwnerRole)
                            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide {{ $badgeClass }}">{{ $role }}</span>
                    </div>
                </div>

                @can("updateMember", [$project, $member])
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 bg-white/90 backdrop-blur-sm rounded-lg p-1 border border-zinc-200 shadow-sm z-10">
                        <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors" onclick="openEditMemberModal('{{ $member->id }}', '{{ $fullName }}', '{{ $role }}')" title="Edit Role">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </button>
                        @if ($member->id !== auth()->id())
                            <button class="p-1.5 text-zinc-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors" onclick="removeMember('{{ $member->id }}', '{{ $fullName }}')" title="Remove Member">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </button>
                        @endif
                    </div>
                @endcan
            </div>
        @empty
            <div class="text-center py-6 border border-dashed border-zinc-200 rounded-xl bg-zinc-50">
                <p class="text-[10px] text-zinc-400 font-bold uppercase">No personnel assigned</p>
            </div>
        @endforelse
    </div>
</div>
