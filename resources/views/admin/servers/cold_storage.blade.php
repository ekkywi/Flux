@extends("layouts.app")
@section("title", "Cold Storage")

@section("content")
    <div class="space-y-8 pb-20">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-slate-400 mb-1">
                    <div class="h-1 w-6 bg-slate-400 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Data Vault</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Cold Storage</h1>
                <p class="text-xs text-slate-500 font-medium italic">Arsip permanen untuk entitas yang sudah melewati masa retensi 30 hari.</p>
            </div>

            <a class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200 flex items-center gap-2" href="{{ route("admin.servers.index") }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5" />
                </svg>
                Back to Inventory
            </a>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Archived Identity</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Technical Specs</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Audit Trails</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($archives as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">{{ $item->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $item->filename }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-600 font-mono">{{ $item->ip }}</span>
                                        <span class="text-[9px] uppercase font-bold text-slate-400">Env: {{ $item->env }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[9px] font-black rounded-full">
                                        {{ $item->logs_count }} EVENTS CAPTURED
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <button class="p-2 text-slate-400 hover:text-indigo-600 transition-all" onclick="viewDetail('{{ addslashes(json_encode($item->raw_data)) }}')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" />
                                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-20 text-center" colspan="4">
                                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">No archived data found in the vault.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- HUMAN-FRIENDLY DETAIL MODAL --}}
    <div class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-900/90 backdrop-blur-sm p-4" id="detailModal">
        <div class="bg-white w-full max-w-4xl rounded-[2.5rem] overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">

            {{-- Header --}}
            <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <div>
                    <h3 class="text-xl font-black text-slate-900" id="modalNodeName">Node Details</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">Archived Entity Profile</p>
                </div>
                <button class="p-2 text-slate-400 hover:text-rose-500 transition-all bg-white border border-slate-200 rounded-xl" onclick="closeModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-8 space-y-8">
                {{-- Section 1: Identity Card --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3">Network Access</span>
                        <p class="text-sm font-bold text-slate-700" id="modalIp">0.0.0.0</p>
                        <p class="text-[10px] text-slate-400 font-mono mt-1" id="modalUserPort">user@port</p>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3">Environment</span>
                        <span class="inline-flex px-3 py-1 bg-indigo-100 text-indigo-600 text-[10px] font-black rounded-lg uppercase tracking-widest" id="modalEnv">STAGING</span>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3">Pruned At</span>
                        <p class="text-sm font-bold text-slate-700" id="modalPrunedDate">-</p>
                    </div>
                </div>

                {{-- Section 2: Audit Trail Table --}}
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-px flex-1 bg-slate-100"></div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Historical Audit Trail</span>
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </div>

                    <div class="border border-slate-100 rounded-3xl overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase">Event</th>
                                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase">Timestamp</th>
                                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase text-right">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-[11px]" id="modalLogTable">
                                {{-- Will be populated by JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bonus: Raw JSON for Techies (Toggleable) --}}
                <details class="group cursor-pointer">
                    <summary class="text-[9px] font-black text-slate-300 uppercase tracking-widest hover:text-indigo-500 transition-colors list-none text-center">
                        — View Raw JSON Data —
                    </summary>
                    <div class="mt-4 p-6 bg-slate-900 rounded-3xl text-green-400 font-mono text-[10px] overflow-x-auto">
                        <pre id="jsonRaw"></pre>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <script>
        function viewDetail(rawData) {
            const data = JSON.parse(rawData);
            const identity = data.identity;
            const metadata = data.metadata;
            const logs = data.audit_trail || [];

            // 1. Populate Identity
            document.getElementById('modalNodeName').textContent = identity.name;
            document.getElementById('modalIp').textContent = identity.ip_address;
            document.getElementById('modalUserPort').textContent = `${identity.ssh_user}@port:${identity.ssh_port}`;
            document.getElementById('modalEnv').textContent = identity.environment;
            document.getElementById('modalPrunedDate').textContent = metadata.prune_at;

            // 2. Populate Log Table
            const tableBody = document.getElementById('modalLogTable');
            tableBody.innerHTML = ''; // Reset table

            if (logs.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="3" class="px-6 py-10 text-center text-slate-400 italic">No historical events captured for this entity.</td></tr>`;
            } else {
                logs.forEach(log => {
                    const row = `
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-black text-slate-700 uppercase tracking-tighter">${log.action.replace(/_/g, ' ')}</span>
                    </td>
                    <td class="px-6 py-4 text-slate-500 font-mono">
                        ${new Date(log.created_at).toLocaleString()}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-[9px] bg-slate-100 px-2 py-1 rounded text-slate-400 font-mono">
                            ID: ${log.id.substring(0, 8)}...
                        </span>
                    </td>
                </tr>
            `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            }

            // 3. Raw Data
            document.getElementById('jsonRaw').textContent = JSON.stringify(data, null, 4);

            // Show Modal
            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailModal').classList.add('flex');
        }
    </script>
@endsection
