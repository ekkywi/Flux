@extends("layouts.app")
@section("title", "Infrastructure Inventory")
@section("page_title", "Server Nodes")
@section("page_subtitle", "Manage core infrastructure and connection endpoints.")

@section("content")
    <div class="space-y-6 pb-20">

        {{-- 1. CONTROL BAR --}}
        <div class="sticky top-0 z-30 flex flex-col gap-4 rounded-2xl bg-white/80 p-2 backdrop-blur-xl border border-zinc-200 shadow-sm md:flex-row md:items-center md:justify-between transition-all">

            {{-- Left: Status --}}
            <div class="flex items-center gap-3 px-4">
                <div class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 leading-none">Node Monitor</h2>
                    <p class="text-[10px] font-medium text-zinc-500 mt-0.5">
                        <span class="font-bold text-zinc-900">{{ $servers->total() }}</span> active endpoints
                    </p>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-2 pl-2 overflow-x-auto no-scrollbar">

                {{-- Stats Production --}}
                <div class="flex items-center gap-3 px-4 py-2 bg-zinc-50/50 border border-zinc-200/50 rounded-xl hidden md:flex">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest leading-none">Production</span>
                        <span class="text-xs font-bold text-rose-600 mt-0.5">{{ \App\Models\Server::where("environment", "production")->count() }} Nodes</span>
                    </div>
                </div>

                <div class="h-8 w-px bg-zinc-200 mx-1 hidden md:block"></div>

                {{-- Vault Button --}}
                <a class="flex items-center gap-2 px-3 py-2 bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-600 rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm hover:border-zinc-300" href="{{ route("admin.servers.archived") }}">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span>Archived</span>
                </a>

                {{-- Provision Button --}}
                <button class="flex items-center gap-2 px-4 py-2 bg-zinc-900 text-white hover:bg-blue-600 rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm hover:shadow-blue-500/20 active:scale-95 border border-transparent" onclick="openProvisionModal()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span>Provision Node</span>
                </button>
            </div>
        </div>

        {{-- 2. SERVER LIST --}}
        <div class="grid grid-cols-1 gap-3">
            @forelse ($servers as $server)
                <div class="group relative flex flex-col md:flex-row md:items-center gap-4 rounded-2xl bg-white p-4 shadow-sm border border-zinc-200 hover:border-blue-500/30 hover:shadow-md transition-all duration-200">

                    {{-- Icon & Name (FIXED LAYOUT) --}}
                    <div class="flex items-center gap-4 md:w-[300px] shrink-0"> {{-- Lebarkan dikit jadi 300px --}}

                        {{-- Icon --}}
                        <div class="h-10 w-10 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-white transition-all shadow-sm group-hover:bg-blue-600 group-hover:border-blue-500 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </div>

                        {{-- Text Content --}}
                        <div class="min-w-0 flex-1">
                            <h3 class="text-sm font-bold text-zinc-900 group-hover:text-blue-600 truncate transition-colors">
                                {{ $server->name }}
                            </h3>

                            {{-- Baris IP dan UUID --}}
                            <div class="flex items-center gap-2 mt-1.5">
                                {{-- 1. IP ADDRESS (PENTING: shrink-0 agar tidak kepotong) --}}
                                <span class="shrink-0 inline-flex items-center px-1.5 py-0.5 rounded bg-zinc-100 border border-zinc-200 text-[10px] font-mono font-bold text-zinc-700 tracking-tight">
                                    {{ $server->ip_address }}
                                </span>

                                {{-- Separator --}}
                                <span class="text-zinc-300 text-[10px] shrink-0">•</span>

                                {{-- 2. UUID (SECONDARY: truncate agar dia yang ngalah) --}}
                                <span class="text-[10px] font-mono text-zinc-400 truncate min-w-0" title="Full ID: {{ $server->id }}">
                                    {{ substr($server->id, 0, 8) }}...
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Technical Details --}}
                    <div class="flex-1 flex flex-col md:flex-row md:items-center gap-2 md:gap-8 md:px-6 md:border-l md:border-zinc-100">
                        <div class="flex flex-col min-w-[120px]">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Access Point</span>
                            <span class="text-xs font-mono font-bold text-zinc-600 mt-0.5">{{ $server->ssh_user }}<span class="text-zinc-400">@</span>{{ $server->ssh_port }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Env</span>
                            <div class="mt-0.5">
                                @php
                                    $envClass = match ($server->environment) {
                                        "production" => "bg-rose-50 text-rose-600 border-rose-100",
                                        "staging" => "bg-amber-50 text-amber-600 border-amber-100",
                                        default => "bg-blue-50 text-blue-600 border-blue-100",
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded border {{ $envClass }} text-[9px] font-black uppercase tracking-widest">
                                    {{ $server->environment }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between md:justify-end gap-2 md:w-auto md:pl-6 md:border-l md:border-zinc-100 mt-4 md:mt-0">

                        {{-- Test Connection --}}
                        <button class="p-2 text-zinc-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" onclick="testConnection({{ $server }})" title="Test Connectivity">
                            <svg class="w-4 h-4" fill="none" id="icon-ping-{{ $server->id }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </button>

                        {{-- Deploy Key --}}
                        <button class="p-2 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" onclick="openDeployModal({{ $server }})" title="Deploy Master Key">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </button>

                        {{-- Divider --}}
                        <div class="w-px h-4 bg-zinc-200 mx-1"></div>

                        {{-- Edit --}}
                        <button class="p-2 text-zinc-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" onclick="openEditModal({{ $server }})" title="Edit Configuration">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </button>

                        {{-- Decommission --}}
                        <button class="p-2 text-zinc-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" onclick="confirmDecommission({{ $server }})" title="Decommission">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center rounded-3xl border-2 border-dashed border-zinc-200 bg-zinc-50/50">
                    <h3 class="text-zinc-900 font-bold text-lg">No Infrastructure Nodes</h3>
                    <p class="text-zinc-500 text-sm mt-1">Start by provisioning a new server node.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $servers->links() }}
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        // ==========================================
        // 1. CONFIGURATION
        // ==========================================
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        const fluxSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-2xl border border-zinc-200 shadow-2xl p-0 overflow-hidden font-sans',
                title: 'text-zinc-900 text-lg font-bold pt-6 px-6',
                htmlContainer: 'text-zinc-500 text-sm px-6 pb-6',
                confirmButton: 'bg-zinc-900 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-800 transition-colors shadow-sm mx-2 mb-6',
                cancelButton: 'bg-white text-zinc-600 border border-zinc-200 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-50 transition-colors mx-2 mb-6',
                input: 'bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all mx-6 mb-4 w-auto'
            },
            buttonsStyling: false
        });

        // ==========================================
        // 2. HELPER: SUBMIT FORM
        // ==========================================
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
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = method;
                form.appendChild(methodInput);
            }

            for (const [key, value] of Object.entries(data)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            fluxSwal.fire({
                title: 'Processing...',
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            form.submit();
        };

        // ==========================================
        // 3. PROVISION SERVER
        // ==========================================
        window.openProvisionModal = async function() {
            const {
                value: formValues
            } = await fluxSwal.fire({
                title: 'Provision Node',
                html: `
                    <div class="flex flex-col gap-3 text-left">
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">Friendly Name</label>
                            <input id="prov-name" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500" placeholder="Web-Prod-01">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">IP Address</label>
                                <input id="prov-ip" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm font-mono text-blue-600 font-bold outline-none" placeholder="103.x.x.x">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">SSH Port</label>
                                <input id="prov-port" type="number" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none" value="22">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">SSH User</label>
                                <input id="prov-user" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none" value="flux">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Environment</label>
                                <select id="prov-env" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none">
                                    <option value="development">Development</option>
                                    <option value="staging">Staging</option>
                                    <option value="production">Production</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">Description</label>
                            <textarea id="prov-desc" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Authorize Node',
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        name: document.getElementById('prov-name').value,
                        ip_address: document.getElementById('prov-ip').value,
                        ssh_port: document.getElementById('prov-port').value,
                        ssh_user: document.getElementById('prov-user').value,
                        environment: document.getElementById('prov-env').value,
                        description: document.getElementById('prov-desc').value,
                    }
                }
            });

            if (formValues) {
                window.submitForm('{{ route("admin.servers.store") }}', 'POST', formValues);
            }
        };

        // ==========================================
        // 4. EDIT SERVER
        // ==========================================
        window.openEditModal = async function(server) {
            const {
                value: formValues
            } = await fluxSwal.fire({
                title: 'Edit Node',
                html: `
                    <div class="flex flex-col gap-3 text-left">
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">Friendly Name</label>
                            <input id="edit-name" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none" value="${server.name}">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">IP Address</label>
                                <input id="edit-ip" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm font-mono font-bold outline-none" value="${server.ip_address}">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">SSH Port</label>
                                <input id="edit-port" type="number" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none" value="${server.ssh_port}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">SSH User</label>
                                <input id="edit-user" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none" value="${server.ssh_user}">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Environment</label>
                                <select id="edit-env" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none">
                                    <option value="development" ${server.environment === 'development' ? 'selected' : ''}>Development</option>
                                    <option value="staging" ${server.environment === 'staging' ? 'selected' : ''}>Staging</option>
                                    <option value="production" ${server.environment === 'production' ? 'selected' : ''}>Production</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">Description</label>
                            <textarea id="edit-desc" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none" rows="2">${server.description || ''}</textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update Node',
                preConfirm: () => {
                    return {
                        name: document.getElementById('edit-name').value,
                        ip_address: document.getElementById('edit-ip').value,
                        ssh_port: document.getElementById('edit-port').value,
                        ssh_user: document.getElementById('edit-user').value,
                        environment: document.getElementById('edit-env').value,
                        description: document.getElementById('edit-desc').value,
                    }
                }
            });

            if (formValues) {
                window.submitForm(`/admin/servers/${server.id}`, 'PUT', formValues);
            }
        };

        // ==========================================
        // 5. DECOMMISSION (DELETE)
        // ==========================================
        window.confirmDecommission = function(server) {
            fluxSwal.fire({
                title: 'Decommission Node?',
                html: `Server <b class="text-zinc-900">${server.name}</b> will be removed from inventory.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirm Decommission',
                confirmButtonClass: 'bg-rose-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-rose-700 mx-2 mb-6',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.submitForm(`/admin/servers/${server.id}`, 'DELETE');
                }
            });
        };

        // ==========================================
        // 6. DEPLOY KEY (SPECIAL LOGIC)
        // ==========================================
        window.openDeployModal = async function(server) {
            const {
                value: password
            } = await fluxSwal.fire({
                title: 'Deploy Master Key',
                html: `
                    <p class="mb-4">Pushing public key to <b class="text-blue-600">${server.name}</b> (${server.ip_address}).</p>
                    <div class="text-left">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">SSH Root/User Password</label>
                        <input id="ssh-pass" type="password" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors" placeholder="••••••••">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Authorize & Push',
                preConfirm: () => {
                    const pass = document.getElementById('ssh-pass').value;
                    if (!pass) Swal.showValidationMessage('SSH Password is required to push keys');
                    return pass;
                }
            });

            if (password) {
                // Proses Deploy menggunakan Fetch API karena butuh respon realtime
                fluxSwal.fire({
                    title: 'Deploying Key...',
                    html: 'Establishing SSH handshake...',
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const response = await fetch(`/admin/servers/${server.id}/deploy-key`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            ssh_password: password
                        })
                    });
                    const data = await response.json();

                    if (response.ok) {
                        fluxSwal.fire({
                            icon: 'success',
                            title: 'Deployed!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        fluxSwal.fire({
                            icon: 'error',
                            title: 'Deployment Failed',
                            text: data.message
                        });
                    }
                } catch (error) {
                    fluxSwal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Could not reach server.'
                    });
                }
            }
        };

        // ==========================================
        // 7. TEST CONNECTION
        // ==========================================
        window.testConnection = async function(server) {
            const id = server.id; // Ambil ID dari object
            const icon = document.getElementById(`icon-ping-${id}`);

            if (icon) icon.classList.add('animate-spin', 'text-blue-600');

            try {
                const response = await fetch(`/admin/servers/${id}/test-link`);
                const data = await response.json();

                if (data.status === 'success') {
                    fluxSwal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        icon: 'success',
                        title: 'Node Accessible',
                        html: `<span class="text-xs text-zinc-500">${data.detail}</span>`
                    });
                } else {
                    fluxSwal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        icon: 'error',
                        title: 'Unreachable',
                        html: `<span class="text-xs text-zinc-500">${data.message}</span>`
                    });
                }
            } catch (error) {
                fluxSwal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'System Error'
                });
            } finally {
                if (icon) icon.classList.remove('animate-spin', 'text-blue-600');
            }
        };
    </script>
@endpush
