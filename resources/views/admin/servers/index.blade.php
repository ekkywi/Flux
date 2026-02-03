@extends("layouts.app")
@section("title", "Infrastructure Inventory")
@section("page_title", "Core Management")

@section("content")
    <div class="space-y-8 pb-20 text-slate-900">

        {{-- 1. STREAMLINED HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <div class="h-1 w-6 bg-indigo-600 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Infrastructure Core</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Server Inventory</h1>
                <p class="text-xs text-slate-500 font-medium">
                    Managing <span class="text-indigo-600 font-bold">{{ $servers->total() }} registered nodes</span> within the console proxy.
                </p>
            </div>

            {{-- Compact Stats --}}
            <div class="flex items-center gap-4 px-5 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="text-center min-w-[50px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Total</span>
                    <span class="text-sm font-black text-slate-900">{{ $servers->total() }}</span>
                </div>
                <div class="w-px h-6 bg-slate-100"></div>
                <div class="text-center min-w-[50px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Production</span>
                    <span class="text-sm font-black text-rose-600">{{ \App\Models\Server::where("environment", "production")->count() }}</span>
                </div>
                <div class="w-px h-6 bg-slate-100"></div>

                <a class="px-3 py-1.5 bg-slate-50 text-slate-400 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-all flex items-center gap-2 border border-transparent hover:border-indigo-100" href="{{ route("admin.cold-storage.index", "infrastructure") }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                    Vault
                </a>
                <a class="px-3 py-1.5 bg-slate-50 text-slate-400 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center gap-2 border border-transparent hover:border-rose-100" href="{{ route("admin.servers.archived") }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" />
                    </svg>
                    Archived
                </a>

                <button class="px-4 py-1.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-indigo-600 transition-all shadow-md" onclick="toggleProvisionModal()">
                    + Provision Node
                </button>
            </div>
        </div>

        {{-- 2. SERVER DIRECTORY TABLE --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Node Name</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Access Point</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Environment</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($servers as $server)
                            <tr class="hover:bg-slate-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-slate-900 flex items-center justify-center text-white transition-all shadow-lg font-black border border-slate-800">
                                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" stroke-width="2.5" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-900 truncate tracking-tight">{{ $server->name }}</p>
                                            <p class="text-[10px] font-mono text-slate-400 truncate">{{ $server->id }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-700 font-mono">{{ $server->ip_address }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase font-bold">{{ $server->ssh_user }}@port:{{ $server->ssh_port }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $envColors = match ($server->environment) {
                                            "production" => "bg-rose-50 text-rose-600 border-rose-100",
                                            "staging" => "bg-amber-50 text-amber-600 border-amber-100",
                                            default => "bg-indigo-50 text-indigo-600 border-indigo-100",
                                        };
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 rounded-lg border {{ $envColors }} text-[9px] font-black uppercase tracking-widest">
                                        {{ $server->environment }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-emerald-600 text-[9px] font-black uppercase tracking-widest">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Active
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button class="p-2 text-slate-400 hover:text-emerald-500 transition-colors rounded-lg hover:bg-emerald-50 btn-test-link" data-id="{{ $server->id }}" title="Check Connectivity">
                                            <svg class="w-4 h-4" fill="none" id="icon-{{ $server->id }}" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                                            </svg>
                                        </button>
                                        <button class="p-2 text-slate-400 hover:text-indigo-600 transition-colors rounded-lg hover:bg-indigo-50" title="SSH Terminal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" />
                                            </svg>
                                        </button>
                                        <button class="p-2 text-slate-400 hover:text-indigo-600 transition-colors" onclick="openDeployModal('{{ $server->id }}', '{{ $server->name }}')" title="Deploy Master Key">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                        </button>
                                        <button class="p-2 text-slate-400 hover:text-amber-500 transition-colors rounded-lg hover:bg-amber-50" onclick="openEditModal({{ json_encode($server) }})" title="Edit Configuration">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                            </svg>
                                        </button>
                                        <button class="p-2 text-slate-400 hover:text-rose-600 transition-colors rounded-lg hover:bg-rose-50" onclick="openDecommissionModal('{{ $server->id }}', '{{ $server->name }}')" title="Decommission">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-20 text-center" colspan="5">
                                    <p class="text-slate-400 text-xs font-medium uppercase tracking-widest">No infrastructure nodes provisioned yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                {{ $servers->links() }}
            </div>
        </div>
    </div>

    {{-- 3. PROVISION MODAL --}}
    <div class="fixed inset-0 z-[100] items-center justify-center hidden bg-slate-900/60 backdrop-blur-sm px-4" id="provisionModal">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl border border-slate-200 transform transition-all">
            <div class="mb-8">
                <div class="h-1.5 w-12 bg-indigo-600 rounded-full mb-4"></div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Provision Node</h3>
                <p class="text-xs text-slate-500 font-medium">Register a new infrastructure entity to the core inventory.</p>
            </div>

            <form action="{{ route("admin.servers.store") }}" class="space-y-5" method="POST">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Friendly Name</label>
                    <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-medium" name="name" placeholder="e.g. Web-Production-01" required type="text">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">IP Address / Host</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm font-mono focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-bold text-indigo-600" name="ip_address" placeholder="103.x.x.x" required type="text">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">SSH Port</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-bold" name="ssh_port" required type="number" value="22">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">SSH User</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-medium" name="ssh_user" required type="text" value="flux">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Environment</label>
                        <select class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 appearance-none font-bold" name="environment" required>
                            <option value="development">DEVELOPMENT</option>
                            <option value="staging">STAGING</option>
                            <option value="production">PRODUCTION</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Brief Description</label>
                    <textarea class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-medium" name="description" placeholder="Optional notes about this node..." rows="2"></textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleProvisionModal()" type="button">Abort</button>
                    <button class="flex-1 px-6 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200" type="submit">Authorize Node</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. EDIT MODAL --}}
    <div class="fixed inset-0 z-[100] items-center justify-center hidden bg-slate-900/60 backdrop-blur-sm px-4" id="editModal">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl border border-slate-200 transform transition-all">
            <div class="mb-8">
                <div class="h-1.5 w-12 bg-amber-500 rounded-full mb-4"></div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Edit Node</h3>
                <p class="text-xs text-slate-500 font-medium">Recalibrate infrastructure entity parameters.</p>
            </div>

            <form class="space-y-5" id="editForm" method="POST">
                @csrf
                @method("PUT")

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Friendly Name</label>
                    <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-medium" id="edit_name" name="name" required type="text">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">IP Address / Host</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm font-mono focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-bold text-indigo-600" id="edit_ip_address" name="ip_address" required type="text">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">SSH Port</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-bold" id="edit_ssh_port" name="ssh_port" required type="number">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">SSH User</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-medium" id="edit_ssh_user" name="ssh_user" required type="text">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Environment</label>
                        <select class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 appearance-none font-bold" id="edit_environment" name="environment" required>
                            <option value="development">DEVELOPMENT</option>
                            <option value="staging">STAGING</option>
                            <option value="production">PRODUCTION</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Brief Description</label>
                    <textarea class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-medium" id="edit_description" name="description" placeholder="Optional notes..." rows="2"></textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleEditModal()" type="button">Abort</button>
                    <button class="flex-1 px-6 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200" type="submit">Update Node</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 5. DECOMMISSION MODAL (Destructive) --}}
    <div class="fixed inset-0 z-[110] items-center justify-center hidden bg-slate-900/80 backdrop-blur-md px-4" id="decommissionModal">
        <div class="bg-white w-full max-w-sm rounded-[2.5rem] p-10 shadow-2xl border border-rose-100">
            <div class="mb-8 text-center">
                <div class="mx-auto h-16 w-16 bg-rose-50 rounded-full flex items-center justify-center mb-6 border border-rose-100">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Decommission?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    Server <span class="text-rose-600 font-bold" id="decomServerNameDisplay"></span> will be removed from the active inventory.
                </p>
            </div>

            <form id="decommissionForm" method="POST">
                @csrf
                @method("DELETE")
                <div class="flex flex-col gap-3">
                    <button class="w-full px-6 py-4 bg-rose-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-xl shadow-rose-200" type="submit">
                        Confirm Decommission
                    </button>
                    <button class="w-full px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleDecommissionModal()" type="button">
                        Abort
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="fixed inset-0 z-50 hidden overflow-y-auto" id="deployKeyModal">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 border border-slate-200">
                <div class="mb-6">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Deploy Master Key</h3>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Flux will push the public key to <span class="text-indigo-600 font-bold" id="targetServerName"></span> via temporary SSH access.</p>
                </div>

                <form id="deployKeyForm">
                    @csrf
                    <input id="deployServerId" type="hidden">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">SSH Password</label>
                            <input class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none" id="ssh_password" placeholder="Target's root/user password" required type="password">
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button class="flex-1 px-6 py-3 border border-slate-200 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-50 transition-all" onclick="closeDeployModal()" type="button">
                                Cancel
                            </button>
                            <button class="flex-1 px-6 py-3 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all" id="btnSubmitDeploy" type="submit">
                                Authorize & Push
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        // 1. Modal Management
        function toggleProvisionModal() {
            const modal = document.getElementById('provisionModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        const deployModal = document.getElementById('deployKeyModal');
        const deployForm = document.getElementById('deployKeyForm');

        function openDeployModal(id, name) {
            document.getElementById('deployServerId').value = id;
            document.getElementById('targetServerName').textContent = name;
            deployModal.classList.remove('hidden');
        }

        function closeDeployModal() {
            deployModal.classList.add('hidden');
            deployForm.reset();
        }

        function toggleEditModal() {
            const modal = document.getElementById('editModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function openEditModal(server) {
            const form = document.getElementById('editForm');
            form.action = `/admin/servers/${server.id}`;

            document.getElementById('edit_name').value = server.name;
            document.getElementById('edit_ip_address').value = server.ip_address;
            document.getElementById('edit_ssh_port').value = server.ssh_port;
            document.getElementById('edit_ssh_user').value = server.ssh_user;
            document.getElementById('edit_environment').value = server.environment;
            document.getElementById('edit_description').value = server.description || '';


            toggleEditModal();
        }

        function toggleDecommissionModal() {
            const modal = document.getElementById('decommissionModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function openDecommissionModal(id, name) {
            const form = document.getElementById('decommissionForm');
            form.action = `/admin/servers/${id}`;
            document.getElementById('decomServerNameDisplay').textContent = name;
            toggleDecommissionModal();
        }

        // 2. Connectivity & Health Test
        document.querySelectorAll('.btn-test-link').forEach(button => {
            button.addEventListener('click', async function() {
                const serverId = this.getAttribute('data-id');
                const icon = document.getElementById('icon-' + serverId);
                icon.classList.add('animate-spin', 'text-indigo-600');

                try {
                    const response = await fetch(`/admin/servers/${serverId}/test-link`);
                    const data = await response.json();

                    if (data.status === 'success') {
                        Toast.fire({
                            icon: 'success',
                            iconColor: '#10b981',
                            title: 'Node Accessible',
                            html: `
                                <div class="mt-2 py-2 border-t border-slate-100 text-left">
                                    <span class="flux-toast-content block mb-2">Technical response received:</span>
                                    <code class="text-[10px] text-emerald-600 font-mono font-bold block bg-emerald-50 rounded p-2 border border-emerald-100">
                                        ${data.detail}
                                    </code>
                                </div>
                            `,
                            customClass: {
                                popup: 'flux-toast flux-toast-success',
                                title: 'flux-toast-title'
                            }
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            iconColor: '#ef4444',
                            title: 'Connection Failed',
                            html: `<span class="flux-toast-content">${data.message}</span>`,
                            customClass: {
                                popup: 'flux-toast flux-toast-error',
                                title: 'flux-toast-title'
                            }
                        });
                    }
                } catch (error) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Network Error',
                        customClass: {
                            popup: 'flux-toast flux-toast-error',
                            title: 'flux-toast-title'
                        }
                    });
                } finally {
                    icon.classList.remove('animate-spin', 'text-indigo-600');
                }
            });
        });

        // 3. Key Deployment Logic
        deployForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('deployServerId').value;
            const password = document.getElementById('ssh_password').value;
            const btn = document.getElementById('btnSubmitDeploy');

            btn.disabled = true;
            btn.innerHTML = '<span class="animate-pulse text-[10px]">Deploying...</span>';

            try {
                const response = await fetch(`/admin/servers/${id}/deploy-key`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ssh_password: password
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    Toast.fire({
                        icon: 'success',
                        iconColor: '#10b981',
                        title: 'Identity Deployed',
                        html: `<span class="flux-toast-content">${data.message}</span>`,
                        customClass: {
                            popup: 'flux-toast flux-toast-success',
                            title: 'flux-toast-title'
                        }
                    });
                    closeDeployModal();
                } else {
                    Toast.fire({
                        icon: 'error',
                        iconColor: '#ef4444',
                        title: 'Auth Error',
                        html: `<span class="flux-toast-content">${data.message || 'Deployment failed'}</span>`,
                        customClass: {
                            popup: 'flux-toast flux-toast-error',
                            title: 'flux-toast-title'
                        }
                    });
                }
            } catch (error) {
                Toast.fire({
                    icon: 'error',
                    title: 'Network Error',
                    customClass: {
                        popup: 'flux-toast flux-toast-error',
                        title: 'flux-toast-title'
                    }
                });
            } finally {
                btn.disabled = false;
                btn.textContent = 'Authorize & Push';
            }
        });
    </script>
@endpush
