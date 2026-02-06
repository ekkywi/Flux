@extends("layouts.app")
@section("title", $project->name)
@section("page_title", "Project Control Panel")

@section("content")
    <div class="space-y-8 pb-20 text-slate-900">

        {{-- 1. HEADER SECTION --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <div class="h-1 w-6 bg-indigo-600 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Deployment Protocol</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">{{ $project->name }}</h1>
                <p class="text-xs text-slate-500 font-medium font-mono">
                    ORIGIN // <a class="text-indigo-600 underline decoration-indigo-200 hover:text-indigo-800 transition-colors" href="{{ $project->repository_url }}" target="_blank">{{ $project->repository_url }}</a>
                </p>
                <input id="currentProjectId" type="hidden" value="{{ $project->id }}">
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

        {{-- 2. ENVIRONMENT GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            @foreach ($project->environments as $env)
                <div class="bg-white rounded-[2.5rem] border border-slate-200 p-10 shadow-sm flex flex-col justify-between group hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 relative overflow-visible">

                    {{-- Card Header --}}
                    <div class="flex items-center justify-between mb-8">
                        <span class="px-4 py-2 bg-slate-100 text-slate-600 text-[9px] font-black uppercase tracking-[0.2em] rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            {{ $env->name }}
                        </span>

                        @if ($env->server_app_id)
                            <span class="flex items-center gap-2 text-emerald-600 text-[9px] font-black uppercase tracking-widest">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Online
                            </span>
                        @else
                            <span class="flex items-center gap-2 text-slate-300 text-[9px] font-black uppercase tracking-widest font-mono">
                                [ Disconnected ]
                            </span>
                        @endif
                    </div>

                    <div class="space-y-6">
                        {{-- Port Info --}}
                        <div class="space-y-1">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Access Port</label>
                            <div class="flex items-baseline gap-2">
                                <p class="text-3xl font-black text-slate-900 font-mono tracking-tighter group-hover:text-indigo-600 transition-colors">
                                    :{{ $env->assigned_port }}
                                </p>
                                @if ($env->server_app_id)
                                    <a class="text-slate-300 hover:text-indigo-600 transition-colors" href="http://{{ $env->appServer->ip_address }}:{{ $env->assigned_port }}" target="_blank">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2"></path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Server Details --}}
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
                                </div>
                            @else
                                <div class="py-4 text-center border-2 border-dashed border-slate-100 rounded-xl">
                                    <p class="text-[10px] font-bold text-slate-300 uppercase italic tracking-widest">Awaiting Assignment</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions Area --}}
                    <div class="mt-12 space-y-3">
                        @if (!$env->server_app_id)
                            <button class="w-full py-4 bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-2 group/btn" onclick="toggleAssignModal('{{ $env->id }}', '{{ $env->name }}')">
                                <span>Link Node</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2.5" />
                                </svg>
                            </button>
                        @else
                            {{-- DEPLOYMENT FORM --}}
                            <form action="{{ route("console.projects.deploy", $env->id) }}" class="space-y-3" id="deployForm-{{ $env->id }}" method="POST">
                                @csrf

                                {{-- NEW: Branch Selector --}}
                                <div class="relative group/select">
                                    <label class="absolute -top-2 left-3 px-1 bg-white text-[8px] font-black uppercase text-indigo-600 tracking-widest z-10">Target Branch</label>
                                    <div class="relative">
                                        <select class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl appearance-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer hover:bg-white transition-colors font-mono" id="branchSelect-{{ $env->id }}" name="branch" onclick="loadBranchesIfNeeded('{{ $env->id }}')">
                                            {{-- Default value dari DB --}}
                                            <option selected value="{{ $env->branch ?? "main" }}">{{ $env->branch ?? "main" }} (Current)</option>
                                            <option disabled>──────────</option>
                                            <option disabled id="loader-{{ $env->id }}">Click to fetch...</option>
                                        </select>
                                        {{-- Arrow Icon --}}
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path>
                                            </svg>
                                        </div>
                                        {{-- Loading Spinner (Hidden) --}}
                                        <div class="absolute inset-y-0 right-8 flex items-center hidden" id="spinner-{{ $env->id }}">
                                            <svg class="animate-spin h-3 w-3 text-indigo-600" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4" stroke="currentColor"></circle>
                                                <path class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" fill="currentColor"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Execute Button --}}
                                <button class="w-full py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-emerald-500 transition-all shadow-xl shadow-indigo-100 flex items-center justify-center gap-2" type="submit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2.5" />
                                    </svg>
                                    <span>Deploy</span>
                                </button>
                            </form>

                            {{-- View Logs Button --}}
                            <button class="w-full py-3 text-slate-400 hover:text-indigo-600 text-[9px] font-black uppercase tracking-widest transition-all border border-transparent hover:border-indigo-50 hover:bg-indigo-50/30 rounded-xl flex items-center justify-center gap-2" onclick="viewDeployLogs('{{ $env->id }}')">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                                </span>
                                View Streaming Logs
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- MODAL: PROVISION NODE (Tetap sama, tapi styling dirapikan) --}}
    <div class="fixed inset-0 z-[100] items-center justify-center hidden bg-slate-900/80 backdrop-blur-md px-4 transition-opacity" id="assignModal">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl border border-slate-200 transform transition-all scale-95" id="assignModalContent">
            <div class="mb-8 text-center">
                <div class="h-1 w-12 bg-indigo-600 rounded-full mx-auto mb-4"></div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">Provision Node</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Target: <span class="text-indigo-600" id="env_name_display"></span></p>
            </div>

            <form action="" class="space-y-6" id="assignForm" method="POST">
                @csrf
                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1 tracking-widest">Select Infrastructure</label>
                    <div class="relative">
                        <select class="w-full px-6 py-4 rounded-2xl border-slate-200 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-slate-50 font-bold text-slate-700 appearance-none shadow-inner" name="server_id" required>
                            <option disabled selected value="">Available Nodes...</option>
                            @foreach ($servers as $server)
                                <option value="{{ $server->id }}">{{ $server->name }} ({{ $server->ip_address }})</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-6 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button class="flex-1 py-4 bg-slate-100 text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleAssignModal()" type="button">Cancel</button>
                    <button class="flex-1 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg" type="submit">Link Node</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        // GLOBAL VARIABLES
        const projectId = document.getElementById('currentProjectId').value;

        // 1. MODAL ASSIGN SERVER
        function toggleAssignModal(envId = null, envName = null) {
            const modal = document.getElementById('assignModal');
            const content = document.getElementById('assignModalContent');
            const form = document.getElementById('assignForm');
            const display = document.getElementById('env_name_display');

            if (envId) {
                form.action = `/console/projects/environments/${envId}/assign-server`;
                display.textContent = envName.toUpperCase();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                // Animation
                setTimeout(() => content.classList.replace('scale-95', 'scale-100'), 10);
            } else {
                content.classList.replace('scale-100', 'scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 150);
            }
        }

        // 2. FETCH BRANCHES FROM GITEA (LAZY LOADING)
        const loadedDropdowns = {}; // Mencegah fetch berulang kali

        async function loadBranchesIfNeeded(envId) {
            if (loadedDropdowns[envId]) return; // Kalau sudah pernah load, stop.

            const select = document.getElementById(`branchSelect-${envId}`);
            const spinner = document.getElementById(`spinner-${envId}`);
            const loaderOpt = document.getElementById(`loader-${envId}`);

            // UI Loading
            spinner.classList.remove('hidden');

            try {
                // Call Internal API
                const res = await fetch(`/internal/projects/${projectId}/branches`);
                const data = await res.json();

                // Cleanup Loading Option
                if (loaderOpt) loaderOpt.remove();

                if (data.status === 'success' && data.branches.length > 0) {
                    data.branches.forEach(branch => {
                        // Jangan duplikasi value yang sudah selected
                        if (select.value !== branch) {
                            const option = document.createElement('option');
                            option.value = branch;
                            option.text = branch;
                            select.appendChild(option);
                        }
                    });
                    loadedDropdowns[envId] = true; // Tandai sudah loaded
                } else {
                    alert("No branches found or Connection Error.");
                }

            } catch (err) {
                console.error("Gitea Fetch Error:", err);
                const errOpt = document.createElement('option');
                errOpt.text = "Error fetching branches";
                errOpt.disabled = true;
                select.appendChild(errOpt);
            } finally {
                spinner.classList.add('hidden');
            }
        }

        // 3. LOG VIEW & LIVE STREAMING (FIXED)
        function viewDeployLogs(envId) {
            if (typeof window.Echo === 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Connecting...',
                    text: 'Vite/Echo is initializing. Please wait a second and try again.'
                });
                return;
            }

            Swal.fire({
                title: 'LIVE TERMINAL',
                html: `
                    <div class="mt-2 w-full text-left bg-slate-900 rounded-xl border border-slate-700 overflow-hidden relative">
                        <div class="px-4 py-2 bg-slate-800 border-b border-slate-700 flex justify-between items-center">
                            <span class="text-[10px] text-slate-400 font-mono">user@flux-worker:~# tail -f deploy.log</span>
                            <div class="flex gap-1.5">
                                <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            </div>
                        </div>
                        <div id="log-container-${envId}" class="h-80 p-4 overflow-y-auto font-mono text-[10px] leading-5 text-slate-300 custom-scrollbar">
                            <div id="log-content-${envId}" class="space-y-0.5">
                                <p class="text-indigo-400 animate-pulse">> ESTABLISHING SECURE CONNECTION...</p>
                            </div>
                        </div>
                    </div>
                `,
                width: '700px',
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    popup: 'flux-popup',
                    title: 'flux-title text-left'
                },
                didOpen: () => {
                    const content = document.getElementById(`log-content-${envId}`);
                    const container = document.getElementById(`log-container-${envId}`);

                    // A. Ambil History Terakhir (Optional, jika endpoint ada)
                    fetch(`/environments/${envId}/logs`).then(r => r.json()).then(d => {
                        if (d.logs && d.logs.length) {
                            content.innerHTML = ''; // Clear init text
                            d.logs.forEach(l => appendLog(content, container, l));
                        }
                    }).catch(e => console.log("No history logs"));

                    // B. Listen Channel
                    window.Echo.private(`environment.logs.${envId}`)
                        .listen('.log.received', (e) => {
                            // Bersihkan pesan "Connecting..." saat log pertama masuk
                            if (content.innerText.includes('ESTABLISHING')) content.innerHTML = '';
                            appendLog(content, container, e.logData);
                        });
                },
                willClose: () => {
                    window.Echo.leave(`environment.logs.${envId}`);
                }
            });
        }

        function appendLog(target, container, log) {
            // Styling Log Line
            const colorClass = log.type === 'error' ? 'text-rose-400 font-bold' : 'text-emerald-400';
            const time = `<span class="text-slate-600 select-none mr-2">[${new Date().toLocaleTimeString('en-US',{hour12:false})}]</span>`;

            const div = document.createElement('div');
            div.className = "hover:bg-slate-800/50 px-1 -mx-1 rounded transition-colors break-words whitespace-pre-wrap";
            div.innerHTML = `${time}<span class="${colorClass}">${log.line}</span>`;

            target.appendChild(div);
            // Auto Scroll ke bawah
            container.scrollTop = container.scrollHeight;
        }

        // 4. NODE DIAGNOSTIC (Dummy Visual)
        function checkNodeStatus(envId) {
            let timerInterval;
            Swal.fire({
                title: 'SYSTEM DIAGNOSTIC',
                html: 'Pinging Docker Daemon...',
                timer: 2000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.timer) {
                    Swal.fire({
                        icon: 'success',
                        title: 'All Systems Operational',
                        text: 'Latency: 24ms | Docker: Active | SSH: Connected',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            });
        }
    </script>
@endpush
