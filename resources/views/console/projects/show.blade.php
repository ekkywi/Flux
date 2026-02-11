@extends("layouts.app")

@section("title", $project->name)
@section("page_title", "Mission Control")
@section("page_subtitle", "Overview and operational status.")

@section("actions")
    {{-- Top Level Actions --}}
    <div class="flex items-center gap-3">
        <a class="flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 text-zinc-600 hover:text-zinc-900 hover:border-zinc-300 text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-sm group" href="{{ $project->repository_url }}" target="_blank">
            <svg class="w-4 h-4 text-zinc-400 group-hover:text-[#181717] transition-colors" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
            </svg>
            <span>Repository</span>
        </a>

        @can("delete", $project)
            <button class="flex items-center gap-2 px-4 py-2 bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-600 hover:text-white hover:border-rose-600 text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-sm" onclick="toggleRevokeModal()">
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

        {{-- 1. HEADER & IDENTITY CARD --}}
        <div class="bg-white rounded-[2rem] border border-zinc-200 p-8 shadow-sm relative overflow-hidden">
            {{-- Background Decoration --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-50 to-transparent rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                {{-- Left: Identity --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <h1 class="text-4xl font-black tracking-tighter text-zinc-900">{{ $project->name }}</h1>
                        {{-- Status Badge Dynamic --}}
                        @php
                            $statusColors = match ($project->status ?? "active") {
                                "active" => "bg-emerald-100 text-emerald-700 border-emerald-200",
                                "maintenance" => "bg-amber-100 text-amber-700 border-amber-200",
                                "archived" => "bg-zinc-100 text-zinc-600 border-zinc-200",
                                default => "bg-zinc-100 text-zinc-600 border-zinc-200",
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusColors }}">
                            {{ $project->status ?? "ACTIVE" }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-zinc-500">
                        <div class="flex items-center gap-1.5 cursor-pointer hover:text-blue-600 transition-colors" onclick="copyToClipboard('{{ $project->id }}', 'UUID Copied!')" title="Click to copy UUID">
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

                {{-- Right: Owner Mini Profile --}}
                <div class="flex items-center gap-3 bg-zinc-50 pl-4 pr-6 py-2 rounded-full border border-zinc-200">
                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-200">
                        {{ substr(optional($project->owner->first())->name ?? "S", 0, 1) }}
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Project Owner</p>
                        <p class="text-xs font-bold text-zinc-700">{{ optional($project->owner->first())->name ?? "System Admin" }}</p>
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
            {{-- ================= LEFT COLUMN (Stats & Team) ================= --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- SYSTEM HEALTH CARD (Midnight Blue) --}}
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

                        <div class="mt-6 pt-4 border-t border-white/5">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[9px] font-bold text-zinc-500 uppercase">Target Velocity</span>
                                <span class="text-[9px] font-bold text-blue-400">Good</span>
                            </div>
                            <div class="w-full bg-zinc-800 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-blue-500 to-emerald-400 h-1.5 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TEAM CARD --}}
                <div class="bg-white rounded-[2rem] border border-zinc-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-black text-zinc-900 uppercase tracking-widest">Personnel</h3>
                        <button class="text-zinc-400 hover:text-blue-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3">
                        @forelse($project->members->take(5) as $member)
                            <div class="flex items-center gap-3 p-2 hover:bg-zinc-50 rounded-xl transition-all group">
                                <img alt="" class="h-8 w-8 rounded-lg border border-zinc-200" src="https://ui-avatars.com/api/?name={{ $member->name }}&background=random">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-zinc-900 truncate">{{ $member->name }}</p>
                                    <p class="text-[9px] font-medium text-zinc-500 uppercase tracking-wider">{{ $member->pivot->role ?? "COLLABORATOR" }}</p>
                                </div>
                                <div class="w-1.5 h-1.5 rounded-full bg-zinc-300 group-hover:bg-blue-500 transition-colors"></div>
                            </div>
                        @empty
                            <div class="text-center py-6 border border-dashed border-zinc-200 rounded-xl bg-zinc-50">
                                <p class="text-[10px] text-zinc-400 font-bold">No personnel assigned</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ================= RIGHT COLUMN (Environments) ================= --}}
            <div class="lg:col-span-2 space-y-6">

                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-zinc-900 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span> Active Environments
                    </h3>
                    <button class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-all">
                        + Provision Node
                    </button>
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
                                    {{-- Icon Box --}}
                                    <div class="h-12 w-12 rounded-xl {{ $iconColor }} border flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </div>

                                    <div>
                                        <div class="flex items-center gap-3 mb-1">
                                            <h2 class="text-lg font-black text-zinc-900">{{ $env->name }}</h2>
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest {{ $badgeColor }}">
                                                {{ $env->type }}
                                            </span>
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
                            <div class="h-14 w-14 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm border border-zinc-100">
                                <svg class="w-6 h-6 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-black text-zinc-900 uppercase tracking-widest mb-1">No Environments</h3>
                            <p class="text-xs text-zinc-500">Initialize a new environment to begin deployment sequence.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: TERMINATE PROJECT --}}
    <div class="fixed inset-0 z-[110] hidden items-center justify-center bg-[#0B1120]/80 backdrop-blur-md transition-opacity duration-300 opacity-0" id="revokeModal">
        <div class="bg-white w-full max-w-sm rounded-[2rem] p-8 shadow-2xl border border-zinc-200 transform scale-95 transition-transform duration-300" id="revokeModalContent">
            <div class="text-center mb-8">
                <div class="mx-auto h-16 w-16 bg-rose-50 rounded-full flex items-center justify-center mb-6 border border-rose-100 shadow-inner">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
                <h3 class="text-xl font-black text-zinc-900 tracking-tight mb-2">Terminate Protocol</h3>
                <p class="text-xs text-zinc-500 leading-relaxed px-4">
                    Confirm deletion of <span class="text-rose-600 font-bold font-mono">{{ $project->name }}</span>. This action is <span class="uppercase font-bold text-zinc-900">irreversible</span>.
                </p>
            </div>

            <form action="{{ route("console.projects.destroy", $project) }}" class="space-y-3" method="POST">
                @csrf
                @method("DELETE")
                <button class="w-full py-3.5 bg-rose-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-lg shadow-rose-500/20 hover:shadow-rose-600/40 transform active:scale-95" type="submit">
                    Confirm Termination
                </button>
                <button class="w-full py-3.5 bg-zinc-100 text-zinc-600 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-zinc-200 transition-all" onclick="toggleRevokeModal()" type="button">
                    Abort
                </button>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    @push("scripts")
        <script>
            // Modal Logic dengan Animasi
            function toggleRevokeModal() {
                const modal = document.getElementById('revokeModal');
                const content = document.getElementById('revokeModalContent');

                if (modal.classList.contains('hidden')) {
                    // Open
                    modal.classList.remove('hidden');
                    // Small delay to allow display:flex to apply before opacity transition
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        content.classList.remove('scale-95');
                        content.classList.add('scale-100');
                    }, 10);
                } else {
                    // Close
                    modal.classList.add('opacity-0');
                    content.classList.remove('scale-100');
                    content.classList.add('scale-95');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                    }, 300);
                }
            }

            // Copy to Clipboard Utility
            function copyToClipboard(text, successMsg) {
                navigator.clipboard.writeText(text).then(() => {
                    // Gunakan SweetAlert Toast yang sudah ada di layout
                    Toast.fire({
                        icon: 'success',
                        title: successMsg
                    });
                });
            }

            // Deploy Confirmation
            function deployConfirm(envName) {
                Swal.fire({
                    title: 'Initiate Deployment?',
                    text: `Deploying latest code to ${envName} environment.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#e4e4e7',
                    confirmButtonText: 'Yes, Deploy',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'flux-popup',
                        title: 'flux-title',
                        confirmButton: 'flux-confirm-btn',
                        cancelButton: 'text-zinc-600 font-bold bg-zinc-100 px-6 py-3 rounded-xl hover:bg-zinc-200'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit form or ajax call here
                        Toast.fire({
                            icon: 'info',
                            title: 'Deployment Queued'
                        });
                    }
                })
            }
        </script>
    @endpush
@endsection
