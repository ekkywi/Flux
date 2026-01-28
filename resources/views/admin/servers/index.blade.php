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
                                        <button class="p-2 text-slate-400 hover:text-indigo-600 transition-colors rounded-lg hover:bg-indigo-50" title="SSH Terminal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" />
                                            </svg>
                                        </button>
                                        <button class="p-2 text-slate-400 hover:text-rose-600 transition-colors rounded-lg hover:bg-rose-50" title="Decommission">
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
@endsection

@push("scripts")
    <script>
        function toggleProvisionModal() {
            const modal = document.getElementById('provisionModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }
    </script>
@endpush
