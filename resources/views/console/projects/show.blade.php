@extends("layouts.app")

@section("title", $project->name)
@section("page_title", "Mission Control")
@section("page_subtitle", "Overview and operational status.")

@section("actions")
    <div class="flex items-center gap-3">
        <a class="flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 text-zinc-600 hover:text-zinc-900 hover:border-zinc-300 text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-sm group" href="{{ $project->repository_url }}" target="_blank">
            <svg class="w-4 h-4 text-zinc-400 group-hover:text-[#181717] transition-colors" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
            </svg>
            <span>Repository</span>
        </a>

        {{-- 🔥 PAKAI POLICY: Cek apakah user boleh 'delete' project --}}
        @can("delete", $project)
            <button class="flex items-center gap-2 px-4 py-2 bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-600 hover:text-white hover:border-rose-600 text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-sm" onclick="confirmTermination()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <span>Terminate</span>
            </button>
        @endcan
    </div>
@endsection

@section("content")
    <div class="space-y-8 max-w-7xl mx-auto">
        {{-- HEADER & IDENTITY --}}
        <div class="bg-white rounded-[2rem] border border-zinc-200 p-8 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-50 to-transparent rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                <div class="space-y-3">
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
            @if ($project->description)
                <div class="mt-6 pt-6 border-t border-zinc-100">
                    <p class="text-sm text-zinc-500 italic max-w-3xl leading-relaxed">"{{ $project->description }}"</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                {{-- HEALTH CARD --}}
                <div class="bg-[#0B1120] rounded-[2rem] p-6 text-white relative overflow-hidden shadow-xl shadow-blue-900/10 border border-blue-900/30">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-blue-600 rounded-full blur-[60px] opacity-20 -mr-10 -mt-10"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400">System Health</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                                <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Environments</p>
                                <p class="text-2xl font-black text-white">{{ $project->environments->count() }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                                <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Stability</p>
                                <p class="text-2xl font-black text-emerald-400">99.9%</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TEAM CARD --}}
                <div class="bg-white rounded-[2rem] border border-zinc-200 p-6 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-black text-zinc-900 uppercase tracking-widest">Personnel</h3>
                            <span class="bg-zinc-100 text-zinc-500 text-[9px] font-bold px-1.5 py-0.5 rounded">{{ $project->members->count() }}</span>
                        </div>

                        {{-- 🔥 PAKAI POLICY: Cek apakah boleh nambah member --}}
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
                                $isMe = $member->id === auth()->id();

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

                                {{-- 🔥 PAKAI POLICY: Cek apakah boleh edit user TERSEBUT --}}
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
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-zinc-900 uppercase tracking-widest flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-600"></span> Active Environments</h3>
                    <button class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-all">+ Provision Node</button>
                </div>
                <div class="space-y-4">
                    @forelse($project->environments as $env)
                        @php
                            $isProd = $env->type === "production";
                            $borderColor = $isProd ? "border-l-rose-500" : "border-l-blue-500";
                            $iconColor = $isProd ? "text-rose-600 bg-rose-50 border-rose-100" : "text-blue-600 bg-blue-50 border-blue-100";
                            $badgeColor = $isProd ? "bg-rose-100 text-rose-700" : "bg-blue-100 text-blue-700";
                        @endphp
                        <div class="bg-white rounded-2xl border border-zinc-200 p-6 shadow-sm hover:shadow-lg hover:shadow-zinc-200/50 transition-all group border-l-4 {{ $borderColor }}">
                            <div class="flex flex-col md:flex-row justify-between md:items-center gap-6">
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
                                <div class="flex items-center gap-4 pl-0 md:pl-6 border-l-0 md:border-l border-zinc-100 w-full md:w-auto justify-end">
                                    <button class="px-6 py-3 rounded-xl bg-zinc-900 text-white font-bold text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-zinc-900/10 flex items-center gap-2 group-hover:-translate-y-0.5" onclick="deployConfirm('{{ $env->name }}')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                        Deploy
                                    </button>
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
            </div>
        </div>
    </div>

    @push("scripts")
        <script>
            // === CONFIG ===
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            // 🔥 1. AMBIL ROLE DARI PHP (Untuk Filtering Dropdown)
            // Kita ambil role secara manual via PHP helper karena JS tidak bisa baca Policy langsung
            @php
                $currentUser = auth()->user();
                $isSysAdmin = $currentUser->role === "System Administrator";
                $myProjectRole = $project->members->firstWhere("id", $currentUser->id)?->pivot->role;
            @endphp
            const myRole = "{{ $isSysAdmin ? "sysadmin" : $myProjectRole ?? "member" }}";

            // 🔥 2. HELPER GENERATE DROPDOWN
            function getRoleOptions(currentSelected = 'member') {
                let options = `<option value="member" ${currentSelected === 'member' ? 'selected' : ''}>Member</option>`;
                if (myRole === 'sysadmin' || myRole === 'owner') {
                    options += `<option value="manager" ${currentSelected === 'manager' ? 'selected' : ''}>Manager</option>`;
                    options += `<option value="owner" ${currentSelected === 'owner' ? 'selected' : ''}>Owner</option>`;
                }
                return options;
            }

            // Init FluxSwal
            if (typeof fluxSwal === 'undefined') {
                window.fluxSwal = Swal.mixin({
                    customClass: {
                        popup: 'rounded-2xl border border-zinc-200 shadow-2xl p-0 overflow-hidden font-sans',
                        title: 'text-zinc-900 text-lg font-bold pt-6 px-6',
                        htmlContainer: 'text-zinc-500 text-sm px-6 pb-6',
                        confirmButton: 'bg-zinc-900 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-800 transition-colors shadow-sm mx-2 mb-6',
                        cancelButton: 'bg-white text-zinc-600 border border-zinc-200 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-50 transition-colors mx-2 mb-6',
                    },
                    buttonsStyling: false
                });
            }

            // Init Toast
            if (typeof Toast === 'undefined') {
                window.Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    },
                    customClass: {
                        popup: 'bg-white border border-zinc-200 shadow-lg rounded-xl p-3 flex items-center gap-2',
                        title: 'text-zinc-800 text-xs font-bold',
                        timerProgressBar: 'bg-zinc-900'
                    }
                });
            }

            // Helper Submit
            if (typeof window.submitForm === 'undefined') {
                window.submitForm = function(action, method, data = {}) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = action;
                    form.style.display = 'none';
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                    if (method !== 'POST') {
                        const m = document.createElement('input');
                        m.type = 'hidden';
                        m.name = '_method';
                        m.value = method;
                        form.appendChild(m);
                    }
                    for (const [k, v] of Object.entries(data)) {
                        const i = document.createElement('input');
                        i.type = 'hidden';
                        i.name = k;
                        i.value = v;
                        form.appendChild(i);
                    }
                    document.body.appendChild(form);
                    fluxSwal.fire({
                        title: 'Processing...',
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });
                    form.submit();
                }
            }

            // === FUNCTIONS ===
            window.openAddMemberModal = async function() {
                fluxSwal.fire({
                    title: 'Loading Users...',
                    didOpen: () => Swal.showLoading()
                });
                try {
                    const response = await fetch('{{ route("projects.members.search", $project->id) }}');
                    if (!response.ok) throw new Error('Network error');
                    const users = await response.json();
                    let optionsHtml = '';
                    users.forEach(user => {
                        const name = (user.first_name && user.last_name) ? `${user.first_name} ${user.last_name}` : user.name;
                        optionsHtml += `<option value="${user.email}">${name}</option>`;
                    });

                    const {
                        value: formValues
                    } = await fluxSwal.fire({
                        title: 'Add Personnel',
                        html: `
                        <div class="flex flex-col gap-4 text-left">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Search User</label>
                                <input list="available-users" id="mem-email" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors placeholder-zinc-400" placeholder="Type name or email...">
                                <datalist id="available-users">${optionsHtml}</datalist>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Assign Role</label>
                                <select id="mem-role" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors">
                                    ${getRoleOptions()}
                                </select>
                            </div>
                        </div>`,
                        showCancelButton: true,
                        confirmButtonText: 'Invite User',
                        preConfirm: () => {
                            const email = document.getElementById('mem-email').value;
                            const role = document.getElementById('mem-role').value;
                            if (!email) Swal.showValidationMessage('User selection required');
                            return {
                                email,
                                role
                            };
                        }
                    });
                    if (formValues) window.submitForm('{{ route("projects.members.store", $project->id) }}', 'POST', formValues);
                } catch (error) {
                    console.error(error);
                    fluxSwal.fire('Error', 'Failed to load user list.', 'error');
                }
            };

            window.openEditMemberModal = async function(userId, name, currentRole) {
                const {
                    value: role
                } = await fluxSwal.fire({
                    title: 'Update Role',
                    html: `
                    <p class="text-xs text-zinc-500 mb-4 text-left">Modify access for <b class="text-zinc-900">${name}</b>.</p>
                    <div class="text-left">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Select Role</label>
                        <select id="edit-mem-role" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500">
                            ${getRoleOptions(currentRole)}
                        </select>
                    </div>`,
                    showCancelButton: true,
                    confirmButtonText: 'Save Changes',
                    preConfirm: () => document.getElementById('edit-mem-role').value
                });
                if (role) window.submitForm('{{ route("projects.members.update", [$project->id, ":uid"]) }}'.replace(':uid', userId), 'PATCH', {
                    role: role
                });
            };

            window.removeMember = function(userId, name) {
                fluxSwal.fire({
                    title: 'Remove User?',
                    html: `Remove <b class="text-zinc-900">${name}</b> from project?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Remove',
                    confirmButtonClass: 'bg-rose-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-rose-700 mx-2 mb-6',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) window.submitForm('{{ route("projects.members.destroy", [$project->id, ":uid"]) }}'.replace(':uid', userId), 'DELETE');
                });
            };

            window.confirmTermination = function() {
                fluxSwal.fire({
                    title: 'Terminate Protocol',
                    html: `
                        <div class="text-left">
                            <p class="text-sm text-zinc-500 mb-4">Are you sure you want to delete <b class="text-zinc-900">{{ $project->name }}</b>?</p>
                            <div class="bg-rose-50 border border-rose-100 rounded-xl p-4 flex gap-3 items-start">
                                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <div>
                                    <p class="text-xs text-rose-800 font-bold uppercase tracking-wide mb-1">Irreversible Action</p>
                                    <p class="text-[11px] text-rose-600/80 leading-relaxed">All environments, deployments, and member associations will be permanently destroyed.</p>
                                </div>
                            </div>
                        </div>`,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Terminate',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl border border-zinc-200 shadow-2xl p-0 overflow-hidden font-sans',
                        title: 'text-zinc-900 text-lg font-bold pt-6 px-6',
                        htmlContainer: 'text-zinc-500 text-sm px-6 pb-6',
                        cancelButton: 'bg-white text-zinc-600 border border-zinc-200 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-50 transition-colors mx-2 mb-6',
                        confirmButton: 'bg-rose-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-rose-700 transition-colors shadow-sm mx-2 mb-6',
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) window.submitForm('{{ route("console.projects.destroy", $project->id) }}', 'DELETE');
                });
            };

            function copyToClipboard(text, successMsg) {
                navigator.clipboard.writeText(text).then(() => {
                    if (typeof Toast !== 'undefined') Toast.fire({
                        icon: 'success',
                        title: successMsg
                    });
                });
            }

            function deployConfirm(envName) {
                fluxSwal.fire({
                    title: 'Initiate Deployment?',
                    text: `Deploying to ${envName}.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Deploy'
                }).then((result) => {
                    if (result.isConfirmed && typeof Toast !== 'undefined') Toast.fire({
                        icon: 'info',
                        title: 'Deployment Queued'
                    });
                })
            }
        </script>
    @endpush
@endsection
