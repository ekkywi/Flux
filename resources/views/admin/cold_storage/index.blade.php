@extends("layouts.app")

@section("title", ucfirst($type) . " Vault")
@section("page_title", ucfirst($type) . " Data Vault")

@section("content")
    <div class="space-y-8 pb-20">
        {{-- HEADER SECTION --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-slate-400 mb-1">
                    <div class="h-1 w-6 bg-slate-400 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">{{ $type }} Vault</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">
                    {{ ucfirst($type) }} Cold Storage
                </h1>
                <p class="text-xs text-slate-500 font-medium italic">
                    Permanent archive for {{ $type }} entities that have passed the 30 day retention period.
                </p>
            </div>

            @php
                $backRoute = match ($type) {
                    "infrastructure" => route("admin.servers.index"),
                    "identity" => route("admin.users.index"),
                    default => route("console.dashboard"),
                };
            @endphp
            <a class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200 flex items-center gap-2" href="{{ $backRoute }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5" />
                </svg>
                Back to {{ ucfirst($type === "infrastructure" ? "inventory" : $type) }}
            </a>
        </div>

        {{-- LIVE SEARCH BOX --}}
        <div class="mb-6 relative group max-w-md">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" />
                </svg>
            </div>
            <input class="w-full pl-11 pr-5 py-3.5 bg-white border border-slate-200 rounded-2xl text-xs font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none placeholder:text-slate-400 shadow-sm" id="vaultSearch" placeholder="Search archives by name or ID..." type="text">
        </div>

        {{-- ARCHIVE TABLE --}}
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="archiveTable">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Archived Identity</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                {{ $type === "infrastructure" ? "Technical Specs" : "Access Identifier" }}
                            </th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Audit Trails</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($archives as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors archive-row">
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">{{ $item->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $item->filename }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-600 font-mono">{{ $item->identifier }}</span>
                                        <span class="text-[9px] uppercase font-bold text-slate-400">Env: {{ $item->env }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[9px] font-black rounded-full">
                                        {{ $item->logs_count }} EVENTS
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <button class="p-2 text-slate-400 hover:text-indigo-600 transition-colors" onclick="viewDetail('{{ addslashes(json_encode($item->raw_data)) }}', '{{ $item->filename }}')">
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
                                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">No archived {{ $type }} data found in the vault.</p>
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
                    <h3 class="text-xl font-black text-slate-900" id="modalNodeName">Entity Details</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1" id="modalSubtitle">Archived Entity Profile</p>
                </div>

                <div class="flex items-center gap-3">
                    <a class="px-4 py-2 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all flex items-center gap-2 shadow-lg shadow-emerald-100" href="#" id="downloadBtn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" />
                        </svg>
                        Export CSV
                    </a>

                    <button class="p-2 text-slate-400 hover:text-rose-500 transition-all bg-white border border-slate-200 rounded-xl" onclick="closeModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-8 space-y-8">
                {{-- Section 1: Identity Card --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3" id="labelIdentifier">Identifier</span>
                        <p class="text-sm font-bold text-slate-700" id="modalIdentifier">N/A</p>
                        <p class="text-[10px] text-slate-400 font-mono mt-1" id="modalSubIdentifier">-</p>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3">Environment / Category</span>
                        <span class="inline-flex px-3 py-1 bg-indigo-100 text-indigo-600 text-[10px] font-black rounded-lg uppercase tracking-widest" id="modalEnv">STAGING</span>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3">Archived Date</span>
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
                                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase">Actor</th>
                                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase">Timestamp</th>
                                    <th class="px-6 py-4 text-[9px] font-black text-slate-400 uppercase text-right">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-[11px]" id="modalLogTable"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Section 3: Raw JSON Data --}}
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
@endsection

@push("scripts")
    <script>
        const modal = document.getElementById('detailModal');
        const jsonContent = document.getElementById('jsonRaw');
        const downloadBtn = document.getElementById('downloadBtn');
        const searchInput = document.getElementById('vaultSearch');

        // --- 1. LIVE SEARCH LOGIC ---
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('.archive-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isMatch = text.includes(query);
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) visibleCount++;
            });

            // Handle empty state
            let noResults = document.getElementById('noResultsMsg');
            if (visibleCount === 0 && !noResults) {
                const tbody = document.querySelector('tbody');
                tbody.insertAdjacentHTML('beforeend', `
                    <tr id="noResultsMsg">
                        <td colspan="4" class="px-6 py-20 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest">
                            No matching archives found.
                        </td>
                    </tr>
                `);
            } else if (visibleCount > 0 && noResults) {
                noResults.remove();
            }
        });

        // --- 2. VIEW DETAIL LOGIC ---
        function viewDetail(rawData, filename) {
            try {
                const data = JSON.parse(rawData);
                const identity = data.identity;
                const type = "{{ $type }}";

                // Setup Download Link
                const baseUrl = "{{ route("admin.cold-storage.download", [$type, ":filename"]) }}";
                downloadBtn.href = baseUrl.replace(':filename', filename);

                // Populate Modal Headers
                document.getElementById('modalNodeName').textContent = identity.name || identity.username || "Unknown";
                document.getElementById('modalSubtitle').textContent = `Archived ${type.charAt(0).toUpperCase() + type.slice(1)} Profile`;
                document.getElementById('modalPrunedDate').textContent = data.metadata.prune_at;

                // Adaptive Identity Mapping
                const labelElem = document.getElementById('labelIdentifier');
                const idElem = document.getElementById('modalIdentifier');
                const subIdElem = document.getElementById('modalSubIdentifier');
                const envElem = document.getElementById('modalEnv');

                if (type === 'infrastructure') {
                    labelElem.textContent = "Network Access";
                    idElem.textContent = identity.ip_address || "0.0.0.0";
                    subIdElem.textContent = `${identity.ssh_user || 'root'}@port:${identity.ssh_port || '22'}`;
                    envElem.textContent = (identity.environment || 'N/A').toUpperCase();
                } else if (type === 'identity') {
                    labelElem.textContent = "Email Address";
                    idElem.textContent = identity.email || "N/A";
                    subIdElem.textContent = `Role: ${identity.role || 'User'}`;
                    envElem.textContent = "IDENTITY VAULT";
                } else {
                    labelElem.textContent = "Project ID";
                    idElem.textContent = identity.identifier || "N/A";
                    subIdElem.textContent = "-";
                    envElem.textContent = "PROJECT ARCHIVE";
                }

                // Populate Audit Log Table
                const tableBody = document.getElementById('modalLogTable');
                let rowsHtml = '';
                const logs = data.audit_trail || [];

                if (logs.length === 0) {
                    rowsHtml = `<tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 italic font-medium">NO EVENTS CAPTURED</td></tr>`;
                } else {
                    logs.forEach(log => {
                        rowsHtml += `
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-black text-slate-700 uppercase tracking-tighter">${log.action.replace(/_/g, ' ')}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-[9px] font-bold uppercase">${log.actor || 'SYSTEM'}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 font-mono">${new Date(log.created_at).toLocaleString('id-ID')}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-[9px] bg-slate-100 px-2 py-1 rounded text-slate-400 font-mono">ID: ${(log.id || '').substring(0,8)}</span>
                            </td>
                        </tr>`;
                    });
                }
                tableBody.innerHTML = rowsHtml;

                // Raw JSON
                jsonContent.textContent = JSON.stringify(data, null, 4);

                // Show Modal
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } catch (e) {
                console.error("JSON Parsing Error:", e);
                Swal.fire('Error', 'Failed to process archive data.', 'error');
            }
        }

        // --- 3. MODAL CONTROLS ---
        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === "Escape" && !modal.classList.contains('hidden')) closeModal();
        });
    </script>
@endpush
