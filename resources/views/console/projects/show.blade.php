@extends("layouts.app")

@section("title", $project->name)
@section("page_title", $project->name)

@section("content")
    <div class="space-y-6">

        {{-- Top Info Card --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-wider">Active</span>
                    <span class="text-slate-400 text-xs font-mono">{{ $project->id }}</span>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mt-1">Repository Details</h3>
                <a class="text-indigo-600 text-xs font-mono hover:underline flex items-center gap-1 mt-1" href="{{ $project->repository_url }}" target="_blank">
                    {{ $project->repository_url }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-linecap="round" stroke-width="2" />
                    </svg>
                </a>
            </div>

            <div class="flex gap-2">
                {{-- Tombol Edit (Dummy dulu) --}}
                <button class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
                    Sync Repo
                </button>
            </div>
        </div>

        {{-- Environments Section --}}
        <div>
            <h4 class="text-slate-800 font-bold text-sm uppercase tracking-wider mb-4">Deployment Environments</h4>

            <div class="space-y-4">
                @foreach ($project->environments as $env)
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 hover:border-indigo-200 transition-colors group">
                        <div class="flex justify-between items-center">

                            {{-- Environment Info --}}
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-lg {{ $env->isProduction() ? "bg-rose-50 text-rose-600" : "bg-blue-50 text-blue-600" }} flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($env->isProduction())
                                            <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-width="2" /> {{-- Icon Rocket/Prod --}}
                                            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" />
                                        @else
                                            <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" stroke-width="2" />
                                        @endif
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h5 class="font-bold text-slate-800">{{ $env->name }}</h5>
                                        @if ($env->isProduction())
                                            <span class="px-1.5 py-0.5 bg-rose-100 text-rose-600 text-[9px] font-black uppercase tracking-wider rounded">Production</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">Branch: <span class="font-mono text-indigo-500 font-bold bg-indigo-50 px-1 rounded">{{ $env->branch }}</span></p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mr-2">Ready to Deploy</span>
                                <button class="px-5 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold shadow-lg shadow-slate-900/20 hover:bg-slate-800 transition-all flex items-center gap-2" onclick="deployConfirm('{{ $env->name }}')">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Deploy
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        function deployConfirm(envName) {
            Swal.fire({
                title: 'Deploy to ' + envName + '?',
                text: "This will pull the latest code from the branch and restart containers.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Start Deployment',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'flux-popup',
                    title: 'flux-title',
                    htmlContainer: 'flux-content',
                    confirmButton: 'flux-confirm-btn',
                    cancelButton: 'flux-cancel-btn'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Nanti kita arahkan ke route deploy controller
                    Swal.fire({
                        title: 'Queued!',
                        text: 'Deployment job has been dispatched.',
                        icon: 'success',
                        customClass: {
                            popup: 'flux-popup',
                            title: 'flux-title',
                            htmlContainer: 'flux-content',
                            confirmButton: 'flux-confirm-btn'
                        },
                        buttonsStyling: false
                    });
                }
            })
        }
    </script>
@endpush
