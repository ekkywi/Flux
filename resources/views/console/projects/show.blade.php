@extends("layouts.app")
@section("title", $project->name)
@section("page_title", "Project Control Panel")

@section("content")
    <div class="space-y-8 pb-20 text-slate-900">

        {{-- 1. HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <div class="h-1 w-6 bg-indigo-600 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Deployment Protocol</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">{{ $project->name }}</h1>
                <p class="text-xs text-slate-500 font-medium font-mono">
                    ORIGIN // <span class="text-indigo-600 underline decoration-indigo-200">{{ $project->repository_url }}</span>
                </p>
            </div>

            <div class="flex items-center gap-4">
                <a class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200 flex items-center gap-2" href="{{ route("console.projects.index") }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5" />
                    </svg>
                    Return to Fleet
                </a>
            </div>
        </div>

        {{-- 2. ENVIRONMENT ORCHESTRATION GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            @foreach ($project->environments as $env)
                <div class="bg-white rounded-[2.5rem] border border-slate-200 p-10 shadow-sm flex flex-col justify-between group hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 relative overflow-hidden">

                    <div class="flex items-center justify-between mb-12">
                        <span class="px-4 py-2 bg-slate-100 text-slate-600 text-[9px] font-black uppercase tracking-[0.2em] rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            {{ $env->name }}
                        </span>

                        @if ($env->server_app_id)
                            <span class="flex items-center gap-2 text-emerald-600 text-[9px] font-black uppercase tracking-widest">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Provisioned
                            </span>
                        @else
                            <span class="flex items-center gap-2 text-slate-300 text-[9px] font-black uppercase tracking-widest font-mono">
                                [ Standby Node ]
                            </span>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Access Port</label>
                            <p class="text-3xl font-black text-slate-900 font-mono tracking-tighter group-hover:text-indigo-600 transition-colors">
                                :{{ $env->assigned_port }}
                            </p>
                        </div>

                        <div class="space-y-4 p-6 bg-slate-50/50 rounded-2xl border border-slate-100 group-hover:bg-white group-hover:border-indigo-100 transition-all">
                            <div class="flex justify-between items-center">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">Infrastructure Node</label>
                                @if ($env->server_app_id)
                                    <button class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm" onclick="checkNodeStatus('{{ $env->id }}')">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2.5" />
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            @if ($env->server_app_id)
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-indigo-600 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2" stroke-width="2.5" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate tracking-tight">{{ $env->appServer->name }}</p>
                                            <p class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $env->appServer->ip_address }}</p>
                                        </div>
                                    </div>
                                    <div class="pt-3 border-t border-slate-100">
                                        <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mb-1">Active Stack ID</p>
                                        <p class="text-[10px] font-mono text-indigo-600 font-black truncate bg-indigo-50/50 px-2.5 py-1.5 rounded-lg border border-indigo-100/50">
                                            {{ $env->env_vars["CONTAINER_NAME"] ?? "AWAITING_INIT" }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="py-4 text-center border-2 border-dashed border-slate-100 rounded-xl">
                                    <p class="text-[10px] font-bold text-slate-300 uppercase italic tracking-widest">Awaiting Assignment</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-12 space-y-3">
                        @if (!$env->server_app_id)
                            <button class="w-full py-4 bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-2 group/btn" onclick="toggleAssignModal('{{ $env->id }}', '{{ $env->name }}')">
                                <span>Link Node</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2.5" />
                                </svg>
                            </button>
                        @else
                            <form action="{{ route("console.projects.deploy", $env->id) }}" id="deployForm-{{ $env->id }}" method="POST">
                                @csrf
                                <button class="w-full py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-emerald-500 transition-all shadow-xl shadow-indigo-100 flex items-center justify-center gap-2" type="submit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2.5" />
                                    </svg>
                                    <span>Execute Deploy</span>
                                </button>
                            </form>
                            <button class="w-full py-3 text-slate-400 hover:text-indigo-600 text-[9px] font-black uppercase tracking-widest transition-all border border-transparent hover:border-indigo-50 hover:bg-indigo-50/30 rounded-xl" onclick="viewDeployLogs('{{ $env->id }}')">
                                View Streaming Logs
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- MODAL: PROVISION NODE --}}
    <div class="fixed inset-0 z-[100] items-center justify-center hidden bg-slate-900/60 backdrop-blur-sm px-4" id="assignModal">
        <div class="bg-white w-full max-w-md rounded-[3rem] p-12 shadow-2xl border border-slate-200 transform transition-all">
            <div class="mb-10 text-center">
                <div class="h-1.5 w-16 bg-indigo-600 rounded-full mx-auto mb-6"></div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Provision Node</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2">Environment: <span class="text-indigo-600" id="env_name_display"></span></p>
            </div>

            <form action="" class="space-y-6" id="assignForm" method="POST">
                @csrf
                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1 tracking-widest">Infrastructure Selector</label>
                    <div class="relative">
                        <select class="w-full px-6 py-4 rounded-2xl border-slate-200 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-slate-50 font-bold text-slate-700 appearance-none shadow-inner" name="server_id" required>
                            <option disabled selected value="">Search Nodes...</option>
                            @foreach ($servers as $server)
                                <option value="{{ $server->id }}">{{ $server->name }} // {{ $server->ip_address }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" stroke-width="3" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-6">
                    <button class="flex-1 px-6 py-4 bg-slate-100 text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleAssignModal()" type="button">Abort</button>
                    <button class="flex-1 px-6 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl" type="submit">Link Cluster</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        // 1. MODAL ASSIGN SERVER
        function toggleAssignModal(envId = null, envName = null) {
            const modal = document.getElementById('assignModal');
            const form = document.getElementById('assignForm');
            const display = document.getElementById('env_name_display');

            if (envId) {
                form.action = `/console/projects/environments/${envId}/assign-server`;
                display.textContent = envName.toUpperCase();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // 2. DIAGNOSTIC ALERT
        function checkNodeStatus(envId) {
            Swal.fire({
                title: 'NODE_DIAGNOSTIC_v1.0',
                html: `
                    <div class="space-y-4 pt-4 text-left">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] animate-pulse">Running system integrity check...</p>
                        <div class="h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-600 w-1/3 animate-pulse"></div>
                        </div>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: {
                    popup: 'flux-popup',
                    title: 'flux-title text-left'
                }
            });

            setTimeout(() => {
                Swal.fire({
                    title: 'DIAGNOSTIC_PASSED',
                    html: `
                        <div class="space-y-6 pt-4 text-left">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Infrastructure node is fully operational.</p>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="block text-[8px] font-black text-slate-400 uppercase mb-1">SSH Tunnel</span>
                                    <span class="text-[10px] font-black text-emerald-600 uppercase">SECURED_200</span>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                    <span class="block text-[8px] font-black text-slate-400 uppercase mb-1">Docker Daemon</span>
                                    <span class="text-[10px] font-black text-emerald-600 uppercase">ACTIVE_v24.0</span>
                                </div>
                            </div>
                            <div class="p-5 bg-indigo-50/50 rounded-2xl border border-dashed border-indigo-100 overflow-hidden">
                                <p class="text-[9px] font-mono text-indigo-600 leading-relaxed truncate">
                                    > systemctl status docker.service<br>
                                    > Output: active (running) since Fri...
                                </p>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    iconColor: '#4f46e5',
                    confirmButtonText: 'ACKNOWLEDGE SYSTEM',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'flux-popup',
                        title: 'flux-title',
                        confirmButton: 'flux-confirm-btn w-full mt-4'
                    }
                });
            }, 1800);
        }

        // 3. LOG VIEW & LIVE STREAMING (THE HYBRID ENGINE)
        function viewDeployLogs(envId) {
            if (typeof window.Echo === 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'ASSET_ERROR',
                    text: 'Echo is not initialized. Run "npm run build" first.'
                });
                return;
            }

            Swal.fire({
                title: 'TERMINAL_STREAM_v1.0',
                html: `
            <div id="log-container" class="mt-4 w-full h-80 bg-[#0f172a] rounded-2xl p-6 overflow-y-auto text-left border border-slate-800 shadow-inner custom-scrollbar font-mono text-[10px] leading-relaxed">
                <div id="log-content" class="space-y-1">
                    <p class="text-indigo-400 animate-pulse tracking-widest">INITIALIZING SECURE SOCKET LINK...</p>
                </div>
            </div>
        `,
                width: '640px',
                confirmButtonText: 'DISCONNECT SESSION',
                buttonsStyling: false,
                customClass: {
                    popup: 'flux-popup',
                    title: 'flux-title text-left',
                    confirmButton: 'flux-cancel-btn w-full mt-4'
                },
                didOpen: (modal) => {
                    // CARA AMAN: Ambil elemen dari dalam modal yang baru terbuka
                    const content = modal.querySelector('#log-content');
                    const container = modal.querySelector('#log-container');

                    if (!content || !container) {
                        console.error("Critical UI Error: Log elements not found in DOM.");
                        return;
                    }

                    // A. FETCH HISTORY (REDIS)
                    fetch(`/environments/${envId}/logs`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.logs && data.logs.length > 0) {
                                content.innerHTML = '';
                                data.logs.forEach(log => appendLogLine(content, log));
                                container.scrollTop = container.scrollHeight;
                            } else {
                                content.innerHTML = '<p class="text-slate-500 uppercase tracking-widest text-center mt-20">No active session logs found.</p>';
                            }
                        })
                        .catch(err => {
                            content.innerHTML = `<p class="text-rose-500">SYSTEM_ERROR: Failed to fetch history.</p>`;
                        });

                    // B. LISTEN LIVE STREAM (REVERB)
                    window.Echo.private(`environment.logs.${envId}`)
                        .listen('.log.received', (e) => {
                            if (content.innerText.includes('INITIALIZING') || content.innerText.includes('No active')) {
                                content.innerHTML = '';
                            }
                            appendLogLine(content, e.logData);
                            container.scrollTop = container.scrollHeight;
                        });
                },
                willClose: () => {
                    window.Echo.leave(`environment.logs.${envId}`);
                }
            });
        }

        // Helper: Format & Append Log Line
        function appendLogLine(target, log) {
            const color = log.type === 'error' ? 'text-rose-400 font-bold' : 'text-emerald-400';
            const timestamp = `<span class="text-slate-600 mr-2">[${log.time}]</span>`;
            const lineHtml = `<p class="truncate"><span class="text-slate-500 mr-1">$</span> ${timestamp} <span class="${color}">${log.line}</span></p>`;
            target.insertAdjacentHTML('beforeend', lineHtml);
        }

        // 4. DEPLOYMENT TRIGGER
        document.querySelectorAll('form[id^="deployForm-"]').forEach(form => {
            form.onsubmit = function() {
                Swal.fire({
                    title: 'ORCHESTRATING_',
                    html: '<p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest text-left">Dispatching deployment job to infrastructure pipeline...</p>',
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
        });
    </script>
@endpush
